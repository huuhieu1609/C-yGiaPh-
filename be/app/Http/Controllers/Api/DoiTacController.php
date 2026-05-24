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

    public function danhSachQuyenChiNhanh($chiNhanhId)
    {
        $user = auth('sanctum')->user();
        $chiNhanh = ChiNhanh::where('id', $chiNhanhId)->where('id_nguoi_dung', $user->id)->first();
        
        if (!$chiNhanh) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy chi nhánh hoặc không có quyền.'], 404);
        }

        $users = $chiNhanh->nguoiDungsDuocPhep()->select('nguoi_dungs.id', 'nguoi_dungs.ho_ten', 'nguoi_dungs.email')->get();
        return response()->json(['status' => true, 'data' => $users]);
    }

    public function capQuyenChiNhanh(Request $request)
    {
        $request->validate([
            'chi_nhanh_id' => 'required|integer',
            'email' => 'required|email'
        ]);

        $user = auth('sanctum')->user();
        $chiNhanh = ChiNhanh::where('id', $request->chi_nhanh_id)->where('id_nguoi_dung', $user->id)->first();

        if (!$chiNhanh) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy chi nhánh hoặc không có quyền.'], 404);
        }

        $targetUser = \App\Models\NguoiDung::where('email', $request->email)->first();
        if (!$targetUser) {
            return response()->json(['status' => false, 'message' => 'Người dùng với email này chưa đăng ký tài khoản trên hệ thống.'], 404);
        }

        // Tránh trùng lặp
        if (!$chiNhanh->nguoiDungsDuocPhep()->where('nguoi_dung_id', $targetUser->id)->exists()) {
            $chiNhanh->nguoiDungsDuocPhep()->attach($targetUser->id);

            // gửi email
            try {
                $tieu_de = "Thông báo cấp quyền xem nhánh tộc";
                $data['chiNhanh'] = $chiNhanh;
                $data['targetUser'] = $targetUser;
                $data['partnerUser'] = $user;
                $data['link'] = env('APP_URL', 'http://localhost:5173') . '/gia-pha?chi_nhanh_id=' . $chiNhanh->id;
                $view = 'emails.grant-branch-access';

                \Illuminate\Support\Facades\Mail::to($targetUser->email)->send(new \App\Mail\MasterMail($tieu_de, $data, $view));
            } catch (\Exception $e) {
                // Log the exception if needed, but we don't want to fail the whole process if email fails
                return response()->json(['status' => true, 'message' => 'Cấp quyền thành công nhưng có lỗi khi gửi email.', 'error' => $e->getMessage()]);
            }
            
            return response()->json(['status' => true, 'message' => 'Cấp quyền và gửi email thông báo thành công.']);
        }

        return response()->json(['status' => false, 'message' => 'Người dùng này đã có quyền xem chi nhánh này.']);
    }

    public function thuHoiQuyenChiNhanh(Request $request)
    {
        $request->validate([
            'chi_nhanh_id' => 'required|integer',
            'nguoi_dung_id' => 'required|integer'
        ]);

        $user = auth('sanctum')->user();
        $chiNhanh = ChiNhanh::where('id', $request->chi_nhanh_id)->where('id_nguoi_dung', $user->id)->first();

        if (!$chiNhanh) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy chi nhánh hoặc không có quyền.'], 404);
        }

        $chiNhanh->nguoiDungsDuocPhep()->detach($request->nguoi_dung_id);

        return response()->json(['status' => true, 'message' => 'Thu hồi quyền truy cập thành công.']);
    }
}