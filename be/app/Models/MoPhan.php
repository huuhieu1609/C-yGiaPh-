<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MoPhan extends Model
{
    use HasFactory;

    protected $table = "mo_phans";
    protected $fillable = [
        "thanh_vien_id",
        "ten_mo",
        "dia_chi",
        "khu",
        "hang",
        "so_mo",
        "huong_mo",
        "ten_nghia_trang",
        "kinh_do",
        "vi_do",
        "hinh_anh",
        "ghi_chu",
    ];

    public function thanhVien()
    {
        return $this->belongsTo(ThanhVien::class);
    }

    /**
     * Scope: lọc mộ phần gần tọa độ (vi_do=lat, kinh_do=lng) trong bán kính radius km
     * Haversine formula (MySQL compatible)
     */
    public function scopeNearby($query, float $lat, float $lng, float $radiusKm = 10)
    {
        return $query->selectRaw("
                mo_phans.*,
                (
                    6371 * ACOS(
                        LEAST(1.0,
                            COS(RADIANS(?)) * COS(RADIANS(CAST(vi_do AS DECIMAL(10,7)))) *
                            COS(RADIANS(CAST(kinh_do AS DECIMAL(10,7))) - RADIANS(?)) +
                            SIN(RADIANS(?)) * SIN(RADIANS(CAST(vi_do AS DECIMAL(10,7))))
                        )
                    )
                ) AS distance_km
            ", [$lat, $lng, $lat])
            ->whereNotNull('vi_do')
            ->whereNotNull('kinh_do')
            ->where('vi_do', '!=', '')
            ->where('kinh_do', '!=', '')
            ->havingRaw('distance_km <= ?', [$radiusKm])
            ->orderBy('distance_km', 'asc');
    }
}
