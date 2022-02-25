<?php

namespace App\Console\Commands;

use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CancelRequestItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cancel:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'canceled request item after 2 days';

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
        // جلب عناصر الطلبيات
        $items = Item::select('id', 'status', 'profile_seller_id')->with(['item_date_expired' =>
        function ($q) {
            $q->select('id', 'item_id', 'date_expired');
        },'profileSeller' =>function ($q) {
            $q->select('id', 'profile_id')->with('profile', function ($q) {
                $q->select('id', 'user_id')->with('user:id')->without('level', 'badge');
            })->without('level', 'badge');
        }])
        ->where('status', Item::STATUS_PENDING)
        ->get();

        // عمل لووب من اجل فحص وقت النافذ للطلبية
        foreach ($items as $item) {
            if ($item['item_date_expired']->date_expired != null && Carbon::now()->toDateTimeString() >= $item['item_date_expired']->date_expired) {
                $item->status = Item::STATUS_CANCELLED_BY_BUYER;
                $item->save();
                // ammount => عبد الله

                // notification => لعبد الله
            }
        }
        return 0;
    }
}
