<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeXuatChinhSua extends Model
{
    use HasFactory;

    protected $table = "de_xuat_chinh_suas";

    protected $fillable = [
        "thanh_vien_id",
        "proposed_by_user_id",
        "type",
        "data",
        "status",
        "approved_by",
        "note",
    ];

    protected $casts = [
        "data" => "array",
    ];

    public function thanhVien()
    {
        return $this->belongsTo(ThanhVien::class, "thanh_vien_id");
    }

    public function proposedBy()
    {
        return $this->belongsTo(NguoiDung::class, "proposed_by_user_id");
    }

    public function approver()
    {
        return $this->belongsTo(NguoiDung::class, "approved_by");
    }
}
