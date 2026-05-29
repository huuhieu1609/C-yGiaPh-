<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChucNang;
use Illuminate\Http\Request;

class ChucNangController extends Controller
{
    public function getData()
    {
        $data = ChucNang::all();

        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công!',
            'data' => $data,
        ]);
    }

    public function create(Request $request)
    {
        $item = ChucNang::create([
            'ten_chuc_nang' => $request->ten_chuc_nang,
            'ten_slug' => $request->ten_slug,
            'url' => $request->url,
            'mo_ta' => $request->mo_ta,
            'trang_thai' => $request->trang_thai,
            'hien_thi_cho' => $request->hien_thi_cho,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Thêm chức năng '.$request->ten_chuc_nang.' thành công!',
            'data' => $item,
        ]);
    }

    public function update(Request $request)
    {
        $item = ChucNang::findOrFail($request->id);
        $item->update([
            'ten_chuc_nang' => $request->ten_chuc_nang,
            'ten_slug' => $request->ten_slug,
            'url' => $request->url,
            'mo_ta' => $request->mo_ta,
            'trang_thai' => $request->trang_thai,
            'hien_thi_cho' => $request->hien_thi_cho,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật chức năng '.$request->ten_chuc_nang.' thành công!',
            'data' => $item,
        ]);
    }

    public function delete(Request $request)
    {
        $item = ChucNang::findOrFail($request->id);
        $tenChucNang = $item->ten_chuc_nang;
        $item->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa chức năng '.$tenChucNang.' thành công!',
        ]);
    }

    public function status(Request $request)
    {
        $item = ChucNang::findOrFail($request->id);
        $item->trang_thai = $item->trang_thai == 'Hoạt động' ? 'Khóa' : 'Hoạt động';
        $item->save();

        return response()->json([
            'status' => true,
            'message' => 'Đổi trạng thái thành công!',
            'data' => $item,
        ]);
    }

    public function getComingSoonMenus(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Chưa đăng nhập!',
            ], 401);
        }

        // Xác định nhóm vai trò của user
        $roleGroup = 'Người dùng';
        if ($user->vai_tro === 'Admin') {
            $roleGroup = 'Người dùng';
        } elseif ($user->is_doi_tac == 1) {
            $roleGroup = 'Đối tác';
        } elseif ($user->vai_tro === 'Thành viên') {
            $roleGroup = 'Thành viên';
        }

        // Lấy tất cả các chức năng Coming Soon của nhóm vai trò này
        $data = ChucNang::where('trang_thai', 'Hoạt động')
            ->where('url', '/coming-soon')
            ->where(function ($q) use ($roleGroup) {
                $q->where('hien_thi_cho', 'like', "%{$roleGroup}%");
            })
            ->get(['id', 'ten_chuc_nang', 'url', 'mo_ta']);

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }
}
