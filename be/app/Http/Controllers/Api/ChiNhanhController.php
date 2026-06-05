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

        // Tự động tạo 10 thế hệ (Đời) mặc định cho chi nhánh mới
        $generationNames = [
            1 => 'Thủy Tổ Khai Sáng',
            2 => 'Viễn Tổ Trung Hưng',
            3 => 'Tằng Tổ Phát Triển',
            4 => 'Cao Tổ Kiến Thiết',
            5 => 'Tổ Khảo Kế Thừa',
            6 => 'Thế Hệ Tiếp Bước',
            7 => 'Thế Hệ Đổi Mới',
            8 => 'Thế Hệ Hội Nhập',
            9 => 'Thế Hệ Tinh Anh',
            10 => 'Thế Hệ Tương Lai',
        ];
        $generationDescriptions = [
            1 => 'Thế hệ đầu tiên khai sơn lập địa, đặt nền móng cho dòng tộc.',
            2 => 'Thế hệ thứ hai tiếp nối chí hướng mở mang gia nghiệp.',
            3 => 'Thế hệ thứ ba củng cố gia đạo và phát triển kinh tế.',
            4 => 'Thế hệ thứ tư chấn hưng văn hóa, khuyến khích học hành.',
            5 => 'Thế hệ thứ năm kế thừa di sản văn hóa, giữ gìn giềng mối gia đình.',
            6 => 'Thế hệ tiếp bước gìn giữ nề nếp gia phong, gia tăng uy tín dòng tộc.',
            7 => 'Thế hệ thời kỳ đổi mới, phát triển kinh tế xã hội hiện đại.',
            8 => 'Thế hệ hội nhập quốc tế, học hỏi tinh hoa nhân loại.',
            9 => 'Thế hệ của những tinh hoa ưu tú, mang vinh quang về cho dòng họ.',
            10 => 'Thế hệ trẻ tương lai, niềm hy vọng mới của gia tộc.',
        ];

        $branchName = str_replace('Chi Nhánh ', '', $chiNhanh->ten_chi);
        for ($i = 1; $i <= 10; $i++) {
            \App\Models\DoiTocHo::create([
                'chi_nhanh_id' => $chiNhanh->id,
                'so_doi' => $i,
                'ten_doi' => 'Đời thứ ' . $i . ' (' . $generationNames[$i] . ' - ' . $branchName . ')',
                'mo_ta' => $generationDescriptions[$i] . ' Thuộc ' . $branchName . '.',
                'trang_thai' => 1,
            ]);
        }

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
