<?php

namespace App\Http\Controllers\KepalaPustaka;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VerifikasiDendaController extends Controller
{
    /**
     * Daftar denda yang perlu diverifikasi
     */
    public function index(Request $request)
    {
        $query = Peminjaman::with(['user', 'buku', 'petugas', 'diverifikasiOleh'])
            ->where('denda_total', '>', 0);
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status_verifikasi', $request->status);
        } else {
            // Default: pending dulu, lalu sisanya
            $query->orderByRaw("FIELD(status_verifikasi, 'pending', 'disetujui', 'ditolak')");
        }
        
        // Filter berdasarkan petugas
        if ($request->filled('petugas')) {
            $query->where('petugas_id', $request->petugas);
        }
        
        // Filter tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Filter nominal
        if ($request->filled('min_nominal')) {
            $query->where('denda_total', '>=', $request->min_nominal);
        }
        
        if ($request->filled('max_nominal')) {
            $query->where('denda_total', '<=', $request->max_nominal);
        }
        
        $dendas = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();
        
        // Statistik verifikasi REAL
        $statistik = [
            'pending' => Peminjaman::where('status_verifikasi', 'pending')
                ->where('denda_total', '>', 0)
                ->count(),
            'disetujui' => Peminjaman::where('status_verifikasi', 'disetujui')
                ->where('denda_total', '>', 0)
                ->count(),
            'ditolak' => Peminjaman::where('status_verifikasi', 'ditolak')
                ->where('denda_total', '>', 0)
                ->count(),
            'total_nominal_pending' => Peminjaman::where('status_verifikasi', 'pending')
                ->where('denda_total', '>', 0)
                ->sum('denda_total'),
            'total_nominal_disetujui' => Peminjaman::where('status_verifikasi', 'disetujui')
                ->where('denda_total', '>', 0)
                ->sum('denda_total'),
        ];
        
        // Daftar petugas untuk filter
        $petugas = User::where('role', 'petugas')->get(['id', 'name']);
        
        // Statistik per petugas
        $statistikPetugas = User::where('role', 'petugas')
            ->withCount([
                'peminjaman as total_denda' => function ($q) {
                    $q->where('denda_total', '>', 0);
                },
                'peminjaman as denda_pending' => function ($q) {
                    $q->where('status_verifikasi', 'pending')
                      ->where('denda_total', '>', 0);
                },
                'peminjaman as denda_disetujui' => function ($q) {
                    $q->where('status_verifikasi', 'disetujui')
                      ->where('denda_total', '>', 0);
                }
            ])
            ->get();
        
