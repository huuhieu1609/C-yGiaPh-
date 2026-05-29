<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberRole extends Model
{
    use HasFactory;

    protected $table = 'member_role';
    protected $fillable = ['member_id','role_id','chi_nhanh_id','assigned_by','expires_at'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function member()
    {
        return $this->belongsTo(\App\Models\ThanhVien::class, 'member_id');
    }
}
