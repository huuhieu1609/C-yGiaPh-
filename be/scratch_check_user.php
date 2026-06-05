<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$admin = DB::table('nguoi_dungs')->where('email', 'admin@master.com')->first();
$roles = DB::table('chuc_vus')->get();

echo "=== ADMIN USER ===\n";
print_r($admin);

echo "\n=== ROLES ===\n";
print_r($roles);
