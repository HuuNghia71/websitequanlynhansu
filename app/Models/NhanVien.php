<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhanVien extends Model
{
    protected $table = 'NhanVien';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'Ten',
        'NgaySinh',
        'GioiTinh',
        'SoDienThoai',
        'Email',
        'DiaChi',
        'TrangThai',
        'NgayTao'
    ];

    public function phongBans()
    {
        return $this->belongsToMany(PhongBan::class, 'NhanVien_PhongBan', 'NhanVienId', 'PhongBanId');
    }
}
