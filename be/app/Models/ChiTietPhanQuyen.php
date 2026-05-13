<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietPhanQuyen extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_phan_quyens';

    protected $fillable = [
        'chuc_vu_id',
        'chuc_nang_id',
    ];
}
