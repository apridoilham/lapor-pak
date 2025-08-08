@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

    {{-- Kartu Statistik (tidak berubah) --}}
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Kategori Laporan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $reportCategoryCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Laporan Masuk</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $reportCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Masyarakat</div>
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

    {{-- PERUBAHAN DI SINI: Baris baru untuk menampung Grafik dan Tabel Laporan --}}
    <div class="row">

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Laporan per Kategori</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="categoryPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        {{-- Keterangan warna akan dibuat oleh JavaScript --}}
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
                                        <td>{{ $report->resident->user->name }}</td>
                                        <td>
                                            @if ($report->latestStatus)
                                                <span class="badge badge-info">{{ $report->latestStatus->status->value }}</span>
                                            @else
                                                <span class="badge badge-secondary">Baru</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($report->latestStatus)
                                                {{ $report->latestStatus->created_at->tz('Asia/Jakarta')->isoFormat('D MMM Y, HH:mm') }}
                                            @else
                                                {{ $report->created_at->tz('Asia/Jakarta')->isoFormat('D MMM Y, HH:mm') }}
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
@endsection

@section('scripts')
{{-- ▼▼▼ TAMBAHKAN SCRIPT BARU UNTUK GRAFIK DI SINI ▼▼▼ --}}
<script>
// Pie Chart Example
var ctx = document.getElementById("categoryPieChart");
var categoryPieChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    // Mengambil data nama kategori dari controller
    labels: {!! json_encode($categoryLabels) !!},
    datasets: [{
      // Mengambil data jumlah laporan dari controller
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
@endsection