<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBankTransferDetailRequest;
use App\Http\Requests\UpdateBankTransferDetailRequest;
use App\Models\BankTransferDetail;

class BankTransferDetailController extends Controller
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
     * @param  \App\Http\Requests\StoreBankTransferDetailRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBankTransferDetailRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BankTransferDetail  $bankTransferDetail
     * @return \Illuminate\Http\Response
     */
    public function show(BankTransferDetail $bankTransferDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BankTransferDetail  $bankTransferDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(BankTransferDetail $bankTransferDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBankTransferDetailRequest  $request
     * @param  \App\Models\BankTransferDetail  $bankTransferDetail
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBankTransferDetailRequest $request, BankTransferDetail $bankTransferDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BankTransferDetail  $bankTransferDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankTransferDetail $bankTransferDetail)
    {
        //
    }
}
