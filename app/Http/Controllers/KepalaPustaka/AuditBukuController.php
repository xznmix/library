<?php

namespace App\Http\Controllers\KepalaPustaka;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\StockOpnameLog;
use App\Models\Peminjaman;
use App\Models\AuditSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuditBukuController extends Controller
{
    /* =========================
     * INDEX (HALAMAN UTAMA)
     * ========================= */
    public function index(Request $request)
    {
        try {
            $bukuAudit = $this->getFilteredBuku($request);
            
            // Data statistik buku
            $statistikBuku = $this->getStatistikBuku();
            
            // Data kerugian
            $kerugianData = $this->getKerugianData();
            
            // Data grafik - PERBAIKAN!
            $grafikData = $this->getGrafikData();
            
            // Data opname & kategori
            $opnameData = $this->getOpnameData();
            
            // Data audit management
            $auditData = $this->getAuditManagementData();

            return view('kepala-pustaka.pages.audit.buku', array_merge(
                ['bukuAudit' => $bukuAudit],
                $statistikBuku,
                $kerugianData,
                $grafikData,
                $opnameData,
                $auditData
            ));

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * STOCK OPNAME (AJAX) - FIXED
     */
    public function stockOpname(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'buku_id' => 'required|exists:buku,id',
            'stok_fisik' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $buku = Buku::findOrFail($request->buku_id);
            $stokSistem = $buku->stok_tersedia;

            // Update stok
            if ($request->stok_fisik < $stokSistem) {
                $buku->stok_hilang += ($stokSistem - $request->stok_fisik);
            } elseif ($request->stok_fisik > $stokSistem) {
                $buku->stok_tersedia = $request->stok_fisik;
            } else {
                $buku->stok_tersedia = $request->stok_fisik;
            }
            
            $buku->stok_tersedia = $request->stok_fisik;
            $buku->save();

            // Create log
            StockOpnameLog::create([
                'buku_id' => $buku->id,
                'user_id' => Auth::id(),
                'stok_sistem' => $stokSistem,
                'stok_fisik' => $request->stok_fisik,
                'selisih' => abs($stokSistem - $request->stok_fisik),
                'keterangan' => $request->keterangan
            ]);

            // Complete audit queue
            $audit = AuditSchedule::where('buku_id', $buku->id)
                ->where('status', 'in_progress')
                ->first();

            if ($audit) {
                $audit->update([
                    'status' => 'completed',
                    'completed_date' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Stock opname berhasil disimpan!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '❌ Gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    /* =========================
     * UPDATE STATUS AUDIT (AJAX)
     * ========================= */
    public function updateAuditStatus(Request $request, $id)
    {
        try {
            $schedule = AuditSchedule::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,in_progress,completed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $schedule->status = $request->status;
            
            if ($request->status == 'completed') {
                $schedule->completed_date = now();
            }
            
            $schedule->save();

            return response()->json([
                'success' => true,
                'message' => 'Status audit berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    /* =========================
     * FILTER BUKU
     * ========================= */
    private function getFilteredBuku($request)
    {
        return Buku::with('kategori')
            ->withCount(['peminjaman as total_dipinjam' => function($q) {
                $q->select(DB::raw('COUNT(*)'));
            }])
            ->withCount(['peminjaman as dipinjam_saat_ini' => function($q) {
                $q->where('status_pinjam', 'dipinjam');
            }])
            ->when($request->kategori, fn($q) => $q->where('kategori_id', $request->kategori))
            ->when($request->kondisi, function ($q) use ($request) {
                match ($request->kondisi) {
                    'rusak' => $q->where('stok_rusak', '>', 0),
                    'hilang' => $q->where('stok_hilang', '>', 0),
                    'menipis' => $q->whereBetween('stok_tersedia', [1, 3]),
                    'habis' => $q->where('stok_tersedia', 0),
                    default => $q
                };
            })
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('judul', 'like', "%{$request->search}%")
                        ->orWhere('pengarang', 'like', "%{$request->search}%")
                        ->orWhere('isbn', 'like', "%{$request->search}%")
                        ->orWhere('kode_buku', 'like', "%{$request->search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }

    /* =========================
     * UPDATE STOK
     * ========================= */
    private function updateStok($buku, $stokFisik)
    {
        $stokSistem = $buku->stok_tersedia;

        if ($stokFisik < $stokSistem) {
            $buku->stok_hilang += ($stokSistem - $stokFisik);
        }

        $buku->stok_tersedia = $stokFisik;
        $buku->save();
    }

    /* =========================
     * LOG OPNAME (DIPERBAIKI)
     * ========================= */
    private function createLogOpname($buku, $request, $stokSistem)
    {
        StockOpnameLog::create([
            'buku_id' => $buku->id,
            'user_id' => Auth::id(),
            'stok_sistem' => $stokSistem,
            'stok_fisik' => $request->stok_fisik,
            'selisih' => abs($stokSistem - $request->stok_fisik),
            'keterangan' => $request->keterangan
        ]);
    }

    /* =========================
     * COMPLETE AUDIT
     * ========================= */
    private function completeAudit($bukuId)
    {
        $audit = AuditSchedule::where('buku_id', $bukuId)
            ->where('status', 'in_progress')
            ->first();

        if ($audit) {
            $audit->update([
                'status' => 'completed',
                'completed_date' => now()
            ]);
        }
    }

    /* =========================
     * STATISTIK BUKU (DIPERBAIKI)
     * ========================= */
    private function getStatistikBuku()
    {
        // Buku dengan stok menipis
        $bukuMenipis = Buku::where('stok_tersedia', '>', 0)
            ->where('stok_tersedia', '<=', 3)
            ->orderBy('stok_tersedia', 'asc')
            ->limit(10)
            ->get();

        // Buku habis
        $bukuHabis = Buku::where('stok_tersedia', 0)->count();

        return [
            'totalBuku' => Buku::count(),
            'totalEksemplar' => Buku::sum('stok'),
            'totalTersedia' => Buku::sum('stok_tersedia'),
            'bukuRusak' => Buku::sum('stok_rusak'),
            'bukuHilang' => Buku::sum('stok_hilang'),
            'bukuMenipis' => $bukuMenipis,
            'bukuHabis' => $bukuHabis,
        ];
    }

    /* =========================
     * DATA KERUGIAN (DIPERBAIKI)
     * ========================= */
    private function getKerugianData()
    {
        $data = Buku::select('id', 'judul', 'stok_rusak', 'stok_hilang', 'harga')
            ->where(function($q) {
                $q->where('stok_rusak', '>', 0)->orWhere('stok_hilang', '>', 0);
            })
            ->get()
            ->map(function ($buku) {
                $harga = $buku->harga ?? 50000;
                $buku->kerugian = ($buku->stok_rusak + $buku->stok_hilang) * $harga;
                return $buku;
            });

        return [
            'bukuKerugianTerbesar' => $data->sortByDesc('kerugian')->take(5)->values(),
            'totalKerugian' => Buku::all()->reduce(function($carry, $buku) {
                $harga = $buku->harga ?? 50000;
                return $carry + (($buku->stok_rusak + $buku->stok_hilang) * $harga);
            }, 0)
        ];
    }

    /* =========================
     * GRAFIK (DIPERBAIKI)
     * ========================= */
    private function getGrafikData()
    {
        $labels = [];
        $dataRusak = [];
        $dataHilang = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M Y');
            
            $dataRusak[] = StockOpnameLog::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->whereRaw('stok_sistem > stok_fisik')
                ->sum('selisih');

            $dataHilang[] = StockOpnameLog::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->whereRaw('stok_sistem < stok_fisik')
                ->sum('selisih');
        }

        return [
            'grafikKerusakan' => [
                'labels' => $labels,
                'rusak' => $dataRusak,
                'hilang' => $dataHilang
            ]
        ];
    }

    /* =========================
     * HISTORY + KATEGORI
     * ========================= */
    private function getOpnameData()
    {
        return [
            'historyOpname' => StockOpnameLog::with(['buku', 'user'])
                ->latest()
                ->limit(10)
                ->get(),
            'kategoriList' => \App\Models\KategoriBuku::orderBy('nama')->get()
        ];
    }

    /* =========================
     * AUTO AUDIT SYSTEM (DIPERBAIKI)
     * ========================= */
    private function getAuditManagementData()
    {
        // Buku bermasalah
        $bukuBermasalah = Buku::where(function ($q) {
            $q->where('stok_rusak', '>', 0)
                ->orWhere('stok_hilang', '>', 0)
                ->orWhere('stok_tersedia', '<=', 3);
        })->pluck('id')->toArray();

        // Buku dalam antrian
        $existing = AuditSchedule::whereIn('status', ['pending', 'in_progress'])
            ->pluck('buku_id')->toArray();

        // Auto tambah ke antrian
        $new = array_diff($bukuBermasalah, $existing);

        foreach ($new as $id) {
            AuditSchedule::updateOrCreate(
                ['buku_id' => $id, 'status' => 'pending'],
                [
                    'scheduled_date' => now()->addDay(),
                    'assigned_by' => Auth::id(),
                    'notes' => 'Auto generated dari buku bermasalah'
                ]
            );
        }

        // Antrian audit
        $antrianAudit = AuditSchedule::with(['buku', 'assignedBy'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Sudah diaudit
        $sudahDiaudit = AuditSchedule::with(['buku', 'assignedBy'])
            ->where('status', 'completed')
            ->orderBy('completed_date', 'desc')
            ->limit(10)
            ->get();

        // Statistik audit
        $statistikAudit = [
            'total_antrian' => AuditSchedule::whereIn('status', ['pending', 'in_progress'])->count(),
            'total_selesai' => AuditSchedule::where('status', 'completed')->count(),
            'total_bermasalah' => count($bukuBermasalah),
        ];

        return [
            'antrianAudit' => $antrianAudit,
            'sudahDiaudit' => $sudahDiaudit,
            'statistikAudit' => $statistikAudit
        ];
    }

    /* =========================
     * DETAIL BUKU
     * ========================= */
    public function detail($id)
    {
        try {
            $buku = Buku::with(['kategori', 'peminjaman' => function($q) {
                $q->with('user')->orderBy('created_at', 'desc')->limit(10);
            }])->findOrFail($id);

            $statistik = [
                'total_dipinjam' => Peminjaman::where('buku_id', $id)->count(),
                'dipinjam_saat_ini' => Peminjaman::where('buku_id', $id)->where('status_pinjam', 'dipinjam')->count(),
                'rata_rata_perbulan' => $this->getRataRataPeminjaman($id)
            ];

            $riwayatOpname = StockOpnameLog::with('user')
                ->where('buku_id', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('kepala-pustaka.pages.audit.detail', compact('buku', 'statistik', 'riwayatOpname'));
            
        } catch (\Exception $e) {
            return redirect()->route('kepala-pustaka.audit.buku')
                ->with('error', 'Buku tidak ditemukan');
        }
    }

    /* =========================
     * STOCK OPNAME PAGE
     * ========================= */
    public function stockOpnamePage()
    {
        try {
            $bukuList = Buku::orderBy('judul')->get(['id', 'judul', 'stok_tersedia', 'kode_buku']);
            $historyOpname = StockOpnameLog::with(['buku', 'user'])
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();
                
            return view('kepala-pustaka.pages.audit.stock-opname', compact('bukuList', 'historyOpname'));
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /* =========================
     * UPDATE KONDISI BUKU
     * ========================= */
    public function updateKondisi(Request $request, $id)
    {
        try {
            $request->validate([
                'stok_rusak' => 'nullable|integer|min:0',
                'stok_hilang' => 'nullable|integer|min:0',
                'keterangan' => 'required|string|max:500'
            ]);

            DB::beginTransaction();

            $buku = Buku::findOrFail($id);
            
            if ($request->has('stok_rusak')) {
                $buku->stok_rusak = $request->stok_rusak;
            }
            if ($request->has('stok_hilang')) {
                $buku->stok_hilang = $request->stok_hilang;
            }

            $buku->save();
            DB::commit();

            return redirect()->back()->with('success', 'Kondisi buku berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /* =========================
     * EXPORT EXCEL
     * ========================= */
    public function export(Request $request)
    {
        try {
            $query = Buku::with('kategori');

            if ($request->filled('kategori')) {
                $query->where('kategori_id', $request->kategori);
            }

            if ($request->filled('kondisi')) {
                switch ($request->kondisi) {
                    case 'rusak':
                        $query->where('stok_rusak', '>', 0);
                        break;
                    case 'hilang':
                        $query->where('stok_hilang', '>', 0);
                        break;
                    case 'menipis':
                        $query->whereRaw('stok_tersedia <= 3 AND stok_tersedia > 0');
                        break;
                }
            }

            $buku = $query->get();
            $filename = 'audit-buku-' . date('Y-m-d') . '.xls';
            
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=" . $filename);

            $output = '<table border="1">';
            $output .= '<tr><th>No</th><th>Kode</th><th>Judul</th><th>Pengarang</th><th>Kategori</th><th>Stok Total</th><th>Tersedia</th><th>Rusak</th><th>Hilang</th><th>Kerugian</th></tr>';

            foreach ($buku as $index => $item) {
                $harga = $item->harga ?? 50000;
                $kerugian = ($item->stok_rusak + $item->stok_hilang) * $harga;
                
                $output .= '<tr>';
                $output .= '<td>' . ($index + 1) . '</td>';
                $output .= '<td>' . ($item->kode_buku ?? 'B-' . str_pad($item->id, 5, '0', STR_PAD_LEFT)) . '</td>';
                $output .= '<td>' . $item->judul . '</td>';
                $output .= '<td>' . ($item->pengarang ?? '-') . '</td>';
                $output .= '<td>' . ($item->kategori->nama ?? '-') . '</td>';
                $output .= '<td>' . $item->stok . '</td>';
                $output .= '<td>' . $item->stok_tersedia . '</td>';
                $output .= '<td>' . $item->stok_rusak . '</td>';
                $output .= '<td>' . $item->stok_hilang . '</td>';
                $output .= '<td>Rp ' . number_format($kerugian, 0, ',', '.') . '</td>';
                $output .= '</tr>';
            }

            $output .= '</table>';
            echo $output;
            exit;

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    /* =========================
     * HELPER: RATA-RATA PEMINJAMAN
     * ========================= */
    private function getRataRataPeminjaman($bukuId)
    {
        $year = now()->year;
        $total = Peminjaman::where('buku_id', $bukuId)
            ->whereYear('created_at', $year)
            ->count();

        return $total > 0 ? round($total / 12, 1) : 0;
    }

    /* =========================
     * RESPONSE JSON
     * ========================= */
    private function jsonSuccess($msg)
    {
        return response()->json([
            'success' => true,
            'message' => '✅ ' . $msg
        ]);
    }

    private function jsonError($msg, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => '❌ ' . $msg
        ], $code);
    }
}