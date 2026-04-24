<!-- resources/views/chamcong/index.blade.php -->

<div id="app-content">

    <!-- CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <div class="container-fluid mt-4">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-primary mb-0">
                <i class="fas fa-clock me-2"></i>
                Quản Lý Chấm Công
            </h3>

            <div class="text-muted fw-semibold" id="realTimeClock">
                Đang tải thời gian...
            </div>
        </div>

        <!-- CHECK IN + THỐNG KÊ -->
        <div class="row mb-4">

            <!-- CHECK IN -->
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center py-4">

                        <h5 class="text-muted mb-3">Thao tác hôm nay</h5>

                        <button id="btnCheckIn"
                            class="btn btn-success btn-lg me-2"
                            onclick="handleCheckIn()">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Check-in
                        </button>

                        <button id="btnCheckOut"
                            class="btn btn-danger btn-lg"
                            onclick="handleCheckOut()">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Check-out
                        </button>

                        <small class="text-muted d-block mt-3">
                            * Giờ vào quy định: 08:00 | Giờ ra: 17:30
                        </small>

                    </div>
                </div>
            </div>

            <!-- THỐNG KÊ -->
            <div class="col-md-6 col-lg-8 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">

                        <h5 class="text-muted mb-3">Thống kê tháng này</h5>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="bg-light rounded p-3 text-center">
                                    <h4 id="statTongCong" class="text-success fw-bold">0</h4>
                                    <small>Tổng ngày công</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="bg-light rounded p-3 text-center">
                                    <h4 id="statTongTre" class="text-warning fw-bold">0</h4>
                                    <small>Tổng phút trễ</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="bg-light rounded p-3 text-center">
                                    <h4 id="statTongTangCa" class="text-info fw-bold">0</h4>
                                    <small>Giờ tăng ca</small>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- TABLE -->
        <div class="card shadow-sm border-0">

            <div class="card-header bg-white">

                <div class="row align-items-center">

                    <div class="col-md-3">
                        <h5 class="mb-0">Lịch sử chấm công</h5>
                    </div>

                    <div class="col-md-9 text-end">

                        <!-- LỌC MÃ NHÂN VIÊN -->
                        <input
                            type="text"
                            id="filterMaNhanVien"
                            class="form-control d-inline w-auto"
                            placeholder="VD: #1 hoặc 1">

                        <!-- LỌC THÁNG -->
                        <select id="filterThang" class="form-select d-inline w-auto">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                    Tháng {{ $i }}
                                </option>
                            @endfor
                        </select>

                        <!-- LỌC NĂM -->
                        <select id="filterNam" class="form-select d-inline w-auto">
                            @for($y = 2025; $y <= 2030; $y++)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>
                                    Năm {{ $y }}
                                </option>
                            @endfor
                        </select>

                        <button
                            class="btn btn-primary"
                            onclick="loadChamCongData()">
                            Lọc
                        </button>

                    </div>

                </div>

            </div>

            <div class="card-body p-0">

                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ngày</th>
                            <th>Mã NV</th>
                            <th>Tên nhân viên</th>
                            <th>Giờ vào</th>
                            <th>Giờ ra</th>
                            <th>Trễ (Phút)</th>
                            <th>Số giờ làm</th>
                            <th>Công</th>
                            <th>Tăng ca</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>

                    <tbody id="tableData">
                        <tr>
                            <td colspan="10" class="text-center p-4">
                                Đang tải dữ liệu...
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>

        </div>

    </div>
</div>

<script>

let currentNhanVienId = document.body.dataset.userId || 1;

let csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

/*
|--------------------------------------------------------------------------
| CLOCK
|--------------------------------------------------------------------------
*/

function updateClock() {
    const now = new Date();

    document.getElementById("realTimeClock").innerHTML =
        now.toLocaleDateString("vi-VN") + " | " +
        now.toLocaleTimeString("vi-VN");
}

setInterval(updateClock, 1000);
updateClock();


/*
|--------------------------------------------------------------------------
| FORMAT TODAY YYYY-MM-DD
|--------------------------------------------------------------------------
*/

function getTodayVN() {
    const now = new Date();
    const offset = 7 * 60;

    const local = new Date(
        now.getTime() +
        (offset - now.getTimezoneOffset()) * 60000
    );

    return local.toISOString().split("T")[0];
}


/*
|--------------------------------------------------------------------------
| UPDATE BUTTON STATE
|--------------------------------------------------------------------------
*/

function updateCheckButtons(data) {
    const btnIn = document.getElementById("btnCheckIn");
    const btnOut = document.getElementById("btnCheckOut");

    const today = getTodayVN();

    const record = data.find(item =>
        item.NhanVienId == currentNhanVienId &&
        item.Ngay === today
    );

    btnIn.disabled = false;
    btnOut.disabled = false;

    if (record) {
        btnIn.disabled = true;

        if (record.GioRa) {
            btnOut.disabled = true;
        }
    }

    btnIn.classList.toggle("btn-secondary", btnIn.disabled);
    btnOut.classList.toggle("btn-secondary", btnOut.disabled);
}


