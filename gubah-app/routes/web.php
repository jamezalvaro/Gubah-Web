<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PremiumController; // Import Premium Controller
use App\Http\Middleware\CheckPremiumRole;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Area Publik / User Free
Route::get('/dashboard', [InventoryController::class, 'index'])->name('dashboard');
Route::post('/dashboard/generate', [InventoryController::class, 'generate'])->name('inventory.generate');

// Area Terkunci (Hanya Premium) - Dilindungi oleh Middleware
// Route::middleware([CheckPremiumRole::class])->group(function () {
    Route::get('/premium/kalkulator-hpp', [PremiumController::class, 'hppCalculator'])->name('premium.hpp');
    Route::post('/premium/kalkulator-hpp/hitung', [PremiumController::class, 'calculateHpp'])->name('premium.hpp.hitung');
// });