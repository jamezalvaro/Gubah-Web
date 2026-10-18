<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [InventoryController::class, 'index'])->name('dashboard');
Route::post('/dashboard/generate', [InventoryController::class, 'generate'])->name('inventory.generate');