/*
|--------------------------------------------------------------------------
| LOAD DATA
|--------------------------------------------------------------------------
*/

function loadChamCongData() {
    const maNhanVien = document
        .getElementById("filterMaNhanVien")
        .value
        .trim();

    const thang = document
        .getElementById("filterThang")
        .value;

    const nam = document
        .getElementById("filterNam")
        .value;

    let url = `/api/cham-cong?thang=${thang}&nam=${nam}`;

    if (maNhanVien !== "") {
        url += `&ma_nhan_vien=${encodeURIComponent(maNhanVien)}`;
    }

    fetch(url)
        .then(res => res.json())
        .then(data => {

            let html = "";
            let tongCong = 0;
            let tongTre = 0;
            let tongTangCa = 0;

            if (!data.length) {
                html = `
                    <tr>
                        <td colspan="10" class="text-center text-danger p-4">
                            Không có dữ liệu chấm công
                        </td>
                    </tr>
                `;
            } else {

                data.forEach(item => {

                    let tre = Math.max(
                        0,
                        parseInt(item.SoPhutTre || 0)
                    );

                    let gioLam = Math.max(
                        0,
                        parseFloat(item.SoGioLam || 0)
                    );

                    let tangCa = Math.max(
                        0,
                        parseFloat(item.SoGioTangCa || 0)
                    );

                    let ngayCong = parseFloat(
                        item.SoNgayCong || 0
                    );

                    tongCong += ngayCong;
                    tongTre += tre;
                    tongTangCa += tangCa;

                    html += `
                        <tr>
                            <td>${item.Ngay}</td>

                            <td>
                                <span class="fw-bold text-dark">
                                    #${item.NhanVienId}
                                </span>
                            </td>

                            <td class="fw-bold text-primary">
                                ${
                                    item.TenNhanVien
                                    ?? item.nhan_vien?.Ten
                                    ?? 'Chưa xác định'
                                }
                            </td>

                            <td>
                                ${
                                    item.GioVao
                                    ? item.GioVao.split(' ')[1]
                                    : '--'
                                }
                            </td>

                            <td>
                                ${
                                    item.GioRa
                                    ? item.GioRa.split(' ')[1]
                                    : '--'
                                }
                            </td>

                            <td class="${
                                tre > 0
                                ? 'text-danger fw-bold'
                                : ''
                            }">
                                ${tre}
                            </td>

                            <td>
                                ${gioLam.toFixed(2)}h
                            </td>

                            <td>
                                <span class="badge bg-success">
                                    ${ngayCong.toFixed(2)}
                                </span>
                            </td>

                            <td>
                                ${tangCa.toFixed(2)}h
                            </td>

                            <td>
                                ${
                                    item.LaNgayLe == 1
                                    ? `
                                        <span class="badge bg-warning text-dark">
                                            Ngày lễ
                                        </span>
                                      `
                                    : `
                                        <span class="badge bg-secondary">
                                            Ngày thường
                                        </span>
                                      `
                                }
                            </td>
                        </tr>
                    `;
                });
            }

            document.getElementById("tableData").innerHTML = html;

            document.getElementById("statTongCong").innerText =
                tongCong.toFixed(2);

            document.getElementById("statTongTre").innerText =
                tongTre;

            document.getElementById("statTongTangCa").innerText =
                tongTangCa.toFixed(2);

            updateCheckButtons(data);
        })
        .catch(error => {
            console.error(error);

            document.getElementById("tableData").innerHTML = `
                <tr>
                    <td colspan="10" class="text-center text-danger p-4">
                        Lỗi tải dữ liệu
                    </td>
                </tr>
            `;
        });
}


/*
|--------------------------------------------------------------------------
| CHECK IN
|--------------------------------------------------------------------------
*/

function handleCheckIn() {
    fetch("/api/cham-cong/check-in", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({
            NhanVienId: currentNhanVienId
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        loadChamCongData();
    })
    .catch(error => {
        console.error(error);
        alert("Lỗi check-in");
    });
}


/*
|--------------------------------------------------------------------------
| CHECK OUT
|--------------------------------------------------------------------------
*/

function handleCheckOut() {
    fetch("/api/cham-cong/check-out", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({
            NhanVienId: currentNhanVienId
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        loadChamCongData();
    })
    .catch(error => {
        console.error(error);
        alert("Lỗi check-out");
    });
}


/*
|--------------------------------------------------------------------------
| AUTO LOAD
|--------------------------------------------------------------------------
*/

loadChamCongData();

</script>