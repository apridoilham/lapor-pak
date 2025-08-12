@extends('layouts.no-nav')

@section('title', 'Daftar Akun Baru')

@section('content')
    <div class="header-nav mb-4">
        <a href="{{ route('login') }}" class="text-decoration-none">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1>Daftar Pengguna Baru</h1>
    </div>
    
    <p class="text-muted mb-4">
        Silakan mengisi form di bawah ini untuk mendaftar sebagai warga.
    </p>

    <form action="{{ route('register.store') }}" method="POST" class="mt-4" enctype="multipart/form-data" id="register-form">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autocomplete="name">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        
        <div class="mb-3">
            <label for="email_username" class="form-label">Email</label>
            <div class="input-group">
                <input type="text" class="form-control @error('email_username') is-invalid @enderror @error('email') is-invalid @enderror" id="email_username" name="email_username" value="{{ old('email_username') }}" required autocomplete="email">
                <span class="input-group-text">@bsblapor.com</span>
                <div class="invalid-feedback" id="email-error">Email sudah ada sebelumnya.</div>
            </div>
            @error('email_username')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="avatar" class="form-label">Foto Profil</label>
            <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" required>
            @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        
        <hr class="my-4">

        <div class="row">
            <div class="col-6">
                 <div class="mb-3">
                    <label for="rw_id" class="form-label">Pilih RW</label>
                    <select name="rw_id" id="rw_id" class="form-select @error('rw_id') is-invalid @enderror" required>
                        <option value="" disabled selected>Pilih RW Anda</option>
                        @foreach($rws as $rw)
                            <option value="{{ $rw->id }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>{{ $rw->number }}</option>
                        @endforeach
                    </select>
                    @error('rw_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-6">
                <div class="mb-3">
                    <label for="rt_id" class="form-label">Pilih RT</label>
                    <select name="rt_id" id="rt_id" class="form-select @error('rt_id') is-invalid @enderror" required disabled>
                        <option value="" disabled selected>Pilih RW terlebih dahulu</option>
                    </select>
                    @error('rt_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Alamat Lengkap (Nama Jalan, No. Rumah)</label>
            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3" required placeholder="Contoh: Jl. Merdeka No. 12">{{ old('address') }}</textarea>
            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        
        <hr class="my-4">

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
        </div>
        
        <div class="d-grid mt-4">
            <button class="btn btn-primary py-2" type="submit" id="register-btn" disabled>Daftar</button>
        </div>
        
        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-decoration-none text-primary">Sudah punya akun? Masuk</a>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rwSelect = document.getElementById('rw_id');
            const rtSelect = document.getElementById('rt_id');
            const oldRtId = "{{ old('rt_id') }}";

            function fetchRts(rwId, selectedRtId = null) {
                rtSelect.disabled = true;
                rtSelect.innerHTML = '<option value="">Memuat RT...</option>';
                if (!rwId) return;

                fetch(`/api/get-rts-by-rw/${rwId}`)
                    .then(response => response.json())
                    .then(data => {
                        rtSelect.innerHTML = '<option value="" disabled selected>Pilih RT</option>';
                        if (data.length > 0) {
                            data.forEach(rt => {
                                const option = document.createElement('option');
                                option.value = rt.id;
                                option.textContent = rt.number;
                                if (selectedRtId && rt.id == selectedRtId) {
                                    option.selected = true;
                                }
                                rtSelect.appendChild(option);
                            });
                            rtSelect.disabled = false;
                        } else {
                            rtSelect.innerHTML = '<option value="" disabled selected>Tidak ada RT</option>';
                        }
                    });
            }

            rwSelect.addEventListener('change', function() { fetchRts(this.value); });
            if (rwSelect.value) { fetchRts(rwSelect.value, oldRtId); }

            const form = document.getElementById('register-form');
            const registerButton = document.getElementById('register-btn');
            const requiredInputs = form.querySelectorAll('[required]');
            const emailInput = document.getElementById('email_username');
            const emailError = document.getElementById('email-error');

            function checkFormValidity() {
                let allFieldsFilled = true;
                requiredInputs.forEach(input => {
                    if (input.type === 'file') {
                        if (input.files.length === 0) allFieldsFilled = false;
                    } else if (input.value.trim() === '') {
                        allFieldsFilled = false;
                    }
                });
                
                const isEmailInvalid = emailInput.classList.contains('is-invalid');
                registerButton.disabled = !allFieldsFilled || isEmailInvalid;
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