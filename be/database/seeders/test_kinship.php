<?php

use App\Http\Controllers\Api\ThanhVienController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Resolve controller
$controller = app(ThanhVienController::class);

// Get members
$tan = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Tân')->first();
$ti = DB::table('thanh_viens')->where('ho_ten', 'Lê Thị Tí')->first();
$thang = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Đức Thắng')->first();
$trung = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Văn Trung')->first();
$huong = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Thị Hương')->first();
$mai = DB::table('thanh_viens')->where('ho_ten', 'Phạm Thị Mai')->first();
$trang = DB::table('thanh_viens')->where('ho_ten', 'Đỗ Thu Trang')->first();
$tuan = DB::table('thanh_viens')->where('ho_ten', 'Lê Anh Tuấn')->first();
$nam = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Đức Nam')->first();
$minh = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Văn Minh')->first();
$ha = DB::table('thanh_viens')->where('ho_ten', 'Lê Thu Hà')->first();
$hoaiNam = DB::table('thanh_viens')->where('ho_ten', 'Nguyễn Hoài Nam')->first();

if (!$tan || !$ti || !$thang || !$trung || !$huong || !$mai || !$trang || !$tuan || !$nam || !$minh || !$ha || !$hoaiNam) {
    echo "❌ Error: One or more seeded members not found!\n";
    exit(1);
}

function assertRelation($controller, $idA, $idB, $expectedTerm, $scenario) {
    $request = new Request(['id_a' => $idA, 'id_b' => $idB]);
    $response = $controller->xacDinhQuanHe($request);
    $data = json_decode($response->getContent(), true);
    
    if (!$data['status']) {
        echo "❌ [FAIL] $scenario: API error: " . $data['message'] . "\n";
        return;
    }
    
    $actualTerm = $data['term'];
    if ($actualTerm === $expectedTerm) {
        echo "   [PASS] $scenario: '$actualTerm'\n";
    } else {
        echo "❌ [FAIL] $scenario: Expected '$expectedTerm', got '$actualTerm'\n";
    }
}

echo "=== STARTING AUTO KINSHIP TEST ===\n";

// 1. Spousal relations
assertRelation($controller, $tan->id, $ti->id, 'Vợ', 'Husband to Wife');
assertRelation($controller, $ti->id, $tan->id, 'Chồng', 'Wife to Husband');

// 2. Direct parent-child
assertRelation($controller, $thang->id, $tan->id, 'Bố / Cha', 'Son to Father');
assertRelation($controller, $thang->id, $ti->id, 'Mẹ', 'Son to Mother');
assertRelation($controller, $tan->id, $thang->id, 'Con trai', 'Father to Son');

// 3. Siblings
assertRelation($controller, $trung->id, $thang->id, 'Anh trai', 'Younger brother to Older brother');
assertRelation($controller, $thang->id, $trung->id, 'Em trai', 'Older brother to Younger brother');
assertRelation($controller, $thang->id, $huong->id, 'Em gái', 'Older brother to Younger sister');
assertRelation($controller, $huong->id, $thang->id, 'Anh trai', 'Younger sister to Older brother');

// 4. Uncles/Aunts and Nephews/Nieces
assertRelation($controller, $nam->id, $trung->id, 'Chú ruột', 'Nephew to Father\'s younger brother');
assertRelation($controller, $nam->id, $huong->id, 'Cô ruột', 'Nephew to Father\'s younger sister');
assertRelation($controller, $minh->id, $thang->id, 'Bác ruột', 'Nephew to Father\'s older brother');
assertRelation($controller, $minh->id, $huong->id, 'Cô ruột', 'Nephew to Father\'s younger sister');

// 5. In-laws (Dâu/Rể, Bác dâu, Thím, Dượng, Mợ)
assertRelation($controller, $nam->id, $trang->id, 'Thím', 'Nephew to Chú\'s Wife');
assertRelation($controller, $minh->id, $mai->id, 'Bác dâu', 'Nephew to Bác\'s Wife');
assertRelation($controller, $nam->id, $tuan->id, 'Dượng', 'Nephew to Cô\'s Husband');
assertRelation($controller, $ha->id, $mai->id, 'Mợ', 'Niece to Mother\'s brother\'s Wife');

// 6. Cousin relationships
assertRelation($controller, $nam->id, $minh->id, 'Em họ', 'Cousin (Older father\'s son to Younger father\'s son)');
assertRelation($controller, $minh->id, $nam->id, 'Anh họ', 'Cousin (Younger father\'s son to Older father\'s son)');

// 7. Adoptive relationships
assertRelation($controller, $hoaiNam->id, $trung->id, 'Cha nuôi', 'Adopted child to Adoptive Father');
assertRelation($controller, $trung->id, $hoaiNam->id, 'Con nuôi', 'Adoptive Father to Adopted child');

echo "=== KINSHIP TEST COMPLETE ===\n";
