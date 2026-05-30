<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class DoiTac extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "doi_tacs";

    protected $fillable = [
        "id_nguoi_dung",
        "id_goi_dich_vu",
        "ten_goi",
        "features",
        "max_doi",
        "max_thanh_vien",
        "so_tien",
        "ngay_bat_dau",
        "ngay_ket_thuc",
        "trang_thai",
        "ly_do_tu_choi",
    ];

    protected $casts = [
        'ngay_bat_dau'  => 'date',
        'ngay_ket_thuc' => 'date',
    ];

    public static $isResequencing = false;

    // ── Relationships ──────────────────────────────────────────────────────────

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'id_nguoi_dung');
    }

    public function goiDichVu()
    {
        return $this->belongsTo(GoiDichVu::class, 'id_goi_dich_vu');
    }

    // ── Model Events: Tự động sync is_doi_tac ──────────────────────────────────

    protected static function booted()
    {
        static::created(function ($doiTac) {
            self::syncUserPartnerStatus($doiTac->id_nguoi_dung);
            self::resequencePackages($doiTac->id_nguoi_dung);
        });

        static::updated(function ($doiTac) {
            self::syncUserPartnerStatus($doiTac->id_nguoi_dung);
            self::resequencePackages($doiTac->id_nguoi_dung);
        });

        static::deleted(function ($doiTac) {
            self::syncUserPartnerStatus($doiTac->id_nguoi_dung);
            self::resequencePackages($doiTac->id_nguoi_dung);
        });
    }

    /**
     * Sắp xếp và nối chuỗi thời gian hoạt động của tất cả gói APPROVED của user.
     * Gói giàu tính năng nhất sẽ chạy trước, các gói sau nối đuôi tiếp theo.
     */
    public static function resequencePackages(int $userId): void
    {
        if (self::$isResequencing) return;
        self::$isResequencing = true;

        try {
            $packages = self::where('id_nguoi_dung', $userId)
                ->where('trang_thai', 'APPROVED')
                ->get();

            if ($packages->isEmpty()) return;

            // Sắp xếp theo số lượng tính năng giảm dần (gói nhiều tính năng nhất xếp trước)
            $sorted = $packages->sortByDesc(function ($pkg) {
                return count(self::parseFeatures($pkg->features));
            })->values();

            // Lấy ngày bắt đầu sớm nhất hiện có làm mốc khởi động, hoặc dùng hôm nay nếu chưa bắt đầu
            $earliestStart = null;
            foreach ($sorted as $pkg) {
                if ($pkg->ngay_bat_dau) {
                    $start = Carbon::parse($pkg->ngay_bat_dau);
                    if ($earliestStart === null || $start->lt($earliestStart)) {
                        $earliestStart = $start;
                    }
                }
            }

            if ($earliestStart === null || $earliestStart->gt(now())) {
                $currentStart = now()->startOfDay();
            } else {
                $currentStart = $earliestStart->startOfDay();
            }

            foreach ($sorted as $pkg) {
                // Xác định thời hạn (số tháng)
                $months = 12;
                if ($pkg->goiDichVu) {
                    $months = (int) $pkg->goiDichVu->thoi_han;
                } else {
                    if ($pkg->ngay_bat_dau && $pkg->ngay_ket_thuc) {
                        $months = Carbon::parse($pkg->ngay_bat_dau)->diffInMonths(Carbon::parse($pkg->ngay_ket_thuc));
                        if ($months <= 0) $months = 12;
                    }
                }

                $ngayBatDau = $currentStart->toDateString();
                $ngayKetThuc = $currentStart->copy()->addMonths($months)->toDateString();

                $pkg->update([
                    'ngay_bat_dau'  => $ngayBatDau,
                    'ngay_ket_thuc' => $ngayKetThuc,
                ]);

                // Gói tiếp theo bắt đầu ngay khi gói hiện tại kết thúc
                $currentStart = Carbon::parse($ngayKetThuc);
            }
        } finally {
            self::$isResequencing = false;
        }
    }

    /**
     * Đồng bộ trạng thái is_doi_tac cho người dùng
     * dựa trên việc có bất kỳ gói APPROVED còn hiệu lực không.
     */
    public static function syncUserPartnerStatus(int $userId): void
    {
        $user = NguoiDung::find($userId);
        if (!$user) return;

        $hasActive = self::activePackagesOf($userId)->exists();
        $user->update(['is_doi_tac' => $hasActive ? 1 : 0]);
    }

    // ── Query Scopes ───────────────────────────────────────────────────────────

    /**
     * Scope: lấy tất cả gói APPROVED còn hiệu lực của 1 user.
     * Xử lý cả trang_thai = 'APPROVED' (string mới) lẫn = 1 (integer cũ từ seeder).
     */
    public static function activePackagesOf(int $userId)
    {
        return self::where('id_nguoi_dung', $userId)
            ->where(function ($q) {
                $q->where('trang_thai', 'APPROVED')
                  ->orWhere('trang_thai', '1')
                  ->orWhere('trang_thai', 1);
            })
            ->where(function ($q) {
                $q->whereNull('ngay_ket_thuc')
                  ->orWhere('ngay_ket_thuc', '>=', now()->toDateString());
            });
    }

    // ── Effective Features & Limits ────────────────────────────────────────────

    /**
     * Trả về danh sách features UNION từ tất cả gói còn hiệu lực của user.
     * @return array<string>
     */
    public static function getEffectiveFeatures(int $userId): array
    {
        $packages = self::activePackagesOf($userId)->get();

        $allFeatures = [];
        foreach ($packages as $pkg) {
            $feats = self::parseFeatures($pkg->features);
            $allFeatures = array_unique(array_merge($allFeatures, $feats));
        }

        return array_values($allFeatures);
    }

    /**
     * Trả về giới hạn đời và thành viên tổng hợp (lấy MAX từ các gói còn hạn).
     * @return array{max_doi: int, max_thanh_vien: int}
     */
    public static function getEffectiveLimits(int $userId): array
    {
        $packages = self::activePackagesOf($userId)->get();

        $maxDoi        = 0;
        $maxThanhVien  = 0;

        foreach ($packages as $pkg) {
            $doi = (int) ($pkg->max_doi ?? 0);
            $tv  = (int) ($pkg->max_thanh_vien ?? 0);
            if ($doi > $maxDoi) $maxDoi = $doi;
            if ($tv > $maxThanhVien) $maxThanhVien = $tv;
        }

        return [
            'max_doi'         => $maxDoi,
            'max_thanh_vien'  => $maxThanhVien,
        ];
    }

    /**
     * Trả về ngày hết hạn SỚM NHẤT trong các gói còn hiệu lực.
     */
    public static function getEarliestExpiry(int $userId): ?string
    {
        $pkg = self::activePackagesOf($userId)
            ->whereNotNull('ngay_ket_thuc')
            ->orderBy('ngay_ket_thuc', 'asc')
            ->first();

        return $pkg ? $pkg->ngay_ket_thuc->toDateString() : null;
    }

    /**
     * Trả về ngày hết hạn MUỘN NHẤT trong các gói còn hiệu lực.
     */
    public static function getLatestExpiry(int $userId): ?string
    {
        $pkg = self::activePackagesOf($userId)
            ->whereNotNull('ngay_ket_thuc')
            ->orderBy('ngay_ket_thuc', 'desc')
            ->first();

        return $pkg ? $pkg->ngay_ket_thuc->toDateString() : null;
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Parse features từ string CSV hoặc array.
     * @return array<string>
     */
    public static function parseFeatures(mixed $features): array
    {
        if (empty($features)) return [];
        if (is_array($features)) return array_filter($features);
        return array_filter(array_map('trim', explode(',', $features)));
    }

    /**
     * Kiểm tra feature key có trong tập hợp features của gói này không.
     */
    public function hasFeature(string $key): bool
    {
        return in_array($key, self::parseFeatures($this->features));
    }

    /**
     * Tính số ngày còn lại đến hết hạn gói này.
     * @return int|null  null nếu không có ngay_ket_thuc (vô thời hạn)
     */
    public function getDaysRemaining(): ?int
    {
        if (!$this->ngay_ket_thuc) return null;
        $diff = now()->startOfDay()->diffInDays($this->ngay_ket_thuc->startOfDay(), false);
        return max(0, (int) $diff);
    }

    /**
     * Tính phần trăm thời gian còn lại so với tổng thời hạn gói.
     */
    public function getProgressPercent(): float
    {
        if (!$this->ngay_bat_dau || !$this->ngay_ket_thuc) return 100.0;

        $total   = $this->ngay_bat_dau->startOfDay()->diffInDays($this->ngay_ket_thuc->startOfDay());
        $elapsed = $this->ngay_bat_dau->startOfDay()->diffInDays(now()->startOfDay());

        if ($total <= 0) return 0.0;
        $pct = (($total - $elapsed) / $total) * 100;
        return max(0.0, min(100.0, round($pct, 2)));
    }
}
