<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChiNhanhController;
use App\Http\Controllers\Api\ChucNangController;
use App\Http\Controllers\Api\ChucVuController;
use App\Http\Controllers\Api\ConNuoiController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DeXuatController;
use App\Http\Controllers\Api\DoiTacController;
use App\Http\Controllers\Api\DoiTocHoController;
use App\Http\Controllers\Api\DongGopController;
use App\Http\Controllers\Api\GoiDichVuController;
use App\Http\Controllers\Api\HinhAnhController;
use App\Http\Controllers\Api\MoPhanController;
use App\Http\Controllers\Api\NguoiDungController;
use App\Http\Controllers\Api\NhaThoHoController;
use App\Http\Controllers\Api\NhatKyHoatDongController;
use App\Http\Controllers\Api\PhanQuyenController;
use App\Http\Controllers\Api\SuKienController;
use App\Http\Controllers\Api\TaiLieuController;
use App\Http\Controllers\Api\ThamGiaSuKienController;
use App\Http\Controllers\Api\ThanhVienController;
use App\Http\Controllers\Api\ThongBaoController;
use App\Http\Controllers\Api\TuongNiemController;
use App\Http\Controllers\Api\VoChongController;
use App\Http\Controllers\Api\YeuCauMuaGoiController;
use App\Http\Controllers\Api\QuanLyTaiKhoanController;
use App\Http\Controllers\ThanhToanController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\MemberRoleController;
use Illuminate\Support\Facades\Route;

