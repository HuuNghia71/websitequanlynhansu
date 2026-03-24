<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhanVienPhongBan extends Model
{
    protected $table = 'NhanVien_PhongBan';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'NhanVienId',
        'PhongBanId',
        'ChucVu',
        'NgayBatDau',
        'NgayKetThuc',
        'DangLam'
    ];
}