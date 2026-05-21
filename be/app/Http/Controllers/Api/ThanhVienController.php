<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiNhanh;
use App\Models\ThanhVien;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ThanhVienController extends Controller
{
    private const MEMBER_FIELDS = [
        'ho_ten',
        'ten_goi',
        'gioi_tinh',
        'ngay_sinh',
        'ngay_mat',
        'noi_sinh',
        'nghe_nghiep',
        'avatar',
        'doi_thu',
        'chi_nhanh_id',
        'cha_id',
        'me_id',
        'ghi_chu',
        'trang_thai',
        'loai_quan_he',
        'spouse_of_id',
    ];

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'chi_nhanh_id' => 'required|exists:chi_nhanhs,id',
                'ho_ten' => 'required|string|max:255',
                'gioi_tinh' => 'required|string',
                'ngay_sinh' => 'nullable|date',
                'ngay_mat' => 'nullable|date',
                'cha_id' => 'nullable|exists:thanh_viens,id',
                'me_id' => 'nullable|exists:thanh_viens,id',
                'trang_thai' => 'nullable|string',
                'ghi_chu' => 'nullable|string',
            ]);

            $thanhVien = ThanhVien::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Thêm thành viên thành công!',
                'data' => $thanhVien,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi thêm thành viên: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getByChiNhanh(int|string $chiNhanhId): JsonResponse
    {
        try {
            $thanhViens = ThanhVien::where('chi_nhanh_id', $chiNhanhId)->get();

            return response()->json([
                'status' => true,
                'data' => $thanhViens,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi lấy dữ liệu: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getData(): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();

            if ($user && (int) $user->is_doi_tac === 1 && $user->vai_tro !== 'Admin') {
                $chiNhanhIds = ChiNhanh::where('id_nguoi_dung', $user->id)->pluck('id');
                $data = ThanhVien::whereIn('chi_nhanh_id', $chiNhanhIds)->get();
            } else {
                $data = ThanhVien::all();
            }

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

    public function create(Request $request): JsonResponse
    {
        try {
            $data = $this->memberData($request);

            if ($request->hasFile('avatar')) {
                $data['avatar'] = $this->storeAvatar($request);
            }

            $item = ThanhVien::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Thêm thành viên '.$request->ho_ten.' thành công!',
                'data' => $item,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi tạo mới: '.$e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request): JsonResponse
    {
        try {
            $item = ThanhVien::findOrFail($request->id);
            $data = $this->memberData($request);

            if ($request->hasFile('avatar')) {
                $data['avatar'] = $this->storeAvatar($request);
            } elseif (isset($data['avatar']) && $data['avatar'] === 'null') {
                $data['avatar'] = null;
            }

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật thành viên '.$request->ho_ten.' thành công!',
                'data' => $item,
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
        try {
            $item = ThanhVien::findOrFail($request->id);
            $hoTen = $item->ho_ten;
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa thành viên '.$hoTen.' thành công!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi xóa: '.$e->getMessage(),
            ], 500);
        }
    }

    public function status(Request $request): JsonResponse
    {
        try {
            $item = ThanhVien::findOrFail($request->id);

            if (! isset($item->trang_thai)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Model này không hỗ trợ trạng thái!',
                ]);
            }

            $item->trang_thai = $item->trang_thai === 'Còn sống' ? 'Đã mất' : 'Còn sống';
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

    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->value;
            $data = ThanhVien::where('ho_ten', 'like', '%'.$query.'%')
                ->orWhere('ten_goi', 'like', '%'.$query.'%')
                ->orWhere('noi_sinh', 'like', '%'.$query.'%')
                ->orWhere('nghe_nghiep', 'like', '%'.$query.'%')
                ->orWhere('ghi_chu', 'like', '%'.$query.'%')
                ->get();

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

    /**
     * @return array<string, mixed>
     */
    private function memberData(Request $request): array
    {
        $data = $request->except(['mat_khau']);
        $aliases = [
            'id_chi_nhanh' => 'chi_nhanh_id',
            'id_cha' => 'cha_id',
            'id_me' => 'me_id',
            'thong_tin_them' => 'ghi_chu',
        ];

        foreach ($aliases as $legacyKey => $field) {
            if ($request->has($legacyKey) && ! $request->has($field)) {
                $data[$field] = $request->input($legacyKey);
            }

            unset($data[$legacyKey]);
        }

        return array_intersect_key($data, array_flip(self::MEMBER_FIELDS));
    }

    private function storeAvatar(Request $request): string
    {
        $image = $request->file('avatar');
        $imageName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
        $image->move(public_path('uploads/avatars'), $imageName);

        return request()->getSchemeAndHttpHost().'/uploads/avatars/'.$imageName;
    }
}
