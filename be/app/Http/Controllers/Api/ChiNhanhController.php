<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChiNhanhCreateRequest;
use App\Http\Requests\ChiNhanhUpdateRequest;
use App\Models\ChiNhanh;
use Exception;
use Illuminate\Http\Request;

class ChiNhanhController extends Controller
{
    public function getData()
    {
        $data = ChiNhanh::all();

        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công!',
            'data' => $data,
        ]);
    }

    public function create(ChiNhanhCreateRequest $request)
    {
        $chiNhanh = ChiNhanh::create([
            'ten_chi' => $request->ten_chi,
            'mo_ta' => $request->mo_ta,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Thêm chi nhánh '.$request->ten_chi.' thành công!',
            'data' => $chiNhanh,
        ]);
    }

    public function update(ChiNhanhUpdateRequest $request)
    {
        $chiNhanh = ChiNhanh::findOrFail($request->id);

        $chiNhanh->update([
            'ten_chi' => $request->ten_chi,
            'mo_ta' => $request->mo_ta,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật chi nhánh '.$request->ten_chi.' thành công!',
            'data' => $chiNhanh,
        ]);
    }

    public function delete(Request $request)
    {
        $chiNhanh = ChiNhanh::findOrFail($request->id);

        $chiNhanh->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa chi nhánh '.$chiNhanh->ten_chi.' thành công!',
        ]);
    }

    public function status(Request $request)
    {
        try {
            $item = ChiNhanh::findOrFail($request->id);

            if ('ChiNhanh' === 'ThanhVien') {
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
            $data = ChiNhanh::where('ten_chi', 'like', '%'.$query.'%')->get();

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
