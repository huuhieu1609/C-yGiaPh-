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
            if ($user && $user->is_doi_tac == 1) {
                // Return all events or events relating to their branch
                // For simplicity events are shared or can be filtered by branch if branch is in su_kiens table.
                // Since su_kiens has no chi_nhanh_id in current schema, we return all events or filter based on relationships.
                // In 2026_05_12_034915_create_su_kiens_table, su_kiens has no chi_nhanh_id. We will return all.
                $data = SuKien::orderBy('ngay_to_chuc', 'desc')->get();
            } else {
                $data = SuKien::orderBy('ngay_to_chuc', 'desc')->get();
            }

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
                'loai' => 'required|in:Giỗ tổ,Họp họ,Cưới hỏi,Tang lễ',
            ]);

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
                'loai' => 'required|in:Giỗ tổ,Họp họ,Cưới hỏi,Tang lễ',
            ]);

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
