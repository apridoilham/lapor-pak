@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Laporan Masuk</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $reportCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Laporan Terkirim</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $deliveredCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-paper-plane fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Laporan Diproses</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inProcessCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
                        </div>
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
                        <div class="col-auto">
                            <i class="fas fa-check-double fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Laporan Ditolak</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rejectedCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @role('super-admin')
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Total Kategori Laporan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $reportCategoryCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endrole

        <div class="@role('super-admin') col-xl-6 @else col-xl-9 @endrole col-md-12 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            @role('super-admin')
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Pelapor Bojongsari Baru</div>
                            @else
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Pelapor Warga RW {{ $rwNumber }}</div>
                            @endrole
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $residentCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Laporan 7 Hari Terakhir</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="dailyReportsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Laporan per Kategori</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="categoryPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Laporan Terbaru</h6>
                    <a href="{{ route('admin.report.index') }}" class="btn btn-sm btn-primary">Lihat Semua Laporan</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Judul</th>
                                    <th>Pelapor</th>
                                    <th>Status Terakhir</th>
                                    <th>Tanggal Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestReports as $report)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.report.show', $report->id) }}">{{ $report->code }}</a>
                                        </td>
                                        <td>{{ Str::limit($report->title, 20) }}</td>
                                        <td>{{ $report->resident?->user?->name ?? 'N/A' }}</td>
                                        <td>
                                            @if ($report->latestStatus)
                                                @php
                                                    $status = $report->latestStatus->status;
                                                    $badgeClass = match($status) {
                                                        \App\Enums\ReportStatusEnum::DELIVERED => 'badge-secondary',
                                                        \App\Enums\ReportStatusEnum::IN_PROCESS => 'badge-info',
                                                        \App\Enums\ReportStatusEnum::COMPLETED => 'badge-success',
                                                        \App\Enums\ReportStatusEnum::REJECTED => 'badge-danger',
                                                        default => 'badge-light',
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $status->label() }}</span>
                                            @else
                                                <span class="badge badge-secondary">Baru</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($report->latestStatus)
                                                {{ $report->latestStatus->updated_at->isoFormat('D MMM Y, HH:mm') }}
                                            @else
                                                {{ $report->created_at->isoFormat('D MMM Y, HH:mm') }}
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada laporan yang masuk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @role('super-admin')
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Total Laporan per RW</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="rwReportsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endrole

    @role('admin')
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Total Laporan per RT di RW {{ $rwNumber }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="rtReportsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endrole
@endsection

@section('scripts')
    <script src="{{ asset('assets/admin/js/demo/chart-area-demo.js') }}"></script>
    <script src="{{ asset('assets/admin/js/demo/chart-pie-demo.js') }}"></script>
    <script>
        var ctxBar = document.getElementById("dailyReportsChart");
        var dailyReportsChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: {!! json_encode($dailyLabels) !!},
                datasets: [{
                    label: "Jumlah Laporan",
                    backgroundColor: "#4e73df",
                    hoverBackgroundColor: "#2e59d9",
                    borderColor: "#4e73df",
                    data: {!! json_encode($dailyData) !!},
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: { left: 10, right: 25, top: 25, bottom: 0 }
                },
                scales: {
                    xAxes: [{
                        time: { unit: 'day' },
                        gridLines: { display: false, drawBorder: false },
                        ticks: { maxTicksLimit: 7 }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            beginAtZero: true,
                            callback: function(value) { if (Number.isInteger(value)) { return value; } },
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: { display: false },
                tooltips: {
                    intersect: false,
                    mode: 'index',
                }
            }
        });
    </script>
    
    <script>
        var ctxPie = document.getElementById("categoryPieChart");
        var categoryPieChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categoryLabels) !!},
            datasets: [{
            data: {!! json_encode($categoryData) !!},
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#c73024', '#686a76'],
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
                display: true,
                position: 'bottom'
            },
            cutoutPercentage: 80,
        },
        });
    </script>

    @role('super-admin')
    <script>
        var ctxRwBar = document.getElementById("rwReportsChart");
        var rwReportsChart = new Chart(ctxRwBar, {
            type: 'bar',
            data: {
                labels: {!! json_encode($rwLabels) !!},
                datasets: [{
                    label: "Jumlah Laporan",
                    backgroundColor: "#1cc88a",
                    hoverBackgroundColor: "#17a673",
                    borderColor: "#1cc88a",
                    data: {!! json_encode($rwData) !!},
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: { left: 10, right: 25, top: 25, bottom: 0 }
                },
                scales: {
                    xAxes: [{
                        gridLines: { display: false, drawBorder: false },
                        ticks: { maxTicksLimit: 10 }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            beginAtZero: true,
                            callback: function(value) { if (Number.isInteger(value)) { return value; } },
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: { display: false },
                tooltips: {
                    intersect: false,
                    mode: 'index',
                }
            }
        });
    </script>
    @endrole

    @role('admin')
    <script>
        var ctxRtBar = document.getElementById("rtReportsChart");
        var rtReportsChart = new Chart(ctxRtBar, {
            type: 'bar',
            data: {
                labels: {!! json_encode($rtLabels) !!},
                datasets: [{
                    label: "Jumlah Laporan",
                    backgroundColor: "#f6c23e",
                    hoverBackgroundColor: "#dda20a",
                    borderColor: "#f6c23e",
                    data: {!! json_encode($rtData) !!},
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: { left: 10, right: 25, top: 25, bottom: 0 }
                },
                scales: {
                    xAxes: [{
                        gridLines: { display: false, drawBorder: false },
                        ticks: { maxTicksLimit: 15 }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            beginAtZero: true,
                            callback: function(value) { if (Number.isInteger(value)) { return value; } },
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: { display: false },
                tooltips: {
                    intersect: false,
                    mode: 'index',
                }
            }
        });
    </script>
    @endrole
@endsection