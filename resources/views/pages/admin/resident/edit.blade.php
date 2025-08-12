@extends('layouts.admin')
@section('title', 'Ubah Data Pelapor')
@section('content')
    <a href="{{ route('admin.resident.index') }}" class="btn btn-danger mb-3">Kembali</a>
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Form Ubah Data Pelapor</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.resident.update', $resident->id) }}" method="POST" enctype="multipart/form-data" id="edit-resident-form">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $resident->user->name) }}" required>
                </div>

                @php
                    $emailUsername = old('email_username', explode('@', $resident->user->email)[0]);
                @endphp
                <div class="form-group">
                    <label for="email_username">Email</label>
                    <div class="input-group">
                        <input type="text" class="form-control @error('email_username') is-invalid @enderror @error('email') is-invalid @enderror" id="email_username" name="email_username" value="{{ $emailUsername }}" required>
                        <div class="input-group-append">
                            <span class="input-group-text">@bsblapor.com</span>
                        </div>
                        <div class="invalid-feedback" id="email-error">Email sudah ada sebelumnya.</div>
                    </div>
                    @error('email_username')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Foto Profil (Kosongkan jika tidak diubah)</label>
                    <img src="{{ asset('storage/' . $resident->avatar) }}" alt="avatar" width="100" class="d-block mb-2">
                    <input type="file" class="form-control" name="avatar">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="rw_id">RW</label>
                            <select name="rw_id" id="rw_id" class="form-control" required>
                                <option value="" disabled>Pilih RW</option>
                                @foreach($rws as $rw)
                                    <option value="{{ $rw->id }}" {{ old('rw_id', $resident->rw_id) == $rw->id ? 'selected' : '' }}>{{ $rw->number }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="rt_id">RT</label>
                            <select name="rt_id" id="rt_id" class="form-control" required>
                                <option value="" disabled>Pilih RW Dulu</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Alamat Lengkap</label>
                    <textarea name="address" class="form-control" rows="3" required>{{ old('address', $resident->address) }}</textarea>
                </div>
                <hr>
                <p class="text-muted">Kosongkan jika tidak ingin meresetnya.</p>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" name="password" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-primary" id="update-btn" disabled>Simpan Perubahan</button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rwSelect = document.getElementById('rw_id');
            const rtSelect = document.getElementById('rt_id');
            const activeRtId = "{{ old('rt_id', $resident->rt_id) }}";

            function fetchRts(rwId, selectedRtId = null) {
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
                                if (selectedRtId && rt.id == selectedRtId) {
                                    option.selected = true;
                                }
                                rtSelect.appendChild(option);
                            });
                            rtSelect.disabled = false;
                        });
                }
            }
            
            rwSelect.addEventListener('change', function() {
                fetchRts(this.value);
            });

            if (rwSelect.value) {
                fetchRts(rwSelect.value, activeRtId);
            }

            const form = document.getElementById('edit-resident-form');
            const updateButton = document.getElementById('update-btn');
            const inputs = form.querySelectorAll('input, select, textarea');
            const emailInput = document.getElementById('email_username');
            const emailError = document.getElementById('email-error');
            let initialFormState = {};

            inputs.forEach(input => {
                if (input.type === 'file' || input.type === 'password' || input.name === '_token' || input.name === '_method') return;
                initialFormState[input.name] = input.value;
            });

            function checkForChanges() {
                let hasChanged = false;
                inputs.forEach(input => {
                    if (input.type === 'file' && input.files.length > 0) {
                        hasChanged = true;
                    } else if (input.type === 'password' && input.value.length > 0) {
                        hasChanged = true;
                    } else if (initialFormState.hasOwnProperty(input.name) && initialFormState[input.name] !== input.value) {
                        hasChanged = true;
                    }
                });

                const isEmailInvalid = emailInput.classList.contains('is-invalid');
                updateButton.disabled = !hasChanged || isEmailInvalid;
            }

            inputs.forEach(input => {
                input.addEventListener('input', checkForChanges);
                input.addEventListener('change', checkForChanges);
            });

            let debounceTimer;
            emailInput.addEventListener('input', function () {
                checkForChanges();
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
                            body: JSON.stringify({
                                email_username: emailUsername,
                                ignore_user_id: {{ $resident->user->id }}
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.is_taken) {
                                emailInput.classList.add('is-invalid');
                                emailError.classList.add('d-block');
                            }
                            checkForChanges();
                        });
                    }
                }, 500);
            });
        });
    </script>
@endsection