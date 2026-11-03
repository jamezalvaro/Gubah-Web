<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Financial\HppCalculatorService;

class PremiumController extends Controller
{
    protected HppCalculatorService $hppService;

    // Inject Service Deterministik
    public function __construct(HppCalculatorService $hppService)
    {
        $this->hppService = $hppService;
    }

    public function hppCalculator(Request $request)
    {
        $aiBahan = $request->input('ai_bahan', []);
        return view('premium.hpp', compact('aiBahan'));
    }

    public function calculateHpp(Request $request)
    {
        // Validasi input matematika bersarang (Array Validation)
        $validated = $request->validate([
            'bahan' => 'required|array|min:1',
            'bahan.*.nama' => 'required|string',
            'bahan.*.harga_beli' => 'required|numeric|min:0',
            'bahan.*.qty_beli' => 'required|numeric|min:1',
            'bahan.*.qty_pakai' => 'required|numeric|min:0',
            'biaya_operasional' => 'nullable|numeric|min:0',
            'margin' => 'required|numeric|min:0|max:100',
        ]);

        // Eksekusi kalkulasi
        $hasilKalkulasi = $this->hppService->calculate($validated);

        // Kembalikan ke halaman kalkulator dengan membawa hasil perhitungan
        return back()->with('hasil_hpp', $hasilKalkulasi);
    }
}