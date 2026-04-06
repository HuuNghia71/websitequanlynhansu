<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\PhongBanController;

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

// 👉 PHÒNG BAN (dùng controller chuẩn)
Route::get('/phongban', [PhongBanController::class, 'index'])->name('phongban.index');

// 👉 CÁC TRANG MODULE KHÁC
Route::get('/congviec', function () {
    return view('congviec.index');
});

Route::get('/chamcong', function () {
    return view('chamcong.index');
});

Route::get('/luong', function () {
    return view('luong.index');
});

// 👉 Phân công nhân viên vào phòng ban
Route::post('/phongban/{id}/phan-cong',
    [PhongBanController::class, 'phanCongNhanVien'])->name('phongban.phancong');

// 👉 Xem danh sách nhân viên theo phòng ban
Route::get('/phongban/{id}/nhan-vien',
    [PhongBanController::class, 'danhSachNhanVien'])->name('phongban.nhanvien');