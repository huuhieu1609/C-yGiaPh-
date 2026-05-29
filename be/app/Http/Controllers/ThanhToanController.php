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
    public function index()
    {
        try {
            $apiToken = env('SEPAY_API_TOKEN');
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiToken,
            ])->get('https://my.sepay.vn/userapi/transactions/list');

            if ($response->successful()) {
                return response()->json([
                    'status' => true,
                    'data' => $response->json('transactions'),
                ]);
            }

            return response()->json(['status' => false, 'message' => 'Lỗi kết nối API SePay']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function paymentVerify(Request $request)
    {
        try {
            $nguoiDungId = $request->input('nguoi_dung_id');
            $noiDung     = $request->input('noi_dung');

            if (!$nguoiDungId || !$noiDung) {
                return response()->json(['success' => false, 'message' => 'Thiếu thông tin người dùng hoặc nội dung']);
            }

            // Trích xuất phần nội dung tìm kiếm, ví dụ: "MUAGOI HIEU97995"
            $parts           = explode('|', $noiDung);
            $expectedContent = trim($parts[0]);

            $apiToken = env('SEPAY_API_TOKEN');

            // Quét 20 giao dịch gần nhất từ SePay
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiToken,
            ])->get('https://my.sepay.vn/userapi/transactions/list', ['limit' => 20]);

            if (!$response->successful()) {
                return response()->json(['success' => false, 'message' => 'Lỗi kết nối cổng thanh toán SePay.']);
            }

            $transactions = $response->json('transactions') ?? [];
            $matchedTx    = null;

            foreach ($transactions as $tx) {
                $amountIn = (float) ($tx['amount_in'] ?? $tx['amount'] ?? 0);
                $isIn     = (isset($tx['transaction_type']) && $tx['transaction_type'] === 'in') || ($amountIn > 0);

                if (!$isIn || $amountIn <= 0) continue;

                $bankContent = strtoupper($tx['transaction_content'] ?? '');

                if (stripos($bankContent, strtoupper($expectedContent)) === false) continue;

                // Giao dịch phải trong vòng 24 giờ gần nhất
                $txTime = $tx['transaction_date'] ?? $tx['when'] ?? null;
                if ($txTime) {
                    try {
                        $txCarbon = Carbon::parse($txTime);
                        if ($txCarbon->diffInHours(now()) > 24) continue;
                    } catch (\Exception $e) {
                        // Nếu không parse được thời gian, bỏ qua kiểm tra
                    }
                }

                $matchedTx             = $tx;
                $matchedTx['amount_in'] = $amountIn;
                break;
            }

            if (!$matchedTx) {
                return response()->json(['success' => false, 'message' => 'Chưa tìm thấy giao dịch chuyển khoản. Vui lòng đảm bảo đúng nội dung và thử lại sau.']);
            }

            // Giao dịch khớp — bắt đầu xử lý tự động
            return DB::transaction(function () use ($matchedTx, $nguoiDungId, $noiDung, $expectedContent, $request) {

                $amountIn = $matchedTx['amount_in'];

                // ── CHỐNG DUPLICATE: Nếu tài khoản đã là đối tác rồi ──────────────────
                $user = NguoiDung::find($nguoiDungId);
                if (!$user) {
                    return response()->json(['success' => false, 'message' => 'Không tìm thấy tài khoản người dùng.']);
                }

                if ((int) $user->is_doi_tac === 1) {
                    return response()->json([
                        'success'      => true,
                        'is_partner'   => true,
                        'message'      => 'Tài khoản của bạn đã được kích hoạt Đối Tác thành công!',
                        'redirect_url' => '/doi-tac/dashboard',
                    ]);
                }

                // ── Trích xuất giá gói cần mua ────────────────────────────────────────
                $goiAmount = (float) $request->input('so_tien', 0);
                if ($goiAmount <= 0) {
                    if (preg_match('/Mua gói dịch vụ:\s*([\d\.,]+)/i', $noiDung, $matches)) {
                        $goiAmount = (float) str_replace([',', '.'], '', $matches[1]);
                    }
                }

                // ── Kiểm tra thiếu tiền ───────────────────────────────────────────────
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

                // ── Xác định tên gói và thời hạn ─────────────────────────────────────
                $isMuaGoi = stripos($expectedContent, 'MUAGOI') !== false
                            || stripos($matchedTx['transaction_content'] ?? '', 'MUAGOI') !== false;

                if (!$isMuaGoi) {
                    return response()->json(['success' => false, 'message' => 'Giao dịch không phải lệnh mua gói đối tác.']);
                }

                $goi       = GoiDichVu::where('gia_ca', $goiAmount)->first();
                $tenGoi    = $goi ? $goi->ten_goi : 'Gói Đối Tác Quản Trị Gia Phả';
                $thoiHan   = $goi ? (int) $goi->thoi_han : 12; // Mặc định 12 tháng

                $ngayBatDau  = now()->toDateString();
                $ngayKetThuc = now()->addMonths($thoiHan)->toDateString();

                // ── Chống duplicate PENDING ───────────────────────────────────────────
                $existingPending = DoiTac::where('id_nguoi_dung', $nguoiDungId)
                    ->where('trang_thai', 'PENDING')
                    ->first();
                if ($existingPending) {
                    // Nâng cấp bản ghi PENDING sẵn có lên APPROVED
                    $existingPending->update([
                        'trang_thai'   => 'APPROVED',
                        'so_tien'      => $amountIn,
                        'ten_goi'      => $tenGoi,
                        'ngay_bat_dau' => $ngayBatDau,
                        'ngay_ket_thuc'=> $ngayKetThuc,
                    ]);
                } else {
                    // Tạo mới bản ghi APPROVED ngay lập tức
                    DoiTac::create([
                        'id_nguoi_dung' => $nguoiDungId,
                        'ten_goi'       => $tenGoi,
                        'so_tien'       => $amountIn,
                        'ngay_bat_dau'  => $ngayBatDau,
                        'ngay_ket_thuc' => $ngayKetThuc,
                        'trang_thai'    => 'APPROVED',
                    ]);
                }

                // Model DoiTac::booted() listener tự động set is_doi_tac = 1 cho NguoiDung

                // ── Ghi nhật ký hoạt động ─────────────────────────────────────────────
                NhatKyHoatDong::ghiLog(
                    sprintf(
                        'Tự động kích hoạt đối tác cho người dùng #%d (%s) — Gói: %s — Số tiền: %s VNĐ',
                        $nguoiDungId,
                        $user->ho_ten,
                        $tenGoi,
                        number_format($amountIn, 0, ',', '.')
                    ),
                    [
                        'id_nguoi_dung'  => $nguoiDungId,
                        'ten_goi'        => $tenGoi,
                        'so_tien'        => $amountIn,
                        'ngay_bat_dau'   => $ngayBatDau,
                        'ngay_ket_thuc'  => $ngayKetThuc,
                        'auto_approved'  => true,
                        'transaction_id' => $matchedTx['id'] ?? null,
                    ],
                    'Thành công',
                    null // Không có admin_id vì là tự động
                );

                return response()->json([
                    'success'      => true,
                    'is_partner'   => true,
                    'auto_approved'=> true,
                    'message'      => sprintf(
                        'Chúc mừng! Thanh toán %s VNĐ đã được xác nhận thành công. Tài khoản đối tác của bạn đã được kích hoạt ngay lập tức!',
                        number_format($amountIn, 0, ',', '.')
                    ),
                    'ten_goi'      => $tenGoi,
                    'ngay_ket_thuc'=> $ngayKetThuc,
                    'redirect_url' => '/doi-tac/dashboard',
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Lỗi paymentVerify: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi máy chủ: ' . $e->getMessage()]);
        }
    }
}
