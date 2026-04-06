<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý bảng lương</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Ép buộc ẩn hoàn toàn khi mới load trang */
        #editPanel {
            display: none;
            margin-bottom: 25px;
            border: 2px solid #ffc107;
            background-color: #fffdf5;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body class="bg-light">

    <div id="app-content">
        <div class="container mt-5">
            <h2 class="mb-4 text-center">💰 Quản lý bảng lương</h2>

            <div class="card p-3 mb-4 shadow-sm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" id="ten" class="form-control" placeholder="Tên nhân viên">
                    </div>
                    <div class="col-md-2">
                        <input type="number" id="thang" class="form-control" placeholder="Tháng">
                    </div>
                    <div class="col-md-2">
                        <input type="number" id="nam" class="form-control" placeholder="Năm">
                    </div>
                    <div class="col-md-2">
                        <select id="sort_by" class="form-select">
                            <option value="">-- Sắp xếp --</option>
                            <option value="tong_luong">Tổng lương</option>
                            <option value="ngay_cong">Ngày công</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="sort_order" class="form-select">
                            <option value="desc">Giảm dần</option>
                            <option value="asc">Tăng dần</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-grid"> 
                        <button class="btn btn-primary mb-1" onclick="loadData()">🔍</button>
                        <button class="btn btn-success" onclick="openAddForm()">➕</button>
                    </div>
                </div>
            </div>
            
            <div id="addPanel" class="card mb-4" style="display:none;">
                <div class="card-body">
                    <h5>📊 Tạo phiếu lương</h5>

                    <div class="row g-2">
                        <!-- Nhân viên -->
                        <div class="col-md-4">
                            <select id="select_nhanvien" class="form-select">
                                <option value="">-- Chọn nhân viên --</option>
                            </select>
                        </div>

                        <!-- Tháng -->
                        <div class="col-md-3">
                            <select id="select_thang" class="form-select"></select>
                        </div>

                        <!-- Năm -->
                        <div class="col-md-3">
                            <select id="select_nam" class="form-select"></select>
                        </div>

                        <!-- Nút -->
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" onclick="loadChamCong()">Chọn</button>
                        </div>
                    </div>

                    <!-- KẾT QUẢ -->
                    <div id="resultArea" class="mt-4"></div>
                </div>
            </div>

            <div id="editPanel" class="card" style="display: none;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="m-0 text-dark">✏️ Chỉnh sửa phiếu lương</h4>
                        <button type="button" class="btn-close" onclick="closePanel()"></button>
                    </div>
                    
                    <input type="hidden" id="edit_id">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Tháng</label>
                            <input type="number" id="edit_thang" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Năm</label>
                            <input type="number" id="edit_nam" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Ngày công</label>
                            <input type="number" id="edit_ngay_cong" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Thưởng</label>
                            <input type="number" id="edit_thuong" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Phạt</label>
                            <input type="number" id="edit_phat" class="form-control">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-success px-4" onclick="updateLuong()">💾 Lưu thay đổi</button>
                        <button class="btn btn-light border px-4" onclick="closePanel()">Hủy bỏ</button>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID</th>
                                <th>Tên</th>
                                <th>Tháng</th>
                                <th>Năm</th>
                                <th>Ngày công</th>
                                <th>Thưởng</th>
                                <th>Phạt</th>
                                <th>Tổng lương</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="table-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    const API_URL = "http://127.0.0.1:8000/api/luong";

    async function loadData() {
    // Lấy giá trị từ các ô input (lúc mới mở trang sẽ là rỗng "")
    let ten = document.getElementById("ten").value || "";
    let thang = document.getElementById("thang").value || "";
    let nam = document.getElementById("nam").value || "";
    let sort_by = document.getElementById("sort_by").value || "";
    let sort_order = document.getElementById("sort_order").value || "desc";

    // Xây dựng URL động: chỉ thêm tham số nếu có giá trị
    let params = new URLSearchParams();
    if (ten) params.append('ten', ten);
    if (thang) params.append('thang', thang);
    if (nam) params.append('nam', nam);
    if (sort_by) {
        params.append('sort_by', sort_by);
        params.append('sort_order', sort_order);
    }

    let url = `${API_URL}?${params.toString()}`;

    try {
        let res = await fetch(url);
        let data = await res.json();
        let tbody = document.getElementById("table-body");
        tbody.innerHTML = "";

        if (data.data && data.data.length > 0) {
            data.data.forEach(item => {
                tbody.innerHTML += `
                    <tr class="text-center align-middle">
                        <td>${item.Id}</td>
                        <td class="text-start fw-bold">${item.nhan_vien?.Ten ?? ''}</td>
                        <td>${item.Thang}</td>
                        <td>${item.Nam}</td>
                        <td>${item.TongNgayCong}</td>
                        <td>${item.Thuong}</td>
                        <td>${item.Phat}</td>
                        <td class="text-success fw-bold">${formatMoney(item.TongLuong)}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="editLuong(${item.Id})">✏️ Sửa</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteLuong(${item.Id})">🗑 Xóa</button>
                        </td>
                    </tr>`;
            });
        } else {
            tbody.innerHTML = `<tr><td colspan="9" class="text-center p-3">Không có dữ liệu hiển thị</td></tr>`;
        }
    } catch (err) { 
        console.error("Lỗi tải dữ liệu:", err); 
    }
}
function openAddForm() {
    document.getElementById("addPanel").style.display = "block";

    initDropdown();   // 👈 thêm dòng này
    loadNhanVien();   // 👈 thêm luôn cho chắc

    document.getElementById("resultArea").innerHTML = "";
}

