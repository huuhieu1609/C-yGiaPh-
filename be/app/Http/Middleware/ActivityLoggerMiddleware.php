<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\NhatKyHoatDong;

class ActivityLoggerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Execute the request first, so we only log successful actions (status code 200 or 201)
        $response = $next($request);

        // We only log POST, PUT, PATCH, DELETE requests that are successful
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']) && $response->isSuccessful()) {
            try {
                $user = auth('sanctum')->user() ?: $request->user();
                if ($user) {
                    $hanhDong = $this->determineActionDescription($request);
                    if ($hanhDong) {
                        NhatKyHoatDong::create([
                            'nguoi_dung_id' => $user->id,
                            'hanh_dong'     => $hanhDong,
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                // Prevent any logging failures from interrupting the application flow
                logger()->error('Activity logger middleware error: ' . $e->getMessage());
            }
        }

        return $response;
    }

    /**
     * Determine the activity description based on route and request data.
     */
    private function determineActionDescription(Request $request): ?string
    {
        $uri = $request->path(); // e.g. api/update-profile or api/chi-nhanh/create
        
        // Remove 'api/' prefix if exists
        if (str_starts_with($uri, 'api/')) {
            $uri = substr($uri, 4);
        }

        if ($uri === 'update-profile') {
            $hoTen = $request->input('ho_ten', '');
            return "Cập nhật thông tin tài khoản cá nhân" . ($hoTen ? " (Họ tên mới: $hoTen)" : "");
        }

        if ($uri === 'change-password') {
            return "Thay đổi mật khẩu tài khoản bảo mật";
        }

        if ($uri === 'doi-tac/update-profile') {
            $hoTen = $request->input('ho_ten', '');
            return "Đối tác cập nhật thông tin tài khoản" . ($hoTen ? " (Họ tên: $hoTen)" : "");
        }

        if ($uri === 'doi-tac/tao-chi-nhanh') {
            return "Đối tác tạo chi nhánh mới: " . $request->input('ten_chi', '');
        }

        // Standard resources
        // Format: {prefix}/{action}
        $parts = explode('/', $uri);
        if (count($parts) >= 2) {
            $prefix = $parts[0];
            $action = $parts[1];

            $name = '';
            $entity = '';

            switch ($prefix) {
                case 'chi-nhanh':
                    $name = $request->input('ten_chi', '');
                    $entity = 'chi nhánh dòng họ';
                    break;
                case 'thanh-vien':
                    $name = $request->input('ho_ten', '');
                    $entity = 'thành viên dòng họ';
                    break;
                case 'nguoi-dung':
                    $name = $request->input('ho_ten', '');
                    $entity = 'người dùng';
                    break;
                case 'chuc-vu':
                    $name = $request->input('ten_chuc_vu', '');
                    $entity = 'chức vụ';
                    break;
                case 'chuc-nang':
                    $name = $request->input('ten_chuc_nang', '');
                    $entity = 'chức năng';
                    break;
                case 'doi-toc-ho':
                    $name = $request->input('ten_dong_ho', '');
                    $entity = 'tộc họ';
                    break;
                case 'vo-chong':
                    $entity = 'quan hệ vợ chồng';
                    break;
                case 'con-nuoi':
                    $entity = 'quan hệ con nuôi';
                    break;
                case 'dong-gop':
                    $name = $request->input('noi_dung', '');
                    $entity = 'khoản đóng góp quỹ';
                    break;
                case 'mo-phan':
                    $name = $request->input('ten_mo', '');
                    $entity = 'mộ phần';
                    break;
                case 'nha-tho-ho':
                    $name = $request->input('ten_nha_tho', '');
                    $entity = 'nhà thờ họ';
                    break;
                case 'su-kien':
                    $name = $request->input('ten_su_kien', '');
                    $entity = 'sự kiện';
                    break;
                case 'de-xuat':
                    $name = $request->input('ten_de_xuat', '');
                    $entity = 'đề xuất chỉnh sửa';
                    break;
                case 'tai-lieu':
                    $name = $request->input('ten_tai_lieu', '');
                    $entity = 'tài liệu';
                    break;
                case 'goi-dich-vu':
                    $name = $request->input('ten_goi', '');
                    $entity = 'gói dịch vụ';
                    break;
                case 'thong-bao':
                    $name = $request->input('tieude', '');
                    $entity = 'thông báo';
                    break;
            }

            if ($entity) {
                switch ($action) {
                    case 'create':
                        return "Thêm mới $entity" . ($name ? ": \"$name\"" : "");
                    case 'update':
                        return "Cập nhật $entity" . ($name ? ": \"$name\"" : " ID: " . $request->input('id'));
                    case 'delete':
                        return "Xóa $entity ID: " . $request->input('id');
                    case 'status':
                        return "Thay đổi trạng thái $entity ID: " . $request->input('id');
                    case 'register':
                        return "Đăng ký tham gia $entity ID: " . $request->input('su_kien_id');
                    case 'unregister':
                        return "Hủy đăng ký tham gia $entity ID: " . $request->input('su_kien_id');
                    case 'approve':
                        return "Phê duyệt $entity ID: " . $request->input('id');
                    case 'reject':
                        return "Từ chối $entity ID: " . $request->input('id');
                }
            }
        }

        // Special actions
        if ($uri === 'tuong-niem/create') {
            return "Gửi lời tưởng niệm / thắp hương thành viên dòng họ";
        }

        return null;
    }
}
