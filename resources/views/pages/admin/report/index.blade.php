@extends('layouts.admin')

@section('title', 'Data Laporan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="{{ route('admin.report.create') }}" class="btn btn-primary">Tambah Data</a>
            {{-- Tombol ekspor lama sudah dihapus dari sini --}}
        </div>
        
        <form action="{{ route('admin.report.index') }}" method="GET" class="d-flex">
            <input type="text" class="form-control" name="search" placeholder="Cari laporan..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary ml-2">Cari</button>
        </form>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Laporan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Laporan</th>
                            <th>Pelapor</th>
                            <th>Kategori Pelapor</th>
                            <th>Judul Laporan</th>
                            <th>Bukti Laporan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reports as $report)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $report->code }}</td>
                                <td>{{ $report->resident->user->name }}</td>
                                <td>{{ $report->reportCategory->name }}</td>
                                <td>{{ $report->title }}</td>
                                <td>
                                    <img src="{{ asset('storage/' . $report->image) }}" alt="image" width="100">
                                </td>
                                <td>
                                    <a href="{{ route('admin.report.edit', $report->id) }}" class="btn btn-warning btn-sm mb-1">Edit</a>
                                    <a href="{{ route('admin.report.show', $report->id) }}" class="btn btn-info btn-sm mb-1">Show</a>
                                    <form action="{{ route('admin.report.destroy', $report->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm mb-1">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Data tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "destroy": true,
            "searching": false,
            "paging": true,
            "ordering": true,
            "info": true
        });
    });
</script>
@endsection