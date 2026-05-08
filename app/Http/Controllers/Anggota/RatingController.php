<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\UlasanBuku;
use App\Models\Peminjaman;
use App\Models\PoinAnggota;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request, $bukuId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'ulasan' => 'nullable|string|max:1000'
        ]);
        
        $user = Auth::user();
        $buku = Buku::findOrFail($bukuId);
        
        $sudahRating = UlasanBuku::where('user_id', $user->id)
            ->where('buku_id', $bukuId)
            ->exists();
            
        if ($sudahRating) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memberikan rating untuk buku ini'
            ], 400);
        }
        
        $pernahPinjam = Peminjaman::where('user_id', $user->id)
            ->where('buku_id', $bukuId)
            ->where('status_pinjam', 'dikembalikan')
            ->exists();
        
        $isApproved = $pernahPinjam;
        
        $ulasan = UlasanBuku::create([
            'user_id' => $user->id,
            'buku_id' => $bukuId,
            'rating' => $request->rating,
            'ulasan' => $request->ulasan,
            'is_approved' => $isApproved
        ]);
        
        $avgRating = UlasanBuku::where('buku_id', $bukuId)
            ->where('is_approved', true)
            ->avg('rating');
        
        $totalUlasan = UlasanBuku::where('buku_id', $bukuId)
            ->where('is_approved', true)
            ->count();
        
        $buku->rating = round($avgRating ?? 0, 1);
        $buku->jumlah_ulasan = $totalUlasan;
        $buku->save();
        
        if ($isApproved) {
            PoinAnggota::tambahPoin(
                $user->id,
                5,
                'Memberi rating untuk buku: ' . $buku->judul,
                'rating_' . $ulasan->id
            );
        }
        
        $message = $isApproved 
            ? 'Terima kasih atas rating dan ulasannya!'
            : 'Terima kasih atas rating dan ulasannya! Ulasan Anda akan ditampilkan setelah diverifikasi petugas.';
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'average_rating' => $buku->rating,
                'total_ratings' => $buku->jumlah_ulasan,
                'user_rating' => $request->rating,
                'is_approved' => $isApproved
            ]
        ]);
    }
    
    public function update(Request $request, $bukuId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'ulasan' => 'nullable|string|max:1000'
        ]);
        
        $user = Auth::user();
        
        $ulasan = UlasanBuku::where('user_id', $user->id)
            ->where('buku_id', $bukuId)
            ->firstOrFail();
        
        $pernahPinjam = Peminjaman::where('user_id', $user->id)
            ->where('buku_id', $bukuId)
            ->where('status_pinjam', 'dikembalikan')
            ->exists();
        
        $ulasan->update([
            'rating' => $request->rating,
            'ulasan' => $request->ulasan,
            'is_approved' => $pernahPinjam
        ]);
        
        $buku = Buku::find($bukuId);
        $this->updateBukuRatingStats($buku);
        
        return response()->json([
            'success' => true,
            'message' => $pernahPinjam ? 'Rating berhasil diupdate' : 'Rating berhasil diupdate dan menunggu verifikasi',
            'data' => [
                'average_rating' => $this->getAverageRating($bukuId),
                'total_ratings' => $this->getTotalRatings($bukuId)
            ]
        ]);
    }
    
    public function getRating($bukuId)
    {
        $buku = Buku::with(['ulasan' => function($query) {
            $query->where('is_approved', true)->with('user');
        }])->findOrFail($bukuId);
        
        $userRating = null;
        $userUlasan = null;
        
        if (Auth::check()) {
            $userRating = $this->getUserRating(Auth::id(), $bukuId);
            $userUlasan = UlasanBuku::where('user_id', Auth::id())
                ->where('buku_id', $bukuId)
                ->first();
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'average_rating' => $this->getAverageRating($bukuId),
                'total_ratings' => $this->getTotalRatings($bukuId),
                'rating_distribution' => $this->getRatingDistribution($bukuId),
                'user_rating' => $userRating,
                'user_review' => $userUlasan ? [
                    'id' => $userUlasan->id,
                    'rating' => $userUlasan->rating,
                    'ulasan' => $userUlasan->ulasan,
                    'is_approved' => $userUlasan->is_approved
                ] : null,
                'reviews' => $buku->ulasan->map(function($ulasan) {
                    return [
                        'id' => $ulasan->id,
                        'user_name' => $ulasan->user->name,
                        'rating' => $ulasan->rating,
                        'ulasan' => $ulasan->ulasan,
                        'created_at' => $ulasan->created_at->diffForHumans()
                    ];
                })
            ]
        ]);
    }
    
    public function destroy($bukuId)
    {
        $user = Auth::user();
        
        $ulasan = UlasanBuku::where('user_id', $user->id)
            ->where('buku_id', $bukuId)
            ->firstOrFail();
        
        $ulasan->delete();
        
        $buku = Buku::find($bukuId);
        $this->updateBukuRatingStats($buku);
        
        return response()->json([
            'success' => true,
            'message' => 'Rating berhasil dihapus'
        ]);
    }

    public function create($peminjamanId)
    {
        $peminjaman = Peminjaman::with('buku')
            ->where('user_id', Auth::id())
            ->where('id', $peminjamanId)
            ->where('status_pinjam', 'dikembalikan')
            ->firstOrFail();
        
        $sudahRating = UlasanBuku::where('user_id', Auth::id())
            ->where('buku_id', $peminjaman->buku_id)
            ->exists();
        
        return view('anggota.pages.rating.create', compact('peminjaman', 'sudahRating'));
    }

    public function createFromBuku($bukuId)
    {
        $buku = Buku::findOrFail($bukuId);
        
        $pernahPinjam = Peminjaman::where('user_id', Auth::id())
            ->where('buku_id', $bukuId)
            ->where('status_pinjam', 'dikembalikan')
            ->exists();
        
        if (!$pernahPinjam) {
            return redirect()->route('anggota.riwayat.index')
                ->with('error', 'Anda hanya bisa memberi rating untuk buku yang pernah Anda pinjam dan kembalikan.');
        }
        
        $sudahRating = UlasanBuku::where('user_id', Auth::id())
            ->where('buku_id', $bukuId)
            ->exists();
        
        $existingRating = null;
        if ($sudahRating) {
            $existingRating = UlasanBuku::where('user_id', Auth::id())
                ->where('buku_id', $bukuId)
                ->first();
        }
        
        return view('anggota.pages.rating.buku-rating', compact('buku', 'sudahRating', 'existingRating'));
    }
    
    private function updateBukuRatingStats($buku)
    {
        $avgRating = $this->getAverageRating($buku->id);
        $totalRatings = $this->getTotalRatings($buku->id);
        
        $buku->rating = $avgRating;
        $buku->jumlah_ulasan = $totalRatings;
        $buku->save();
    }
    
    private function getAverageRating($bukuId)
    {
        return round(UlasanBuku::where('buku_id', $bukuId)
            ->where('is_approved', true)
            ->avg('rating') ?? 0, 1);
    }
    
    private function getTotalRatings($bukuId)
    {
        return UlasanBuku::where('buku_id', $bukuId)
            ->where('is_approved', true)
            ->count();
    }
    
    private function getRatingDistribution($bukuId)
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = UlasanBuku::where('buku_id', $bukuId)
                ->where('is_approved', true)
                ->where('rating', $i)
                ->count();
        }
        return $distribution;
    }
    
    private function getUserRating($userId, $bukuId)
    {
        $rating = UlasanBuku::where('user_id', $userId)
            ->where('buku_id', $bukuId)
            ->first();
            
        return $rating ? $rating->rating : null;
    }
}