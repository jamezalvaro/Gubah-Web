<?php

namespace App\Services\AIEngine;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiPromptService
{
    public function generateRecipe(array $materials): array
    {
        $ingredientList = implode(', ', $materials);

        $systemPrompt = "Kamu adalah 'GuBah AI Engine', asisten pakar inovasi produk kuliner khusus UMKM.
Input pengguna: {$ingredientList}

INSTRUKSI EVALUASI:
1. Jika input HANYA berupa PROFIL RASA (asam, manis) atau bahan NON-KULINER, kembalikan JSON murni:
{\"is_valid\": false, \"pesan_error\": \"Mohon masukkan nama bahan baku fisik yang valid.\"}

2. JIKA VALID SEBAGAI BAHAN KULINER FISIK, kembalikan JSON murni TANPA markdown:
{
    \"is_valid\": true,
    \"nama_produk\": \"Nama Produk Inovatif\",
    \"deskripsi\": \"Deskripsi singkat 1-2 kalimat.\",
    \"kebutuhan_bahan\": [
        {
            \"nama_bahan\": \"Nama Bahan Baku (Utama & Tambahan)\",
            \"takaran_praktis\": \"Contoh: 2 sdm, 1 siung, 1/2 sdt, 1 mangkok\",
            \"kebutuhan_gram_ml\": 30 
        }
    ],
    \"langkah_pembuatan\": [
        \"Langkah 1: ...\",
        \"Langkah 2: ...\"
    ]
}

ATURAN MUTLAK:
- 'kebutuhan_gram_ml' WAJIB berupa ANGKA BULAT (integer) estimasi berat/volume dari takaran_praktis (dalam satuan gram atau ml). Jangan isi dengan string.
- DILARANG menghitung harga modal.
- Output WAJIB JSON murni.";

        $routes = [
            [
                'provider' => 'gemini',
                'name' => 'Google Gemini API',
                'model' => env('GEMINI_MODEL', 'gemini-3.1-flash-lite'),
                'api_key' => env('GEMINI_API_KEY'),
            ],
            [
                'provider' => 'groq',
                'name' => 'Groq (Llama 3.1 Super Cepat)',
                'model' => env('GROQ_MODEL', 'llama-3.1-8b-instant'),
                'api_key' => env('GROQ_API_KEY'),
            ]
        ];

        foreach ($routes as $index => $route) {
            try {
                Log::info("9Router Engine: Mengalihkan request ke rute [{$route['name']}] menggunakan model [{$route['model']}]");

                if (empty($route['api_key'])) {
                    throw new Exception("API Key untuk {$route['name']} belum diatur di .env");
                }

                $responseData = [];
                $rawText = '';

                // ---------------------------------------------------
                // LOGIKA PENGIRIMAN: GOOGLE GEMINI
                // ---------------------------------------------------
                if ($route['provider'] === 'gemini') {
                    $baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$route['model']}:generateContent";
                    
                    $response = Http::withoutVerifying()->timeout(45)->withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post("{$baseUrl}?key={$route['api_key']}", [
                        'contents' => [['parts' => [['text' => $systemPrompt]]]],
                        'generationConfig' => [
                            'responseMimeType' => 'application/json',
                            'temperature' => 0.2, 
                        ]
                    ]);

                    if ($response->failed()) {
                        throw new Exception("Gemini Error: HTTP " . $response->status() . " - " . $response->body());
                    }

                    $responseData = $response->json();
                    $rawText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
                } 
                // ---------------------------------------------------
                // LOGIKA PENGIRIMAN: GROQ API
                // ---------------------------------------------------
                else if ($route['provider'] === 'groq') {
                    $baseUrl = "https://api.groq.com/openai/v1/chat/completions";
                    
                    $response = Http::withoutVerifying()->timeout(45)->withHeaders([
                        'Authorization' => "Bearer {$route['api_key']}",
                        'Content-Type' => 'application/json',
                    ])->post($baseUrl, [
                        'model' => $route['model'],
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt]
                        ],
                        'temperature' => 0.2,
                        'response_format' => ['type' => 'json_object'] // Memaksa Groq mengembalikan JSON murni
                    ]);

                    if ($response->failed()) {
                        throw new Exception("Groq Error: HTTP " . $response->status() . " - " . $response->body());
                    }

                    $responseData = $response->json();
                    $rawText = $responseData['choices'][0]['message']['content'] ?? '{}';
                }

                // --- PEMBERSIHAN & VALIDASI JSON ---
                $rawText = str_replace(['```json', '```'], '', $rawText);
                $parsedJson = json_decode(trim($rawText), true);

                if ($parsedJson && isset($parsedJson['is_valid'])) {
                    Log::info("9Router Engine: SUKSES! Data didapatkan dari [{$route['name']}]");
                    return $parsedJson; 
                }

                throw new Exception("Format JSON dari {$route['name']} tidak valid.");

            } catch (Exception $e) {
                Log::warning("9Router Engine: Rute [{$route['name']}] GAGAL -> " . $e->getMessage());
                
                if ($index === count($routes) - 1) {
                    Log::error("9Router Engine: FATAL ERROR. Semua rute API AI gagal merespons.");
                    break;
                }
            }
        }

        return [
            "is_valid" => false,
            "pesan_error" => "Mohon maaf, sistem 9Router kami mendeteksi bahwa seluruh server AI saat ini sedang sibuk (High Demand). Mohon tunggu beberapa saat dan klik Generate kembali."
        ];
    }
}