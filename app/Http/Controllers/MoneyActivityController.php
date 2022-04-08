<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMoneyActivityRequest;
use App\Http\Requests\UpdateMoneyActivityRequest;
use App\Models\MoneyActivity;

class MoneyActivityController extends Controller
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
     * @param  \App\Http\Requests\StoreMoneyActivityRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMoneyActivityRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MoneyActivity  $moneyActivity
     * @return \Illuminate\Http\Response
     */
    public function show(MoneyActivity $moneyActivity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MoneyActivity  $moneyActivity
     * @return \Illuminate\Http\Response
     */
    public function edit(MoneyActivity $moneyActivity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMoneyActivityRequest  $request
     * @param  \App\Models\MoneyActivity  $moneyActivity
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMoneyActivityRequest $request, MoneyActivity $moneyActivity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MoneyActivity  $moneyActivity
     * @return \Illuminate\Http\Response
     */
    public function destroy(MoneyActivity $moneyActivity)
    {
        //
    }
}
