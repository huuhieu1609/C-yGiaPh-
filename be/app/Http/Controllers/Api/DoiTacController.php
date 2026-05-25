<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChiNhanh;
use App\Models\ThanhVien;
use Illuminate\Support\Facades\Auth;

class DoiTacController extends Controller
{
    public function taoChiNhanh(Request $request)
    {
        $user = auth('sanctum')->user(); // Dùng sanctum theo cấu hình hiện tại của bạn

        $chiNhanhDaCo = ChiNhanh::where('id_nguoi_dung', $user->id)->first();
        
        if ($chiNhanhDaCo) {
            return response()->json([
                'success' => false,
                'message' => 'Đối tác chỉ được phép tạo duy nhất 1 chi nhánh dòng họ.',
                'chi_nhanh' => $chiNhanhDaCo
            ], 403);
        }

        $chiNhanh = ChiNhanh::create([
            'ten_chi' => $request->input('ten_chi'),
            'mo_ta' => $request->input('mo_ta'),
            'id_nguoi_dung' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thêm chi nhánh thành công.',
            'chi_nhanh' => $chiNhanh,
            'redirect_url' => '/cay-gia-pha' // Gửi kèm URL để Frontend tự động redirect
        ], 201);
    }

    // API 2: Lấy danh sách chi nhánh (Để đổ vào thẻ <select> ở trang cây gia phả)
    public function layChiNhanhCuaDoiTac()
    {
        $user = auth('sanctum')->user();
        // Đối tác chỉ có 1, nhưng ta cứ trả về dạng mảng để FE dễ đổ vào Select
        $chiNhanhs = ChiNhanh::where('id_nguoi_dung', $user->id)->get();
        
        return response()->json(['data' => $chiNhanhs]);
    }

    // API 3: Tra cứu thành viên thuộc chi nhánh (Dùng chung cho cả Cây Gia Phả & Danh Sách)
    public function traCuuThanhVien(Request $request, $chiNhanhId)
    {
        $keyword = $request->input('tu_khoa');
        
        $thanhViens = ThanhVien::search($chiNhanhId, $keyword)->get();

        return response()->json(['data' => $thanhViens]);
    }
<<<<<<< Updated upstream
}
=======

    private function partnerProfile(Request $request): DoiTac
    {
        return DoiTac::firstOrCreate(
            ['id_nguoi_dung' => $request->user()->id],
            [
                'ten_goi' => 'Gói Đối Tác',
                'so_tien' => 0,
                'ngay_bat_dau' => now(),
                'ngay_ket_thuc' => now()->addYear(),
                'trang_thai' => 1,
            ],
        );
    }

    public function adminGetData(): JsonResponse
    {
        try {
            $data = DoiTac::with('nguoiDung')->get();
            return response()->json([
                'status' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function adminCreate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id_nguoi_dung' => 'required|exists:nguoi_dungs,id',
                'ten_goi' => 'required|string|max:255',
                'so_tien' => 'required|numeric|min:0',
                'ngay_bat_dau' => 'required|date',
                'ngay_ket_thuc' => 'required|date|after_or_equal:ngay_bat_dau',
                'trang_thai' => 'required|in:0,1',
            ]);

            // Kiểm tra xem người dùng đã là đối tác chưa
            $exists = DoiTac::where('id_nguoi_dung', $validated['id_nguoi_dung'])->exists();
            if ($exists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Người dùng này đã là đối tác trong hệ thống!',
                ]);
            }

            $doiTac = DoiTac::create($validated);

            // Đồng bộ trạng thái is_doi_tac = 1 của NguoiDung
            $user = \App\Models\NguoiDung::find($validated['id_nguoi_dung']);
            if ($user) {
                $user->update(['is_doi_tac' => 1]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Thêm đối tác thành công!',
                'data' => $doiTac,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi thêm đối tác: ' . $e->getMessage()
            ], 500);
        }
    }

    public function adminUpdate(Request $request): JsonResponse
    {
        try {
            $doiTac = DoiTac::findOrFail($request->id);
            $validated = $request->validate([
                'ten_goi' => 'required|string|max:255',
                'so_tien' => 'required|numeric|min:0',
                'ngay_bat_dau' => 'required|date',
                'ngay_ket_thuc' => 'required|date|after_or_equal:ngay_bat_dau',
                'trang_thai' => 'required|in:0,1',
            ]);

            $doiTac->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật thông tin đối tác thành công!',
                'data' => $doiTac,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi cập nhật đối tác: ' . $e->getMessage()
            ], 500);
        }
    }

    public function adminDelete(Request $request): JsonResponse
    {
        try {
            $doiTac = DoiTac::findOrFail($request->id);
            $userId = $doiTac->id_nguoi_dung;
            $doiTac->delete();

            // Reset is_doi_tac = 0 cho người dùng nếu không còn bản ghi đối tác nào khác
            $stillPartner = DoiTac::where('id_nguoi_dung', $userId)->exists();
            if (!$stillPartner) {
                $user = \App\Models\NguoiDung::find($userId);
                if ($user) {
                    $user->update(['is_doi_tac' => 0]);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Xóa đối tác thành công!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi xóa đối tác: ' . $e->getMessage()
            ], 500);
        }
    }

    public function adminStatus(Request $request): JsonResponse
    {
        try {
            $doiTac = DoiTac::findOrFail($request->id);
            $doiTac->trang_thai = $doiTac->trang_thai == 1 ? 0 : 1;
            $doiTac->save();

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật trạng thái đối tác thành công!',
                'trang_thai' => $doiTac->trang_thai,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage()
            ], 500);
        }
    }
}
>>>>>>> Stashed changes
