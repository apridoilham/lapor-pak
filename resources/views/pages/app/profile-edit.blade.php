@extends('layouts.app')
@section('title', 'Edit Profil')

@push('styles')
<style>
    .avatar-upload-container {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto;
    }
    .avatar-upload-container .avatar-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .avatar-upload-container .avatar-edit-button {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background-color: var(--primary);
        color: white;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .avatar-upload-container .avatar-edit-button:hover {
        background-color: var(--primaryHover);
    }
</style>
@endpush

@section('content')
    <div class="header-nav mb-4">
        <a href="{{ route('profile') }}" class="text-decoration-none">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1>Edit Profil</h1>
    </div>

    <form action="{{ route('profile.update') }}" method="POST" class="mt-4" enctype="multipart/form-data" id="profile-form">
        @csrf
        @method('PUT')

        <div class="avatar-upload-container mb-4">
            <img src="{{ $user->resident->avatar ? asset('storage/' . $user->resident->avatar) : asset('assets/app/images/default-avatar.png') }}" alt="avatar" class="avatar-preview" id="avatar-preview">
            <label for="avatar" class="avatar-edit-button">
                <i class="fa-solid fa-camera"></i>
            </label>
            <input type="file" name="avatar" id="avatar" class="form-control d-none @error('avatar') is-invalid @enderror">
        </div>
        @error('avatar')
            <div class="invalid-feedback d-block text-center mb-3">{{ $message }}</div>
        @enderror

        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <hr class="my-4">

        <div class="row">
            <div class="col-6">
                 <div class="mb-3">
                    <label for="rw_id" class="form-label">Pilih RW</label>
                    <select name="rw_id" id="rw_id" class="form-select @error('rw_id') is-invalid @enderror" required>
                        <option value="" disabled>Pilih RW Anda</option>
                        @foreach($rws as $rw)
                            <option value="{{ $rw->id }}" {{ old('rw_id', $user->resident->rw_id) == $rw->id ? 'selected' : '' }}>{{ $rw->number }}</option>
                        @endforeach
                    </select>
                    @error('rw_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-6">
                <div class="mb-3">
                    <label for="rt_id" class="form-label">Pilih RT</label>
                    <select name="rt_id" id="rt_id" class="form-select @error('rt_id') is-invalid @enderror" required {{ !$user->resident->rw_id ? 'disabled' : '' }}>
                        <option value="" disabled selected>Pilih RW terlebih dahulu</option>
                    </select>
                    @error('rt_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Alamat Lengkap (Nama Jalan, No. Rumah)</label>
            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3" required>{{ old('address', $user->resident->address) }}</textarea>
            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        
        <hr class="my-4">
        
        <p class="text-muted">Kosongkan jika tidak ingin mengubah password.</p>
        
        <div class="mb-3">
            <label for="current_password" class="form-label">Password Lama</label>
            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" autocomplete="new-password">
            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password Baru</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" autocomplete="new-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
        </div>
        
        <div class="d-grid gap-3 mt-4">
             <button class="btn btn-primary py-2" type="submit" id="save-changes-btn" disabled>Simpan Perubahan</button>
             <a href="{{ route('profile') }}" class="btn btn-link text-secondary text-decoration-none">Kembali</a>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('sweetalert::alert')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Logika Tombol Simpan Aktif/Nonaktif
            const form = document.getElementById('profile-form');
            const saveButton = document.getElementById('save-changes-btn');
            const inputs = form.querySelectorAll('input, select, textarea');
            let initialFormState = {};
            inputs.forEach(input => {
                if (input.type === 'file') {
                    initialFormState[input.name] = null;
                } else {
                    initialFormState[input.name] = input.value;
                }
            });

            function checkForChanges() {
                let hasChanged = false;
                inputs.forEach(input => {
                    if (input.type === 'file') {
                        if (input.files.length > 0) hasChanged = true;
                    } else if (initialFormState[input.name] !== input.value) {
                        hasChanged = true;
                    }
                });
                saveButton.disabled = !hasChanged;
            }
            inputs.forEach(input => {
                input.addEventListener('input', checkForChanges);
                if (input.type === 'file' || input.tagName.toLowerCase() === 'select') {
                    input.addEventListener('change', checkForChanges);
                }
            });

            // Logika Pratinjau Avatar
            document.getElementById('avatar').addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('avatar-preview').src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Logika Dropdown RT/RW Bertingkat
            const rwSelect = document.getElementById('rw_id');
            const rtSelect = document.getElementById('rt_id');
            const activeRtId = "{{ old('rt_id', $user->resident->rt_id) }}";

            function fetchRts(rwId, selectedRtId = null) {
                rtSelect.disabled = true;
                rtSelect.innerHTML = '<option value="" disabled selected>Memuat RT...</option>';
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
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        rtSelect.innerHTML = '<option value="" disabled selected>Gagal memuat RT</option>';
                    });
            }

            rwSelect.addEventListener('change', function() {
                fetchRts(this.value);
            });

            if (rwSelect.value) {
                fetchRts(rwSelect.value, activeRtId);
            }

            // Logika Konfirmasi Submit dengan SweetAlert
            form.addEventListener('submit', function(event) {
                if (saveButton.disabled) {
                    event.preventDefault();
                    return;
                }
                event.preventDefault();
                Swal.fire({
                    title: 'Simpan Perubahan?',
                    text: "Apakah Anda yakin ingin menyimpan perubahan pada profil Anda?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16752B',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection