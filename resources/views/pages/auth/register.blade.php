@extends('layouts.no-nav')

@section('title', 'Daftar')

@section('content')
    <h5 class="fw-bold mt-5">Daftar sebagai pengguna baru</h5>
    <p class="text-muted mt-2">Silahkan mengisi form dibawah ini untuk mendaftar</p>

    <form action="{{ route('register.store') }}" method="POST" class="mt-4" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="avatar" class="form-label">Foto Profil</label>
            <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" required>
            @error('avatar')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
        </div>

        <button class="btn btn-primary w-100 mt-2" type="submit">
            Daftar
        </button>

        <div class="d-flex justify-content-center mt-3">
            <a href="{{ route('login') }}" class="text-decoration-none text-primary">Sudah punya akun? Masuk</a>
        </div>
    </form>
@endsection