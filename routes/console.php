<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Scheduler untuk auto-deactivate diskon yang sudah expired
// Dijalankan setiap hari jam 00:01 (1 menit setelah tengah malam)
Schedule::command('discounts:deactivate-expired')
    ->dailyAt('00:01')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Scheduled task: Expired discounts deactivated successfully');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Scheduled task: Failed to deactivate expired discounts');
    });

// Scheduler untuk auto-expire pembayaran yang belum dibayar
// Dijalankan setiap menit untuk mengecek transaksi yang sudah lewat 10 menit
Schedule::command('payments:expire')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Scheduled task: Payment expiry check completed');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Scheduled task: Payment expiry check failed');
    });

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
