@extends('layouts.admin')

@section('title', 'Data Laporan')

@push('styles')
<style>
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
        width: 40px;
        height: 40px;
        object-fit: cover;
    }
    .action-dropdown .dropdown-toggle::after {
        display: none;
    }
    .soft-badge {
        font-size: 0.8rem;
        font-weight: 600;
        padding: .4em .8em;
        border-radius: 20px;
    }
    .soft-badge.badge-success { background-color: #d1fae5; color: #065f46; }
    .soft-badge.badge-warning { background-color: #fef3c7; color: #92400e; }
    .soft-badge.badge-danger { background-color: #fee2e2; color: #991b1b; }
    .soft-badge.badge-primary { background-color: #dbeafe; color: #1e40af; }
    .soft-badge.badge-secondary { background-color: #e5e7eb; color: #4b5563; }
</style>
@endpush

@section('content')
    <h1 class="h3 mb-4 text-gray-900 font-weight-bold">Data Laporan</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Semua Laporan</h6>
            <div class="d-flex align-items-center">
                <form action="{{ route('admin.report.index') }}" method="GET" class="d-none d-md-inline-flex form-inline mr-3">
                    @role('super-admin')
                    <select name="rw" id="rw_id_filter" class="form-control form-control-sm mr-2">
                        <option value="">Semua RW</option>
                        @foreach ($rws as $rw)
                            <option value="{{ $rw->id }}" {{ request('rw') == $rw->id ? 'selected' : '' }}>
                                RW {{ $rw->number }}
                            </option>
                        @endforeach
                    </select>
                    @endrole
                    <select name="rt" id="rt_id_filter" class="form-control form-control-sm mr-2" {{ auth()->user()->hasRole('super-admin') ? 'disabled' : '' }}>
                        @role('super-admin')
                            <option value="">Pilih RW</option>
                        @else
                            <option value="">Semua RT</option>
                            @foreach ($rts as $rt)
                                <option value="{{ $rt->id }}" {{ request('rt') == $rt->id ? 'selected' : '' }}>
                                    RT {{ $rt->number }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <button type="submit" class="btn btn-sm btn-info">Filter</button>
                    <a href="{{ route('admin.report.index') }}" class="btn btn-sm btn-secondary ml-1">Reset</a>
                </form>
                <a href="{{ route('admin.report.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus fa-sm mr-1"></i> Tambah Laporan
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Pelapor</th>
                            <th>Judul Laporan</th>
                            <th class="text-center">Status</th>
                            <th>Waktu Dibuat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reports as $report)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.report.show', $report->id) }}" class="font-weight-bold">{{ $report->code }}</a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @php
                                            $avatarUrl = $report->resident->avatar;
                                            if ($avatarUrl && !Str::startsWith($avatarUrl, 'http')) {
                                                $avatarUrl = asset('storage/' . $avatarUrl);
                                            } elseif (!$avatarUrl) {
                                                $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($report->resident->user->name) . '&background=1a202c&color=fff&size=60';
                                            }
                                        @endphp
                                        <img class="img-profile rounded-circle avatar-in-table mr-3" src="{{ $avatarUrl }}">
                                        <div>
                                            <div class="font-weight-bold text-dark">{{ $report->resident->user->name }}</div>
                                            <div class="small text-muted">RT {{ $report->resident->rt->number }}/RW {{ $report->resident->rw->number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ Str::limit($report->title, 40) }}</td>
                                <td class="text-center">
                                     @if ($report->latestStatus)
                                        @php
                                            $status = $report->latestStatus->status;
                                            $badgeClass = match($status) {
                                                \App\Enums\ReportStatusEnum::DELIVERED => 'badge-primary',
                                                \App\Enums\ReportStatusEnum::IN_PROCESS => 'badge-warning',
                                                \App\Enums\ReportStatusEnum::COMPLETED => 'badge-success',
                                                \App\Enums\ReportStatusEnum::REJECTED => 'badge-danger',
                                                default => 'badge-secondary',
                                            };
                                        @endphp
                                        <span class="soft-badge {{ $badgeClass }}">{{ $status->label() }}</span>
                                    @else
                                        <span class="soft-badge badge-secondary">Baru</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-dark">{{ $report->created_at->isoFormat('D MMM YYYY') }}</div>
                                    <div class="small text-muted">{{ $report->created_at->format('HH:mm') }} WIB</div>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown no-arrow action-dropdown">
                                        <a class="dropdown-toggle btn btn-sm btn-light" href="#" role="button" id="dropdownMenuLink{{ $report->id }}" data-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v text-gray-600"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink{{ $report->id }}">
                                            <a class="dropdown-item" href="{{ route('admin.report.show', $report->id) }}"><i class="fas fa-eye fa-sm fa-fw mr-2 text-gray-400"></i> Lihat Detail</a>
                                            <a class="dropdown-item" href="{{ route('admin.report.edit', $report->id) }}"><i class="fas fa-pencil-alt fa-sm fa-fw mr-2 text-gray-400"></i> Ubah</a>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('admin.report.destroy', $report->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" data-title="Hapus Laporan?" data-text="Anda yakin ingin menghapus laporan '{{ Str::limit($report->title, 20) }}'?">
                                                    <i class="fas fa-trash fa-sm fa-fw mr-2"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-search-minus fa-2x text-gray-400 mb-2"></i>
                                    <p class="text-muted">Data laporan tidak ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
{{-- Script untuk konfirmasi hapus dan filter dependent dropdown --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    @role('super-admin')
    const rwSelect = document.getElementById('rw_id_filter');
    const rtSelect = document.getElementById('rt_id_filter');
    const currentRtId = "{{ request('rt') }}";

    function fetchRts(rwId, selectedRtId = null) {
        if (!rwId) {
            rtSelect.innerHTML = '<option value="">Pilih RW</option>';
            rtSelect.disabled = true;
            return;
        }

        rtSelect.disabled = true;
        rtSelect.innerHTML = '<option value="">Memuat...</option>';

        fetch(`/api/get-rts-by-rw/${rwId}`)
            .then(response => response.json())
            .then(data => {
                rtSelect.innerHTML = '<option value="">Semua RT</option>';
                data.forEach(rt => {
                    const option = document.createElement('option');
                    option.value = rt.id;
                    option.textContent = `RT ${rt.number}`;
                    if (selectedRtId && rt.id == selectedRtId) {
                        option.selected = true;
                    }
                    rtSelect.appendChild(option);
                });
                rtSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error fetching RT data:', error);
                rtSelect.innerHTML = '<option value="">Gagal memuat</option>';
            });
    }

    rwSelect.addEventListener('change', function() {
        fetchRts(this.value);
    });

    if (rwSelect.value) {
        fetchRts(rwSelect.value, currentRtId);
    }
    @endrole
});
</script>
@endsection