<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



Schedule::command('flashsale:revert-stock')->everyMinute();


// schedule untuk clean expired promotion
Schedule::command('promotions:clean-expired')->everyMinute();
