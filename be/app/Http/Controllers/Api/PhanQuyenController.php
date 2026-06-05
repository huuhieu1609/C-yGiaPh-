<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiTietPhanQuyen;
use App\Models\ChucNang;
use Illuminate\Http\Request;

class PhanQuyenController extends Controller
{
    public function getChucNang(Request $request)
    {
        $chuc_vu_id = $request->chuc_vu_id;
        $user = $request->user();
        $isMasterAdmin = ($user && (strtolower(trim($user->vai_tro)) === 'admin' || $user->email === 'admin@master.com'));

        // 1. Chặn người dùng không phải Admin tối cao xem các quyền của Admin/Quản trị viên
        $targetChucVu = \App\Models\ChucVu::find($chuc_vu_id);
        if (!$isMasterAdmin) {
            $targetName = $targetChucVu ? strtolower($targetChucVu->ten_chuc_vu) : '';
            if (str_contains($targetName, 'tổng') || str_contains($targetName, 'admin') || str_contains($targetName, 'quản trị') || $chuc_vu_id == $user->id_chuc_vu) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bạn không có quyền quản lý chức vụ này!'
                ], 403);
            }
        }

        // 2. Nếu không phải Admin tối cao, phân loại để cho phép Sub-Admin phân các quyền thành viên
        if (!$isMasterAdmin) {
            $adminFuncs = [
                'Admin Dashboard', 
                'Quản Lý Gia Phả Hệ', 
                'Quản Lý Bản Đồ Hệ Thống', 
                'Quản Lý Dòng Họ Hệ Thống', 
                'Quản Lý Sự Kiện Hệ Thống', 
                'Quản Lý Đóng Góp Hệ Thống', 
                'Quản Lý Nhật Ký Hoạt Động', 
                'Quản Lý Đối Tác', 
                'Quản Lý Người Dùng', 
                'Quản Lý Chức Vụ', 
                'Quản Lý Chức Năng', 
                'Hệ Thống'
            ];

            $targetName = $targetChucVu ? strtolower($targetChucVu->ten_chuc_vu) : '';
            $isTargetAdmin = (str_contains($targetName, 'admin') || str_contains($targetName, 'tổng') || str_contains($targetName, 'quản trị'));
            
            if ($isTargetAdmin) {
                // Đối tượng là Admin/Quản trị viên -> Chỉ trả về các quyền admin mà người phân quyền đang có
                $my_permission_names = \App\Models\ThanhVienChucNang::getMemberActivePermissions($user);
                $list_chuc_nang = ChucNang::where('trang_thai', 'Hoạt động')
                    ->whereIn('ten_chuc_nang', $my_permission_names)
                    ->whereIn('ten_chuc_nang', $adminFuncs)
                    ->get();
            } else {
                // Đối tượng là đối tác/thành viên -> Sub-Admin được phép phân tất cả các quyền thành viên
                $list_chuc_nang = ChucNang::where('trang_thai', 'Hoạt động')
                    ->whereNotIn('ten_chuc_nang', $adminFuncs)
                    ->get();
            }
        } else {
            $list_chuc_nang = ChucNang::where('trang_thai', 'Hoạt động')->get();
        }

        $list_da_chon = ChiTietPhanQuyen::where('chuc_vu_id', $chuc_vu_id)->pluck('chuc_nang_id')->toArray();
        
        return response()->json([
            'status' => true,
            'data' => $list_chuc_nang,
            'da_chon' => $list_da_chon
        ]);
    }

    public function updatePhanQuyen(Request $request)
    {
        $chuc_vu_id = $request->chuc_vu_id;
        $list_chuc_nang = $request->list_chuc_nang; // Array of IDs
        $user = $request->user();
        $isMasterAdmin = ($user && (strtolower(trim($user->vai_tro)) === 'admin' || $user->email === 'admin@master.com'));

        // 1. Chặn người dùng không phải Admin tối cao sửa các quyền của Admin/Quản trị viên
        $chucVu = \App\Models\ChucVu::find($chuc_vu_id);
        if (!$isMasterAdmin) {
            $targetName = $chucVu ? strtolower($chucVu->ten_chuc_vu) : '';
            if (str_contains($targetName, 'tổng') || str_contains($targetName, 'admin') || str_contains($targetName, 'quản trị') || $chuc_vu_id == $user->id_chuc_vu) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bạn không có quyền quản lý chức vụ này!'
                ], 403);
            }
        }

        // 2. Chốt chặn bảo mật chống leo thang đặc quyền
        if (!$isMasterAdmin) {
            $adminFuncs = [
                'Admin Dashboard', 
                'Quản Lý Gia Phả Hệ', 
                'Quản Lý Bản Đồ Hệ Thống', 
                'Quản Lý Dòng Họ Hệ Thống', 
                'Quản Lý Sự Kiện Hệ Thống', 
                'Quản Lý Đóng Góp Hệ Thống', 
                'Quản Lý Nhật Ký Hoạt Động', 
                'Quản Lý Đối Tác', 
                'Quản Lý Người Dùng', 
                'Quản Lý Chức Vụ', 
                'Quản Lý Chức Năng', 
                'Hệ Thống'
            ];

            $targetName = $chucVu ? strtolower($chucVu->ten_chuc_vu) : '';
            $isTargetAdmin = (str_contains($targetName, 'admin') || str_contains($targetName, 'tổng') || str_contains($targetName, 'quản trị'));
            
            if ($isTargetAdmin) {
                // Chỉ cho phép cấp các quyền admin mà người phân quyền đang sở hữu
                $my_permission_names = \App\Models\ThanhVienChucNang::getMemberActivePermissions($user);
                $my_chuc_nang_ids = ChucNang::whereIn('ten_chuc_nang', $my_permission_names)
                    ->whereIn('ten_chuc_nang', $adminFuncs)
                    ->pluck('id')
                    ->toArray();
                $list_chuc_nang = array_values(array_intersect($list_chuc_nang, $my_chuc_nang_ids));
            } else {
                // Chỉ cho phép cấp các quyền thành viên (các quyền không thuộc adminFuncs)
                $my_chuc_nang_ids = ChucNang::whereNotIn('ten_chuc_nang', $adminFuncs)->pluck('id')->toArray();
                $list_chuc_nang = array_values(array_intersect($list_chuc_nang, $my_chuc_nang_ids));
            }
        }

        $roleName = $chucVu ? $chucVu->ten_chuc_vu : '';
        $old_chuc_nang_ids = ChiTietPhanQuyen::where('chuc_vu_id', $chuc_vu_id)->pluck('chuc_nang_id')->toArray();

        // Clear existing
        ChiTietPhanQuyen::where('chuc_vu_id', $chuc_vu_id)->delete();

        // Add new
        foreach ($list_chuc_nang as $id_chuc_nang) {
            ChiTietPhanQuyen::create([
                'chuc_vu_id' => $chuc_vu_id,
                'chuc_nang_id' => $id_chuc_nang
            ]);
        }

        // Generate notifications for Trưởng Nhánh (Quản trị viên chi nhánh) role
        if ($chucVu && (stripos($roleName, 'Trưởng Nhánh') !== false || stripos($roleName, 'Quản trị viên chi nhánh') !== false)) {
            $turned_off_ids = array_diff($old_chuc_nang_ids, $list_chuc_nang);
            $turned_on_ids = array_diff($list_chuc_nang, $old_chuc_nang_ids);

            $all_users = \App\Models\NguoiDung::where('trang_thai', 'Hoạt động')->get();

            // Notify turned off functions
            foreach ($turned_off_ids as $id_chuc_nang) {
                $chucNang = \App\Models\ChucNang::find($id_chuc_nang);
                if ($chucNang) {
                    $ten_chuc_nang = $chucNang->ten_chuc_nang;
                    foreach ($all_users as $user) {
                        \App\Models\NotificationCustom::create([
                            'user_id' => $user->id,
                            'target_member_id' => null,
                            'title' => 'Chức năng tạm khóa',
                            'body' => "Chức năng '{$ten_chuc_nang}' đã bị tạm khóa bởi hệ thống/Admin. Bạn không thể sử dụng chức năng này cho đến khi được bật lại.",
                            'meta' => ['type' => 'permission_lock', 'chuc_nang_id' => $id_chuc_nang, 'ten_chuc_nang' => $ten_chuc_nang]
                        ]);
                    }
                }
            }

            // Notify turned on functions
            foreach ($turned_on_ids as $id_chuc_nang) {
                $chucNang = \App\Models\ChucNang::find($id_chuc_nang);
                if ($chucNang) {
                    $ten_chuc_nang = $chucNang->ten_chuc_nang;
                    foreach ($all_users as $user) {
                        \App\Models\NotificationCustom::create([
                            'user_id' => $user->id,
                            'target_member_id' => null,
                            'title' => 'Chức năng mở khóa',
                            'body' => "Chức năng '{$ten_chuc_nang}' đã được mở khóa. Bạn hiện tại có thể truy cập và sử dụng bình thường.",
                            'meta' => ['type' => 'permission_unlock', 'chuc_nang_id' => $id_chuc_nang, 'ten_chuc_nang' => $ten_chuc_nang]
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật phân quyền thành công!'
        ]);
    }

    public function getMemberChucNang(Request $request)
    {
        $thanh_vien_id = $request->thanh_vien_id;
        $thanhVien = \App\Models\ThanhVien::findOrFail($thanh_vien_id);
        
        // Tìm người dùng có email trùng khớp
        $user = null;
        if (!empty($thanhVien->email)) {
            $user = \App\Models\NguoiDung::where('email', $thanhVien->email)->first();
        }
        
        $idChucVu = null;
        if ($user) {
            $idChucVu = $user->id_chuc_vu;
        }
        
        if (!$idChucVu) {
            $idChucVu = \Illuminate\Support\Facades\DB::table('chuc_vus')
                ->where('ten_chuc_vu', 'like', '%Thành Viên%')
                ->value('id') ?? 3;
        }
        
        $chucVu = \App\Models\ChucVu::find($idChucVu);
        
        $currentUser = $request->user();
        $isMasterAdmin = ($currentUser && (strtolower(trim($currentUser->vai_tro)) === 'admin' || $currentUser->email === 'admin@master.com'));
        $adminFuncs = ['Admin Dashboard', 'Quản Lý Đối Tác', 'Quản Lý Người Dùng', 'Quản Lý Chức Vụ', 'Quản Lý Chức Năng', 'Hệ Thống'];
        
        if (!$isMasterAdmin) {
            $my_permission_names = \App\Models\ThanhVienChucNang::getMemberActivePermissions($currentUser);
            $list_chuc_nang = ChucNang::where('trang_thai', 'Hoạt động')
                ->whereNotIn('ten_chuc_nang', $adminFuncs)
                ->whereIn('ten_chuc_nang', $my_permission_names)
                ->get();
        } else {
            $list_chuc_nang = ChucNang::where('trang_thai', 'Hoạt động')
                ->whereNotIn('ten_chuc_nang', $adminFuncs)
                ->get();
        }
            
        $global_enabled_ids = ChiTietPhanQuyen::where('chuc_vu_id', $idChucVu)->pluck('chuc_nang_id')->toArray();
        
        $custom_records = \App\Models\ThanhVienChucNang::where('thanh_vien_id', $thanh_vien_id)->get();
        $has_customized = $custom_records->isNotEmpty();
        $custom_enabled_ids = $custom_records->where('is_enabled', true)->pluck('chuc_nang_id')->toArray();
        
        return response()->json([
            'status' => true,
            'data' => $list_chuc_nang,
            'global_enabled_ids' => $global_enabled_ids,
            'custom_enabled_ids' => $custom_enabled_ids,
            'has_customized' => $has_customized,
            'member' => $thanhVien,
            'role_name' => $chucVu ? $chucVu->ten_chuc_vu : 'Thành Viên'
        ]);
    }

    public function updateMemberPhanQuyen(Request $request)
    {
        $thanh_vien_id = $request->thanh_vien_id;
        $list_chuc_nang = $request->list_chuc_nang; // Mảng các ID được BẬT
        $currentUser = $request->user();

        // Chốt chặn bảo mật chống leo thang đặc quyền cho thành viên
        $isMasterAdmin = ($currentUser && (strtolower(trim($currentUser->vai_tro)) === 'admin' || $currentUser->email === 'admin@master.com'));
        if (!$isMasterAdmin) {
            $my_permission_names = \App\Models\ThanhVienChucNang::getMemberActivePermissions($currentUser);
            $my_chuc_nang_ids = ChucNang::whereIn('ten_chuc_nang', $my_permission_names)->pluck('id')->toArray();
            $list_chuc_nang = array_values(array_intersect($list_chuc_nang, $my_chuc_nang_ids));
        }
        
        // Xóa các cấu hình cũ của thành viên này
        \App\Models\ThanhVienChucNang::where('thanh_vien_id', $thanh_vien_id)->delete();
        
        // Lấy toàn bộ các chức năng member-facing đang hoạt động
        $adminFuncs = ['Admin Dashboard', 'Quản Lý Đối Tác', 'Quản Lý Người Dùng', 'Quản Lý Chức Vụ', 'Quản Lý Chức Năng', 'Hệ Thống'];
        $all_funcs = ChucNang::where('trang_thai', 'Hoạt động')
            ->whereNotIn('ten_chuc_nang', $adminFuncs)
            ->pluck('id')
            ->toArray();
            
        // Lưu lại trạng thái của từng chức năng: is_enabled = true nếu có trong list_chuc_nang, ngược lại false
        foreach ($all_funcs as $func_id) {
            $is_enabled = in_array($func_id, $list_chuc_nang);
            \App\Models\ThanhVienChucNang::create([
                'thanh_vien_id' => $thanh_vien_id,
                'chuc_nang_id' => $func_id,
                'is_enabled' => $is_enabled
            ]);
        }
        
        return response()->json([
            'status' => true,
            'message' => 'Cập nhật phân quyền thành viên thành công!'
        ]);
    }

    public function linkMemberAccount(Request $request)
    {
        $request->validate([
            'thanh_vien_id' => 'required|exists:thanh_viens,id',
            'email' => 'required|email|exists:nguoi_dungs,email',
        ], [
            'thanh_vien_id.exists' => 'Thành viên không tồn tại!',
            'email.exists' => 'Email này chưa được đăng ký tài khoản trong hệ thống!',
        ]);

        $thanh_vien_id = $request->thanh_vien_id;
        $email = $request->email;

        // Kiểm tra xem email này đã được liên kết với một thành viên khác trên cây chưa
        $already_linked = \App\Models\ThanhVien::where('email', $email)
            ->where('id', '!=', $thanh_vien_id)
            ->exists();

        if ($already_linked) {
            return response()->json([
                'status' => false,
                'message' => 'Email này đã được liên kết với một thành viên khác trên cây!'
            ], 400);
        }

        // Cập nhật email cho thành viên trên cây phả hệ
        $thanhVien = \App\Models\ThanhVien::findOrFail($thanh_vien_id);
        $thanhVien->email = $email;
        $thanhVien->save();

        // Đồng bộ chi_nhanh_id của NguoiDung tương ứng
        $user = \App\Models\NguoiDung::where('email', $email)->first();
        if ($user) {
            $user->chi_nhanh_id = $thanhVien->chi_nhanh_id;
            $user->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Liên kết tài khoản thành viên thành công!'
        ]);
    }

    public function unlinkMemberAccount(Request $request)
    {
        $request->validate([
            'thanh_vien_id' => 'required|exists:thanh_viens,id',
        ]);

        $thanh_vien_id = $request->thanh_vien_id;
        $thanhVien = \App\Models\ThanhVien::findOrFail($thanh_vien_id);
        
        $old_email = $thanhVien->email;

        // Gỡ liên kết email trên cây
        $thanhVien->email = null;
        $thanhVien->save();

        // Gỡ chi_nhanh_id trên người dùng
        if (!empty($old_email)) {
            $user = \App\Models\NguoiDung::where('email', $old_email)->first();
            if ($user) {
                $user->chi_nhanh_id = null;
                $user->save();
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Hủy liên kết tài khoản thành công!'
        ]);
    }
}