// ... (vẫn giữ các use khác)

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware(['auth:sanctum', 'activity'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/me/roles-permissions', [MemberRoleController::class, 'myRolesAndPermissions']);
    Route::get('/me/notifications', [MemberRoleController::class, 'getNotifications']);
    Route::post('/me/notifications/read-all', [MemberRoleController::class, 'readAllNotifications']);
    Route::post('/thanh-vien/{id}/update-life-status', [ThanhVienController::class, 'updateLifeStatus']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // =========================================================
    // ADMIN: Tổng Quan Dashboard
    // =========================================================
    Route::get('/admin/dashboard', [DashboardController::class, 'getStatistics'])
        ->middleware('phan_quyen:Admin Dashboard');

    // =========================================================
    // ADMIN: Đối Tác & Mua Gói
    // =========================================================
    Route::prefix('/doi-tac')->group(function () {
        Route::get('/get-profile', [DoiTacController::class, 'getProfile']);
        Route::post('/update-profile', [DoiTacController::class, 'updateProfile']);
        Route::get('/statistics', [DoiTacController::class, 'getStatistics']);
        Route::post('/tao-chi-nhanh', [DoiTacController::class, 'taoChiNhanh']);
        Route::get('/lay-chi-nhanh', [DoiTacController::class, 'layChiNhanhCuaDoiTac']);
        Route::get('/check-pending', [DoiTacController::class, 'checkPending']);
        Route::get('/my-packages', [DoiTacController::class, 'getMyPackages']);
    });

    Route::prefix('/admin/doi-tac')->middleware('phan_quyen:Quản Lý Đối Tác')->group(function () {
        Route::get('/get-data', [DoiTacController::class, 'adminGetData']);
        Route::post('/create', [DoiTacController::class, 'adminCreate']);
        Route::post('/update', [DoiTacController::class, 'adminUpdate']);
        Route::post('/delete', [DoiTacController::class, 'adminDelete']);
        Route::post('/status', [DoiTacController::class, 'adminStatus']);
    });

    Route::prefix('/admin/yeu-cau-mua-goi')->middleware('phan_quyen:Quản Lý Đối Tác')->group(function () {
        Route::get('/get-data', [YeuCauMuaGoiController::class, 'getData']);
        Route::post('/approve', [YeuCauMuaGoiController::class, 'approve']);
        Route::post('/reject', [YeuCauMuaGoiController::class, 'reject']);
        Route::post('/delete', [YeuCauMuaGoiController::class, 'delete']);
    });

    Route::prefix('/admin/quan-ly-tai-khoan')->middleware('phan_quyen:Quản Lý Người Dùng')->group(function () {
        Route::get('/statistics', [QuanLyTaiKhoanController::class, 'getStatistics']);
        Route::get('/accounts', [QuanLyTaiKhoanController::class, 'getAccounts']);
        Route::get('/account-detail/{id}', [QuanLyTaiKhoanController::class, 'getAccountDetail']);
        Route::post('/update-account', [QuanLyTaiKhoanController::class, 'updateAccount']);
        Route::post('/lock-account', [QuanLyTaiKhoanController::class, 'lockAccount']);
        Route::post('/unlock-account', [QuanLyTaiKhoanController::class, 'unlockAccount']);
        Route::post('/upgrade-partner', [QuanLyTaiKhoanController::class, 'upgradePartner']);
        Route::post('/remove-partner', [QuanLyTaiKhoanController::class, 'removePartner']);
        Route::post('/reset-password', [QuanLyTaiKhoanController::class, 'resetPassword']);
        Route::post('/delete-account', [QuanLyTaiKhoanController::class, 'deleteAccount']);
        Route::post('/restore-account', [QuanLyTaiKhoanController::class, 'restoreAccount']);
        Route::post('/bulk-action', [QuanLyTaiKhoanController::class, 'bulkAction']);
    });

    // =========================================================
    // ADMIN: Hệ Thống — Chức Vụ, Chức Năng, Phân Quyền
    // (chỉ Master Admin mới dùng, không cần phan_quyen middleware)
    // =========================================================
    Route::prefix('/chuc-vu')->group(function () {
        Route::get('/get-data', [ChucVuController::class, 'getData']);
        Route::post('/create', [ChucVuController::class, 'create']);
        Route::post('/update', [ChucVuController::class, 'update']);
        Route::post('/delete', [ChucVuController::class, 'delete']);
        Route::post('/status', [ChucVuController::class, 'status']);
        Route::post('/search', [ChucVuController::class, 'search']);
    });

    Route::prefix('/chuc-nang')->group(function () {
        Route::get('/get-data', [ChucNangController::class, 'getData']);
        Route::get('/coming-soon-menus', [ChucNangController::class, 'getComingSoonMenus']);
        Route::post('/create', [ChucNangController::class, 'create']);
        Route::post('/update', [ChucNangController::class, 'update']);
        Route::post('/delete', [ChucNangController::class, 'delete']);
        Route::post('/status', [ChucNangController::class, 'status']);
    });

    Route::prefix('/phan-quyen')->group(function () {
        Route::post('/get-chuc-nang', [PhanQuyenController::class, 'getChucNang']);
        Route::post('/update', [PhanQuyenController::class, 'updatePhanQuyen']);
        Route::post('/get-member-chuc-nang', [PhanQuyenController::class, 'getMemberChucNang']);
        Route::post('/update-member', [PhanQuyenController::class, 'updateMemberPhanQuyen']);
        Route::post('/link-member', [PhanQuyenController::class, 'linkMemberAccount']);
        Route::post('/unlink-member', [PhanQuyenController::class, 'unlinkMemberAccount']);
    });

    // New roles & member role management
    Route::get('/roles/list', [RoleController::class, 'index']);
    Route::get('/permissions/list', [RoleController::class, 'permissions']);
    Route::post('/member/{id}/assign-role', [MemberRoleController::class, 'assign']);
    Route::post('/member/{id}/revoke-role', [MemberRoleController::class, 'revoke']);
    Route::get('/member/{id}/roles', [MemberRoleController::class, 'list']);

    Route::prefix('/goi-dich-vu')->group(function () {
        Route::get('/get-data', [GoiDichVuController::class, 'getData']);
        Route::post('/create', [GoiDichVuController::class, 'create']);
        Route::post('/update', [GoiDichVuController::class, 'update']);
        Route::post('/delete', [GoiDichVuController::class, 'delete']);
        Route::post('/status', [GoiDichVuController::class, 'status']);
    });

    // =========================================================
    // CÂY GIA PHẢ: Thành Viên, Vợ Chồng, Con Nuôi
    // =========================================================
    Route::prefix('/thanh-vien')->middleware('phan_quyen:Cây Gia Phả')->group(function () {
        Route::get('/get-data', [ThanhVienController::class, 'getData']);
        Route::get('/chi-nhanh/{chiNhanhId}', [ThanhVienController::class, 'getByChiNhanh']);
        Route::post('/create', [ThanhVienController::class, 'create']);
        Route::post('/update', [ThanhVienController::class, 'update']);
        Route::post('/delete', [ThanhVienController::class, 'delete']);
        Route::post('/status', [ThanhVienController::class, 'status']);
        Route::post('/search', [ThanhVienController::class, 'search']);
        Route::post('/xac-dinh-quan-he', [ThanhVienController::class, 'xacDinhQuanHe']);
        Route::post('/tra-cuu-quan-he', [ThanhVienController::class, 'traCuuQuanHe']);
    });

    Route::prefix('/vo-chong')->middleware('phan_quyen:Cây Gia Phả')->group(function () {
        Route::get('/get-data', [VoChongController::class, 'getData']);
        Route::post('/create', [VoChongController::class, 'create']);
        Route::post('/update', [VoChongController::class, 'update']);
        Route::post('/delete', [VoChongController::class, 'delete']);
        Route::post('/status', [VoChongController::class, 'status']);
    });

    Route::prefix('/con-nuoi')->middleware('phan_quyen:Cây Gia Phả')->group(function () {
        Route::get('/get-data', [ConNuoiController::class, 'getData']);
        Route::post('/create', [ConNuoiController::class, 'create']);
        Route::post('/update', [ConNuoiController::class, 'update']);
        Route::post('/delete', [ConNuoiController::class, 'delete']);
        Route::post('/status', [ConNuoiController::class, 'status']);
    });

    // =========================================================
    // DÒNG HỌ, CHI NHÁNH, ĐỜI TỘC HỌ
    // =========================================================
    Route::prefix('/nguoi-dung')->middleware('phan_quyen:Quản Lý Người Dùng')->group(function () {
        Route::get('/get-data', [NguoiDungController::class, 'getData']);
        Route::post('/create', [NguoiDungController::class, 'create']);
        Route::post('/update', [NguoiDungController::class, 'update']);
        Route::post('/delete', [NguoiDungController::class, 'delete']);
        Route::post('/status', [NguoiDungController::class, 'status']);
        Route::post('/search', [NguoiDungController::class, 'search']);
    });

    Route::prefix('/doi-toc-ho')->middleware('phan_quyen:Cây Gia Phả')->group(function () {
        Route::get('/get-data', [DoiTocHoController::class, 'getData']);
        Route::post('/create', [DoiTocHoController::class, 'create']);
        Route::post('/update', [DoiTocHoController::class, 'update']);
        Route::post('/delete', [DoiTocHoController::class, 'delete']);
        Route::post('/status', [DoiTocHoController::class, 'status']);
        Route::post('/search', [DoiTocHoController::class, 'search']);
    });

    Route::prefix('/chi-nhanh')->middleware('phan_quyen:Quản Lý Chi Nhánh')->group(function () {
        Route::get('/get-data', [ChiNhanhController::class, 'getData']);
        Route::post('/create', [ChiNhanhController::class, 'create']);
        Route::post('/update', [ChiNhanhController::class, 'update']);
        Route::post('/delete', [ChiNhanhController::class, 'delete']);
        Route::post('/status', [ChiNhanhController::class, 'status']);
        Route::post('/search', [ChiNhanhController::class, 'search']);
    });

    // =========================================================
    // NHÀ THỜ HỌ, MỘ PHẦN
    // =========================================================
    Route::prefix('/nha-tho-ho')->middleware('phan_quyen:Cây Gia Phả')->group(function () {
        Route::get('/get-data', [NhaThoHoController::class, 'getData']);
        Route::post('/create', [NhaThoHoController::class, 'create']);
        Route::post('/update', [NhaThoHoController::class, 'update']);
        Route::post('/delete', [NhaThoHoController::class, 'delete']);
        Route::post('/status', [NhaThoHoController::class, 'status']);
    });

    Route::prefix('/mo-phan')->middleware('phan_quyen:Quản Lý Mộ Phần')->group(function () {
        Route::get('/get-data', [MoPhanController::class, 'getData']);
        Route::get('/get-detail/{id}', [MoPhanController::class, 'getDetail']);
        Route::get('/nearby', [MoPhanController::class, 'getNearby']);
        Route::post('/create', [MoPhanController::class, 'create']);
        Route::post('/update', [MoPhanController::class, 'update']);
        Route::post('/delete', [MoPhanController::class, 'delete']);
        Route::post('/search', [MoPhanController::class, 'search']);
    });

    // =========================================================
    // SỰ KIỆN, ĐÓNG GÓP, TÀI LIỆU, THÔNG BÁO
    // =========================================================
    Route::prefix('/su-kien')->middleware('phan_quyen:Quản Lý Sự Kiện')->group(function () {
        Route::get('/get-data', [SuKienController::class, 'getData']);
        Route::post('/create', [SuKienController::class, 'create']);
        Route::post('/update', [SuKienController::class, 'update']);
        Route::post('/delete', [SuKienController::class, 'delete']);
        Route::post('/status', [SuKienController::class, 'status']);
        Route::get('/get-participants/{suKienId}', [SuKienController::class, 'getParticipants']);
        Route::post('/register', [SuKienController::class, 'register']);
        Route::post('/unregister', [SuKienController::class, 'unregister']);
    });

    Route::prefix('/dong-gop')->middleware('phan_quyen:Quản Lý Sự Kiện')->group(function () {
        Route::get('/get-data', [DongGopController::class, 'getData']);
        Route::post('/create', [DongGopController::class, 'create']);
        Route::post('/update', [DongGopController::class, 'update']);
        Route::post('/delete', [DongGopController::class, 'delete']);
        Route::post('/status', [DongGopController::class, 'status']);
    });

    Route::prefix('/tai-lieu')->middleware('phan_quyen:Quản Lý Tài Liệu')->group(function () {
        Route::get('/get-data', [TaiLieuController::class, 'getData']);
        Route::post('/create', [TaiLieuController::class, 'create']);
        Route::post('/update', [TaiLieuController::class, 'update']);
        Route::post('/delete', [TaiLieuController::class, 'delete']);
        Route::post('/status', [TaiLieuController::class, 'status']);
    });

    Route::prefix('/thong-bao')->middleware('phan_quyen:Quản Lý Thông Báo')->group(function () {
        Route::get('/get-data', [ThongBaoController::class, 'getData']);
        Route::post('/create', [ThongBaoController::class, 'create']);
        Route::post('/update', [ThongBaoController::class, 'update']);
        Route::post('/delete', [ThongBaoController::class, 'delete']);
        Route::post('/status', [ThongBaoController::class, 'status']);
    });

    // =========================================================
    // HINH ANH, ĐỀ XUẤT, THAM GIA, TƯỞNG NIỆM, NHẬT KÝ
    // =========================================================
    Route::prefix('/hinh-anh')->group(function () {
        Route::get('/get-data', [HinhAnhController::class, 'getData']);
        Route::post('/create', [HinhAnhController::class, 'create']);
        Route::post('/update', [HinhAnhController::class, 'update']);
        Route::post('/delete', [HinhAnhController::class, 'delete']);
        Route::post('/status', [HinhAnhController::class, 'status']);
    });

    Route::prefix('/de-xuat')->group(function () {
        Route::get('/get-data', [DeXuatController::class, 'getData']);
        Route::get('/my-proposals', [DeXuatController::class, 'myProposals']);
        Route::post('/create', [DeXuatController::class, 'create']);
        Route::post('/approve', [DeXuatController::class, 'approve']);
        Route::post('/reject', [DeXuatController::class, 'reject']);
        Route::post('/toggle-auto-approve', [DeXuatController::class, 'toggleAutoApprove']);
    });

    Route::prefix('/tham-gia-su-kien')->group(function () {
        Route::get('/get-data', [ThamGiaSuKienController::class, 'getData']);
        Route::post('/create', [ThamGiaSuKienController::class, 'create']);
        Route::post('/update', [ThamGiaSuKienController::class, 'update']);
        Route::post('/delete', [ThamGiaSuKienController::class, 'delete']);
        Route::post('/status', [ThamGiaSuKienController::class, 'status']);
    });

    Route::prefix('/tuong-niem')->group(function () {
        Route::get('/thanh-vien/{memberId}', [TuongNiemController::class, 'getTributes']);
        Route::post('/create', [TuongNiemController::class, 'createTribute']);
        Route::get('/sap-toi', [TuongNiemController::class, 'getUpcomingAnniversaries']);
    });

    Route::prefix('/nhat-ky-hoat-dong')->middleware('phan_quyen:Nhật Ký Thao Tác')->group(function () {
        Route::get('/get-data', [NhatKyHoatDongController::class, 'getData']);
        Route::get('/get-my-logs', [NhatKyHoatDongController::class, 'getMyLogs']);
        Route::post('/create', [NhatKyHoatDongController::class, 'create']);
        Route::post('/update', [NhatKyHoatDongController::class, 'update']);
        Route::post('/delete', [NhatKyHoatDongController::class, 'delete']);
        Route::post('/status', [NhatKyHoatDongController::class, 'status']);
    });

    // =========================================================
    // TRA CỨU XƯNG HÔ
    // =========================================================
    // (đã thuộc /thanh-vien bên trên, không cần route riêng)


});

Route::prefix('/thanh-toan')->group(function () {
    Route::get('/get-data', [ThanhToanController::class, 'index']);
    Route::post('/xac-nhan-thanh-toan', [ThanhToanController::class, 'paymentVerify']);
});

Route::get('/thanh-vien/public-detail/{id}', [ThanhVienController::class, 'getPublicDetail']);
