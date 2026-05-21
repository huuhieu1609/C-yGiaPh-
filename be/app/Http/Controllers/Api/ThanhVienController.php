<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ThanhVien;
use Illuminate\Http\Request;
use Exception;

class ThanhVienController extends Controller
{
    // API Thêm thành viên mới vào dòng họ
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'chi_nhanh_id'   => 'required|exists:chi_nhanhs,id',
                'ho_ten'         => 'required|string|max:255',
                'gioi_tinh'      => 'required|string',
                'ngay_sinh'      => 'nullable|date',
                'ngay_mat'       => 'nullable|date',
                'cha_id'         => 'nullable|exists:thanh_viens,id',
                'me_id'          => 'nullable|exists:thanh_viens,id',
                'trang_thai'     => 'nullable|string'
            ]);

            $thanhVien = ThanhVien::create($data);

            return response()->json([
                'status'  => true,
                'message' => 'Thêm thành viên thành công!',
                'data'    => $thanhVien
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Lỗi khi thêm thành viên: ' . $e->getMessage()
            ], 500);
        }
    }

    // API Lấy toàn bộ danh sách thành viên của 1 chi nhánh (để vẽ cây / hiển thị danh sách)
    public function getByChiNhanh($chiNhanhId)
    {
        try {
            $thanhViens = ThanhVien::where('chi_nhanh_id', $chiNhanhId)->get();
            return response()->json(['status' => true, 'data' => $thanhViens]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Lỗi lấy dữ liệu: ' . $e->getMessage()
            ], 500);
        }
    }

    // Cung cấp thêm các hàm chuẩn theo API Resource nếu Frontend dùng
    public function getData()
    {
        try {
            $data = ThanhVien::all();
            return response()->json([
                'status'  => true,
                'message' => 'Lấy dữ liệu thành công!',
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $data = $request->all();
            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/avatars'), $imageName);
                $data['avatar'] = asset('uploads/avatars/' . $imageName);
            }
            $item = ThanhVien::create($data);
            return response()->json([
                'status'  => true,
                'message' => 'Tạo mới thành công!',
                'data'    => $item
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $item = ThanhVien::findOrFail($request->id);
            $data = $request->all();
            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/avatars'), $imageName);
                $data['avatar'] = asset('uploads/avatars/' . $imageName);
            }
            $item->update($data);
            return response()->json([
                'status'  => true,
                'message' => 'Cập nhật thành công!',
                'data'    => $item
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $item = ThanhVien::findOrFail($request->id);
            $item->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Xóa thành công!',
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function status(Request $request)
    {
        try {
            $item = ThanhVien::findOrFail($request->id);
            if (isset($item->trang_thai)) {
                $item->trang_thai = $item->trang_thai == 'Còn sống' ? 'Đã mất' : 'Còn sống';
                $item->save();
            }
            return response()->json([
                'status'  => true,
                'message' => 'Cập nhật trạng thái thành công!',
                'trang_thai' => $item->trang_thai
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->value;
            $data = ThanhVien::where('ho_ten', 'like', '%' . $query . '%')
                             ->orWhere('nghe_nghiep', 'like', '%' . $query . '%')
                             ->get();
            return response()->json([
                'status'  => true,
                'message' => 'Tìm thấy ' . count($data) . ' kết quả',
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}