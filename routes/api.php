<?php

use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    require __DIR__.'/nhanvien.php';
    require __DIR__.'/phongban.php';
    require __DIR__.'/congviec.php';
    require __DIR__.'/chamcong.php';
    require __DIR__.'/luong.php';
    require __DIR__.'/auth.php';
});

Route::get('/test-api', function () {
    return 'api ok';
});