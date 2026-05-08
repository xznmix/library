<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    // Gunakan model yang tersedia dari daftar
    protected $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent'; // Ganti ke model yang valid

    public function __construct()
    {
        $this->apiKey = env('GOOGLE_GEMINI_API_KEY');
    }

    public function generateContent($prompt)
    {
        if (!$this->apiKey) {
            Log::warning('Gemini API Key tidak ditemukan');
            return null;
        }

        try {
            $url = $this->apiUrl . '?key=' . $this->apiKey;
            
            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 2048,
                ]
            ];
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            if ($curlError) {
                Log::error('cURL Error: ' . $curlError);
                return null;
            }
            
            if ($httpCode !== 200) {
                Log::error('Gemini API Error: HTTP ' . $httpCode . ' - ' . $response);
                return $this->tryAlternativeModel($prompt);
            }
            
            $data = json_decode($response, true);
            
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return $data['candidates'][0]['content']['parts'][0]['text'];
            }
            
            Log::warning('Gemini response no content: ' . $response);
            return null;
            
        } catch (\Exception $e) {
            Log::error('Gemini Exception: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Coba dengan model alternatif yang tersedia
     */
    private function tryAlternativeModel($prompt)
    {
        // Model yang tersedia dari daftar
        $alternativeModels = [
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent',
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-pro:generateContent',
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent',
        ];
        
        foreach ($alternativeModels as $modelUrl) {
            try {
                $url = $modelUrl . '?key=' . $this->apiKey;
                
                $payload = [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ];
                
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($httpCode === 200) {
                    $data = json_decode($response, true);
                    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                        Log::info('Berhasil menggunakan model alternatif: ' . $modelUrl);
                        return $data['candidates'][0]['content']['parts'][0]['text'];
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        return null;
    }

    public function checkApiKey()
    {
        if (empty($this->apiKey)) {
            return false;
        }
        
        // Test dengan model yang tersedia
        try {
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $this->apiKey;
            $payload = [
                'contents' => [
                    ['parts' => [['text' => 'test']]]
                ]
            ];
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            return $httpCode === 200;
        } catch (\Exception $e) {
            return false;
        }
    }
}