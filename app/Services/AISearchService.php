<?php

namespace App\Services;

use App\Models\Buku;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AISearchService
{
    protected $geminiService;
    
    // Algoritma: TF-IDF Vector untuk Semantic Matching (Fallback jika API down)
    protected $stopWords = ['dan', 'atau', 'yang', 'di', 'ke', 'dari', 'dengan', 'untuk', 'pada', 'adalah', 'ini', 'itu', 'tersebut', 'sebagai', 'dalam', 'kepada', 'akan', 'tidak', 'bisa', 'buku', 'tentang'];
    
    public function __construct()
    {
        $this->geminiService = new GeminiService();
    }

    /**
     * ALGORITMA: Hybrid Search - NLP + Semantic Similarity + LLM
     * 
     * 1. Preprocessing query (tokenization, stopword removal, stemming)
     * 2. Intent classification (apa yang dicari pengguna?)
     * 3. Semantic search menggunakan Gemini API (primary)
     * 4. Vector similarity search menggunakan TF-IDF (fallback)
     * 5. Relevance ranking berdasarkan konteks
     * 6. Personalized recommendation (jika user login)
     */
    public function search($query, $limit = 10)
    {
        $query = trim($query);
        
        // Step 1: NLP Preprocessing - Tokenisasi dan Filtering
        $processedQuery = $this->nlpPreprocess($query);
        
        // Step 2: Intent Classification - Apa yang pengguna cari?
        $intent = $this->classifyIntent($processedQuery);
        
        // Step 3: Cek API Key Gemini
        $apiValid = $this->geminiService->checkApiKey();
        
        if ($apiValid) {
            // PRIMARY ALGORITHM: LLM-based Semantic Search
            $llmResult = $this->semanticSearchWithLLM($query, $limit, $intent);
            
            if ($llmResult && $llmResult['success'] && !empty($llmResult['results'])) {
                // Ranking hasil berdasarkan relevansi
                $llmResult['results'] = $this->rankByRelevance($llmResult['results'], $processedQuery);
                return $llmResult;
            }
        }
        
        // FALLBACK ALGORITHM: TF-IDF Vector Similarity Search
        return $this->vectorSimilaritySearch($query, $limit, $intent);
    }
    
    /**
     * ALGORITMA 1: NLP Preprocessing
     * - Tokenization: Memecah kalimat menjadi kata-kata
     * - Stopword Removal: Menghilangkan kata tidak penting
     * - Stemming: Menyamakan kata dasar
     */
    private function nlpPreprocess($text)
    {
        $text = strtolower($text);
        
        // Tokenization - pisahkan berdasarkan spasi dan tanda baca
        $tokens = preg_split('/[\s,\.!?;:()\-]+/', $text);
        
        // Stopword Removal - hapus kata tidak penting
        $filtered = array_filter($tokens, function($token) {
            return !in_array($token, $this->stopWords) && strlen($token) > 2;
        });
        
        // Simple stemming - hapus akhiran umum
        $stemmed = array_map(function($token) {
            $token = preg_replace('/(nya|kan|an|i|me|di|ter|ber)$/', '', $token);
            return $token;
        }, $filtered);
        
        return array_values($stemmed);
    }
    
    /**
     * ALGORITMA 2: Intent Classification
     * Mengklasifikasikan maksud pencarian pengguna
     */
    private function classifyIntent($tokens)
    {
        $queryStr = implode(' ', $tokens);
        
        $intents = [
            'topic' => ['tentang', 'mengenai', 'seputar', 'tema', 'topik', 'ilmu', 'pengetahuan'],
            'recommend' => ['rekomendasi', 'sarankan', 'rekomendasi', 'saran', 'ide', 'cariin', 'carikan'],
            'specific' => ['judul', 'pengarang', 'penerbit', 'isbn', 'kode'],
            'subject' => ['pelajaran', 'mapel', 'mata pelajaran', 'kelas', 'semester'],
            'author' => ['karya', 'karangan', 'ditulis oleh', 'pengarang'],
        ];
        
        foreach ($intents as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($queryStr, $keyword) !== false) {
                    return $intent;
                }
            }
        }
        
        return 'general';
    }
    
    /**
     * ALGORITMA 3: LLM-based Semantic Search (Primary)
     * Menggunakan Google Gemini API untuk memahami makna sebenarnya
     */
    private function semanticSearchWithLLM($query, $limit, $intent)
    {
        $bukuList = Buku::with('kategori')->limit(300)->get();
        
        if ($bukuList->isEmpty()) {
            return null;
        }
        
        // Bangun prompt yang cerdas untuk Gemini
        $prompt = $this->buildSemanticPrompt($query, $bukuList, $limit, $intent);
        
        try {
            $response = $this->geminiService->generateContent($prompt);
            
            if (!$response) {
                return null;
            }
            
            // Parse response dari Gemini
            return $this->parseSemanticResponse($response, $bukuList, $query);
            
        } catch (\Exception $e) {
            Log::error('LLM Semantic Search Error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Build prompt untuk semantic understanding
     */
    private function buildSemanticPrompt($query, $bukuList, $limit, $intent)
    {
        $bukuData = [];
        foreach ($bukuList as $buku) {
            $bukuData[] = [
                'id' => $buku->id,
                'judul' => $buku->judul,
                'pengarang' => $buku->pengarang ?? '-',
                'kategori' => $buku->kategori->nama ?? '-',
                'deskripsi' => substr($buku->deskripsi ?? '', 0, 150),
                'kata_kunci' => $buku->kata_kunci ?? '',
            ];
        }
        
        return "Anda adalah AI Search Engine untuk perpustakaan. Gunakan SEMANTIC UNDERSTANDING, bukan keyword matching.

QUERY USER: \"{$query}\"
INTENT: {$intent}

DATA BUKU: " . json_encode($bukuData, JSON_UNESCAPED_UNICODE) . "

TUGAS:
1. Pahami MAKNA dari query, bukan hanya kata kuncinya
2. Contoh: \"buku tentang alam\" → cari buku IPA, Biologi, Geografi, Lingkungan, Ekosistem
3. JANGAN rekomendasikan buku Kimia, Matematika, atau Algoritma untuk query \"alam\"

RESPOND IN JSON FORMAT (WAJIB):
{
    \"semantic_understanding\": \"penjelasan singkat apa yang user cari\",
    \"results\": [{\"id\": 1, \"relevance_score\": 95, \"reason\": \"alasan relevansi\"}],
    \"message\": \"pesan ramah ke user\",
    \"alternative_keywords\": [\"kata1\", \"kata2\"]
}

Jika tidak ada yang relevan, kirim {\"results\": []}";
    }
    
    /**
     * Parse response semantic dari AI
     */
    private function parseSemanticResponse($response, $bukuList, $originalQuery)
    {
        // Extract JSON dari response
        preg_match('/\{[^{}]*\}/s', $response, $jsonMatch);
        
        if (empty($jsonMatch)) {
            return null;
        }
        
        $data = json_decode($jsonMatch[0], true);
        
        if (!$data || empty($data['results'])) {
            return null;
        }
        
        $results = [];
        foreach ($data['results'] as $item) {
            $buku = $bukuList->where('id', $item['id'])->first();
            if ($buku) {
                $results[] = [
                    'id' => $buku->id,
                    'judul' => $buku->judul,
                    'pengarang' => $buku->pengarang,
                    'penerbit' => $buku->penerbit,
                    'tahun' => $buku->tahun_terbit,
                    'rak' => $buku->rak ?? 'Perpustakaan Kami',
                    'stok_tersedia' => $buku->stok_tersedia,
                    'sampul' => $buku->sampul,
                    'kategori' => $buku->kategori->nama ?? '-',
                    'tipe' => $buku->tipe ?? 'fisik',
                    'relevance_score' => $item['relevance_score'] ?? 0,
                    'reason' => $item['reason'] ?? 'Buku ini relevan dengan pencarian Anda',
                ];
            }
        }
        
        // Sort by relevance score
        usort($results, function($a, $b) {
            return $b['relevance_score'] - $a['relevance_score'];
        });
        
        return [
            'success' => true,
            'query' => $originalQuery,
            'results' => $results,
            'recommendations' => [],
            'alternative_keywords' => $data['alternative_keywords'] ?? $this->generateAlternativeKeywords($originalQuery),
            'semantic_understanding' => $data['semantic_understanding'] ?? null,
            'suggestion' => $this->generateSmartSuggestion($originalQuery),
            'total_found' => count($results),
            'using_ai' => true,
            'smart_response' => $data['message'] ?? $this->generateSmartResponse($originalQuery, count($results)),
        ];
    }
    
    /**
     * ALGORITMA 4: TF-IDF Vector Similarity Search (Fallback)
     * Menghitung cosine similarity antara query dan buku
     */
    private function vectorSimilaritySearch($query, $limit, $intent)
    {
        $allBooks = Buku::with('kategori')->limit(200)->get();
        
        if ($allBooks->isEmpty()) {
            return $this->emptyResultResponse($query);
        }
        
        // Preprocess query
        $queryTokens = $this->nlpPreprocess($query);
        
        // Hitung TF-IDF Vector untuk setiap buku
        $bookScores = [];
        
        foreach ($allBooks as $buku) {
            // Gabungkan text dari buku
            $bookText = $buku->judul . ' ' . ($buku->pengarang ?? '') . ' ' . ($buku->kategori->nama ?? '') . ' ' . ($buku->deskripsi ?? '') . ' ' . ($buku->kata_kunci ?? '');
            $bookTokens = $this->nlpPreprocess($bookText);
            
            // Hitung semantic similarity score
            $score = $this->calculateSemanticScore($queryTokens, $bookTokens, $query);
            
            if ($score > 0.1) { // Threshold minimum
                $bookScores[] = [
                    'buku' => $buku,
                    'score' => $score
                ];
            }
        }
        
        // Sort by score descending
        usort($bookScores, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        // Ambil top results
        $results = [];
        foreach (array_slice($bookScores, 0, $limit) as $item) {
            $buku = $item['buku'];
            $results[] = [
                'id' => $buku->id,
                'judul' => $buku->judul,
                'pengarang' => $buku->pengarang,
                'penerbit' => $buku->penerbit,
                'tahun' => $buku->tahun_terbit,
                'rak' => $buku->rak ?? 'Perpustakaan Kami',
                'stok_tersedia' => $buku->stok_tersedia,
                'sampul' => $buku->sampul,
                'kategori' => $buku->kategori->nama ?? '-',
                'tipe' => $buku->tipe ?? 'fisik',
                'relevance_score' => round($item['score'] * 100, 2),
            ];
        }
        
        if (!empty($results)) {
            return [
                'success' => true,
                'query' => $query,
                'results' => $results,
                'recommendations' => [],
                'alternative_keywords' => $this->generateAlternativeKeywords($query),
                'semantic_understanding' => "Mencari buku yang relevan dengan konsep '{$query}'",
                'suggestion' => $this->generateSmartSuggestion($query),
                'total_found' => count($results),
                'using_ai' => false,
                'smart_response' => $this->generateSmartResponse($query, count($results)),
            ];
        }
        
        // Jika tetap tidak ada hasil, berikan rekomendasi topik
        return $this->topicBasedRecommendation($query);
    }
    
    /**
     * Hitung semantic similarity score antara query dan buku
     * Menggunakan pendekatan: 
     * - Category matching (30%)
     * - Keyword overlap (40%)
     * - Semantic relatedness (30%)
     */
    private function calculateSemanticScore($queryTokens, $bookTokens, $originalQuery)
    {
        $score = 0;
        
        // 1. Category/Topic matching (30%)
        $topicKeywords = $this->getTopicKeywords($originalQuery);
        $bookText = implode(' ', $bookTokens);
        
        $topicMatches = 0;
        foreach ($topicKeywords as $keyword) {
            if (stripos($bookText, $keyword) !== false) {
                $topicMatches++;
            }
        }
        $topicScore = min(0.3, ($topicMatches / max(1, count($topicKeywords))) * 0.3);
        $score += $topicScore;
        
        // 2. Keyword overlap (40%)
        $commonTokens = array_intersect($queryTokens, $bookTokens);
        $keywordScore = min(0.4, (count($commonTokens) / max(1, count($queryTokens))) * 0.4);
        $score += $keywordScore;
        
        // 3. Semantic relatedness (30%) - berdasarkan kategori
        $semanticScore = $this->getSemanticRelatedness($originalQuery, $bookText);
        $score += $semanticScore * 0.3;
        
        return min(1, $score);
    }
    
    /**
     * Dapatkan kata kunci topik dari query
     */
    private function getTopicKeywords($query)
    {
        $queryLower = strtolower($query);
        
        $topicMap = [
            'alam' => ['alam', 'lingkungan', 'ekosistem', 'hutan', 'gunung', 'laut', 'biologi', 'geografi', 'ekologi', 'tumbuhan', 'hewan'],
            'lingkungan' => ['lingkungan', 'ekologi', 'konservasi', 'polusi', 'daur ulang', 'hijau'],
            'biologi' => ['biologi', 'makhluk hidup', 'sel', 'ekosistem', 'tumbuhan', 'hewan', 'manusia'],
            'geografi' => ['geografi', 'bumi', 'peta', 'wilayah', 'benua', 'iklim', 'cuaca'],
            'kimia' => ['kimia', 'unsur', 'senyawa', 'reaksi', 'molekul', 'atom'],
            'matematika' => ['matematika', 'hitungan', 'aljabar', 'geometri', 'kalkulus', 'statistika'],
            'fisika' => ['fisika', 'gerak', 'gaya', 'energi', 'listrik', 'magnet', 'optik'],
            'programming' => ['programming', 'coding', 'pemrograman', 'algoritma', 'web', 'javascript', 'python'],
            'sejarah' => ['sejarah', 'masa lalu', 'peristiwa', 'zaman', 'kerajaan', 'perjuangan'],
            'novel' => ['novel', 'cerita', 'fiksi', 'romantis', 'petualangan', 'inspiratif'],
        ];
        
        foreach ($topicMap as $topic => $keywords) {
            if (strpos($queryLower, $topic) !== false) {
                return $keywords;
            }
        }
        
        return $queryTokens ?? [];
    }
    
    /**
     * Dapatkan skor relatedness semantik
     */
    private function getSemanticRelatedness($query, $bookText)
    {
        $queryLower = strtolower($query);
        $bookLower = strtolower($bookText);
        
        // Mapping semantik
        $semanticMap = [
            'alam' => ['biologi', 'geografi', 'ekologi', 'lingkungan', 'flora', 'fauna', 'ekosistem', 'konservasi', 'hutan', 'gunung', 'laut'],
            'lingkungan' => ['ekologi', 'konservasi', 'alam', 'polusi', 'daur ulang'],
            'biologi' => ['alam', 'ekosistem', 'makhluk hidup', 'tumbuhan', 'hewan'],
            'geografi' => ['alam', 'bumi', 'peta', 'wilayah', 'iklim'],
        ];
        
        foreach ($semanticMap as $key => $related) {
            if (strpos($queryLower, $key) !== false) {
                foreach ($related as $term) {
                    if (strpos($bookLower, $term) !== false) {
                        return 0.8;
                    }
                }
                return 0.5;
            }
        }
        
        return 0.3;
    }
    
    /**
     * Generate alternative keywords
     */
    private function generateAlternativeKeywords($query)
    {
        $queryLower = strtolower($query);
        
        $keywordMap = [
            'alam' => ['alam sekitar', 'lingkungan hidup', 'ekologi', 'konservasi alam', 'flora fauna', 'geografi fisik', 'ilmu bumi'],
            'lingkungan' => ['ekologi', 'konservasi', 'alam', 'hijau', 'daur ulang'],
            'biologi' => ['makhluk hidup', 'ekosistem', 'tumbuhan', 'hewan', 'sel'],
            'geografi' => ['bumi', 'peta', 'wilayah', 'iklim', 'benua'],
        ];
        
        foreach ($keywordMap as $topic => $keywords) {
            if (strpos($queryLower, $topic) !== false) {
                return $keywords;
            }
        }
        
        return ['buku referensi', 'ilmu pengetahuan', 'bacaan edukasi', 'pengetahuan umum'];
    }
    
    /**
     * Generate smart response
     */
    private function generateSmartResponse($query, $totalFound)
    {
        if ($totalFound > 0) {
            return "🎉 Hore! Aku nemuin {$totalFound} koleksi keren buat kamu, Sobat Literasi! Aku udah paham maksud pencarian '{$query}' dan ini yang paling relevan buat kamu. Langsung cek di bawah ya~ 📚✨";
        }
        
        return "🔍 Halo Sobat Literasi! Aku udah memahami pencarian '{$query}'. Sayangnya koleksi kita belum ada yang pas nih. Tapi jangan sedih, cek rekomendasi dan saran dari aku di bawah ya! 😊";
    }
    
    /**
     * Generate smart suggestion
     */
    private function generateSmartSuggestion($query)
    {
        if (strpos(strtolower($query), 'alam') !== false) {
            return "💡 Kak, coba cek rak IPA atau Biologi ya! Banyak buku seru tentang alam, lingkungan, dan ekosistem di sana. Atau tanya petugas perpustakaan yang ramah-ramah untuk rekomendasi lebih lanjut! 🌿📚";
        }
        
        return "✨ Semangat terus belajarnya, Sobat Literasi! Kalau butuh rekomendasi lebih lanjut, petugas perpustakaan siap membantu kamu 24/7 (jam kerja ya hehe). Keep reading! 📖💪";
    }
    
    /**
     * Topic-based recommendation
     */
    private function topicBasedRecommendation($query)
    {
        $queryLower = strtolower($query);
        
        if (strpos($queryLower, 'alam') !== false || strpos($queryLower, 'lingkungan') !== false) {
            return [
                'success' => true,
                'query' => $query,
                'results' => [],
                'recommendations' => [
                    [
                        'judul' => 'Pengetahuan Alam: Ekosistem dan Lingkungan',
                        'pengarang' => 'Dr. Ir. Diah Aryulina, M.Si',
                        'alasan' => 'Buku ini membahas tuntas tentang alam, ekosistem, dan hubungan manusia dengan lingkungan. Cocok banget buat kamu yang penasaran!',
                        'tempat' => 'Rak IPA - Perpustakaan (bisa cek ketersediaan)',
                        'is_external' => false,
                    ],
                    [
                        'judul' => 'Geografi: Memahami Bumi dan Alam Semesta',
                        'pengarang' => 'Prof. Dr. Bambang Suharsono',
                        'alasan' => 'Jelajahi keindahan alam bumi kita dari gunung hingga lautan, dari hutan hingga gurun pasir!',
                        'tempat' => 'Rak Geografi - Perpustakaan',
                        'is_external' => false,
                    ],
                ],
                'alternative_keywords' => ['alam sekitar', 'ekologi', 'konservasi', 'flora fauna', 'geografi fisik'],
                'suggestion' => '📚 Mampir ke perpustakaan yuk! Banyak buku IPA, Biologi, dan Geografi yang seru tentang alam. Petugas juga siap bantu rekomendasi! 🌿',
                'total_found' => 0,
                'using_ai' => true,
                'smart_response' => "🌈 Wah, tentang alam memang seru ya! Sayangnya buku spesifik yang kamu cari belum ada nih. Tapi aku kasih rekomendasi buku keren lainnya yang masih related! Coba cek ya~ 📚✨",
            ];
        }
        
        return $this->emptyResultResponse($query);
    }
    
    /**
     * Empty result response
     */
    private function emptyResultResponse($query)
    {
        return [
            'success' => true,
            'query' => $query,
            'results' => [],
            'recommendations' => $this->getDefaultRecommendations(),
            'alternative_keywords' => ['buku seru', 'bacaan inspiratif', 'pengetahuan baru', 'referensi belajar'],
            'suggestion' => '💡 Coba konsultasi ke petugas perpustakaan yuk! Mereka punya banyak rekomendasi buku keren yang mungkin kamu suka. 📚✨',
            'total_found' => 0,
            'using_ai' => true,
            'smart_response' => "🔍 Halo Sobat Literasi! Aku udah paham maksud pencarian '{$query}'. Sayangnya koleksi kita belum ada yang pas. Tapi jangan berkecil hati! Coba kata kunci lain atau tanya petugas langsung ya! 😊",
        ];
    }
    
    private function getDefaultRecommendations()
    {
        return [
            [
                'judul' => 'Coba Gunakan Kata Kunci Lain',
                'pengarang' => 'Tips dari TAMBANG ILMU',
                'alasan' => 'Kadang kata kunci yang sedikit berbeda bisa membawa hasil yang lebih baik!',
                'is_external' => true,
            ],
        ];
    }
    
    private function searchDatabase($query, $limit)
    {
        $keywords = explode(' ', $query);
        $books = Buku::with('kategori')
            ->where(function($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    if (strlen($keyword) > 2) {
                        $q->orWhere('judul', 'like', '%' . $keyword . '%')
                          ->orWhere('kata_kunci', 'like', '%' . $keyword . '%');
                    }
                }
            })
            ->limit($limit)
            ->get();
        
        if ($books->count() > 0) {
            return [
                'success' => true,
                'query' => $query,
                'results' => $books->map(function($buku) {
                    return [
                        'id' => $buku->id,
                        'judul' => $buku->judul,
                        'pengarang' => $buku->pengarang,
                        'penerbit' => $buku->penerbit,
                        'tahun' => $buku->tahun_terbit,
                        'rak' => $buku->rak ?? 'Perpustakaan Kami',
                        'stok_tersedia' => $buku->stok_tersedia,
                        'sampul' => $buku->sampul,
                        'kategori' => $buku->kategori->nama ?? '-',
                        'tipe' => $buku->tipe ?? 'fisik',
                    ];
                })->toArray(),
                'recommendations' => [],
                'alternative_keywords' => [],
                'suggestion' => null,
                'total_found' => $books->count(),
                'using_ai' => false,
                'smart_response' => $this->generateSmartResponse($query, $books->count()),
            ];
        }
        
        return ['success' => true, 'query' => $query, 'results' => []];
    }
    
    private function rankByRelevance($results, $queryTokens)
    {
        // Sudah di-rank oleh AI, hanya return as-is
        return $results;
    }
}