function closeAddForm() {
    document.getElementById("addPanel").style.display = "none";
}


    function formatMoney(num) {
        return new Intl.NumberFormat('vi-VN').format(num) + ' đ';
    }

    function editLuong(id) {
        let row = [...document.querySelectorAll("#table-body tr")]
            .find(tr => tr.children[0].innerText.trim() == id);
        if (!row) return;

        // Điền dữ liệu
        document.getElementById("edit_id").value = id;
        document.getElementById("edit_thang").value = row.children[2].innerText.trim();
        document.getElementById("edit_nam").value = row.children[3].innerText.trim();
        document.getElementById("edit_ngay_cong").value = row.children[4].innerText.trim();
        document.getElementById("edit_thuong").value = row.children[5].innerText.trim();
        document.getElementById("edit_phat").value = row.children[6].innerText.trim();

        // HIỆN KHUNG
        let panel = document.getElementById("editPanel");
        panel.style.display = "block";
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    
    function closePanel() {
        let panel = document.getElementById("editPanel");
        panel.style.display = "none"; // ✅ đóng panel
    }

    async function updateLuong() {
        let id = document.getElementById("edit_id").value;
        let body = {
            Thang: document.getElementById("edit_thang").value,
            Nam: document.getElementById("edit_nam").value,
            TongNgayCong: document.getElementById("edit_ngay_cong").value,
            Thuong: document.getElementById("edit_thuong").value,
            Phat: document.getElementById("edit_phat").value
        };

        try {
            let res = await fetch(`${API_URL}/${id}`, {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(body)
            });
            let data = await res.json();
            if (data.success) {
                alert("✅ Cập nhật thành công");
                loadData();
                closePanel();
            } else {
                alert("❌ Cập nhật thất bại");
            }
        } catch (err) { alert("Lỗi server!"); }
    }

    async function deleteLuong(id) {
        if (!confirm("Bạn có chắc muốn xóa không?")) return;
        try {
            let res = await fetch(`${API_URL}/${id}`, { method: "DELETE" });
            let data = await res.json();
            if (data.success) {
                alert("Xóa thành công!");
                loadData();
            }
        } catch (err) { alert("Lỗi server!"); }
    }
    function initDropdown() {
        let thang = document.getElementById("select_thang");
        thang.innerHTML = `<option value="">-- Chọn tháng --</option>`;
        for (let i = 1; i <= 12; i++) {
            thang.innerHTML += `<option value="${i}">Tháng ${i}</option>`;
        }

        let nam = document.getElementById("select_nam");
        nam.innerHTML = `<option value="">-- Chọn năm --</option>`;
        for (let i = 2016; i <= 2026; i++) {
            nam.innerHTML += `<option value="${i}">${i}</option>`;
        }
    }

    async function loadNhanVien() {
        try {
            let res = await fetch("http://127.0.0.1:8000/api/nhanvien");
            let data = await res.json();

            console.log("DATA NHAN VIEN:", data); // 👈 debug cực quan trọng

            let select = document.getElementById("select_nhanvien");
            select.innerHTML = '<option value="">-- Chọn nhân viên --</option>';

            // ✅ FIX MẠNH NHẤT (cover mọi case)
            let list = [];

            if (Array.isArray(data)) {
                list = data;
            } else if (Array.isArray(data.data)) {
                list = data.data;
            } else if (Array.isArray(data.data?.data)) {
                list = data.data.data;
            }

            // 🚨 nếu vẫn không có
            if (list.length === 0) {
                console.warn("Không có dữ liệu nhân viên!");
                return;
            }

            list.forEach(nv => {
                select.innerHTML += `<option value="${nv.Id || nv.id}">${nv.Ten || nv.ten}</option>`;
            });

        } catch (err) {
            console.error("Lỗi loadNhanVien:", err);
        }
    }
async function loadChamCong() {
    let nhanvien_id = document.getElementById("select_nhanvien").value;
    let thang = document.getElementById("select_thang").value;
    let nam = document.getElementById("select_nam").value;

    if (!nhanvien_id || !thang || !nam) {
        alert("Vui lòng chọn đầy đủ!");
        return;
    }

    let res = await fetch(`http://127.0.0.1:8000/api/luong/chamcong?nhanvien_id=${nhanvien_id}&thang=${thang}&nam=${nam}`);
    let data = await res.json();

    // ✅ check sau khi có data
    if (!data.success) {
        alert("❌ Không lấy được chấm công");
        return;
    }

    let html = `
        <h6>Tổng ngày công: ${data.data.tong_ngay_cong}</h6>
        <h6>Tổng phút trễ: ${data.data.tong_phut_tre}</h6>

        <button class="btn btn-success mt-2" onclick="createLuong(${nhanvien_id}, ${thang}, ${nam})">
            💰 Tạo phiếu lương
        </button>

        <table class="table mt-3">
            <tr><th>Ngày</th><th>Giờ làm</th><th>Phút trễ</th></tr>
    `;

    if (data.data && data.data.cham_cong) {
        data.data.cham_cong.forEach(c => {
            html += `
                <tr>
                    <td>${c.Ngay}</td>
                    <td>${c.SoGioLam}</td>
                    <td>${c.SoPhutTre}</td>
                </tr>
            `;
        });
    }

    html += `</table>`;

    document.getElementById("resultArea").innerHTML = html;
}
async function createLuong(nhanvien_id, thang, nam) {
    let body = {
        NhanVienId: nhanvien_id,
        Thang: thang,
        Nam: nam,
        LuongCoBan: 10000000
    };

    let res = await fetch("http://127.0.0.1:8000/api/luong", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(body)
    });

    let data = await res.json();

    if (data.success) {
        alert("✅ Tạo lương thành công");
        loadData();
        closeAddForm(); 
    } else {
        alert("❌ Lỗi tạo lương");
    }
}
    // Thay vì chỉ ghi loadData(); hãy ghi như sau:
window.addEventListener('DOMContentLoaded', (event) => {
    loadData();
    loadNhanVien();   // 👈 thêm
    initDropdown();   // 👈 thêm
});
    </script>
</body>
</html>
