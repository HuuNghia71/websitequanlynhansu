<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BangLuongController;

// CHẤM CÔNG
Route::get('/luong/chamcong', [BangLuongController::class, 'getChamCong']);

// LƯƠNG
Route::get('/luong', [BangLuongController::class, 'index']);
Route::get('/luong/{id}', [BangLuongController::class, 'show']);
Route::post('/luong', [BangLuongController::class, 'store']);
Route::put('/luong/{id}', [BangLuongController::class, 'update']);
Route::delete('/luong/{id}', [BangLuongController::class, 'destroy']);



// ✅ THÊM CÁI NÀY
Route::get('/nhanvien', [BangLuongController::class, 'getNhanVien']);
