<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChucNang;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $validated = $request->validate([
            'ten_chuc_nang' => 'required|string|max:255',
            'ten_slug' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'required|in:Hoạt động,Khóa',
        ]);

        $slug = $validated['ten_slug'] ?? Str::slug($validated['ten_chuc_nang']);
        $url = $validated['url'] ?? '/' . $slug;

        $item = ChucNang::create([
            'ten_chuc_nang' => $validated['ten_chuc_nang'],
            'ten_slug' => $slug,
            'url' => $url,
            'mo_ta' => $validated['mo_ta'] ?? null,
            'trang_thai' => $validated['trang_thai'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Thêm chức năng ' . $validated['ten_chuc_nang'] . ' thành công!',
            'data' => $item,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:chuc_nangs,id',
            'ten_chuc_nang' => 'required|string|max:255',
            'ten_slug' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'required|in:Hoạt động,Khóa',
        ]);

        $item = ChucNang::findOrFail($validated['id']);
        $slug = $validated['ten_slug'] ?? Str::slug($validated['ten_chuc_nang']);
        $url = $validated['url'] ?? '/' . $slug;

        $item->update([
            'ten_chuc_nang' => $validated['ten_chuc_nang'],
            'ten_slug' => $slug,
            'url' => $url,
            'mo_ta' => $validated['mo_ta'] ?? null,
            'trang_thai' => $validated['trang_thai'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật chức năng ' . $validated['ten_chuc_nang'] . ' thành công!',
            'data' => $item,
        ]);
    }

    public function delete(Request $request)
    {
        $item = ChucNang::findOrFail($request->id);
        $name = $item->ten_chuc_nang;
        $item->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa chức năng ' . $name . ' thành công!',
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
