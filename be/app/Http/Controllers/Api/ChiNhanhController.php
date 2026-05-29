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

            $data = $user && (int) $user->is_doi_tac === 1 && $user->vai_tro !== 'Admin'
                ? ChiNhanh::whereIn('id', ChiNhanh::getManagedBranchIds($user))->get()
                : ChiNhanh::all();

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

    public function create(ChiNhanhCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = auth('sanctum')->user();

        if ($user && (int) $user->is_doi_tac === 1 && $user->vai_tro !== 'Admin') {
            $data['id_nguoi_dung'] = $user->id;
        }

        $chiNhanh = ChiNhanh::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Thêm chi nhánh '.$request->ten_chi.' thành công!',
            'data' => $chiNhanh,
        ]);
    }

    public function update(ChiNhanhUpdateRequest $request): JsonResponse
    {
        try {
            $chiNhanh = ChiNhanh::findOrFail($request->id);
            $chiNhanh->update($request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật chi nhánh '.$request->ten_chi.' thành công!',
                'data' => $chiNhanh,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi cập nhật: '.$e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        $chiNhanh = ChiNhanh::findOrFail($request->id);

        $chiNhanh->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa chi nhánh '.$chiNhanh->ten_chi.' thành công!',
        ]);
    }

    public function status(Request $request): JsonResponse
    {
        try {
            $chiNhanh = ChiNhanh::findOrFail($request->id);

            if (! isset($chiNhanh->trang_thai)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Model này không hỗ trợ trạng thái!',
                ]);
            }

            $chiNhanh->trang_thai = $chiNhanh->trang_thai === 'Hoạt động' ? 'Khóa' : 'Hoạt động';
            $chiNhanh->save();

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật trạng thái thành công!',
                'trang_thai' => $chiNhanh->trang_thai,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi cập nhật trạng thái: '.$e->getMessage(),
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
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
