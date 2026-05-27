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
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật chức năng '.$request->ten_chuc_nang.' thành công!',
            'data' => $item,
        ]);
    }

    public function delete(Request $request)
    {
        $item = ChucNang::findOrFail($request->id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa chức năng '.$item->ten_chuc_nang.' thành công!',
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
}
