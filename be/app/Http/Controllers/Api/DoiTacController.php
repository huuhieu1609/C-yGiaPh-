<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoiTac;
use App\Models\ThanhVien;
use App\Models\ChiNhanh;
use Illuminate\Http\Request;
use Exception;

class DoiTacController extends Controller
{
    public function getProfile()
    {
        try {
            $user = auth('sanctum')->user();
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Bạn cần đăng nhập!'], 401);
            }

            $doiTac = DoiTac::where('id_nguoi_dung', $user->id)->first();
            
            return response()->json([
                'status' => true,
                'data' => $doiTac
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Bạn cần đăng nhập!'], 401);
            }

            $doiTac = DoiTac::where('id_nguoi_dung', $user->id)->first();
            if (!$doiTac) {
                return response()->json(['status' => false, 'message' => 'Không tìm thấy hồ sơ đối tác!'], 404);
            }

            $doiTac->update([
                'ten_goi' => $request->ten_goi
            ]);

            // Tự động tạo hoặc cập nhật Chi Nhánh duy nhất cho đối tác
            $chiNhanh = ChiNhanh::where('id_nguoi_dung', $user->id)->first();
            if ($chiNhanh) {
                $chiNhanh->update(['ten_chi' => $request->ten_goi]);
            } else {
                ChiNhanh::create([
                    'id_nguoi_dung' => $user->id,
                    'ten_chi'       => $request->ten_goi,
                    'mo_ta'         => 'Cây gia phả của ' . $request->ten_goi
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật thông tin dòng họ thành công!',
                'data' => $doiTac
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getStatistics()
    {
        try {
            $user = auth('sanctum')->user();
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Bạn cần đăng nhập!'], 401);
            }

            $chiNhanhIds = ChiNhanh::where('id_nguoi_dung', $user->id)->pluck('id');
            
            $totalMembers = ThanhVien::whereIn('chi_nhanh_id', $chiNhanhIds)->count();
            $maxGeneration = ThanhVien::whereIn('chi_nhanh_id', $chiNhanhIds)->max('doi_thu') ?? 0;
            
            // Lấy 5 thành viên cập nhật gần đây nhất
            $recentMembers = ThanhVien::whereIn('chi_nhanh_id', $chiNhanhIds)
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'status' => true,
                'data' => [
                    'total_members' => $totalMembers,
                    'max_generation' => $maxGeneration,
                    'upcoming_events' => 0, // Placeholder
                    'recent_members' => $recentMembers
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
