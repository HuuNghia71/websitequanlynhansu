<?php

namespace App\Http\Controllers;
use App\Models\NhanVien;
use App\Models\ChamCong;
use Illuminate\Http\Request; // 👈 THÊM DÒNG NÀY
use App\Http\Requests\LuongIndexRequest;
use App\Services\BangLuongService;
use App\Http\Requests\StoreLuongRequest;
use App\Http\Requests\UpdateLuongRequest;

class BangLuongController extends Controller
{
    protected $service;

    public function __construct(BangLuongService $service)
    {
        $this->service = $service;
    }

    public function index(LuongIndexRequest $request)
    {
        $data = $this->service->getList($request->validated());

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $data = $this->service->findById($id);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(StoreLuongRequest $request)
    {
        $data = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Thêm thành công',
            'data' => $data
        ]);
    }

    public function update(UpdateLuongRequest $request, $id)
    {
        $data = $this->service->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công',
            'data' => $data
        ]);
    }

    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Xóa thành công'
        ]);
    }

    // ✅ API lấy chấm công
    public function getChamCong(Request $request)
{
    try {
        $nhanvien_id = $request->nhanvien_id;
        $thang = $request->thang;
        $nam = $request->nam;

        $chamCong = ChamCong::where('NhanVienId', $nhanvien_id)
            ->whereMonth('Ngay', $thang)
            ->whereYear('Ngay', $nam)
            ->get();

            return response()->json([
                'success' => true, // ✅ THÊM DÒNG NÀY
                'data' => [
                    'cham_cong' => $chamCong,
                    'tong_ngay_cong' => $chamCong->count(),
                    'tong_phut_tre' => $chamCong->sum('SoPhutTre')
                ]
            ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}
public function getNhanVien()
{
    $data = NhanVien::select('Id', 'Ten')->get();

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}
}
