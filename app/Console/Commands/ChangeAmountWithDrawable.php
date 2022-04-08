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
        Amount::select('id', 'status', 'transfered_at')
        ->where('status', Amount::PENDING_AMOUNT)
        ->get()->map(function ($amount) {
            // عمل لووب من اجل فحص وقت تحويل الاموال
            if (Carbon::now()->toDateTimeString() >= $amount->transfered_at) {
                $amount->status = Amount::WITHDRAWABLE_AMOUNT;
                $amount->save();
            }
        });
        return 0;
    }
}
