<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('NhanVien', function (Blueprint $table) {
            $table->id('Id');
            $table->string('Ten', 255);
            $table->date('NgaySinh');
            $table->enum('GioiTinh', ['Nam', 'Nữ']);
            $table->string('SoDienThoai', 20);
            $table->string('Email')->unique();
            $table->string('DiaChi', 255);
            $table->enum('TrangThai', ['Hoạt động', 'Đã nghỉ'])->default('Hoạt động');
            $table->timestamp('NgayTao')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('NhanVien');
    }
};
