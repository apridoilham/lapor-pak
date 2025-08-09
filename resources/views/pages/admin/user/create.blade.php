@extends('layouts.admin')

@section('title', 'Tambah Admin Baru')

@section('content')
    <a href="{{ route('admin.admin-user.index') }}" class="btn btn-danger mb-3">Kembali</a>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Admin</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.admin-user.store')}}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    {{-- ▼▼▼ PERUBAHAN DI SINI ▼▼▼ --}}
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    {{-- ▼▼▼ PERUBAHAN DI SINI ▼▼▼ --}}
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
@endsection