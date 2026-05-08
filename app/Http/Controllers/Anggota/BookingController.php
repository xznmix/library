<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\Booking;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Daftar booking saya
     */
    public function index()
    {
        $bookings = Booking::with('buku')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('anggota.pages.booking.index', compact('bookings'));
    }

    /**
     * Form booking buku
     */
    public function create($bukuId)
    {
        $buku = Buku::findOrFail($bukuId);
        
        // Cek apakah buku bisa di-booking
        if (!$buku->canBook()) {
            return redirect()->route('opac.show', $bukuId)
                ->with('error', 'Maaf, buku ini sedang tidak tersedia untuk di-booking.');
        }
        
        // Cek batas booking anggota (maks 2 booking aktif)
        $aktifCount = Booking::where('user_id', Auth::id())
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->count();
        
        if ($aktifCount >= 2) {
            return redirect()->route('opac.show', $bukuId)
                ->with('error', 'Anda sudah memiliki 2 booking aktif. Selesaikan booking sebelumnya.');
        }
        
        // Cek apakah sudah pernah booking buku ini (belum selesai)
        $sudahBooking = Booking::where('user_id', Auth::id())
            ->where('buku_id', $bukuId)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->exists();
        
        if ($sudahBooking) {
            return redirect()->route('opac.show', $bukuId)
                ->with('error', 'Anda sudah melakukan booking untuk buku ini. Silakan tunggu konfirmasi petugas.');
        }
        
        // Generate tanggal ambil (H+1 sampai H+2, exclude Minggu)
        $tanggalOptions = [];
        $hariKe = 1;
        $maxHari = 3; // Maks cari sampai 3 hari ke depan
        
        for ($i = 1; $i <= $maxHari; $i++) {
            $tanggal = Carbon::now()->addDays($i);
            
            // Skip jika hari Minggu (Carbon::SUNDAY = 0 atau 7 tergantung pengaturan)
            if ($tanggal->isSunday()) {
                $maxHari++; // Tambah maks pencarian karena skip 1 hari
                continue;
            }
            
            $tanggalOptions[] = [
                'value' => $tanggal->format('Y-m-d'),
                'label' => $tanggal->translatedFormat('l, d F Y'),
                'hari' => $this->getHariLabel($i, $tanggal)
            ];
            
            if (count($tanggalOptions) >= 2) {
                break;
            }
        }
        
        // Jika masih kurang dari 2 opsi (misal karena banyak Minggu), tambah lagi
        while (count($tanggalOptions) < 2) {
            $i = $maxHari + 1;
            $tanggal = Carbon::now()->addDays($i);
            
            if (!$tanggal->isSunday()) {
                $tanggalOptions[] = [
                    'value' => $tanggal->format('Y-m-d'),
                    'label' => $tanggal->translatedFormat('l, d F Y'),
                    'hari' => $this->getHariLabel($i, $tanggal)
                ];
            }
            $maxHari++;
        }
        
        return view('anggota.pages.booking.create', compact('buku', 'tanggalOptions'));
    }

    /**
     * Helper untuk label hari
     */
    private function getHariLabel($i, $tanggal)
    {
        if ($i == 1) {
            return 'Besok';
        } elseif ($i == 2) {
            return 'Lusa';
        } else {
            return $tanggal->translatedFormat('l');
        }
    }

    /**
     * Simpan booking
     */
    public function store(Request $request, $bukuId)
    {
        $request->validate([
            'tanggal_ambil' => 'required|date|after_or_equal:' . now()->addDay()->format('Y-m-d') . '|before_or_equal:' . now()->addDays(2)->format('Y-m-d'),
        ]);
        
        $buku = Buku::findOrFail($bukuId);
        
        // Validasi ulang
        if (!$buku->canBook()) {
            return back()->with('error', 'Maaf, buku ini sedang tidak tersedia untuk di-booking.');
        }
        
        $aktifCount = Booking::where('user_id', Auth::id())
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->count();
        
        if ($aktifCount >= 2) {
            return back()->with('error', 'Anda sudah memiliki 2 booking aktif.');
        }
        
        $sudahBooking = Booking::where('user_id', Auth::id())
            ->where('buku_id', $bukuId)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->exists();
        
        if ($sudahBooking) {
            return back()->with('error', 'Anda sudah melakukan booking untuk buku ini.');
        }
        
        $tanggalAmbil = Carbon::parse($request->tanggal_ambil);
        $batasAmbil = $tanggalAmbil->copy()->endOfDay();
        
        DB::beginTransaction();
        try {
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'buku_id' => $bukuId,
                'tanggal_booking' => now(),
                'tanggal_ambil' => $tanggalAmbil,
                'batas_ambil' => $batasAmbil,
                'status' => 'menunggu',
            ]);
            
            DB::commit();
            
            // Notifikasi ke petugas (akan ditampilkan di dashboard petugas)
            $this->notifikasiKePetugas($booking);
            
            return redirect()->route('anggota.booking.index')
                ->with('success', 'Booking berhasil! Silakan tunggu konfirmasi dari petugas.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal melakukan booking: ' . $e->getMessage());
        }
    }

    /**
     * Batalkan booking (hanya untuk status menunggu)
     */
    public function cancel($id)
    {
        $booking = Booking::where('user_id', Auth::id())
            ->where('id', $id)
            ->where('status', 'menunggu')
            ->firstOrFail();
        
        $booking->update(['status' => 'hangus']);
        
        return redirect()->route('anggota.booking.index')
            ->with('success', 'Booking berhasil dibatalkan.');
    }

    /**
     * Detail booking
     */
    public function show($id)
    {
        $booking = Booking::with(['buku', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        
        return view('anggota.pages.booking.show', compact('booking'));
    }

    /**
     * Kirim notifikasi ke petugas
     */
    private function notifikasiKePetugas($booking)
    {
        $petugas = \App\Models\User::where('role', 'petugas')->get();
        
        foreach ($petugas as $p) {
            Notifikasi::create([
                'user_id' => $p->id,
                'judul' => '📖 Booking Buku Baru',
                'isi' => $booking->user->name . ' melakukan booking buku "' . $booking->buku->judul . '". Silakan proses segera.',
                'type' => 'info',
                'link' => route('petugas.booking.show', $booking->id),
            ]);
        }
    }
}