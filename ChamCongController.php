<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChamCong;
use App\Models\NgayLe;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ChamCongController extends Controller
{
    // Lấy danh sách lịch sử chấm công
    public function index(Request $request)
    {
        $query = ChamCong::with('nhanVien');

        if ($request->has('thang') && $request->has('nam')) {
            $query->whereMonth('Ngay', $request->thang)
                  ->whereYear('Ngay', $request->nam);
        }

        return response()->json($query->orderBy('Ngay', 'desc')->get());
    }

    // Check-in sáng
    public function checkIn(Request $request)
    {
        $nhanVienId = $request->NhanVienId;
        $today = Carbon::today()->format('Y-m-d');
        $now = Carbon::now();

        $exists = ChamCong::where('NhanVienId', $nhanVienId)->where('Ngay', $today)->first();
        if ($exists) {
            return response()->json(['message' => 'Hôm nay bạn đã Check-in rồi!'], 400);
        }

        $gioQuyDinh = Carbon::createFromTimeString('08:00:00');
        $soPhutTre = $now->gt($gioQuyDinh) ? $now->diffInMinutes($gioQuyDinh) : 0;
        $isHoliday = NgayLe::where('Ngay', $today)->exists() ? 1 : 0;

        $chamCong = ChamCong::create([
            'NhanVienId' => $nhanVienId,
            'Ngay'       => $today,
            'GioVao'     => $now,
            'SoPhutTre'  => $soPhutTre,
            'LaNgayLe'   => $isHoliday,
            'SoNgayCong' => 0 
        ]);

        return response()->json(['message' => 'Check-in thành công', 'data' => $chamCong]);
    }

    // Check-out chiều
    public function checkOut(Request $request)
    {
        $nhanVienId = $request->NhanVienId;
        $today = Carbon::today()->format('Y-m-d');
        $now = Carbon::now();

        $chamCong = ChamCong::where('NhanVienId', $nhanVienId)->where('Ngay', $today)->first();
        if (!$chamCong || !$chamCong->GioVao) {
            return response()->json(['message' => 'Bạn chưa Check-in sáng nay!'], 400);
        }

        $gioVao = Carbon::parse($chamCong->GioVao);
        $soGioLam = $now->diffInMinutes($gioVao) / 60;
        $soNgayCong = $soGioLam >= 8 ? 1 : ($soGioLam >= 4 ? 0.5 : 0);

        $gioKetThucQuyDinh = Carbon::createFromTimeString('17:30:00');
        $soGioTangCa = $now->gt($gioKetThucQuyDinh) ? $now->diffInMinutes($gioKetThucQuyDinh) / 60 : 0;

        $chamCong->update([
            'GioRa'        => $now,
            'SoGioLam'     => round($soGioLam, 2),
            'SoNgayCong'   => $soNgayCong,
            'SoGioTangCa'  => round($soGioTangCa, 2)
        ]);

        return response()->json(['message' => 'Check-out thành công', 'data' => $chamCong]);
    }

    // Xem chi tiết 1 bản ghi
    public function show($id)
    {
        $data = ChamCong::with('nhanVien')->find($id);
        return $data ? response()->json($data) : response()->json(['message' => 'Không tìm thấy'], 404);
    }

    // Cập nhật (Dành cho Admin)
    public function update(Request $request, $id)
    {
        $chamCong = ChamCong::findOrFail($id);
        $chamCong->update($request->all());
        return response()->json(['message' => 'Cập nhật thành công', 'data' => $chamCong]);
    }

    // Xóa (Dành cho Admin)
    public function destroy($id)
    {
        ChamCong::destroy($id);
        return response()->json(['message' => 'Đã xóa bản ghi']);
    }

    // Thống kê tháng
    public function summaryByMonth($nhanVienId, $thang, $nam)
    {
        $summary = ChamCong::where('NhanVienId', $nhanVienId)
            ->whereMonth('Ngay', $thang)
            ->whereYear('Ngay', $nam)
            ->select(
                DB::raw('SUM(SoNgayCong) as TongCong'),
                DB::raw('SUM(SoGioTangCa) as TongTangCa'),
                DB::raw('SUM(SoPhutTre) as TongTre')
            )->first();
        return response()->json($summary);
    }
}