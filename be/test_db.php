<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$chucNangs = \App\Models\ChucNang::all();
echo "TOTAL SYSTEM CHUC NANGS: " . count($chucNangs) . PHP_EOL;
foreach ($chucNangs as $cn) {
    echo "- ID: {$cn->id} | Name: {$cn->ten_chuc_nang} | Display For: {$cn->hien_thi_cho}" . PHP_EOL;
}

$tn = \Illuminate\Support\Facades\DB::table('chuc_vus')->where('ten_chuc_vu', 'Trưởng Nhánh')->first();
if ($tn) {
    $tnPerms = \App\Models\ChiTietPhanQuyen::join('chuc_nangs', 'chi_tiet_phan_quyens.chuc_nang_id', '=', 'chuc_nangs.id')
        ->where('chi_tiet_phan_quyens.chuc_vu_id', $tn->id)
        ->select('chuc_nangs.id', 'chuc_nangs.ten_chuc_nang', 'chuc_nangs.hien_thi_cho')
        ->get();
    echo PHP_EOL . "TRUONG NHANH ACTIVE PERMISSIONS COUNT: " . count($tnPerms) . PHP_EOL;
    foreach ($tnPerms as $p) {
        echo "- ID: {$p->id} | Name: {$p->ten_chuc_nang} | Display For: {$p->hien_thi_cho}" . PHP_EOL;
    }
}
