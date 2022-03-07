<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWiseCountryRequest;
use App\Http\Requests\UpdateWiseCountryRequest;
use App\Models\WiseCountry;

class WiseCountryController extends Controller
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
     * @param  \App\Http\Requests\StoreWiseCountryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWiseCountryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WiseCountry  $wiseCountry
     * @return \Illuminate\Http\Response
     */
    public function show(WiseCountry $wiseCountry)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WiseCountry  $wiseCountry
     * @return \Illuminate\Http\Response
     */
    public function edit(WiseCountry $wiseCountry)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateWiseCountryRequest  $request
     * @param  \App\Models\WiseCountry  $wiseCountry
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWiseCountryRequest $request, WiseCountry $wiseCountry)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WiseCountry  $wiseCountry
     * @return \Illuminate\Http\Response
     */
    public function destroy(WiseCountry $wiseCountry)
    {
        //
    }
}
