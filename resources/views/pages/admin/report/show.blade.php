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
        --bg-main: #f8f9fc;
        --bg-card: #ffffff;
        --border-color: #eaecf4;
        --text-dark: #3a3b45;
        --text-light: #858796;
        --font-sans: 'Inter', sans-serif;
    }
    #content-wrapper, #content { background-color: var(--bg-main) !important; }
    body { font-family: var(--font-sans); }

    .page-header-v6 {
        background-color: var(--bg-card);
        padding: 1.5rem 2rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--border-color);
    }

    /* === KARTU KONTEN UTAMA DENGAN TAB === */
    .main-content-card {
        background-color: var(--bg-card);
        border-radius: 0.75rem;
        border: 1px solid var(--border-color);
        overflow: hidden;
    }
    .nav-tabs-custom {
        border-bottom: 1px solid var(--border-color);
    }
    .nav-tabs-custom .nav-link {
        padding: 1rem 1.5rem;
        font-weight: 600;
        font-size: 0.95rem;
        color: var(--text-light);
        border: none;
        border-bottom: 3px solid transparent;
    }
    .nav-tabs-custom .nav-link.active,
    .nav-tabs-custom .nav-link:hover {
        color: var(--primary-color);
        border-bottom: 3px solid var(--primary-color);
    }
    .tab-content {
        padding: 2rem;
    }

    /* === KONTEN DALAM TAB === */
    .report-main-image {
        width: 100%;
        border-radius: 0.5rem;
        cursor: pointer;
        border: 1px solid var(--border-color);
    }
    #map {
        height: 250px;
        width: 100%;
        border-radius: 0.5rem;
        z-index: 1;
    }
    .reporter-profile-card {
        max-width: 500px;
    }
    .reporter-profile-card .avatar {
        width: 80px; height: 80px;
        border-radius: 50%; object-fit: cover;
    }

    /* === TIMELINE === */
    .timeline-v6 {
        border-left: 3px solid var(--border-color);
        padding-left: 2rem;
        margin-left: 1rem;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 2.5rem;
    }
    .timeline-item:last-child { padding-bottom: 0; }
    .timeline-item .icon {
        position: absolute;
        left: -2.3rem;
        top: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        z-index: 1;
    }
    .timeline-item .card {
        border: 1px solid var(--border-color);
        box-shadow: none;
    }
    .timeline-item .card-header {
        background-color: var(--light-gray);
    }
    .timeline-item .proof-image { max-width: 100%; cursor: pointer; border-radius: 8px; margin-top: 1rem; }

    /* LIGHTBOX */
    .lightbox-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.85); display: flex; align-items: center; justify-content: center;
        z-index: 9999; opacity: 0; visibility: hidden; transition: opacity 0.3s ease;
        backdrop-filter: blur(5px);
    }
    .lightbox-overlay.show { opacity: 1; visibility: visible; }
    .lightbox-content img { max-width: 90vw; max-height: 90vh; object-fit: contain; }
    .lightbox-close-btn { position: absolute; top: 20px; right: 30px; color: white; font-size: 2.5rem; border: none; background: transparent; cursor: pointer; }
</style>
@endpush

