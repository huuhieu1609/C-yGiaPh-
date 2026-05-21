<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiNhanh;
use App\Models\DoiTac;
use App\Models\SuKien;
use App\Models\ThanhVien;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoiTacController extends Controller
{
    public function getProfile(Request $request): JsonResponse
    {
        $profile = $this->partnerProfile($request);

        return response()->json([
            'status' => true,
            'data' => $profile,
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ten_goi' => 'required|string|max:255',
        ]);

        $profile = $this->partnerProfile($request);
        $profile->update([
            'ten_goi' => $validated['ten_goi'],
        ]);

        ChiNhanh::updateOrCreate(
            ['id_nguoi_dung' => $request->user()->id],
            [
                'ten_chi' => $validated['ten_goi'],
                'mo_ta' => 'Cây gia phả của '.$validated['ten_goi'],
            ],
        );

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thông tin đối tác thành công!',
            'data' => $profile->fresh(),
        ]);
    }

    public function getStatistics(Request $request): JsonResponse
    {
        $chiNhanhIds = ChiNhanh::where('id_nguoi_dung', $request->user()->id)->pluck('id');
        $memberQuery = ThanhVien::whereIn('chi_nhanh_id', $chiNhanhIds);

        return response()->json([
            'status' => true,
            'data' => [
                'total_members' => (clone $memberQuery)->count(),
                'max_generation' => (int) (clone $memberQuery)->max('doi_thu'),
                'upcoming_events' => SuKien::whereDate('ngay_to_chuc', '>=', now()->toDateString())->count(),
                'recent_members' => (clone $memberQuery)->latest('updated_at')->limit(5)->get(),
            ],
        ]);
    }

    public function taoChiNhanh(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ten_chi' => 'required|string|max:255',
            'mo_ta' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $chiNhanhDaCo = ChiNhanh::where('id_nguoi_dung', $user->id)->first();

        if ($chiNhanhDaCo) {
            return response()->json([
                'success' => false,
                'message' => 'Đối tác chỉ được phép tạo duy nhất 1 chi nhánh dòng họ.',
                'chi_nhanh' => $chiNhanhDaCo,
            ], 403);
        }

        $chiNhanh = ChiNhanh::create([
            'ten_chi' => $validated['ten_chi'],
            'mo_ta' => $validated['mo_ta'] ?? null,
            'id_nguoi_dung' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thêm chi nhánh thành công.',
            'chi_nhanh' => $chiNhanh,
            'redirect_url' => '/cay-gia-pha',
        ], 201);
    }

    public function layChiNhanhCuaDoiTac(Request $request): JsonResponse
    {
        $chiNhanhs = ChiNhanh::where('id_nguoi_dung', $request->user()->id)->get();

        return response()->json(['data' => $chiNhanhs]);
    }

    public function traCuuThanhVien(Request $request, int|string $chiNhanhId): JsonResponse
    {
        $keyword = $request->input('tu_khoa');
        $ownsBranch = ChiNhanh::where('id_nguoi_dung', $request->user()->id)
            ->where('id', $chiNhanhId)
            ->exists();

        if (! $ownsBranch) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn không có quyền tra cứu chi nhánh này.',
            ], 403);
        }

        $thanhViens = ThanhVien::search($chiNhanhId, $keyword)->get();

        return response()->json(['data' => $thanhViens]);
    }

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
}
