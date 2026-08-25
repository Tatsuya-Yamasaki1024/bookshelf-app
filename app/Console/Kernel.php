<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * アプリケーションのスケジュールを定義する。
     *
     * @param  Schedule  $schedule  スケジューラー
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('reading-plans:process-reminders')
            ->dailyAt('00:00');
    }

    /**
     * アプリケーションのArtisanコマンドを登録する。
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
