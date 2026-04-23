<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-primary mb-0"><i class="fas fa-clock me-2"></i>Quản Lý Chấm Công</h3>
        <div class="current-time text-muted" id="realTimeClock">Đang tải thời gian...</div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center py-4">
                    <h5 class="card-title text-muted mb-3">Thao tác hôm nay</h5>
                    <div class="d-grid gap-2 d-md-block">
                        <button id="btnCheckIn" class="btn btn-success btn-lg px-4 me-md-2" onclick="handleCheckIn()">
                            <i class="fas fa-sign-in-alt me-2"></i>Check-in
                        </button>
                        <button id="btnCheckOut" class="btn btn-danger btn-lg px-4" onclick="handleCheckOut()">
                            <i class="fas fa-sign-out-alt me-2"></i>Check-out
                        </button>
                    </div>
                    <small class="text-muted mt-3 d-block">* Giờ vào quy định: 08:00 | Giờ ra: 17:30</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-8 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title text-muted mb-3">Thống kê tháng này</h5>
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded text-center">
                                <h4 class="text-success mb-1" id="statTongCong">0</h4>
                                <small class="text-muted">Tổng ngày công</small>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded text-center">
                                <h4 class="text-warning mb-1" id="statTongTre">0</h4>
                                <small class="text-muted">Tổng phút trễ</small>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded text-center">
                                <h4 class="text-info mb-1" id="statTongTangCa">0</h4>
                                <small class="text-muted">Giờ tăng ca</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h5 class="mb-0 text-secondary">Lịch sử chấm công</h5>
                </div>
                <div class="col-md-8">
                    <div class="d-flex justify-content-md-end gap-2">
                        <select class="form-select w-auto" id="filterThang">
                            <option value="1">Tháng 1</option>
                            <option value="2">Tháng 2</option>
                            <option value="3">Tháng 3</option>
                            <option value="4" selected>Tháng 4</option>
                            <option value="5">Tháng 5</option>
                            <option value="6">Tháng 6</option>
                            <option value="7">Tháng 7</option>
                            <option value="8">Tháng 8</option>
                            <option value="9">Tháng 9</option>
                            <option value="10">Tháng 10</option>
                            <option value="11">Tháng 11</option>
                            <option value="12">Tháng 12</option>
                        </select>
                        <select class="form-select w-auto" id="filterNam">
                            <option value="2025">Năm 2025</option>
                            <option value="2026" selected>Năm 2026</option>
                        </select>
                        <button class="btn btn-primary" onclick="loadChamCongData()">
                            <i class="fas fa-filter me-2"></i>Lọc
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Ngày</th>
                            <th>Giờ vào</th>
                            <th>Giờ ra</th>
                            <th>Trễ (Phút)</th>
                            <th>Số giờ làm</th>
                            <th>Công</th>
                            <th>Tăng ca</th>
                            <th class="text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody id="tableData">
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                Đang tải dữ liệu chấm công...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Đồng hồ thời gian thực
    function updateClock() {
        const now = new Date();
        document.getElementById('realTimeClock').innerHTML = `<i class="far fa-calendar-alt me-1"></i> ${now.toLocaleDateString('vi-VN')} | <i class="far fa-clock me-1"></i> ${now.toLocaleTimeString('vi-VN')}`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Giả lập ID nhân viên đang đăng nhập (Thực tế bạn sẽ lấy từ Auth của Laravel)
    const currentNhanVienId = 1; 

    // 2. Hàm gọi API tải dữ liệu lịch sử chấm công
    function loadChamCongData() {
        const thang = document.getElementById('filterThang').value;
        const nam = document.getElementById('filterNam').value;
        const tbody = document.getElementById('tableData');

        tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Đang tải dữ liệu...</td></tr>`;

        // Gọi API Laravel
        fetch(`/api/cham-cong?thang=${thang}&nam=${nam}`)
            .then(res => res.json())
            .then(data => {
                let html = '';
                if(data.length === 0) {
                    html = `<tr><td colspan="8" class="text-center py-4 text-danger">Không có dữ liệu chấm công trong tháng này!</td></tr>`;
                } else {
                    data.forEach(item => {
                        const gioVao = item.GioVao ? new Date(item.GioVao).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'}) : '--:--';
                        const gioRa = item.GioRa ? new Date(item.GioRa).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'}) : '--:--';
                        
                        html += `
                            <tr>
                                <td class="ps-3 fw-bold">${new Date(item.Ngay).toLocaleDateString('vi-VN')}</td>
                                <td class="text-success">${gioVao}</td>
                                <td class="text-danger">${gioRa}</td>
                                <td><span class="${item.SoPhutTre > 0 ? 'text-danger fw-bold' : 'text-muted'}">${item.SoPhutTre}</span></td>
                                <td>${item.SoGioLam || 0}h</td>
                                <td><span class="badge bg-success">${item.SoNgayCong}</span></td>
                                <td>${item.SoGioTangCa || 0}h</td>
                                <td class="text-center">
                                    ${item.LaNgayLe ? '<span class="badge bg-purple" style="background-color: #6f42c1;">Ngày Lễ</span>' : '<span class="badge bg-secondary">Ngày thường</span>'}
                                </td>
                            </tr>
                        `;
                    });
                }
                tbody.innerHTML = html;
            })
            .catch(err => {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger">Lỗi khi tải dữ liệu từ máy chủ!</td></tr>`;
            });
            
        // Gọi thêm API lấy thống kê tổng hợp (tự viết thêm logic để fill vào ô stat)
    }

    // 3. Xử lý Check-in
    function handleCheckIn() {
        if (!confirm('Bạn muốn thực hiện Check-in ngay bây giờ?')) return;

        fetch('/api/cham-cong/check-in', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ NhanVienId: currentNhanVienId })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            loadChamCongData(); // Load lại bảng
        })
        .catch(err => alert('Lỗi hệ thống khi check-in!'));
    }

    // 4. Xử lý Check-out
    function handleCheckOut() {
        if (!confirm('Bạn muốn thực hiện Check-out ngay bây giờ?')) return;

        fetch('/api/cham-cong/check-out', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ NhanVienId: currentNhanVienId })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            loadChamCongData(); // Load lại bảng
        })
        .catch(err => alert('Lỗi hệ thống khi check-out!'));
    }

    // Chạy hàm load dữ liệu lần đầu khi trang được nạp
    document.addEventListener('DOMContentLoaded', loadChamCongData);
</script>