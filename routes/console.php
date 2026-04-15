<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Publicar automáticamente cada hora las publicaciones aprobadas cuya fecha ya llegó
Schedule::command('publicaciones:publicar')->hourly()->appendOutputTo(storage_path('logs/publicaciones.log'));
