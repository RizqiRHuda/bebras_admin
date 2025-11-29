<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\Kegiatan\ChallengeController;
use App\Http\Controllers\Kegiatan\PengumumanController;
use App\Http\Controllers\Kegiatan\WorkshopController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\LatihanController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\TentangBebrasController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('app');
// });

Route::get('/', [AuthController::class, 'showLogin']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// khusus admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', fn() => view('admin.dashboard'))->name('admin.dashboard');
    Route::prefix('akun')->group(function () {
        Route::get('/register', [RegisterController::class, 'index'])->name('register');
        Route::get('/get-akun', [RegisterController::class, 'getAkun'])->name('register.get-akun');
        Route::post('/store', [RegisterController::class, 'store'])->name('store.akun');
        Route::get('/{user}/edit', [RegisterController::class, 'edit'])->name('edit.akun');
        Route::put('/{user}', [RegisterController::class, 'update'])->name('update.akun');
        Route::delete('/{user}', [RegisterController::class, 'destroy'])->name('destroy.akun');
    });

    Route::prefix('tentang-bebras')->group(function () {
        Route::get('/', [TentangBebrasController::class, 'index'])->name('tentang_bebras.index');
        Route::get('/form', [TentangBebrasController::class, 'create'])->name('form-tentang-bebras');
        Route::post('/store', [TentangBebrasController::class, 'store'])->name('tentang_bebras.store');
        Route::get('/get-data', [TentangBebrasController::class, 'getData'])->name('tentang_bebras.data');
        Route::get('/{id}', [TentangBebrasController::class, 'show'])->name('tentang_bebras.show');
        Route::get('/{id}/edit', [TentangBebrasController::class, 'edit'])->name('tentang_bebras.edit');
        Route::put('/{id}', [TentangBebrasController::class, 'update'])->name('tentang_bebras.update');
        Route::delete('/{id}', [TentangBebrasController::class, 'destroy'])->name('tentang_bebras.destroy');

    });

    Route::prefix('soal-bebras')->group(function () {
        Route::get('/', [SoalController::class, 'index'])->name('soal_bebras.index');
        Route::get('/form', [SoalController::class, 'create'])->name('form-soal-bebras');
        Route::post('/store', [SoalController::class, 'store'])->name('soal_bebras.store');
        Route::get('/{id}', [SoalController::class, 'show'])->name('soal_bebras.show');
        Route::get('/{id}/edit', [SoalController::class, 'edit'])->name('soal_bebras.edit');
        Route::put('/{id}', [SoalController::class, 'update'])->name('soal_bebras.update');
        Route::delete('/{id}', [SoalController::class, 'destroy'])->name('soal_bebras.destroy');
    });

    Route::prefix('kontak')->group(function () {
        Route::get('/', [KontakController::class, 'index'])->name('kontak.index');
        Route::post('/store', [KontakController::class, 'store'])->name('kontak.store');
        Route::get('/list', [KontakController::class, 'list'])->name('kontak.list');
        Route::get('/detail/{id}', [KontakController::class, 'detail'])->name('kontak.detail');
        Route::get('/{id}/edit', [KontakController::class, 'edit'])->name('kontak.edit');
        Route::put('/{id}', [KontakController::class, 'update'])->name('kontak.update');
        Route::delete('/{id}', [KontakController::class, 'destroy'])->name('kontak.destroy');
    });

    Route::prefix('latihan')->group(function () {
        Route::get('/', [LatihanController::class, 'index'])->name('latihan.index');
        Route::post('/store', [LatihanController::class, 'store'])->name('latihan.store');
        Route::get('/list', [LatihanController::class, 'list'])->name('latihan.list');
        Route::get('/{id}/edit', [LatihanController::class, 'edit'])->name('latihan.edit');
        Route::post('/{id}/update', [LatihanController::class, 'update'])->name('latihan.update');
        Route::delete('/{id}', [LatihanController::class, 'destroy'])->name('latihan.destroy');
        Route::get('/{id}/deskripsi', [LatihanController::class, 'deskripsi'])->name('latihan.deskripsi');
    });

    Route::prefix('kegiatan')->group(function () {
        Route::get('/workshop', [WorkshopController::class, 'index'])->name('workshop.index');
        Route::get('/workshop-page', [WorkshopController::class, 'dataWorkshop'])->name('page_workhop.index');
        Route::post('/simpan-workshop', [WorkshopController::class, 'store'])->name('workshop.store');
        Route::get('/workshop-list', [WorkshopController::class, 'listWorkshop'])->name('workshop.list');
        Route::get('/workshop/{id}/edit', [WorkshopController::class, 'edit'])->name('workshop.edit');
        Route::put('/workshop/{id}', [WorkshopController::class, 'update'])->name('workshop.update');
        Route::delete('/{id}', [WorkshopController::class, 'destroy'])->name('workshop.delete');

        // bebras challenge
        Route::get('/challenge-index', [ChallengeController::class, 'index'])->name('challenge.index');
        Route::get('/form-challenge', [ChallengeController::class, 'create'])->name('form-challenge.index');
        Route::post('/store-challenge', [ChallengeController::class, 'store'])
            ->name('form-challenge.store');
        Route::get('/getData-challenge', [ChallengeController::class, 'getData'])->name('getData-challenge');
        Route::get('/challenge/{id}/edit', [ChallengeController::class, 'edit'])->name('challenge.edit');
        Route::put('/challenge/{id}', [ChallengeController::class, 'update'])->name('challenge.update');
        Route::delete('/hapus-challenge/{id}', [ChallengeController::class, 'destroy'])->name('challenge.delete');

    });

    Route::prefix('pengumuman')->group(function () {
        Route::get('hasil-challenge', [PengumumanController::class, 'index'])->name('pengumuman.index');
        Route::post('simpan-pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
        Route::get('data-pengumuman', [PengumumanController::class, 'getData'])->name('pengumuman.data');
        Route::get('show/{id}', [PengumumanController::class, 'show'])->name('pengumuman.show');
        Route::get('edit/{id}', [PengumumanController::class, 'edit'])->name('pengumuman.edit');
        Route::put('update/{id}', [PengumumanController::class, 'update'])->name('pengumuman.update');
        Route::delete('hapus/{id}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy');
    });

});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', fn() => view('user.dashboard'))->name('user.dashboard');
    Route::prefix('berita')->group(function () {
        Route::get('/', [BeritaController::class, 'index'])->name('berita.index');
        Route::get('/form-berita', [BeritaController::class, 'create'])->name('form-berita.index');
        Route::post('/simpan-berita', [BeritaController::class, 'store'])->name('berita.store');
        Route::get('/data-berita', [BeritaController::class, 'dataBerita'])->name('data-berita.list');
        Route::get('edit/{id}', [BeritaController::class, 'edit'])->name('berita.edit');
        Route::put('update/{id}', [BeritaController::class, 'update'])->name('berita.update');
        Route::delete('hapus/{id}', [BeritaController::class, 'destroy'])->name('berita.destroy');
    });
});
