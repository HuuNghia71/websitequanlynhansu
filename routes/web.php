<?php

use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
use App\Http\Controllers\NhanVienController;
=======
use App\Http\Controllers\PhongBanController;
>>>>>>> b608f206ab683dab3a69ee0a3dd7985db8fd7f34

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

<<<<<<< HEAD
// 👉 CÁC TRANG MODULE KHÁC (trả về HTML fragment để AJAX load)
Route::get('/phongban', function () {
    return view('phongban.index');
});
=======

Route::get('/phongban',
 [PhongBanController::class, 'index'])->name('phongban.index');
>>>>>>> b608f206ab683dab3a69ee0a3dd7985db8fd7f34

Route::get('/congviec', function () {
    return view('congviec.index');
});

Route::get('/chamcong', function () {
    return view('chamcong.index');
});

Route::get('/luong', function () {
    return view('luong.index');
});
Route::post('/phongban/{id}/phan-cong',
 [PhongBanController::class, 'phanCongNhanVien'])->name('phongban.phancong');

 // Khi click vào phòng ban, gọi Route này để xem danh sách nhân viên
Route::get('/phongban/{id}/nhan-vien',
 [PhongBanController::class, 'danhSachNhanVien'])->name('phongban.nhanvien');