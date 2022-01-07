<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSellerBadgeRequest;
use App\Http\Requests\UpdateSellerBadgeRequest;
use App\Models\SellerBadge;

class SellerBadgeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSellerBadgeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSellerBadgeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SellerBadge  $sellerBadge
     * @return \Illuminate\Http\Response
     */
    public function show(SellerBadge $sellerBadge)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SellerBadge  $sellerBadge
     * @return \Illuminate\Http\Response
     */
    public function edit(SellerBadge $sellerBadge)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSellerBadgeRequest  $request
     * @param  \App\Models\SellerBadge  $sellerBadge
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSellerBadgeRequest $request, SellerBadge $sellerBadge)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SellerBadge  $sellerBadge
     * @return \Illuminate\Http\Response
     */
    public function destroy(SellerBadge $sellerBadge)
    {
        //
    }
}
