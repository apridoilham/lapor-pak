@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <a href="{{ route('admin.report.export.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Ekspor Laporan
        </a>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Laporan Masuk</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalReports }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-file-alt fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Laporan Diproses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inProcessCount }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-cogs fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Laporan Selesai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedCount }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Pelapor</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalResidents }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Laporan 7 Hari Terakhir</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area"><canvas id="weeklyReportsChart"></canvas></div>
                </div>
            </div>
             <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        @role('super-admin') Distribusi Laporan per RW @else Distribusi Laporan per RT @endrole
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar"><canvas id="reportsByAreaChart"></canvas></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Laporan per Kategori</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2"><canvas id="categoryPieChart"></canvas></div>
                    <div class="mt-4 text-center small" id="pie-chart-legend">
                    </div>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pelapor Teratas</h6>
                </div>
                <div class="card-body">
                    @forelse($topReporters as $reporter)
                        <div class="d-flex justify-content-between align-items-center {{ !$loop->last ? 'mb-3' : '' }}">
                            <div>
                                <h6 class="font-weight-bold mb-0">{{ Str::limit($reporter->user->name, 20) }}</h6>
                                <small class="text-muted">RT {{ $reporter->rt->number }}/RW {{ $reporter->rw->number }}</small>
                            </div>
                            <span class="badge badge-primary badge-pill">{{ $reporter->reports_count }} Laporan</span>
                        </div>
                    @empty
                        <p class="text-center text-muted">Belum ada laporan yang dibuat.</p>
                    @endforelse
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Laporan Terbaru</h6>
                </div>
                <div class="card-body">
                     @forelse($latestReports as $report)
                        <a href="{{ route('admin.report.show', $report->id) }}" class="text-decoration-none">
                            <div class="list-group-item list-group-item-action border-0 px-0 {{ !$loop->last ? 'pb-3 mb-3 border-bottom' : 'pb-0' }}">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 font-weight-bold text-dark">{{ Str::limit($report->title, 30) }}</h6>
                                    <small class="text-muted">{{ $report->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 text-gray-700 small">Oleh: {{ $report->resident->user->name }}</p>
                            </div>
                        </a>
                    @empty
                        <p class="text-center text-muted">Belum ada laporan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="{{ asset('assets/admin/vendor/chart.js/Chart.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function number_format(number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) { var k = Math.pow(10, prec); return '' + Math.round(n * k) / k; };
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) { s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep); }
        if ((s[1] || '').length < prec) { s[1] = s[1] || ''; s[1] += new Array(prec - s[1].length + 1).join('0'); }
        return s.join(dec);
    }

    var ctxLine = document.getElementById("weeklyReportsChart");
    var myLineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: @json($dailyLabels),
            datasets: [{
                label: "Laporan",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: @json($dailyData),
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
            scales: {
                xAxes: [{ time: { unit: 'date' }, gridLines: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 7 } }],
                yAxes: [{ ticks: { maxTicksLimit: 5, padding: 10, callback: function(value) { if (Number.isInteger(value)) { return number_format(value); } } }, gridLines: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] } }],
            },
            legend: { display: false },
        }
    });

    var ctxBar = document.getElementById("reportsByAreaChart");
    var myBarChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: @json($areaLabels),
            datasets: [{
                label: "Jumlah Laporan",
                backgroundColor: "#4e73df",
                hoverBackgroundColor: "#2e59d9",
                borderColor: "#4e73df",
                data: @json($areaData),
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
            scales: {
                xAxes: [{ gridLines: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 10 } }],
                yAxes: [{ ticks: { min: 0, maxTicksLimit: 5, padding: 10, callback: function(value) { if (Number.isInteger(value)) { return number_format(value); } } }, gridLines: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] } }],
            },
            legend: { display: false },
        }
    });

    var ctxPie = document.getElementById("categoryPieChart");
    var pieChartColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'];
    var myPieChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: @json($categoryLabels),
            datasets: [{
                data: @json($categoryData),
                backgroundColor: pieChartColors,
                hoverBackgroundColor: pieChartColors.map(color => Chart.helpers.getHoverColor(color)),
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            },
            cutoutPercentage: 80,
        },
    });
    
    const legendContainer = document.getElementById('pie-chart-legend');
    if (legendContainer) {
        myPieChart.data.labels.forEach((label, i) => {
            const color = myPieChart.data.datasets[0].backgroundColor[i % pieChartColors.length];
            const legendItem = document.createElement('span');
            legendItem.classList.add('mr-2');
            legendItem.innerHTML = `<i class="fas fa-circle" style="color:${color}"></i> ${label}`;
            legendContainer.appendChild(legendItem);
        });
    }
});
</script>
@endsection