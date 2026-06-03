<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendAnniversaryReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:send-anniversary-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan and send email/Zalo reminders for family events and Lunar death anniversaries 3 days in advance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Scanning anniversaries and events occurring 3 days from now...");

        // Calculate Solar date 3 days from now
        $targetSolar = now()->addDays(3);
        $solarDateStr = $targetSolar->toDateString();
        
        // Convert to Lunar Date
        list($lDay, $lMonth, $lYear, $isLeap) = \App\Utils\LunarHelper::solarToLunar(
            (int)$targetSolar->format('Y'),
            (int)$targetSolar->format('m'),
            (int)$targetSolar->format('d')
        );

        $this->info("Target Solar Date: {$solarDateStr}");
        $this->info("Target Lunar Date: Ngày {$lDay} tháng {$lMonth} " . ($isLeap ? '(Nhuận) ' : '') . "năm {$lYear}");

        $reminders = [];

        // 1. Query manual solar-based events
        $solarEvents = \App\Models\SuKien::where('is_lunar', false)
            ->whereDate('ngay_to_chuc', $solarDateStr)
            ->get();

        foreach ($solarEvents as $event) {
            $reminders[$event->chi_nhanh_id][] = [
                'tieu_de' => $event->tieu_de,
                'loai' => $event->loai,
                'thoi_gian' => $targetSolar->format('d/m/Y H:i'),
                'dia_diem' => $event->dia_diem,
                'noi_dung' => $event->noi_dung,
            ];
        }

        // 2. Query manual lunar-based events
        $lunarEvents = \App\Models\SuKien::where('is_lunar', true)
            ->where('ngay_al_ngay', $lDay)
            ->where('ngay_al_thang', $lMonth)
            ->get();

        foreach ($lunarEvents as $event) {
            $reminders[$event->chi_nhanh_id][] = [
                'tieu_de' => $event->tieu_de,
                'loai' => $event->loai,
                'thoi_gian' => $targetSolar->format('d/m/Y') . " (Tức ngày {$lDay}/{$lMonth} Âm lịch)",
                'dia_diem' => $event->dia_diem,
                'noi_dung' => $event->noi_dung,
            ];
        }

        // 3. Query deceased family member anniversaries (Giỗ chạp)
        $deceased = \DB::table('thanh_viens')
            ->where('trang_thai', 'Đã mất')
            ->where('ngay_mat_al_ngay', $lDay)
            ->where('ngay_mat_al_thang', $lMonth)
            ->get();

        foreach ($deceased as $member) {
            $yearsPassed = $member->ngay_mat_al_nam ? ($lYear - $member->ngay_mat_al_nam) : 0;
            $title = "Giỗ chạp cụ/ông/bà " . $member->ho_ten;
            if ($yearsPassed > 0) {
                $title .= " (Lần thứ {$yearsPassed})";
            }

            $reminders[$member->chi_nhanh_id][] = [
                'tieu_de' => $title,
                'loai' => 'Giỗ chạp',
                'thoi_gian' => $targetSolar->format('d/m/Y') . " (Tức ngày {$lDay}/{$lMonth} Âm lịch)",
                'dia_diem' => $member->ghi_chu ? 'Theo thông tin dòng họ' : '',
                'noi_dung' => "Kính mời quý bà con tề tựu tại nhà thờ tổ hoặc gia đình để thắp hương, làm lễ tưởng nhớ ngày mất của cụ/ông/bà {$member->ho_ten}.",
            ];
        }

        // Send notifications grouped by branch
        foreach ($reminders as $branchId => $eventsList) {
            if (!$branchId) continue;

            // Fetch emails associated with this branch
            $userEmails = \DB::table('nguoi_dungs')
                ->where('chi_nhanh_id', $branchId)
                ->whereNotNull('email')
                ->pluck('email')
                ->toArray();

            $memberEmails = \DB::table('thanh_viens')
                ->where('chi_nhanh_id', $branchId)
                ->whereNotNull('email')
                ->pluck('email')
                ->toArray();

            $emails = array_unique(array_merge($userEmails, $memberEmails));

            if (empty($emails)) {
                $this->info("No emails registered for branch ID: {$branchId}. Skipping.");
                continue;
            }

            foreach ($eventsList as $eventData) {
                $this->info("Sending email for '{$eventData['tieu_de']}' to " . count($emails) . " recipients...");

                // Send email
                foreach ($emails as $email) {
                    try {
                        \Mail::to($email)->send(new \App\Mail\AnniversaryReminderMail($eventData));
                        $this->info(" - Sent to {$email}");
                    } catch (\Exception $e) {
                        $this->error(" - Failed sending to {$email}: " . $e->getMessage());
                    }
                }

                // Log Zalo mock notification
                \Log::info("Mock Zalo notification sent to branch {$branchId} for event: '{$eventData['tieu_de']}'.");
            }
        }

        $this->info("Finished sending reminders!");
    }
}
