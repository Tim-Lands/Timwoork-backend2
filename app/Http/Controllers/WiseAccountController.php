<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWiseAccountRequest;
use App\Http\Requests\UpdateWiseAccountRequest;
use App\Models\WiseAccount;

class WiseAccountController extends Controller
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
     * @param  \App\Http\Requests\StoreWiseAccountRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWiseAccountRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WiseAccount  $wiseAccount
     * @return \Illuminate\Http\Response
     */
    public function show(WiseAccount $wiseAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WiseAccount  $wiseAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(WiseAccount $wiseAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateWiseAccountRequest  $request
     * @param  \App\Models\WiseAccount  $wiseAccount
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWiseAccountRequest $request, WiseAccount $wiseAccount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WiseAccount  $wiseAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(WiseAccount $wiseAccount)
    {
        //
    }
}
