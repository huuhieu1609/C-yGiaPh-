<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MoPhan;
use App\Models\ChiNhanh;
use App\Models\ThanhVien;
use Illuminate\Http\Request;
use Exception;

class MoPhanController extends Controller
{
    // ─── GET ALL (phân quyền theo role) ────────────────────────────────────────
    public function getData()
    {
        try {
            $user = auth('sanctum')->user();
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Bạn cần đăng nhập!'], 401);
            }

            if ($user->vai_tro === 'Admin') {
                $data = MoPhan::with(['thanhVien'])->get();
            } elseif ($user->is_doi_tac == 1) {
                $chiNhanhIds = ChiNhanh::where('id_nguoi_dung', $user->id)->pluck('id');
                $data = MoPhan::whereHas('thanhVien', function ($q) use ($chiNhanhIds) {
                    $q->whereIn('chi_nhanh_id', $chiNhanhIds);
                })->with(['thanhVien'])->get();
            } else {
                $myMember = ThanhVien::where('email', $user->email)->whereNotNull('email')->first();
                if ($myMember) {
                    $data = MoPhan::whereHas('thanhVien', function ($q) use ($myMember) {
                        $q->where('chi_nhanh_id', $myMember->chi_nhanh_id);
                    })->with(['thanhVien'])->get();
                } else {
                    $data = [];
                }
            }

            // Bổ sung flag hasLocation
            $formatted = collect($data)->map(function ($item) {
                $arr = $item->toArray();
                $arr['has_location'] = !empty($item->vi_do) && !empty($item->kinh_do);
                return $arr;
            });

            return response()->json([
                'status'  => true,
                'message' => 'Lấy dữ liệu thành công!',
                'data'    => $formatted,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─── GET SINGLE ─────────────────────────────────────────────────────────────
    public function getDetail(Request $request, $id = null)
    {
        try {
            $id = $id ?? $request->id;
            $item = MoPhan::with(['thanhVien'])->findOrFail($id);
            return response()->json([
                'status'  => true,
                'message' => 'Lấy chi tiết thành công!',
                'data'    => $item,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Không tìm thấy mộ phần: ' . $e->getMessage(),
            ], 404);
        }
    }

    // ─── NEARBY ─────────────────────────────────────────────────────────────────
    public function getNearby(Request $request)
    {
        try {
            $lat    = (float) $request->get('lat', 0);
            $lng    = (float) $request->get('lng', 0);
            $radius = (float) $request->get('radius', 10); // km

            if ($lat == 0 || $lng == 0) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Vui lòng cung cấp tọa độ lat, lng',
                ], 422);
            }

            $user = auth('sanctum')->user();
            $query = MoPhan::nearby($lat, $lng, $radius)->with(['thanhVien']);

            // Phân quyền
            if ($user && $user->vai_tro !== 'Admin') {
                if ($user->is_doi_tac == 1) {
                    $chiNhanhIds = ChiNhanh::where('id_nguoi_dung', $user->id)->pluck('id');
                    $query->whereHas('thanhVien', function ($q) use ($chiNhanhIds) {
                        $q->whereIn('chi_nhanh_id', $chiNhanhIds);
                    });
                } else {
                    $myMember = ThanhVien::where('email', $user->email)->whereNotNull('email')->first();
                    if ($myMember) {
                        $query->whereHas('thanhVien', function ($q) use ($myMember) {
                            $q->where('chi_nhanh_id', $myMember->chi_nhanh_id);
                        });
                    }
                }
            }

            $data = $query->get();

            return response()->json([
                'status'  => true,
                'message' => 'Tìm thấy ' . count($data) . ' mộ phần gần đây',
                'data'    => $data,
                'center'  => ['lat' => $lat, 'lng' => $lng],
                'radius'  => $radius,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─── CREATE ─────────────────────────────────────────────────────────────────
    public function create(Request $request)
    {
        try {
            $data = $request->only([
                'thanh_vien_id', 'ten_mo', 'dia_chi',
                'khu', 'hang', 'so_mo', 'huong_mo', 'ten_nghia_trang',
                'kinh_do', 'vi_do', 'hinh_anh', 'ghi_chu',
            ]);
            $item = MoPhan::create($data);

            // Ghi nhật ký
            $this->ghiNhatKy("Thêm mộ phần: {$item->ten_mo} (ID: #{$item->id})");

            return response()->json([
                'status'  => true,
                'message' => 'Tạo mới thành công!',
                'data'    => $item->load('thanhVien'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Lỗi khi tạo mới: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─── UPDATE ─────────────────────────────────────────────────────────────────
    public function update(Request $request)
    {
        try {
            $item = MoPhan::findOrFail($request->id);
            $data = $request->only([
                'thanh_vien_id', 'ten_mo', 'dia_chi',
                'khu', 'hang', 'so_mo', 'huong_mo', 'ten_nghia_trang',
                'kinh_do', 'vi_do', 'hinh_anh', 'ghi_chu',
            ]);
            $item->update($data);

            // Ghi nhật ký
            $this->ghiNhatKy("Cập nhật mộ phần: {$item->ten_mo} (ID: #{$item->id})");

            return response()->json([
                'status'  => true,
                'message' => 'Cập nhật thành công!',
                'data'    => $item->load('thanhVien'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Lỗi khi cập nhật: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─── DELETE ─────────────────────────────────────────────────────────────────
    public function delete(Request $request)
    {
        try {
            $item = MoPhan::findOrFail($request->id);
            $name = $item->ten_mo;
            $item->delete();

            // Ghi nhật ký
            $this->ghiNhatKy("Xóa mộ phần: {$name}");

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

    // ─── SEARCH ─────────────────────────────────────────────────────────────────
    public function search(Request $request)
    {
        try {
            $query = $request->value;
            $data = MoPhan::where(function ($q) use ($query) {
                $q->where('ten_mo', 'like', "%{$query}%")
                  ->orWhere('dia_chi', 'like', "%{$query}%")
                  ->orWhere('ten_nghia_trang', 'like', "%{$query}%")
                  ->orWhere('khu', 'like', "%{$query}%");
            })->with(['thanhVien'])->get();

            return response()->json([
                'status'  => true,
                'message' => 'Tìm thấy ' . count($data) . ' kết quả',
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Lỗi khi tìm kiếm: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─── HELPER ─────────────────────────────────────────────────────────────────
    private function ghiNhatKy(string $hanhDong)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user) {
                \App\Models\NhatKyHoatDong::create([
                    'nguoi_dung_id' => $user->id,
                    'hanh_dong'     => $hanhDong,
                ]);
            }
        } catch (\Throwable $e) {
            // Không ném lỗi nếu ghi nhật ký thất bại
        }
    }
}
