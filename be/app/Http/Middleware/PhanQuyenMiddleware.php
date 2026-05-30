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

        // Kiểm tra chức năng này có đang hoạt động không
        $chucNangRecord = ChucNang::where('ten_chuc_nang', $chucNang)
            ->where('trang_thai', 'Hoạt động')
            ->first();

        if (!$chucNangRecord) {
            // Nếu chức năng không tồn tại trong DB thì bỏ qua kiểm tra (không chặn)
            return $next($request);
        }

        // Kiểm tra quyền hoạt động thực tế sau khi giao thoa
        $active_permissions = \App\Models\ThanhVienChucNang::getMemberActivePermissions($user);
        $hasPermission = in_array($chucNangRecord->ten_chuc_nang, $active_permissions);

        if (!$hasPermission) {
            $msg = 'Bạn không có quyền thực hiện chức năng: ' . $chucNang;
            if ($user->is_doi_tac == 1) {
                $msg = 'Bạn không có quyền sử dụng chức năng này. Khi nào bên Admin bật lại thì bạn mới có thể dùng được!';
            }
            return response()->json([
                'success' => false,
                'message' => $msg,
                'required_permission' => $chucNang,
            ], 403);
        }

        return $next($request);
    }
}
