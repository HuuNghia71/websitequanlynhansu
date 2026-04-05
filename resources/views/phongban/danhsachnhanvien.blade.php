@extends('layout.main') @section('content')
<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Danh sách nhân viên - Phòng {{ $phongBan->TenPhong }}</h4>
            <a href="{{ route('phongban.index') }}" class="btn btn-secondary btn-sm">Quay lại</a>
        </div>
        
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>STT</th>
                        <th>Tên nhân viên</th>
                        <th>Chức vụ (Trong phòng)</th>
                        <th>Ngày bắt đầu làm</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($danhSachNhanVien as $index => $nvpb)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $nvpb->nhanVien->TenNhanVien ?? 'Không xác định' }}</td>
                            <td>
                                @if($nvpb->ChucVu == 'Trưởng phòng')
                                    <span class="badge bg-danger">{{ $nvpb->ChucVu }}</span>
                                @else
                                    <span class="badge bg-info">{{ $nvpb->ChucVu }}</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($nvpb->NgayBatDau)->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Chưa có nhân viên nào trong phòng ban này.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection