<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NhanVienController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Đây là nơi định nghĩa các route cho giao diện (Blade)
*/

// 👉 Trang chính: load layout (sidebar + content)
Route::get('/', function () {
    return view('layout.main');
});

// 👉 AUTH (nếu có login)
Route::get('/login', function () {
    return view('auth.login');
});

// 👉 QUẢN LÝ NHÂN VIÊN (CRUD)
Route::resource('nhanvien', NhanVienController::class);

// 👉 CÁC TRANG MODULE KHÁC (trả về HTML fragment để AJAX load)
Route::get('/phongban', function () {
    return view('phongban.index');
});

Route::get('/congviec', function () {
    return view('congviec.index');
});

Route::get('/chamcong', function () {
    return view('chamcong.index');
});

Route::get('/luong', function () {
    return view('luong.index');
});