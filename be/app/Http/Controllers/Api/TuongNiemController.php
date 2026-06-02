<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TuongNiem;
use App\Models\ThanhVien;
use App\Models\ChiNhanh;
use Carbon\Carbon;
use Exception;

class TuongNiemController extends Controller
{
    /**
     * Lấy danh sách tri ân & thống kê thắp hương của thành viên đã mất
     */
    public function getTributes($memberId)
    {
        try {
            $member = ThanhVien::findOrFail($memberId);
            if ($member->trang_thai !== 'Đã mất') {
                return response()->json([
                    'status' => false,
                    'message' => 'Thành viên này vẫn còn sống, không thuộc mục tưởng niệm!'
                ], 400);
            }

            // Thống kê số lượng lễ vật đã dâng
            $stats = [
                'Nhang' => TuongNiem::where('thanh_vien_id', $memberId)->where('loai_le_vat', 'Nhang')->count(),
                'Hoa' => TuongNiem::where('thanh_vien_id', $memberId)->where('loai_le_vat', 'Hoa')->count(),
                'Nen' => TuongNiem::where('thanh_vien_id', $memberId)->where('loai_le_vat', 'Nen')->count(),
                'TraiCay' => TuongNiem::where('thanh_vien_id', $memberId)->where('loai_le_vat', 'TraiCay')->count(),
            ];

            // Lấy 50 lời nhắn tri ân mới nhất
            $tributes = TuongNiem::where('thanh_vien_id', $memberId)
                ->whereNotNull('loi_nhan')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Lấy dữ liệu tưởng niệm thành công!',
                'stats' => $stats,
                'tributes' => $tributes
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dâng lễ vật & gửi lời tri ân tổ tiên
     */
    public function createTribute(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Bạn cần đăng nhập!'], 401);
            }

            $request->validate([
                'thanh_vien_id' => 'required|exists:thanh_viens,id',
                'loai_le_vat' => 'required|in:Nhang,Hoa,Nen,TraiCay',
                'loi_nhan' => 'nullable|string'
            ]);

            $member = ThanhVien::findOrFail($request->thanh_vien_id);
            if ($member->trang_thai !== 'Đã mất') {
                return response()->json(['status' => false, 'message' => 'Không thể thắp hương cho thành viên còn sống!'], 400);
            }

            // Tạo bản ghi dâng hương
            $tribute = TuongNiem::create([
                'thanh_vien_id' => $request->thanh_vien_id,
                'nguoi_dung_id' => $user->id,
                'ho_ten_nguoi_gui' => $user->ho_ten,
                'loai_le_vat' => $request->loai_le_vat,
                'loi_nhan' => $request->loi_nhan
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Dâng lễ thành kính lên tổ tiên thành công!',
                'data' => $tribute
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi dâng hương: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tính toán danh sách ngày kỵ nhật (ngày giỗ) sắp tới trong dòng họ
     */
    public function getUpcomingAnniversaries()
    {
        try {
            $user = auth('sanctum')->user();
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Bạn cần đăng nhập!'], 401);
            }

            // Lấy danh sách chi nhánh được phân quyền của user
            if ($user->vai_tro === 'Admin' || $user->isAdminOrSubAdmin()) {
                $chiNhanhIds = ChiNhanh::pluck('id');
            } elseif ($user->is_doi_tac == 1) {
                $chiNhanhIds = ChiNhanh::getManagedBranchIds($user);
            } else {
                $myMember = ThanhVien::where('email', $user->email)->whereNotNull('email')->first();
                if ($myMember) {
                    $chiNhanhIds = [$myMember->chi_nhanh_id];
                } else {
                    $chiNhanhIds = [];
                }
            }

            // Lấy toàn bộ thành viên đã mất trong chi nhánh
            $deceased = ThanhVien::whereIn('chi_nhanh_id', $chiNhanhIds)
                ->where('trang_thai', 'Đã mất')
                ->whereNotNull('ngay_mat')
                ->get();

            $today = Carbon::today();
            $upcoming = [];

            foreach ($deceased as $tv) {
                $dateMat = Carbon::parse($tv->ngay_mat);
                
                // Ngày giỗ trong năm hiện tại
                $anniversary = Carbon::create($today->year, $dateMat->month, $dateMat->day)->startOfDay();
                
                // Nếu ngày giỗ năm nay đã qua, tính ngày giỗ của năm sau
                if ($anniversary->lt($today)) {
                    $anniversary->addYear();
                }

                $daysRemaining = $today->diffInDays($anniversary, false);

                // Giỗ lần thứ mấy
                $yearsMat = $anniversary->year - $dateMat->year;

                // Chỉ trả về ngày giỗ sắp tới trong vòng 45 ngày để con cháu chuẩn bị chu đáo
                if ($daysRemaining <= 45) {
                    $upcoming[] = [
                        'id' => $tv->id,
                        'ho_ten' => $tv->ho_ten,
                        'avatar' => $tv->avatar,
                        'ngay_mat' => $tv->ngay_mat,
                        'days_remaining' => $daysRemaining,
                        'years_mat' => $yearsMat,
                        'anniversary_date' => $anniversary->toDateString()
                    ];
                }
            }

            // Sắp xếp ngày giỗ gần nhất lên đầu
            usort($upcoming, function ($a, $b) {
                return $a['days_remaining'] <=> $b['days_remaining'];
            });

            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách kỵ nhật thành công!',
                'data' => $upcoming
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
