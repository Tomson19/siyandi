<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Command bawaan Laravel
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ================================
// 👇 Tambahan untuk auto cancel permohonan
// ================================

// Jalankan command auto cancel setiap hari jam 00:00
Schedule::command('permohonan:autocancel')->dailyAt('00:00');
