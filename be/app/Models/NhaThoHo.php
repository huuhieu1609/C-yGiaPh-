<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NhaThoHo extends Model
{
    use HasFactory;

    protected $table = "nha_tho_hos";
    protected $fillable = ["ten_nha_tho", "dia_chi", "hinh_anh", "mo_ta", "chi_nhanh_id", "id_chi_nhanh", "kinh_do", "vi_do"];

    protected $appends = ['id_chi_nhanh'];

    public function chiNhanh()
    {
        return $this->belongsTo(ChiNhanh::class, 'chi_nhanh_id');
    }

    public function getIdChiNhanhAttribute()
    {
        return $this->attributes['chi_nhanh_id'] ?? null;
    }

    public function setIdChiNhanhAttribute($value)
    {
        $this->attributes['chi_nhanh_id'] = $value;
    }

}
