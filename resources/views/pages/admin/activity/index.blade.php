@extends('layouts.admin')

@section('title', 'Riwayat Login')

@push('styles')
<style>
    /* Custom Table Styling */
    .table thead th {
        background-color: #f8f9fc;
        border-bottom-width: 1px;
        font-weight: 600;
        color: #5a5c69;
    }
    .table td, .table th {
        vertical-align: middle;
    }
    .table tbody tr:hover {
        background-color: #f8f9fc;
    }
    .avatar-in-table {
        width: 36px;
        height: 36px;
        object-fit: cover;
    }
</style>
@endpush

@section('content')
    <h1 class="h3 mb-4 text-gray-900 font-weight-bold">Riwayat Login</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Aktivitas Login Admin</h6>
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
                                                 src="https://ui-avatars.com/api/?name={{ urlencode($activity->user->name ?? 'N/A') }}&background=1a202c&color=fff&size=60">
                                            <div>
                                                <div class="font-weight-bold text-dark">{{ $activity->user->name ?? 'Pengguna Dihapus' }}</div>
                                                <div class="small text-muted">{{ $activity->user->email ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                @endrole
                                <td>
                                    <span class="badge badge-secondary">{{ $activity->ip_address }}</span>
                                </td>
                                <td>
                                    <span title="{{ $activity->user_agent }}">{{ \Illuminate\Support\Str::limit($activity->user_agent, 60) }}</span>
                                </td>
                                <td class="text-right">
                                    <div class="text-dark">{{ \Carbon\Carbon::parse($activity->login_at)->tz('Asia/Jakarta')->isoFormat('dddd, D MMMM YYYY') }}</div>
                                    <div class="small text-muted">{{ \Carbon\Carbon::parse($activity->login_at)->tz('Asia/Jakarta')->format('HH:mm:ss') }} ({{ \Carbon\Carbon::parse($activity->login_at)->diffForHumans() }})</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('super-admin') ? '4' : '3' }}" class="text-center py-5">
                                    <i class="fas fa-history fa-2x text-gray-400 mb-2"></i>
                                    <p class="text-muted">Belum ada aktivitas login yang tercatat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
@endsection