<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiNhanh extends Model
{
    use HasFactory;

    protected $table = 'chi_nhanhs';

    // Cần đảm bảo database bảng chi_nhanhs có cột 'id_nguoi_dung' để kiểm tra quyền sở hữu
    protected $fillable = [
        'ten_chi', 'mo_ta', 'id_nguoi_dung'
    ];

    // Một chi nhánh có nhiều thành viên
    public function thanhViens()
    {
        return $this->hasMany(ThanhVien::class, 'chi_nhanh_id', 'id');
    }

    public function nguoiDungsDuocPhep()
    {
        return $this->belongsToMany(NguoiDung::class, 'chi_nhanh_nguoi_dung', 'chi_nhanh_id', 'nguoi_dung_id');
    }
}