<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChiNhanhCreateRequest;
use App\Http\Requests\ChiNhanhUpdateRequest;
use App\Models\ChiNhanh;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChiNhanhController extends Controller
{
    public function getData(): JsonResponse
    {
        try {

            $user = auth('sanctum')->user();

            $data = $user
                && (int) $user->is_doi_tac === 1
                && $user->vai_tro !== 'Admin'
                ? ChiNhanh::where('id_nguoi_dung', $user->id)->get()
                : ChiNhanh::all();

            return response()->json([
                'status'  => true,
                'message' => 'Lấy dữ liệu thành công!',
                'data'    => $data,
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function create(ChiNhanhCreateRequest $request): JsonResponse
    {
        try {

            $data = $request->validated();

            $user = auth('sanctum')->user();

            if (
                $user
                && (int) $user->is_doi_tac === 1
                && $user->vai_tro !== 'Admin'
            ) {
                $data['id_nguoi_dung'] = $user->id;
            }

            $chiNhanh = ChiNhanh::create($data);

            return response()->json([
                'status'  => true,
                'message' => 'Thêm chi nhánh ' . $request->ten_chi . ' thành công!',
                'data'    => $chiNhanh,
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Lỗi khi thêm: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(ChiNhanhUpdateRequest $request): JsonResponse
    {
        try {

            $chiNhanh = ChiNhanh::findOrFail($request->id);

            $chiNhanh->update($request->validated());

            return response()->json([
                'status'  => true,
                'message' => 'Cập nhật chi nhánh ' . $request->ten_chi . ' thành công!',
                'data'    => $chiNhanh,
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Lỗi khi cập nhật: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $item = ChiNhanh::findOrFail($request->id);
            $item->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Xóa thành công!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Lỗi khi xóa: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {

            $query = $request->value;

            $data = ChiNhanh::where(
                'ten_chi',
                'like',
                '%' . $query . '%'
            )->get();

            return response()->json([
                'status'  => true,
                'message' => 'Tìm thấy ' . count($data) . ' kết quả',
                'data'    => $data,
            ]);
        } catch (Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Lỗi tìm kiếm: ' . $e->getMessage(),
            ], 500);
        }
    }
}
