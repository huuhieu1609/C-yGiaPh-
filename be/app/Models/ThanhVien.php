<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanhVien extends Model
{
    use HasFactory;

    protected $table = 'thanh_viens';

    protected $fillable = [
        'id_chi_nhanh',
        'chi_nhanh_id',
        'ho_ten',
        'email',
        'gioi_tinh',
        'ngay_sinh',
        'ngay_mat',
        'id_cha',
        'cha_id',
        'id_me',
        'me_id',
        'trang_thai', // Ví dụ: 'Còn sống', 'Đã mất'
        'tinh_trang_hon_nhan', // Ví dụ: 'Độc thân', 'Đã kết hôn', 'Ly hôn', 'Góa'
        'thong_tin_them',
        'doi_thu',
        'loai_quan_he',
        'spouse_of_id',
        'ghi_chu',
        'avatar',
        'ngay_mat_al_ngay',
        'ngay_mat_al_thang',
        'ngay_mat_al_nam',
        'ngay_mat_al_nhuan'
    ];

    protected $appends = ['id_chi_nhanh', 'id_cha', 'id_me'];

    // Quan hệ: Thành viên thuộc về 1 Chi nhánh
    public function chiNhanh()
    {
        return $this->belongsTo(ChiNhanh::class, 'chi_nhanh_id');
    }

    // Accessors & Mutators to transparently support both naming conventions (database vs frontend)
    public function getIdChiNhanhAttribute()
    {
        return $this->attributes['chi_nhanh_id'] ?? null;
    }

    public function setIdChiNhanhAttribute($value)
    {
        $this->attributes['chi_nhanh_id'] = $value;
    }

    public function getIdChaAttribute()
    {
        return $this->attributes['cha_id'] ?? null;
    }

    public function setIdChaAttribute($value)
    {
        $this->attributes['cha_id'] = $value;
    }

    public function getIdMeAttribute()
    {
        return $this->attributes['me_id'] ?? null;
    }

    public function setIdMeAttribute($value)
    {
        $this->attributes['me_id'] = $value;
    }

    // Scope tìm kiếm thành viên theo chi nhánh (để DoiTacController sử dụng)
    public function scopeSearch($query, $chiNhanhId, $keyword)
    {
        return $query->where('chi_nhanh_id', $chiNhanhId)
                     ->when($keyword, function ($q) use ($keyword) {
                         $q->where(function($subQuery) use ($keyword) {
                             $subQuery->where('ho_ten', 'like', '%' . $keyword . '%')
                                      ->orWhere('thong_tin_them', 'like', '%' . $keyword . '%')
                                      ->orWhere('ghi_chu', 'like', '%' . $keyword . '%');
                         });
                     });
    }
}