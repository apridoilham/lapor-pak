@extends('layouts.admin')

@section('title', 'Riwayat Login')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Riwayat Login</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Aktivitas Login</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            {{-- Tampilkan kolom User hanya jika yang melihat adalah Super Admin --}}
                            @role('super-admin')
                                <th>User</th>
                            @endrole
                            <th>Alamat IP</th>
                            <th>Perangkat (User Agent)</th>
                            <th>Waktu Login</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activities as $activity)
                            <tr>
                                @role('super-admin')
                                    <td>{{ $activity->user ? $activity->user->name : 'N/A' }}</td>
                                @endrole
                                <td>{{ $activity->ip_address }}</td>
                                <td>{{ \Str::limit($activity->user_agent, 80) }}</td>
                                <td>{{ \Carbon\Carbon::parse($activity->login_at)->tz('Asia/Jakarta')->isoFormat('dddd, D MMMM YYYY, HH:mm:ss') }}</td>
                            </tr>
                        @empty
                            <tr>
                                {{-- Sesuaikan colspan berdasarkan peran --}}
                                <td colspan="{{ auth()->user()->hasRole('super-admin') ? '4' : '3' }}" class="text-center">Belum ada aktivitas login.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
@endsection