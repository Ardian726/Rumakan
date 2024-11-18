<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\ProductCategoriesController;
use App\Http\Controllers\DiscountCategoriesController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/customers-register', function () {
    return view('customers-register');
})->name('customers-register');

Route::get('/customers-login', function () {
    return view('customers-login');
})->name('customers-login');

Route::post('/customers-register', [CustomersController::class, 'register'])->name('customer.register');
Route::post('/customers-login', [CustomersController::class, 'login'])->name('customer.login');
Route::post('/customers-logout', [CustomersController::class, 'logout'])->name('customer.logout')->middleware('auth:customers');

Route::prefix('product-categories')->name('product_categories.')->group(function () {
    Route::get('/', [ProductCategoriesController::class, 'index'])->name('index');
    Route::get('/create', [ProductCategoriesController::class, 'create'])->name('create');
    Route::post('/', [ProductCategoriesController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ProductCategoriesController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ProductCategoriesController::class, 'update'])->name('update');
    Route::delete('/{id}', [ProductCategoriesController::class, 'destroy'])->name('destroy');
});

Route::prefix('discount-categories')->name('discount_categories.')->group(function () {
    Route::get('/', [DiscountCategoriesController::class, 'index'])->name('index');
    Route::get('/create', [DiscountCategoriesController::class, 'create'])->name('create');
    Route::post('/store', [DiscountCategoriesController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [DiscountCategoriesController::class, 'edit'])->name('edit');
    Route::put('/{id}', [DiscountCategoriesController::class, 'update'])->name('update');
    Route::delete('/{id}', [DiscountCategoriesController::class, 'destroy'])->name('destroy');
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductsController::class, 'index'])->name('products.index');
    Route::get('/create', [ProductsController::class, 'create'])->name('products.create');
    Route::post('/store', [ProductsController::class, 'store'])->name('products.store');
    Route::get('/{id}/edit', [ProductsController::class, 'edit'])->name('products.edit');
    Route::put('/{id}', [ProductsController::class, 'update'])->name('products.update');
    Route::delete('/{id}', [ProductsController::class, 'destroy'])->name('products.destroy');
});





require __DIR__.'/auth.php';
