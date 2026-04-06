<div id="app-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-people"></i> Quản lý nhân viên</h3>
        <button class="btn btn-primary" onclick="goToCreate()">
            <i class="bi bi-plus-circle"></i> Thêm nhân viên
        </button>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tên nhân viên</th>
                        <th>Ngày sinh</th>
                        <th>Giới tính</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($nhanviens as $nhanvien)
                        <tr>
                            <td>{{ $nhanvien->Id }}</td>
                            <td>
                                <strong>{{ $nhanvien->Ten }}</strong>
                            </td>
                            <td>{{ date('d/m/Y', strtotime($nhanvien->NgaySinh)) }}</td>
                            <td>{{ $nhanvien->GioiTinh }}</td>
                            <td>{{ $nhanvien->SoDienThoai }}</td>
                            <td>{{ $nhanvien->Email }}</td>
                            <td>
                                <span class="badge {{ $nhanvien->TrangThai === 'Hoạt động' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $nhanvien->TrangThai }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="goToShow({{ $nhanvien->Id }})">
                                    <i class="bi bi-eye"></i> Xem
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="goToEdit({{ $nhanvien->Id }})">
                                    <i class="bi bi-pencil"></i> Sửa
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteNhanVien({{ $nhanvien->Id }})">
                                    <i class="bi bi-trash"></i> Xóa
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox"></i> Không có nhân viên nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function goToCreate() {
    loadPage('/nhanvien/create');
}

function goToShow(id) {
    loadPage('/nhanvien/' + id);
}

function goToEdit(id) {
    loadPage('/nhanvien/' + id + '/edit');
}

function deleteNhanVien(id) {
    if (confirm('Bạn có chắc chắn muốn xóa nhân viên này?')) {
        fetch('/nhanvien/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Quay lại danh sách
                loadPage('/nhanvien');
            } else {
                alert('Lỗi khi xóa nhân viên: ' + (data.message || ''));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi kết nối');
        });
    }
}
</script>