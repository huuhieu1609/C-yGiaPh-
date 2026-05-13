<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DoiTocHo extends Model
{
    use HasFactory;

    protected $table = "doi_toc_hos";
    protected $fillable = ["so_doi", "ten_doi", "mo_ta", "trang_thai", "chi_nhanh_id"];

    public function chiNhanh()
    {
        return $this->belongsTo(ChiNhanh::class, 'chi_nhanh_id');
    }
}
