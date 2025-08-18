@extends('layouts.app')

@section('title', 'Beranda')

@push('styles')
<style>
    :root {
        --primary: #16752B;
        --secondary-text: #6c757d;
        --light-gray-bg: #f8f9fa;
        --border-color: #e2e8f0;
        --white: #ffffff;
        --shadow-color-light: rgba(17, 24, 39, 0.05);
        --shadow-color-hover: rgba(17, 24, 39, 0.08);
    }

    html {
        background-color: var(--light-gray-bg);
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--white);
        max-width: 480px;
        margin: 0 auto;
        min-height: 100vh;
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow-x: hidden;
    }

    .main-content {
        padding: 0;
    }

    .home-header {
        background: linear-gradient(135deg, var(--primary), #2c5282);
        color: var(--white);
        padding: 1.5rem;
        border-bottom-left-radius: 24px;
        border-bottom-right-radius: 24px;
    }

    .home-header .header-top-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .home-header .user-welcome {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .home-header .user-welcome .avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.5);
    }

    .home-header .user-welcome .greeting-text p {
        margin: 0;
        font-size: 0.9rem;
        color: var(--white);
        opacity: 0.8;
    }

    .home-header .user-welcome .greeting-text h6 {
        margin: 0;
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--white);
    }

    .home-header .notification-bell {
        position: relative;
        font-size: 1.5rem;
        color: var(--white);
        opacity: 0.9;
        text-decoration: none;
    }

    .home-header .notification-bell .badge {
        position: absolute;
        top: -5px;
        right: -8px;
        font-size: 0.6rem;
        padding: 0.2em 0.5em;
        border-radius: 50%;
        border: 2px solid var(--white);
    }

    .home-header .header-headline {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--white);
        line-height: 1.3;
        margin-bottom: 1rem;
    }

    .home-header .header-search-form {
        position: relative;
    }

    .home-header .header-search-form .form-control {
        border-radius: 12px;
        padding: 0.75rem 3rem 0.75rem 2.75rem;
        background-color: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        height: 50px;
        color: var(--white);
    }

    .home-header .header-search-form .form-control::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .home-header .header-search-form .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.7);
        font-size: 1.1rem;
        z-index: 10;
    }

    .home-header .header-search-form .clear-search-btn {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.7);
        font-size: 1.2rem;
        text-decoration: none;
        display: none;
        z-index: 10;
    }

    .content-section {
        padding: 1.5rem;
        padding-bottom: 100px;
    }

    .section-title {
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .category-grid {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 1rem;
        margin-bottom: -1rem;
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .category-grid::-webkit-scrollbar {
        display: none;
    }

    .category-item {
        text-decoration: none;
        text-align: center;
        flex-shrink: 0;
        width: 80px;
    }

    .category-item:hover .icon-wrapper {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .category-item .icon-wrapper {
        width: 100%;
        padding-top: 100%;
        position: relative;
        border-radius: 18px;
        background-color: var(--light-gray-bg);
        transition: all 0.2s ease-in-out;
    }

    .category-item .icon-wrapper.c-1 { background-color: #e6fffa; }
    .category-item .icon-wrapper.c-2 { background-color: #fffbeb; }
    .category-item .icon-wrapper.c-3 { background-color: #ebf4ff; }
    .category-item .icon-wrapper.c-4 { background-color: #fef3c7; }

    .category-item .icon-wrapper img {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 50%;
        height: 50%;
        object-fit: contain;
    }

    .category-item p {
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1.3;
        margin-top: 0.5rem;
        color: #4a5568;
        white-space: normal;
    }

    .btn-see-all-reports {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.75rem;
        margin-top: 1.5rem;
        background-color: var(--light-gray-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        color: var(--secondary-text);
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s ease-in-out;
    }

    .btn-see-all-reports:hover {
        background-color: #e8f5e9;
        color: var(--primary);
        border-color: var(--primary);
    }

    .report-feed {
        margin-top: 2rem;
    }

    .report-card {
        background-color: var(--white);
        border-radius: 16px;
        box-shadow: 0 4px 25px var(--shadow-color-light);
        text-decoration: none;
        color: #2d3748;
        display: block;
        overflow: hidden;
        margin-bottom: 1.5rem;
        border: 1px solid var(--border-color);
        transition: all 0.2s ease-in-out;
    }

    .report-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px var(--shadow-color-hover);
    }

    .report-card .card-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
    }

    .report-card .card-header .avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }

    .report-card .card-header .user-info {
        font-size: 0.85rem;
    }

    .report-card .card-header .user-name {
        font-weight: 600;
    }

    .report-card .card-header .user-location {
        font-size: 0.75rem;
        color: var(--secondary-text);
    }

    .report-card .card-image-container {
        position: relative;
    }

    .report-card .card-image-container img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .report-card .badge-status {
        position: absolute;
        bottom: 10px;
        left: 10px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--white);
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .report-card .badge-status.status-delivered { background-color: #3B82F6; }
    .report-card .badge-status.status-in_process { background-color: #F59E0B; }
    .report-card .badge-status.status-completed { background-color: #10B981; }
    .report-card .badge-status.status-rejected { background-color: #EF4444; }

    .report-card .card-content {
        padding: 1rem;
    }

    .report-card .card-title {
        font-weight: 700;
        line-height: 1.4;
        margin-bottom: 0.25rem;
        font-size: 1.1rem;
    }

    .report-card .card-description {
        font-size: 0.9rem;
        color: var(--secondary-text);
        margin-bottom: 0.75rem;
    }

    .report-card .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.75rem;
        color: var(--secondary-text);
        padding: 0.75rem 1rem;
        background-color: var(--light-gray-bg);
        border-top: 1px solid var(--border-color);
    }
</style>
@endpush

@section('content')
<div class="home-header">
    <div class="header-top-row">
        <div class="user-welcome">
            @php
            $user = Auth::user();
            $avatarUrl = $user->resident->avatar;
            if ($avatarUrl && !Str::startsWith($avatarUrl, 'http')) {
                $avatarUrl = asset('storage/' . $avatarUrl);
            } elseif (!$avatarUrl) {
                $avatarUrl = asset('assets/app/images/default-avatar.png');
            }
            $unreadNotificationsCount = $user->unreadNotifications->count();
            @endphp
            <img src="{{ $avatarUrl }}" alt="avatar" class="avatar">
            <div class="greeting-text">
                <p>Selamat Datang,</p>
                <h6>{{ $user->name }}</h6>
            </div>
        </div>
        <a href="{{ route('notifications.index') }}" class="notification-bell">
            <i class="fa-regular fa-bell"></i>
            @if($unreadNotificationsCount > 0)
            <span class="badge bg-danger">{{ $unreadNotificationsCount }}</span>
            @endif
        </a>
    </div>
    <h4 class="header-headline">Laporkan masalah di sekitar Anda</h4>
    <form action="{{ route('home') }}" method="GET" class="header-search-form">
        <i class="fa-solid fa-search search-icon"></i>
        <input type="text" name="search" id="search-input" class="form-control" placeholder="Cari laporan..." value="{{ request('search') }}">
        <a href="{{ route('home') }}" id="clear-search-btn" class="clear-search-btn">
            <i class="fa-solid fa-xmark"></i>
        </a>
    </form>
</div>

<div class="content-section">
    <div class="category-section">
        <h6 class="section-title">Pilih Kategori Laporan</h6>
        <div class="category-grid">
            @foreach ($categories as $category)
            <a href="{{ route('report.index', ['category' => $category->name]) }}" class="category-item">
                <div class="icon-wrapper c-{{ ($loop->iteration % 4) + 1 }}">
                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                </div>
                <p>{{ $category->name }}</p>
            </a>
            @endforeach
        </div>
    </div>

    <a href="{{ route('report.index') }}" class="btn-see-all-reports">
        Lihat Semua Laporan
        <i class="fa-solid fa-arrow-right"></i>
    </a>

    <div class="report-feed">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0">
                @if(request('search'))
                Hasil Pencarian
                @else
                Pengaduan Terbaru
                @endif
            </h6>
        </div>

        <div class="d-flex flex-column gap-3">
            @forelse($reports as $report)
            <a href="{{ route('report.show', ['code' => $report->code, '_ref' => request()->fullUrl()]) }}" class="report-card">
                <div class="card-header">
                    @php
                    $reporterAvatar = $report->resident->avatar;
                    if ($reporterAvatar && !Str::startsWith($reporterAvatar, 'http')) {
                        $reporterAvatar = asset('storage/' . $reporterAvatar);
                    } elseif (!$reporterAvatar) {
                        $reporterAvatar = asset('assets/app/images/default-avatar.png');
                    }
                    @endphp
                    <img src="{{ $reporterAvatar }}" alt="Avatar Pelapor" class="avatar">
                    <div class="user-info">
                        <div class="user-name">{{ $report->resident->user->censored_name }}</div>
                        <div class="user-location">RT {{ $report->resident->rt->number }}/RW {{ $report->resident->rw->number }}</div>
                    </div>
                </div>

                <div class="card-image-container">
                    <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}">
                    @if($report->latestStatus)
                    @php
                    $status = $report->latestStatus->status;
                    $statusClass = 'status-' . $status->value;
                    $statusIcon = match($status) {
                        \App\Enums\ReportStatusEnum::DELIVERED => 'fa-paper-plane fa-bounce',
                        \App\Enums\ReportStatusEnum::IN_PROCESS => 'fa-spinner fa-spin',
                        \App\Enums\ReportStatusEnum::COMPLETED => 'fa-check-double fa-beat',
                        \App\Enums\ReportStatusEnum::REJECTED => 'fa-xmark fa-shake',
                        default => 'fa-question-circle',
                    };
                    @endphp
                    <div class="badge-status {{ $statusClass }}">
                        <i class="fa-solid {{ $statusIcon }}"></i>
                        <span>{{ $status->label() }}</span>
                    </div>
                    @endif
                </div>

                <div class="card-content">
                    <h5 class="card-title">{{ $report->title }}</h5>
                    <p class="card-description">{{ Str::limit($report->description, 100) }}</p>
                </div>

                <div class="card-footer">
                    <span>
                        <i class="fa-solid fa-map-marker-alt me-1"></i>
                        {{ Str::limit($report->address, 35) }}
                    </span>
                    <span>{{ \Carbon\Carbon::parse($report->created_at)->diffForHumans() }}</span>
                </div>
            </a>
            @empty
            <div class="d-flex flex-column justify-content-center align-items-center text-center py-5">
                <div id="lottie-empty-home" style="width: 250px; height: 250px;"></div>
                <h5 class="mt-3 fw-bold">Laporan Tidak Ditemukan</h5>
                <p class="text-secondary px-4">
                    @if(request('search'))
                    Tidak ada laporan yang cocok dengan pencarian Anda.
                    @else
                    Saat ini belum ada pengaduan sama sekali.
                    @endif
                </p>
                @if(request('search'))
                <a href="{{ route('home') }}" class="btn btn-secondary rounded-pill py-2 px-4 mt-3">
                    Lihat Semua Laporan
                </a>
                @endif
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.2/lottie.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lottieContainer = document.getElementById('lottie-empty-home');
        if (lottieContainer) {
            bodymovin.loadAnimation({
                container: lottieContainer,
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: '{{ asset('assets/app/lottie/not-found.json') }}'
            });
        }

        const searchInput = document.getElementById('search-input');
        const clearSearchBtn = document.getElementById('clear-search-btn');

        if (searchInput && clearSearchBtn) {
            const toggleClearButton = () => {
                if (searchInput.value.length > 0) {
                    clearSearchBtn.style.display = 'block';
                } else {
                    clearSearchBtn.style.display = 'none';
                }
            };

            toggleClearButton();
            searchInput.addEventListener('input', toggleClearButton);
        }
    });
</script>
@endpush