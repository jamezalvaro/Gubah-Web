<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventoryController extends Controller
{

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
            return back()->with('error', 'Mohon masukkan minimal 3 bahan baku yang dipisahkan dengan tanda koma (,).');
        }

        return back()->with('success', 'Bahan baku berhasil diproses! (Integrasi AI segera hadir).');
    }
}