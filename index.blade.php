<!-- resources/views/chamcong.blade.php -->

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Chấm Công</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-light">

<div class="container-fluid mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-primary mb-0">
            <i class="fas fa-clock me-2"></i>Quản Lý Chấm Công
        </h3>

        <div class="text-muted" id="realTimeClock">
            Đang tải thời gian...
        </div>
    </div>

    <!-- Checkin + Statistics -->
    <div class="row mb-4">

        <!-- Check-in Check-out -->
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center py-4">

                    <h5 class="text-muted mb-3">
                        Thao tác hôm nay
                    </h5>

                    <button
                        id="btnCheckIn"
                        class="btn btn-success btn-lg me-2"
                        onclick="handleCheckIn()"
                    >
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Check-in
                    </button>

                    <button
                        id="btnCheckOut"
                        class="btn btn-danger btn-lg"
                        onclick="handleCheckOut()"
                    >
                        <i class="fas fa-sign-out-alt me-2"></i>
                        Check-out
                    </button>

                    <small class="text-muted d-block mt-3">
                        * Giờ vào quy định: 08:00 | Giờ ra: 17:30
                    </small>

                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="col-md-6 col-lg-8 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">

                    <h5 class="text-muted mb-3">
                        Thống kê tháng này
                    </h5>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="bg-light rounded p-3 text-center">
                                <h4 class="text-success" id="statTongCong">0</h4>
                                <small>Tổng ngày công</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="bg-light rounded p-3 text-center">
                                <h4 class="text-warning" id="statTongTre">0</h4>
                                <small>Tổng phút trễ</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="bg-light rounded p-3 text-center">
                                <h4 class="text-info" id="statTongTangCa">0</h4>
                                <small>Giờ tăng ca</small>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- Table -->
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white">

            <div class="row align-items-center">

                <div class="col-md-3">
                    <h5 class="mb-0">Lịch sử chấm công</h5>
                </div>

                <div class="col-md-9 text-end">

                    <!-- Lọc theo mã nhân viên -->
                    <input
                        type="text"
                        id="filterMaNhanVien"
                        class="form-control d-inline w-auto"
                        placeholder="Mã nhân viên"
                    >

                    <!-- Lọc theo tháng -->
                    <select id="filterThang" class="form-select d-inline w-auto">
                        @for($i = 1; $i <= 12; $i++)
                            <option
                                value="{{ $i }}"
                                {{ $i == date('m') ? 'selected' : '' }}
                            >
                                Tháng {{ $i }}
                            </option>
                        @endfor
                    </select>

                    <!-- Lọc theo năm -->
                    <select id="filterNam" class="form-select d-inline w-auto">
                        <option value="2025">2025</option>
                        <option value="2026" selected>2026</option>
                    </select>

                    <!-- Nút lọc -->
                    <button
                        class="btn btn-primary"
                        onclick="loadChamCongData()"
                    >
                        <i class="fas fa-filter me-2"></i>
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
                        <td colspan="9" class="text-center p-4">
                            Đang tải dữ liệu...
                        </td>
                    </tr>
                </tbody>

            </table>

        </div>
    </div>

</div>

<script>

const currentNhanVienId = {{ auth()->user()->id ?? 1 }};

const csrfToken = document.querySelector(
    'meta[name="csrf-token"]'
).content;


/*
|--------------------------------------------------------------------------
| Đồng hồ thời gian thực
|--------------------------------------------------------------------------
*/

function updateClock() {
    const now = new Date();

    document.getElementById('realTimeClock').innerHTML =
        `${now.toLocaleDateString('vi-VN')} | ${now.toLocaleTimeString('vi-VN')}`;
}

setInterval(updateClock, 1000);
updateClock();


/*
|--------------------------------------------------------------------------
| Load dữ liệu bảng + lọc theo mã nhân viên
|--------------------------------------------------------------------------
*/

