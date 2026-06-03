<?php
require __DIR__ . '/../be/vendor/autoload.php';
$app = require_once __DIR__ . '/../be/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $targetEmail = 'nttthuy1403@gmail.com';
    
    // Tìm các email cũ để sửa đổi
    $emailsToUpdate = ['email_cua_ban@gmail.com', 'minhvy@master.com'];
    
    $updatedUsers = DB::table('nguoi_dungs')
        ->whereIn('email', $emailsToUpdate)
        ->update(['email' => $targetEmail]);
        
    $updatedMembers = DB::table('thanh_viens')
        ->whereIn('email', $emailsToUpdate)
        ->update(['email' => $targetEmail]);
        
    echo "Cập nhật thành công!\n";
    echo "- Số dòng nguoi_dungs đã sửa: {$updatedUsers}\n";
    echo "- Số dòng thanh_viens đã sửa: {$updatedMembers}\n";
    
} catch (\Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
