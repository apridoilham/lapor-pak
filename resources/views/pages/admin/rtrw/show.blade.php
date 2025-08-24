@extends('layouts.admin')
@section('title', 'Detail RW ' . $rw->number)

@push('styles')
<style>
    .kpi-card { border-left: 4px solid #4e73df; }
    .rt-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 1rem; }
    .rt-card { display: flex; flex-direction: column; justify-content: center; align-items: center; background-color: #f8f9fc; border: 1px solid #e3e6f0; border-radius: .5rem; padding: 1.5rem; text-align: center; transition: all .2s ease-in-out; aspect-ratio: 3 / 4; }
    .rt-card:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15); }
    .rt-card .rt-number { font-size: 2.25rem; font-weight: 700; color: #4e73df; line-height: 1.2; }
    .rt-card .rt-label { font-size: 0.9rem; color: #858796; margin-top: 0.25rem; }
    .table thead th { background-color: #f8f9fc; border-bottom-width: 1px; font-weight: 600; color: #5a5c69; }
    .table td, .table th { vertical-align: middle; }
    .table tbody tr:hover { background-color: #f8f9fc; }
    .avatar-in-table { width: 40px; height: 40px; object-fit: cover; }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.rtrw.index') }}" class="btn btn-primary btn-circle mr-3" title="Kembali">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Detail Wilayah</h1>
                <p class="mb-0 text-muted">RW {{ $rw->number }}</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.rtrw.edit', $rw) }}" class="btn btn-sm btn-outline-warning shadow-sm">
                <i class="fas fa-edit fa-sm mr-2"></i>Ubah Data
            </a>
            <form action="{{ route('admin.rtrw.destroy', $rw) }}" method="POST" class="d-inline delete-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm"
                        data-title="Hapus RW {{ $rw->number }}?" 
                        data-text="RW hanya dapat dihapus jika tidak ada lagi data warga yang terikat padanya. Lanjutkan?">
                    <i class="fas fa-trash fa-sm mr-2"></i>Hapus RW
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card kpi-card shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Jumlah RT</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rw->rts->count() }} RT</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-sitemap fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card kpi-card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Warga Terdaftar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $residentCount }} Warga</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow border-0 mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Rukun Tetangga (RT) di RW {{ $rw->number }}</h6>
        </div>
        <div class="card-body">
            @if($rw->rts->isEmpty())
                <p class="text-center text-muted">Belum ada data RT di wilayah RW ini.</p>
            @else
                <div class="rt-grid">
                    @foreach ($rw->rts->sortBy('number') as $rt)
                        <div class="rt-card">
                            <div class="rt-number">{{ $rt->number }}</div>
                            <div class="rt-label">RT</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- KARTU BARU UNTUK DAFTAR WARGA --}}
    <div class="card shadow border-0 mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Warga Terdaftar di RW {{ $rw->number }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Pelapor</th>
                            <th class="text-center">Wilayah RT</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rw->residents as $resident)
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
                                <td class="text-center">RT {{ optional($resident->rt)->number }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.resident.show', $resident->id) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail Pelapor">
                                        <i class="fas fa-eye fa-sm mr-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <i class="fas fa-user-slash fa-2x text-gray-400 mb-2"></i>
                                    <p class="text-muted">Belum ada warga yang terdaftar di RW ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const button = this.querySelector('button[type="submit"]');
            Swal.fire({
                title: button.dataset.title || 'Anda yakin?',
                text: button.dataset.text || 'Tindakan ini tidak dapat dibatalkan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush