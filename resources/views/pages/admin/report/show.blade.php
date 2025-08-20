@extends('layouts.admin')

@section('title', 'Detail Laporan')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    .report-hero-header {
        position: relative;
        border-radius: .75rem;
        overflow: hidden;
        padding: 2.5rem;
        color: white;
        background-color: #1a202c;
    }
    .report-hero-bg {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-size: cover;
        background-position: center;
        filter: brightness(0.4);
        transform: scale(1.1);
    }
    .report-hero-content {
        position: relative;
        z-index: 2;
    }
    .report-hero-content .badge-category {
        background-color: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        font-weight: 500;
        padding: .5em 1em;
        color: white;
    }
    .report-hero-content .report-title {
        font-size: 2.25rem;
        font-weight: 700;
        text-shadow: 0 2px 10px rgba(0,0,0,0.5);
    }
    .meta-info {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        opacity: 0.8;
        font-size: 0.9rem;
    }
    .meta-info .avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }

    .info-dl dt {
        font-size: 0.8rem;
        font-weight: 600;
        color: #858796;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-dl dd {
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 1.25rem;
        font-size: 1rem;
    }
    .info-dl dd:last-child {
        margin-bottom: 0;
    }

    .timeline { position: relative; padding-left: 10px; }
    .timeline::before { content: ''; position: absolute; left: 20px; top: 10px; bottom: 10px; width: 3px; background: #e3e6f0; border-radius: 3px; }
    .timeline-item { position: relative; margin-bottom: 2rem; }
    .timeline-item:last-child { margin-bottom: 0; }
    .timeline-icon { position: absolute; left: 0; top: 0; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; z-index: 1; border: 3px solid #f8f9fc; }
    .timeline-content { margin-left: 55px; }
    .timeline-content .proof-image { max-width: 150px; border-radius: .35rem; cursor: pointer; }
</style>
@endpush

@section('content')
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.report.index') }}" class="btn btn-outline-primary btn-circle mr-3" title="Kembali">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 text-gray-900 font-weight-bold">Detail Kasus Laporan</h1>
            <p class="mb-0 text-muted">Kode: {{ $report->code }}</p>
        </div>
    </div>

    <div class="report-hero-header mb-4">
        <div class="report-hero-bg" style="background-image: url('{{ asset('storage/' . $report->image) }}')"></div>
        <div class="report-hero-content">
            <p class="mb-2"><span class="badge-category">{{ $report->reportCategory->name }}</span></p>
            <h1 class="report-title">{{ $report->title }}</h1>
            <div class="meta-info mt-3">
                @php
                    $resident = $report->resident;
                    $avatarUrl = optional($resident->user)->avatar;
                    if ($avatarUrl && !Str::startsWith($avatarUrl, 'http')) { $avatarUrl = asset('storage/' . $avatarUrl); }
                    elseif (!$avatarUrl) { $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode(optional($resident->user)->name) . '&background=fff&color=1a202c&size=64'; }
                @endphp
                <img src="{{ $avatarUrl }}" alt="Avatar" class="avatar">
                <span class="font-weight-bold">{{ optional($resident->user)->name }}</span>
                <span><i class="far fa-clock mr-1"></i> Dilaporkan {{ $report->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-alt mr-2"></i>Deskripsi & Lokasi</h6>
                </div>
                <div class="card-body">
                    <p class="text-gray-700 mb-4" style="line-height: 1.8;">{{ $report->description }}</p>
                    <hr>
                    <p class="text-muted mt-4 mb-3">{{ $report->address }}</p>
                    <div id="map" style="height: 350px; border-radius: .35rem; border: 1px solid #e3e6f0;"></div>
                </div>
            </div>
            <div class="card shadow mb-4">
                 <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i>Riwayat Perkembangan</h6>
                    <a href="{{ route('admin.report-status.create', $report->id) }}" class="btn btn-sm btn-primary shadow-sm">
                        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Progress
                    </a>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @forelse ($report->reportStatuses->sortBy('created_at') as $status)
                            <div class="timeline-item">
                                @php
                                    $icon = match($status->status) {
                                        \App\Enums\ReportStatusEnum::DELIVERED => ['icon' => 'fa-paper-plane', 'bg' => 'bg-primary'],
                                        \App\Enums\ReportStatusEnum::IN_PROCESS => ['icon' => 'fa-cogs', 'bg' => 'bg-warning'],
                                        \App\Enums\ReportStatusEnum::COMPLETED => ['icon' => 'fa-check-circle', 'bg' => 'bg-success'],
                                        \App\Enums\ReportStatusEnum::REJECTED => ['icon' => 'fa-times-circle', 'bg' => 'bg-danger'],
                                    };
                                @endphp
                                <div class="timeline-icon {{ $icon['bg'] }}"><i class="fas {{ $icon['icon'] }}"></i></div>
                                <div class="timeline-content">
                                    <h6 class="font-weight-bold">{{ $status->status->label() }} <span class="text-muted font-weight-normal">oleh</span> <span class="font-weight-bold text-capitalize">{{ $status->created_by_role }}</span></h6>
                                    <p class="small text-muted mb-2"><i class="far fa-clock"></i> {{ $status->created_at->isoFormat('dddd, D MMMM YYYY - HH:mm') }} WIB</p>
                                    <p>{{ $status->description }}</p>
                                    @if($status->image)
                                        <a href="{{ asset('storage/' . $status->image) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $status->image) }}" class="img-thumbnail proof-image" alt="Bukti Progress">
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                             <div class="timeline-item">
                                <div class="timeline-icon bg-secondary"><i class="fas fa-question-circle"></i></div>
                                <div class="timeline-content"><p class="text-muted">Belum ada riwayat perkembangan untuk laporan ini.</p></div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user mr-2"></i>Informasi Pelapor</h6>
                </div>
                <div class="card-body">
                    <dl class="info-dl">
                        <dt>Nama Lengkap</dt>
                        <dd>{{ optional($resident->user)->name }}</dd>
                        <dt>Email</dt>
                        <dd>{{ optional($resident->user)->email }}</dd>
                        <dt>Nomor Telepon</dt>
                        <dd>{{ optional($resident)->phone ?? 'Tidak diisi' }}</dd>
                        <dt>Wilayah</dt>
                        <dd>RT {{ optional($resident->rt)->number }} / RW {{ optional($resident->rw)->number }}</dd>
                    </dl>
                </div>
                 <div class="card-footer text-center">
                    <a href="{{ route('admin.resident.show', $resident->id) }}" class="btn btn-sm btn-outline-primary">Lihat Profil Lengkap Pelapor</a>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-2"></i>Detail Teknis</h6>
                </div>
                <div class="card-body">
                    <dl class="info-dl">
                        <dt>Kode Laporan</dt>
                        <dd class="text-monospace">{{ $report->code }}</dd>
                        <dt>Status Terakhir</dt>
                        <dd>{{ $report->latestStatus ? $report->latestStatus->status->label() : 'Baru' }}</dd>
                        <dt>Tanggal Dibuat</dt>
                        <dd>{{ $report->created_at->isoFormat('D MMM YYYY, HH:mm') }}</dd>
                        <dt>Visibilitas</dt>
                        <dd>{{ $report->visibility->label(Auth::user()) }}</dd>
                        {{-- [PENAMBAHAN] Menampilkan informasi koordinat --}}
                        <dt>Koordinat</dt>
                        <dd class="text-monospace small">{{ $report->latitude }}, {{ $report->longitude }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([{{ $report->latitude }}, {{ $report->longitude }}], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    L.marker([{{ $report->latitude }}, {{ $report->longitude }}]).addTo(map)
        .bindPopup('<b>{{ Str::limit($report->title, 20) }}</b><br>Lokasi kejadian.')
        .openPopup();
</script>
@endsection