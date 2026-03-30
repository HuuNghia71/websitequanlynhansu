<!DOCTYPE html>

<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý bảng lương</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div id="app-content">
    <div class="container mt-5">
        <h2 class="mb-4 text-center">💰 Quản lý bảng lương</h2>

    ``` 
    <!-- FILTER -->
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
                    <option value="tham_nien">Thâm niên</option>
                </select>
            </div>
            <div class="col-md-2">
                <select id="sort_order" class="form-select">
                    <option value="desc">Giảm dần</option>
                    <option value="asc">Tăng dần</option>
                </select>
            </div>
            <div class="col-md-1 d-grid">
                <button class="btn btn-primary" onclick="loadData()">🔍</button>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
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
                    </tr>
                </thead>
                <tbody id="table-body">
                </tbody>
            </table>
        </div>
    </div>
</div>
```

</div>

<script>
const API_URL = "http://127.0.0.1:8000/api/luong";

async function loadData() {
    let ten = document.getElementById("ten").value;
    let thang = document.getElementById("thang").value;
    let nam = document.getElementById("nam").value;
    let sort_by = document.getElementById("sort_by").value;
    let sort_order = document.getElementById("sort_order").value;

    let url = API_URL + "?";

    if (ten) url += `ten=${ten}&`;
    if (thang) url += `thang=${thang}&`;
    if (nam) url += `nam=${nam}&`;
    if (sort_by) url += `sort_by=${sort_by}&sort_order=${sort_order}`;

    try {
        let res = await fetch(url);
        let data = await res.json();

        let tbody = document.getElementById("table-body");
        tbody.innerHTML = "";

        data.data.forEach(item => {
            let row = `
                <tr class="text-center">
                    <td>${item.Id}</td>
                    <td>${item.nhan_vien?.Ten ?? ''}</td>
                    <td>${item.Thang}</td>
                    <td>${item.Nam}</td>
                    <td>${item.TongNgayCong}</td>
                    <td>${item.Thuong}</td>
                    <td>${item.Phat}</td>
                    <td class="text-success fw-bold">${formatMoney(item.TongLuong)}</td>
                </tr>
            `;
            tbody.innerHTML += row;
        });

    } catch (err) {
        console.error(err);
    }
}

function formatMoney(num) {
    return new Intl.NumberFormat('vi-VN').format(num) + ' đ';
}

// load lần đầu
loadData();
</script>

</body>
</html>
