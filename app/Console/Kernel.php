<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Menjalankan queue worker setiap menit.
        // --stop-when-empty: Worker akan mati sendiri jika tidak ada job (hemat resource).
        // --tries=3: Akan mencoba 3x jika job gagal sebelum dianggap 'failed'.
        // withoutOverlapping: Mencegah worker menumpuk jika proses sebelumnya belum selesai.
        $schedule->command('queue:work --stop-when-empty --tries=3')
                 ->everyMinute()
                 ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
