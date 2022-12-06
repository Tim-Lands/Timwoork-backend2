<?php

namespace App\Http\Controllers;

use App\Models\ProfileSeller;
use Exception;
use Illuminate\Http\Request;

class ProfileSellerController extends Controller
{
    public function index(Request $request){
        try{
        $is_portfolio =!is_null($request->portfolio);
        $x_localization = 'ar';
        if ($request->hasHeader('X-localization')) {
            $x_localization = $request->header('X-localization');
        }
        $paginate = $request->query('paginate') ? $request->query('paginate') : 12;
        $res = $is_portfolio ?  ProfileSeller::select('id', 'steps', 'number_of_sales', 'portfolio', "bio_{$x_localization} AS bio", 'profile_id')
        ->whereNotNull('portfolio')
        ->with([
            'profile'=>function($q){
                $q->without(['wise_account', 'paypal_account', 'bank_account', 'bank_transfer_detail']);
            },
            'profile.badge'=>function($q) use($x_localization){
                $q->select('id', "name_{$x_localization} AS name");
            },
            'profile.level' =>function($q) use($x_localization){
                $q->select('id', "name_{$x_localization} AS name");
            },

        ])
        ->paginate($paginate)
        :ProfileSeller::select('id', 'steps', 'number_of_sales', 'portfolio', "bio_{$x_localization} AS bio", 'profile_id')
        ->with([
            'profile',
            'profile.badge'=>function($q) use($x_localization){
                $q->select('id', "name_{$x_localization} AS name");
            },
            'profile.level' =>function($q) use($x_localization){
                $q->select('id', "name_{$x_localization} AS name");
            },

        ])
        ->without(['profile.wise_account', 'profile.paypal_account', 'profile.bank_account', 'profile.bank_transfer_detail'])
        ->whereNotNull('profile_seller')
        ->paginate($paginate);
        if (!$res->isEmpty()) {
            return response()->success(__("messages.filter.filter_success"), $res);
        } else {
            return response()->success(__("messages.filter.filter_field"), [], 204);
        }
    }
    catch(Exception  $exc){
        echo $exc;
    }
}
    //
}
