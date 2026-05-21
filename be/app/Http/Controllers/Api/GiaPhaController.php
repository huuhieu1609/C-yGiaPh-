<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ThanhVien;
use Illuminate\Http\Request;

class GiaPhaController extends Controller
{
    public function getData()
    {
        $data = ThanhVien::all();
        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ho_ten'       => 'required|string|max:255',
            'gioi_tinh'    => 'required|string|in:Nam,Nữ,Khác',
            'ngay_sinh'    => 'nullable|date',
            'ngay_mat'     => 'nullable|date',
            'chi_nhanh_id' => 'required|exists:chi_nhanhs,id',
            // Thêm các rules khác tùy thuộc vào cấu trúc bảng thanh_viens của bạn
        ]);

        // Xử lý logic cơ bản
        $thanhVien = ThanhVien::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Đã thêm thành viên mới thành công!',
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'ho_ten'       => 'sometimes|required|string|max:255',
            'gioi_tinh'    => 'sometimes|required|string|in:Nam,Nữ,Khác',
            'ngay_sinh'    => 'nullable|date',
            'ngay_mat'     => 'nullable|date',
            'chi_nhanh_id' => 'sometimes|required|exists:chi_nhanhs,id',
        ]);

        $thanhVien = ThanhVien::find($request->id);
        if ($thanhVien) {
            $thanhVien->update($validated);
            return response()->json([
                'status' => true,
                'message' => 'Đã cập nhật thông tin thành công!',
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Không tìm thấy thành viên!',
        ], 404);
    }

    public function destroy($id)
    {
        $thanhVien = ThanhVien::find($id);
        if ($thanhVien) {
            $thanhVien->delete();
            return response()->json([
                'status' => true,
                'message' => 'Đã xóa thành viên!',
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Không tìm thấy thành viên!',
        ], 404);
    }
}
