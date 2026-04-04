<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhongBanController;

Route::prefix('phongban')->group(function () {
    // CRUD Cơ bản
    Route::get('/', [PhongBanController::class, 'index'])->name('phongban.index');
    Route::post('/store', [PhongBanController::class, 'store'])->name('phongban.store');
    Route::put('/{id}/update', [PhongBanController::class, 'update'])->name('phongban.update');
    Route::delete('/{id}/delete', [PhongBanController::class, 'destroy'])->name('phongban.destroy');

    // Chức năng nâng cao theo yêu cầu
    Route::post('/{id}/phan-cong', [PhongBanController::class, 'phanCongNhanVien'])->name('phongban.phancong');
    Route::post('/{id}/thay-truong-phong', [PhongBanController::class, 'thayTruongPhong'])->name('phongban.thaytruongphong');
    Route::get('/{id}/cong-viec', [PhongBanController::class, 'danhSachCongViec'])->name('phongban.congviec');
    Route::get('/{id}/ngay-cong', [PhongBanController::class, 'ngayCongNhanVien'])->name('phongban.ngaycong');
    Route::post('/{id}/phan-cong', [PhongBanController::class, 'phanCongNhanVien']);
});