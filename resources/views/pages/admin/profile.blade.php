@extends('layouts.admin')

@section('title', 'Profil Admin')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Profil Saya</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Akun</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px;">Nama</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Peran (Role)</th>
                            <td>
                                @foreach ($user->getRoleNames() as $role)
                                    <span class="badge badge-success text-capitalize">{{ $role }}</span>
                                @endforeach
                            </td>
                        </tr>
                    </table>
                    {{-- Tombol ini sekarang bisa dilihat semua admin dan mengarah ke route baru --}}
                    @role('admin|super-admin')
                        <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary">Edit Profil</a>
                    @endrole
                </div>
            </div>
        </div>
    </div>
@endsection