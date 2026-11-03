<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AIEngine\GeminiPromptService;
use Exception;

class InventoryController extends Controller
{
    protected GeminiPromptService $aiService;

    public function __construct(GeminiPromptService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        return view('dashboard');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'bahan_baku' => 'required|string|min:3'
        ]);

        $bahanArray = array_map('trim', explode(',', $request->bahan_baku));

        if (count($bahanArray) < 3) {
            return redirect()->route('dashboard')
                ->withInput()
                ->with('error', 'Mohon masukkan minimal 3 bahan baku yang dipisahkan dengan tanda koma (,).');
        }

        try {
            // Panggil Service AI
            $resepResult = $this->aiService->generateRecipe($bahanArray);

            // Jika AI mendeteksi bahan non-F&B atau Prompt Injection (Test Case AI-02 & AI-03)
            if (isset($resepResult['is_valid']) && $resepResult['is_valid'] === false) {
                return redirect()->route('dashboard')
                    ->withInput()
                    ->with('error', $resepResult['pesan_error'] ?? 'Bahan baku tidak valid untuk produk makanan/minuman.');
            }

            // Jika valid, tampilkan ke view dashboard
            return view('dashboard', [
                'resep' => $resepResult,
                'bahan_input' => $request->bahan_baku
            ])->with('success', '✨ Ide inovasi berhasil dibuat oleh AI!');

        } catch (Exception $e) {
            return redirect()->route('dashboard')
                ->withInput()
                ->with('error', 'Mesin ide kami sedang beristirahat. Silakan coba klik Generate sekali lagi dalam beberapa detik.');
        }
    }
}