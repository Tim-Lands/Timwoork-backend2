<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaypalAccountRequest;
use App\Http\Requests\UpdatePaypalAccountRequest;
use App\Models\PaypalAccount;

class PaypalAccountController extends Controller
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
     * @param  \App\Http\Requests\StorePaypalAccountRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePaypalAccountRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PaypalAccount  $paypalAccount
     * @return \Illuminate\Http\Response
     */
    public function show(PaypalAccount $paypalAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PaypalAccount  $paypalAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(PaypalAccount $paypalAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePaypalAccountRequest  $request
     * @param  \App\Models\PaypalAccount  $paypalAccount
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePaypalAccountRequest $request, PaypalAccount $paypalAccount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PaypalAccount  $paypalAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaypalAccount $paypalAccount)
    {
        //
    }
}
