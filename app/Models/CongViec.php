<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CongViec extends Model
{
    protected $table = 'CongViec';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'TenCongViec',
        'MoTa',
        'NgayBatDau',
        'NgayKetThuc',
        'TrangThai',
        'PhongBanId'
    ];

    public function phongBan()
    {
        return $this->belongsTo(PhongBan::class, 'PhongBanId');
    }
}