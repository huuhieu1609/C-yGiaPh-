<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ThanhVien;
use Illuminate\Http\Request;
use Exception;

class ThanhVienController extends Controller
{
    public function getData()
    {
        try {
            $user = auth('sanctum')->user();
            if ($user && $user->is_doi_tac == 1) {
                // Lấy ID các chi nhánh thuộc sở hữu của đối tác này
                $chiNhanhIds = \App\Models\ChiNhanh::where('id_nguoi_dung', $user->id)->pluck('id');
                $data = ThanhVien::whereIn('chi_nhanh_id', $chiNhanhIds)->get();
            } else {
                $data = ThanhVien::all();
            }

            return response()->json([
                'status'  => true,
                'message' => 'Lấy dữ liệu thành công!',
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $data = $request->all();
            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/avatars'), $imageName);
                $data['avatar'] = request()->getSchemeAndHttpHost() . '/uploads/avatars/' . $imageName;
            }
            if ('ThanhVien' === 'NguoiDung' && isset($data['mat_khau'])) {
                $data['mat_khau'] = bcrypt($data['mat_khau']);
            }
            $item = ThanhVien::create($data);
            return response()->json([
                'status'  => true,
                'message' => 'Tạo mới thành công!',
                'data'    => $item
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Lỗi khi tạo mới: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $item = ThanhVien::findOrFail($request->id);
            $data = $request->all();
            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/avatars'), $imageName);
                $data['avatar'] = request()->getSchemeAndHttpHost() . '/uploads/avatars/' . $imageName;
            } elseif (isset($data['avatar']) && $data['avatar'] === 'null') {
                $data['avatar'] = null; // Option to remove avatar if explicitly sent as string 'null'
            }
            if ('ThanhVien' === 'NguoiDung' && isset($data['mat_khau'])) {
                $data['mat_khau'] = bcrypt($data['mat_khau']);
            }
            $item->update($data);
            return response()->json([
                'status'  => true,
                'message' => 'Cập nhật thành công!',
                'data'    => $item
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Lỗi khi cập nhật: ' . $e->getMessage(),
            ], 500);
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
            return response()->json([
                'status'  => false,
                'message' => 'Lỗi khi xóa: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function status(Request $request)
    {
        try {
            $item = ThanhVien::findOrFail($request->id);
            
            if ('ThanhVien' === 'ThanhVien') {
                $item->trang_thai = $item->trang_thai == 'Còn sống' ? 'Đã mất' : 'Còn sống';
            } elseif (isset($item->trang_thai)) {
                $item->trang_thai = $item->trang_thai == 'Hoạt động' ? 'Khóa' : 'Hoạt động';
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Model này không hỗ trợ trạng thái!',
                ]);
            }
            
            $item->save();
            return response()->json([
                'status'  => true,
                'message' => 'Cập nhật trạng thái thành công!',
                'trang_thai' => $item->trang_thai
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Lỗi cập nhật trạng thái: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->value;
            $data = ThanhVien::where('ho_ten', 'like', '%' . $query . '%')->get();
            return response()->json([
                'status'  => true,
                'message' => 'Tìm thấy ' . count($data) . ' kết quả',
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Lỗi khi tìm kiếm: ' . $e->getMessage(),
            ], 500);
        }
    }
}
