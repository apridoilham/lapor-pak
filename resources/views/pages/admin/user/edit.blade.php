@extends('layouts.admin')

@section('title', 'Edit Data Admin')

@section('content')
    <a href="{{ route('admin.admin-user.index') }}" class="btn btn-danger mb-3">Kembali</a>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Ubah Data Admin</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.admin-user.update', $admin->id) }}" method="POST" id="edit-admin-form">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $admin->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                @php
                    $emailUsername = old('email_username', explode('@', $admin->email)[0]);
                @endphp
                <div class="form-group">
                    <label for="email_username">Email</label>
                    <div class="input-group">
                        <input type="text" class="form-control @error('email_username') is-invalid @enderror @error('email') is-invalid @enderror" id="email_username" name="email_username" value="{{ $emailUsername }}" required>
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
                        <option value="" disabled>Pilih RW untuk admin ini</option>
                        @foreach ($rws as $rw)
                            <option value="{{ $rw->id }}" {{ old('rw_id', $admin->rw_id) == $rw->id ? 'selected' : '' }}>RW {{ $rw->number }}</option>
                        @endforeach
                    </select>
                     @error('rw_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <hr>
                <p class="text-muted">Kosongkan password jika tidak ingin mengubahnya.</p>
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" autocomplete="new-password">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                </div>
                
                <button type="submit" class="btn btn-primary" id="update-btn" disabled>Simpan Perubahan</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('edit-admin-form');
        const updateButton = document.getElementById('update-btn');
        const inputs = form.querySelectorAll('input, select');
        let initialFormState = {};

        inputs.forEach(input => {
            if (input.type === 'password' || input.name === '_token' || input.name === '_method') return;
            initialFormState[input.name] = input.value;
        });

        function checkForChanges() {
            let hasChanged = false;
            
            for (const input of inputs) {
                if (input.type === 'password') {
                    if (input.value.length > 0) {
                        hasChanged = true;
                        break;
                    }
                } else if (initialFormState.hasOwnProperty(input.name) && initialFormState[input.name] !== input.value) {
                    hasChanged = true;
                    break;
                }
            }
            
            updateButton.disabled = !hasChanged;
        }

        inputs.forEach(input => {
            input.addEventListener('input', checkForChanges);
            input.addEventListener('change', checkForChanges);
        });
    });
</script>
@endsection