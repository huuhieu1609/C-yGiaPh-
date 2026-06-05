<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class NguoiDung extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes;

    protected $table = "nguoi_dungs";
    protected $fillable = ["ho_ten", "email", "mat_khau", "so_dien_thoai", "avatar", "vai_tro", "id_chuc_vu", "trang_thai", "hash_reset", "is_doi_tac", "chi_nhanh_id"];
    protected $hidden = ["mat_khau", "remember_token"];
    public function getAuthPassword() { return $this->mat_khau; }
    
    public function nhatKyHoatDongs() { return $this->hasMany(NhatKyHoatDong::class); }
    public function dongGops() { return $this->hasMany(DongGop::class); }
    public function doiTac() { return $this->hasOne(DoiTac::class, 'id_nguoi_dung'); }
    public function chiNhanh() { return $this->belongsTo(ChiNhanh::class, 'chi_nhanh_id'); }
    public function chucVu() { return $this->belongsTo(ChucVu::class, 'id_chuc_vu'); }

    public function isAdminOrSubAdmin()
    {
        if (strtolower(trim($this->vai_tro)) === 'admin' || $this->email === 'admin@master.com') {
            return true;
        }
        
        $chucVu = $this->chucVu;
        if ($chucVu) {
            $roleName = strtolower($chucVu->ten_chuc_vu);
            if (str_contains($roleName, 'quản trị') || str_contains($roleName, 'admin')) {
                return true;
            }
        }
        
        return false;
    }

}
