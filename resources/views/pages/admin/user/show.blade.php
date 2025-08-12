@extends('layouts.admin')

@section('title', 'Detail Admin')

@section('content')
    <a href="{{ route('admin.admin-user.index') }}" class="btn btn-danger mb-3">Kembali</a>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Admin RW {{ $admin->rw->number }}</h6>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th style="width: 200px;">Nama</th>
                    <td>{{ $admin->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $admin->email }}</td>
                </tr>
                <tr>
                    <th>Peran (Role)</th>
                    <td>
                        @foreach ($admin->getRoleNames() as $role)
                            <span class="badge badge-success text-capitalize">{{ $role }}</span>
                        @endforeach
                    </td>
                </tr>
                @if ($admin->rw)
                <tr>
                    <th>Wilayah RW</th>
                    <td>RW {{ $admin->rw->number }}</td>
                </tr>
                @endif
                <tr>
                    <th>Tanggal Dibuat</th>
                    <td>{{ $admin->created_at->isoFormat('dddd, D MMMM YYYY') }}</td>
                </tr>
            </table>
        </div>
    </div>
@endsection