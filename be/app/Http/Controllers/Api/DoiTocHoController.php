<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoiTocHo;
use Exception;
use Illuminate\Http\Request;

class DoiTocHoController extends Controller
{
    public function getData()
    {
        $data = DoiTocHo::all();

        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công!',
            'data' => $data,
        ]);
    }

    public function create(Request $request)
    {
        $doi_toc_ho = DoiTocHo::create([
            'so_doi' => $request->so_doi,
            'ten_doi' => $request->ten_doi,
            'mo_ta' => $request->mo_ta,
            'trang_thai' => $request->trang_thai,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Thêm '.$request->ten_doi.' thành công!',
            'data' => $doi_toc_ho,
        ]);
    }

    public function update(Request $request)
    {
        $doiTocHo = DoiTocHo::findOrFail($request->id);

        $doiTocHo->update([
            'so_doi' => $request->so_doi,
            'ten_doi' => $request->ten_doi,
            'mo_ta' => $request->mo_ta,
            'trang_thai' => $request->trang_thai,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật '.$request->ten_doi.' thành công!',
            'data' => $doiTocHo,
        ]);
    }

    public function delete($id)
    {
        $doiTocHo = DoiTocHo::findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa '.$doiTocHo->ten_doi.' thành công!',
        ]);
    }

    public function status(Request $request)
    {
        try {
            $item = DoiTocHo::findOrFail($request->id);

            if ('DoiTocHo' === 'ThanhVien') {
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
            $data = DoiTocHo::where('ten_doi', 'like', '%'.$query.'%')->get();

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