@section('content')
    @php
        $resident = $report->resident;
        $avatarUrl = optional($resident->user)->avatar;
        if ($avatarUrl && !Str::startsWith($avatarUrl, 'http')) {
            $avatarUrl = asset('storage/' . $avatarUrl);
        } elseif (!$avatarUrl) {
            $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode(optional($resident->user)->name) . '&background=4e73df&color=fff&size=128';
        }
        $latestStatusEnum = $report->latestStatus ? $report->latestStatus->status : \App\Enums\ReportStatusEnum::DELIVERED;
    @endphp

    {{-- HEADER HALAMAN BARU --}}
    <div class="page-header-v6">
        <div class="row align-items-center">
            <div class="col-md-8">
                <a href="{{ route('admin.report.index') }}" class="small text-decoration-none text-muted mb-2 d-inline-block">← Kembali ke Daftar Laporan</a>
                <h1 class="h3 mb-1 text-gray-900 font-weight-bold">{{ $report->title }}</h1>
                <p class="mb-0 text-muted">Kode Laporan: <strong>{{ $report->code }}</strong></p>
            </div>
            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                 @php
                    $badgeClass = match($latestStatusEnum) {
                        \App\Enums\ReportStatusEnum::IN_PROCESS => 'badge-warning',
                        \App\Enums\ReportStatusEnum::COMPLETED => 'badge-success',
                        \App\Enums\ReportStatusEnum::REJECTED => 'badge-danger',
                        default => 'badge-primary',
                    };
                @endphp
                <span class="badge {{ $badgeClass }} p-2" style="font-size: 1rem;">{{ $latestStatusEnum->label() }}</span>
                 @can('manageStatus', $report)
                    <a href="{{ route('admin.report-status.create', $report->id) }}" class="btn btn-primary shadow-sm ml-2">
                        <i class="fas fa-plus fa-sm mr-1"></i> Update Status
                    </a>
                @endcan
            </div>
        </div>
    </div>
    
    <div class="main-content-card">
        <ul class="nav nav-tabs nav-tabs-custom" id="reportTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="detail-tab" data-toggle="tab" href="#detail" role="tab" aria-controls="detail" aria-selected="true">Detail Laporan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="timeline-tab" data-toggle="tab" href="#timeline" role="tab" aria-controls="timeline" aria-selected="false">Riwayat Aktivitas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="reporter-tab" data-toggle="tab" href="#reporter" role="tab" aria-controls="reporter" aria-selected="false">Info Pelapor</a>
            </li>
        </ul>
        <div class="tab-content" id="reportTabContent">
            <div class="tab-pane fade show active" id="detail" role="tabpanel" aria-labelledby="detail-tab">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="font-weight-bold text-dark mb-3">Foto Laporan</h5>
                        <img src="{{ asset('storage/' . $report->image) }}" alt="Foto Laporan" class="report-main-image">
                    </div>
                    <div class="col-md-6">
                        <h5 class="font-weight-bold text-dark mb-3">Deskripsi & Lokasi</h5>
                        <p class="text-gray-700" style="line-height: 1.7;">{{ $report->description }}</p>
                        <hr>
                        <p class="font-weight-bold text-dark mb-2">Alamat:</p>
                        <p class="text-muted">{{ $report->address }}</p>
                        <div id="map"></div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="timeline" role="tabpanel" aria-labelledby="timeline-tab">
                 <div class="timeline-v6">
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
                            <div class="icon {{ $iconInfo['bg'] }}"><i class="fas {{ $iconInfo['icon'] }}"></i></div>
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="font-weight-bold text-dark mb-0">{{ $status->status->label() }}</h6>
                                        <small class="text-muted">
                                            oleh <strong>@if($status->created_by_role === 'resident') Pelapor @else {{ ucfirst($status->created_by_role) }} @endif</strong>
                                        </small>
                                    </div>
                                    <small class="text-muted">{{ $status->created_at->isoFormat('D MMM YYYY, HH:mm') }}</small>
                                </div>
                                <div class="card-body">
                                    <p>{{ $status->description }}</p>
                                    @if($status->image)
                                        <img src="{{ asset('storage/' . $status->image) }}" class="img-fluid proof-image" alt="Bukti Progress">
                                    @endif
                                </div>
                                @can('manageStatus', $report)
                                <div class="card-footer text-right bg-white">
                                    <a href="{{ route('admin.report-status.edit', $status->id) }}" class="btn btn-sm btn-light" title="Edit"><i class="fas fa-edit fa-sm"></i> Edit</a>
                                    <form action="{{ route('admin.report-status.destroy', $status->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light" title="Hapus" data-title="Hapus Progress?" data-text="Anda yakin?"><i class="fas fa-trash fa-sm"></i> Hapus</button>
                                    </form>
                                </div>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <p class="text-muted ml-4">Belum ada riwayat aktivitas.</p>
                    @endforelse
                </div>
            </div>

            <div class="tab-pane fade" id="reporter" role="tabpanel" aria-labelledby="reporter-tab">
                <div class="reporter-profile-card">
                     <div class="d-flex align-items-center mb-4">
                        <img src="{{ $avatarUrl }}" alt="Avatar" class="avatar mr-3">
                        <div>
                            <h5 class="font-weight-bold text-dark mb-0">{{ $resident->user->name }}</h5>
                            <p class="text-muted mb-0">{{ $resident->user->email }}</p>
                        </div>
                    </div>
                    <table class="table table-borderless">
                        <tr><td class="font-weight-bold text-muted" style="width:120px;">Telepon</td><td>{{ $resident->phone ?? '-' }}</td></tr>
                        <tr><td class="font-weight-bold text-muted">Wilayah</td><td>RT {{ $resident->rt->number }} / RW {{ $resident->rw->number }}</td></tr>
                        <tr><td class="font-weight-bold text-muted">Alamat</td><td>{{ $resident->address }}</td></tr>
                    </table>
                    <a href="{{ route('admin.resident.show', $resident->id) }}" class="btn btn-outline-primary mt-3">Lihat Halaman Profil Lengkap <i class="fas fa-arrow-right fa-sm ml-1"></i></a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="lightbox-overlay" id="lightbox">
        <button class="lightbox-close-btn" id="lightbox-close">&times;</button>
        <div class="lightbox-content"><img src="" alt="Gambar Bukti Laporan" id="lightbox-image"></div>
    </div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('map', {
            center: [{{ $report->latitude }}, {{ $report->longitude }}],
            zoom: 16
        });
        var mapInitialized = false;

        function initializeMap() {
            if (!mapInitialized) {
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                L.marker([{{ $report->latitude }}, {{ $report->longitude }}]).addTo(map);
                mapInitialized = true;
            }
            setTimeout(() => map.invalidateSize(), 10);
        }
        
        if ($('#detail-tab').hasClass('active')) {
            initializeMap();
        }
        
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if (e.target.id === 'detail-tab') {
                initializeMap();
            }
        });

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