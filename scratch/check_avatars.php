<?php
require __DIR__ . '/../be/vendor/autoload.php';
$app = require_once __DIR__ . '/../be/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\NguoiDung;

$users = NguoiDung::all();
foreach ($users as $user) {
    echo "ID: " . $user->id . " - Name: " . $user->ho_ten . " - Avatar: " . $user->avatar . "\n";
}
