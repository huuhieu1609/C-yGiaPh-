<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChiNhanhController;
use App\Http\Controllers\Api\ChucNangController;
use App\Http\Controllers\Api\ChucVuController;
use App\Http\Controllers\Api\ConNuoiController;
use App\Http\Controllers\Api\DoiTacController;
use App\Http\Controllers\Api\DoiTocHoController;
use App\Http\Controllers\Api\DongGopController;
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
use App\Http\Controllers\Api\VoChongController;
use App\Http\Controllers\ThanhToanControlles;
use Illuminate\Support\Facades\Route;

// ... (vẫn giữ các use khác)

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    
    Route::prefix('/doi-tac')->group(function () {
        Route::get('/get-profile', [DoiTacController::class, 'getProfile']);
        Route::post('/update-profile', [DoiTacController::class, 'updateProfile']);
        Route::get('/statistics', [DoiTacController::class, 'getStatistics']);
    });
});

// Resources Routes
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
    Route::post('/create', [ChucNangController::class, 'create']);
    Route::post('/update', [ChucNangController::class, 'update']);
    Route::post('/delete', [ChucNangController::class, 'delete']);
    Route::post('/status', [ChucNangController::class, 'status']);
});

// Thêm các prefix route cho 15 models khác tương tự...
Route::prefix('/thanh-vien')->group(function () {
    Route::get('/get-data', [ThanhVienController::class, 'getData']);
    Route::post('/create', [ThanhVienController::class, 'create']);
    Route::post('/update', [ThanhVienController::class, 'update']);
    Route::post('/delete', [ThanhVienController::class, 'delete']);
    Route::post('/status', [ThanhVienController::class, 'status']);
});

Route::prefix('/nguoi-dung')->group(function () {
    Route::get('/get-data', [NguoiDungController::class, 'getData']);
    Route::post('/create', [NguoiDungController::class, 'create']);
    Route::post('/update', [NguoiDungController::class, 'update']);
    Route::post('/delete', [NguoiDungController::class, 'delete']);
    Route::post('/status', [NguoiDungController::class, 'status']);
});

Route::prefix('/doi-toc-ho')->group(function () {
    Route::get('/get-data', [DoiTocHoController::class, 'getData']);
    Route::post('/create', [DoiTocHoController::class, 'create']);
    Route::post('/update', [DoiTocHoController::class, 'update']);
    Route::post('/delete', [DoiTocHoController::class, 'delete']);
    Route::post('/status', [DoiTocHoController::class, 'status']);
});

Route::prefix('/chi-nhanh')->group(function () {
    Route::get('/get-data', [ChiNhanhController::class, 'getData']);
    Route::post('/create', [ChiNhanhController::class, 'create']);
    Route::post('/update', [ChiNhanhController::class, 'update']);
    Route::post('/delete', [ChiNhanhController::class, 'delete']);
    Route::post('/status', [ChiNhanhController::class, 'status']);
});

Route::prefix('/vo-chong')->group(function () {
    Route::get('/get-data', [VoChongController::class, 'getData']);
    Route::post('/create', [VoChongController::class, 'create']);
    Route::post('/update', [VoChongController::class, 'update']);
    Route::post('/delete', [VoChongController::class, 'delete']);
    Route::post('/status', [VoChongController::class, 'status']);
});

Route::prefix('/con-nuoi')->group(function () {
    Route::get('/get-data', [ConNuoiController::class, 'getData']);
    Route::post('/create', [ConNuoiController::class, 'create']);
    Route::post('/update', [ConNuoiController::class, 'update']);
    Route::post('/delete', [ConNuoiController::class, 'delete']);
    Route::post('/status', [ConNuoiController::class, 'status']);
});

Route::prefix('/dong-gop')->group(function () {
    Route::get('/get-data', [DongGopController::class, 'getData']);
    Route::post('/create', [DongGopController::class, 'create']);
    Route::post('/update', [DongGopController::class, 'update']);
    Route::post('/delete', [DongGopController::class, 'delete']);
    Route::post('/status', [DongGopController::class, 'status']);
});

