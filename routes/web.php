<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', function () {
    return view('welcome');
})->middleware(['verify.shopify'])->name('home');

Route::get('/products', [ProductController::class, 'getProducts'])->middleware(['verify.shopify'])->name('products');
