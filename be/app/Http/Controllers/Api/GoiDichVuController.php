<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GoiDichVu;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoiDichVuController extends Controller
{
    public function getData(): JsonResponse
    {
        try {
            $data = GoiDichVu::all();

            return response()->json([
                'status' => true,
                'message' => 'Lấy dữ liệu gói dịch vụ thành công!',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function create(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ten_goi' => 'required|string|max:255',
                'gia_ca' => 'required|numeric|min:0',
                'thoi_han' => 'required|integer|min:1',
                'max_doi' => 'required|integer|min:1',
                'max_thanh_vien' => 'required|integer|min:1',
                'mo_ta' => 'nullable|string',
                'trang_thai' => 'required|in:Hoạt động,Tạm dừng',
            ]);

            $item = GoiDichVu::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Thêm gói dịch vụ thành công!',
                'data' => $item,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi tạo mới: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request): JsonResponse
    {
        try {
            $item = GoiDichVu::findOrFail($request->id);
            $validated = $request->validate([
                'ten_goi' => 'required|string|max:255',
                'gia_ca' => 'required|numeric|min:0',
                'thoi_han' => 'required|integer|min:1',
                'max_doi' => 'required|integer|min:1',
                'max_thanh_vien' => 'required|integer|min:1',
                'mo_ta' => 'nullable|string',
                'trang_thai' => 'required|in:Hoạt động,Tạm dừng',
            ]);

            $item->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật gói dịch vụ thành công!',
                'data' => $item,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi cập nhật: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $item = GoiDichVu::findOrFail($request->id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa gói dịch vụ thành công!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi xóa: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function status(Request $request): JsonResponse
    {
        try {
            $item = GoiDichVu::findOrFail($request->id);
            $item->trang_thai = $item->trang_thai == 'Hoạt động' ? 'Tạm dừng' : 'Hoạt động';
            $item->save();

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật trạng thái thành công!',
                'trang_thai' => $item->trang_thai,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi cập nhật trạng thái: ' . $e->getMessage(),
            ], 500);
        }
    }
}
