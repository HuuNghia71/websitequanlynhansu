<?php

namespace App\Http\Controllers;

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
    // 🔍 Xem 1
    public function show($id)
    {
        $data = $this->service->findById($id);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // ➕ Thêm
    public function store(StoreLuongRequest $request)
    {
        $data = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Thêm thành công',
            'data' => $data
        ]);
    }

    // ✏️ Sửa
    public function update(UpdateLuongRequest $request, $id)
    {
        $data = $this->service->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công',
            'data' => $data
        ]);
    }

    // ❌ Xóa
    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Xóa thành công'
        ]);
    }
}