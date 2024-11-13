<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});



//Route::get('/', function () {
//    return view('welcome');
//})->middleware(['verify.shopify'])->name('home');

//Route::get('/api/products', [ProductController::class, 'index'])->middleware('verify.shopify');

Route::get('/', [ProductController::class, 'index'])->middleware('verify.shopify');



