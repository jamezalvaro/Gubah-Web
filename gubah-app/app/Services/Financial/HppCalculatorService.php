<?php

namespace App\Services\Financial;

class HppCalculatorService
{
    /**
     * Menghitung HPP (Harga Pokok Penjualan) secara deterministik
     * Rumus: ((Harga Beli / Qty Beli) * Qty Pakai) + Biaya Operasional
     */
    public function calculate(array $data): array
    {
        $bahanList = $data['bahan'] ?? [];
        $operasional = $data['biaya_operasional'] ?? 0;
        $targetMargin = $data['margin'] ?? 0; // dalam persentase (%)

        $totalBiayaBahan = 0;
        $detailBahan = [];

        foreach ($bahanList as $bahan) {
            $hargaBeli = $bahan['harga_beli'] ?? 0;
            // Qty beli minimal 1 untuk menghindari error Division by Zero
            $qtyBeli = max((float) ($bahan['qty_beli'] ?? 1), 1); 
            $qtyPakai = (float) ($bahan['qty_pakai'] ?? 0);

            // Komputasi biaya akurat per bahan baku
            $biayaPerBahan = ($hargaBeli / $qtyBeli) * $qtyPakai;
            $totalBiayaBahan += $biayaPerBahan;

            $detailBahan[] = [
                'nama' => $bahan['nama'],
                'biaya_terpakai' => $biayaPerBahan
            ];
        }

        // Komputasi Final
        $hppTotal = $totalBiayaBahan + $operasional;
        $nominalKeuntungan = $hppTotal * ($targetMargin / 100);
        $hargaJualRekomendasi = $hppTotal + $nominalKeuntungan;

        // Kembalikan array terstruktur untuk dirender di UI
        return [
            'detail_bahan' => $detailBahan,
            'total_biaya_bahan' => $totalBiayaBahan,
            'biaya_operasional' => $operasional,
            'hpp_total' => $hppTotal,
            'target_margin_persen' => $targetMargin,
            'nominal_keuntungan' => $nominalKeuntungan,
            'harga_jual_rekomendasi' => $hargaJualRekomendasi
        ];
    }
}