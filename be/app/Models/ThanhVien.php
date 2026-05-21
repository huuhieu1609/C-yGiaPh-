<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanhVien extends Model
{
    use HasFactory;

    protected $table = 'thanh_viens';

    protected $fillable = [
        'chi_nhanh_id',
        'ho_ten',
        'ten_goi',
        'gioi_tinh',
        'ngay_sinh',
        'ngay_mat',
        'noi_sinh',
        'nghe_nghiep',
        'cha_id',
        'me_id',
        'trang_thai',
        'doi_thu',
        'loai_quan_he',
        'spouse_of_id',
        'ghi_chu',
        'avatar'
    ];

    // Quan hệ: Thành viên thuộc về 1 Chi nhánh
    public function chiNhanh()
    {
        return $this->belongsTo(ChiNhanh::class, 'chi_nhanh_id');
    }

    // Scope tìm kiếm thành viên theo chi nhánh (để DoiTacController sử dụng)
    public function scopeSearch($query, $chiNhanhId, $keyword)
    {
        return $query->where('chi_nhanh_id', $chiNhanhId)
                     ->when($keyword, function ($q) use ($keyword) {
                         $q->where(function($subQuery) use ($keyword) {
                             $subQuery->where('ho_ten', 'like', '%' . $keyword . '%')
                                      ->orWhere('nghe_nghiep', 'like', '%' . $keyword . '%')
                                      ->orWhere('ghi_chu', 'like', '%' . $keyword . '%');
                         });
                     });
    }
}