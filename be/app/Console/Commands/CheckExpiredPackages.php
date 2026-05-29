<?php
namespace App\Console\Commands;

use App\Models\DoiTac;
use App\Models\NguoiDung;
use App\Models\NhatKyHoatDong;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckExpiredPackages extends Command
{
    protected $signature = 'cron:check-expired-packages';
    protected $description = 'Kiểm tra và tự động cập nhật trạng thái các gói đối tác đã hết hạn';

    public function handle(): int
    {
        $today = now()->toDateString();
        $expiredCount = 0;
        $syncCount = 0;

        try {
            DB::transaction(function () use ($today, &$expiredCount, &$syncCount) {
                // Batch update: đổi APPROVED -> EXPIRED nếu quá hạn
                $expiredCount = DoiTac::where('trang_thai', 'APPROVED')
                    ->whereNotNull('ngay_ket_thuc')
                    ->where('ngay_ket_thuc', '<', $today)
                    ->update(['trang_thai' => 'EXPIRED']);

                // Đồng bộ is_doi_tac cho những user bị ảnh hưởng
                $affectedUserIds = DoiTac::where('trang_thai', 'EXPIRED')
                    ->whereNotNull('ngay_ket_thuc')
                    ->where('ngay_ket_thuc', '<', $today)
                    ->pluck('id_nguoi_dung')
                    ->unique();

                foreach ($affectedUserIds as $userId) {
                    DoiTac::syncUserPartnerStatus($userId);
                    $syncCount++;
                }
            });

            $this->info("✓ Đã cập nhật {$expiredCount} gói hết hạn. Đồng bộ {$syncCount} tài khoản.");
            Log::info('CheckExpiredPackages: expired=' . $expiredCount . ' synced=' . $syncCount);
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Lỗi cron: ' . $e->getMessage());
            Log::error('CheckExpiredPackages error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
