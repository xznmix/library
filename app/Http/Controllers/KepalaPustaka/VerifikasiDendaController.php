<?php

namespace App\Http\Controllers\KepalaPustaka;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\Denda;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VerifikasiDendaController extends Controller
{
    public function index(Request $request)
    {
        $query = Denda::with(['peminjaman.user', 'peminjaman.buku' => fn($q) => $q->withTrashed(), 'peminjaman.petugas', 'anggota'])
            ->where('payment_status', '!=', 'paid');

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        } else {
            $query->where('payment_status', 'pending');
        }

        if ($request->filled('petugas')) {
            $query->whereHas('peminjaman', fn($q) => $q->where('petugas_id', $request->petugas));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('min_nominal')) {
            $query->where('jumlah_denda', '>=', $request->min_nominal);
        }

        if ($request->filled('max_nominal')) {
            $query->where('jumlah_denda', '<=', $request->max_nominal);
        }

        $dendas = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $statistik = [
            'pending'                 => Denda::where('payment_status', 'pending')->count(),
            'disetujui'               => Denda::where('payment_status', 'paid')->count(),
            'ditolak'                 => Denda::where('payment_status', 'failed')->count(),
            'total_nominal_pending'   => Denda::where('payment_status', 'pending')->sum('jumlah_denda'),
            'total_nominal_disetujui' => Denda::where('payment_status', 'paid')->sum('jumlah_denda'),
        ];

        $petugas = User::where('role', 'petugas')->get(['id', 'name']);

        $statistikPetugas = User::where('role', 'petugas')
            ->withCount([
                'denda as total_denda'    => fn($q) => $q->where('payment_status', '!=', 'paid'),
                'denda as denda_pending'  => fn($q) => $q->where('payment_status', 'pending'),
                'denda as denda_disetujui'=> fn($q) => $q->where('payment_status', 'paid'),
            ])
            ->get()
            ->map(function ($p) {
                $p->total_nominal = Denda::where('confirmed_by', $p->id)
                    ->where('payment_status', 'paid')
                    ->sum('jumlah_denda');
                return $p;
            });