Route::prefix('/hinh-anh')->group(function () {
    Route::get('/get-data', [HinhAnhController::class, 'getData']);
    Route::post('/create', [HinhAnhController::class, 'create']);
    Route::post('/update', [HinhAnhController::class, 'update']);
    Route::post('/delete', [HinhAnhController::class, 'delete']);
    Route::post('/status', [HinhAnhController::class, 'status']);
});

Route::prefix('/mo-phan')->group(function () {
    Route::get('/get-data', [MoPhanController::class, 'getData']);
    Route::post('/create', [MoPhanController::class, 'create']);
    Route::post('/update', [MoPhanController::class, 'update']);
    Route::post('/delete', [MoPhanController::class, 'delete']);
    Route::post('/status', [MoPhanController::class, 'status']);
});

Route::prefix('/nha-tho-ho')->group(function () {
    Route::get('/get-data', [NhaThoHoController::class, 'getData']);
    Route::post('/create', [NhaThoHoController::class, 'create']);
    Route::post('/update', [NhaThoHoController::class, 'update']);
    Route::post('/delete', [NhaThoHoController::class, 'delete']);
    Route::post('/status', [NhaThoHoController::class, 'status']);
});

Route::prefix('/su-kien')->group(function () {
    Route::get('/get-data', [SuKienController::class, 'getData']);
    Route::post('/create', [SuKienController::class, 'create']);
    Route::post('/update', [SuKienController::class, 'update']);
    Route::post('/delete', [SuKienController::class, 'delete']);
    Route::post('/status', [SuKienController::class, 'status']);
});

Route::prefix('/tai-lieu')->group(function () {
    Route::get('/get-data', [TaiLieuController::class, 'getData']);
    Route::post('/create', [TaiLieuController::class, 'create']);
    Route::post('/update', [TaiLieuController::class, 'update']);
    Route::post('/delete', [TaiLieuController::class, 'delete']);
    Route::post('/status', [TaiLieuController::class, 'status']);
});

Route::prefix('/tham-gia-su-kien')->group(function () {
    Route::get('/get-data', [ThamGiaSuKienController::class, 'getData']);
    Route::post('/create', [ThamGiaSuKienController::class, 'create']);
    Route::post('/update', [ThamGiaSuKienController::class, 'update']);
    Route::post('/delete', [ThamGiaSuKienController::class, 'delete']);
    Route::post('/status', [ThamGiaSuKienController::class, 'status']);
});

Route::prefix('/nhat-ky-hoat-dong')->group(function () {
    Route::get('/get-data', [NhatKyHoatDongController::class, 'getData']);
    Route::post('/create', [NhatKyHoatDongController::class, 'create']);
    Route::post('/update', [NhatKyHoatDongController::class, 'update']);
    Route::post('/delete', [NhatKyHoatDongController::class, 'delete']);
    Route::post('/status', [NhatKyHoatDongController::class, 'status']);
});

Route::prefix('/thong-bao')->group(function () {
    Route::get('/get-data', [ThongBaoController::class, 'getData']);
    Route::post('/create', [ThongBaoController::class, 'create']);
    Route::post('/update', [ThongBaoController::class, 'update']);
    Route::post('/delete', [ThongBaoController::class, 'delete']);
    Route::post('/status', [ThongBaoController::class, 'status']);
});

Route::prefix('/thanh-toan')->group(function () {
    Route::get('/get-data', [ThanhToanControlles::class, 'index']);
    Route::post('/xac-nhan-thanh-toan', [ThanhToanControlles::class, 'paymentVerify']);
});

Route::prefix('/phan-quyen')->group(function () {
    Route::post('/get-chuc-nang', [PhanQuyenController::class, 'getChucNang']);
    Route::post('/update', [PhanQuyenController::class, 'updatePhanQuyen']);
});
