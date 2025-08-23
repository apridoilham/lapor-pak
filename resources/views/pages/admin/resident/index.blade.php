@extends('layouts.admin')
@section('title', 'Data Pelapor')

@push('styles')
<style>
    .table thead th {
        font-weight: 600; color: #5a5c69; background-color: #f8f9fc; border-bottom-width: 1px;
    }
    .table td, .table th { vertical-align: middle; }
    .table tbody tr:hover { background-color: #f8f9fc; }
    .avatar-in-table { width: 40px; height: 40px; object-fit: cover; }
    .badge-report-count { font-size: 0.9em; padding: .5em .8em; }
</style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Data Pelapor (Warga)</h1>
            <p class="mb-0 text-muted">Lihat dan kelola semua data warga yang terdaftar sebagai pelapor.</p>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.resident.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="sort" class="form-label font-weight-bold small">Urutkan Berdasarkan</label>
                    <select name="sort" id="sort" class="form-control">
                        <option value="terbaru" {{ request('sort', 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                        <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                        <option value="nama_asc" {{ request('sort') == 'nama_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                        <option value="nama_desc" {{ request('sort') == 'nama_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                        <option value="laporan_terbanyak" {{ request('sort') == 'laporan_terbanyak' ? 'selected' : '' }}>Laporan Terbanyak</option>
                        <option value="laporan_sedikit" {{ request('sort') == 'laporan_sedikit' ? 'selected' : '' }}>Laporan Terdikit</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="rw_id" class="form-label font-weight-bold small">Filter RW</label>
                    <select name="rw_id" id="rw_id" class="form-control" onchange="this.form.submit()">
                        <option value="">Semua RW</option>
                        @foreach($rws as $rw)
                            <option value="{{ $rw->id }}" {{ request('rw_id') == $rw->id ? 'selected' : '' }}>RW {{ $rw->number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="rt_id" class="form-label font-weight-bold small">Filter RT</label>
                    <select name="rt_id" id="rt_id" class="form-control" {{ !request('rw_id') ? 'disabled' : '' }}>
                        <option value="">Semua RT</option>
                        @if(request('rw_id'))
                            @foreach($rts as $rt)
                                <option value="{{ $rt->id }}" {{ request('rt_id') == $rt->id ? 'selected' : '' }}>RT {{ $rt->number }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3 d-flex">
                    <button type="submit" class="btn btn-primary flex-grow-1 mr-2">
                        <i class="fas fa-filter fa-sm"></i> Terapkan
                    </button>
                    <a href="{{ route('admin.resident.index') }}" class="btn btn-secondary" title="Reset Filter">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pelapor</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Pelapor</th>
                            <th class="text-center">Wilayah</th>
                            <th class="text-center">Laporan Dibuat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($residents as $resident)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @php
                                            $avatarUrl = optional($resident->user)->avatar ?? $resident->avatar;
                                            if ($avatarUrl && !filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
                                                $avatarUrl = asset('storage/' . $avatarUrl);
                                            } elseif (empty($avatarUrl)) {
                                                $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode(optional($resident->user)->name) . '&background=1a202c&color=fff&size=60';
                                            }
                                        @endphp
                                        <img class="img-profile rounded-circle avatar-in-table mr-3" src="{{ $avatarUrl }}">
                                        <div>
                                            <div class="font-weight-bold text-dark">{{ optional($resident->user)->name }}</div>
                                            <div class="small text-muted">{{ optional($resident->user)->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">RT {{ optional($resident->rt)->number }} / RW {{ optional($resident->rw)->number }}</td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-primary badge-report-count">{{ $resident->reports_count }} Laporan</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.resident.show', $resident->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye fa-sm mr-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="fas fa-user-slash fa-3x text-gray-400 mb-3"></i>
                                    <p class="text-muted font-weight-bold">Data Pelapor Tidak Ditemukan</p>
                                    <p class="text-muted small">Coba ubah filter atau urutan data Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $residents->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rwSelect = document.getElementById('rw_id');
        const rtSelect = document.getElementById('rt_id');
        const currentRtId = "{{ request('rt_id') }}";

        function fetchRts(rwId, selectedRtId = null) {
            if (!rwId) {
                rtSelect.innerHTML = '<option value="">Pilih RW Dulu</option>';
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

        if (rwSelect.value) {
            fetchRts(rwSelect.value, currentRtId);
        }
    });
</script>
@endpush