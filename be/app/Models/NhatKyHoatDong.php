<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NhatKyHoatDong extends Model
{
    use HasFactory;

    protected $table = "nhat_ky_hoat_dongs";
    protected $fillable = ["nguoi_dung_id", "hanh_dong", "thoi_gian", "chi_tiet", "ip", "trang_thai"];
    
    public function nguoiDung() { return $this->belongsTo(NguoiDung::class); }

    public static function ghiLog($hanhDong, $chiTiet = null, $trangThai = 'Thành công', $userId = null)
    {
        if (!$userId) {
            $user = auth('sanctum')->user();
            $userId = $user ? $user->id : null;
        }

        $chiTietStr = null;
        if ($chiTiet !== null) {
            if (is_array($chiTiet) || is_object($chiTiet)) {
                $chiTietStr = json_encode($chiTiet, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            } else {
                $chiTietStr = (string)$chiTiet;
            }
        }

        return self::create([
            'nguoi_dung_id' => $userId,
            'hanh_dong'     => $hanhDong,
            'thoi_gian'     => now(),
            'chi_tiet'      => $chiTietStr,
            'ip'            => request()->ip() ?: '127.0.0.1',
            'trang_thai'    => $trangThai,
        ]);
    }


}
