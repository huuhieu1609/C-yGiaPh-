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
        'ho_ten',
        'email',
        'gioi_tinh',
        'ngay_sinh',
        'ngay_mat',
        'id_cha',
        'id_me',
        'trang_thai', // Ví dụ: 'Còn sống', 'Đã mất'
        'thong_tin_them',
        'doi_thu',
        'loai_quan_he',
        'spouse_of_id',
        'ghi_chu',
        'avatar'
    ];

    // Quan hệ: Thành viên thuộc về 1 Chi nhánh
    public function chiNhanh()
    {
        return $this->belongsTo(ChiNhanh::class, 'id_chi_nhanh');
    }

    // Scope tìm kiếm thành viên theo chi nhánh (để DoiTacController sử dụng)
    public function scopeSearch($query, $chiNhanhId, $keyword)
    {
        return $query->where('id_chi_nhanh', $chiNhanhId)
                     ->when($keyword, function ($q) use ($keyword) {
                         $q->where(function($subQuery) use ($keyword) {
                             $subQuery->where('ho_ten', 'like', '%' . $keyword . '%')
                                      ->orWhere('thong_tin_them', 'like', '%' . $keyword . '%')
                                      ->orWhere('ghi_chu', 'like', '%' . $keyword . '%');
                         });
                     });
    }
}