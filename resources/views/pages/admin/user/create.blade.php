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
                    <label for="email_username">Email</label>
                    <div class="input-group">
                        <input type="text" class="form-control @error('email_username') is-invalid @enderror @error('email') is-invalid @enderror" id="email_username" name="email_username" value="{{ old('email_username') }}" required>
                        <div class="input-group-append">
                            <span class="input-group-text">@bsblapor.com</span>
                        </div>
                    </div>
                    @error('email_username')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="rw_id">Wilayah RW</label>
                    <select name="rw_id" id="rw_id" class="form-control @error('rw_id') is-invalid @enderror" required>
                        <option value="" disabled selected>Pilih RW untuk admin ini</option>
                        @foreach ($rws as $rw)
                            <option value="{{ $rw->id }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>RW {{ $rw->number }}</option>
                        @endforeach
                    </select>
                     @error('rw_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
@endsection