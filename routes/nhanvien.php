<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NhanVienController;

Route::resource('nhanvien', NhanVienController::class);
