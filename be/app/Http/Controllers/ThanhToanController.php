<?php

namespace App\Http\Controllers;

use App\Models\DoiTac;
use App\Models\DongGop;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
            $noiDung = $request->input('noi_dung'); // VD: "MUAGOI HIEU | Số tiền: ..."

            if (! $nguoiDungId || ! $noiDung) {
                return response()->json(['success' => false, 'message' => 'Thiếu thông tin người dùng hoặc nội dung']);
            }

            // Lấy mã tìm kiếm, ví dụ "MUAGOI HIEU"
            $parts = explode('|', $noiDung);
            $expectedContent = trim($parts[0]);

            $apiToken = env('SEPAY_API_TOKEN');

            // Gọi API SePay để quét 20 giao dịch gần nhất
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiToken,
            ])->get('https://my.sepay.vn/userapi/transactions/list', [
                'limit' => 20,
            ]);

            if ($response->successful()) {
                $transactions = $response->json('transactions') ?? [];
                $matchedTx = null;

                foreach ($transactions as $tx) {
                    $amountIn = (float) ($tx['amount_in'] ?? $tx['amount'] ?? 0);
                    $isIn = (isset($tx['transaction_type']) && $tx['transaction_type'] === 'in') 
                            || ($amountIn > 0);
                    
                    if (!$isIn || $amountIn <= 0) continue;

                    $bankContent = strtoupper($tx['transaction_content'] ?? '');

                    // CHỈ khớp khi nội dung chứa CHÍNH XÁC chuỗi expectedContent (gồm tên + mã số 5 chữ số)
                    // Ví dụ: "MUAGOI HIEU97995" phải xuất hiện trong nội dung chuyển khoản
                    if (stripos($bankContent, strtoupper($expectedContent)) === false) continue;

                    // Giao dịch phải trong vòng 24 giờ gần nhất (tránh khớp giao dịch cũ)
                    $txTime = $tx['transaction_date'] ?? $tx['when'] ?? null;
                    if ($txTime) {
                        try {
                            $txCarbon = \Carbon\Carbon::parse($txTime);
                            if ($txCarbon->diffInHours(now()) > 24) continue;
                        } catch (\Exception $e) {
                            // Nếu không parse được thời gian, bỏ qua kiểm tra
                        }
                    }

                    $matchedTx = $tx;
                    $matchedTx['amount_in'] = $amountIn;
                    break;
                }

                if ($matchedTx) {
                    $amountIn = $matchedTx['amount_in'];

                    // CHỐNG DUPLICATE: Nếu tài khoản đã là đối tác rồi thì trả về thông báo thành công và dừng
                    $user = \App\Models\NguoiDung::find($nguoiDungId);
                    if ($user && (int)$user->is_doi_tac === 1) {
                        return response()->json([
                            'success' => true,
                            'is_partner' => true,
                            'message' => 'Tài khoản của bạn đã được kích hoạt Đối Tác thành công!',
                            'redirect_url' => '/doi-tac/dashboard'
                        ]);
                    }

                    // 1. Trích xuất giá gói cần mua
                    $goiAmount = (float) $request->input('so_tien', 0);
                    if ($goiAmount <= 0) {
                        if (preg_match('/Mua gói dịch vụ:\s*([\d\.,]+)/i', $noiDung, $matches)) {
                            $goiAmount = (float) str_replace([',', '.'], '', $matches[1]);
                        }
                    }

                    // 2. Kiểm tra nếu khách chuyển thiếu tiền (Chuyển khoản thật < Giá gói)
                    if ($goiAmount > 0 && $amountIn < $goiAmount) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Hệ thống ghi nhận bạn đã chuyển khoản số tiền ' . number_format($amountIn) . ' VNĐ. Tuy nhiên, giá trị gói đăng ký là ' . number_format($goiAmount) . ' VNĐ. Vui lòng thực hiện chuyển khoản thêm phần còn thiếu để xác nhận!'
                        ], 400);
                    }

                    $dongGop = DongGop::firstOrCreate(
                        ['nguoi_dung_id' => $nguoiDungId, 'noi_dung' => $noiDung],
                        ['trang_thai' => 'Chờ duyệt']
                    );
                    $dongGop->update(['trang_thai' => 'Chờ duyệt']);

                    // 3. Ghi nhận yêu cầu nâng cấp gói đối tác ở trạng thái PENDING
                    $isMuaGoi = (stripos($expectedContent, 'MUAGOI') !== false) || 
                                 (stripos($matchedTx['transaction_content'] ?? '', 'MUAGOI') !== false);
                    
                    if ($isMuaGoi) {
                        // Xác thực gói cước theo giá gói ban đầu
                        $goi = \App\Models\GoiDichVu::where('gia_ca', $goiAmount)->first();
                        $tenGoi = $goi ? $goi->ten_goi : 'Gói Đối Tác Quản Trị Gia Phả';

                        // Chống duplicate: Kiểm tra xem đã có yêu cầu PENDING tương tự cho user chưa
                        $pendingRequest = DoiTac::where('id_nguoi_dung', $nguoiDungId)
                            ->where('trang_thai', 'PENDING')
                            ->first();

                        if (!$pendingRequest) {
                            DoiTac::create([
                                'id_nguoi_dung' => $nguoiDungId,
                                'ten_goi' => $tenGoi,
                                'so_tien' => $amountIn, // Lưu số tiền thực nhận
                                'ngay_bat_dau' => null,
                                'ngay_ket_thuc' => null,
                                'trang_thai' => 'PENDING',
                            ]);
                        }
                    }

                    return response()->json([
                        'success' => true, 
                        'message' => 'Ghi nhận giao dịch chuyển khoản thành công! Yêu cầu mua gói của bạn đang ở trạng thái CHỜ DUYỆT. Admin sẽ sớm xác thực và kích hoạt tài khoản.',
                        'redirect_url' => '/thanh-toan'
                    ]);
                }
            }

            return response()->json(['success' => false, 'message' => 'Chưa tìm thấy giao dịch chuyển khoản. Vui lòng kiểm tra lại.']);

        } catch (\Exception $e) {
            Log::error('Lỗi paymentVerify: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Lỗi máy chủ: '.$e->getMessage()]);
        }
    }
}
