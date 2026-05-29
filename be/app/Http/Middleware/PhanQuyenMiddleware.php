<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ChiTietPhanQuyen;
use App\Models\ChucNang;
use Symfony\Component\HttpFoundation\Response;

/**
 * PhanQuyenMiddleware — Kiểm tra quyền truy cập theo tên chức năng.
 *
 * Cách dùng trong routes:
 *   Route::get('/example', ...)->middleware('phan_quyen:Tên Chức Năng');
 *
 * Quy tắc:
 *   - Master Admin (vai_tro = 'admin') → LUÔN đi qua, không bị chặn.
 *   - User có chức vụ (id_chuc_vu) → kiểm tra chi_tiet_phan_quyens.
 *   - User không có chức vụ → từ chối nếu không phải admin.
 */
class PhanQuyenMiddleware
{
    public function handle(Request $request, Closure $next, string $chucNang): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa xác thực. Vui lòng đăng nhập.',
            ], 401);
        }

        // Master Admin luôn có toàn quyền
        if (strtolower(trim($user->vai_tro)) === 'admin') {
            return $next($request);
        }

        // Đối tác tối cao (chủ tài khoản đối tác) không có chức vụ → có toàn quyền đối tác
        if ($user->is_doi_tac == 1 && !$user->id_chuc_vu) {
            return $next($request);
        }

        // Xác định ID Chức Vụ để kiểm tra quyền:
        // Nếu user không có chức vụ (id_chuc_vu = null) -> Mặc định gán vai trò "Thành Viên" (ID = 3)
        $idChucVu = $user->id_chuc_vu;
        if (!$idChucVu) {
            $idChucVu = \Illuminate\Support\Facades\DB::table('chuc_vus')
                ->where('ten_chuc_vu', 'like', '%Thành Viên%')
                ->value('id') ?? 3;
        }

        // Kiểm tra chức năng này có đang hoạt động không
        $chucNangRecord = ChucNang::where('ten_chuc_nang', $chucNang)
            ->where('trang_thai', 'Hoạt động')
            ->first();

        if (!$chucNangRecord) {
            // Nếu chức năng không tồn tại trong DB thì bỏ qua kiểm tra (không chặn)
            return $next($request);
        }

        // Kiểm tra chức vụ của user (hoặc vai trò mặc định "Thành Viên") có được gán quyền này không
        $hasPermission = ChiTietPhanQuyen::where('chuc_vu_id', $idChucVu)
            ->where('chuc_nang_id', $chucNangRecord->id)
            ->exists();

        if (!$hasPermission) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện chức năng: ' . $chucNang,
                'required_permission' => $chucNang,
            ], 403);
        }

        return $next($request);
    }
}
