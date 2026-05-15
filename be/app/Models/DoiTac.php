<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
 
class DoiTac extends Model
{
    use HasFactory;
 
    protected $table = "doi_tacs";
 
    protected $fillable = [
        "id_nguoi_dung",
        "ten_goi",
        "so_tien",
        "ngay_bat_dau",
        "ngay_ket_thuc",
        "trang_thai"
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'id_nguoi_dung');
    }

    protected static function booted()
    {
        static::created(function ($doiTac) {
            $user = $doiTac->nguoiDung;
            if ($user) {
                $user->update(['is_doi_tac' => 1]);
            }
        });
    }
}
