<?php

namespace App\Console\Commands;

use App\Models\Buku;
use App\Models\UlasanBuku;
use Illuminate\Console\Command;

class SyncBookRatings extends Command
{
    protected $signature = 'ratings:sync';
    protected $description = 'Sinkronisasi rating dari ulasan_buku ke tabel buku';

    public function handle()
    {
        $buku = Buku::all();
        
        foreach ($buku as $item) {
            $avgRating = UlasanBuku::where('buku_id', $item->id)
                ->where('is_approved', true)
                ->avg('rating');
            
            $totalUlasan = UlasanBuku::where('buku_id', $item->id)
                ->where('is_approved', true)
                ->count();
            
            $item->rating = round($avgRating ?? 0, 1);
            $item->jumlah_ulasan = $totalUlasan;
            $item->save();
            
            $this->info("Buku {$item->judul}: rating {$item->rating} ({$totalUlasan} ulasan)");
        }
        
        $this->info('Sinkronisasi selesai!');
    }
}