<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NgayLe extends Model
{
    protected $table = 'NgayLe';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'Ngay',
        'TenNgayLe'
    ];
}