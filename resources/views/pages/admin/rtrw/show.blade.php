@extends('layouts.admin')
@section('title', 'Detail RW ' . $rw->number)

@push('styles')
<style>
    .kpi-card {
        border-left: 4px solid #4e73df;
    }
    .rt-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 1rem;
    }
    .rt-card {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background-color: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: .5rem;
        padding: 1.5rem;
        text-align: center;
        transition: all .2s ease-in-out;
        aspect-ratio: 3 / 4;
    }
    .rt-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
    }
    .rt-card .rt-number {
        font-size: 2.25rem;
        font-weight: 700;
        color: #4e73df;
        line-height: 1.2;
    }
    .rt-card .rt-label {
        font-size: 0.9rem;
        color: #858796;
        margin-top: 0.25rem;
    }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.rtrw.index') }}" class="btn btn-outline-primary btn-circle mr-3" title="Kembali">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Detail Wilayah RW {{ $rw->number }}</h1>
            </div>
        </div>
        <div>
            {{-- PERUBAHAN GAYA TOMBOL DI SINI --}}
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

    <div class="card shadow border-0">
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