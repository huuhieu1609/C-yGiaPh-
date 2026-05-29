<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoiTac;
use App\Models\NguoiDung;
use App\Models\NhatKyHoatDong;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class YeuCauMuaGoiController extends Controller
{
    /**
     * Lấy danh sách toàn bộ yêu cầu mua gói (đối tác)
     */
    public function getData(): JsonResponse
    {
        try {
            // Tự động đồng bộ hóa các bản ghi có trạng thái cũ sang chuẩn mới
            DoiTac::where('trang_thai', '1')->update(['trang_thai' => 'APPROVED']);
            DoiTac::where('trang_thai', '0')->update(['trang_thai' => 'PENDING']);
            DoiTac::where('trang_thai', 'Hoạt động')->update(['trang_thai' => 'APPROVED']);
            DoiTac::where('trang_thai', 'Hết hạn')->update(['trang_thai' => 'EXPIRED']);

            $data = DoiTac::with(['nguoiDung'])
                ->orderByRaw("CASE WHEN trang_thai = 'PENDING' THEN 0 ELSE 1 END")
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách yêu cầu mua gói thành công!',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Phê duyệt yêu cầu mua gói
     */
    public function approve(Request $request): JsonResponse
    {
        $admin = auth('sanctum')->user();
        if (!$admin || $admin->vai_tro !== 'Admin') {
            return response()->json([
                'status' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này!',
            ], 403);
        }

        $id = $request->input('id');
        if (!$id) {
            return response()->json(['status' => false, 'message' => 'Thiếu ID yêu cầu phê duyệt.'], 400);
        }

        try {
            return DB::transaction(function () use ($id, $admin) {
                $doiTac = DoiTac::findOrFail($id);

                // Chống duyệt trùng
                if ($doiTac->trang_thai === 'APPROVED') {
                    return response()->json([
                        'status' => false,
                        'message' => 'Yêu cầu này đã được phê duyệt từ trước!',
                    ], 400);
                }

                // Cập nhật trạng thái
                $doiTac->update([
                    'trang_thai' => 'APPROVED',
                    'ngay_bat_dau' => now()->toDateString(),
                    'ngay_ket_thuc' => now()->addYear()->toDateString(), // Mặc định thời hạn 1 năm
                    'ly_do_tu_choi' => null,
                ]);

                // Ghi nhật ký hoạt động của Admin
                NhatKyHoatDong::ghiLog(
                    'Phê duyệt yêu cầu mua gói của người dùng ID ' . $doiTac->id_nguoi_dung . ' (Số tiền: ' . number_format($doiTac->so_tien) . ' VNĐ)',
                    [
                        'id_doi_tac' => $doiTac->id,
                        'id_nguoi_dung' => $doiTac->id_nguoi_dung,
                        'ten_goi' => $doiTac->ten_goi,
                        'so_tien' => $doiTac->so_tien,
                        'ngay_bat_dau' => $doiTac->ngay_bat_dau,
                        'ngay_ket_thuc' => $doiTac->ngay_ket_thuc,
                        'approved_by' => $admin->ho_ten,
                    ],
                    'Thành công',
                    $admin->id
                );

                return response()->json([
                    'status' => true,
                    'message' => 'Phê duyệt yêu cầu mua gói thành công!',
                    'data' => $doiTac->load('nguoiDung'),
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Lỗi khi phê duyệt mua gói: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Lỗi máy chủ khi phê duyệt: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Từ chối yêu cầu mua gói
     */
    public function reject(Request $request): JsonResponse
    {
        $admin = auth('sanctum')->user();
        if (!$admin || $admin->vai_tro !== 'Admin') {
            return response()->json([
                'status' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này!',
            ], 403);
        }

        $id = $request->input('id');
        $lyDo = $request->input('ly_do_tu_choi');

        if (!$id) {
            return response()->json(['status' => false, 'message' => 'Thiếu ID yêu cầu.'], 400);
        }

        if (empty($lyDo)) {
            return response()->json(['status' => false, 'message' => 'Vui lòng cung cấp lý do từ chối.'], 400);
        }

        try {
            return DB::transaction(function () use ($id, $lyDo, $admin) {
                $doiTac = DoiTac::findOrFail($id);

                if ($doiTac->trang_thai === 'APPROVED') {
                    return response()->json([
                        'status' => false,
                        'message' => 'Không thể từ chối một yêu cầu đã được phê duyệt thành công!',
                    ], 400);
                }

                $doiTac->update([
                    'trang_thai' => 'REJECTED',
                    'ly_do_tu_choi' => $lyDo,
                    'ngay_bat_dau' => null,
                    'ngay_ket_thuc' => null,
                ]);

                // Ghi nhật ký hoạt động
                NhatKyHoatDong::ghiLog(
                    'Từ chối yêu cầu mua gói của người dùng ID ' . $doiTac->id_nguoi_dung . '. Lý do: ' . $lyDo,
                    [
                        'id_doi_tac' => $doiTac->id,
                        'id_nguoi_dung' => $doiTac->id_nguoi_dung,
                        'ten_goi' => $doiTac->ten_goi,
                        'so_tien' => $doiTac->so_tien,
                        'ly_do_tu_choi' => $lyDo,
                        'rejected_by' => $admin->ho_ten,
                    ],
                    'Thành công',
                    $admin->id
                );

                return response()->json([
                    'status' => true,
                    'message' => 'Đã từ chối yêu cầu mua gói thành công!',
                    'data' => $doiTac->load('nguoiDung'),
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Lỗi khi từ chối mua gói: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Lỗi máy chủ khi từ chối: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Xóa yêu cầu mua gói (Soft Delete)
     */
    public function delete(Request $request): JsonResponse
    {
        $admin = auth('sanctum')->user();
        if (!$admin || $admin->vai_tro !== 'Admin') {
            return response()->json([
                'status' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này!',
            ], 403);
        }

        $id = $request->input('id');
        if (!$id) {
            return response()->json(['status' => false, 'message' => 'Thiếu ID yêu cầu cần xóa.'], 400);
        }

        try {
            return DB::transaction(function () use ($id, $admin) {
                $doiTac = DoiTac::findOrFail($id);
                $userId = $doiTac->id_nguoi_dung;
                $doiTac->delete(); // Soft delete do Model có SoftDeletes

                // Ghi nhật ký hoạt động
                NhatKyHoatDong::ghiLog(
                    'Xóa (Soft Delete) yêu cầu mua gói đối tác của người dùng ID ' . $userId,
                    [
                        'id_doi_tac' => $doiTac->id,
                        'id_nguoi_dung' => $userId,
                        'ten_goi' => $doiTac->ten_goi,
                        'so_tien' => $doiTac->so_tien,
                        'deleted_by' => $admin->ho_ten,
                    ],
                    'Thành công',
                    $admin->id
                );

                return response()->json([
                    'status' => true,
                    'message' => 'Xóa yêu cầu mua gói thành công!',
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa mua gói: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Lỗi máy chủ khi xóa: ' . $e->getMessage(),
            ], 500);
        }
    }
}
