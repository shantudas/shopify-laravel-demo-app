<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function getProducts()
    {
        $shop = Auth::user();

        $request = $shop->api()->rest('GET', '/admin/products.json');
        $products = $request['body']['products'];

        return view('products', compact('products'));
    }
}