        return view('kepala-pustaka.pages.verifikasi.index', compact(
            'dendas', 'statistik', 'petugas', 'statistikPetugas'
        ));
    }

    public function show($id)
    {
        $denda = Denda::with([
            'peminjaman.user',
            'peminjaman.buku' => fn($q) => $q->withTrashed(),
            'peminjaman.petugas',
            'anggota',
            'confirmedBy',
        ])->findOrFail($id);

        $peminjaman = $denda->peminjaman;

        // Hitung keterlambatan dengan null-safe
        $terlambat = 0;
        if ($peminjaman && $peminjaman->tgl_jatuh_tempo && $peminjaman->tanggal_pengembalian) {
            $jatuhTempo = Carbon::parse($peminjaman->tgl_jatuh_tempo);
            $kembali    = Carbon::parse($peminjaman->tanggal_pengembalian);
            if ($kembali->gt($jatuhTempo)) {
                $terlambat = $jatuhTempo->diffInDays($kembali);
            }
        }

        $userId = $peminjaman?->user_id;
        $riwayatAnggota = $userId
            ? Peminjaman::where('user_id', $userId)
                ->with(['buku' => fn($q) => $q->withTrashed()])
                ->latest()
                ->limit(5)
                ->get()
            : collect();

        return view('kepala-pustaka.pages.verifikasi.detail', compact(
            'denda', 'riwayatAnggota', 'terlambat'
        ));
    }

    public function verifikasi(Request $request, $id)
    {
        try {
            $request->validate([
                'status'         => 'required|in:disetujui,ditolak',
                'catatan'        => 'required_if:status,ditolak|nullable|string|max:500',
                'nominal_setuju' => 'nullable|numeric|min:0',
            ]);

            DB::beginTransaction();

            $denda      = Denda::findOrFail($id);
            $peminjaman = $denda->peminjaman;

            if ($denda->payment_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Denda ini sudah diproses sebelumnya.',
                ], 400);
            }

            if ($request->status === 'disetujui') {
                $denda->update([
                    'payment_status' => 'paid',
                    'status'         => 'lunas',
                    'paid_at'        => now(),
                    'confirmed_by'   => Auth::id(),
                ]);

                if ($peminjaman) {
                    $peminjaman->update(['status_verifikasi' => 'disetujui']);
                }
            } else {
                $denda->update([
                    'payment_status' => 'failed',
                    'status'         => 'failed',
                    'confirmed_by'   => Auth::id(),
                    'keterangan'     => $request->catatan,
                ]);

                if ($peminjaman) {
                    $peminjaman->update([
                        'status_verifikasi'  => 'ditolak',
                        'catatan_verifikasi' => $request->catatan,
                    ]);
                }
            }

            ActivityLog::create([
                'user_id'     => Auth::id(),
                'role'        => 'kepala_pustaka',
                'action'      => 'verifikasi_denda',
                'model'       => 'Denda',
                'model_id'    => $id,
                'description' => Auth::user()->name . ' ' .
                    ($request->status === 'disetujui' ? 'menyetujui' : 'menolak') .
                    ' denda Rp ' . number_format($denda->jumlah_denda, 0, ',', '.'),
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $request->status === 'disetujui'
                    ? 'Denda berhasil disetujui.'
                    : 'Denda berhasil ditolak.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function verifikasiMassal(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:denda,id',
            'status' => 'required|in:disetujui,ditolak',
        ]);

        DB::beginTransaction();

        try {
            $count      = 0;
            $totalDenda = 0;
            $gagal      = 0;

            foreach ($request->ids as $id) {
                $denda = Denda::find($id);

                if ($denda && $denda->payment_status === 'pending') {
                    if ($request->status === 'disetujui') {
                        $denda->update([
                            'payment_status' => 'paid',
                            'status'         => 'lunas',
                            'paid_at'        => now(),
                            'confirmed_by'   => Auth::id(),
                        ]);
                        $denda->peminjaman?->update(['status_verifikasi' => 'disetujui']);
                    } else {
                        $denda->update([
                            'payment_status' => 'failed',
                            'status'         => 'failed',
                            'confirmed_by'   => Auth::id(),
                        ]);
                        $denda->peminjaman?->update(['status_verifikasi' => 'ditolak']);
                    }

                    $totalDenda += $denda->jumlah_denda;
                    $count++;
                } else {
                    $gagal++;
                }
            }

            ActivityLog::create([
                'user_id'     => Auth::id(),
                'role'        => 'kepala_pustaka',
                'action'      => 'verifikasi_massal',
                'model'       => 'Denda',
                'description' => Auth::user()->name . ' melakukan verifikasi massal ' .
                    $count . ' denda dengan status ' . $request->status .
                    ($gagal > 0 ? " ({$gagal} dilewati)" : ''),
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);

            DB::commit();

            $message = "{$count} denda berhasil diverifikasi. " .
                'Total: Rp ' . number_format($totalDenda, 0, ',', '.');
            if ($gagal > 0) {
                $message .= " {$gagal} data dilewati.";
            }

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Statistik verifikasi per petugas
     */
    public function statistikPetugas()
    {
        $petugas = User::where('role', 'petugas')
            ->withCount([
                'denda as total_denda' => function ($q) {
                    $q->where('payment_status', '!=', 'paid');
                },
                'denda as denda_pending' => function ($q) {
                    $q->where('payment_status', 'pending');
                },
                'denda as denda_disetujui' => function ($q) {
                    $q->where('payment_status', 'paid');
                }
            ])
            ->get()
            ->map(function ($p) {
                $p->total_nominal = Denda::where('confirmed_by', $p->id)
                    ->where('payment_status', 'paid')
                    ->sum('jumlah_denda');
                return $p;
            });
        
        return response()->json($petugas);
    }
}