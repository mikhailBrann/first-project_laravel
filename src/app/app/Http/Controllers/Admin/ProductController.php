<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\CreateProductNotificJob;
use App\Mail\CreateProductNotific;
use App\Http\Requests\Admin\ProductFormRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Mail;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth("admin")->user();
        if(!$user) {
            return redirect(route('admin.login'));
        }

        $products = Product::orderBy("status", "ASC")->paginate(12);
        foreach($products as $product) {
            $product->data = json_decode($product->data, true);
        }

        return view('admin.products.index' , [
            "products" => $products,
            "user" => $user,
        ]);
    }

    /**
     * Show detail product info.
     *
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $user = auth("admin")->user();
        if(!$user) {
            return redirect(route('admin.login'));
        }

        $product->data = json_decode($product->data, true);

        return view('admin.products.detail' , [
            "product" => $product,
            "user" => $user,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = auth("admin")->user();
        if(!$user) {
            return redirect(route('admin.login'));
        }

        return view('admin.products.create' , [
            "user" => $user,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ProductFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductFormRequest $request)
    {
        $user = auth("admin")->user();
        if(!$user) {
            return redirect(route('admin.login'));
        }

        $product = Product::create($request->validated());

        //создаем задачу на отправку письма о создании товара
        $this->dispatch(new CreateProductNotificJob($product, $user));

        return redirect(route('admin.products.index'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $user = auth("admin")->user();
        if(!$user) {
            return redirect(route('admin.login'));
        }

        return view('admin.products.create' , [
            "product" => $product,
            "user" => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductFormRequest $request
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductFormRequest $request, Product $product)
    {
        $user = auth("admin")->user();
        if(!$user) {
            return redirect(route('admin.login'));
        }

        $product->update($request->validated());

        return view('admin.products.create' , [
            "product" => $product,
            "user" => $user,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $user = auth("admin")->user();
        if(!$user) {
            return redirect(route('admin.login'));
        }

        $product->delete();

        return redirect(route('admin.products.index'));
    }
}
