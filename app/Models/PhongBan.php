<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhongBan extends Model
{
    protected $table = 'PhongBan';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'TenPhong',
        'MoTa',
        'TruongPhongId',
        'TrangThai'
    ];

    public function nhanViens()
    {
        return $this->belongsToMany(NhanVien::class, 'NhanVien_PhongBan', 'PhongBanId', 'NhanVienId');
    }
}