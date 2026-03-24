<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileCongViec extends Model
{
    protected $table = 'FileCongViec';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'CongViecId',
        'DuongDan'
    ];
}