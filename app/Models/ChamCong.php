<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChamCong extends Model
{
    protected $table = 'ChamCong';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'NhanVienId',
        'Ngay',
        'GioVao',
        'GioRa',
        'SoPhutTre',
        'SoGioLam',
        'SoNgayCong',
        'LaNgayLe',
        'SoGioTangCa'
    ];

    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'NhanVienId');
    }
}