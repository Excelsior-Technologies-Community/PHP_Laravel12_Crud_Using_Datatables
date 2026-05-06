<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Default welcome page route
Route::get('/', function () {
    return view('welcome');
});

// Product Routes

// Show all products (index page)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Show form to create a new product
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');

// Store a new product in the database
Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');

// Show form to edit an existing product
Route::get('/products/edit/{id}', [ProductController::class, 'edit'])->name('products.edit');

// Update an existing product in the database
Route::post('/products/update/{id}', [ProductController::class, 'update'])->name('products.update');

// Show details of a single product
Route::get('/products/show/{id}', [ProductController::class, 'show'])->name('products.show');

// Delete a product (soft delete)
Route::delete('/products/delete/{id}', [ProductController::class, 'destroy'])->name('products.delete');


//  Restore
Route::post('/products/restore/{id}', [ProductController::class, 'restore']);

//  Toggle status
Route::post('/products/status/{id}', [ProductController::class, 'toggleStatus']);

// export products to Excel
Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');

Route::get('/products/suggestions', [ProductController::class, 'searchSuggestions'])->name('products.suggestions');