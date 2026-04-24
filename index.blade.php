<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý công việc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .sidebar {
            height: 100vh;
            background: #1f2937;
            color: white;
            box-shadow: 2px 0 5px rgba(0,0,0,0.2);
        }

        .menu-item {
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: 0.2s;
        }

        .menu-item:hover {
            background: #374151;
        }

        .menu-item.active {
            background: #2563eb;
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-2 sidebar p-3">
            <h4 class="mb-4">💼 HR System</h4>

            <div class="menu-item" onclick="setActive(this); loadPage('/nhanvien')">
                <i class="bi bi-people"></i>
                <span>Nhân viên</span>
            </div>

            <div class="menu-item" onclick="setActive(this); loadPage('/phongban')">
                <i class="bi bi-building"></i>
                <span>Phòng ban</span>
            </div>

            <div class="menu-item active" onclick="setActive(this); loadPage('/congviec')">
                <i class="bi bi-list-task"></i>
                <span>Công việc</span>
            </div>

            <div class="menu-item" onclick="setActive(this); loadPage('/chamcong')">
                <i class="bi bi-clock-history"></i>
                <span>Chấm công</span>
            </div>

            <div class="menu-item" onclick="setActive(this); loadPage('/luong')">
                <i class="bi bi-cash-stack"></i>
                <span>Lương</span>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="col-10 p-4" id="content">
            <div id="app-content">
                <div class="container mt-3">
                    <h2 class="mb-4">📋 Quản lý công việc</h2>

                    <!-- Form thêm / sửa công việc -->
                    <div class="card p-3 mb-4 shadow-sm">
                        <h5>Thêm / sửa công việc</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Tên công việc</label>
                                <input type="text" id="task-title" class="form-control" placeholder="Ví dụ: Kiểm thử chức năng">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Mô tả</label>
                                <input type="text" id="task-desc" class="form-control" placeholder="Mô tả công việc">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ngày bắt đầu</label>
                                <input type="date" id="task-start" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ngày kết thúc</label>
                                <input type="date" id="task-end" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Trạng thái</label>
                                <select id="task-status" class="form-select">
                                    <option value="Chua bat dau">Chưa bắt đầu</option>
                                    <option value="Dang thuc hien">Đang thực hiện</option>
                                    <option value="Sap den han">Sắp đến hạn</option>
                                    <option value="Tre">Trễ</option>
                                    <option value="Hoan thanh">Hoàn thành</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nhân viên</label>
                                <select id="task-employee" class="form-select">
                                    <option value="">Chọn nhân viên</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mã phòng ban</label>
                                <input type="number" id="task-department" class="form-control" placeholder="ID phòng ban">
                            </div>
                            <div class="col-md-3 d-grid align-self-end">
                                <button class="btn btn-success" onclick="saveTask()">Lưu công việc</button>
                                <button class="btn btn-secondary mt-2" onclick="clearForm()">Xóa form</button>
                            </div>
                        </div>
                    </div>

                    <!-- Bộ lọc và sắp xếp -->
                    <div class="card p-3 mb-4 shadow-sm">
                        <h5>Bộ lọc</h5>
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Tìm theo tên</label>
                                <input type="text" id="filter-text" class="form-control" placeholder="Nhập tên công việc">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Trạng thái</label>
                                <select id="filter-status" class="form-select">
                                    <option value="">Tất cả</option>
                                    <option value="Chua bat dau">Chưa bắt đầu</option>
                                    <option value="Dang thuc hien">Đang thực hiện</option>
                                    <option value="Sap den han">Sắp đến hạn</option>
                                    <option value="Tre">Trễ</option>
                                    <option value="Hoan thanh">Hoàn thành</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nhân viên</label>
                                <select id="filter-employee" class="form-select">
                                    <option value="">Tất cả nhân viên</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Sắp xếp</label>
                                <div class="d-flex gap-2">
                                    <select id="filter-sort-by" class="form-select">
                                        <option value="created">Ngày tạo</option>
                                        <option value="due_date">Ngày hết hạn</option>
                                    </select>
                                    <select id="filter-sort-order" class="form-select">
                                        <option value="desc">Giảm dần</option>
                                        <option value="asc">Tăng dần</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 d-grid">
                                <button class="btn btn-primary" onclick="loadData()">Lấy danh sách</button>
                            </div>
                        </div>
                    </div>

                    <!-- Bảng công việc -->
                    <div class="card shadow-sm">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên</th>
                                        <th>Mô tả</th>
                                        <th>Phòng ban</th>
                                        <th>Ngày bắt đầu</th>
                                        <th>Ngày kết thúc</th>
                                        <th>Trạng thái</th>
                                        <th>Hạn</th>
                                        <th>Nhân viên</th>
                                        <th>File</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <input type="file" id="upload-file" style="display:none" onchange="handleFileChange(event)">
        </div>

    </div>
</div>

<script>
function loadPage(url) {
    fetch(url)
        .then(res => res.text())
        .then(html => {
            let parser = new DOMParser();
            let doc = parser.parseFromString(html, 'text/html');
            let content = doc.querySelector('#app-content');

            document.getElementById('content').innerHTML = content.innerHTML;

            // 👉 chạy lại script nếu có
            let scripts = doc.querySelectorAll("script");
            scripts.forEach(oldScript => {
                let newScript = document.createElement("script");
                newScript.text = oldScript.text;
                document.body.appendChild(newScript);
            });
        });
}

function setActive(element) {
    document.querySelectorAll('.menu-item').forEach(item => {
        item.classList.remove('active');
    });
    element.classList.add('active');
}

// 👉 load mặc định trang nhân viên
window.onload = function () {
    loadPage('/nhanvien');
}

const API_URL = '/api/congviec';
let currentEditId = null; // Id công việc đang edit
let currentUploadId = null; // Id công việc đang upload file

// Hàm tải danh sách nhân viên và điền vào select
async function loadEmployees() {
    try {
        let res = await fetch('/api/nhanvien');
        let json = await res.json();
        
        if (json.success && json.data) {
            let employees = json.data;
            let selectTask = document.getElementById('task-employee');
            let selectFilter = document.getElementById('filter-employee');
            
            if (selectTask) {
                selectTask.innerHTML = '<option value="">Chọn nhân viên</option>';
                employees.forEach(emp => {
                    let option = document.createElement('option');
                    option.value = emp.Id;
                    option.textContent = emp.Ten;
                    selectTask.appendChild(option);
                });
            }
            
            if (selectFilter) {
                selectFilter.innerHTML = '<option value="">Tất cả nhân viên</option>';
                employees.forEach(emp => {
                    let option = document.createElement('option');
                    option.value = emp.Id;
                    option.textContent = emp.Ten;
                    selectFilter.appendChild(option);
                });
            }
        }
    } catch (error) {
        console.error('Lỗi tải danh sách nhân viên:', error);
    }
}


async function loadData() {
    let q = document.getElementById('filter-text').value;
    let status = document.getElementById('filter-status').value;
    let employee = document.getElementById('filter-employee').value;
    let sortBy = document.getElementById('filter-sort-by').value;
    let sortOrder = document.getElementById('filter-sort-order').value;

    let url = new URL(API_URL, window.location.origin);
    if (q) url.searchParams.set('q', q);
    if (status) url.searchParams.set('trang_thai', status);
    if (employee) url.searchParams.set('nhan_vien_id', employee);
    url.searchParams.set('sort_by', sortBy);
    url.searchParams.set('sort_order', sortOrder);

    try {
        let res = await fetch(url.toString());
        let json = await res.json();
        let tbody = document.getElementById('table-body');
        tbody.innerHTML = '';

        json.data.forEach(item => {
            let daysText = item.days_remaining === null ? '' : (item.days_remaining < 0 ? 'Quá hạn ' + Math.abs(item.days_remaining) + ' ngày' : item.days_remaining === 0 ? 'Hết hạn hôm nay' : item.days_remaining + ' ngày');
            let attachText = item.attachment_count ? item.attachment_count + ' file' : 'Chưa có';

            let row = `
                <tr class="text-center align-middle">
                    <td>${item.Id}</td>
                    <td>${item.TenCongViec || ''}</td>
                    <td>${item.MoTa || ''}</td>
                    <td>${item.phong_ban?.TenPhongBan || item.PhongBanId || ''}</td>
                    <td>${item.NgayBatDau || ''}</td>
                    <td>${item.NgayKetThuc || ''}</td>
                    <td>${item.TrangThai || ''}</td>
                    <td>${daysText}</td>
                    <td>${item.employee_names || ''}</td>
                    <td>${attachText}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="setForm(${item.Id})">Sửa</button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteTask(${item.Id})">Xóa</button>
                        <button class="btn btn-sm btn-outline-success" onclick="selectFileForTask(${item.Id})">Upload</button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    } catch (error) {
        console.error('Lỗi gọi API:', error);
    }
}

// Điền dữ liệu lên form khi bấm sửa
function setForm(id) {
    fetch(`${API_URL}/${id}`)
        .then(res => res.json())
        .then(json => {
            if (!json.success) {
                alert(json.message || 'Không lấy được dữ liệu');
                return;
            }

            const item = json.data;
            currentEditId = item.Id;
            document.getElementById('task-title').value = item.TenCongViec || '';
            document.getElementById('task-desc').value = item.MoTa || '';
            document.getElementById('task-start').value = item.NgayBatDau || '';
            document.getElementById('task-end').value = item.NgayKetThuc || '';
            document.getElementById('task-status').value = item.TrangThai || 'Chua bat dau';
            document.getElementById('task-department').value = item.PhongBanId || '';
            document.getElementById('task-employee').value = item.nhanViens?.length ? item.nhanViens[0].Id : '';
        });
}

// Reset form sau khi thêm hoặc sửa xong
function clearForm() {
    currentEditId = null;
    document.getElementById('task-title').value = '';
    document.getElementById('task-desc').value = '';
    document.getElementById('task-start').value = '';
    document.getElementById('task-end').value = '';
    document.getElementById('task-status').value = 'Chua bat dau';
    document.getElementById('task-department').value = '';
    document.getElementById('task-employee').value = '';
}

// Thêm hoặc cập nhật công việc
async function saveTask() {
    let payload = {
        TenCongViec: document.getElementById('task-title').value,
        MoTa: document.getElementById('task-desc').value,
        NgayBatDau: document.getElementById('task-start').value,
        NgayKetThuc: document.getElementById('task-end').value,
        TrangThai: document.getElementById('task-status').value,
        PhongBanId: document.getElementById('task-department').value,
        NhanVienId: document.getElementById('task-employee').value,
    };

    let method = 'POST';
    let url = API_URL;

    if (currentEditId) {
        method = 'PUT';
        url = `${API_URL}/${currentEditId}`;
    }

    try {
        let res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });
        let json = await res.json();

        if (!json.success) {
            alert(json.message || 'Lỗi không xác định');
            return;
        }

        alert(json.message);
        clearForm();
        loadData();
    } catch (error) {
        console.error('Lỗi lưu công việc:', error);
    }
}

// Xóa công việc
async function deleteTask(id) {
    if (!confirm('Bạn có chắc muốn xóa công việc này không?')) {
        return;
    }

    try {
        let res = await fetch(`${API_URL}/${id}`, { method: 'DELETE' });
        let json = await res.json();
        if (!json.success) {
            alert(json.message || 'Xóa thất bại');
            return;
        }

        alert(json.message);
        loadData();
    } catch (error) {
        console.error('Lỗi xóa công việc:', error);
    }
}

// Chọn file để upload cho công việc
function selectFileForTask(id) {
    currentUploadId = id;
    document.getElementById('upload-file').value = '';
    document.getElementById('upload-file').click();
}

// Xử lý upload file chọn xong
async function handleFileChange(event) {
    let file = event.target.files[0];
    if (!file || !currentUploadId) {
        return;
    }

    let formData = new FormData();
    formData.append('attachment', file);

    try {
        let res = await fetch(`${API_URL}/${currentUploadId}/files`, {
            method: 'POST',
            body: formData,
        });
        let json = await res.json();

        if (!json.success) {
            alert(json.message || 'Upload thất bại');
            return;
        }

        alert(json.message);
        loadData();
    } catch (error) {
        console.error('Lỗi upload file:', error);
    }
}

clearForm();
loadEmployees();
loadData();
</script>

</body>
</html>
