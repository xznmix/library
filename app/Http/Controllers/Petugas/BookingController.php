<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Buku;
use App\Models\Notifikasi;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Daftar semua booking
     */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'buku']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_booking', 'like', "%{$search}%")
                  ->orWhereHas('user', function($user) use ($search) {
                      $user->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('buku', function($buku) use ($search) {
                      $buku->where('judul', 'like', "%{$search}%");
                  });
            });
        }
        
        $bookings = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        $statistik = [
            'total' => Booking::count(),
            'menunggu' => Booking::where('status', 'menunggu')->count(),
            'disetujui' => Booking::where('status', 'disetujui')->count(),
            'diambil' => Booking::where('status', 'diambil')->count(),
            'hangus' => Booking::where('status', 'hangus')->count(),
        ];
        
        return view('petugas.pages.booking.index', compact('bookings', 'statistik'));
    }

    /**
     * Detail booking
     */
    public function show($id)
    {
        $booking = Booking::with(['user', 'buku'])->findOrFail($id);
        return view('petugas.pages.booking.show', compact('booking'));
    }

    /**
     * Setujui booking
     */
    public function approve($id)
    {
        $booking = Booking::with(['buku', 'user'])->findOrFail($id);
        
        if ($booking->status !== 'menunggu') {
            return back()->with('error', 'Booking ini sudah diproses sebelumnya.');
        }
        
        // Cek stok buku
        if (!$booking->buku->canBook()) {
            return back()->with('error', 'Stok buku tidak mencukupi untuk booking ini.');
        }
        
        DB::beginTransaction();
        try {
            // Kurangi stok_tersedia, tambah stok_direservasi
            $booking->buku->stok_tersedia -= 1;
            $booking->buku->stok_direservasi += 1;
            $booking->buku->save();
            
            // Update status booking
            $booking->update([
                'status' => 'disetujui',
                'petugas_id' => Auth::id(),
            ]);
            
            DB::commit();
            
            // Notifikasi ke anggota
            Notifikasi::create([
                'user_id' => $booking->user_id,
                'judul' => '✅ Booking Buku Disetujui',
                'isi' => 'Booking buku "' . $booking->buku->judul . '" telah DISETUJUI. Silakan ambil buku sebelum ' . Carbon::parse($booking->batas_ambil)->format('d/m/Y H:i') . '.',
                'type' => 'success',
                'link' => route('anggota.booking.show', $booking->id),
            ]);
            
            return redirect()->route('petugas.booking.index')
                ->with('success', 'Booking berhasil disetujui. Buku telah direservasi.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui booking: ' . $e->getMessage());
        }
    }

    /**
     * Tolak booking
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|min:5|max:500',
        ]);
        
        $booking = Booking::with(['buku', 'user'])->findOrFail($id);
        
        if ($booking->status !== 'menunggu') {
            return back()->with('error', 'Booking ini sudah diproses sebelumnya.');
        }
        
        $booking->update([
            'status' => 'ditolak',
            'catatan_penolakan' => $request->alasan,
            'petugas_id' => Auth::id(),
        ]);
        
        // Notifikasi ke anggota
        Notifikasi::create([
            'user_id' => $booking->user_id,
            'judul' => '❌ Booking Buku Ditolak',
            'isi' => 'Booking buku "' . $booking->buku->judul . '" telah DITOLAK. Alasan: ' . $request->alasan,
            'type' => 'error',
            'link' => route('anggota.booking.show', $booking->id),
        ]);
        
        return redirect()->route('petugas.booking.index')
            ->with('success', 'Booking berhasil ditolak.');
    }

    /**
     * Proses ambil buku (konversi booking menjadi peminjaman)
     */
    public function processPickup(Request $request, $id)
    {
        $request->validate([
            'tanggal_pinjam' => 'required|date',
            'tgl_jatuh_tempo' => 'required|date|after:tanggal_pinjam',
        ]);
        
        $booking = Booking::with(['buku', 'user'])->findOrFail($id);
        
        if ($booking->status !== 'disetujui') {
            return back()->with('error', 'Booking ini belum disetujui atau sudah diproses.');
        }
        
        if (now()->greaterThan($booking->batas_ambil)) {
            return back()->with('error', 'Booking sudah melewati batas waktu ambil. Silakan hanguskan terlebih dahulu.');
        }
        
        DB::beginTransaction();
        try {
            // Kurangi stok_direservasi, tambah stok_dipinjam
            $booking->buku->stok_direservasi -= 1;
            $booking->buku->stok_dipinjam += 1;
            $booking->buku->save();
            
            // Buat peminjaman baru
            $peminjaman = Peminjaman::create([
                'user_id' => $booking->user_id,
                'buku_id' => $booking->buku_id,
                'kode_eksemplar' => 'PJM-' . strtoupper(uniqid()),
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tgl_jatuh_tempo' => $request->tgl_jatuh_tempo,
                'status_pinjam' => 'dipinjam',
                'petugas_id' => Auth::id(),
                'keterangan' => 'Dari booking: ' . $booking->kode_booking,
            ]);
            
            // Update booking
            $booking->update([
                'status' => 'diambil',
                'diproses_menjadi_peminjaman_id' => $peminjaman->id,
                'petugas_id' => Auth::id(),
            ]);
            
            DB::commit();
            
            // Notifikasi ke anggota
            Notifikasi::create([
                'user_id' => $booking->user_id,
                'judul' => '📚 Buku Berhasil Diambil',
                'isi' => 'Buku "' . $booking->buku->judul . '" telah Anda ambil. Selamat membaca! Jangan lupa kembalikan tepat waktu.',
                'type' => 'success',
                'link' => route('anggota.peminjaman.riwayat'),
            ]);
            
            return redirect()->route('petugas.booking.index')
                ->with('success', 'Buku berhasil diproses menjadi peminjaman.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pengambilan: ' . $e->getMessage());
        }
    }

    /**
     * Hanguskan booking (otomatis atau manual)
     */
    public function expire($id)
    {
        $booking = Booking::with(['buku'])->findOrFail($id);
        
        if (!in_array($booking->status, ['menunggu', 'disetujui'])) {
            return back()->with('error', 'Booking tidak bisa dihanguskan.');
        }
        
        DB::beginTransaction();
        try {
            // Jika sudah disetujui, kembalikan stok
            if ($booking->status === 'disetujui') {
                $booking->buku->stok_tersedia += 1;
                $booking->buku->stok_direservasi -= 1;
                $booking->buku->save();
            }
            
            $booking->update([
                'status' => 'hangus',
                'petugas_id' => Auth::id(),
            ]);
            
            DB::commit();
            
            // Notifikasi ke anggota
            Notifikasi::create([
                'user_id' => $booking->user_id,
                'judul' => '⏰ Booking Buku Hangus',
                'isi' => 'Booking buku "' . $booking->buku->judul . '" telah hangus karena melewati batas waktu ambil.',
                'type' => 'warning',
                'link' => route('anggota.booking.show', $booking->id),
            ]);
            
            return redirect()->route('petugas.booking.index')
                ->with('success', 'Booking berhasil dihanguskan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghanguskan booking: ' . $e->getMessage());
        }
    }
}