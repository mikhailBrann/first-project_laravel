<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function getList()
    {
        $products = Product::orderBy("status", "ASC")->paginate(6);

        return view('products.list', [
            "products" => $products,
        ]);
    }

    public function getItem($id)
    {
        $product = Product::findOrFail($id);

        return view('products.detail', [
            "product" => $product,
        ]);
    }
}
