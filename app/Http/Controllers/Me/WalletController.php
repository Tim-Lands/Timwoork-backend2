<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    //
    public function index(){
        $wallet = Auth::user()->profile->wallet;
        return response()->json($wallet, 200);

    }
}