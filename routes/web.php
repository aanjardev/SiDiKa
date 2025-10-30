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
Route::resource('admin', AdminProductController::class);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route::middleware(['auth'])->prefix('admin')->group(function () {
//     Route::delete('/delete-image/{id}', [AdminProductController::class, 'deleteImage'])->name('admin.delete.image');
//     Route::resource('/', AdminProductController::class);
// });
