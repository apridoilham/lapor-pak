@extends('layouts.admin')

@section('title', 'Data Pelapor')

@push('styles')
<style>
    .resident-card { transition: all 0.3s ease; border: 1px solid #e3e6f0; }
    .resident-card:hover { transform: translateY(-5px); box-shadow: 0 1rem 3rem rgba(0,0,0,.1)!important; border-color: #4e73df; }
    .resident-card .card-body { padding: 1.5rem; }
    .resident-card .avatar-container { text-align: center; margin-bottom: 1rem; }
    .resident-card .avatar { width: 100px; height: 100px; object-fit: cover; border: 4px solid #fff; box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15); }
    .resident-card .resident-name { font-weight: 700; font-size: 1.2rem; color: #2c3e50; }
    .resident-card .resident-email { font-size: 0.9rem; color: #858796; }
    .resident-card .info-list { list-style: none; padding: 0; font-size: 0.9rem; }
    .resident-card .info-list li { display: flex; align-items: center; color: #5a5c69; margin-bottom: 0.5rem; }
    .resident-card .info-list i { width: 20px; text-align: center; margin-right: 0.75rem; color: #b7b9cc; }
    .resident-card .card-footer { background-color: #f8f9fc; border-top: 1px solid #e3e6f0; }
</style>
@endpush

@section('content')
    <h1 class="h3 mb-4 text-gray-900 font-weight-bold">Data Pelapor</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Warga Pelapor</h6>
            
            {{-- [PERBAIKAN] Tata letak form filter disamakan dengan halaman Data Laporan --}}
            <form action="{{ route('admin.resident.index') }}" method="GET" class="d-flex align-items-center">
                <label for="sort" class="small font-weight-bold text-muted mr-2 mb-0">Urutkan:</label>
                <select name="sort" id="sort" class="form-control form-control-sm mr-3" style="width: auto;" onchange="this.form.submit()">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                    <option value="reports_desc" {{ request('sort') == 'reports_desc' ? 'selected' : '' }}>Laporan Terbanyak</option>
                </select>

                @role('super-admin')
                    <label class="small font-weight-bold text-muted mr-2 mb-0">Filter:</label>
                    <select name="rw" id="rw_id_filter" class="form-control form-control-sm mr-2" style="width: auto;">
                        <option value="">Semua RW</option>
                        @foreach ($rws as $rw)
                            <option value="{{ $rw->id }}" {{ request('rw') == $rw->id ? 'selected' : '' }}>RW {{ $rw->number }}</option>
                        @endforeach
                    </select>
                    <select name="rt" id="rt_id_filter" class="form-control form-control-sm mr-2" style="width: auto;" disabled>
                        <option value="">Pilih RT</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary" title="Terapkan Filter">Filter</button>
                    <a href="{{ route('admin.resident.index') }}" class="btn btn-sm btn-outline-secondary ml-1" title="Reset Filter"><i class="fas fa-sync-alt"></i></a>
                @endrole
            </form>
        </div>
        <div class="card-body bg-light">
            <div class="row">
                @forelse ($residents as $resident)
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card resident-card h-100 shadow-sm">
                            <div class="card-header bg-white border-0 py-3 text-right">
                                <a href="{{ route('admin.resident.show', $resident->id) }}" class="btn btn-sm btn-outline-primary">
                                    Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                            <div class="card-body pt-0">
                                <div class="avatar-container">
                                    @php
                                        $avatarUrl = $resident->avatar;
                                        if ($avatarUrl && !Str::startsWith($avatarUrl, 'http')) {
                                            $avatarUrl = asset('storage/' . $avatarUrl);
                                        } elseif (!$avatarUrl) {
                                            $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($resident->user->name) . '&background=4e73df&color=fff&size=100';
                                        }
                                    @endphp
                                    <img src="{{ $avatarUrl }}" alt="Avatar" class="rounded-circle avatar">
                                </div>
                                <div class="text-center">
                                    <h5 class="resident-name">{{ $resident->user->name }}</h5>
                                    <p class="resident-email">{{ $resident->user->email }}</p>
                                </div>
                                <hr>
                                <ul class="info-list">
                                    <li><i class="fas fa-map-marker-alt"></i> <span>RT {{ $resident->rt->number }} / RW {{ $resident->rw->number }}</span></li>
                                    <li><i class="fas fa-phone"></i> <span>{{ $resident->phone ?? 'Belum diisi' }}</span></li>
                                </ul>
                            </div>
                            <div class="card-footer text-center">
                                <small class="text-muted">Total Laporan Dibuat: </small>
                                <span class="font-weight-bold">{{ $resident->reports_count }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-user-slash fa-3x text-gray-400 mb-3"></i>
                        <p class="text-muted">Data pelapor tidak ditemukan.</p>
                    </div>
                @endforelse
            </div>
             <div class="d-flex justify-content-center mt-3">
                {{ $residents->links() }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
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
    rwSelect.addEventListener('change', function() { fetchRts(this.value); });
    if (rwSelect.value) { fetchRts(rwSelect.value, currentRtId); }
    @endrole
});
</script>
@endsection