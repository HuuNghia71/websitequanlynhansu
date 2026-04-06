<div id="app-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-person-badge"></i> Chi tiết nhân viên</h3>
        <button class="btn btn-secondary" onclick="goBack()">
            <i class="bi bi-arrow-left"></i> Quay lại
        </button>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Tên nhân viên</label>
                            <h5>{{ $nhanvien->Ten }}</h5>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Mã nhân viên</label>
                            <h5>#{{ $nhanvien->Id }}</h5>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Ngày sinh</label>
                            <p>{{ date('d/m/Y', strtotime($nhanvien->NgaySinh)) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Giới tính</label>
                            <p>{{ $nhanvien->GioiTinh }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Số điện thoại</label>
                            <p><i class="bi bi-telephone"></i> {{ $nhanvien->SoDienThoai }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Email</label>
                            <p><i class="bi bi-envelope"></i> {{ $nhanvien->Email }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Địa chỉ</label>
                        <p><i class="bi bi-geo-alt"></i> {{ $nhanvien->DiaChi }}</p>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Trạng thái</label>
                            <span class="badge {{ $nhanvien->TrangThai === 'Hoạt động' ? 'bg-success' : 'bg-danger' }} fs-6">
                                {{ $nhanvien->TrangThai }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Ngày tạo</label>
                            <p><i class="bi bi-calendar-event"></i> {{ date('d/m/Y H:i', strtotime($nhanvien->NgayTao)) }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-secondary" onclick="goBack()">
                            <i class="bi bi-x-circle"></i> Đóng
                        </button>
                        <button type="button" class="btn btn-warning" onclick="goToEdit({{ $nhanvien->Id }})">
                            <i class="bi bi-pencil"></i> Chỉnh sửa
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light shadow-sm">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="bi bi-info-circle"></i> Thông tin bổ sung
                    </h6>
                    <small class="text-muted">
                        <p><strong>Tổng số công việc:</strong> 0</p>
                        <p><strong>Phòng ban:</strong> --</p>
                        <p><strong>Ngày tham gia:</strong> {{ date('d/m/Y', strtotime($nhanvien->NgayTao)) }}</p>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function goBack() {
    loadPage('/nhanvien');
}

function goToEdit(id) {
    loadPage('/nhanvien/' + id + '/edit');
}
</script>
