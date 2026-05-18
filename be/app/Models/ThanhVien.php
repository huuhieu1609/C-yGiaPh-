<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanhVien extends Model
{
    use HasFactory;

    protected $table = 'thanh_viens';

    protected $fillable = [
        'ho_ten', 'ten_goi', 'gioi_tinh', 'ngay_sinh', 'ngay_mat', 
        'noi_sinh', 'nghe_nghiep', 'doi_thu', 'trang_thai', 'ghi_chu',
        'chi_nhanh_id', 'cha_id', 'me_id'
    ];

    // Thuộc về 1 chi nhánh
    public function chiNhanh()
    {
        return $this->belongsTo(ChiNhanh::class, 'chi_nhanh_id', 'id');
    }

    // Tra cứu danh sách theo chi nhánh và từ khóa
    public function scopeSearch($query, $chiNhanhId, $keyword = null)
    {
        $query->where('chi_nhanh_id', $chiNhanhId);
        if ($keyword) {
            $query->where('ho_ten', 'like', '%' . $keyword . '%')
                  ->orWhere('ten_goi', 'like', '%' . $keyword . '%');
        }
        return $query;
    }
}