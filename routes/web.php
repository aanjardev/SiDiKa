<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminProductController;
use Illuminate\Support\Facades\Route;


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

Route::get("/", [PageController::class,"index"]);
Route::get("/about", [PageController::class,"about"]);
Route::get("/contact", [PageController::class,"contact"]);
Route::get("/admin", [PageController::class,"admin"]);



Route::get("/katalog", [ProductController::class, "index"])->name('product.index');
Route::get("/katalog/{id}", [ProductController::class, "show"])->name('product.show');
// Route::resource('admin', AdminProductController::class);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// Routenya admin
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Home
    Route::get('/dashboard', function () {
        return view('admin.index');
    })->name('dashboard');

    // Master Data
    Route::get('/products', function () { return view('admin.dataProduk'); })->name('products');
    Route::get('/customers', function () { return view('admin.dataPelanggan'); })->name('customers');
    Route::get('/employees', function () { return view('admin.dataKaryawan'); })->name('employees');
    Route::get('/categories', function () { return view('admin.dataKategori'); })->name('categories');
    Route::get('/branches', function () { return view('admin.dataCabang'); })->name('branches');

    // Transaksi
    Route::get('/sales', function () { return view('admin.dataPenjualan'); })->name('sales');
    Route::get('/purchases', function () { return view('admin.dataPembelian'); })->name('purchases');
    Route::get('/quality-control', function () { return view('admin.dataQC'); })->name('quality-control');

    // Manajemen
    Route::get('/catalog-settings', function () { return view('admin.catalog-settings'); })->name('catalog-settings.index');
    Route::get('/promotions', function () { return view('admin.promotions'); })->name('promotions.index');
    Route::get('/permissions', function () { return view('admin.permissions'); })->name('permissions.index');
});
