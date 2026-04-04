<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PhongBan;
use App\Models\NhanVien;
use App\Models\NhanVienPhongBan;
use App\Models\CongViec;
use App\Models\ChamCong;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PhongBanController extends Controller
{
    // 1. Xem danh sách phòng ban
    public function index()
    {
        // Lấy danh sách phòng ban
    $phongBans = \App\Models\PhongBan::with('truongPhong')->where('TrangThai', 1)->get();
    
    // Lấy danh sách nhân viên CÒN ĐI LÀM để nạp vào thẻ <select>
    // (Giả sử trong DB cột TrangThai của bảng NhanVien lưu là 'Đang làm việc')
    $nhanViens = \App\Models\NhanVien::where('TrangThai', 'Đang làm việc')->get();
    
    return view('phongban.index', compact('phongBans', 'nhanViens'));
    }

    // 2. Thêm phòng ban
    public function store(Request $request)
    {
        $request->validate(['TenPhong' => 'required|string|max:100']);
        PhongBan::create([
            'TenPhong' => $request->TenPhong,
            'MoTa' => $request->MoTa,
            'TrangThai' => 1
        ]);
        return back()->with('success', 'Thêm phòng ban thành công');
    }

    // 3. Sửa phòng ban
    public function update(Request $request, $id)
    {
        $phongBan = PhongBan::findOrFail($id);
        $phongBan->update($request->only(['TenPhong', 'MoTa']));
        return back()->with('success', 'Cập nhật thành công');
    }

    // 4. Xóa (Ẩn) phòng ban
    public function destroy($id)
    {
        $phongBan = PhongBan::findOrFail($id);
        $phongBan->update(['TrangThai' => 0]); // Xóa mềm
        return back()->with('success', 'Đã xóa phòng ban');
    }

    // 5. Phân công nhân viên vào phòng ban
   public function phanCongNhanVien(\Illuminate\Http\Request $request, $id)
    {
        $nhanVienId = $request->NhanVienId;
        $nhanVien = \App\Models\NhanVien::findOrFail($nhanVienId);

        // 1. Kiểm tra nhân viên còn đi làm không
        if ($nhanVien->TrangThai != 'Đang làm việc') {
            return response()->json(['message' => 'Nhân viên này đã nghỉ việc hoặc tạm khóa.'], 400);
        }

        // 2. Lấy bản ghi làm việc hiện tại của nhân viên (nếu có)
        $dangLamPhongKhac = \App\Models\NhanVienPhongBan::where('NhanVienId', $nhanVienId)
                                            ->where('DangLam', 1)
                                            ->first();

        if ($dangLamPhongKhac && $dangLamPhongKhac->PhongBanId == $id) {
             return response()->json(['message' => 'Nhân viên này hiện đã ở trong phòng ban này rồi.'], 400);
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            if ($dangLamPhongKhac) {
                $dangLamPhongKhac->update([
                    'DangLam' => 0,
                    'NgayKetThuc' => \Carbon\Carbon::today()->toDateString()
                ]);
            }

            \App\Models\NhanVienPhongBan::create([
                'NhanVienId' => $nhanVienId,
                'PhongBanId' => $id,
                'ChucVu' => 'Nhân viên',
                'NgayBatDau' => \Carbon\Carbon::today()->toDateString(),
                'DangLam' => 1
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return response()->json(['message' => 'Phân công thành công!'], 200);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['message' => 'Lỗi hệ thống: ' . $e->getMessage()], 500);
        }
    }
    // 6. Thêm/Xóa Trưởng phòng (Lưu lịch sử)
    public function thayTruongPhong(Request $request, $id)
    {
        $phongBan = PhongBan::findOrFail($id);
        $nhanVienIdMoi = $request->NhanVienId; // ID trưởng phòng mới

        DB::beginTransaction();
        try {
            // Hạ bệ trưởng phòng cũ (nếu có) trong bảng lịch sử
            if ($phongBan->TruongPhongId) {
                NhanVienPhongBan::where('NhanVienId', $phongBan->TruongPhongId)
                    ->where('PhongBanId', $id)
                    ->where('DangLam', 1)
                    ->update(['ChucVu' => 'Nhân viên']);
            }

            // Cập nhật trưởng phòng mới
            $phongBan->update(['TruongPhongId' => $nhanVienIdMoi]);

            // Nâng cấp chức vụ trưởng phòng mới trong lịch sử
            if ($nhanVienIdMoi) {
                NhanVienPhongBan::where('NhanVienId', $nhanVienIdMoi)
                    ->where('PhongBanId', $id)
                    ->where('DangLam', 1)
                    ->update(['ChucVu' => 'Trưởng phòng']);
            }

            DB::commit();
            return back()->with('success', 'Cập nhật trưởng phòng thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi hệ thống');
        }
    }

    // 7. Xem danh sách công việc (Cập nhật trạng thái tự động)
    public function danhSachCongViec($id)
    {
        $congViecs = CongViec::where('PhongBanId', $id)->get();
        $today = Carbon::today();

        foreach ($congViecs as $cv) {
            $hanChot = Carbon::parse($cv->NgayKetThuc);
            
            // Logic cập nhật trạng thái tự động
            if ($cv->TrangThai != 'Hoàn thành') {
                if ($hanChot->isPast()) {
                    $cv->TrangThai = 'Trễ hạn';
                    $cv->save(); // Lưu thẳng xuống DB
                } elseif ($hanChot->diffInDays($today) <= 1) {
                    $cv->TrangThai = 'Sắp đến hạn';
                    $cv->save();
                }
            }
        }

        return view('phongban.congviec', compact('congViecs'));
    }

    // 8. Xem ngày công nhân viên trong phòng ban theo tháng
    public function ngayCongNhanVien(Request $request, $id)
    {
        $thang = $request->thang ?? date('m');
        $nam = $request->nam ?? date('Y');

        // Lấy danh sách ID nhân viên ĐANG LÀM trong phòng ban này
        $nhanVienIds = NhanVienPhongBan::where('PhongBanId', $id)
                            ->where('DangLam', 1)
                            ->pluck('NhanVienId');

        // Lấy dữ liệu chấm công của họ trong tháng/năm được chọn
        $chamCongs = ChamCong::with('nhanVien') // Cần thiết lập quan hệ belongsTo trong model ChamCong
                            ->whereIn('NhanVienId', $nhanVienIds)
                            ->whereMonth('Ngay', $thang)
                            ->whereYear('Ngay', $nam)
                            ->get();

        return view('phongban.ngaycong', compact('chamCongs', 'thang', 'nam'));
    }
}