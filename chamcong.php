<?php

use App\Http\Controllers\ChamCongController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Cham Cong Routes
|--------------------------------------------------------------------------
| File này được require từ routes/api.php
| Prefix mặc định (nếu có): api/cham-cong
*/

Route::prefix('cham-cong')->group(function () {

    // ==========================================
    // 1. NGHIỆP VỤ DÀNH CHO NHÂN VIÊN (Điểm danh)
    // ==========================================
    
    // 👉 Check-in sáng
    // URL: POST /api/cham-cong/check-in
    Route::post('/check-in', [ChamCongController::class, 'checkIn'])
         ->name('api.chamcong.checkin');

    // 👉 Check-out chiều
    // URL: POST /api/cham-cong/check-out
    Route::post('/check-out', [ChamCongController::class, 'checkOut'])
         ->name('api.chamcong.checkout');


    // ==========================================
    // 2. NGHIỆP VỤ TRA CỨU & THỐNG KÊ
    // ==========================================

    // 👉 Lấy danh sách lịch sử chấm công (Có thể truyền thêm query ?thang=...&nam=...)
    // URL: GET /api/cham-cong
    Route::get('/', [ChamCongController::class, 'index'])
         ->name('api.chamcong.index');

    // 👉 Xem chi tiết chấm công của 1 ngày cụ thể
    // URL: GET /api/cham-cong/{id}
    Route::get('/{id}', [ChamCongController::class, 'show'])
         ->name('api.chamcong.show')
         ->where('id', '[0-9]+'); // Chỉ cho phép id là số

    // 👉 Thống kê tổng số công/số giờ làm trong tháng của 1 nhân viên (Để tính lương)
    // URL: GET /api/cham-cong/summary/{nhanVienId}/{thang}/{nam}
    Route::get('/summary/{nhanVienId}/{thang}/{nam}', [ChamCongController::class, 'summaryByMonth'])
         ->name('api.chamcong.summary')
         ->where(['nhanVienId' => '[0-9]+', 'thang' => '[0-9]+', 'nam' => '[0-9]+']);


    // ==========================================
    // 3. NGHIỆP VỤ QUẢN TRỊ (Admin / HR)
    // ==========================================
    // (Thường các route này sẽ bọc thêm middleware check quyền Admin ở file api.php)

    // 👉 Admin tạo hộ chấm công (Trường hợp đi công tác, quên mang thẻ...)
    // URL: POST /api/cham-cong
    Route::post('/', [ChamCongController::class, 'store'])
         ->name('api.chamcong.store');

    // 👉 Admin cập nhật/sửa giờ chấm công của nhân viên
    // URL: PUT /api/cham-cong/{id}
    Route::put('/{id}', [ChamCongController::class, 'update'])
         ->name('api.chamcong.update')
         ->where('id', '[0-9]+');

    // 👉 Admin xóa bản ghi chấm công bị lỗi
    // URL: DELETE /api/cham-cong/{id}
    Route::delete('/{id}', [ChamCongController::class, 'destroy'])
         ->name('api.chamcong.destroy')
         ->where('id', '[0-9]+');
});