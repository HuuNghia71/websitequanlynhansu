<div id="app-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-pencil"></i> Chỉnh sửa nhân viên</h3>
        <button class="btn btn-secondary" onclick="goBack()">
            <i class="bi bi-arrow-left"></i> Quay lại
        </button>
    </div>

    <div id="alertError"></div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form id="formEditNhanVien" onsubmit="submitForm(event)">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="Ten" class="form-label">Tên nhân viên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" 
                               id="Ten" name="Ten" value="{{ $nhanvien->Ten }}" required>
                        <div class="invalid-feedback d-block" id="error-Ten"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="NgaySinh" class="form-label">Ngày sinh <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" 
                               id="NgaySinh" name="NgaySinh" value="{{ $nhanvien->NgaySinh }}" required>
                        <div class="invalid-feedback d-block" id="error-NgaySinh"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="GioiTinh" class="form-label">Giới tính <span class="text-danger">*</span></label>
                        <select class="form-select" 
                                id="GioiTinh" name="GioiTinh" required>
                            <option value="">-- Chọn giới tính --</option>
                            <option value="Nam" {{ $nhanvien->GioiTinh === 'Nam' ? 'selected' : '' }}>Nam</option>
                            <option value="Nữ" {{ $nhanvien->GioiTinh === 'Nữ' ? 'selected' : '' }}>Nữ</option>
                        </select>
                        <div class="invalid-feedback d-block" id="error-GioiTinh"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="SoDienThoai" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" 
                               id="SoDienThoai" name="SoDienThoai" value="{{ $nhanvien->SoDienThoai }}" required>
                        <div class="invalid-feedback d-block" id="error-SoDienThoai"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="Email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" 
                               id="Email" name="Email" value="{{ $nhanvien->Email }}" required>
                        <div class="invalid-feedback d-block" id="error-Email"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="TrangThai" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select class="form-select" 
                                id="TrangThai" name="TrangThai" required>
                            <option value="">-- Chọn trạng thái --</option>
                            <option value="Hoạt động" {{ $nhanvien->TrangThai === 'Hoạt động' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="Đã nghỉ" {{ $nhanvien->TrangThai === 'Đã nghỉ' ? 'selected' : '' }}>Đã nghỉ</option>
                        </select>
                        <div class="invalid-feedback d-block" id="error-TrangThai"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="DiaChi" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" 
                           id="DiaChi" name="DiaChi" value="{{ $nhanvien->DiaChi }}" required>
                    <div class="invalid-feedback d-block" id="error-DiaChi"></div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="bi bi-calendar-event"></i> 
                            Ngày tạo: {{ date('d/m/Y H:i', strtotime($nhanvien->NgayTao)) }}
                        </small>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-secondary" onclick="goBack()">
                        <i class="bi bi-x-circle"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="bi bi-check-circle"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function goBack() {
    loadPage('/nhanvien');
}

async function submitForm(event) {
    event.preventDefault();
    
    const btnSubmit = document.getElementById('btnSubmit');
    const alertError = document.getElementById('alertError');
    
    // Xóa lỗi cũ
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    alertError.innerHTML = '';
    
    // Lấy dữ liệu form dưới dạng JSON
    const form = document.getElementById('formEditNhanVien');
    const formData = new FormData(form);
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });
    
    const nhanVienId = {{ $nhanvien->Id }};
    
    try {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';
        
        const response = await fetch(`/nhanvien/${nhanVienId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const responseData = await response.json();
        
        if (response.ok) {
            // Thành công - quay lại danh sách
            alertError.innerHTML = `<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> ${responseData.message || 'Cập nhật nhân viên thành công!'}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
            
            setTimeout(() => {
                loadPage('/nhanvien');
            }, 1000);
        } else {
            // Lỗi validation
            if (responseData.errors) {
                Object.keys(responseData.errors).forEach(field => {
                    const errorEl = document.getElementById(`error-${field}`);
                    if (errorEl) {
                        errorEl.textContent = responseData.errors[field][0];
                    }
                });
            }
            
            alertError.innerHTML = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> ${responseData.message || 'Có lỗi xảy ra, vui lòng kiểm tra lại!'}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
        }
    } catch (error) {
        console.error('Error:', error);
        alertError.innerHTML = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> Lỗi kết nối: ${error.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
    } finally {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="bi bi-check-circle"></i> Lưu thay đổi';
    }
}
</script>
