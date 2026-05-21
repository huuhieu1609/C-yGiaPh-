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
}