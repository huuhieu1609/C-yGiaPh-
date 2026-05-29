<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RelationshipService;
use App\Models\ThanhVien;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ThanhVienController extends Controller

{
    // API Thêm thành viên mới vào dòng họ
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'id_chi_nhanh' => 'required|exists:chi_nhanhs,id',
                'ho_ten' => 'required|string|max:255',
                'email' => 'nullable|email',
                'gioi_tinh' => 'required|string',
                'ngay_sinh' => 'nullable|date',
                'ngay_mat' => 'nullable|date',
                'id_cha' => 'nullable|exists:thanh_viens,id',
                'id_me' => 'nullable|exists:thanh_viens,id',
                'trang_thai' => 'nullable|string',
                'thong_tin_them' => 'nullable|string',
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
                'message' => 'Lỗi khi thêm thành viên: ' . $e->getMessage(),
            ], 500);
        }
    }

    // API Lấy toàn bộ danh sách thành viên của 1 chi nhánh (để vẽ cây / hiển thị danh sách)
    public function getByChiNhanh($chiNhanhId)
    {
        try {
            $user = auth('sanctum')->user();
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Bạn cần đăng nhập!'], 401);
            }

            // Kiểm tra quyền truy cập chi nhánh
            if ($user->vai_tro === 'Admin') {
                $thanhViens = ThanhVien::where('chi_nhanh_id', $chiNhanhId)->get();
            } elseif ($user->is_doi_tac == 1) {
                // Đối tác phải sở hữu chi nhánh này
                $ownsBranch = \App\Models\ChiNhanh::where('id', $chiNhanhId)->where('id_nguoi_dung', $user->id)->exists();
                if ($ownsBranch) {
                    $thanhViens = ThanhVien::where('chi_nhanh_id', $chiNhanhId)->get();
                } else {
                    return response()->json(['status' => false, 'message' => 'Bạn không có quyền truy cập dòng họ này!'], 403);
                }
            } else {
                // Thành viên bình thường phải thuộc về chi nhánh này
                $belongsToBranch = ($user->chi_nhanh_id && (int)$user->chi_nhanh_id === (int)$chiNhanhId) || ThanhVien::where('email', $user->email)->whereNotNull('email')->where('chi_nhanh_id', $chiNhanhId)->exists();
                if ($belongsToBranch) {
                    $thanhViens = ThanhVien::where('chi_nhanh_id', $chiNhanhId)->get();
                } else {
                    return response()->json(['status' => false, 'message' => 'Bạn không có quyền truy cập dòng họ này!'], 403);
                }
            }

            return response()->json(['status' => true, 'data' => $thanhViens]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi lấy dữ liệu: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Cung cấp thêm các hàm chuẩn theo API Resource nếu Frontend dùng
    public function getData()
    {
        try {
            $user = auth('sanctum')->user();
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Bạn cần đăng nhập!'], 401);
            }

            if ($user->vai_tro === 'Admin') {
                $data = ThanhVien::all();
            } elseif ($user->is_doi_tac == 1) {
                $chiNhanhIds = \App\Models\ChiNhanh::where('id_nguoi_dung', $user->id)->pluck('id');
                $data = ThanhVien::with('chiNhanh')->whereIn('chi_nhanh_id', $chiNhanhIds)->get();
            } else {
                // Thành viên bình thường chỉ được lấy danh sách thành viên thuộc chi nhánh dòng họ của mình
                $cnId = $user->chi_nhanh_id;
                if (!$cnId) {
                    $myMember = ThanhVien::where('email', $user->email)->whereNotNull('email')->first();
                    if ($myMember) {
                        $cnId = $myMember->chi_nhanh_id;
                    }
                }
                if ($cnId) {
                    $data = ThanhVien::with('chiNhanh')->where('chi_nhanh_id', $cnId)->get();
                } else {
                    $data = [];
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Lấy dữ liệu thành công!',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $data = $request->all();
            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/avatars'), $imageName);
                $data['avatar'] = asset('uploads/avatars/' . $imageName);
            }
            $item = ThanhVien::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Tạo mới thành công!',
                'data' => $item,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $item = ThanhVien::findOrFail($request->id);
            $data = $request->all();
            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/avatars'), $imageName);
                $data['avatar'] = asset('uploads/avatars/' . $imageName);
            }
            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật thành công!',
                'data' => $item,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $item = ThanhVien::findOrFail($request->id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa thành công!',
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function status(Request $request)
    {
        try {
            $item = ThanhVien::findOrFail($request->id);
            if (isset($item->trang_thai)) {
                $item->trang_thai = $item->trang_thai == 'Còn sống' ? 'Đã mất' : 'Còn sống';
                $item->save();
            }

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật trạng thái thành công!',
                'trang_thai' => $item->trang_thai,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->value;
            $data = ThanhVien::where('ho_ten', 'like', '%' . $query . '%')
                ->orWhere('email', 'like', '%' . $query . '%')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Tìm thấy ' . count($data) . ' kết quả',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API tra cứu quan hệ (đã được dọn dẹp).
     * Phương thức này chỉ giữ lại để tương thích ngược với route cũ.
     * Toàn bộ logic đã được chuyển sang traCuuQuanHe và RelationshipService.
     */
    public function xacDinhQuanHe(Request $request, RelationshipService $relationshipService)
    {
        // Chuyển tiếp request sang phương thức tra cứu chính đã được tối ưu
        return $this->traCuuQuanHe($request, $relationshipService);
    }

    public function traCuuQuanHe(Request $request, RelationshipService $relationshipService)
    {
        try {
            // Chuẩn hóa tham số để tương thích cả Web (id_a, id_b) và các API clients khác (nguoi_1, nguoi_2)
            $n1 = $request->input('nguoi_1') ?? $request->input('id_a') ?? $request->input('id_1');
            $n2 = $request->input('nguoi_2') ?? $request->input('id_b') ?? $request->input('id_2');

            // Merge để chạy validate
            $request->merge([
                'nguoi_1' => $n1,
                'nguoi_2' => $n2,
            ]);

            $validator = Validator::make($request->all(), [
                'nguoi_1' => 'required|integer|exists:thanh_viens,id',
                'nguoi_2' => 'required|integer|exists:thanh_viens,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ!',
                    'errors' => $validator->errors()
                ], 400);
            }

            if ($request->nguoi_1 == $request->nguoi_2) {
                return response()->json([
                    'status' => false,
                    'success' => false,
                    'message' => 'Hai người cần tra cứu phải khác nhau!'
                ], 400);
            }

            $nguoi1 = ThanhVien::findOrFail($request->nguoi_1);
            $nguoi2 = ThanhVien::findOrFail($request->nguoi_2);

            $result = $relationshipService->resolveDetailed($nguoi1, $nguoi2);
            $term = $relationshipService->resolve($nguoi1, $nguoi2) ?? 'quan hệ';

            // Bổ sung đầy đủ các bộ key để tương thích đồng thời cả Web và Mobile
            return response()->json([
                'status' => true,
                'success' => true,
                'term' => $term, // Cho Web hiển thị trên Badge (Ví dụ: "cụ họ")
                'relationship' => $result['relationship'], // Cho Mobile/API (Ví dụ: "Nguyễn Đức Cường là cụ họ của...")
                'description' => $result['relationship'], // Cho Web hiển thị ở Footer (Ví dụ: "Nguyễn Đức Cường là cụ họ của...")
                'path' => $result['path'],
                'members' => $result['members']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Lấy thông tin công khai của một thành viên, bao gồm các mối quan hệ cơ bản
     * và xác định quan hệ với người đang xem (nếu có).
     */
    public function getPublicDetail($id, RelationshipService $relationshipService)
    {
        try {
            $member = ThanhVien::with('chiNhanh')->findOrFail($id);

            // Lấy thông tin Cha và Mẹ
            $father = $member->cha_id ? ThanhVien::find($member->cha_id) : null; // Giữ lại để trả về cho API
            $mother = $member->me_id ? ThanhVien::find($member->me_id) : null; // Giữ lại để trả về cho API

            // Lấy danh sách Vợ/Chồng
            $spouses = collect();
            if ($member->spouse_of_id) {
                $mainSpouse = ThanhVien::find($member->spouse_of_id);
                if ($mainSpouse) {
                    $spouses->push($mainSpouse);
                }
            }
            $otherSpouses = ThanhVien::where('spouse_of_id', $id)->get();
            $spouses = $spouses->merge($otherSpouses)->unique('id');

            // Lấy danh sách Con cái
            $children = ThanhVien::where('cha_id', $id)
                ->orWhere('me_id', $id)
                ->get();

            // Sửa lỗi: Dùng RelationshipService để tra cứu, không gọi lại controller khác
            $relationshipMessage = null;
            $user = auth('sanctum')->user();
            if ($user) {
                $me = ThanhVien::where('email', $user->email)->whereNotNull('email')->first();
                if ($me && $me->id != $id) {
                    $relation = $relationshipService->resolve($me, $member);
                    if ($relation) {
                        // Format lại câu cho thân thiện với người dùng
                        $relationshipMessage = "Bạn là {$relation} của {$member->ho_ten}";
                    }
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Lấy dữ liệu thành viên thành công!',
                'data' => [
                    'member' => $member,
                    'father' => $father,
                    'mother' => $mother,
                    'spouses' => $spouses,
                    'children' => $children,
                    'relationship' => $relationshipMessage
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy thành viên hoặc lỗi hệ thống: ' . $e->getMessage()
            ], 404);
        }
    }

    public function uploadAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/avatars'), $imageName);
                $url = asset('uploads/avatars/' . $imageName);

                return response()->json([
                    'status' => true,
                    'message' => 'Tải ảnh lên thành công!',
                    'url' => $url
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy file tải lên.'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi upload ảnh: ' . $e->getMessage()
            ], 500);
        }
    }
}
