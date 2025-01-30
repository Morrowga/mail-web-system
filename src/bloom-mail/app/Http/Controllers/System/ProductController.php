<?php

namespace App\Http\Controllers\System;

use App\Models\Shop;
use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCreateRequest;
use App\Interfaces\ProductRepositoryInterface;

class ProductController extends Controller
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!check_user_permission('product_read')) {
            return abort(401);
        }

        $products = $this->productRepository->index($request);

        return Inertia::render('System/Product/Index', [
            "products" => $products['data']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!check_user_permission('product_createdit')) {
            return abort(401);
        }

        $shops = Shop::get();

        return Inertia::render('System/Product/CreateEdit', [
            "shops" => $shops
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductCreateRequest $request)
    {
        if (!check_user_permission('product_createdit')) {
            return abort(401);
        }

        $createSpam = $this->productRepository->store($request);

        return redirect()->route('products.index')->with('success', 'Form submitted successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        if (!check_user_permission('product_createdit')) {
            return abort(401);
        }

        $shops = Shop::get();

        return Inertia::render('System/Product/CreateEdit', [
            "product" => $product,
            "shops" => $shops
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductCreateRequest $request, Product $product)
    {
        if (!check_user_permission('product_createdit')) {
            return abort(401);
        }

        $updateProduct = $this->productRepository->update($request, $product);

        return redirect()->route('products.index')->with('success', 'Form submitted successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if (!check_user_permission('product_delete')) {
            return abort(401);
        }

        $deleteProduct = $this->productRepository->delete($product);

        return redirect()->back();
    }
}
