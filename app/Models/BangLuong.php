<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BangLuong extends Model
{
    protected $table = 'BangLuong';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'NhanVienId',
        'Thang',
        'Nam',
        'LuongCoBan',
        'TongNgayCong', 
        'TongGioTangCa',   // ✅ thêm
        'TienTangCa',      // ✅ thêm
        'Thuong',
        'Phat',
        'TongLuong'
    ];

    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'NhanVienId');
    }
}