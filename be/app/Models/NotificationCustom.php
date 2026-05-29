<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationCustom extends Model
{
    use HasFactory;

    protected $table = 'notifications_custom';
    protected $fillable = ['user_id','target_member_id','title','body','meta','read_at'];

    protected $casts = [ 'meta' => 'array', 'read_at' => 'datetime' ];
}
