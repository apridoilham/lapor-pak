@extends('layouts.app')

@section('title', 'Detail Laporan ' . $report->code)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Variabel Desain "Kekinian" */
    :root {
        --primary-color: #10B981;
        --primary-gradient: linear-gradient(135deg, #10B981 0%, #34D399 100%);
        --text-dark: #111827;
        --text-light: #6B7280;
        --bg-body: #F9FAFB;
        --bg-white: #FFFFFF;
        --font-sans: 'Inter', 'Poppins', 'Segoe UI', sans-serif;
    }

    /* Pengaturan Dasar & Font */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    html, body { background-color: var(--bg-body); }
    body {
        font-family: var(--font-sans);
        max-width: 480px;
        margin: 0 auto;
        min-height: 100vh;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.05);
        background-color: var(--bg-body);
    }
    .main-content { padding: 0; padding-bottom: 80px; }

    /* Hero Section */
    .hero-container { position: relative; }
    .hero-image {
        width: 100%;
        height: 320px;
        object-fit: cover;
        display: block;
    }
    .hero-gradient-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 150px;
        background: linear-gradient(180deg, rgba(249, 250, 251, 0) 0%, var(--bg-body) 100%);
    }
    .hero-overlay-header {
        position: absolute; top: 0; left: 0; right: 0;
        display: flex; justify-content: space-between; align-items: center;
        padding: 1.25rem;
    }
    .overlay-button {
        background-color: rgba(30, 30, 30, 0.5);
        color: var(--white);
        width: 44px; height: 44px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        text-decoration: none; font-size: 1.1rem;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.2s ease;
    }
    .overlay-button:hover { background-color: rgba(0,0,0,0.7); }

    /* Konten Detail */
    .content-container {
        padding: 0 1.5rem;
        margin-top: -50px; /* Konten overlap di atas gradasi */
        position: relative;
        z-index: 10;
    }
    .report-title {
        font-weight: 800;
        font-size: 1.75rem;
        color: var(--text-dark);
        margin-bottom: 1.5rem;
        line-height: 1.3;
        background: var(--bg-white);
        padding: 1.5rem;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.07);
    }

    /* Grid Info "Glassmorphism" */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .info-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 16px;
        padding: 1rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .info-card .info-icon {
        width: 40px; height: 40px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; margin-bottom: 0.75rem;
    }
    .info-card .info-label { font-size: 0.8rem; color: var(--text-light); margin-bottom: 0.25rem; }
    .info-card .info-value { font-weight: 600; color: var(--text-dark); font-size: 0.95rem; }

    /* Warna Ikon Info */
    .icon-status { background-color: #F0FDF4; color: #10B981; }
    .icon-category { background-color: #EFF6FF; color: #3B82F6; }
    .icon-reporter { background-color: #FEF3C7; color: #D97706; }
    .icon-date { background-color: #F3E8FF; color: #9333EA; }

    /* Section Styling */
    .section { margin-bottom: 2.5rem; }
    .section-title { font-weight: 700; font-size: 1.25rem; color: var(--text-dark); margin-bottom: 1rem; }
    .section p.description { color: var(--text-light); line-height: 1.7; font-size: 0.95rem; }
    
    /* Tombol Peta */
    .map-button {
        display: flex; align-items: center; gap: 1rem;
        width: 100%; text-align: left; background: var(--bg-white);
        padding: 1rem; border-radius: 16px; border: 1px solid #e5e7eb;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-decoration: none;
    }
    .map-button .map-icon {
        width: 50px; height: 50px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        background: var(--primary-gradient); color: var(--white); font-size: 1.5rem; flex-shrink: 0;
    }
    .map-button .map-text h6 { font-weight: 600; color: var(--text-dark); margin: 0; }
    .map-button .map-text p { font-size: 0.85rem; color: var(--text-light); margin: 0; line-height: 1.4; }

    /* Modal Peta */
    .map-modal { display: none; /* ... (sama seperti sebelumnya) ... */ }
    .map-modal-content, .map-modal .close-map-btn { /* ... (sama seperti sebelumnya) ... */ }

    /* Timeline Kekinian */
    .timeline { position: relative; padding-left: 25px; border-left: 2px solid #e5e7eb; }
    .timeline-item { position: relative; padding: 1rem 0 1.5rem; }
    .timeline-item .timeline-icon-wrapper {
        position: absolute; left: -20px; top: 1rem;
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: var(--bg-white);
    }
    .timeline-item .timeline-icon {
        width: 32px; height: 32px; border-radius: 50%; color: var(--white);
        display: flex; align-items: center; justify-content: center;
    }
    .timeline-item .icon-delivered { background: #3B82F6; }
    .timeline-item .icon-in_process { background: #F59E0B; }
    .timeline-item .icon-completed { background: #10B981; }
    .timeline-item .icon-rejected { background: #EF4444; }

    .timeline-item .timeline-content { padding-left: 1.5rem; }
    .timeline-item .timeline-content .status { font-weight: 600; color: var(--text-dark); }
    .timeline-item .timeline-content .date { font-size: 0.8rem; color: var(--text-light); }
    .timeline-item .timeline-content .description { font-size: 0.9rem; color: var(--text-light); margin-top: 0.5rem; }
</style>
@endpush

@section('content')
    <div class="page-container">
        <div class="hero-container">
            <div class="hero-overlay-header">
                <a href="{{ request()->query('_ref', route('home')) }}" class="overlay-button">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>
            <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}" class="hero-image">
            <div class="hero-gradient-overlay"></div>
        </div>

        <div class="content-container">
            <h1 class="report-title">{{ $report->title }}</h1>

            <div class="info-grid">
                <div class="info-card">
                    <div class="info-icon icon-status"><i class="fa-solid fa-flag"></i></div>
                    <p class="info-label">Status</p>
                    <h6 class="info-value">{{ $report->latestStatus ? $report->latestStatus->status->label() : 'Baru' }}</h6>
                </div>
                <div class="info-card">
                    <div class="info-icon icon-category"><i class="fa-solid fa-tag"></i></div>
                    <p class="info-label">Kategori</p>
                    <h6 class="info-value">{{ $report->reportCategory->name }}</h6>
                </div>
                <div class="info-card">
                    <div class="info-icon icon-reporter"><i class="fa-solid fa-user"></i></div>
                    <p class="info-label">Pelapor</p>
                    <h6 class="info-value">{{ $report->resident->user->name }}</h6>
                </div>
                <div class="info-card">
                    <div class="info-icon icon-date"><i class="fa-solid fa-calendar"></i></div>
                    <p class="info-label">Tanggal</p>
                    <h6 class="info-value">{{ $report->created_at->isoFormat('D MMM YYYY') }}</h6>
                </div>
            </div>

            <div class="section">
                <h5 class="section-title">Detail Masalah</h5>
                <p class="description">{{ $report->description }}</p>
            </div>
            <div class="section">
                <h5 class="section-title">Lokasi</h5>
                <a href="javascript:void(0)" id="open-map-link" class="map-button">
                    <div class="map-icon"><i class="fa-solid fa-map-location-dot"></i></div>
                    <div class="map-text">
                        <h6>Lihat di Peta</h6>
                        <p>{{ Str::limit($report->address, 50) }}</p>
                    </div>
                </a>
            </div>

            <div class="section" id="riwayat-perkembangan">
                <h5 class="section-title">Riwayat Perkembangan</h5>
                <div class="timeline">
                    @forelse ($report->reportStatuses->sortBy('created_at') as $status)
                        <div class="timeline-item">
                            <div class="timeline-icon-wrapper">
                                <div class="timeline-icon icon-{{$status->status->value}}">
                                    @php
                                        $icon = match($status->status) {
                                            \App\Enums\ReportStatusEnum::DELIVERED => 'fa-paper-plane',
                                            \App\Enums\ReportStatusEnum::IN_PROCESS => 'fa-spinner fa-spin',
                                            \App\Enums\ReportStatusEnum::COMPLETED => 'fa-check-double',
                                            \App\Enums\ReportStatusEnum::REJECTED => 'fa-xmark',
                                        };
                                    @endphp
                                    <i class="fa-solid {{ $icon }}"></i>
                                </div>
                            </div>
                            <div class="timeline-content">
                                <p class="status">{{ $status->status->label() }}</p>
                                <p class="date">{{ $status->created_at->isoFormat('D MMMM YYYY, HH:mm') }}</p>
                                <p class="description">{{ $status->description }}</p>
                            </div>
                        </div>
                    @empty
                        <p>Belum ada riwayat perkembangan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="map-modal" id="map-modal" style="display: none; align-items: center; justify-content: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); z-index: 2000;">
        <div class="map-modal-content" style="position: relative; width: 90%; height: 70%; max-width: 480px; background-color: white; border-radius: 16px; overflow: hidden;">
            <button class="close-map-btn" id="close-map-btn" style="position: absolute; top: 10px; right: 10px; z-index: 1000; width: 35px; height: 35px; border-radius: 50%; background-color: rgba(0,0,0,0.5); color: white; border: none;">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div id="map-container" style="width: 100%; height: 100%;"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapModal = document.getElementById('map-modal');
            const openMapLink = document.getElementById('open-map-link');
            const closeMapBtn = document.getElementById('close-map-btn');
            let map;
            let isMapInitialized = false;

            openMapLink.addEventListener('click', function() {
                mapModal.style.display = 'flex';
                if (!isMapInitialized) {
                    map = L.map('map-container').setView([{{ $report->latitude }}, {{ $report->longitude }}], 17);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(map);
                    L.marker([{{ $report->latitude }}, {{ $report->longitude }}]).addTo(map);
                    isMapInitialized = true;
                }
                setTimeout(() => { map.invalidateSize(); }, 10);
            });

            closeMapBtn.addEventListener('click', function() {
                mapModal.style.display = 'none';
            });
        });
    </script>
@endpush