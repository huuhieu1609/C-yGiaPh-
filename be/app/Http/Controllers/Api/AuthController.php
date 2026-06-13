<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\ChiTietPhanQuyen;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:nguoi_dungs,email',
            'mat_khau' => 'required|string|min:6',
            'so_dien_thoai' => 'nullable|string|max:20',
        ], [
            'ho_ten.required' => 'Vui lòng nhập họ và tên!',
            'email.required' => 'Vui lòng nhập địa chỉ email!',
            'email.email' => 'Địa chỉ email không đúng định dạng!',
            'email.unique' => 'Địa chỉ email này đã được sử dụng bởi một tài khoản khác!',
            'mat_khau.required' => 'Vui lòng nhập mật khẩu!',
            'mat_khau.min' => 'Mật khẩu phải từ 6 ký tự trở lên!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        try {
            // Find matching member on the tree by email to assign chi_nhanh_id
            $matchingMember = \App\Models\ThanhVien::where('email', $request->email)
                ->whereNotNull('email')
                ->first();

            $user = NguoiDung::create([
                'ho_ten' => $request->ho_ten,
                'email' => $request->email,
                'mat_khau' => Hash::make($request->mat_khau),
                'so_dien_thoai' => $request->so_dien_thoai,
                'trang_thai' => 'Hoạt động', // Giá trị Enum
                'vai_tro' => 'Thành viên', // Giá trị Enum
                'chi_nhanh_id' => $matchingMember ? $matchingMember->chi_nhanh_id : null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Đăng ký tài khoản thành công!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Đăng ký thất bại: '.$e->getMessage(),
            ], 400);
        }
    }

    public function login(Request $request)
    {
        $user = NguoiDung::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->mat_khau, $user->mat_khau)) {
            return response()->json([
                'status' => false,
                'message' => 'Email hoặc mật khẩu không chính xác!',
            ], 401);
        }

        // Auto-sync chi_nhanh_id if null but matching ThanhVien exists on the tree
        if (!$user->chi_nhanh_id && $user->email) {
            $matchingMember = \App\Models\ThanhVien::where('email', $user->email)
                ->whereNotNull('email')
                ->first();
            if ($matchingMember && $matchingMember->chi_nhanh_id) {
                $user->chi_nhanh_id = $matchingMember->chi_nhanh_id;
                $user->save();
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $user->load('chucVu');

        $permissions = \App\Models\ThanhVienChucNang::getMemberActivePermissions($user);

        // Mặc định: Tài khoản người dùng thì vào trang người dùng
        $redirect_url = '/nguoi-dung';

        // Tài khoản Admin hoặc Quản Trị Viên thì đăng nhập vào trang admin
        $chucVu = \App\Models\ChucVu::find($user->id_chuc_vu);
        $roleName = $chucVu ? strtolower($chucVu->ten_chuc_vu) : '';
        if (strtolower(trim($user->vai_tro)) === 'admin' || $user->email === 'admin@master.com' || str_contains($roleName, 'quản trị') || str_contains($roleName, 'admin')) {
            $redirect_url = '/admin/dashboard';
        }
        // Tài khoản người dùng sau khi mua gói (is_doi_tac = 1) thì vào trang đã mua gói
        elseif ($user->is_doi_tac == 1) {
            $redirect_url = '/doi-tac';
        }



        return response()->json([
            'status' => true,
            'message' => 'Đăng nhập thành công!',
            'access_token' => $token,
            'user' => $user,
            'permissions' => $permissions,
            'redirect_url' => $redirect_url,
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $user = NguoiDung::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Email không tồn tại trong hệ thống!',
            ], 404);
        }

        $code = Str::random(6);
        $user->hash_reset = $code;
        $user->save();

        try {
            Mail::to($user->email)->send(new ResetPasswordMail($user, $code));

            return response()->json([
                'status' => true,
                'message' => 'Mã xác nhận đã được gửi vào Gmail của bạn!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể gửi mail: '.$e->getMessage(),
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $user = NguoiDung::where('email', $request->email)
            ->where('hash_reset', $request->code)
            ->first();

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Mã xác nhận không chính xác!',
            ], 400);
        }

        $user->mat_khau = Hash::make($request->mat_khau);
        $user->hash_reset = null;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Đổi mật khẩu thành công!',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Đã đăng xuất']);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->load('chucVu');
        }
        $permissions = \App\Models\ThanhVienChucNang::getMemberActivePermissions($user);

        return response()->json([
            'user' => $user,
            'permissions' => $permissions,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'ho_ten' => 'required|string|max:255',
            'so_dien_thoai' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->ho_ten = $request->ho_ten;
        $user->so_dien_thoai = $request->so_dien_thoai;

        if ($request->hasFile('avatar')) {
            $image = $request->file('avatar');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('uploads/avatars'), $imageName);
            $user->avatar = asset('uploads/avatars/'.$imageName);
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thông tin thành công!',
            'user' => $user,
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if (! Hash::check($request->current_password, $user->mat_khau)) {
            return response()->json([
                'status' => false,
                'message' => 'Mật khẩu hiện tại không đúng!',
            ], 400);
        }

        $user->mat_khau = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Đổi mật khẩu thành công!',
        ]);
    }
}
