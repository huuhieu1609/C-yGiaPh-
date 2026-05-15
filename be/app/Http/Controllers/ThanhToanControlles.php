<?php

namespace App\Http\Controllers;

use App\Models\DoiTac;
use App\Models\DongGop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ThanhToanControlles extends Controller
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

            // Gọi API SePay để quét 20 giao dịch gần nhất
            $apiToken = env('SEPAY_API_TOKEN');
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiToken,
            ])->get('https://my.sepay.vn/userapi/transactions/list', [
                'limit' => 20,
            ]);

            if ($response->successful()) {
                $transactions = $response->json('transactions') ?? [];
                $matchedTx = null;

                foreach ($transactions as $tx) {
                    $isIn = (isset($tx['transaction_type']) && $tx['transaction_type'] === 'in') 
                            || (isset($tx['amount_in']) && $tx['amount_in'] > 0);
                    
                    if ($isIn) {
                        $bankContent = strtoupper($tx['transaction_content'] ?? '');
                        // Kiểm tra nếu nội dung chuyển khoản có chứa mã của mình
                        if (stripos($bankContent, $expectedContent) !== false) {
                            $matchedTx = $tx;
                            break;
                        }
                    }
                }

                if ($matchedTx) {
                    $amountIn = $matchedTx['amount_in'];

                    $dongGop = DongGop::firstOrCreate(
                        ['nguoi_dung_id' => $nguoiDungId, 'noi_dung' => $noiDung],
                        ['trang_thai' => 'Đã duyệt']
                    );
                    $dongGop->update(['trang_thai' => 'Đã duyệt']);

                    // 2. Tự động kích hoạt/cộng dồn Gói Đối Tác
                    $doiTac = DoiTac::where('id_nguoi_dung', $nguoiDungId)
                        ->where('trang_thai', 1)
                        ->first();
                    if ($doiTac) {
                        $doiTac->so_tien += $amountIn;
                        $doiTac->ngay_ket_thuc = Carbon::parse($doiTac->ngay_ket_thuc)->addYear();
                        $doiTac->save();
                    } else {
                        DoiTac::create([
                            'id_nguoi_dung' => $nguoiDungId,
                            'ten_goi' => 'Gói Đối Tác',
                            'so_tien' => $amountIn,
                            'ngay_bat_dau' => now(),
                            'ngay_ket_thuc' => now()->addYear(),
                            'trang_thai' => 1,
                        ]);
                    }

                    return response()->json(['success' => true, 'message' => 'Xác nhận thành công!']);
                }
            }

            return response()->json(['success' => false, 'message' => 'Chưa tìm thấy giao dịch chuyển khoản. Vui lòng kiểm tra lại.']);

        } catch (\Exception $e) {
            Log::error('Lỗi paymentVerify: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Lỗi máy chủ: '.$e->getMessage()]);
        }
    }
}
