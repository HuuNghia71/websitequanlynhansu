<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class NhanVienController extends Controller
{
    /**
     * Hiển thị danh sách nhân viên
     */
    public function index()
    {
        $nhanviens = NhanVien::all();
        return view('nhanvien.index', compact('nhanviens'));
    }

    /**
     * Hiển thị form tạo nhân viên mới
     */
    public function create()
    {
        return view('nhanvien.create');
    }

    /**
     * Lưu nhân viên mới vào database
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'Ten' => 'required|string|max:255',
                'NgaySinh' => 'required|date',
                'GioiTinh' => 'required|in:Nam,Nữ',
                'SoDienThoai' => 'required|string|max:20',
                'Email' => 'required|email|unique:NhanVien',
                'DiaChi' => 'required|string|max:255',
                'TrangThai' => 'required|in:Hoạt động,Đã nghỉ',
            ]);

            $validated['NgayTao'] = now();
            
            NhanVien::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Thêm nhân viên thành công!'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kiểm tra lại thông tin',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Hiển thị chi tiết nhân viên
     */
    public function show(NhanVien $nhanvien)
    {
        return view('nhanvien.show', compact('nhanvien'));
    }

    /**
     * Hiển thị form chỉnh sửa nhân viên
     */
    public function edit(NhanVien $nhanvien)
    {
        return view('nhanvien.edit', compact('nhanvien'));
    }

    /**
     * Cập nhật thông tin nhân viên
     */
    public function update(Request $request, NhanVien $nhanvien)
    {
        try {
            $validated = $request->validate([
                'Ten' => 'required|string|max:255',
                'NgaySinh' => 'required|date',
                'GioiTinh' => 'required|in:Nam,Nữ',
                'SoDienThoai' => 'required|string|max:20',
                'Email' => 'required|email|unique:NhanVien,Email,' . $nhanvien->Id . ',Id',
                'DiaChi' => 'required|string|max:255',
                'TrangThai' => 'required|in:Hoạt động,Đã nghỉ',
            ]);

            $nhanvien->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật nhân viên thành công!'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kiểm tra lại thông tin',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Xóa nhân viên
     */
    public function destroy(NhanVien $nhanvien)
    {
        $nhanvien->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa nhân viên thành công!'
        ], 200);
    }
}
