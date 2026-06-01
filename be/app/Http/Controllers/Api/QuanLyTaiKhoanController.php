<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use App\Models\DoiTac;
use App\Models\NhatKyHoatDong;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class QuanLyTaiKhoanController extends Controller
{
    // ── Shared select columns (giảm dữ liệu trả về, tránh load toàn bộ) ──
    private const ACCOUNT_COLUMNS = [
        'id', 'ho_ten', 'email', 'so_dien_thoai', 'vai_tro', 'id_chuc_vu',
        'trang_thai', 'is_doi_tac', 'avatar', 'created_at', 'deleted_at'
    ];

    /**
     * API 1: Thống kê dashboard
     */
    public function getStatistics(): JsonResponse
    {
        try {
            // Dùng raw query để tránh multiple queries
            $stats = DB::selectOne("
                SELECT
                    COUNT(*) as total_accounts,
                    SUM(CASE WHEN vai_tro = 'Thành viên' AND is_doi_tac != 1 THEN 1 ELSE 0 END) as total_members,
                    SUM(CASE WHEN is_doi_tac = 1 THEN 1 ELSE 0 END) as total_partners,
                    SUM(CASE WHEN vai_tro = 'Admin' THEN 1 ELSE 0 END) as total_admins,
                    SUM(CASE WHEN trang_thai = 'Khóa' THEN 1 ELSE 0 END) as total_locked
                FROM nguoi_dungs WHERE deleted_at IS NULL
            ");

            $totalPendingPartners = DoiTac::where('trang_thai', 'PENDING')->count();

            return response()->json([
                'status' => true,
                'data' => [
                    'total_accounts'         => (int) $stats->total_accounts,
                    'total_members'          => (int) $stats->total_members,
                    'total_partners'         => (int) $stats->total_partners,
                    'total_admins'           => (int) $stats->total_admins,
                    'total_locked'           => (int) $stats->total_locked,
                    'total_pending_partners' => (int) $totalPendingPartners,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Lỗi thống kê'], 500);
        }
    }

    /**
     * API 2: Danh sách tài khoản - paginate server-side, no N+1
     */
    public function getAccounts(Request $request): JsonResponse
    {
        try {
            $query = NguoiDung::select(self::ACCOUNT_COLUMNS)
                ->with([
                    'doiTac:id,id_nguoi_dung,ten_goi,so_tien,ngay_bat_dau,ngay_ket_thuc,trang_thai',
                    'chucVu:id,ten_chuc_vu',
                ])
                ->withTrashed();

            // Search realtime (dùng orWhere đúng cú pháp)
            if ($request->filled('search')) {
                $search = trim($request->input('search'));
                $query->where(function ($q) use ($search) {
                    $q->where('ho_ten', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('so_dien_thoai', 'like', "%{$search}%");
                });
            }

            // Filter
            if ($request->filled('filter')) {
                $this->applyFilter($query, $request->input('filter'));
            }

            // Sort
            $sortBy    = in_array($request->input('sort_by'), ['ho_ten', 'created_at', 'vai_tro', 'trang_thai'])
                         ? $request->input('sort_by') : 'created_at';
            $sortOrder = $request->input('sort_order') === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sortBy, $sortOrder);

            $perPage  = min((int) $request->input('per_page', 10), 100);
            $accounts = $query->paginate($perPage);

            return response()->json(['status' => true, 'data' => $accounts]);
        } catch (\Exception $e) {
            Log::error('getAccounts error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Lỗi tải danh sách tài khoản'], 500);
        }
    }

    private function applyFilter($query, string $filter): void
    {
        switch ($filter) {
            case 'MEMBER':
                $query->where('vai_tro', 'Thành viên')->where('is_doi_tac', '!=', 1);
                break;
            case 'PARTNER':
                $query->where('is_doi_tac', 1);
                break;
            case 'ADMIN':
                $query->where('vai_tro', 'Admin');
                break;
            case 'LOCKED':
                $query->where('trang_thai', 'Khóa');
                break;
            case 'DELETED':
                $query->onlyTrashed();
                break;
            case 'PENDING':
                $query->whereHas('doiTac', fn($q) => $q->where('trang_thai', 'PENDING'));
                break;
            case 'ACTIVE':
                $query->whereHas('doiTac', function ($q) {
                    $q->where('trang_thai', 'APPROVED')
                      ->where(fn($sub) => $sub->whereNull('ngay_ket_thuc')->orWhere('ngay_ket_thuc', '>=', now()->toDateString()));
                });
                break;
            case 'EXPIRED':
                $query->whereHas('doiTac', fn($q) => $q->where('ngay_ket_thuc', '<', now()->toDateString()));
                break;
        }
    }

    /**
     * API 3: Chi tiết tài khoản
     */
    public function getAccountDetail($id): JsonResponse
    {
        try {
            $account = NguoiDung::with([
                'doiTac',
                'chucVu:id,ten_chuc_vu',
            ])->withTrashed()->findOrFail($id);

            $logs = NhatKyHoatDong::where('nguoi_dung_id', $id)
                ->orderBy('thoi_gian', 'desc')
                ->limit(20)
                ->get(['id', 'hanh_dong', 'thoi_gian', 'created_at', 'chi_tiet', 'ip', 'trang_thai']);

            return response()->json(['status' => true, 'data' => compact('account', 'logs')]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy tài khoản'], 404);
        }
    }

    /**
     * API 4: Cập nhật tài khoản
     */
    public function updateAccount(Request $request): JsonResponse
    {
        $admin = auth('sanctum')->user();
        if (!$admin || $admin->vai_tro !== 'Admin') {
            return response()->json(['status' => false, 'message' => 'Không có quyền!'], 403);
        }

        $id = $request->input('id');

        $validator = Validator::make($request->all(), [
            'id'           => 'required|exists:nguoi_dungs,id',
            'ho_ten'       => 'required|string|max:255',
            'email'        => "required|email|unique:nguoi_dungs,email,{$id}",
            'so_dien_thoai'=> 'nullable|string|max:20',
            'vai_tro'      => 'required|in:Admin,Thành viên',
            'is_doi_tac'   => 'nullable|integer|in:0,1',
            'trang_thai'   => 'required|in:Hoạt động,Khóa',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
        }

        if ((int)$id === $admin->id) {
            return response()->json(['status' => false, 'message' => 'Không thể tự sửa tài khoản của chính mình thông qua module này!'], 400);
        }

        try {
            return DB::transaction(function () use ($request, $admin, $id) {
                $user = NguoiDung::findOrFail($id);

                if ($request->input('vai_tro') === 'Admin' && $user->vai_tro !== 'Admin' && $admin->id !== 1) {
                    return response()->json(['status' => false, 'message' => 'Chỉ Master Admin mới có quyền cấp vai trò Admin!'], 403);
                }

                $changes = [];
                foreach (['ho_ten', 'email', 'so_dien_thoai', 'vai_tro', 'trang_thai'] as $field) {
                    if ($user->$field !== $request->input($field)) {
                        $changes[$field] = ['from' => $user->$field, 'to' => $request->input($field)];
                    }
                }

                // Cập nhật các thông tin cơ bản
                $user->ho_ten        = $request->input('ho_ten');
                $user->email         = $request->input('email');
                $user->so_dien_thoai = $request->input('so_dien_thoai');
                $user->vai_tro       = $request->input('vai_tro');
                $user->trang_thai    = $request->input('trang_thai');

                // Kiểm tra và xử lý thay đổi is_doi_tac
                if ($request->has('is_doi_tac')) {
                    $newIsDoiTac = (int)$request->input('is_doi_tac');
                    if ($user->is_doi_tac !== $newIsDoiTac) {
                        $changes['is_doi_tac'] = ['from' => $user->is_doi_tac, 'to' => $newIsDoiTac];

                        if ($newIsDoiTac === 1) {
                            // Nâng cấp lên Trưởng Nhánh
                            $doiTac = DoiTac::where('id_nguoi_dung', $user->id)->first();
                            if ($doiTac) {
                                $doiTac->update([
                                    'ten_goi'       => 'Gói Đối Tác',
                                    'ngay_ket_thuc' => now()->addYear()->toDateString(),
                                    'trang_thai'    => 'APPROVED',
                                    'ly_do_tu_choi' => null,
                                ]);
                            } else {
                                DoiTac::create([
                                    'id_nguoi_dung' => $user->id,
                                    'ten_goi'       => 'Gói Đối Tác',
                                    'so_tien'       => 0,
                                    'ngay_bat_dau'  => now()->toDateString(),
                                    'ngay_ket_thuc' => now()->addYear()->toDateString(),
                                    'trang_thai'    => 'APPROVED',
                                ]);
                            }
                            $user->is_doi_tac = 1;

                            // Tự động gán chi_nhanh_id theo email thành viên nếu có
                            if (!$user->chi_nhanh_id) {
                                $linkedMember = \App\Models\ThanhVien::where('email', $user->email)
                                    ->whereNotNull('email')
                                    ->first();
                                if ($linkedMember) {
                                    $user->chi_nhanh_id = $linkedMember->chi_nhanh_id;
                                }
                            }
                        } else {
                            // Hủy quyền Trưởng Nhánh
                            $doiTac = DoiTac::where('id_nguoi_dung', $user->id)->first();
                            if ($doiTac) {
                                $doiTac->update([
                                    'trang_thai'    => 'EXPIRED',
                                    'ngay_ket_thuc' => now()->subDay()->toDateString(),
                                ]);
                            }
                            $user->is_doi_tac = 0;
                        }
                    }
                }

                $user->save();

                if ($request->input('trang_thai') === 'Khóa') {
                    $user->tokens()->delete();
                }

                if (!empty($changes)) {
                    NhatKyHoatDong::ghiLog(
                        'Cập nhật tài khoản #' . $user->id . ' (' . $user->email . ')',
                        [
                            'id_nguoi_dung' => $user->id,
                            'email' => $user->email,
                            'ho_ten' => $user->ho_ten,
                            'changes' => $changes,
                            'updated_by' => $admin->ho_ten,
                        ],
                        'Thành công',
                        $admin->id
                    );
                }

                // Tạo thông điệp phản hồi sinh động cho người cập nhật
                $msg = 'Cập nhật tài khoản thành công!';
                if (isset($changes['is_doi_tac'])) {
                    if ((int)$changes['is_doi_tac']['to'] === 1) {
                        $msg = "Đã nâng cấp tài khoản \"{$user->ho_ten}\" thành Trưởng Nhánh & kích hoạt Gói Đối Tác thành công!";
                    } else {
                        $msg = "Đã hủy quyền Trưởng Nhánh của tài khoản \"{$user->ho_ten}\", chuyển về Thành viên thường thành công!";
                    }
                } else {
                    $msg = "Cập nhật tài khoản \"{$user->ho_ten}\" thành công!";
                }

                return response()->json(['status' => true, 'message' => $msg, 'data' => $user]);
            });
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Lỗi cập nhật: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API 5: Khóa tài khoản
     */
    public function lockAccount(Request $request): JsonResponse
    {
        $admin = auth('sanctum')->user();
        if (!$admin || $admin->vai_tro !== 'Admin') {
            return response()->json(['status' => false, 'message' => 'Không có quyền!'], 403);
        }

        $id = (int) $request->input('id');
        if ($id === $admin->id) {
            return response()->json(['status' => false, 'message' => 'Không thể tự khóa tài khoản của chính mình!'], 400);
        }

        try {
            return DB::transaction(function () use ($id, $admin) {
                $user = NguoiDung::findOrFail($id);
                if ($user->trang_thai === 'Khóa') {
                    return response()->json(['status' => false, 'message' => 'Tài khoản đã bị khóa rồi!'], 400);
                }
                $user->update(['trang_thai' => 'Khóa']);
                $user->tokens()->delete(); // Revoke all tokens → force logout

                NhatKyHoatDong::ghiLog(
                    'Khóa tài khoản #' . $user->id . ' (' . $user->email . ')',
                    [
                        'id_nguoi_dung' => $user->id,
                        'email' => $user->email,
                        'ho_ten' => $user->ho_ten,
                        'vai_tro' => $user->vai_tro,
                        'trang_thai_truoc' => 'Hoạt động',
                        'trang_thai_sau' => 'Khóa',
                        'locked_by' => $admin->ho_ten,
                    ],
                    'Thành công',
                    $admin->id
                );

                return response()->json(['status' => true, 'message' => 'Đã khóa và buộc đăng xuất thiết bị thành công!']);
            });
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API 6: Mở khóa tài khoản
     */
    public function unlockAccount(Request $request): JsonResponse
    {
        $admin = auth('sanctum')->user();
        if (!$admin || $admin->vai_tro !== 'Admin') {
            return response()->json(['status' => false, 'message' => 'Không có quyền!'], 403);
        }

        try {
            return DB::transaction(function () use ($request, $admin) {
                $user = NguoiDung::findOrFail($request->input('id'));
                if ($user->trang_thai !== 'Khóa') {
                    return response()->json(['status' => false, 'message' => 'Tài khoản chưa bị khóa!'], 400);
                }
                $user->update(['trang_thai' => 'Hoạt động']);

                NhatKyHoatDong::ghiLog(
                    'Mở khóa tài khoản #' . $user->id . ' (' . $user->email . ')',
                    [
                        'id_nguoi_dung' => $user->id,
                        'email' => $user->email,
                        'ho_ten' => $user->ho_ten,
                        'vai_tro' => $user->vai_tro,
                        'trang_thai_truoc' => 'Khóa',
                        'trang_thai_sau' => 'Hoạt động',
                        'unlocked_by' => $admin->ho_ten,
                    ],
                    'Thành công',
                    $admin->id
                );

                return response()->json(['status' => true, 'message' => 'Mở khóa tài khoản thành công!']);
            });
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API 7: Nâng cấp / Gia hạn Đối tác
     */
    public function upgradePartner(Request $request): JsonResponse
    {
        $admin = auth('sanctum')->user();
        if (!$admin || $admin->vai_tro !== 'Admin') {
            return response()->json(['status' => false, 'message' => 'Không có quyền!'], 403);
        }

        $validator = Validator::make($request->all(), [
            'id_nguoi_dung' => 'required|exists:nguoi_dungs,id',
            'ten_goi'       => 'required|string|max:255',
            'so_tien'       => 'required|numeric|min:0',
            'thoi_han_nam'  => 'required|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
        }

        $userId = (int) $request->input('id_nguoi_dung');

        try {
            return DB::transaction(function () use ($request, $userId, $admin) {
                $user        = NguoiDung::findOrFail($userId);
                $thoiHanNam  = (int) $request->input('thoi_han_nam');
                $doiTac      = DoiTac::where('id_nguoi_dung', $userId)->first();

                if ($doiTac) {
                    $baseDate = Carbon::parse($doiTac->ngay_ket_thuc ?? now());
                    if ($baseDate->isPast()) $baseDate = now();
                    $ngayKetThuc = $baseDate->addYears($thoiHanNam)->toDateString();

                    $doiTac->update([
                        'ten_goi'       => $request->input('ten_goi'),
                        'so_tien'       => $doiTac->so_tien + $request->input('so_tien'),
                        'ngay_ket_thuc' => $ngayKetThuc,
                        'trang_thai'    => 'APPROVED',
                        'ly_do_tu_choi' => null,
                    ]);
                } else {
                    DoiTac::create([
                        'id_nguoi_dung' => $userId,
                        'ten_goi'       => $request->input('ten_goi'),
                        'so_tien'       => $request->input('so_tien'),
                        'ngay_bat_dau'  => now()->toDateString(),
                        'ngay_ket_thuc' => now()->addYears($thoiHanNam)->toDateString(),
                        'trang_thai'    => 'APPROVED',
                    ]);
                }

                // Sync is_doi_tac (DoiTac model booted hook handles it too)
                $user->is_doi_tac = 1;

                // Nếu user chưa có chi_nhanh_id, tự động liên kết với chi_nhanh_id của thành viên có cùng email
                if (!$user->chi_nhanh_id) {
                    $linkedMember = \App\Models\ThanhVien::where('email', $user->email)
                        ->whereNotNull('email')
                        ->first();
                    if ($linkedMember) {
                        $user->chi_nhanh_id = $linkedMember->chi_nhanh_id;
                    }
                }
                $user->save();

                NhatKyHoatDong::ghiLog(
                    "Cấp/Gia hạn đối tác cho #{$user->id} ({$user->email}). Gói: {$request->input('ten_goi')}, {$thoiHanNam} năm",
                    [
                        'id_nguoi_dung' => $user->id,
                        'email' => $user->email,
                        'ho_ten' => $user->ho_ten,
                        'ten_goi' => $request->input('ten_goi'),
                        'so_tien' => $request->input('so_tien'),
                        'thoi_han_nam' => $thoiHanNam,
                        'chi_nhanh_id' => $user->chi_nhanh_id,
                        'granted_by' => $admin->ho_ten,
                    ],
                    'Thành công',
                    $admin->id
                );

                return response()->json(['status' => true, 'message' => 'Nâng cấp quyền đối tác thành công!']);
            });
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API 8: Hủy quyền Đối tác
     */
    public function removePartner(Request $request): JsonResponse
    {
        $admin = auth('sanctum')->user();
        if (!$admin || $admin->vai_tro !== 'Admin') {
            return response()->json(['status' => false, 'message' => 'Không có quyền!'], 403);
        }

        try {
            return DB::transaction(function () use ($request, $admin) {
                $userId = (int) $request->input('id'); // id của NguoiDung
                $doiTac = DoiTac::where('id_nguoi_dung', $userId)->firstOrFail();
                $user   = NguoiDung::findOrFail($userId);

                $doiTac->update([
                    'trang_thai'    => 'EXPIRED',
                    'ngay_ket_thuc' => now()->subDay()->toDateString(),
                ]);

                $user->update(['is_doi_tac' => 0]);

                NhatKyHoatDong::ghiLog(
                    "Hủy quyền đối tác: #{$user->id} ({$user->email})",
                    [
                        'id_nguoi_dung' => $user->id,
                        'email' => $user->email,
                        'ho_ten' => $user->ho_ten,
                        'is_doi_tac_before' => 1,
                        'is_doi_tac_after' => 0,
                        'chi_nhanh_id' => $user->chi_nhanh_id,
                        'revoked_by' => $admin->ho_ten,
                    ],
                    'Thành công',
                    $admin->id
                );

                return response()->json(['status' => true, 'message' => 'Hủy quyền đối tác thành công!']);
            });
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API 9: Reset mật khẩu
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $admin = auth('sanctum')->user();
        if (!$admin || $admin->vai_tro !== 'Admin') {
            return response()->json(['status' => false, 'message' => 'Không có quyền!'], 403);
        }

        $validator = Validator::make($request->all(), [
            'id'           => 'required|exists:nguoi_dungs,id',
            'mat_khau_moi' => 'required|string|min:6|max:128',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
        }

        try {
            return DB::transaction(function () use ($request, $admin) {
                $user = NguoiDung::findOrFail($request->input('id'));
                $user->update(['mat_khau' => Hash::make($request->input('mat_khau_moi'))]);
                $user->tokens()->delete(); // Revoke all sessions

                NhatKyHoatDong::ghiLog(
                    "Reset mật khẩu tài khoản #{$user->id} ({$user->email})",
                    [
                        'id_nguoi_dung' => $user->id,
                        'email' => $user->email,
                        'ho_ten' => $user->ho_ten,
                        'action' => 'Reset password & revoke active sessions',
                        'reset_by' => $admin->ho_ten,
                    ],
                    'Thành công',
                    $admin->id
                );

                return response()->json(['status' => true, 'message' => 'Reset mật khẩu thành công, đã buộc đăng xuất thiết bị!']);
            });
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API 10: Xóa tài khoản (Soft Delete)
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $admin = auth('sanctum')->user();
        if (!$admin || $admin->vai_tro !== 'Admin') {
            return response()->json(['status' => false, 'message' => 'Không có quyền!'], 403);
        }

        $id = (int) $request->input('id');
        if ($id === $admin->id) {
            return response()->json(['status' => false, 'message' => 'Không thể tự xóa tài khoản của chính mình!'], 400);
        }

        try {
            return DB::transaction(function () use ($id, $admin) {
                $user = NguoiDung::findOrFail($id);

                if ($user->vai_tro === 'Admin' && $admin->id !== 1) {
                    return response()->json(['status' => false, 'message' => 'Chỉ Master Admin mới có quyền xóa tài khoản Admin!'], 403);
                }

                $user->tokens()->delete();
                $user->delete(); // Soft delete

                NhatKyHoatDong::ghiLog(
                    "Soft-delete tài khoản #{$user->id} ({$user->email})",
                    [
                        'id_nguoi_dung' => $user->id,
                        'email' => $user->email,
                        'ho_ten' => $user->ho_ten,
                        'vai_tro' => $user->vai_tro,
                        'deleted_by' => $admin->ho_ten,
                    ],
                    'Thành công',
                    $admin->id
                );

                return response()->json(['status' => true, 'message' => 'Xóa tài khoản thành công (có thể khôi phục)!']);
            });
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API 11: Khôi phục tài khoản
     */
    public function restoreAccount(Request $request): JsonResponse
    {
        $admin = auth('sanctum')->user();
        if (!$admin || $admin->vai_tro !== 'Admin') {
            return response()->json(['status' => false, 'message' => 'Không có quyền!'], 403);
        }

        try {
            return DB::transaction(function () use ($request, $admin) {
                $user = NguoiDung::onlyTrashed()->findOrFail($request->input('id'));
                $user->restore();
                $user->update(['trang_thai' => 'Hoạt động']); // Đảm bảo trạng thái hợp lệ

                // Re-sync đối tác nếu có
                DoiTac::syncUserPartnerStatus($user->id);

                NhatKyHoatDong::ghiLog(
                    "Khôi phục tài khoản #{$user->id} ({$user->email})",
                    [
                        'id_nguoi_dung' => $user->id,
                        'email' => $user->email,
                        'ho_ten' => $user->ho_ten,
                        'vai_tro' => $user->vai_tro,
                        'restored_by' => $admin->ho_ten,
                    ],
                    'Thành công',
                    $admin->id
                );

                return response()->json(['status' => true, 'message' => 'Khôi phục tài khoản thành công!']);
            });
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API 12: Bulk actions - khóa/mở khóa/xóa hàng loạt
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $admin = auth('sanctum')->user();
        if (!$admin || $admin->vai_tro !== 'Admin') {
            return response()->json(['status' => false, 'message' => 'Không có quyền!'], 403);
        }

        $validator = Validator::make($request->all(), [
            'action' => 'required|in:lock,unlock,delete,restore',
            'ids'    => 'required|array|min:1|max:100',
            'ids.*'  => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
        }

        $ids    = array_filter($request->input('ids'), fn($id) => (int)$id !== $admin->id);
        $action = $request->input('action');
        $count  = 0;

        try {
            DB::transaction(function () use ($ids, $action, $admin, &$count) {
                switch ($action) {
                    case 'lock':
                        NguoiDung::whereIn('id', $ids)->where('trang_thai', '!=', 'Khóa')->update(['trang_thai' => 'Khóa']);
                        // Revoke tokens for locked users
                        $lockedUsers = NguoiDung::whereIn('id', $ids)->get();
                        foreach ($lockedUsers as $u) { $u->tokens()->delete(); }
                        $count = count($ids);
                        break;
                    case 'unlock':
                        $count = NguoiDung::whereIn('id', $ids)->update(['trang_thai' => 'Hoạt động']);
                        break;
                    case 'delete':
                        $users = NguoiDung::whereIn('id', $ids)->get();
                        foreach ($users as $u) { $u->tokens()->delete(); $u->delete(); $count++; }
                        break;
                    case 'restore':
                        $users = NguoiDung::onlyTrashed()->whereIn('id', $ids)->get();
                        foreach ($users as $u) { $u->restore(); $count++; }
                        break;
                }

                NhatKyHoatDong::ghiLog(
                    "Bulk action [{$action}] trên " . count($ids) . " tài khoản: IDs=[" . implode(',', $ids) . "]",
                    [
                        'action' => $action,
                        'target_ids' => $ids,
                        'count' => $count,
                        'executed_by' => $admin->ho_ten,
                    ],
                    'Thành công',
                    $admin->id
                );
            });

            return response()->json(['status' => true, 'message' => "Thực hiện [{$action}] thành công cho {$count} tài khoản!"]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Lỗi bulk action: ' . $e->getMessage()], 500);
        }
    }
}
