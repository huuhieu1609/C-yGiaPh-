<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class DoiTac extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $table = "doi_tacs";
 
    protected $fillable = [
        "id_nguoi_dung",
        "ten_goi",
        "so_tien",
        "ngay_bat_dau",
        "ngay_ket_thuc",
        "trang_thai",
        "ly_do_tu_choi"
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'id_nguoi_dung');
    }

    protected static function booted()
    {
        static::created(function ($doiTac) {
            self::syncUserPartnerStatus($doiTac->id_nguoi_dung);
        });

        static::updated(function ($doiTac) {
            self::syncUserPartnerStatus($doiTac->id_nguoi_dung);
        });

        static::deleted(function ($doiTac) {
            self::syncUserPartnerStatus($doiTac->id_nguoi_dung);
        });
    }

    public static function syncUserPartnerStatus($userId)
    {
        $user = NguoiDung::find($userId);
        if ($user) {
            // Kiểm tra xem người dùng có gói nào ở trạng thái APPROVED và chưa hết hạn không
            $hasActive = self::where('id_nguoi_dung', $userId)
                ->where('trang_thai', 'APPROVED')
                ->where(function($query) {
                    $query->whereNull('ngay_ket_thuc')
                          ->orWhere('ngay_ket_thuc', '>=', now()->toDateString());
                })
                ->exists();
            
            $user->update(['is_doi_tac' => $hasActive ? 1 : 0]);
        }
    }
}
