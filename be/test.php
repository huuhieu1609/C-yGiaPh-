<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$partners = App\Models\NguoiDung::where('is_doi_tac', 1)->get();
foreach ($partners as $p) {
    echo "Partner: " . $p->email . "\n";
}

