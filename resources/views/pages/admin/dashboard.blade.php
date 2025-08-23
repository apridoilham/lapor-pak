@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<style>
    .stat-card-dashboard {
        border: 1px solid #e3e6f0;
        border-radius: .75rem;
        padding: 1.25rem;
        transition: all 0.3s ease;
        border-bottom-width: 4px;
    }
    .stat-card-dashboard:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.075)!important;
    }
    .stat-card-dashboard.border-bottom-dark { border-bottom-color: #5a5c69; }
    .stat-card-dashboard.border-bottom-primary { border-bottom-color: #4e73df; }
    .stat-card-dashboard.border-bottom-warning { border-bottom-color: #f6c23e; }
    .stat-card-dashboard.border-bottom-success { border-bottom-color: #1cc88a; }
    .stat-card-dashboard.border-bottom-danger { border-bottom-color: #e74a3b; }
    .stat-card-dashboard.border-bottom-info { border-bottom-color: #36b9cc; }

    .stat-card-dashboard .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #fff;
        flex-shrink: 0;
    }
    .stat-card-dashboard .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #343a40;
    }
    .stat-card-dashboard .stat-label {
        font-size: 0.8rem;
        color: #858796;
        text-transform: uppercase;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-900 font-weight-bold">Selamat Datang Kembali, {{ Auth::user()->name }}!</h1>
            <p class="mb-0 text-gray-600">Berikut adalah ringkasan aktivitas aplikasi Anda.</p>
        </div>
    </div>

    {{-- Menggunakan grid 6 kolom di layar besar, dan 2 di layar kecil --}}
    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-6">
        <div class="col mb-4">
            <div class="card stat-card-dashboard h-100 shadow-sm border-bottom-dark">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-dark mr-3"><i class="fas fa-file-alt"></i></div>
                    <div>
                        <div class="stat-value">{{ $totalReports }}</div>
                        <div class="stat-label">Total Laporan</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col mb-4">
            <div class="card stat-card-dashboard h-100 shadow-sm border-bottom-primary">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-primary mr-3"><i class="fas fa-paper-plane"></i></div>
                    <div>
                        <div class="stat-value">{{ $deliveredCount }}</div>
                        <div class="stat-label">Terkirim</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col mb-4">
            <div class="card stat-card-dashboard h-100 shadow-sm border-bottom-warning">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-warning mr-3"><i class="fas fa-cogs"></i></div>
                    <div>
                        <div class="stat-value">{{ $inProcessCount }}</div>
                        <div class="stat-label">Diproses</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col mb-4">
            <div class="card stat-card-dashboard h-100 shadow-sm border-bottom-success">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-success mr-3"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <div class="stat-value">{{ $completedCount }}</div>
                        <div class="stat-label">Selesai</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col mb-4">
            <div class="card stat-card-dashboard h-100 shadow-sm border-bottom-danger">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-danger mr-3"><i class="fas fa-times-circle"></i></div>
                    <div>
                        <div class="stat-value">{{ $rejectedCount }}</div>
                        <div class="stat-label">Ditolak</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col mb-4">
            <div class="card stat-card-dashboard h-100 shadow-sm border-bottom-info">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-info mr-3"><i class="fas fa-users"></i></div>
                    <div>
                        <div class="stat-value">{{ $totalResidents }}</div>
                        <div class="stat-label">Total Pelapor</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tren Laporan (7 Hari Terakhir)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 300px;"><canvas id="weeklyReportsChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
             <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Distribusi Laporan per RW</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="height: 300px;"><canvas id="reportsByRwChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('assets/admin/vendor/chart.js/Chart.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.global.defaultFontFamily = 'Inter', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    
    var ctxLine = document.getElementById("weeklyReportsChart").getContext('2d');
    new Chart(ctxLine, { type: 'line', data: { labels: @json($dailyLabels), datasets: [{ label: "Laporan", lineTension: 0.3, backgroundColor: "rgba(78, 115, 223, 0.05)", borderColor: "rgba(78, 115, 223, 1)", pointRadius: 3, pointBackgroundColor: "rgba(78, 115, 223, 1)", pointBorderColor: "rgba(78, 115, 223, 1)", data: @json($dailyData) }] }, options: { maintainAspectRatio: false, scales: { yAxes: [{ ticks: { beginAtZero: true, callback: function(value) { if (Number.isInteger(value)) return value; } } }] }, legend: { display: false } } });
    
    var ctxBar = document.getElementById("reportsByRwChart").getContext('2d');
    new Chart(ctxBar, { type: 'bar', data: { labels: @json($rwLabels), datasets: [{ label: "Laporan", backgroundColor: "#4e73df", hoverBackgroundColor: "#2e59d9", borderColor: "#4e73df", data: @json($rwData) }] }, options: { maintainAspectRatio: false, scales: { yAxes: [{ ticks: { beginAtZero: true, callback: function(value) { if (Number.isInteger(value)) return value; } } }] }, legend: { display: false } } });
});
</script>
@endsection