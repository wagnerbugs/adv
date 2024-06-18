<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('')->everyMinute();
Schedule::command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping(10);
Schedule::command('queue:restart')->hourly();
Schedule::command('queue:db-monitor')->everyTenMinutes();
