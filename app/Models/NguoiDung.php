<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NguoiDung extends Model
{
    protected $table = 'NguoiDung';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'TenDangNhap',
        'MatKhauHash',
        'VaiTro',
        'TrangThai',
        'NgayTao'
    ];
}