function loadChamCongData() {

    const maNhanVien = document.getElementById('filterMaNhanVien').value;
    const thang = document.getElementById('filterThang').value;
    const nam = document.getElementById('filterNam').value;
    const tbody = document.getElementById('tableData');

    tbody.innerHTML = `
        <tr>
            <td colspan="9" class="text-center p-4">
                Đang tải dữ liệu...
            </td>
        </tr>
    `;

    let url = `/api/cham-cong?thang=${thang}&nam=${nam}`;

    // Nếu có nhập mã nhân viên thì thêm vào URL
    if (maNhanVien.trim() !== '') {
        url += `&ma_nhan_vien=${maNhanVien}`;
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {

            let html = '';

            if (data.length === 0) {
                html = `
                    <tr>
                        <td colspan="9" class="text-center text-danger p-4">
                            Không có dữ liệu
                        </td>
                    </tr>
                `;
            } else {

                data.forEach(item => {

                    html += `
                        <tr>
                            <td>${item.Ngay}</td>
                            <td>${item.MaNhanVien ?? '--'}</td>
                            <td class="text-success">${item.GioVao ?? '--:--'}</td>
                            <td class="text-danger">${item.GioRa ?? '--:--'}</td>
                            <td>${item.SoPhutTre}</td>
                            <td>${item.SoGioLam}</td>
                            <td>${item.SoNgayCong}</td>
                            <td>${item.SoGioTangCa}</td>
                            <td>
                                ${
                                    item.LaNgayLe == 1
                                    ? '<span class="badge bg-warning">Ngày lễ</span>'
                                    : '<span class="badge bg-secondary">Ngày thường</span>'
                                }
                            </td>
                        </tr>
                    `;
                });
            }

            tbody.innerHTML = html;
        })
        .catch(error => {
            console.log(error);

            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center text-danger p-4">
                        Lỗi tải dữ liệu
                    </td>
                </tr>
            `;
        });
}


/*
|--------------------------------------------------------------------------
| Check In
|--------------------------------------------------------------------------
*/

function handleCheckIn() {

    if (!confirm("Bạn muốn Check-in?")) return;

    fetch('/api/cham-cong/check-in', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            NhanVienId: currentNhanVienId
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        loadChamCongData();
    })
    .catch(error => {
        console.log(error);
        alert("Lỗi hệ thống khi Check-in");
    });
}


/*
|--------------------------------------------------------------------------
| Check Out
|--------------------------------------------------------------------------
*/

function handleCheckOut() {

    if (!confirm("Bạn muốn Check-out?")) return;

    fetch('/api/cham-cong/check-out', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            NhanVienId: currentNhanVienId
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        loadChamCongData();
    })
    .catch(error => {
        console.log(error);
        alert("Lỗi hệ thống khi Check-out");
    });
}


/*
|--------------------------------------------------------------------------
| Load khi mở trang
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    loadChamCongData
);

</script>

<!-- Code injected by live-server -->
<script>
	// <![CDATA[  <-- For SVG support
	if ('WebSocket' in window) {
		(function () {
			function refreshCSS() {
				var sheets = [].slice.call(document.getElementsByTagName("link"));
				var head = document.getElementsByTagName("head")[0];
				for (var i = 0; i < sheets.length; ++i) {
					var elem = sheets[i];
					var parent = elem.parentElement || head;
					parent.removeChild(elem);
					var rel = elem.rel;
					if (elem.href && typeof rel != "string" || rel.length == 0 || rel.toLowerCase() == "stylesheet") {
						var url = elem.href.replace(/(&|\?)_cacheOverride=\d+/, '');
						elem.href = url + (url.indexOf('?') >= 0 ? '&' : '?') + '_cacheOverride=' + (new Date().valueOf());
					}
					parent.appendChild(elem);
				}
			}
			var protocol = window.location.protocol === 'http:' ? 'ws://' : 'wss://';
			var address = protocol + window.location.host + window.location.pathname + '/ws';
			var socket = new WebSocket(address);
			socket.onmessage = function (msg) {
				if (msg.data == 'reload') window.location.reload();
				else if (msg.data == 'refreshcss') refreshCSS();
			};
			if (sessionStorage && !sessionStorage.getItem('IsThisFirstTime_Log_From_LiveServer')) {
				console.log('Live reload enabled.');
				sessionStorage.setItem('IsThisFirstTime_Log_From_LiveServer', true);
			}
		})();
	}
	else {
		console.error('Upgrade your browser. This Browser is NOT supported WebSocket for Live-Reloading.');
	}
	// ]]>
</script>
</body>
</html>