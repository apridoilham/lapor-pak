@extends('layouts.admin')
@section('title', 'Tambah Data Pelapor')
@section('content')
    <a href="{{ route('admin.resident.index') }}" class="btn btn-danger mb-3">Kembali</a>
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Data Pelapor</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.resident.store') }}" method="POST" enctype="multipart/form-data" id="create-resident-form">
                @csrf
                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
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
                    <label for="avatar">Foto Profil</label>
                    <input type="file" class="form-control @error('avatar') is-invalid @enderror" name="avatar" required>
                    @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="rw_id">RW</label>
                            @if($rws->isEmpty())
                                <select class="form-control" disabled>
                                    <option>Tidak ada data RW. Silakan buat terlebih dahulu.</option>
                                </select>
                                <small class="form-text text-muted">
                                    <a href="{{ route('admin.rtrw.index') }}">Klik di sini untuk menambah data RT/RW baru.</a>
                                </small>
                            @else
                                <select name="rw_id" id="rw_id" class="form-control @error('rw_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>Pilih RW</option>
                                    @foreach($rws as $rw)
                                        <option value="{{ $rw->id }}">{{ $rw->number }}</option>
                                    @endforeach
                                </select>
                                @error('rw_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="rt_id">RT</label>
                            <select name="rt_id" id="rt_id" class="form-control @error('rt_id') is-invalid @enderror" required disabled>
                                <option value="" disabled selected>Pilih RW Dulu</option>
                            </select>
                            @error('rt_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Alamat Lengkap</label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3" required>{{ old('address') }}</textarea>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <hr>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-primary" id="simpan-btn" disabled>Tambah Pelapor</button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rwSelect = document.getElementById('rw_id');
            const rtSelect = document.getElementById('rt_id');
            
            function fetchRts(rwId) {
                rtSelect.disabled = true;
                rtSelect.innerHTML = '<option value="">Memuat...</option>';
                if (rwId) {
                    fetch(`/api/get-rts-by-rw/${rwId}`)
                        .then(response => response.json())
                        .then(data => {
                            rtSelect.innerHTML = '<option value="" disabled selected>Pilih RT</option>';
                            data.forEach(rt => {
                                const option = document.createElement('option');
                                option.value = rt.id;
                                option.textContent = rt.number;
                                rtSelect.appendChild(option);
                            });
                            rtSelect.disabled = false;
                        });
                }
            }
            
            if (rwSelect) {
                rwSelect.addEventListener('change', function() {
                    fetchRts(this.value);
                });
            }

            const form = document.getElementById('create-resident-form');
            const saveButton = document.getElementById('simpan-btn');
            const requiredInputs = form.querySelectorAll('[required]');

            function checkFormValidity() {
                let allFieldsFilled = true;
                requiredInputs.forEach(input => {
                    if (input.type === 'file') {
                        if (input.files.length === 0) {
                            allFieldsFilled = false;
                        }
                    } else if (input.value.trim() === '') {
                        allFieldsFilled = false;
                    }
                });
                
                const isEmailInvalid = document.getElementById('email_username').classList.contains('is-invalid');
                saveButton.disabled = !allFieldsFilled || isEmailInvalid;
            }

            requiredInputs.forEach(input => {
                input.addEventListener('input', checkFormValidity);
                input.addEventListener('change', checkFormValidity);
            });

            const emailInput = document.getElementById('email_username');
            const emailError = document.getElementById('email-error');
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
                            } else {
                                emailInput.classList.remove('is-invalid');
                                emailError.classList.remove('d-block');
                            }
                            checkFormValidity();
                        });
                    }
                }, 500);
            });
        });
    </script>
@endsection