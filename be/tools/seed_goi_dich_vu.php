<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\GoiDichVu;

$defaultPackages = [
    [
        'ten_goi' => 'Gói Khởi Tạo',
        'gia_ca' => 100000,
        'thoi_han' => 12,
        'max_doi' => 5,
        'max_thanh_vien' => 100,
        'mo_ta' => 'Phù hợp cho chi ngành nhỏ hoặc dòng tộc bắt đầu số hóa phả hệ.',
        'trang_thai' => 'Hoạt động'
    ],
    [
        'ten_goi' => 'Gói Hưng Thịnh',
        'gia_ca' => 250000,
        'thoi_han' => 12,
        'max_doi' => 10,
        'max_thanh_vien' => 500,
        'mo_ta' => 'Giải pháp toàn diện cho các dòng tộc quy mô trung bình.',
        'trang_thai' => 'Hoạt động'
    ],
    [
        'ten_goi' => 'Gói Trường Tồn',
        'gia_ca' => 500000,
        'thoi_han' => 12,
        'max_doi' => 999,
        'max_thanh_vien' => 99999,
        'mo_ta' => 'Không giới hạn đặc quyền dành cho đại gia tộc lớn nhiều chi nhánh.',
        'trang_thai' => 'Hoạt động'
    ]
];

foreach ($defaultPackages as $pkg) {
    GoiDichVu::updateOrCreate(
        ['ten_goi' => $pkg['ten_goi']],
        $pkg
    );
}

echo "Seeded goi_dich_vus successfully!" . PHP_EOL;
