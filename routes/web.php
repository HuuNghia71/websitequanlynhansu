<?php

use Illuminate\Support\Facades\Route;
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

// 👉 CÁC TRANG MODULE (trả về HTML fragment để AJAX load)

Route::get('/nhanvien', function () {
    return view('nhanvien.index');
});


Route::get('/phongban',
 [PhongBanController::class, 'index'])->name('phongban.index');

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