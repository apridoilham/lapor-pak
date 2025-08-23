@extends('layouts.admin')

@section('title', 'Riwayat Login')

@push('styles')
<style>
    .card-activity-log .card-header {
        background-color: #fff;
        border-bottom: 1px solid #eaecf4;
    }
    .table thead th {
        background-color: #f8f9fc;
        border-bottom-width: 1px;
        font-weight: 600;
        color: #5a5c69;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .table td, .table th {
        vertical-align: middle;
        padding: 1rem;
    }
    .table tbody tr:hover {
        background-color: #f8f9fc;
    }
    .avatar-in-table {
        width: 40px;
        height: 40px;
        object-fit: cover;
    }
    .user-info .font-weight-bold {
        color: #3a3b45;
    }
    .user-info .small {
        color: #858796;
    }
    .badge-ip {
        font-size: 0.8rem;
        font-weight: 600;
        padding: .4em .7em;
    }
    .time-info .main-time {
        font-weight: 500;
        color: #5a5c69;
    }
    .time-info .sub-time {
        font-size: 0.8rem;
        color: #858796;
    }
    .pagination .page-item .page-link {
        border-radius: .35rem;
        margin: 0 3px;
    }
</style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-900 font-weight-bold">Riwayat Login</h1>
    </div>

    <div class="card shadow-sm border-0 card-activity-log mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history mr-2"></i>Riwayat Aktivitas Login Admin
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            @role('super-admin')
                                <th>Pengguna</th>
                            @endrole
                            <th>Alamat IP</th>
                            <th>Perangkat (User Agent)</th>
                            <th class="text-right">Waktu Login</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activities as $activity)
                            <tr>
                                @role('super-admin')
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img class="img-profile rounded-circle avatar-in-table mr-3"
                                                 src="{{ optional($activity->user)->avatar ? optional($activity->user)->avatar : 'https://ui-avatars.com/api/?name=' . urlencode(optional($activity->user)->name ?? 'N/A') . '&background=1a202c&color=fff&size=60' }}">
                                            <div class="user-info">
                                                <div class="font-weight-bold">{{ optional($activity->user)->name ?? 'Pengguna Dihapus' }}</div>
                                                <div class="small">{{ optional($activity->user)->email ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                @endrole
                                <td>
                                    <span class="badge badge-secondary badge-ip">{{ $activity->ip_address }}</span>
                                </td>
                                <td>
                                    <span title="{{ $activity->user_agent }}">{{ \Illuminate\Support\Str::limit($activity->user_agent, 60) }}</span>
                                </td>
                                <td class="text-right time-info">
                                    <div class="main-time">{{ \Carbon\Carbon::parse($activity->login_at)->tz('Asia/Jakarta')->isoFormat('dddd, D MMMM YYYY') }}</div>
                                    <div class="sub-time">{{ \Carbon\Carbon::parse($activity->login_at)->tz('Asia/Jakarta')->format('HH:mm:ss') }} ({{ \Carbon\Carbon::parse($activity->login_at)->diffForHumans() }})</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('super-admin') ? '4' : '3' }}" class="text-center py-5">
                                    <i class="fas fa-history fa-3x text-gray-300 mb-3"></i>
                                    <p class="text-muted">Belum ada aktivitas login yang tercatat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($activities->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $activities->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection