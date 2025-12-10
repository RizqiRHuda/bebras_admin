<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PengumumanController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API untuk File Pengumuman Bebras
// Data diambil langsung dari database yang sama
// API ini HANYA untuk akses file (download & view)
Route::prefix('files')->name('api.files.')->group(function () {
    // Download file Excel hasil pengumuman (force download)
    Route::get('/download/{id}', [PengumumanController::class, 'downloadFile'])->name('download');
    
    // View/Stream file untuk embed (return binary/redirect)
    Route::get('/view/{id}', [PengumumanController::class, 'getFileUrl'])->name('view');
    
    // Get file info dalam format JSON (untuk metadata)
    Route::get('/info/{id}', [PengumumanController::class, 'getFileInfo'])->name('info');
    
    // Check if file exists (untuk validasi)
    Route::get('/check/{id}', [PengumumanController::class, 'checkFile'])->name('check');
});
