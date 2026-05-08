<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QrCodeHelper
{
    /**
     * Generate QR Code menggunakan API Google (FREE)
     */
    public static function generateGoogleQrCode($data, $size = 300)
    {
        $url = 'https://chart.googleapis.com/chart';
        $params = [
            'cht' => 'qr',
            'chs' => "{$size}x{$size}",
            'chl' => urlencode($data),
            'choe' => 'UTF-8'
        ];
        
        $fullUrl = $url . '?' . http_build_query($params);
        return $fullUrl;
    }
    
    /**
     * Generate QR Code menggunakan API QuickChart.io (FREE)
     */
    public static function generateQuickChartQrCode($data, $size = 300)
    {
        $url = 'https://quickchart.io/qr';
        $params = [
            'text' => $data,
            'size' => $size,
            'margin' => 2
        ];
        
        return $url . '?' . http_build_query($params);
    }
    
    /**
     * Generate dan simpan QR Code lokal
     */
    public static function generateAndSaveQrCode($data, $filename)
    {
        try {
            // Gunakan QuickChart API (gratis, tanpa API key)
            $qrUrl = self::generateQuickChartQrCode($data, 300);
            
            // Download dan simpan
            $response = Http::timeout(30)->get($qrUrl);
            
            if ($response->successful()) {
                $path = 'qrcodes/' . $filename . '.png';
                Storage::disk('public')->put($path, $response->body());
                return $path;
            }
            
            // Fallback ke Google API
            $qrUrl = self::generateGoogleQrCode($data, 300);
            $response = Http::timeout(30)->get($qrUrl);
            
            if ($response->successful()) {
                $path = 'qrcodes/' . $filename . '.png';
                Storage::disk('public')->put($path, $response->body());
                return $path;
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Gagal generate QR Code: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Generate QR Code sebagai Base64 (tanpa simpan file)
     */
    public static function generateBase64QrCode($data, $size = 300)
    {
        $qrUrl = self::generateQuickChartQrCode($data, $size);
        return $qrUrl;
    }
}