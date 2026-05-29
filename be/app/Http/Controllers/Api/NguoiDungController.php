<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use Exception;
use Illuminate\Http\Request;

class NguoiDungController extends Controller
{
    public function getData()
    {
        try {
            $user = auth('sanctum')->user();
            if ($user && $user->vai_tro === 'Admin') {
                $data = NguoiDung::all();
            } else if ($user && $user->chi_nhanh_id) {
                $data = NguoiDung::where('chi_nhanh_id', $user->chi_nhanh_id)->get();
            } else if ($user) {
                $data = NguoiDung::where('id', $user->id)->get();
            } else {
                $data = [];
            }

            return response()->json([
                'status' => true,
                'message' => 'Lấy dữ liệu thành công!',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra: '.$e->getMessage(),
            ], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $data = [
                'ho_ten' => $request->ho_ten,
                'email' => $request->email,
                'so_dien_thoai' => $request->so_dien_thoai,
                'avatar' => $request->avatar,
                'vai_tro' => $request->vai_tro,
                'id_chuc_vu' => $request->id_chuc_vu,
                'trang_thai' => $request->trang_thai,
                'is_doi_tac' => $request->is_doi_tac,
                'chi_nhanh_id' => $request->chi_nhanh_id ?? ($user ? $user->chi_nhanh_id : null),
            ];
            if ($request->has('mat_khau') && $request->mat_khau) {
                $data['mat_khau'] = bcrypt($request->mat_khau);
            }
            $item = NguoiDung::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Thêm người dùng '.$request->ho_ten.' thành công!',
                'data' => $item,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi tạo mới: '.$e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $item = NguoiDung::findOrFail($request->id);
            $data = [
                'ho_ten' => $request->ho_ten,
                'email' => $request->email,
                'so_dien_thoai' => $request->so_dien_thoai,
                'avatar' => $request->avatar,
                'vai_tro' => $request->vai_tro,
                'id_chuc_vu' => $request->id_chuc_vu,
                'trang_thai' => $request->trang_thai,
                'is_doi_tac' => $request->is_doi_tac,
                'chi_nhanh_id' => $request->has('chi_nhanh_id') ? $request->chi_nhanh_id : $item->chi_nhanh_id,
            ];
            if ($request->has('mat_khau') && $request->mat_khau) {
                $data['mat_khau'] = bcrypt($request->mat_khau);
            }
            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật người dùng '.$request->ho_ten.' thành công!',
                'data' => $item,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi cập nhật: '.$e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $item = NguoiDung::findOrFail($request->id);
            $ho_ten = $item->ho_ten;
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa người dùng '.$ho_ten.' thành công!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi xóa: '.$e->getMessage(),
            ], 500);
        }
    }

    public function status(Request $request)
    {
        try {
            $item = NguoiDung::findOrFail($request->id);

            if ('NguoiDung' === 'ThanhVien') {
                $item->trang_thai = $item->trang_thai == 'Còn sống' ? 'Đã mất' : 'Còn sống';
            } elseif (isset($item->trang_thai)) {
                $item->trang_thai = $item->trang_thai == 'Hoạt động' ? 'Khóa' : 'Hoạt động';
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Model này không hỗ trợ trạng thái!',
                ]);
            }

            $item->save();

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật trạng thái thành công!',
                'trang_thai' => $item->trang_thai,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi cập nhật trạng thái: '.$e->getMessage(),
            ], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->value;
            $data = NguoiDung::where('ho_ten', 'like', '%'.$query.'%')->get();

            return response()->json([
                'status' => true,
                'message' => 'Tìm thấy '.count($data).' kết quả',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi tìm kiếm: '.$e->getMessage(),
            ], 500);
        }
    }
}
