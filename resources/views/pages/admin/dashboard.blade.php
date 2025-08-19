@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<style>
    .kpi-card {
        border-radius: .75rem;
        transition: all 0.3s ease;
        border: 1px solid var(--card-border);
    }
    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.075)!important;
    }
    .kpi-card .card-body {
        padding: 1.5rem;
    }
    .kpi-card .kpi-icon-container {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    .kpi-card .kpi-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-heading);
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

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="kpi-icon-container bg-primary mr-3"><i class="fas fa-file-alt"></i></div>
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Laporan</div>
                        <div class="kpi-value mb-0">{{ $totalReports }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="kpi-icon-container bg-warning mr-3"><i class="fas fa-cogs"></i></div>
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Laporan Diproses</div>
                        <div class="kpi-value mb-0">{{ $inProcessCount }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="kpi-icon-container bg-success mr-3"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Laporan Selesai</div>
                        <div class="kpi-value mb-0">{{ $completedCount }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="kpi-icon-container bg-info mr-3"><i class="fas fa-users"></i></div>
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Pelapor</div>
                        <div class="kpi-value mb-0">{{ $totalResidents }}</div>
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