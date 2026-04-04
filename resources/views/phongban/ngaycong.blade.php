<div id="app-content">
<div class="container-fluid mt-4">
    <h3>Bảng công nhân viên - Tháng {{ $thang }}/{{ $nam }}</h3>
    
    <form action="{{ route('phongban.ngaycong', request()->id) }}" method="GET" class="row g-3 mb-4">
        <div class="col-auto">
            <select name="thang" class="form-select">
                @for($m=1; $m<=12; $m++)
                    <option value="{{ $m }}" {{ $thang == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                @endfor
            </select>
        </div>
        <div class="col-auto">
            <input type="number" name="nam" class="form-control" value="{{ $nam }}" placeholder="Năm">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Lọc</button>
        </div>
    </form>

    <div class="table-responsive bg-white p-3 shadow-sm rounded">
        <table class="table table-bordered table-striped text-center">
            <thead class="table-dark">
                <tr>
                    <th>Nhân viên</th>
                    <th>Ngày</th>
                    <th>Giờ vào</th>
                    <th>Giờ ra</th>
                    <th>Công</th>
                    <th>Tăng ca</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chamCongs as $cc)
                <tr>
                    <td class="text-start">{{ $cc->nhanVien->Ten }}</td>
                    <td>{{ $cc->Ngay }}</td>
                    <td>{{ $cc->GioVao }}</td>
                    <td>{{ $cc->GioRa }}</td>
                    <td><span class="fw-bold text-success">{{ $cc->SoNgayCong }}</span></td>
                    <td>{{ $cc->SoGioTangCa }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">Không có dữ liệu chấm công cho tháng này.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>