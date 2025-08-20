@extends('layouts.admin')
@section('title', 'Data Pelapor')

@push('styles')
<style>
    .table thead th {
        font-weight: 700;
        color: #5a5c69;
        background-color: #f8f9fc;
        border-bottom-width: 1px;
    }
    .table td, .table th {
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Data Pelapor (Warga)</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Wilayah</th>
                            <th>Laporan Dibuat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($residents as $resident)
                            <tr>
                                <td>{{ $resident->user->name }}</td>
                                <td>{{ $resident->user->email }}</td>
                                <td>RT {{ $resident->rt->number }} / RW {{ $resident->rw->number }}</td>
                                <td>{{ $resident->reports_count }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.resident.show', $resident->id) }}" class="btn btn-info btn-sm">
                                        <i class="fa fa-eye"></i> Detail
                                    </a>
                                    <form action="{{ route('admin.resident.destroy', $resident->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit" data-title="Hapus {{ $resident->user->name }}?" data-text="Semua data laporan yang dibuat oleh pengguna ini juga akan ikut terhapus. Lanjutkan?">
                                            <i class="fa fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data pelapor.</td>
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
    document.addEventListener('DOMContentLoaded', function() {
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
    });
</script>
@endpush