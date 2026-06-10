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
                $data = DongGop::with(['nguoiDung.chiNhanh', 'nguoiDung.managedBranches'])->get();
            } else if ($user && $user->is_doi_tac == 1) {
                $branchIds = \App\Models\ChiNhanh::getManagedBranchIds($user);
                $data = DongGop::with(['nguoiDung.chiNhanh', 'nguoiDung.managedBranches'])
                    ->where('trang_thai', 'Đã duyệt')
                    ->where(function ($query) use ($user, $branchIds) {
                        $query->whereHas('nguoiDung', function ($q) use ($branchIds) {
                            $q->whereIn('chi_nhanh_id', $branchIds);
                        })->orWhere('nguoi_dung_id', $user->id);
                    })->get();
            } else if ($user && $user->chi_nhanh_id) {
                $data = DongGop::with(['nguoiDung.chiNhanh', 'nguoiDung.managedBranches'])
                    ->where(function ($query) use ($user) {
                        $query->whereHas('nguoiDung', function ($q) use ($user) {
                            $q->where('chi_nhanh_id', $user->chi_nhanh_id);
                        })->orWhere('nguoi_dung_id', $user->id);
                    })->get();
            } else if ($user) {
                $data = DongGop::with(['nguoiDung.chiNhanh', 'nguoiDung.managedBranches'])->where('nguoi_dung_id', $user->id)->get();
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

    public function getDongGopSepayConfig(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        
        // 1. Resolve branch ID
        $branchId = $request->query('chi_nhanh_id');
        
        if (!$branchId) {
            // If the user is a partner, resolve the branch they own
            if ($user && $user->is_doi_tac == 1) {
                $branch = \App\Models\ChiNhanh::where('id_nguoi_dung', $user->id)->first();
                $branchId = $branch ? $branch->id : null;
            } else if ($user) {
                $branchId = $user->chi_nhanh_id;
            }
        }

        // 2. Look up the branch and its owner's SePay configuration
        if ($branchId) {
            $branch = \App\Models\ChiNhanh::find($branchId);
            if ($branch) {
                $ownerId = $branch->id_nguoi_dung ?: (($user && $user->is_doi_tac == 1) ? $user->id : null);
                
                if ($ownerId) {
                    $owner = \App\Models\NguoiDung::find($ownerId);
                    if ($owner && $owner->is_doi_tac == 1) {
                        if ($owner->sepay_api_token && $owner->sepay_bank_account && $owner->sepay_bank_name) {
                            return response()->json([
                                'status' => true,
                                'is_custom' => true,
                                'is_configured' => true,
                                'data' => [
                                    'sepay_bank_account' => $owner->sepay_bank_account,
                                    'sepay_bank_name' => $owner->sepay_bank_name,
                                    'sepay_bank_owner' => $owner->sepay_bank_owner ?: $owner->ho_ten,
                                ]
                            ]);
                        }
                    }
                }
            }
        }

        // 3. Do not fall back to global SePay details for lineage donations
        return response()->json([
            'status' => true,
            'is_custom' => false,
            'is_configured' => false,
            'message' => 'Dòng họ này chưa thiết lập cấu hình cổng SePay để nhận đóng góp.',
            'data' => null
        ]);
    }
}
