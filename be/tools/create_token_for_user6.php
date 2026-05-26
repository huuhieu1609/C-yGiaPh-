<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\NguoiDung;

$u = NguoiDung::find(6);
if (! $u) {
    echo "User not found\n";
    exit(1);
}
$token = $u->createToken('cli-token')->plainTextToken;
echo $token . PHP_EOL;
