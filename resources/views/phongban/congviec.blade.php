
<div id="app-content">
<div class="container-fluid mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('phongban.index') }}">Phòng ban</a></li>
            <li class="breadcrumb-item active">Danh sách công việc</li>
        </ol>
    </nav>

    <h2 class="mb-4">Công việc trong phòng</h2>

    <div class="row">
        @foreach($congViecs as $cv)
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-start border-4 
                {{ $cv->TrangThai == 'Trễ hạn' ? 'border-danger' : ($cv->TrangThai == 'Sắp đến hạn' ? 'border-warning' : 'border-success') }}">
                <div class="card-body">
                    <h5 class="card-title">{{ $cv->TenCongViec }}</h5>
                    <p class="card-text text-muted small">{{ $cv->MoTa }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge {{ $cv->TrangThai == 'Trễ hạn' ? 'bg-danger' : ($cv->TrangThai == 'Sắp đến hạn' ? 'bg-warning' : 'bg-success') }}">
                            {{ $cv->TrangThai }}
                        </span>
                        <small class="text-muted">Hết hạn: {{ $cv->NgayKetThuc }}</small>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
</div>