        return view('kepala-pustaka.pages.verifikasi.index', compact(
            'dendas', 
            'statistik', 
            'petugas',
            'statistikPetugas'
        ));
    }

    /**
     * Detail denda
     */
    public function show($id)
    {
        $denda = Peminjaman::with([
            'user', 
            'buku', 
            'petugas', 
            'diverifikasiOleh',
        ])->findOrFail($id);
        
        // Hitung keterlambatan
        $jatuhTempo = Carbon::parse($denda->tgl_jatuh_tempo);
        $kembali = Carbon::parse($denda->tanggal_pengembalian);
        $denda->lambat_hari = $kembali->diffInDays($jatuhTempo);
        
        // Hitung denda per hari (dari pengaturan)
        $denda->denda_per_hari = $denda->denda / max($denda->lambat_hari, 1);
        
        // Riwayat peminjaman anggota
        $riwayatAnggota = Peminjaman::where('user_id', $denda->user_id)
            ->with(['buku'])
            ->latest()
            ->limit(5)
            ->get();
        
        return view('kepala-pustaka.pages.verifikasi.detail', compact('denda', 'riwayatAnggota'));
    }

    /**
     * Proses verifikasi denda
     */
    public function verifikasi(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:disetujui,ditolak',
                'catatan' => 'required_if:status,ditolak|nullable|string|max:500',
                'nominal_setuju' => 'nullable|numeric|min:0'
            ]);

            DB::beginTransaction();
            
            $peminjaman = Peminjaman::findOrFail($id);
            
            // Cek apakah sudah diverifikasi sebelumnya
            if ($peminjaman->status_verifikasi != 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Denda ini sudah diverifikasi sebelumnya.'
                ], 400);
            }

            $dataUpdate = [
                'status_verifikasi' => $request->status,
                'diverifikasi_oleh' => Auth::id(),
                'diverifikasi_at' => now(),
                'catatan_verifikasi' => $request->catatan
            ];

            // Jika nominal disetujui berbeda (untuk negosiasi)
            if ($request->filled('nominal_setuju') && $request->status == 'disetujui') {
                $dataUpdate['denda_total'] = $request->nominal_setuju;
                $dataUpdate['denda_asli'] = $peminjaman->denda_total;
            }

            $peminjaman->update($dataUpdate);

            // Catat log aktivitas
            ActivityLog::create([
                'user_id' => Auth::id(),
                'role' => 'kepala_pustaka',
                'action' => 'verifikasi_denda',
                'model' => 'Peminjaman',
                'model_id' => $id,
                'description' => Auth::user()->name . ' ' . 
                    ($request->status == 'disetujui' ? 'menyetujui' : 'menolak') . 
                    ' denda Rp ' . number_format($peminjaman->denda_total, 0, ',', '.'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $request->status == 'disetujui' ? 'Denda berhasil disetujui.' : 'Denda ditolak.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifikasi massal (batch)
     */
    public function verifikasiMassal(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:peminjaman,id',
            'status' => 'required|in:disetujui,ditolak'
        ]);

        DB::beginTransaction();
        
        try {
            $count = 0;
            $totalDenda = 0;
            $gagal = 0;
            
            foreach ($request->ids as $id) {
                $peminjaman = Peminjaman::find($id);
                
                // Hanya proses yang masih pending
                if ($peminjaman->status_verifikasi == 'pending') {
                    $peminjaman->update([
                        'status_verifikasi' => $request->status,
                        'diverifikasi_oleh' => Auth::id(),
                        'diverifikasi_at' => now(),
                        'catatan_verifikasi' => 'Verifikasi massal'
                    ]);
                    
                    $totalDenda += $peminjaman->denda_total;
                    $count++;
                    
                    // Notifikasi untuk petugas
                    Notifikasi::create([
                        'user_id' => $peminjaman->petugas_id,
                        'judul' => 'Denda Diverifikasi Massal',
                        'isi' => 'Denda anda telah diverifikasi massal dengan status: ' . $request->status,
                        'type' => 'info'
                    ]);
                } else {
                    $gagal++;
                }
            }
            
            // Catat log aktivitas massal
            ActivityLog::create([
                'user_id' => Auth::id(),
                'role' => 'kepala_pustaka',
                'action' => 'verifikasi_massal',
                'model' => 'Peminjaman',
                'description' => Auth::user()->name . ' melakukan verifikasi massal ' . 
                    $count . ' denda dengan status ' . $request->status . 
                    ($gagal > 0 ? " ({$gagal} sudah terverifikasi sebelumnya)" : ""),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();

            $message = "✅ {$count} denda berhasil diverifikasi. ";
            $message .= "Total denda: Rp " . number_format($totalDenda, 0, ',', '.');
            
            if ($gagal > 0) {
                $message .= " ⚠️ {$gagal} data dilewati (sudah terverifikasi).";
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Statistik verifikasi per petugas (REAL DATA)
     */
    public function statistikPetugas()
    {
        $petugas = User::where('role', 'petugas')
            ->withCount([
                'peminjaman as total_transaksi' => function ($q) {
                    $q->where('denda_total', '>', 0);
                },
                'peminjaman as denda_pending' => function ($q) {
                    $q->where('status_verifikasi', 'pending')
                      ->where('denda_total', '>', 0);
                },
                'peminjaman as denda_disetujui' => function ($q) {
                    $q->where('status_verifikasi', 'disetujui')
                      ->where('denda_total', '>', 0);
                },
                'peminjaman as denda_ditolak' => function ($q) {
                    $q->where('status_verifikasi', 'ditolak')
                      ->where('denda_total', '>', 0);
                }
            ])
            ->get()
            ->map(function ($p) {
                $p->total_nominal = Peminjaman::where('petugas_id', $p->id)
                    ->where('status_verifikasi', 'disetujui')
                    ->sum('denda_total');
                
                $p->rata_denda = Peminjaman::where('petugas_id', $p->id)
                    ->where('status_verifikasi', 'disetujui')
                    ->where('denda_total', '>', 0)
                    ->avg('denda_total') ?? 0;
                
                return $p;
            });
        
        return response()->json($petugas);
    }
}