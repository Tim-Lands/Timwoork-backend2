<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    //
    public function index(Request $request){
        try{
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $title_localization = "title_{$x_localization}";
        $payment_method_localization = "payment_method_{$x_localization}";
        $wallet = Auth::user()->profile->wallet;
        $wallet->load(['activities'=>function($q){$q->select('*');}]);
        $wallet_json = $wallet;
        $wallet_json->activities=$wallet_json->activities->map(function($elm) use($title_localization, $payment_method_localization){
            $tmp_payload = $elm['payload'];
            $tmp_payload['title'] = $tmp_payload[$title_localization];
            if (isset($tmp_payload[$payment_method_localization])){
                $tmp_payload['payment_method'] = $tmp_payload[$payment_method_localization];
                unset($tmp_payload['payment_method_ar'], $tmp_payload['payment_method_en'], $tmp_payload['payment_method_fr']);
            }
            unset(
                $tmp_payload['title_ar'], $tmp_payload['title_en'], $tmp_payload['title_fr']
            );
            $elm['payload'] = $tmp_payload;
            return $elm;
        });
        
        return response()->json($wallet_json, 200);
    }
    catch(Exception $exc){
        echo $exc;
    }
    }
}