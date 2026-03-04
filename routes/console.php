<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Roda este comando diariamente as 9 horas.
Schedule::command('app:send-whatsapp-reminders')
    ->dailyAt('09:00')
    ->timezone('America/Sao_Paulo');

// Roda no primeiro dia do mês as 01h da madrugada.
Schedule::command('pmoc:generate')->monthlyOn(1, '01:00');

// Para testes.
// Schedule::command('pmoc:generate')->everyMinute();
