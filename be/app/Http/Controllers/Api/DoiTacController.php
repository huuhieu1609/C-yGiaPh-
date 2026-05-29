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
        $chiNhanhIds = ChiNhanh::getManagedBranchIds($request->user());
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
        $chiNhanhs = ChiNhanh::whereIn('id', ChiNhanh::getManagedBranchIds($request->user()))->get();

        return response()->json(['data' => $chiNhanhs]);
    }

    public function checkPending(Request $request): JsonResponse
    {
        $user = $request->user();
        $pending = DoiTac::where('id_nguoi_dung', $user->id)
            ->where('trang_thai', 'PENDING')
            ->first();

        return response()->json([
            'status' => true,
            'has_pending' => $pending ? true : false,
            'data' => $pending
        ]);
    }

    public function traCuuThanhVien(Request $request, int|string $chiNhanhId): JsonResponse
    {
        $keyword = $request->input('tu_khoa');
        $ownsBranch = in_array((int)$chiNhanhId, ChiNhanh::getManagedBranchIds($request->user()));

        if (! $ownsBranch) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn không có quyền tra cứu chi nhánh này.',
            ], 403);
        }

        $thanhViens = ThanhVien::search($chiNhanhId, $keyword)->get();

        return response()->json(['data' => $thanhViens]);
    }

    private function partnerProfile(Request $request): \App\Models\DoiTac
    {
        return \App\Models\DoiTac::firstOrCreate(
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

    public function adminGetData(): \Illuminate\Http\JsonResponse
    {
        try {
            $data = \App\Models\DoiTac::with(['nguoiDung.chiNhanh'])->get();
            
            // Map each partner to resolve which branch/lineage they manage
            $data->each(function ($item) {
                $ownedBranch = ChiNhanh::where('id_nguoi_dung', $item->id_nguoi_dung)->first();
                if ($ownedBranch) {
                    $item->dong_ho = $ownedBranch->ten_chi;
                } elseif ($item->nguoiDung && $item->nguoiDung->chiNhanh) {
                    $item->dong_ho = $item->nguoiDung->chiNhanh->ten_chi;
                } else {
                    $item->dong_ho = 'Chưa liên kết dòng họ';
                }
            });

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

    public function adminCreate(Request $request): \Illuminate\Http\JsonResponse
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
            $exists = \App\Models\DoiTac::where('id_nguoi_dung', $validated['id_nguoi_dung'])->exists();
            if ($exists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Người dùng này đã là đối tác trong hệ thống!',
                ]);
            }

            $doiTac = \App\Models\DoiTac::create($validated);

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

    public function adminUpdate(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $doiTac = \App\Models\DoiTac::findOrFail($request->id);
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

    public function adminDelete(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $doiTac = \App\Models\DoiTac::findOrFail($request->id);
            $userId = $doiTac->id_nguoi_dung;
            $doiTac->delete();

            // Reset is_doi_tac = 0 cho người dùng nếu không còn bản ghi đối tác nào khác
            $stillPartner = \App\Models\DoiTac::where('id_nguoi_dung', $userId)->exists();
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

    public function adminStatus(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $doiTac = \App\Models\DoiTac::findOrFail($request->id);
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
