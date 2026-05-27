<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoiDichVu extends Model
{
    use HasFactory;

    protected $table = 'goi_dich_vus';

    protected $fillable = [
        'ten_goi',
        'gia_ca',
        'thoi_han',
        'max_doi',
        'max_thanh_vien',
        'mo_ta',
        'trang_thai'
    ];
}
