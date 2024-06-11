<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DatabaseQueueMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:db-monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if our database queue is still running';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $records = DB::table('jobs')->where('created_at', '<', Carbon::now()->subMinutes(5)->getTimestamp())->get();

        if (!$records->isEmpty()) {
            report('Queue jobs table should be emptied by now but it is not! Please check your queue worker.');

            $this->warn('Queue jobs table should be emptied by now but it is not! Please check your queue worker.');

            return;
        }

        $this->info('Queue jobs are looking good.');
    }
}
