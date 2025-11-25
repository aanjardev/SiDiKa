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
use App\Http\Controllers\CatalogSettingsController;
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
    // Page for products that need photos (no images yet) - register BEFORE the resource so it isn't captured by /products/{id}
    Route::get('/products/photos', [AdminProductController::class, 'photos'])->name('products.photos');
    // Upload photos for a specific product (uploader role)
    Route::get('/products/{id}/photos-upload', [AdminProductController::class, 'photosUpload'])->name('products.photos.upload');
    Route::post('/products/{id}/photos-upload', [AdminProductController::class, 'photosUploadStore'])->name('products.photos.uploadStore');
    // Single AJAX upload for one image (used by the upload UI)
    Route::post('/products/{id}/photo-upload', [AdminProductController::class, 'photosUploadSingle'])->name('products.photos.uploadSingle');
    // Set a specific image as main
    Route::post('/products/{productId}/photos/{imageId}/set-main', [AdminProductController::class, 'setMainPhoto'])->name('products.photos.setMain');
    // Delete a product image
    Route::post('/products/{productId}/photos/{imageId}/delete', [AdminProductController::class, 'deletePhoto'])->name('products.photos.delete');
    Route::resource('/products', AdminProductController::class)->names('products');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products/store', [AdminProductController::class, 'store'])->name('products.store');
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::put('/products/{id}', [AdminProductController::class, 'update'])->name('products.update');
    Route::resource('/customers', CustomerController::class)->names('customers');
    Route::get('admin/customers/search', [CustomerController::class, 'search'])->name('customers.search');
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
    Route::post('/sales/checkout', [PenjualanController::class, 'checkout'])->name('sales.checkout');
    Route::resource('/purchases', PembelianController::class)->names('purchases');
    Route::post('/purchases/store-item-draft', [PembelianController::class, 'ajaxStoreItemDraft'])->name('purchases.ajaxStoreItemDraft');
    Route::put('/purchases/update-item-draft/{item_id}', [PembelianController::class, 'ajaxUpdateItemDraft'])->name('purchases.ajaxUpdateItemDraft');
    Route::delete('/purchases/delete-item-draft/{item_id}', [PembelianController::class, 'ajaxDeleteItemDraft'])->name('purchases.ajaxDeleteItemDraft');
    // Route untuk melihat arsip produk QC (tidak layak jual)
    // IMPORTANT: register this explicit route BEFORE the resource routes so
    // it doesn't get captured by the resource 'show' route (/quality-control/{id}).
    Route::get('/quality-control/archived', [QCController::class, 'archived'])->name('quality-control.archived');
    // Route untuk mengembalikan item dari arsip (restore)
    Route::post('/quality-control/{id}/restore', [QCController::class, 'restore'])->name('quality-control.restore');
    Route::resource('/quality-control', QCController::class)->names('quality-control');

    // Manajemen
    Route::get('/catalog-settings', [CatalogSettingsController::class, 'edit'])->name('catalog-settings.index');
    Route::post('/catalog-settings', [CatalogSettingsController::class, 'update'])->name('catalog-settings.update');
    Route::get('/promotions', function () { return view('admin.promotions'); })->name('promotions');

    Route::middleware('role:manager')->group(function () {
        Route::resource('/employees', EmployeeController::class)->names('employees');
        Route::get('/permissions', [PermissionsController::class, 'index'])->name('permissions');
        Route::get('/permissions/create', [PermissionsController::class, 'create'])->name('permissions.create');
        Route::post('/permissions', [PermissionsController::class, 'store'])->name('permissions.store');
        Route::get('/permissions/{id}/edit', [PermissionsController::class, 'edit'])->name('permissions.edit');
        Route::put('/permissions/{id}', [PermissionsController::class, 'update'])->name('permissions.update');
        Route::delete('/permissions/{id}', [PermissionsController::class, 'destroy'])->name('permissions.destroy');
        Route::get('purchases/{id}/print', [PembelianController::class, 'printNota'])->name('purchases.print');
    });

    Route::resource('/profile', ProfileController::class)->names('profile');

    // Route::get('purchases/{id}/print', [PembelianController::class, 'printNota'])->name('admin.purchases.print');
});
