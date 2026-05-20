<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MoPhan;
use Exception;
use Illuminate\Http\Request;

class MoPhanController extends Controller
{
    public function getData()
    {
        try {
            $data = MoPhan::all();

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
            $item = MoPhan::create([
                'thanh_vien_id' => $request->thanh_vien_id,
                'ten_mo' => $request->ten_mo,
                'dia_chi' => $request->dia_chi,
                'kinh_do' => $request->kinh_do,
                'vi_do' => $request->vi_do,
                'hinh_anh' => $request->hinh_anh,
                'ghi_chu' => $request->ghi_chu,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Thêm mộ phần '.$request->ten_mo.' thành công!',
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
            $item = MoPhan::findOrFail($request->id);
            $item->update([
                'thanh_vien_id' => $request->thanh_vien_id,
                'ten_mo' => $request->ten_mo,
                'dia_chi' => $request->dia_chi,
                'kinh_do' => $request->kinh_do,
                'vi_do' => $request->vi_do,
                'hinh_anh' => $request->hinh_anh,
                'ghi_chu' => $request->ghi_chu,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật mộ phần '.$request->ten_mo.' thành công!',
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
            $item = MoPhan::findOrFail($request->id);
            $ten_mo = $item->ten_mo;
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa mộ phần '.$ten_mo.' thành công!',
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
            $item = MoPhan::findOrFail($request->id);

            if ('MoPhan' === 'ThanhVien') {
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
            $data = MoPhan::where('ten_mo', 'like', '%'.$query.'%')->get();

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
