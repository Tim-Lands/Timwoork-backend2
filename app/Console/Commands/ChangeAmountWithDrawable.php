<?php

namespace App\Console\Commands;

use App\Models\Amount;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ChangeAmountWithDrawable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amount:withdrawable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change amount to ammount withdrawable';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // جلب الارصدة المعلقة
        $amounts = Amount::with('wallet.profile')
        ->where('transfered_at', '<=', Carbon::now())
        ->where('status', Amount::PENDING_AMOUNT)
            ->get();
        //return $amount;
        foreach ($amounts as $amount) {
            $amount->status = Amount::WITHDRAWABLE_AMOUNT;
            $amount->save();
            // المحفظة
            $pending_amount = $amount->wallet->amounts_pending - $amount->amount;
            $withdrawable_amount =$amount->wallet->withdrawable_amount + $amount->amount;
            // تعديل المحفظة
            $amount->wallet->update([
            'amounts_pending' => $pending_amount,
            'withdrawable_amount' => $withdrawable_amount,
        ]);
            // تعديل المبلغ المستخدم
            $amount->wallet->profile->update([
            'pending_amount' => $pending_amount,
            'withdrawable_amount' => $withdrawable_amount,
        ]);
        }

        return 0;
    }
}
