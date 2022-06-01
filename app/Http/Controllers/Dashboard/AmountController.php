<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Amount;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AmountController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        // جلب جميع الحسابات
        $amounts = Amount::selection()->with(['wallet.profile'])->get();
        // اظهار العناصر
        return response()->success(__("messages.oprations.get_all_data"), $amounts);
    }

    /**
     * get_amounts_pending
     *
     * @return void
     */
    public function get_amounts_pending()
    {
        $amounts = Amount::with('wallet.profile')
                 ->where('transfered_at', '<=', Carbon::now())
                 ->where('status', Amount::PENDING_AMOUNT)
                ->get();

        // اظهار العناصر
        return response()->success(__("messages.oprations.get_all_data"), $amounts);
    }

    /**
     * get_amounts_withdrawable
     *
     * @return void
     */
    public function get_amounts_withdrawable()
    {
        $amounts = Amount::with('wallet.profile')
                 ->where('transfered_at', '<=', Carbon::now())
                 ->where('status', Amount::WITHDRAWABLE_AMOUNT)
                ->get();

        // اظهار العناصر
        return response()->success(__("messages.oprations.get_all_data"), $amounts);
    }

    public function change_to_withdrawable($id)
    {
        
    }
}
