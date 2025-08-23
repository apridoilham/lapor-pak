@extends('layouts.admin')

@section('title', 'Detail Laporan ' . $report->code)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    :root {
        --primary-color: #4e73df;
        --success-color: #1cc88a;
        --warning-color: #f6c23e;
        --danger-color: #e74a3b;
        --info-color: #36b9cc;
        --bg-main: #f8f9fc;
        --bg-card: #ffffff;
        --border-color: #eaecf4;
        --text-dark: #3a3b45;
        --text-light: #858796;
        --font-sans: 'Inter', sans-serif;
    }
    
    .card {
        border-radius: .75rem !important;
        border: 1px solid var(--border-color);
        box-shadow: 0 0.25rem 1.25rem rgba(0,0,0,.06) !important;
    }
    
    .report-main-image {
        width: 100%;
        max-height: 450px;
        object-fit: contain;
        background-color: #f8f9fc;
        border-radius: .5rem;
        cursor: pointer;
        border: 1px solid var(--border-color);
    }
    
    #map {
        height: 250px;
        width: 100%;
        border-radius: .5rem;
        z-index: 1;
    }

    .info-card .card-body {
        padding: 1.5rem;
    }
    .info-card .info-item {
        display: flex;
        align-items: flex-start;
        padding: 0.9rem 0;
        border-bottom: 1px solid var(--border-color);
    }
    .info-card .info-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .info-card .info-item:first-child {
        padding-top: 0;
    }
    .info-card .info-item-icon {
        flex-shrink: 0;
        width: 38px;
        height: 38px;
        border-radius: .5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        background-color: #edf2ff;
        font-size: 1rem;
        margin-right: 1rem;
    }
    .info-card .info-item-label {
        font-size: 0.85rem;
        color: var(--text-light);
        margin-bottom: 0.1rem;
        display: block;
    }
    .info-card .info-item-value {
        font-weight: 600;
        color: var(--text-dark);
    }
    .info-card .info-item-value.pelapor {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .info-card .avatar {
        width: 32px;
        height: 32px;
        object-fit: cover;
    }

    .timeline {
        position: relative;
    }
    .timeline-item {
        padding-left: 2.5rem;
        position: relative;
        padding-bottom: 2.5rem;
    }
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 11px;
        top: 5px;
        bottom: -5px;
        width: 2px;
        background-color: #f1f3f5;
    }
    .timeline-item:last-child::before {
        display: none;
    }
    .timeline-item .timeline-icon {
        position: absolute;
        left: 0;
        top: 0;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
    }
    .timeline-content { margin-left: 0; }
    .proof-image { 
        max-width: 200px;
        height: auto;
        cursor: pointer; 
        border-radius: 8px; 
        margin-top: 1rem; 
        border: 1px solid var(--border-color);
    }
    
    .soft-badge { font-size: 0.9rem; font-weight: 600; padding: .4em .8em; border-radius: 20px; }
    .soft-badge.badge-success { background-color: #d1fae5; color: #065f46; }
    .soft-badge.badge-warning { background-color: #fef3c7; color: #92400e; }
    .soft-badge.badge-danger { background-color: #fee2e2; color: #991b1b; }
    .soft-badge.badge-primary { background-color: #dbeafe; color: #1e40af; }

    .lightbox-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.85); display: flex; align-items: center; justify-content: center; z-index: 1051; opacity: 0; visibility: hidden; transition: opacity 0.3s ease; backdrop-filter: blur(5px); }
    .lightbox-overlay.show { opacity: 1; visibility: visible; }
    .lightbox-content img { max-width: 90vw; max-height: 90vh; object-fit: contain; border-radius: 8px; }
    .lightbox-close-btn { position: absolute; top: 20px; right: 30px; color: white; font-size: 2.5rem; border: none; background: transparent; cursor: pointer; }
</style>
@endpush

@section('content')
    @php
        $resident = $report->resident;
        $latestStatusEnum = $report->latestStatus ? $report->latestStatus->status : \App\Enums\ReportStatusEnum::DELIVERED;
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.report.index') }}" class="btn btn-outline-primary btn-circle mr-3" title="Kembali">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">{{ $report->title }}</h1>
                 <p class="mb-0 text-muted small">Kode Laporan: <strong>{{ $report->code }}</strong></p>
            </div>
        </div>
        <div>
            @can('manageStatus', $report)
                <a href="{{ route('admin.report-status.create', $report->id) }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus fa-sm mr-2"></i>Update Status
                </a>
            @endcan
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card mb-4">
                 <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bukti Laporan</h6>
                </div>
                <div class="card-body">
                    <img src="{{ asset('storage/' . $report->image) }}" alt="Foto Laporan" class="report-main-image">
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-align-left mr-2"></i>Deskripsi Lengkap</h6>
                </div>
                <div class="card-body">
                    <p class="text-gray-800" style="line-height: 1.8;">{{ $report->description }}</p>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tasks mr-2"></i>Riwayat Perkembangan Laporan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @forelse ($report->reportStatuses->sortBy('created_at') as $status)
                            <div class="timeline-item">
                                @php
                                    $iconInfo = match($status->status) {
                                        \App\Enums\ReportStatusEnum::DELIVERED => ['icon' => 'fa-paper-plane', 'bg' => 'bg-primary'],
                                        \App\Enums\ReportStatusEnum::IN_PROCESS => ['icon' => 'fa-cogs', 'bg' => 'bg-warning'],
                                        \App\Enums\ReportStatusEnum::COMPLETED => ['icon' => 'fa-check-circle', 'bg' => 'bg-success'],
                                        \App\Enums\ReportStatusEnum::REJECTED => ['icon' => 'fa-times-circle', 'bg' => 'bg-danger'],
                                    };
                                @endphp
                                <div class="timeline-icon {{ $iconInfo['bg'] }}"><i class="fas {{ $iconInfo['icon'] }} fa-sm"></i></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="font-weight-bold text-dark mb-0">{{ $status->status->label() }}</h6>
                                        <small class="text-muted">{{ $status->created_at->isoFormat('D MMM YYYY, HH:mm') }}</small>
                                    </div>
                                    <p class="mb-2"><small class="text-muted">oleh <strong>@if($status->created_by_role === 'resident') Pelapor @else {{ ucfirst($status->created_by_role) }} @endif</strong></small></p>
                                    <p class="text-gray-700">{{ $status->description }}</p>
                                    @if($status->image)
                                        <img src="{{ asset('storage/' . $status->image) }}" class="img-fluid proof-image" alt="Bukti Progress">
                                    @endif
                                    @can('manageStatus', $report)
                                        @if(!($loop->first && $status->status == \App\Enums\ReportStatusEnum::DELIVERED))
                                            <div class="text-right mt-3">
                                                <a href="{{ route('admin.report-status.edit', $status->id) }}" class="btn btn-sm btn-outline-warning" title="Edit"><i class="fas fa-edit fa-sm"></i> Edit</a>
                                                <form action="{{ route('admin.report-status.destroy', $status->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus" data-title="Hapus Progress?" data-text="Anda yakin?"><i class="fas fa-trash fa-sm"></i> Hapus</button>
                                                </form>
                                            </div>
                                        @endif
                                    @endcan
                                </div>
                            </div>
                        @empty
                            <p class="text-muted pl-4">Belum ada riwayat aktivitas.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card mb-4 info-card">
                 <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Laporan</h6>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-item-icon"><i class="fas fa-flag"></i></div>
                        <div>
                            <span class="info-item-label">Status Terkini</span>
                            <span class="info-item-value soft-badge badge-{{ $latestStatusEnum->colorClass() }}">{{ $latestStatusEnum->label() }}</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-icon"><i class="fas fa-tag"></i></div>
                        <div>
                            <span class="info-item-label">Kategori</span>
                            <span class="info-item-value">{{ $report->reportCategory->name }}</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-icon"><i class="fas fa-calendar-alt"></i></div>
                        <div>
                            <span class="info-item-label">Tanggal Dilaporkan</span>
                            <span class="info-item-value">{{ $report->created_at->isoFormat('D MMMM YYYY') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4 reporter-card">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Info Pelapor</h6></div>
                <div class="card-body">
                    @php
                        $avatarUrl = optional($resident->user)->avatar ?? $resident->avatar;
                        if ($avatarUrl && !filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
                            $avatarUrl = asset('storage/' . $avatarUrl);
                        } elseif (empty($avatarUrl)) {
                            $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($resident->user->name);
                        }
                    @endphp
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $avatarUrl }}" alt="Avatar" class="avatar rounded-circle mr-3">
                        <div>
                            <h6 class="font-weight-bold text-dark mb-0">{{ $resident->user->name }}</h6>
                            <p class="text-muted mb-0 small">RT {{ $resident->rt->number }} / RW {{ $resident->rw->number }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.resident.show', $resident->id) }}" class="btn btn-outline-primary btn-block">Lihat Profil Lengkap</a>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header py-3">
                     <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-map-marked-alt mr-2"></i>Lokasi Kejadian</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">{{ $report->address }}</p>
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="lightbox-overlay" id="lightbox">
        <button class="lightbox-close-btn" id="lightbox-close">&times;</button>
        <div class="lightbox-content"><img src="" alt="Gambar Laporan" id="lightbox-image"></div>
    </div>

@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var mapInitialized = false;
        
        function initializeMap() {
            if (document.getElementById('map') && !mapInitialized) {
                var map = L.map('map').setView([{{ $report->latitude }}, {{ $report->longitude }}], 16);
                // PERUBAHAN: Menggunakan tile layer standar berwarna
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                L.marker([{{ $report->latitude }}, {{ $report->longitude }}]).addTo(map);
                mapInitialized = true;
                setTimeout(() => map.invalidateSize(), 200);
            }
        }
        
        initializeMap();

        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                const button = this.querySelector('button[type="submit"]');
                Swal.fire({
                    title: button.dataset.title || 'Anda yakin?',
                    text: button.dataset.text || 'Tindakan ini tidak dapat dibatalkan!',
                    icon: 'warning', showCancelButton: true,
                    confirmButtonColor: '#e74a3b', cancelButtonColor: '#858796',
                    confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
                }).then((result) => { if (result.isConfirmed) { this.submit(); } });
            });
        });

        const lightbox = document.getElementById('lightbox');
        if(lightbox) {
            const allImages = document.querySelectorAll('.report-main-image, .proof-image');
            const lightboxImage = document.getElementById('lightbox-image');
            const lightboxClose = document.getElementById('lightbox-close');
            allImages.forEach(image => {
                image.addEventListener('click', function() {
                    lightboxImage.src = this.src;
                    lightbox.classList.add('show');
                });
            });
            const closeLightbox = () => lightbox.classList.remove('show');
            lightboxClose.addEventListener('click', closeLightbox);
            lightbox.addEventListener('click', (e) => { if (e.target === lightbox) closeLightbox(); });
            document.addEventListener('keydown', (e) => { if (e.key === "Escape" && lightbox.classList.contains('show')) closeLightbox(); });
        }
    });
</script>
@endsection