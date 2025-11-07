<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/products', AdminProductController::class)->names('products');
    Route::resource('/customers', CustomerController::class)->names('customers');
    Route::resource('/employees', EmployeeController::class)->names('employees');
    Route::resource('/categories', CategoryController::class)->names('categories');
    Route::resource('/branches', BranchController::class)->names('branches');

    // Transaksi
    Route::get('/sales', function () { return view('admin.dataPenjualan'); })->name('sales');
    Route::get('/purchases', function () { return view('admin.dataPembelian'); })->name('purchases');
    Route::get('/quality-control', function () { return view('admin.dataQC'); })->name('quality-control');

    // Manajemen
    Route::get('/catalog-settings', function () { return view('admin.catalog-settings'); })->name('catalog-settings.index');
    Route::get('/promotions', function () { return view('admin.promotions'); })->name('promotions.index');
    Route::get('/permissions', function () { return view('admin.permissions'); })->name('permissions.index');
});
