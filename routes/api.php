<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PengumumanController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API untuk Pengumuman Bebras
Route::prefix('pengumuman')->name('api.pengumuman.')->group(function () {
    // Mendapatkan daftar tahun yang tersedia
    Route::get('/tahun', [PengumumanController::class, 'getTahunList'])->name('tahun');
    
    // Mendapatkan hasil pengumuman berdasarkan tahun
    Route::get('/hasil/{tahun}', [PengumumanController::class, 'getHasilByTahun'])->name('hasil');
    
    // Download file untuk hasil pengumuman
    Route::get('/download/{id}', [PengumumanController::class, 'downloadFile'])->name('download');
});
