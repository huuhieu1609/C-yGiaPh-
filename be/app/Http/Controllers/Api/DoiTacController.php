<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiNhanh;
use App\Models\DoiTac;
use App\Models\GoiDichVu;
use App\Models\SuKien;
use App\Models\ThanhVien;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoiTacController extends Controller
{
    public function getProfile(Request $request): JsonResponse
    {
        $userId  = $request->user()->id;
        $profile = $this->partnerProfile($request);

        // Tổng hợp features & limits từ TẤT CẢ gói còn hiệu lực
        $effectiveFeatures = DoiTac::getEffectiveFeatures($userId);
        $effectiveLimits   = DoiTac::getEffectiveLimits($userId);
        $activeCount       = DoiTac::activePackagesOf($userId)->count();
        $latestExpiry      = DoiTac::getLatestExpiry($userId);
        $earliestExpiry    = DoiTac::getEarliestExpiry($userId);

        return response()->json([
            'status' => true,
            'data'   => array_merge($profile->toArray(), [
                'effective_features'   => $effectiveFeatures,
                'effective_max_doi'    => $effectiveLimits['max_doi'],
                'effective_max_thanh_vien' => $effectiveLimits['max_thanh_vien'],
                'effective_max_chi_nhanh' => $effectiveLimits['max_chi_nhanh'] ?? 1,
                'active_packages_count'=> $activeCount,
                'latest_expiry'        => $latestExpiry,
                'earliest_expiry'      => $earliestExpiry,
            ]),
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

        $recentMembers = (clone $memberQuery)->latest('updated_at')->limit(5)->get();

        // Attach a brief changes summary for each recent member based on activity logs
        $recentMembersData = $recentMembers->map(function ($m) {
            // Try to find activity logs that mention the member's name
            $logs = \App\Models\NhatKyHoatDong::where('hanh_dong', 'like', '%' . addslashes($m->ho_ten) . '%')
                ->orderBy('thoi_gian', 'desc')
                ->limit(3)
                ->pluck('hanh_dong')
                ->toArray();

            $changes = null;
            $update_summary = null;
            if (!empty($logs)) {
                // Provide the latest log as a summary and list others as 'changes'
                $update_summary = $logs[0];
                $changes = [];
                foreach ($logs as $i => $log) {
                    $changes['activity_' . ($i + 1)] = $log;
                }
            }

            return [
                'id' => $m->id,
                'ho_ten' => $m->ho_ten,
                'loai_quan_he' => $m->loai_quan_he,
                'updated_at' => $m->updated_at,
                'avatar' => $m->avatar ?? null,
                'changes' => $changes,
                'update_summary' => $update_summary,
            ];
        });

        // Count recent system notifications (example: created in last 7 days)
        $systemAlertsCount = \App\Models\ThongBao::where('created_at', '>=', now()->subDays(7))->count();

        // Prepare monthly new members for last 6 months (oldest -> newest)
        $labels = [];
        $counts = [];
        for ($i = 5; $i >= 0; $i--) {
            $start = now()->subMonths($i)->startOfMonth();
            $end = now()->subMonths($i)->endOfMonth();
            $labels[] = 'Tháng ' . $start->format('n');
            $counts[] = (clone $memberQuery)->whereBetween('created_at', [$start->toDateString() . ' 00:00:00', $end->toDateString() . ' 23:59:59'])->count();
        }

        return response()->json([
            'status' => true,
            'data' => [
                'total_members' => (clone $memberQuery)->count(),
                'max_generation' => (int) (clone $memberQuery)->max('doi_thu'),
                'upcoming_events' => SuKien::whereDate('ngay_to_chuc', '>=', now()->toDateString())->count(),
                'system_alerts_count' => $systemAlertsCount,
                'recent_members' => $recentMembersData,
                'monthly_new_members' => [
                    'labels' => $labels,
                    'data' => $counts,
                ],
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
        $limits = DoiTac::getEffectiveLimits($user->id);
        $maxChiNhanh = $limits['max_chi_nhanh'] ?? 1;

        $chiNhanhCount = ChiNhanh::where('id_nguoi_dung', $user->id)->count();

        if ($chiNhanhCount >= $maxChiNhanh) {
            return response()->json([
                'success' => false,
                'message' => 'Đối tác đã đạt giới hạn tối đa ' . $maxChiNhanh . ' chi nhánh dòng họ của gói dịch vụ.',
            ], 403);
        }

        $chiNhanh = ChiNhanh::create([
            'ten_chi' => $validated['ten_chi'],
            'mo_ta' => $validated['mo_ta'] ?? null,
            'id_nguoi_dung' => $user->id,
        ]);

        // Tự động tạo 10 thế hệ (Đời) mặc định cho chi nhánh mới
        $generationNames = [
            1 => 'Thủy Tổ Khai Sáng',
            2 => 'Viễn Tổ Trung Hưng',
            3 => 'Tằng Tổ Phát Triển',
            4 => 'Cao Tổ Kiến Thiết',
            5 => 'Tổ Khảo Kế Thừa',
            6 => 'Thế Hệ Tiếp Bước',
            7 => 'Thế Hệ Đổi Mới',
            8 => 'Thế Hệ Hội Nhập',
            9 => 'Thế Hệ Tinh Anh',
            10 => 'Thế Hệ Tương Lai',
        ];
        $generationDescriptions = [
            1 => 'Thế hệ đầu tiên khai sơn lập địa, đặt nền móng cho dòng tộc.',
            2 => 'Thế hệ thứ hai tiếp nối chí hướng mở mang gia nghiệp.',
            3 => 'Thế hệ thứ ba củng cố gia đạo và phát triển kinh tế.',
            4 => 'Thế hệ thứ tư chấn hưng văn hóa, khuyến khích học hành.',
            5 => 'Thế hệ thứ năm kế thừa di sản văn hóa, giữ gìn giềng mối gia đình.',
            6 => 'Thế hệ tiếp bước gìn giữ nề nếp gia phong, gia tăng uy tín dòng tộc.',
            7 => 'Thế hệ thời kỳ đổi mới, phát triển kinh tế xã hội hiện đại.',
            8 => 'Thế hệ hội nhập quốc tế, học hỏi tinh hoa nhân loại.',
            9 => 'Thế hệ của những tinh hoa ưu tú, mang vinh quang về cho dòng họ.',
            10 => 'Thế hệ trẻ tương lai, niềm hy vọng mới của gia tộc.',
        ];

        $branchName = str_replace('Chi Nhánh ', '', $chiNhanh->ten_chi);
        for ($i = 1; $i <= 10; $i++) {
            \App\Models\DoiTocHo::create([
                'chi_nhanh_id' => $chiNhanh->id,
                'so_doi' => $i,
                'ten_doi' => 'Đời thứ ' . $i . ' (' . $generationNames[$i] . ' - ' . $branchName . ')',
                'mo_ta' => $generationDescriptions[$i] . ' Thuộc ' . $branchName . '.',
                'trang_thai' => 1,
            ]);
        }

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
        $user    = $request->user();
        $pending = DoiTac::where('id_nguoi_dung', $user->id)
            ->where('trang_thai', 'PENDING')
            ->first();

        return response()->json([
            'status'      => true,
            'has_pending' => (bool) $pending,
            'data'        => $pending,
        ]);
    }

    /**
     * Trả về danh sách TẤT CẢ gói đã mua của user (kể cả hết hạn),
     * kèm thông tin countdown và features.
     */
    public function getMyPackages(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $today = now()->startOfDay();
        $todayStr = $today->toDateString();

        // Tất cả gói đã mua, sắp xếp: còn hạn trước, mới mua sau
        $packages = DoiTac::where('id_nguoi_dung', $userId)
            ->where('trang_thai', 'APPROVED')
            ->orderByRaw("CASE WHEN ngay_ket_thuc IS NULL OR ngay_ket_thuc >= ? THEN 0 ELSE 1 END", [$todayStr])
            ->orderBy('ngay_ket_thuc', 'desc')
            ->get();

        $packageList = $packages->map(function ($pkg) use ($today) {
            $daysRemaining = $pkg->getDaysRemaining();
            $progressPct   = $pkg->getProgressPercent();
            $isActive      = $pkg->ngay_ket_thuc === null
                || Carbon::parse($pkg->ngay_ket_thuc)->gte($today);
            $isExpiringSoon = $isActive && $daysRemaining !== null && $daysRemaining <= 30;

            return [
                'id'              => $pkg->id,
                'ten_goi'         => $pkg->ten_goi,
                'so_tien'         => $pkg->so_tien,
                'features'        => DoiTac::parseFeatures($pkg->features),
                'max_doi'         => $pkg->max_doi,
                'max_thanh_vien'  => $pkg->max_thanh_vien,
                'max_chi_nhanh'   => $pkg->max_chi_nhanh ?? 1,
                'ngay_bat_dau'    => $pkg->ngay_bat_dau ? $pkg->ngay_bat_dau->toDateString() : null,
                'ngay_ket_thuc'   => $pkg->ngay_ket_thuc ? $pkg->ngay_ket_thuc->toDateString() : null,
                'days_remaining'  => $daysRemaining,
                'progress_pct'    => $progressPct,
                'is_active'       => $isActive,
                'is_expiring_soon'=> $isExpiringSoon,
                'trang_thai'      => $pkg->trang_thai,
                'created_at'      => $pkg->created_at,
            ];
        });

        // Effective (union) data từ các gói còn hiệu lực
        $effectiveFeatures = DoiTac::getEffectiveFeatures($userId);
        $effectiveLimits   = DoiTac::getEffectiveLimits($userId);

        return response()->json([
            'status'  => true,
            'data'    => [
                'packages'              => $packageList,
                'effective_features'    => $effectiveFeatures,
                'effective_max_doi'     => $effectiveLimits['max_doi'],
                'effective_max_thanh_vien' => $effectiveLimits['max_thanh_vien'],
                'effective_max_chi_nhanh' => $effectiveLimits['max_chi_nhanh'] ?? 1,
                'active_count'          => $packageList->where('is_active', true)->count(),
                'latest_expiry'         => DoiTac::getLatestExpiry($userId),
                'earliest_expiry'       => DoiTac::getEarliestExpiry($userId),
            ],
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
        $userId = $request->user()->id;

        // Lấy gói APPROVED mới nhất còn hiệu lực của user
        $latestActive = \App\Models\DoiTac::activePackagesOf($userId)
            ->orderBy('ngay_ket_thuc', 'desc')
            ->first();

        if ($latestActive) {
            return $latestActive;
        }

        // Không tìm thấy gói nào — trả về một đối tượng rỗng an toàn (không tự tạo)
        // Dùng null object pattern để tránh "auto-promote" user thành đối tác khi chưa mua gói
        return new \App\Models\DoiTac([
            'id_nguoi_dung' => $userId,
            'ten_goi'       => 'Chưa có gói',
            'so_tien'       => 0,
            'features'      => '',
            'max_doi'       => 0,
            'max_thanh_vien'=> 0,
            'trang_thai'    => 'APPROVED',
        ]);
    }

    public function adminGetData(): \Illuminate\Http\JsonResponse
    {
        try {
            $data = \App\Models\DoiTac::where(function($q) {
                    // Handle cả trang_thai string lẫn integer cũ
                    $q->where('trang_thai', 'APPROVED')
                      ->orWhere('trang_thai', 1)
                      ->orWhere('trang_thai', '1');
                })
                ->whereHas('nguoiDung', function ($query) {
                    $query->where('is_doi_tac', 1);
                })
                ->with(['nguoiDung.chiNhanh'])
                ->get()
                ->unique('id_nguoi_dung')
                ->values();
            
            // Map each partner to resolve which branch/lineage they manage
            $data->each(function ($item) {
                $ownedBranch = ChiNhanh::where('id_nguoi_dung', $item->id_nguoi_dung)->first();
                if ($ownedBranch) {
                    $item->dong_ho = $ownedBranch->ten_chi;
                    $item->id_chi_nhanh = $ownedBranch->id;
                } elseif ($item->nguoiDung && $item->nguoiDung->chiNhanh) {
                    $item->dong_ho = $item->nguoiDung->chiNhanh->ten_chi;
                    $item->id_chi_nhanh = $item->nguoiDung->chiNhanh->id;
                } else {
                    $item->dong_ho = 'Chưa liên kết dòng họ';
                    $item->id_chi_nhanh = null;
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
            // Toggle giữa APPROVED và REJECTED (không dùng 0/1 nữa)
            $newStatus = ($doiTac->trang_thai === 'APPROVED' || $doiTac->trang_thai == 1)
                ? 'REJECTED' : 'APPROVED';
            $doiTac->trang_thai = $newStatus;
            $doiTac->save();

            // Sync is_doi_tac sau khi toggle
            \App\Models\DoiTac::syncUserPartnerStatus($doiTac->id_nguoi_dung);

            return response()->json([
                'status'     => true,
                'message'    => 'Cập nhật trạng thái đối tác thành công!',
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
