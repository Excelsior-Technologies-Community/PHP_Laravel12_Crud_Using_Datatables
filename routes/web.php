<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

// Product Routes - Simple Version
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');
Route::get('/products/edit/{id}', [ProductController::class, 'edit'])->name('products.edit');
Route::post('/products/update/{id}', [ProductController::class, 'update'])->name('products.update');
Route::get('/products/show/{id}', [ProductController::class, 'show'])->name('products.show');
Route::delete('/products/delete/{id}', [ProductController::class, 'destroy'])->name('products.delete');
Route::post('/products/status/{id}', [ProductController::class, 'toggleStatus']);
Route::post('/products/restore/{id}', [ProductController::class, 'restore']);
Route::get('/products/suggestions', [ProductController::class, 'searchSuggestions'])->name('products.suggestions');