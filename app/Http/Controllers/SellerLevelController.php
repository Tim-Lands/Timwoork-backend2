<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSellerLevelRequest;
use App\Http\Requests\UpdateSellerLevelRequest;
use App\Models\SellerLevel;

class SellerLevelController extends Controller
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
     * @param  \App\Http\Requests\StoreSellerLevelRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSellerLevelRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SellerLevel  $sellerLevel
     * @return \Illuminate\Http\Response
     */
    public function show(SellerLevel $sellerLevel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SellerLevel  $sellerLevel
     * @return \Illuminate\Http\Response
     */
    public function edit(SellerLevel $sellerLevel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSellerLevelRequest  $request
     * @param  \App\Models\SellerLevel  $sellerLevel
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSellerLevelRequest $request, SellerLevel $sellerLevel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SellerLevel  $sellerLevel
     * @return \Illuminate\Http\Response
     */
    public function destroy(SellerLevel $sellerLevel)
    {
        //
    }
}
