<?php

namespace App\Console;

use App\Jobs\AddMonthlyLeaveBalance;
use App\Models\Leave;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    // protected function schedule(Schedule $schedule): void
    // {
    //     // $schedule->command('inspire')->hourly();
    // }


    /**
     * Define the application's command schedule.
     */



     protected function schedule(Schedule $schedule)
     {
         Log::info("Schedule is running...");
         
         $schedule->call(function () {
             Leave::where('to', '<', now())->update(['is_active' => false]);
         })->daily();
     
         $schedule->job(new \App\Jobs\AddMonthlyLeaveBalance)->everyTwoMinutes()
             ->onSuccess(function () {
                 Log::info("AddMonthlyLeaveBalance job dispatched successfully.");
             })
             ->onFailure(function (\Throwable $exception) { // Explicitly define \Throwable
                 Log::error("Failed to dispatch job: " . $exception->getMessage());
             });
     }
     



    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
