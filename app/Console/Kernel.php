<?php

namespace App\Console;

use App\PlacementPrimary;
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
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->call(function(){

            $now = date('Y-m-d H:i:s');

            $placements = PlacementPrimary::where('status','application')->get();

            foreach ( $placements as $placement )
            {

                if( $now >= $placement['last_date_of_registration'] )
                {

                    $placement->update( array ( 'status' => 'closed' ) );

                }

            }

        })->hourly();

    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
