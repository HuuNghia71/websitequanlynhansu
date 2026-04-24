<div id="app-content">
    <div class="container-fluid mt-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary"><i class="bi bi-building me-2"></i>Quản lý Phòng Ban</h2>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalThemPhongBan">
                <i class="bi bi-plus-lg me-1"></i> Thêm phòng ban
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow border-0 mb-4">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tên Phòng</th>
                            <th>Mô tả</th>
                            <th>Trưởng phòng</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($phongBans as $pb)
                        <tr>
                            <td>{{ $pb->Id }}</td>
                            <td><strong>{{ $pb->TenPhong }}</strong></td>
                            <td>{{ $pb->MoTa }}</td>
                            <td>
                                @if($pb->truongPhong)
                                    <span class="badge bg-info text-dark">{{ $pb->truongPhong->Ten }}</span>
                                @else
                                    <span class="text-muted fst-italic">Chưa có</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" onclick="loadPage('/phongban/{{ $pb->Id }}/cong-viec')" title="Xem công việc">
                                    <i class="bi bi-list-task"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" onclick="loadPage('/phongban/{{ $pb->Id }}/ngay-cong')" title="Xem ngày công">
                                    <i class="bi bi-calendar-check"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="Sửa" data-bs-toggle="modal" data-bs-target="#modalSua{{ $pb->Id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Phân công nhân viên" data-bs-toggle="modal" data-bs-target="#modalPhanCong{{ $pb->Id }}">
                                    <i class="bi bi-person-plus"></i>
                                </button>
                                
                                <form action="{{ route('phongban.destroy', $pb->Id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa phòng này?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Chưa có dữ liệu phòng ban.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div> @foreach($phongBans as $pb)
 <div class="modal fade" id="modalSua{{ $pb->Id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Sửa Phòng Ban: {{ $pb->TenPhong }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form onsubmit="suaPhongBan(event, {{ $pb->Id }})">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên phòng ban <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="TenPhong" value="{{ $pb->TenPhong }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả</label>
                            <textarea class="form-control" name="MoTa" rows="3">{{ $pb->MoTa }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i> Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <div class="modal fade" id="modalThemPhongBan" tabindex="-1" aria-labelledby="modalThemPhongBanLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalThemPhongBanLabel">
                        <i class="bi bi-plus-circle me-2"></i>Thêm Phòng Ban Mới
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form onsubmit="luuPhongBan(event)">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="TenPhong" class="form-label fw-bold">Tên phòng ban <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="TenPhong" name="TenPhong" placeholder="VD: Phòng Nhân sự" required>
                        </div>
                        <div class="mb-3">
                            <label for="MoTa" class="form-label fw-bold">Mô tả chi tiết</label>
                            <textarea class="form-control" id="MoTa" name="MoTa" rows="3" placeholder="Nhập mô tả về chức năng của phòng ban này..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save me-1"></i> Lưu lại
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div> <script>
// Thêm phòng ban mới
function luuPhongBan(event) {
    event.preventDefault();
    let form = event.target;
    let formData = new FormData(form);

    fetch("{{ route('phongban.store') }}", {
        method: "POST",
        body: formData,
        headers: { "X-Requested-With": "XMLHttpRequest" }
    })
    .then(response => {
        if (response.ok) {
            dongModal('modalThemPhongBan');
            loadPage('/phongban');
        } else {
            alert("Lỗi! Không thể lưu dữ liệu.");
        }
    })
    .catch(error => { alert("Lỗi kết nối mạng."); });
}

// Phân công nhân viên
function phanCongNhanVien(event, phongBanId) {
    event.preventDefault();
    let form = event.target;
    let formData = new FormData(form);

    fetch(`/phongban/${phongBanId}/phan-cong`, {
        method: "POST",
        body: formData,
        headers: { "X-Requested-With": "XMLHttpRequest" }
    })
    .then(async response => {
        let result = await response.json();
        if (response.ok) {
            alert(result.message);
            dongModal(`modalPhanCong${phongBanId}`);
            loadPage('/phongban');
        } else {
            alert("Lỗi: " + result.message);
        }
    })
    .catch(error => { 
        console.error(error);
        alert("Lỗi kết nối mạng."); 
    });
}

// Hàm hỗ trợ tắt Modal an toàn cho SPA
function dongModal(modalId) {
    let modalElement = document.getElementById(modalId);
    if(modalElement) {
        let modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) { modal.hide(); } 
        else {
            modalElement.classList.remove('show');
            modalElement.style.display = 'none';
        }
    }
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style = '';
}

// Cập nhật phòng ban
function suaPhongBan(event, phongBanId) {
    event.preventDefault();
    let form = event.target;
    let formData = new FormData(form);

    fetch(`/phongban/${phongBanId}/sua`, {
        method: "POST", // Laravel sẽ tự nhận diện @method('PUT') từ form
        body: formData,
        headers: { "X-Requested-With": "XMLHttpRequest" }
    })
    .then(response => {
        if (response.ok) {
            dongModal(`modalSua${phongBanId}`); // Đóng modal hiện tại
            loadPage('/phongban');              // Tải lại danh sách
        } else {
            alert("Lỗi! Không thể cập nhật dữ liệu.");
        }
    })
    .catch(error => { 
        console.error(error);
        alert("Lỗi kết nối mạng."); 
    });
}
</script>