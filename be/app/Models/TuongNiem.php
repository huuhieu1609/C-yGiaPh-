<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TuongNiem extends Model
{
    use HasFactory;

    protected $table = 'tuong_niems';

    protected $fillable = [
        'thanh_vien_id',
        'nguoi_dung_id',
        'ho_ten_nguoi_gui',
        'loai_le_vat',
        'loi_nhan'
    ];

    public function thanhVien()
    {
        return $this->belongsTo(ThanhVien::class, 'thanh_vien_id');
    }

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }
}
