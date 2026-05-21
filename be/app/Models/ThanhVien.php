<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThanhVien extends Model
{
    use HasFactory;

    protected $table = 'thanh_viens';

    protected $fillable = [
        'ho_ten',
        'ten_goi',
        'gioi_tinh',
        'ngay_sinh',
        'ngay_mat',
        'noi_sinh',
        'nghe_nghiep',
        'avatar',
        'doi_thu',
        'chi_nhanh_id',
        'cha_id',
        'me_id',
        'ghi_chu',
        'trang_thai',
        'loai_quan_he',
        'spouse_of_id',
    ];

    public function chiNhanh(): BelongsTo
    {
        return $this->belongsTo(ChiNhanh::class, 'chi_nhanh_id');
    }

    public function cha(): BelongsTo
    {
        return $this->belongsTo(self::class, 'cha_id');
    }

    public function me(): BelongsTo
    {
        return $this->belongsTo(self::class, 'me_id');
    }

    public function conCai(): HasMany
    {
        return $this->hasMany(self::class, 'cha_id')->orWhere('me_id', $this->id);
    }

    public function hinhAnhs(): HasMany
    {
        return $this->hasMany(HinhAnh::class);
    }

    public function moPhans(): HasMany
    {
        return $this->hasMany(MoPhan::class);
    }

    public function voChongs(): HasMany
    {
        return $this->hasMany(VoChong::class, 'chong_id')->orWhere('vo_id', $this->id);
    }

    public function conNuois(): HasMany
    {
        return $this->hasMany(ConNuoi::class, 'cha_me_id');
    }

    public function thamGiaSuKiens(): HasMany
    {
        return $this->hasMany(ThamGiaSuKien::class);
    }

    public function scopeSearch(Builder $query, int|string $chiNhanhId, ?string $keyword): Builder
    {
        return $query->where('chi_nhanh_id', $chiNhanhId)
            ->when($keyword, function (Builder $query) use ($keyword): void {
                $query->where(function (Builder $query) use ($keyword): void {
                    $query->where('ho_ten', 'like', '%'.$keyword.'%')
                        ->orWhere('ten_goi', 'like', '%'.$keyword.'%')
                        ->orWhere('ghi_chu', 'like', '%'.$keyword.'%');
                });
            });
    }
}
