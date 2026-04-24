<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChamCong;
use Carbon\Carbon;

class ChamCongController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DANH SÁCH CHẤM CÔNG + LỌC + HIỂN THỊ TÊN NHÂN VIÊN
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = ChamCong::with('nhanVien');

        /*
        |--------------------------------------------------------------------------
        | Lọc theo mã nhân viên
        | Ví dụ:
        | 123
        | 124
        | 1001
        |--------------------------------------------------------------------------
        */
        if ($request->filled('ma_nhan_vien')) {
            $maNhanVien = trim($request->ma_nhan_vien);

            $query->where('NhanVienId', $maNhanVien);
        }

        /*
        |--------------------------------------------------------------------------
        | Lọc theo tháng
        |--------------------------------------------------------------------------
        */
        if ($request->filled('thang')) {
            $query->whereMonth('Ngay', $request->thang);
        }

        /*
        |--------------------------------------------------------------------------
        | Lọc theo năm
        |--------------------------------------------------------------------------
        */
        if ($request->filled('nam')) {
            $query->whereYear('Ngay', $request->nam);
        }

        /*
        |--------------------------------------------------------------------------
        | Lấy dữ liệu + ép kiểu + format thời gian chuẩn
        |--------------------------------------------------------------------------
        */
        $data = $query
            ->orderBy('Ngay', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'Id' => $item->Id,

                    'NhanVienId' => $item->NhanVienId,

                    'TenNhanVien' => optional($item->nhanVien)->Ten
                        ?? 'Chưa xác định',

                    'Ngay' => $item->Ngay,

                    'GioVao' => $item->GioVao
                        ? Carbon::parse($item->GioVao)
                            ->format('Y-m-d H:i:s')
                        : null,

                    'GioRa' => $item->GioRa
                        ? Carbon::parse($item->GioRa)
                            ->format('Y-m-d H:i:s')
                        : null,

                    'SoPhutTre' => (int) ($item->SoPhutTre ?? 0),

                    'SoGioLam' => (float) ($item->SoGioLam ?? 0),

                    'SoNgayCong' => (float) ($item->SoNgayCong ?? 0),

                    'SoGioTangCa' => (float) ($item->SoGioTangCa ?? 0),

                    'LaNgayLe' => (int) ($item->LaNgayLe ?? 0),
                ];
            });

        return response()->json($data);
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK IN
    |--------------------------------------------------------------------------
    */

    public function checkIn(Request $request)
    {
        $request->validate([
            'NhanVienId' => 'required|integer'
        ]);

        $nhanVienId = $request->NhanVienId;

        $now = Carbon::now('Asia/Ho_Chi_Minh');

        $exists = ChamCong::where('NhanVienId', $nhanVienId)
            ->whereDate('Ngay', $now->format('Y-m-d'))
            ->first();

        if ($exists) {
            return response()->json([
                'message' => 'Hôm nay bạn đã Check-in rồi!'
            ], 400);
        }

        $gioQuyDinh = Carbon::today('Asia/Ho_Chi_Minh')
            ->setTime(8, 0, 0);

        $soPhutTre = $now->gt($gioQuyDinh)
            ? $now->diffInMinutes($gioQuyDinh)
            : 0;

        ChamCong::create([
            'NhanVienId' => $nhanVienId,
            'Ngay' => $now->format('Y-m-d'),
            'GioVao' => $now,
            'GioRa' => null,
            'SoPhutTre' => $soPhutTre,
            'SoGioLam' => 0,
            'SoNgayCong' => 0,
            'SoGioTangCa' => 0,
            'LaNgayLe' => 0
        ]);

        return response()->json([
            'message' => 'Check-in thành công'
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK OUT
    |--------------------------------------------------------------------------
    */

    public function checkOut(Request $request)
    {
        $request->validate([
            'NhanVienId' => 'required|integer'
        ]);

        $nhanVienId = $request->NhanVienId;

        $now = Carbon::now('Asia/Ho_Chi_Minh');

        $chamCong = ChamCong::where('NhanVienId', $nhanVienId)
            ->whereDate('Ngay', $now->format('Y-m-d'))
            ->first();

        if (!$chamCong) {
            return response()->json([
                'message' => 'Bạn chưa Check-in hôm nay'
            ], 400);
        }

        if ($chamCong->GioRa) {
            return response()->json([
                'message' => 'Bạn đã Check-out rồi'
            ], 400);
        }

        $gioVao = Carbon::parse($chamCong->GioVao);

        $soGioLam = max(
            0,
            $now->diffInSeconds($gioVao) / 3600
        );

        $soGioLam = round($soGioLam, 2);

        if ($soGioLam >= 8) {
            $soNgayCong = 1;
        } elseif ($soGioLam >= 4) {
            $soNgayCong = 0.5;
        } else {
            $soNgayCong = 0;
        }

        $soTangCa = $soGioLam > 8
            ? round($soGioLam - 8, 2)
            : 0;

        $chamCong->update([
            'GioRa' => $now,
            'SoGioLam' => $soGioLam,
            'SoNgayCong' => $soNgayCong,
            'SoGioTangCa' => $soTangCa
        ]);

        return response()->json([
            'message' => 'Check-out thành công'
        ]);
    }
}