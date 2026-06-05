<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TaiLieu;
use Exception;
use Illuminate\Http\Request;

class TaiLieuController extends Controller
{
    public function getData(Request $request)
    {
        try {
            $user = auth('sanctum')->user();

            if ($user && ($user->vai_tro === 'Admin' || $user->isAdminOrSubAdmin())) {
                $data = TaiLieu::orderBy('created_at', 'desc')->get();
            } elseif ($user && $user->is_doi_tac == 1) {
                $chiNhanhIds = \App\Models\ChiNhanh::getManagedBranchIds($user);
                $data = TaiLieu::whereIn('chi_nhanh_id', $chiNhanhIds)
                    ->orWhereNull('chi_nhanh_id')
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $cnId = $user ? $user->chi_nhanh_id : null;
                if (!$cnId && $user) {
                    $myMember = \App\Models\ThanhVien::where('email', $user->email)->whereNotNull('email')->first();
                    if ($myMember) {
                        $cnId = $myMember->chi_nhanh_id;
                    }
                }
                if ($cnId) {
                    $data = TaiLieu::where('chi_nhanh_id', $cnId)
                        ->orWhereNull('chi_nhanh_id')
                        ->orderBy('created_at', 'desc')
                        ->get();
                } else {
                    $data = TaiLieu::whereNull('chi_nhanh_id')
                        ->orderBy('created_at', 'desc')
                        ->get();
                }
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

    public function create(Request $request)
    {
        try {
            $data = $request->only(['tieu_de', 'file_path', 'mo_ta', 'chi_nhanh_id']);

            // Assign chi_nhanh_id from partner's branch if available and not provided
            if (!isset($data['chi_nhanh_id']) || $data['chi_nhanh_id'] === '' || $data['chi_nhanh_id'] === null) {
                $user = auth('sanctum')->user();
                if ($user && $user->is_doi_tac == 1) {
                    $branchId = collect(\App\Models\ChiNhanh::getManagedBranchIds($user))->first();
                    if ($branchId) {
                        $data['chi_nhanh_id'] = $branchId;
                    }
                }
            }

            $item = TaiLieu::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Thêm tài liệu '.$request->tieu_de.' thành công!',
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
            $item = TaiLieu::findOrFail($request->id);
            $data = $request->only(['tieu_de', 'file_path', 'mo_ta', 'chi_nhanh_id']);
            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật tài liệu '.$request->tieu_de.' thành công!',
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
            $item = TaiLieu::findOrFail($request->id);
            $tieu_de = $item->tieu_de;
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa tài liệu '.$tieu_de.' thành công!',
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
            $item = TaiLieu::findOrFail($request->id);

            if ('TaiLieu' === 'ThanhVien') {
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
            $data = TaiLieu::where('tieu_de', 'like', '%'.$query.'%')->get();

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
