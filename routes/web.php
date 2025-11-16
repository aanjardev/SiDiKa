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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\QCController;
use Illuminate\Support\Facades\Route;

use Symfony\Component\HttpKernel\Profiler\Profile;

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
    // Route::resource('/employees', EmployeeController::class)->names('employees');
    Route::resource('/categories', CategoryController::class)->names('categories');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories/store', [CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::resource('/branches', BranchController::class)->names('branches');
    Route::get('/branches/create', [BranchController::class, 'create'])->name('branches.create');
    Route::post('/branches/store', [BranchController::class, 'store'])->name('branches.store');
    Route::delete('/branches/{id}', [BranchController::class, 'destroy'])->name('branches.destroy');
    Route::put('/branches/{id}', [BranchController::class, 'update'])->name('branches.update');

    // Transaksi
    Route::resource('/sales', PenjualanController::class)->names('sales');
    Route::resource('/purchases', PembelianController::class)->names('purchases');
    Route::resource('/quality-control', QCController::class)->names('quality-control');

    // Manajemen
    Route::get('/catalog-settings', function () { return view('admin.catalog-settings'); })->name('catalog-settings.index');
    Route::get('/promotions', function () { return view('admin.promotions'); })->name('promotions');
    Route::middleware('role:manager')->group(function () {
        Route::resource('/employees', EmployeeController::class)->names('employees');
        Route::get('/permissions', [PermissionsController::class, 'index'])->name('permissions');
        Route::get('/permissions/create', [PermissionsController::class, 'create'])->name('permissions.create');
    });
    Route::resource('/profile', ProfileController::class)->names('profile');
});
