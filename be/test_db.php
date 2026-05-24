<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Users count: " . \App\Models\NguoiDung::count() . PHP_EOL;
echo "DoiTac count: " . \App\Models\DoiTac::count() . PHP_EOL;
echo "ChiNhanh count: " . \App\Models\ChiNhanh::count() . PHP_EOL;
echo "ThanhVien count: " . \App\Models\ThanhVien::count() . PHP_EOL;

$dt = \App\Models\DoiTac::first();
if ($dt) {
    echo "DoiTac first record: " . json_encode($dt) . PHP_EOL;
}
$cn = \App\Models\ChiNhanh::first();
if ($cn) {
    echo "ChiNhanh first record: " . json_encode($cn) . PHP_EOL;
}
