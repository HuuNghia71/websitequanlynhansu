<?php

namespace App\Services;

use App\Models\BangLuong;
use App\Models\ChamCong;

class BangLuongService  
{
    // 📌 Danh sách
    public function getList($filters)
    {
        $query = BangLuong::with('nhanVien');
    
        // 🔍 Lọc theo nhân viên
        if (!empty($filters['nhan_vien_id'])) {
            $query->where('NhanVienId', $filters['nhan_vien_id']);
        }
    
        // 🔍 Lọc theo năm
        if (!empty($filters['nam'])) {
            $query->where('Nam', $filters['nam']);
        }
    
        // 🔍 Lọc theo tháng
        if (!empty($filters['thang'])) {
            $query->where('Thang', $filters['thang']);
        }
    
        // 🔍 Lọc theo tên
        if (!empty($filters['ten'])) {
            $query->whereHas('nhanVien', function ($q) use ($filters) {
                $q->where('Ten', 'like', '%' . $filters['ten'] . '%');
            });
        }
        
        $sortBy = $filters['sort_by'] ?? null;
        $sortOrder = $filters['sort_order'] ?? 'desc';
        
        if ($sortBy) {
            switch ($sortBy) {
                case 'tong_luong':
                    $query->orderBy('TongLuong', $sortOrder);
                    break;
        
                case 'ngay_cong':
                    $query->orderBy('TongNgayCong', $sortOrder);
                    break;
        
                case 'tham_nien':
                    $query->orderBy(
                        \App\Models\NhanVien::select('NgayTao')
                            ->whereColumn('NhanVien.Id', 'BangLuong.NhanVienId'),
                        $sortOrder
                    );
                    break;
            }
        } else {
            $query->orderBy('Id', 'desc');
        }
    
        return $query->get();
    }

    // 🔍 Xem 1
    public function findById($id)
    {
        return BangLuong::with('nhanVien')->findOrFail($id);
    }

    // ➕ Thêm mới (AUTO TÍNH TỪ CHẤM CÔNG)
    public function create($data)
    {
        // 👉 Lấy dữ liệu chấm công
        $chamCong = ChamCong::where('NhanVienId', $data['NhanVienId'])
            ->whereMonth('Ngay', $data['Thang'])
            ->whereYear('Ngay', $data['Nam'])
            ->selectRaw('
                SUM(SoNgayCong) as tongNgayCong,
                SUM(SoPhutTre) as tongPhutTre
            ')
            ->first();

        $tongNgayCong = $chamCong->tongNgayCong ?? 0;
        $tongPhutTre = $chamCong->tongPhutTre ?? 0;

        // 👉 Tính lương chính
        $luongMotNgay = $data['LuongCoBan'] / 26;
        $luongChinh = $luongMotNgay * $tongNgayCong;

        // 👉 Thưởng
        $thuong = 0;
        if ($tongPhutTre < 20) {
            $thuong = 500000;
        }

        // 👉 Phạt
        $phat = 0;
        if ($tongPhutTre > 30) {
            $phat = ($tongPhutTre - 30) * 5000;
        }

        // 👉 Tổng lương
        $tongLuong = $luongChinh + $thuong - $phat;

        // 👉 Gán vào data
        $data['TongNgayCong'] = $tongNgayCong;
        $data['Thuong'] = $thuong;
        $data['Phat'] = $phat;
        $data['TongLuong'] = round($tongLuong, 0);

        return BangLuong::create($data);
    }

    // ✏️ Cập nhật
    public function update($id, $data)
    {
        $luong = BangLuong::findOrFail($id);

        // 👉 Lấy lại chấm công
        $chamCong = ChamCong::where('NhanVienId', $luong->NhanVienId)
            ->whereMonth('Ngay', $luong->Thang)
            ->whereYear('Ngay', $luong->Nam)
            ->selectRaw('
                SUM(SoNgayCong) as tongNgayCong,
                SUM(SoPhutTre) as tongPhutTre
            ')
            ->first();

        $tongNgayCong = $chamCong->tongNgayCong ?? 0;
        $tongPhutTre = $chamCong->tongPhutTre ?? 0;

        // 👉 Lương chính
        $luongMotNgay = ($data['LuongCoBan'] ?? $luong->LuongCoBan) / 26;
        $luongChinh = $luongMotNgay * $tongNgayCong;

        // 👉 Thưởng
        $thuong = ($tongPhutTre < 20) ? 500000 : 0;

        // 👉 Phạt
        $phat = ($tongPhutTre > 30)
            ? ($tongPhutTre - 30) * 5000
            : 0;

        $tongLuong = $luongChinh + $thuong - $phat;

        // 👉 Update
        $luong->update([
            'LuongCoBan' => $data['LuongCoBan'] ?? $luong->LuongCoBan,
            'TongNgayCong' => $tongNgayCong,
            'Thuong' => $thuong,
            'Phat' => $phat,
            'TongLuong' => round($tongLuong, 0)
        ]);

        return $luong;
    }

    // ❌ Xóa
    public function delete($id)
    {
        $luong = BangLuong::findOrFail($id);
        $luong->delete();

        return true;
    }
}