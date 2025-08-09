@extends('layouts.admin')

@section('title', 'Edit Admin')

@section('content')
    <a href="{{ route('admin.admin-user.index') }}" class="btn btn-danger mb-3">Kembali</a>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Admin</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.admin-user.update', $admin->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $admin->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $admin->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <hr>
                <p class="text-muted">Kosongkan password jika tidak ingin mengubahnya.</p>
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    {{-- ▼▼▼ PERUBAHAN DI SINI ▼▼▼ --}}
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" autocomplete="new-password">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password Baru</label>
                    {{-- ▼▼▼ PERUBAHAN DI SINI ▼▼▼ --}}
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
@endsection