<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfilSekolahController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\SekolahController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordController;
use Illuminate\Support\Facades\DB;

// Admin: kelola pengguna
Route::middleware('role:admin')->prefix('admin')->name('admin.')
    ->group(function () {
        Route::resource('users', UserController::class)->except('show');
    });

Route::controller(PetaController::class)->group(function () {
    Route::get('/', 'index')->name('peta.index');
    Route::get('/data/sekolah', 'geojson')->name('peta.data');
    Route::get('/pencarian', 'cari')->name('peta.cari');
    Route::get('/klaster', 'klaster')->name('peta.klaster');
    Route::get('/peta-panas', 'panas')->name('peta.panas');
    Route::get('/wilayah', 'wilayah')->name('peta.wilayah');
    Route::get('/rute', 'rute')->name('peta.rute');
    Route::get('/cek-alamat', 'alamat')->name('peta.alamat');
    Route::get('/data/sekolah', 'geojson')->name('peta.data');
    Route::get('/data/wilayah', 'geojsonWilayah')->name('peta.data.wilayah');
});

// Publik
Route::get('/', [PetaController::class, 'index'])->name('peta.index');
Route::get('/data/sekolah', [PetaController::class, 'geojson'])->name('peta.data');

// Tamu
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});

// Pengguna yang sudah login
Route::middleware('auth')->group(function () {
    // Route Ubah Password
    Route::get('/password/change', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/password/change', [PasswordController::class, 'update'])->name('password.update');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Dinas: daftar, tambah, dan hapus sekolah
    Route::middleware('role:dinas')->prefix('dinas')->name('dinas.')
        ->group(function () {
            Route::resource('sekolah', SekolahController::class)
                ->only(['index', 'create', 'store', 'destroy']);
        });

    // Dinas dan Sekolah: ubah data (dibatasi Policy)
    Route::middleware('role:dinas,sekolah')->group(function () {
        Route::get('sekolah/{sekolah}/edit', [SekolahController::class, 'edit'])
            ->name('sekolah.edit');
        Route::put('sekolah/{sekolah}', [SekolahController::class, 'update'])
            ->name('sekolah.update');
        Route::get('sekolah/{sekolah}/profil', [ProfilSekolahController::class, 'edit'])->name('sekolah.profil.edit');
        Route::put('sekolah/{sekolah}/profil', [ProfilSekolahController::class, 'update'])->name('sekolah.profil.update');
});
    });

    Route::get('/health', function () {
    try {
        DB::connection()->getPdo();

        return response()->json([
            'status'    => 'healthy',
            'app'       => config('app.name'),
            'database'  => DB::connection()->getDatabaseName(),
            'driver'    => DB::connection()->getDriverName(),
            'timestamp' => now()->toDateTimeString(),
        ], 200);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'unhealthy',
            'error'  => 'Database connection failed',
        ], 500);
    }
});