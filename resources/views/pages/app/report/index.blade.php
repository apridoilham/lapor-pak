@extends('layouts.app')

@section('title', 'Daftar Pengaduan')

@section('content')
    <div class="py-3" id="reports">
        <div class="d-flex justify-content-between align-items-center">
            <p class="text-muted">{{ $reports->count() }} List Pengaduan</p>
            
            <button class="btn btn-filter" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fa-solid fa-filter me-2"></i>
                Filter
            </button>
        </div>

        @if (request()->category)
            <div class="d-flex justify-content-between align-items-center mt-3 p-2 bg-light rounded">
                <small>Menampilkan kategori: <strong>{{ request()->category }}</strong></small>
                <a href="{{ route('report.index') }}" class="btn btn-sm btn-outline-danger" style="font-size: 0.7rem;">Hapus Filter</a>
            </div>
        @endif

        <div class="d-flex flex-column gap-3 mt-3">
            @forelse($reports as $report)
                <div class="card card-report border-0 shadow-none">
                    <a href="{{ route('report.show', $report->code) }}" class="text-decoration-none text-dark">
                        <div class="card-body p-0">
                            <div class="card-report-image position-relative mb-2">
                                <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}">

                                @if($report->latestStatus)
                                    @php $statusValue = $report->latestStatus->status->value; @endphp
                                    @if ($statusValue === 'delivered')
                                        <div class="badge-status status-delivered"><i class="fa-solid fa-paper-plane"></i><span>Terkirim</span></div>
                                    @elseif ($statusValue === 'in_process')
                                        <div class="badge-status status-processing"><i class="fa-solid fa-spinner"></i><span>Diproses</span></div>
                                    @elseif ($statusValue === 'completed')
                                        <div class="badge-status status-completed"><i class="fa-solid fa-check-double"></i><span>Selesai</span></div>
                                    @elseif ($statusValue === 'rejected')
                                        <div class="badge-status status-rejected"><i class="fa-solid fa-xmark"></i><span>Ditolak</span></div>
                                    @endif
                                @endif
                            </div>

                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <div class="d-flex align-items-center ">
                                    <img src="{{ asset('assets/app/images/icons/MapPin.png') }}" alt="map pin" class="icon me-2">
                                    <p class="text-primary city">
                                        {{ $report->address }}
                                    </p>
                                </div>
                                <p class="text-secondary date">
                                    {{ \Carbon\Carbon::parse($report->created_at)->diffForHumans() }}
                                </p>
                            </div>
                            <h1 class="card-title">
                                {{ $report->title }}
                            </h1>
                        </div>
                    </a>
                </div>
            @empty
                <div class="d-flex flex-column justify-content-center align-items-center text-center" style="margin-top: 50px;">
                    <div id="lottie-empty-reports" style="width: 250px; height: 250px;"></div>
                    <h5 class="mt-3 fw-bold">Tidak Ada Laporan</h5>
                    <p class="text-secondary px-4">
                        @if (request()->category)
                            Tidak ada laporan yang ditemukan untuk kategori ini.
                        @else
                            Jadilah yang pertama melaporkan masalah di lingkunganmu!
                        @endif
                    </p>
                    <a href="{{ route('report.take') }}" class="btn btn-primary rounded-pill py-2 px-4 mt-3">
                        Buat Laporan Sekarang
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header border-0">
            <h1 class="modal-title fs-5" id="filterModalLabel">Filter Berdasarkan Kategori</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="list-group list-group-flush">
                <a href="{{ route('report.index') }}" class="list-group-item list-group-item-action {{ !request()->category ? 'active' : '' }}">
                    Semua Kategori
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('report.index', ['category' => $category->name]) }}" 
                       class="list-group-item list-group-item-action {{ request()->category == $category->name ? 'active' : '' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.2/lottie.min.js"></script>
    <script>
        var lottieContainer = document.getElementById('lottie-empty-reports');
        if (lottieContainer) {
            var animation = bodymovin.loadAnimation({
                container: lottieContainer,
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: '{{ asset('assets/app/lottie/not-found.json') }}'
            });
        }
    </script>
@endsection