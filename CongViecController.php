<?php

namespace App\Http\Controllers;

use App\Models\CongViec;
use App\Models\FileCongViec;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CongViecController extends Controller
{
    // Xem danh sách công việc và cập nhật trạng thái trước khi trả về
    public function index(Request $request): JsonResponse
    {
        $this->syncStatuses();

        $query = CongViec::with(['phongBan', 'nhanViens', 'files']);

        // Lọc theo tên công việc
        if ($request->filled('q')) {
            $query->where('TenCongViec', 'like', '%'.$request->query('q').'%');
        }

        // Lọc theo trạng thái công việc
        if ($request->filled('trang_thai')) {
            $query->where('TrangThai', $request->query('trang_thai'));
        }

        // Lọc theo phòng ban
        if ($request->filled('phong_ban_id')) {
            $query->where('PhongBanId', $request->query('phong_ban_id'));
        }

        // Lọc theo nhân viên được phân công
        if ($request->filled('nhan_vien_id')) {
            $employeeId = $request->query('nhan_vien_id');
            $query->whereExists(function ($subQuery) use ($employeeId) {
                $subQuery->select(DB::raw(1))
                    ->from('PhanCongCongViec')
                    ->whereColumn('PhanCongCongViec.CongViecId', 'CongViec.Id')
                    ->where('PhanCongCongViec.NhanVienId', $employeeId);
            });
        }

        // Sắp xếp theo ngày tạo hoặc ngày hết hạn
        $sortBy = $request->query('sort_by', 'created');
        $sortOrder = strtolower($request->query('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc';

        if ($sortBy === 'due_date') {
            $query->orderBy('NgayKetThuc', $sortOrder);
        } elseif (DB::getSchemaBuilder()->hasColumn((new CongViec)->getTable(), 'NgayTao')) {
            $query->orderBy('NgayTao', $sortOrder);
        } else {
            $query->orderBy('Id', $sortOrder);
        }

        $data = $query->get()->map(function (CongViec $item) {
            $item->employee_names = $item->nhanViens->pluck('Ten')->join(', ');
            $item->attachment_count = $item->files->count();
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    // Xem chi tiết một công việc
    public function show($id): JsonResponse
    {
        $congviec = CongViec::with(['phongBan', 'nhanViens', 'files'])->find($id);

        if (!$congviec) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy công việc'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $congviec,
        ]);
    }

    // Thêm công việc mới
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'TenCongViec' => 'required|string|max:255',
            'MoTa' => 'nullable|string',
            'NgayBatDau' => 'nullable|date',
            'NgayKetThuc' => 'nullable|date',
            'TrangThai' => 'nullable|string|max:50',
            'PhongBanId' => 'nullable|integer|exists:PhongBan,Id',
            'NhanVienId' => 'nullable|integer|exists:NhanVien,Id',
        ]);

        if (DB::getSchemaBuilder()->hasColumn((new CongViec)->getTable(), 'NgayTao')) {
            $validated['NgayTao'] = Carbon::now();
        }

        $congviec = CongViec::create($validated);

        if (!empty($validated['NhanVienId'])) {
            DB::table('PhanCongCongViec')->insert([
                'CongViecId' => $congviec->Id,
                'NhanVienId' => $validated['NhanVienId'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thêm công việc thành công',
            'data' => $congviec,
        ], 201);
    }

    // Cập nhật thông tin công việc
    public function update(Request $request, $id): JsonResponse
    {
        $congviec = CongViec::find($id);

        if (!$congviec) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy công việc'], 404);
        }

        $validated = $request->validate([
            'TenCongViec' => 'sometimes|required|string|max:255',
            'MoTa' => 'nullable|string',
            'NgayBatDau' => 'nullable|date',
            'NgayKetThuc' => 'nullable|date',
            'TrangThai' => 'nullable|string|max:50',
            'PhongBanId' => 'nullable|integer|exists:PhongBan,Id',
            'NhanVienId' => 'nullable|integer|exists:NhanVien,Id',
        ]);

        $congviec->update($validated);

        if (array_key_exists('NhanVienId', $validated)) {
            DB::table('PhanCongCongViec')->where('CongViecId', $id)->delete();

            if (!empty($validated['NhanVienId'])) {
                DB::table('PhanCongCongViec')->insert([
                    'CongViecId' => $id,
                    'NhanVienId' => $validated['NhanVienId'],
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật công việc thành công',
            'data' => $congviec,
        ]);
    }

    // Xóa công việc và các liên kết file/phan cong
    public function destroy($id): JsonResponse
    {
        $congviec = CongViec::find($id);

        if (!$congviec) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy công việc'], 404);
        }

        Storage::deleteDirectory('public/congviec/'.$id);
        DB::table('FileCongViec')->where('CongViecId', $id)->delete();
        DB::table('PhanCongCongViec')->where('CongViecId', $id)->delete();
        $congviec->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa công việc thành công',
        ]);
    }

    // Upload file cho một công việc
    public function uploadFile(Request $request, $id): JsonResponse
    {
        $congviec = CongViec::find($id);

        if (!$congviec) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy công việc'], 404);
        }

        $validated = $request->validate([
            'attachment' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,txt',
        ]);

        $path = $validated['attachment']->store('public/congviec');
        $url = Storage::url($path);

        FileCongViec::create([
            'CongViecId' => $id,
            'DuongDan' => $url,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Upload file thành công',
            'path' => $url,
        ]);
    }

    // Lấy danh sách file của công việc
    public function getFiles($id): JsonResponse
    {
        $files = DB::table('FileCongViec')->where('CongViecId', $id)->get();

        return response()->json([
            'success' => true,
            'data' => $files,
        ]);
    }

    // Đồng bộ trạng thái công việc mỗi lần load danh sách
    protected function syncStatuses(): void
    {
        $today = Carbon::today();

        $tasks = CongViec::whereNotNull('NgayKetThuc')
            ->whereNotIn('TrangThai', ['Hoàn thành', 'Đã hủy'])
            ->get();

        foreach ($tasks as $task) {
            $deadline = Carbon::parse($task->NgayKetThuc)->startOfDay();
            $days = $deadline->diffInDays($today, false);

            if ($days < 0 && $task->TrangThai !== 'Trễ') {
                $task->TrangThai = 'Trễ';
                $task->save();
            }

            if ($days === 1 && $task->TrangThai !== 'Sắp đến hạn') {
                $task->TrangThai = 'Sắp đến hạn';
                $task->save();
            }
        }
    }
}
