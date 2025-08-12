@extends('layouts.admin')

@section('title', 'Manajemen Admin')

@section('content')
    <a href="{{ route('admin.admin-user.create') }}" class="btn btn-primary mb-3">Tambah Admin</a>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengguna Admin</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Wilayah RW</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($admins as $admin)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $admin->name }}</td>
                                <td>{{ $admin->email }}</td>
                                <td>
                                    @if($admin->rw)
                                        RW {{ $admin->rw->number }}
                                    @else
                                        <span class="badge badge-warning">Super Admin</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.admin-user.edit', $admin->id) }}" class="btn btn-warning btn-sm">Ubah</a>
                                    <a href="{{ route('admin.admin-user.show', $admin->id) }}" class="btn btn-info btn-sm">Lihat</a>
                                    <form action="{{ route('admin.admin-user.destroy', $admin->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" data-title="Hapus Admin?" data-text="Anda yakin ingin menghapus admin RW {{ $admin->rw->number }}?">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection