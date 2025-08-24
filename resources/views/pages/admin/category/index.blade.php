@extends('layouts.admin')

@section('title', 'Data Kategori Laporan')

@push('styles')
<style>
    .table thead th {
        font-weight: 600;
        color: #5a5c69;
        background-color: #f8f9fc;
        border-bottom-width: 1px;
    }
    .table td, .table th {
        vertical-align: middle;
    }
    .table tbody tr:hover {
        background-color: #f8f9fc;
    }
    .badge-report-count {
        font-size: 0.9em;
        font-weight: 600;
        padding: .5em .8em;
    }
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem;
        background-color: #f8f9fc;
        border-radius: .75rem;
    }
</style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Manajemen Kategori</h1>
            <p class="mb-0 text-muted">Buat, ubah, atau hapus kategori untuk pelaporan warga.</p>
        </div>
        <a href="{{ route('admin.report-category.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-2"></i>Tambah Kategori Baru
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Kategori Laporan</h6>
        </div>
        <div class="card-body">
            @if($categories->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-tags fa-4x text-gray-300 mb-3"></i>
                    <h5 class="font-weight-bold">Belum Ada Kategori</h5>
                    <p class="text-muted">Silakan tambahkan kategori laporan pertama Anda.</p>
                    <a href="{{ route('admin.report-category.create') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-plus mr-1"></i> Buat Kategori Baru
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th class="text-center">Jumlah Laporan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-pill badge-primary badge-report-count">{{ $category->reports_count }} Laporan</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.report-category.show', $category) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye fa-sm mr-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection