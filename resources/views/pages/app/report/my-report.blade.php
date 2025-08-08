@extends('layouts.app')

@section('title', 'Laporanmu')

@section('content')
    <h3 class="mb-3">Laporanmu</h3>

    <ul class="nav nav-tabs" id="filter-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ request('status', 'delivered') === 'delivered' ? 'active' : '' }}"
                href="{{ route('report.myreport', ['status' => 'delivered']) }}">
                Terkirim
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ request('status') === 'in_process' ? 'active' : '' }}"
                href="{{ route('report.myreport', ['status' => 'in_process']) }}">
                Diproses
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ request('status') === 'completed' ? 'active' : '' }}"
                href="{{ route('report.myreport', ['status' => 'completed']) }}">
                Selesai
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ request('status') === 'rejected' ? 'active' : '' }}"
                href="{{ route('report.myreport', ['status' => 'rejected']) }}">
                Ditolak
            </a>
        </li>
    </ul>

    <div class="d-flex flex-column gap-3 mt-4">
        @forelse ($reports as $report)
            <div class="card card-report border-0 shadow-none">
                <a href="{{ route('report.show', $report->code) }}" class="text-decoration-none text-dark">
                    <div class="card-body p-0">
                        <div class="card-report-image position-relative mb-2">
                            <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}">

                            @if($report->latestStatus)
                                @php
                                    $statusValue = $report->latestStatus->status->value;
                                @endphp

                                @if ($statusValue === 'delivered')
                                    <div class="badge-status status-delivered">
                                        <i class="fa-solid fa-paper-plane"></i>
                                        <span>Terkirim</span>
                                    </div>
                                @elseif ($statusValue === 'in_process')
                                    <div class="badge-status status-processing">
                                        <i class="fa-solid fa-spinner"></i>
                                        <span>Diproses</span>
                                    </div>
                                @elseif ($statusValue === 'completed')
                                    <div class="badge-status status-completed">
                                        <i class="fa-solid fa-check-double"></i>
                                        <span>Selesai</span>
                                    </div>
                                @elseif ($statusValue === 'rejected')
                                    <div class="badge-status status-rejected">
                                        <i class="fa-solid fa-xmark"></i>
                                        <span>Ditolak</span>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <div class="d-flex align-items-center ">
                                <img src="{{ asset('assets/app/images/icons/MapPin.png') }}" alt="map pin" class="icon me-2">
                                <p class="text-primary city">
                                    {{ \Str::limit($report->address, 20) }}
                                </p>
                            </div>
                            <p class="text-secondary date">
                                {{ \Carbon\Carbon::parse($report->created_at)->diffForHumans() }}
                            </p>
                        </div>
                        <h1 class="card-title">{{ $report->title }}</h1>
                    </div>
                </a>
            </div>
        @empty
            <div class="d-flex flex-column justify-content-center align-items-center text-center" style="margin-top: 80px;">
                <div id="lottie" style="width: 250px; height: 250px;"></div>
                <h5 class="mt-3">Belum ada laporan</h5>
                <p class="text-secondary">Ayo buat laporanmu!</p>
                <a href="{{ route('report.take') }}" class="btn btn-primary py-2 px-4 mt-3">
                    Buat Laporan
                </a>
            </div>
        @endempty
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.2/lottie.min.js"></script>
    <script>
        var lottieContainer = document.getElementById('lottie');

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