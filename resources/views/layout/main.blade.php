<!DOCTYPE html>
<html>
<head>
    <title>HR Management</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
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

            <div class="menu-item active" onclick="setActive(this); loadPage('/nhanvien')">
                <i class="bi bi-people"></i>
                <span>Nhân viên</span>
            </div>

            <div class="menu-item" onclick="setActive(this); loadPage('/phongban')">
                <i class="bi bi-building"></i>
                <span>Phòng ban</span>
            </div>

            <div class="menu-item" onclick="setActive(this); loadPage('/congviec')">
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
            <!-- nội dung sẽ load ở đây -->
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
</script>

</body>
</html>