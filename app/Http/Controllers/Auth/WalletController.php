<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {

        /*         $user = Auth::user();
        $wallet = $user->profile->wallet()->select('amounts_total', 'amounts_pending')->first();
        return response()->json($wallet, 200); */

        /** @var User $user */
        return response()->json($user->first(), 200);
    }
}
