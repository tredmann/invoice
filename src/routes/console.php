<?php

use App\Jobs\SubscriptionHandler;
use App\Services\Invoices\InvoiceService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
|
| Migrated from App\Console\Kernel::schedule() during the Laravel 11
| application skeleton refactor.
|
*/

Schedule::call(function () {
    InvoiceService::updateOpenToOverdue();
})->daily();

Schedule::job(SubscriptionHandler::class)->daily();

if (config('backup.enabled') === true) {
    Schedule::command('backup:clean')->dailyAt('01:00');
    Schedule::command('backup:run')->dailyAt('02:00');
}
