<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordMail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $user = NguoiDung::create([
                'ho_ten'        => $request->ho_ten,
                'email'         => $request->email,
                'mat_khau'      => Hash::make($request->mat_khau),
                'so_dien_thoai' => $request->so_dien_thoai,
                'trang_thai'    => 'Hoạt động', // Giá trị Enum
                'vai_tro'       => 'Thành viên', // Giá trị Enum
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Đăng ký tài khoản thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Đăng ký thất bại: ' . $e->getMessage()
            ], 400);
        }
    }

    public function login(Request $request)
    {
        $user = NguoiDung::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->mat_khau, $user->mat_khau)) {
            return response()->json([
                'status'  => false,
                'message' => 'Email hoặc mật khẩu không chính xác!'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'       => true,
            'message'      => 'Đăng nhập thành công!',
            'access_token' => $token,
            'user'         => $user
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $user = NguoiDung::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Email không tồn tại trong hệ thống!'
            ], 404);
        }

        $code = Str::random(6);
        $user->hash_reset = $code;
        $user->save();

        try {
            Mail::to($user->email)->send(new ResetPasswordMail($user, $code));
            return response()->json([
                'status'  => true,
                'message' => 'Mã xác nhận đã được gửi vào Gmail của bạn!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Không thể gửi mail: ' . $e->getMessage()
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $user = NguoiDung::where('email', $request->email)
                         ->where('hash_reset', $request->code)
                         ->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Mã xác nhận không chính xác!'
            ], 400);
        }

        $user->mat_khau = Hash::make($request->mat_khau);
        $user->hash_reset = null;
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Đổi mật khẩu thành công!'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Đã đăng xuất']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'ho_ten' => 'required|string|max:255',
            'so_dien_thoai' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user->ho_ten = $request->ho_ten;
        $user->so_dien_thoai = $request->so_dien_thoai;

        if ($request->hasFile('avatar')) {
            $image = $request->file('avatar');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/avatars'), $imageName);
            $user->avatar = request()->getSchemeAndHttpHost() . '/uploads/avatars/' . $imageName;
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thông tin thành công!',
            'user' => $user
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->mat_khau)) {
            return response()->json([
                'status' => false,
                'message' => 'Mật khẩu hiện tại không đúng!'
            ], 400);
        }

        $user->mat_khau = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Đổi mật khẩu thành công!'
        ]);
    }
}
