<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NhaThoHo;
use Exception;
use Illuminate\Http\Request;

class NhaThoHoController extends Controller
{
    public function getData()
    {
        $data = NhaThoHo::get();

        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công!',
            'data' => $data,
        ]);
    }

    public function create(Request $request)
    {
        $nhaThoHo = NhaThoHo::create([
            'ten_nha_tho' => $request->ten_nha_tho,
            'dia_chi' => $request->dia_chi,
            'hinh_anh' => $request->hinh_anh,
            'mo_ta' => $request->mo_ta,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Thêm nhà thờ họ '.$request->ten_nha_tho.' thành công!',
            'data' => $nhaThoHo,
        ]);
    }

    public function update(Request $request)
    {
        $nhaThoHo = NhaThoHo::find($request->id);

        if ($nhaThoHo) {
            $nhaThoHo->update([
                'ten_nha_tho' => $request->ten_nha_tho,
                'dia_chi' => $request->dia_chi,
                'hinh_anh' => $request->hinh_anh,
                'mo_ta' => $request->mo_ta,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật nhà thờ họ '.$request->ten_nha_tho.' thành công!',
                'data' => $nhaThoHo,
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Không tìm thấy nhà thờ họ '.$request->ten_nha_tho.' để cập nhật!',
        ]);
    }

    public function delete($id)
    {
        $nhaThoHo = NhaThoHo::find($id);

        if ($nhaThoHo) {
            $nhaThoHo->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa nhà thờ họ '.$nhaThoHo->ten_nha_tho.' thành công!',
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Không tìm thấy nhà thờ họ để xóa!',
        ]);
    }

    public function status(Request $request)
    {
        try {
            $item = NhaThoHo::findOrFail($request->id);

            if ('NhaThoHo' === 'ThanhVien') {
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
            $data = NhaThoHo::where('ten_nha_tho', 'like', '%'.$query.'%')->get();

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
