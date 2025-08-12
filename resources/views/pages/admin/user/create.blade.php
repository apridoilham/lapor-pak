@extends('layouts.admin')

@section('title', 'Tambah Admin Baru')

@section('content')
    <a href="{{ route('admin.admin-user.index') }}" class="btn btn-danger mb-3">Kembali</a>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Admin</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.admin-user.store')}}" method="POST" id="create-admin-form">
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
                        <div class="invalid-feedback" id="email-error">Email sudah ada sebelumnya.</div>
                    </div>
                    @error('email_username')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="rw_id">Wilayah RW</label>
                    @if($rws->isEmpty())
                        <select class="form-control" disabled>
                            <option>Tidak ada data RW. Silakan buat terlebih dahulu.</option>
                        </select>
                        <small class="form-text text-muted">
                            <a href="{{ route('admin.rtrw.index') }}">Klik di sini untuk menambah data RT/RW baru.</a>
                        </small>
                    @else
                        <select name="rw_id" id="rw_id" class="form-control @error('rw_id') is-invalid @enderror" required>
                            <option value="" disabled selected>Pilih RW untuk admin ini</option>
                            @foreach ($rws as $rw)
                                <option value="{{ $rw->id }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>RW {{ $rw->number }}</option>
                            @endforeach
                        </select>
                        @error('rw_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @endif
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
                <button type="submit" class="btn btn-primary" id="simpan-btn" disabled>Simpan</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('create-admin-form');
        const saveButton = document.getElementById('simpan-btn');
        const requiredInputs = form.querySelectorAll('[required]');
        const emailInput = document.getElementById('email_username');
        const emailError = document.getElementById('email-error');

        function checkFormValidity() {
            let allFieldsFilled = true;
            requiredInputs.forEach(input => {
                if (input.value.trim() === '') {
                    allFieldsFilled = false;
                }
            });

            const isEmailInvalid = emailInput.classList.contains('is-invalid');
            saveButton.disabled = !allFieldsFilled || isEmailInvalid;
        }

        requiredInputs.forEach(input => {
            input.addEventListener('input', checkFormValidity);
            input.addEventListener('change', checkFormValidity);
        });

        let debounceTimer;
        emailInput.addEventListener('input', function () {
            checkFormValidity();
            clearTimeout(debounceTimer);
            
            emailInput.classList.remove('is-invalid');
            emailError.classList.remove('d-block');

            debounceTimer = setTimeout(function () {
                const emailUsername = emailInput.value;
                if (emailUsername.length > 2) {
                    fetch('/api/check-email', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ email_username: emailUsername })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.is_taken) {
                            emailInput.classList.add('is-invalid');
                            emailError.classList.add('d-block');
                        }
                        checkFormValidity();
                    });
                }
            }, 500);
        });
    });
</script>
@endsection