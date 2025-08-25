@extends('layouts.admin')

@section('title', 'Data Laporan')

@push('styles')
<style>
    .table thead th { background-color: #f8f9fc; border-bottom-width: 1px; font-weight: 600; color: #5a5c69; }
    .table td, .table th { vertical-align: middle; }
    .table tbody tr:hover { background-color: #f8f9fc; }
    .avatar-in-table { width: 40px; height: 40px; object-fit: cover; }
    .soft-badge { font-size: 0.8rem; font-weight: 600; padding: .4em .8em; border-radius: 20px; }
    .soft-badge.badge-success { background-color: #d1fae5; color: #065f46; }
    .soft-badge.badge-warning { background-color: #fef3c7; color: #92400e; }
    .soft-badge.badge-danger { background-color: #fee2e2; color: #991b1b; }
    .soft-badge.badge-primary { background-color: #dbeafe; color: #1e40af; }
    .soft-badge.badge-secondary { background-color: #e5e7eb; color: #4b5563; }
</style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Data Laporan (Warga)</h1>
            <p class="mb-0 text-muted">Kelola dan lihat semua laporan yang masuk dari warga.</p>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.report.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-9">
                    <label for="sort" class="form-label font-weight-bold small">Urutkan Berdasarkan</label>
                    <select name="sort" id="sort" class="form-control" onchange="this.form.submit()">
                        <option value="latest_updated" {{ request('sort', 'latest_updated') == 'latest_updated' ? 'selected' : '' }}>Terakhir Diperbarui</option>
                        <option value="latest_created" {{ request('sort') == 'latest_created' ? 'selected' : '' }}>Terbaru Dibuat</option>
                        <option value="oldest_created" {{ request('sort') == 'oldest_created' ? 'selected' : '' }}>Terlama Dibuat</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama Pelapor (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama Pelapor (Z-A)</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex">
                    <button type="submit" class="btn btn-primary flex-grow-1 mr-2">
                        <i class="fas fa-filter fa-sm"></i> Terapkan
                    </button>
                    <a href="{{ route('admin.report.index') }}" class="btn btn-secondary" title="Reset Filter">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Semua Laporan</h6>
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
                            <th>Tanggal Update</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reports as $report)
                            <tr>
                                <td><a href="{{ route('admin.report.show', $report->id) }}" class="font-weight-bold">{{ $report->code }}</a></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @php
                                            $avatarUrl = optional($report->resident->user)->avatar ?? $report->resident->avatar;
                                            if ($avatarUrl && !filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
                                                $avatarUrl = asset('storage/' . $avatarUrl);
                                            } elseif (empty($avatarUrl)) {
                                                $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode(optional($report->resident->user)->name) . '&background=1a202c&color=fff&size=60';
                                            }
                                        @endphp
                                        <img class="img-profile rounded-circle avatar-in-table mr-3" src="{{ $avatarUrl }}">
                                        <div>
                                            <div class="font-weight-bold text-dark">{{ optional($report->resident->user)->name }}</div>
                                            <div class="small text-muted">RT {{ optional($report->resident->rt)->number }} / RW {{ optional($report->resident->rw)->number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ Str::limit($report->title, 40) }}</td>
                                <td class="text-center">
                                    @if ($report->latestStatus)
                                        @php $status = $report->latestStatus->status; @endphp
                                        <span class="soft-badge badge-{{ $status->colorClass() }}">{{ $status->label() }}</span>
                                    @else
                                        <span class="soft-badge badge-primary">Baru</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $displayTime = optional($report->latestStatus)->created_at ?? $report->created_at;
                                    @endphp
                                    <div class="text-dark">{{ $displayTime->isoFormat('D MMM YYYY') }}</div>
                                    <div class="small text-muted">{{ $displayTime->format('H:i') }} WIB</div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.report.show', $report->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye fa-sm mr-1"></i>Detail
                                    </a>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(Auth::user()->hasRole('super-admin'))
        const rwSelect = document.getElementById('rw_id_filter');
        const rtSelect = document.getElementById('rt_id_filter');
        const currentRtId = "{{ request('rt') }}";

        function fetchRts(rwId, selectedRtId = null) {
            if (!rwId) {
                rtSelect.innerHTML = '<option value="">Pilih RW terlebih dahulu</option>';
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

        rwSelect.addEventListener('change', function() { fetchRts(this.value); });

        if (rwSelect.value) { fetchRts(rwSelect.value, currentRtId); }
        @endif
    });
</script>
@endpush