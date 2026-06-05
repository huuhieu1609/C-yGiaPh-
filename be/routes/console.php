<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cron: kiểm tra gói đối tác hết hạn mỗi ngày lúc 00:05
Schedule::command('cron:check-expired-packages')->dailyAt('00:05');

// Cron: kiểm tra giỗ chạp và sự kiện sắp diễn ra lúc 00:10 hàng ngày để gửi thông báo trước 3 ngày
Schedule::command('cron:send-anniversary-reminders')->dailyAt('00:10');
