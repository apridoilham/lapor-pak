@extends('layouts.admin')
@section('title', 'Detail Pelapor: ' . $resident->user->name)

@push('styles')
<style>
    .profile-header-card { background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%); border: none; }
    .profile-avatar { width: 100px; height: 100px; border: 4px solid #fff; box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,.1); object-fit: cover; }
    .profile-meta-list { list-style: none; padding: 0; margin: 0; }
    .profile-meta-list li { display: flex; align-items: flex-start; font-size: 0.9rem; color: #5a5c69; margin-bottom: 0.75rem; }
    .profile-meta-list li:last-child { margin-bottom: 0; }
    .profile-meta-list i { color: #b7b9cc; width: 20px; text-align: center; margin-right: 0.75rem; margin-top: 4px; }
    .stat-card-v2 { background-color: #fff; border: 1px solid #e3e6f0; border-radius: .75rem; padding: 1.25rem; transition: all 0.3s ease; border-bottom-width: 4px; }
    .stat-card-v2:hover { transform: translateY(-5px); box-shadow: 0 1rem 3rem rgba(0,0,0,.075)!important; }
    .stat-card-v2.border-bottom-dark { border-color: #5a5c69; }
    .stat-card-v2.border-bottom-primary { border-color: #4e73df; }
    .stat-card-v2.border-bottom-warning { border-color: #f6c23e; }
    .stat-card-v2.border-bottom-success { border-color: #1cc88a; }
    .stat-card-v2.border-bottom-danger { border-color: #e74a3b; }
    .stat-card-v2 .stat-icon { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #fff; flex-shrink: 0; }
    .stat-card-v2 .stat-value { font-size: 1.75rem; font-weight: 700; color: #343a40; }
    .stat-card-v2 .stat-label { font-size: 0.8rem; color: #858796; text-transform: uppercase; font-weight: 500; }
    .table thead th { background-color: #f8f9fc; border-bottom: 1px solid #dee2e6; font-weight: 600; color: #5a5c69; }
    .table tbody tr:hover { background-color: #f8f9fc; }
    .table td, .table th { vertical-align: middle; padding: 1rem; }
    .soft-badge { font-size: 0.8rem; font-weight: 600; padding: .4em .8em; border-radius: 20px; }
    .soft-badge.badge-success { background-color: #d1fae5; color: #065f46; }
    .soft-badge.badge-warning { background-color: #fef3c7; color: #92400e; }
    .soft-badge.badge-danger { background-color: #fee2e2; color: #991b1b; }
    .soft-badge.badge-primary { background-color: #dbeafe; color: #1e40af; }
    .soft-badge.badge-secondary { background-color: #e5e7eb; color: #4b5563; }
</style>
@endpush

@section('content')
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.resident.index') }}" class="btn btn-outline-primary btn-circle mr-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Detail Pelapor</h1>
            <p class="mb-0 text-muted">Ringkasan profil dan aktivitas warga.</p>
        </div>
    </div>

    <div class="card shadow-sm mb-4 profile-header-card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                @php
                    $avatarUrl = optional($resident->user)->avatar ?? $resident->avatar;
                    if ($avatarUrl && !filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
                        $avatarUrl = asset('storage/' . $avatarUrl);
                    } elseif (empty($avatarUrl)) {
                        $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($resident->user->name) . '&background=1a202c&color=fff&size=128&font-size=0.33';
                    }
                @endphp
                <img src="{{ $avatarUrl }}" alt="Avatar" class="rounded-circle profile-avatar mr-4">
                <div class="w-100">
                    <h2 class="font-weight-bold text-gray-800 mb-1">{{ $resident->user->name }}</h2>
                    <p class="text-muted mb-3">{{ $resident->user->email }}</p>
                    <hr class="mt-0 mb-3">
                    <ul class="profile-meta-list">
                        <li><i class="fas fa-map-marker-alt fa-fw"></i> RT {{ $resident->rt->number }} / RW {{ $resident->rw->number }}</li>
                        <li><i class="fas fa-phone fa-fw"></i> {{ $resident->phone ?? 'Belum diisi' }}</li>
                        <li><i class="fas fa-calendar-alt fa-fw"></i> Terdaftar {{ $resident->created_at->diffForHumans() }}</li>
                    </ul>
                    <ul class="profile-meta-list mt-2">
                        <li><i class="fas fa-home fa-fw"></i> <span>{{ $resident->address }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <h6 class="text-uppercase text-muted font-weight-bold small mb-3">Ringkasan Aktivitas</h6>
    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5">
        <div class="col mb-4">
            <div class="card stat-card-v2 h-100 shadow-sm border-bottom-dark">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-dark mr-3"><i class="fas fa-file-alt"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['total'] }}</div>
                        <div class="stat-label">Total Laporan</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col mb-4">
            <div class="card stat-card-v2 h-100 shadow-sm border-bottom-primary">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-primary mr-3"><i class="fas fa-paper-plane"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['delivered'] }}</div>
                        <div class="stat-label">Terkirim</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col mb-4">
            <div class="card stat-card-v2 h-100 shadow-sm border-bottom-warning">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-warning mr-3"><i class="fas fa-cogs"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['in_process'] }}</div>
                        <div class="stat-label">Diproses</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col mb-4">
            <div class="card stat-card-v2 h-100 shadow-sm border-bottom-success">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-success mr-3"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['completed'] }}</div>
                        <div class="stat-label">Selesai</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col mb-4">
            <div class="card stat-card-v2 h-100 shadow-sm border-bottom-danger">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-danger mr-3"><i class="fas fa-times-circle"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['rejected'] }}</div>
                        <div class="stat-label">Ditolak</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Laporan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th class="text-center">Status</th>
                            <th>Tanggal Update</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($resident->reports as $report)
                            <tr>
                                <td><a href="{{ route('admin.report.show', $report->id) }}" class="font-weight-bold">{{ $report->code }}</a></td>
                                <td>{{ Str::limit($report->title, 35) }}</td>
                                <td>{{ $report->reportCategory->name }}</td>
                                <td class="text-center">
                                    @if ($report->latestStatus)
                                        @php $status = $report->latestStatus->status; @endphp
                                        <span class="soft-badge badge-{{ $status->colorClass() }}">{{ $status->label() }}</span>
                                    @else
                                        <span class="soft-badge badge-primary">Baru</span>
                                    @endif
                                </td>
                                <td>{{ ($report->latestStatus ? $report->latestStatus->updated_at : $report->created_at)->isoFormat('D MMM Y, HH:mm') }}</td>
                                <td class="text-right">
                                    <a href="{{ route('admin.report.show', $report->id) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail Laporan">
                                        <i class="fas fa-eye fa-sm mr-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div id="lottie-empty" style="width: 150px; height: 150px; margin: 0 auto;"></div>
                                    <p class="text-muted mt-2">Pelapor ini belum pernah membuat laporan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.2/lottie.min.js"></script>
<script>
    var lottieContainer = document.getElementById('lottie-empty');
    if (lottieContainer) {
        bodymovin.loadAnimation({
            container: lottieContainer,
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: '{{ asset('assets/app/lottie/empty-box.json') }}'
        });
    }
</script>
@endsection