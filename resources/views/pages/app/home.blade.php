@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <h6 class="greeting">Hi, {{ Auth::user()->name }} 👋</h6>
    <h4 class="home-headline">Laporkan masalahmu dan kami segera atasi itu</h4>

    <div class="d-flex align-items-center justify-content-between gap-4 py-3 overflow-auto" id="category" style="white-space: nowrap;">
        @foreach ($categories as $category)
            <a href="{{ route('report.index', ['category' => $category->name]) }}" class="category d-inline-block">
                <div class="icon">
                    <img src="{{ asset('storage/' . $category->image) }}" alt="icon">
                </div>
                <p>{{ $category->name }}</p>
            </a>
        @endforeach
    </div>

    <div class="py-3" id="reports">
        <div class="d-flex justify-content-between align-items-center">
            <h6>Pengaduan terbaru</h6>
            <a href="{{ route('report.index') }}" class="text-primary text-decoration-none show-more">
                Lihat semua
            </a>
        </div>

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
                                    <p class="text-primary city">{{ \Str::limit($report->address, 20) }}</p>
                                </div>
                                <p class="text-secondary date">{{ \Carbon\Carbon::parse($report->created_at)->diffForHumans() }}</p>
                            </div>
                            <h1 class="card-title">{{ $report->title }}</h1>
                        </div>
                    </a>
                </div>
            @empty
                {{-- PERUBAHAN DI SINI: Menggunakan gaya dari halaman "Laporanmu" --}}
                <div class="d-flex flex-column justify-content-center align-items-center text-center" style="margin-top: 50px;">
                    <div id="lottie-empty-home" style="width: 250px; height: 250px;"></div>
                    <h5 class="mt-3 fw-bold">Belum Ada Pengaduan</h5>
                    <p class="text-secondary px-4">Jadilah yang pertama melaporkan masalah di lingkunganmu!</p>
                    <a href="{{ route('report.take') }}" class="btn btn-primary rounded-pill py-2 px-4 mt-3">
                        Buat Laporan Sekarang
                    </a>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@section('scripts')
    {{-- PERUBAHAN DI SINI: Menggunakan animasi lokal yang sama dengan halaman "Laporanmu" --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.2/lottie.min.js"></script>
    <script>
        var lottieContainer = document.getElementById('lottie-empty-home');
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