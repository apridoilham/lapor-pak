@extends('layouts.admin')

@section('title', 'Edit Profil Saya')

@section('content')
    <a href="{{ route('profile') }}" class="btn btn-danger mb-3">Kembali ke Profil</a>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Profil</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" disabled>
                    <small class="form-text text-muted">Email tidak dapat diubah.</small>
                </div>

                @if ($user->rw)
                <div class="form-group">
                    <label for="rw">Wilayah RW</label>
                    <input type="text" class="form-control" id="rw" name="rw" value="RW {{ $user->rw->number }}" disabled>
                    <small class="form-text text-muted">Wilayah RW tidak dapat diubah.</small>
                </div>
                @endif
                
                <hr>
                <p class="text-muted">Kosongkan password jika tidak ingin mengubahnya.</p>
                <div class="form-group">
                    <label for="current_password">Password Lama</label>
                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" autocomplete="new-password">
                     @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" autocomplete="new-password">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                </div>
                
                <button type="submit" class="btn btn-primary">Update Profil</button>
            </form>
        </div>
    </div>
@endsection