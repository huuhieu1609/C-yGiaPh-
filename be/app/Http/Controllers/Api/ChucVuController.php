<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChucVu;
use Illuminate\Http\Request;

class ChucVuController extends Controller
{
    public function getData()
    {
        $data = ChucVu::all();

        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công!',
            'data' => $data,
        ]);
    }

    public function create(Request $request)
    {
        $item = ChucVu::create([
            'ten_chuc_vu' => $request->ten_chuc_vu,
            'mo_ta' => $request->mo_ta,
            'trang_thai' => $request->trang_thai,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Thêm chức vụ '.$request->ten_chuc_vu.' thành công!',
            'data' => $item,
        ]);
    }

    public function update(Request $request)
    {
        $item = ChucVu::findOrFail($request->id);
        $item->update([
            'ten_chuc_vu' => $request->ten_chuc_vu,
            'mo_ta' => $request->mo_ta,
            'trang_thai' => $request->trang_thai,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật chức vụ '.$request->ten_chuc_vu.' thành công!',
            'data' => $item,
        ]);
    }

    public function delete(Request $request)
    {
        $item = ChucVu::findOrFail($request->id);
        $item->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa chức vụ '.$item->ten_chuc_vu.' thành công!',
        ]);
    }

    public function status(Request $request)
    {
        $item = ChucVu::findOrFail($request->id);
        $item->trang_thai = $item->trang_thai == 'Hoạt động' ? 'Khóa' : 'Hoạt động';
        $item->save();

        return response()->json([
            'status' => true,
            'message' => 'Đổi trạng thái thành công!',
            'data' => $item,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->value;
        $data = ChucVu::where('ten_chuc_vu', 'like', '%'.$query.'%')
            ->orWhere('mo_ta', 'like', '%'.$query.'%')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Tìm thấy '.count($data).' kết quả',
            'data' => $data,
        ]);
    }
}
