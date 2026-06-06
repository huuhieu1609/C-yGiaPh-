<?php

namespace App\Http\Controllers;

use App\Models\DoiTac;
use App\Models\GoiDichVu;
use App\Models\NguoiDung;
use App\Models\NhatKyHoatDong;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ThanhToanController extends Controller
{
    // ── Liệt kê giao dịch SePay ───────────────────────────────────────────────

    public function index()
    {
        try {
            $apiToken = env('SEPAY_API_TOKEN');
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
            ])->get('https://my.sepay.vn/userapi/transactions/list');

            if ($response->successful()) {
                return response()->json([
                    'status' => true,
                    'data'   => $response->json('transactions'),
                ]);
            }

            return response()->json(['status' => false, 'message' => 'Lỗi kết nối API SePay']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── Xác nhận thanh toán — tự động duyệt nếu đủ tiền ─────────────────────

    public function paymentVerify(Request $request)
    {
        try {
            $nguoiDungId = $request->input('nguoi_dung_id');
            $noiDung     = $request->input('noi_dung');
            $goiAmount   = (float) $request->input('so_tien', 0);

            if (!$nguoiDungId || (!$noiDung && $goiAmount > 0)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thiếu thông tin người dùng hoặc nội dung',
                ]);
            }

            // ── NẾU LÀ GÓI 0 ĐỒNG (MIỄN PHÍ / DÙNG THỬ) ──
            if ($goiAmount == 0) {
                return DB::transaction(function () use ($nguoiDungId, $noiDung, $goiAmount, $request) {
                    $user = NguoiDung::find($nguoiDungId);
                    if (!$user) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Không tìm thấy tài khoản người dùng.',
                        ]);
                    }

                    $tenGoiInput = $request->input('ten_goi');
                    $goi = null;
                    if ($tenGoiInput) {
                        $goi = GoiDichVu::where('ten_goi', $tenGoiInput)->where('gia_ca', 0)->first();
                    }
                    if (!$goi) {
                        $goi = GoiDichVu::where('gia_ca', 0)->first();
                    }

                    $tenGoi         = $goi ? $goi->ten_goi         : ($tenGoiInput ?: 'Gói Dùng Thử Miễn Phí');
                    $thoiHan        = $goi ? (int) $goi->thoi_han  : 1;
                    $featuresOfGoi  = $goi ? $goi->features        : '';
                    $maxDoi         = $goi ? (int) $goi->max_doi        : 10;
                    $maxThanhVien   = $goi ? (int) $goi->max_thanh_vien : 100;
                    $goiId          = $goi ? $goi->id              : null;

                    $ngayBatDau  = now()->toDateString();
                    $ngayKetThuc = now()->addMonths($thoiHan)->toDateString();

                    $existingSamePackage = null;
                    if ($goiId) {
                        $existingSamePackage = DoiTac::where('id_nguoi_dung', $nguoiDungId)
                            ->where('trang_thai', 'APPROVED')
                            ->where('id_goi_dich_vu', $goiId)
                            ->where('ngay_ket_thuc', '>=', now()->toDateString())
                            ->first();
                    }

                    if ($existingSamePackage) {
                        $newExpiry = Carbon::parse($existingSamePackage->ngay_ket_thuc)
                            ->addMonths($thoiHan)
                            ->toDateString();

                        $existingSamePackage->update([
                            'ngay_ket_thuc' => $newExpiry,
                            'features'      => $featuresOfGoi,
                            'max_doi'       => $maxDoi,
                            'max_thanh_vien'=> $maxThanhVien,
                        ]);

                        $targetRecord   = $existingSamePackage;
                        $actionMessage  = "Gia hạn gói miễn phí {$tenGoi} — cộng thêm {$thoiHan} tháng";
                    } else {
                        $targetRecord = DoiTac::create([
                            'id_nguoi_dung'  => $nguoiDungId,
                            'id_goi_dich_vu' => $goiId,
                            'ten_goi'        => $tenGoi,
                            'features'       => $featuresOfGoi,
                            'max_doi'        => $maxDoi,
                            'max_thanh_vien' => $maxThanhVien,
                            'so_tien'        => 0,
                            'ngay_bat_dau'   => $ngayBatDau,
                            'ngay_ket_thuc'  => $ngayKetThuc,
                            'trang_thai'     => 'APPROVED',
                        ]);
                        $actionMessage = "Kích hoạt gói miễn phí {$tenGoi}";
                    }

                    NhatKyHoatDong::ghiLog(
                        sprintf(
                            '%s cho người dùng #%d (%s) — Gói: %s — Miễn phí — HH: %s',
                            $actionMessage,
                            $nguoiDungId,
                            $user->ho_ten,
                            $tenGoi,
                            $targetRecord->ngay_ket_thuc
                        ),
                        [
                            'id_nguoi_dung'  => $nguoiDungId,
                            'id_doi_tac'     => $targetRecord->id,
                            'ten_goi'        => $tenGoi,
                            'so_tien'        => 0,
                            'ngay_bat_dau'   => $ngayBatDau,
                            'ngay_ket_thuc'  => $targetRecord->ngay_ket_thuc,
                            'auto_approved'  => true,
                        ],
                        'Thành công',
                        null
                    );

                    return response()->json([
                        'success'       => true,
                        'is_partner'    => true,
                        'auto_approved' => true,
                        'message'       => sprintf(
                            'Kích hoạt gói miễn phí "%s" thành công!',
                            $tenGoi
                        ),
                        'ten_goi'       => $tenGoi,
                        'ngay_ket_thuc' => $targetRecord->ngay_ket_thuc,
                        'redirect_url'  => '/doi-tac/dashboard',
                    ]);
                });
            }

            // Trích xuất phần nội dung tìm kiếm, ví dụ: "MUAGOI HIEU97995"
            $parts           = explode('|', $noiDung);
            $expectedContent = trim($parts[0]);

            $apiToken = env('SEPAY_API_TOKEN');

            // Quét 20 giao dịch gần nhất từ SePay
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
            ])->get('https://my.sepay.vn/userapi/transactions/list', ['limit' => 20]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi kết nối cổng thanh toán SePay.',
                ]);
            }

            $transactions = $response->json('transactions') ?? [];
            $matchedTx    = null;

            foreach ($transactions as $tx) {
                $amountIn = (float) ($tx['amount_in'] ?? $tx['amount'] ?? 0);
                $isIn     = (isset($tx['transaction_type']) && $tx['transaction_type'] === 'in')
                            || ($amountIn > 0);

                if (!$isIn || $amountIn <= 0) continue;

                $bankContent = strtoupper($tx['transaction_content'] ?? '');
                if (stripos($bankContent, strtoupper($expectedContent)) === false) continue;

                // Chỉ nhận giao dịch trong vòng 24 giờ gần nhất
                $txTime = $tx['transaction_date'] ?? $tx['when'] ?? null;
                if ($txTime) {
                    try {
                        if (Carbon::parse($txTime)->diffInHours(now()) > 24) continue;
                    } catch (\Exception $e) {
                        // không parse được → bỏ qua kiểm tra thời gian
                    }
                }

                $matchedTx             = $tx;
                $matchedTx['amount_in'] = $amountIn;
                break;
            }

            if (!$matchedTx) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chưa tìm thấy giao dịch chuyển khoản. Vui lòng đảm bảo đúng nội dung và thử lại sau.',
                ]);
            }

            // Giao dịch khớp — xử lý bên trong transaction DB
            return DB::transaction(function () use ($matchedTx, $nguoiDungId, $noiDung, $expectedContent, $request) {
                $amountIn = $matchedTx['amount_in'];

                $user = NguoiDung::find($nguoiDungId);
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không tìm thấy tài khoản người dùng.',
                    ]);
                }

                // ── Xác định gói dịch vụ theo số tiền ──────────────────────────
                $goiAmount = (float) $request->input('so_tien', 0);
                if ($goiAmount <= 0) {
                    if (preg_match('/Mua gói dịch vụ:\s*([\d\.,]+)/i', $noiDung, $matches)) {
                        $goiAmount = (float) str_replace([',', '.'], '', $matches[1]);
                    }
                }

                // ── Kiểm tra thiếu tiền ─────────────────────────────────────────
                if ($goiAmount > 0 && $amountIn < $goiAmount) {
                    $thieu = $goiAmount - $amountIn;
                    return response()->json([
                        'success' => false,
                        'message' => sprintf(
                            'Hệ thống ghi nhận bạn đã chuyển %s VNĐ, nhưng giá gói là %s VNĐ. Vui lòng chuyển thêm %s VNĐ để hoàn tất kích hoạt!',
                            number_format($amountIn, 0, ',', '.'),
                            number_format($goiAmount, 0, ',', '.'),
                            number_format($thieu, 0, ',', '.')
                        ),
                    ], 400);
                }

                // ── Kiểm tra là lệnh mua gói ────────────────────────────────────
                $isMuaGoi = stripos($expectedContent, 'MUAGOI') !== false
                            || stripos($matchedTx['transaction_content'] ?? '', 'MUAGOI') !== false;

                if (!$isMuaGoi) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Giao dịch không phải lệnh mua gói đối tác.',
                    ]);
                }

                // ── Tra cứu gói dịch vụ gốc ─────────────────────────────────────
                $goi = GoiDichVu::where('gia_ca', $goiAmount)->first();

                $tenGoi         = $goi ? $goi->ten_goi         : 'Gói Đối Tác Quản Trị Gia Phả';
                $thoiHan        = $goi ? (int) $goi->thoi_han  : 12;
                $featuresOfGoi  = $goi ? $goi->features        : '';
                $maxDoi         = $goi ? (int) $goi->max_doi        : 5;
                $maxThanhVien   = $goi ? (int) $goi->max_thanh_vien : 100;
                $goiId          = $goi ? $goi->id              : null;

                $ngayBatDau  = now()->toDateString();
                $ngayKetThuc = now()->addMonths($thoiHan)->toDateString();

                // ── Logic mua nhiều gói: cho phép mua thêm gói KHÁC ────────────
                // Nếu cùng gói đã mua và còn hiệu lực → cộng dồn thời gian
                $existingSamePackage = null;
                if ($goiId) {
                    $existingSamePackage = DoiTac::where('id_nguoi_dung', $nguoiDungId)
                        ->where('trang_thai', 'APPROVED')
                        ->where('id_goi_dich_vu', $goiId)
                        ->where('ngay_ket_thuc', '>=', now()->toDateString())
                        ->first();
                }

                if ($existingSamePackage) {
                    // Cộng dồn tháng vào gói cũ
                    $newExpiry = Carbon::parse($existingSamePackage->ngay_ket_thuc)
                        ->addMonths($thoiHan)
                        ->toDateString();

                    $existingSamePackage->update([
                        'ngay_ket_thuc' => $newExpiry,
                        'so_tien'       => $existingSamePackage->so_tien + $amountIn,
                        // Cập nhật features nếu gói gốc có thay đổi
                        'features'      => $featuresOfGoi,
                        'max_doi'       => $maxDoi,
                        'max_thanh_vien'=> $maxThanhVien,
                    ]);

                    $targetRecord   = $existingSamePackage;
                    $actionMessage  = "Gia hạn gói cùng loại — cộng thêm {$thoiHan} tháng";

                } else {
                    // Kiểm tra có bản ghi PENDING nào cho cùng gói không → nâng cấp
                    $pendingRecord = DoiTac::where('id_nguoi_dung', $nguoiDungId)
                        ->where('trang_thai', 'PENDING')
                        ->where(function ($q) use ($goiId) {
                            if ($goiId) $q->where('id_goi_dich_vu', $goiId);
                            else $q->whereNull('id_goi_dich_vu');
                        })
                        ->first();

                    if ($pendingRecord) {
                        $pendingRecord->update([
                            'trang_thai'     => 'APPROVED',
                            'so_tien'        => $amountIn,
                            'ten_goi'        => $tenGoi,
                            'id_goi_dich_vu' => $goiId,
                            'features'       => $featuresOfGoi,
                            'max_doi'        => $maxDoi,
                            'max_thanh_vien' => $maxThanhVien,
                            'ngay_bat_dau'   => $ngayBatDau,
                            'ngay_ket_thuc'  => $ngayKetThuc,
                        ]);
                        $targetRecord   = $pendingRecord;
                        $actionMessage  = "Tự động duyệt gói từ PENDING";
                    } else {
                        // Tạo mới bản ghi APPROVED — cho phép user có nhiều gói
                        $targetRecord = DoiTac::create([
                            'id_nguoi_dung'  => $nguoiDungId,
                            'id_goi_dich_vu' => $goiId,
                            'ten_goi'        => $tenGoi,
                            'features'       => $featuresOfGoi,
                            'max_doi'        => $maxDoi,
                            'max_thanh_vien' => $maxThanhVien,
                            'so_tien'        => $amountIn,
                            'ngay_bat_dau'   => $ngayBatDau,
                            'ngay_ket_thuc'  => $ngayKetThuc,
                            'trang_thai'     => 'APPROVED',
                        ]);
                        $actionMessage = "Kích hoạt gói mới";
                    }
                }

                // ── Model booted() đã tự sync is_doi_tac = 1 ──────────────────

                // ── Ghi nhật ký ───────────────────────────────────────────────
                NhatKyHoatDong::ghiLog(
                    sprintf(
                        '%s cho người dùng #%d (%s) — Gói: %s — Số tiền: %s VNĐ — HH: %s',
                        $actionMessage,
                        $nguoiDungId,
                        $user->ho_ten,
                        $tenGoi,
                        number_format($amountIn, 0, ',', '.'),
                        $targetRecord->ngay_ket_thuc
                    ),
                    [
                        'id_nguoi_dung'  => $nguoiDungId,
                        'id_doi_tac'     => $targetRecord->id,
                        'ten_goi'        => $tenGoi,
                        'so_tien'        => $amountIn,
                        'ngay_bat_dau'   => $ngayBatDau,
                        'ngay_ket_thuc'  => $targetRecord->ngay_ket_thuc,
                        'auto_approved'  => true,
                        'transaction_id' => $matchedTx['id'] ?? null,
                    ],
                    'Thành công',
                    null
                );

                return response()->json([
                    'success'       => true,
                    'is_partner'    => true,
                    'auto_approved' => true,
                    'message'       => sprintf(
                        'Chúc mừng! Thanh toán %s VNĐ đã được xác nhận. %s thành công!',
                        number_format($amountIn, 0, ',', '.'),
                        $existingSamePackage ? 'Gia hạn gói' : 'Kích hoạt tài khoản đối tác'
                    ),
                    'ten_goi'       => $tenGoi,
                    'ngay_ket_thuc' => $targetRecord->ngay_ket_thuc,
                    'redirect_url'  => '/doi-tac/dashboard',
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Lỗi paymentVerify: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi máy chủ: ' . $e->getMessage(),
            ]);
        }
    }
}
