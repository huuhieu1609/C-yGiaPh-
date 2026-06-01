<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DongGop;
use Exception;
use Illuminate\Http\Request;

class DongGopController extends Controller
{
    public function getData()
    {
        try {
            $user = auth('sanctum')->user();

            if ($user && strtolower(trim($user->vai_tro)) === 'admin') {
                $data = DongGop::with(['nguoiDung.chiNhanh'])->get();
            } else if ($user && $user->is_doi_tac == 1) {
                $branchIds = \App\Models\ChiNhanh::getManagedBranchIds($user);
                $data = DongGop::with('nguoiDung')
                    ->where('trang_thai', 'Đã duyệt')
                    ->whereHas('nguoiDung', function ($q) use ($branchIds) {
                        $q->whereIn('chi_nhanh_id', $branchIds);
                    })->get();
            } else if ($user && $user->chi_nhanh_id) {
                $data = DongGop::with('nguoiDung')->whereHas('nguoiDung', function ($q) use ($user) {
                    $q->where('chi_nhanh_id', $user->chi_nhanh_id);
                })->get();
            } else {
                $data = [];
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
            $thanhVienId = $request->input('thanh_vien_id');
            $nguoiDungId = $request->input('nguoi_dung_id');

            if ($thanhVienId) {
                $thanhVien = \App\Models\ThanhVien::find($thanhVienId);
                if ($thanhVien) {
                    $nguoiDung = null;
                    if ($thanhVien->email) {
                        $nguoiDung = \App\Models\NguoiDung::where('email', $thanhVien->email)->first();
                    }
                    if (!$nguoiDung) {
                        $dummyEmail = "tv.{$thanhVien->id}@dongho.com";
                        $nguoiDung = \App\Models\NguoiDung::where('email', $dummyEmail)->first();
                        
                        if (!$nguoiDung) {
                            $nguoiDung = \App\Models\NguoiDung::create([
                                'ho_ten' => $thanhVien->ho_ten,
                                'email' => $thanhVien->email ?: $dummyEmail,
                                'mat_khau' => bcrypt('123456'),
                                'chi_nhanh_id' => $thanhVien->chi_nhanh_id,
                                'vai_tro' => 'Thành viên',
                                'trang_thai' => 'Hoạt động',
                                'is_doi_tac' => 0,
                            ]);
                        }
                    }
                    $nguoiDungId = $nguoiDung->id;
                }
            }

            if (!$nguoiDungId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy tài khoản người dùng hoặc thành viên đóng góp!',
                ], 400);
            }

            $data = [
                'nguoi_dung_id' => $nguoiDungId,
                'noi_dung' => $request->input('noi_dung'),
                'trang_thai' => $request->input('trang_thai', 'Đã duyệt'),
            ];

            $item = DongGop::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Thêm đóng góp thành công!',
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
            $item = DongGop::findOrFail($request->id);
            
            $thanhVienId = $request->input('thanh_vien_id');
            $nguoiDungId = $request->input('nguoi_dung_id');

            if ($thanhVienId) {
                $thanhVien = \App\Models\ThanhVien::find($thanhVienId);
                if ($thanhVien) {
                    $nguoiDung = null;
                    if ($thanhVien->email) {
                        $nguoiDung = \App\Models\NguoiDung::where('email', $thanhVien->email)->first();
                    }
                    if (!$nguoiDung) {
                        $dummyEmail = "tv.{$thanhVien->id}@dongho.com";
                        $nguoiDung = \App\Models\NguoiDung::where('email', $dummyEmail)->first();
                        
                        if (!$nguoiDung) {
                            $nguoiDung = \App\Models\NguoiDung::create([
                                'ho_ten' => $thanhVien->ho_ten,
                                'email' => $thanhVien->email ?: $dummyEmail,
                                'mat_khau' => bcrypt('123456'),
                                'chi_nhanh_id' => $thanhVien->chi_nhanh_id,
                                'vai_tro' => 'Thành viên',
                                'trang_thai' => 'Hoạt động',
                                'is_doi_tac' => 0,
                            ]);
                        }
                    }
                    $nguoiDungId = $nguoiDung->id;
                }
            }

            if (!$nguoiDungId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy tài khoản người dùng hoặc thành viên đóng góp!',
                ], 400);
            }

            $data = [
                'nguoi_dung_id' => $nguoiDungId,
                'noi_dung' => $request->input('noi_dung'),
                'trang_thai' => $request->input('trang_thai', 'Đã duyệt'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật đóng góp thành công!',
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
            $item = DongGop::findOrFail($request->id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa đóng góp thành công!',
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
            $item = DongGop::findOrFail($request->id);

            if (isset($item->trang_thai)) {
                $item->trang_thai = $item->trang_thai == 'Đã duyệt' ? 'Chờ duyệt' : 'Đã duyệt';
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
            $data = DongGop::where('noi_dung', 'like', '%'.$query.'%')->get();

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
