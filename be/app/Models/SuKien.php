<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuKien extends Model
{
    use HasFactory;

    protected $table = "su_kiens";
    protected $fillable = ["tieu_de", "noi_dung", "ngay_to_chuc", "dia_diem", "loai", "chi_nhanh_id"];

    public function thamGiaSuKiens()
    {
        return $this->hasMany(ThamGiaSuKien::class);
    }

    public function chiNhanh()
    {
        return $this->belongsTo(\App\Models\ChiNhanh::class, 'chi_nhanh_id');
    }

}
