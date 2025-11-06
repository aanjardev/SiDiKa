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
Route::get('/admin/data_cabang/add', function() {
    return view('admin.InputDataCabang');
});
Route::get('/admin/data_produk/add', function() {
    return view('admin.InputDataProduk');
});
Route::get('/admin/data_kategori/add', function() {
    return view('admin.InputDataKategori');
});
Route::get("/katalog", [ProductController::class, "index"])->name('product.index');
Route::get("/katalog/{id}", [ProductController::class, "show"])->name('product.show');
Route::resource('admin', AdminProductController::class);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route::middleware(['auth'])->prefix('admin')->group(function () {
//     Route::delete('/delete-image/{id}', [AdminProductController::class, 'deleteImage'])->name('admin.delete.image');
//     Route::resource('/', AdminProductController::class);
// });

// Routenya admin
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Home
    Route::get('/dashboard', function () {
        return view('admin.index');
    })->name('dashboard');

    // Master Data
    Route::get('/products', function () { return view('admin.products'); })->name('products.index');
    Route::get('/customers', function () { return view('admin.customers'); })->name('customers.index');
    Route::get('/employees', function () { return view('admin.employees'); })->name('employees.index');
    Route::get('/categories', function () { return view('admin.categories'); })->name('categories.index');
    Route::get('/branches', function () { return view('admin.branches'); })->name('branches.index');

    // Transaksi
    Route::get('/sales', function () { return view('admin.sales'); })->name('sales.index');
    Route::get('/purchases', function () { return view('admin.purchases'); })->name('purchases.index');
    Route::get('/quality-control', function () { return view('admin.qc'); })->name('qc.index');

    // Manajemen
    Route::get('/catalog-settings', function () { return view('admin.catalog-settings'); })->name('catalog-settings.index');
    Route::get('/promotions', function () { return view('admin.promotions'); })->name('promotions.index');
    Route::get('/permissions', function () { return view('admin.permissions'); })->name('permissions.index');
});
