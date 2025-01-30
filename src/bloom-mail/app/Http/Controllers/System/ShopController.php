<?php

namespace App\Http\Controllers\System;

use App\Models\Shop;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShopCreateRequest;
use App\Interfaces\ShopRepositoryInterface;

class ShopController extends Controller
{
    private ShopRepositoryInterface $shopRepository;

    public function __construct(ShopRepositoryInterface $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!check_user_permission('shop_read')) {
            return abort(401);
        }

        $shops = $this->shopRepository->index($request);

        return Inertia::render('System/Shop/Index', [
            "shops" => $shops['data']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!check_user_permission('shop_createdit')) {
            return abort(401);
        }

        return Inertia::render('System/Shop/CreateEdit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShopCreateRequest $request)
    {
        if (!check_user_permission('shop_createdit')) {
            return abort(401);
        }

        $createShop = $this->shopRepository->store($request);

        return redirect()->route('shops.index')->with('success', 'Form submitted successfully');
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
    public function edit(Shop $shop)
    {
        if (!check_user_permission('shop_createdit')) {
            return abort(401);
        }

        return Inertia::render('System/Shop/CreateEdit', [
            "shop" => $shop,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShopCreateRequest $request, Shop $shop)
    {
        if (!check_user_permission('shop_createdit')) {
            return abort(401);
        }

        $updateShop = $this->shopRepository->update($request, $shop);

        return redirect()->route('shops.index')->with('success', 'Form submitted successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shop $shop)
    {
        if (!check_user_permission('shop_delete')) {
            return abort(401);
        }

        $deleteShop = $this->shopRepository->delete($shop);

        return redirect()->back();
    }
}
