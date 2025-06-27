<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// routes/web.php

Route::get('/dashboard', [DashboardController::class, 'index'])
     ->name('dashboard'); // <- tanpa ->middleware('auth')

Route::get('/mahasiswa', function () {
    return '<h1>Halaman Daftar Mahasiswa</h1><a href="'.route('dashboard').'">Kembali ke Dashboard</a>';
})->name('mahasiswa.index'); // Kita beri nama 'mahasiswa.index'

Route::get('/mata-kuliah', function () {
    return '<h1>Halaman Daftar Mata Kuliah</h1><a href="'.route('dashboard').'">Kembali ke Dashboard</a>';
})->name('matakuliah.index'); // Kita beri nama 'matakuliah.index'

