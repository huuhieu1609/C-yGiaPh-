<?php

use App\Http\Controllers\Api\ThanhVienController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

function assertRelation($controller, $memberAId, $memberBId, $expectedRelation, $contextMessage = '')
{
    $request = new Request([
        'thanh_vien_id' => $memberAId,
        'muc_tieu_id'   => $memberBId
    ]);

    try {
        $response = $controller->getQuanHe($request);
        $data = json_decode($response->getContent(), true);
        $actualRelation = $data['quan_he'] ?? 'Không xác định';

        if ($actualRelation === $expectedRelation) {
            echo "🟢 [PASS] {$contextMessage}: '{$actualRelation}'\n";
        } else {
            echo "🔴 [FAIL] {$contextMessage}: Kỳ vọng '{$expectedRelation}' nhưng nhận được '{$actualRelation}'\n";
        }
    } catch (\Exception $e) {
        echo "❌ [ERROR] {$contextMessage}: Lỗi thực thi - " . $e->getMessage() . "\n";
    }
}

echo "=== BẮT ĐẦU KIỂM THỬ LOGIC QUAN HỆ HỌ HÀNG (KINSHIP) ===\n\n";

// 2. Lấy thông tin các thành viên từ Database để lấy ID thực tế
$tan     = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Tân')->first();
$ti      = DB::table('thanh_viens')->where('ho_ten', 'Lê Thị Tí')->first();
$thang   = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Đức Thắng')->first();
$trung   = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Văn Trung')->first();
$huong   = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Thị Hương')->first();
$mai     = DB::table('thanh_viens')->where('ho_ten', 'Phạm Thị Mai')->first();
$trang   = DB::table('thanh_viens')->where('ho_ten', 'Đỗ Thu Trang')->first();
$tuan    = DB::table('thanh_viens')->where('ho_ten', 'Lê Anh Tuấn')->first();
$nam     = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Đức Nam')->first();
$minh    = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Văn Minh')->first();
$ha      = DB::table('thanh_viens')->where('ho_ten', 'Lê Thu Hà')->first();
$hoaiNam = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Hoài Nam')->first();

// Khởi tạo Controller cần test
$controller = app(ThanhVienController::class);

if (!$tan || !$thang || !$nam || !$minh) {
    echo "⚠️ [CẢNH BÁO] Không tìm thấy đầy đủ thành viên mẫu trong DB. Hãy chạy Seeder trước khi test.\n";
    exit(1);
}

assertRelation($controller, $thang->id, $tan->id, 'Con trai', 'Thắng đối với ông Tân (Cha)');
assertRelation($controller, $tan->id, $thang->id, 'Cha', 'Ông Tân đối với Thắng (Con trai)');
assertRelation($controller, $huong->id, $ti->id, 'Con gái', 'Hương đối với bà Tí (Mẹ)');

assertRelation($controller, $nam->id, $tan->id, 'Cháu nội', 'Nam đối với ông Tân (Ông nội)');
assertRelation($controller, $ha->id, $tan->id, 'Cháu ngoại', 'Hà đối với ông Tân (Ông ngoại)');
assertRelation($controller, $tan->id, $nam->id, 'Ông nội', 'Ông Tân đối với Nam (Cháu nội)');

assertRelation($controller, $thang->id, $trung->id, 'Anh trai', 'Thắng đối với Trung (Em trai)');
assertRelation($controller, $trung->id, $thang->id, 'Em trai', 'Trung đối với Thắng (Anh trai)');
assertRelation($controller, $huong->id, $thang->id, 'Em gái', 'Hương đối với Thắng (Anh trai)');

assertRelation($controller, $nam->id, $trung->id, 'Chú ruột', 'Nam đối với Trung (Chú ruột)');
assertRelation($controller, $nam->id, $huong->id, 'Cô ruột', 'Nam đối với Hương (Cô ruột)');
assertRelation($controller, $minh->id, $thang->id, 'Bác ruột', 'Minh đối với Thắng (Bác ruột)');

assertRelation($controller, $nam->id, $trang->id, 'Thím', 'Nam đối với vợ của Chú (Thím)');
assertRelation($controller, $minh->id, $mai->id, 'Bác dâu', 'Minh đối với vợ của Bác (Bác dâu)');
assertRelation($controller, $nam->id, $tuan->id, 'Dượng', 'Nam đối với chồng của Cô (Dượng)');
assertRelation($controller, $ha->id, $mai->id, 'Mợ', 'Hà đối với vợ của Cậu/Anh trai mẹ (Mợ)');

assertRelation($controller, $nam->id, $minh->id, 'Em họ', 'Nam (Con em) đối với Minh (Con anh)');
assertRelation($controller, $minh->id, $nam->id, 'Anh họ', 'Minh (Con anh) đối với Nam (Con em)');
assertRelation($controller, $ha->id, $nam->id, 'Chị em họ', 'Hà (Con cô) đối với Nam (Con cậu)');

echo "\n=== KẾT THÚC KIỂM THỬ ===\n";
