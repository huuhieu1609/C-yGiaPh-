<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuKien;
use App\Models\ThamGiaSuKien;
use Illuminate\Http\Request;
use Exception;

class SuKienController extends Controller
{
    public function getData(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            // Events with chi_nhanh_id == null are global/public events.
            // If user is admin return all events. If partner return events for their branches + global events.
            // If regular user, attempt to find their member record to determine their chi_nhanh and return global + branch-specific events.
            $query = SuKien::orderBy('ngay_to_chuc', 'desc');

            if (!$user) {
                // not authenticated: return only public events
                $query->whereNull('chi_nhanh_id');
            } elseif ($user->vai_tro === 'Admin') {
                // admin: no further filter
            } elseif ($user->is_doi_tac == 1) {
                // partner: return global + events for branches owned by this partner
                $chiNhanhIds = \App\Models\ChiNhanh::where('id_nguoi_dung', $user->id)->pluck('id')->toArray();
                $query->where(function($q) use ($chiNhanhIds) {
                    $q->whereNull('chi_nhanh_id')->orWhereIn('chi_nhanh_id', $chiNhanhIds);
                });
            } else {
                // regular member: try to locate their ThanhVien to get chi_nhanh_id
                $myMember = \App\Models\ThanhVien::where('email', $user->email)->whereNotNull('email')->first();
                if ($myMember) {
                    $cnId = $myMember->chi_nhanh_id;
                    $query->where(function($q) use ($cnId) {
                        $q->whereNull('chi_nhanh_id')->orWhere('chi_nhanh_id', $cnId);
                    });
                } else {
                    // not associated with a branch: only show public events
                    $query->whereNull('chi_nhanh_id');
                }
            }

            $data = $query->get();

            return response()->json([
                'status'  => true,
                'message' => 'Lấy danh sách sự kiện thành công!',
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $data = $request->validate([
                'tieu_de' => 'required|string|max:255',
                'noi_dung' => 'nullable|string',
                'ngay_to_chuc' => 'required|date',
                'dia_diem' => 'nullable|string|max:255',
                'chi_nhanh_id' => 'nullable|exists:chi_nhanhs,id',
                'loai' => 'required|in:Giỗ tổ,Họp họ,Cưới hỏi,Tang lễ',
            ]);

            $user = auth('sanctum')->user();

            // If partner provided chi_nhanh_id ensure they own it; or auto-assign their branch if omitted
            if ($data['chi_nhanh_id'] ?? null) {
                if ($user && $user->is_doi_tac == 1) {
                    $owns = \App\Models\ChiNhanh::where('id', $data['chi_nhanh_id'])->where('id_nguoi_dung', $user->id)->exists();
                    if (! $owns) {
                        return response()->json(['status' => false, 'message' => 'Bạn không có quyền gán sự kiện cho chi nhánh này.'], 403);
                    }
                }
            } else {
                // if partner and no chi_nhanh_id provided, set to first branch they own
                if ($user && $user->is_doi_tac == 1) {
                    $branchId = \App\Models\ChiNhanh::where('id_nguoi_dung', $user->id)->value('id');
                    if ($branchId) $data['chi_nhanh_id'] = $branchId;
                }
            }

            $item = SuKien::create($data);
            return response()->json([
                'status'  => true,
                'message' => 'Tạo mới sự kiện thành công!',
                'data'    => $item
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Lỗi khi tạo mới sự kiện: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $item = SuKien::findOrFail($request->id);
            $data = $request->validate([
                'tieu_de' => 'required|string|max:255',
                'noi_dung' => 'nullable|string',
                'ngay_to_chuc' => 'required|date',
                'dia_diem' => 'nullable|string|max:255',
                'chi_nhanh_id' => 'nullable|exists:chi_nhanhs,id',
                'loai' => 'required|in:Giỗ tổ,Họp họ,Cưới hỏi,Tang lễ',
            ]);

            $user = auth('sanctum')->user();
            if (isset($data['chi_nhanh_id']) && $data['chi_nhanh_id']) {
                if ($user && $user->is_doi_tac == 1) {
                    $owns = \App\Models\ChiNhanh::where('id', $data['chi_nhanh_id'])->where('id_nguoi_dung', $user->id)->exists();
                    if (! $owns) {
                        return response()->json(['status' => false, 'message' => 'Bạn không có quyền gán sự kiện cho chi nhánh này.'], 403);
                    }
                }
            } else {
                if ($user && $user->is_doi_tac == 1) {
                    $branchId = \App\Models\ChiNhanh::where('id_nguoi_dung', $user->id)->value('id');
                    if ($branchId) $data['chi_nhanh_id'] = $branchId;
                }
            }

            $item->update($data);
            return response()->json([
                'status'  => true,
                'message' => 'Cập nhật sự kiện thành công!',
                'data'    => $item
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Lỗi khi cập nhật sự kiện: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $item = SuKien::findOrFail($request->id);
            $item->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Xóa sự kiện thành công!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Lỗi khi xóa sự kiện: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Client/Partner get participants for event
    public function getParticipants($suKienId)
    {
        try {
            $participants = ThamGiaSuKien::where('su_kien_id', $suKienId)
                ->with('thanhVien')
                ->get()
                ->pluck('thanhVien')
                ->filter()
                ->values();

            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách đăng ký tham gia thành công!',
                'data' => $participants
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi lấy danh sách đăng ký: ' . $e->getMessage()
            ], 500);
        }
    }

    // Client register multiple family members to event
    public function register(Request $request)
    {
        try {
            $request->validate([
                'su_kien_id' => 'required|exists:su_kiens,id',
                'thanh_vien_ids' => 'required|array',
                'thanh_vien_ids.*' => 'exists:thanh_viens,id',
            ]);

            $suKienId = $request->su_kien_id;
            $thanhVienIds = $request->thanh_vien_ids;

            foreach ($thanhVienIds as $thanhVienId) {
                ThamGiaSuKien::updateOrCreate([
                    'su_kien_id' => $suKienId,
                    'thanh_vien_id' => $thanhVienId
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Đăng ký sự kiện thành công!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi đăng ký tham gia: ' . $e->getMessage()
            ], 500);
        }
    }

    // Client cancel event registration
    public function unregister(Request $request)
    {
        try {
            $request->validate([
                'su_kien_id' => 'required|exists:su_kiens,id',
                'thanh_vien_id' => 'required|exists:thanh_viens,id',
            ]);

            ThamGiaSuKien::where('su_kien_id', $request->su_kien_id)
                ->where('thanh_vien_id', $request->thanh_vien_id)
                ->delete();

            return response()->json([
                'status' => true,
                'message' => 'Hủy đăng ký thành công!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi hủy đăng ký: ' . $e->getMessage()
            ], 500);
        }
    }
}
