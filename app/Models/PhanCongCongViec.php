<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhanCongCongViec extends Model
{
    protected $table = 'PhanCongCongViec';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'CongViecId',
        'NhanVienId'
    ];
}