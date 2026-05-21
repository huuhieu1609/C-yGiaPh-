<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChucNang;

class ChucNangController extends Controller
{
    public function getData()
    {
        $data = ChucNang::all();
        return response()->json([
            'status'  => true,
            'message' => 'Lấy dữ liệu thành công!',
            'data'    => $data,
        ]);
    }

    public function create(Request $request)
    {
        $item = ChucNang::create($request->all());
        return response()->json([
            'status'  => true,
            'message' => 'Tạo mới thành công!',
            'data'    => $item,
        ]);
    }

    public function update(Request $request)
    {
        $item = ChucNang::find($request->id);
        if ($item) {
            $item->update($request->all());
            return response()->json([
                'status'  => true,
                'message' => 'Cập nhật thành công!',
                'data'    => $item,
            ]);
        }
        return response()->json([
            'status'  => false,
            'message' => 'Không tìm thấy dữ liệu!',
        ]);
    }

    public function delete(Request $request)
    {
        $item = ChucNang::find($request->id);
        if ($item) {
            $item->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Xóa thành công!',
            ]);
        }
        return response()->json([
            'status'  => false,
            'message' => 'Không tìm thấy dữ liệu!',
        ]);
    }

    public function status(Request $request)
    {
        $item = ChucNang::find($request->id);
        if ($item) {
            $item->trang_thai = $item->trang_thai == 'Hoạt động' ? 'Khóa' : 'Hoạt động';
            $item->save();
            return response()->json([
                'status'  => true,
                'message' => 'Đổi trạng thái thành công!',
                'data'    => $item,
            ]);
        }
        return response()->json([
            'status'  => false,
            'message' => 'Không tìm thấy dữ liệu!',
        ]);
    }
}
