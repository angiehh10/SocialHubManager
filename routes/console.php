<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// === Programación de tareas (Scheduler) ===
// Ejecuta tu “tick” cada minuto y evita solapes
Schedule::job(new \App\Jobs\ProcessQueueTick)
    ->everyMinute()
    ->withoutOverlapping(); // requiere locks del cache (database/redis)
