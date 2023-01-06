<?php

namespace App\Console;

use App\Console\Commands\ChangeAmount;
use App\Console\Commands\UpdateCurrency;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // الغاء الخدمات في حالة نفاذ وقتها
        //$schedule->command('cancel:request')->everyMinute();
        // تحويل الارصدة المعلقة
        $schedule->command('amount:withdrawable')->everyFiveMinutes();
        // حذف يومي للخدمات التي لم تتم تعبئتها
        $schedule->command('product:vide')->daily();
        $schedule->command('command:update_currency')
        ->everyFiveMinutes();
        $schedule->command('amount:change')
        ->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
