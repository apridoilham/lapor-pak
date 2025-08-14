@extends('layouts.app')

@section('title', 'Ubah Profil')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Variabel Desain */
    :root {
        --primary-color: #10B981;
        --primary-gradient: linear-gradient(135deg, #10B981 0%, #34D399 100%);
        --text-dark: #111827;
        --text-light: #6B7280;
        --bg-body: #F9FAFB;
        --bg-white: #FFFFFF;
        --border-color: #e5e7eb;
        --font-sans: 'Inter', 'Poppins', 'Segoe UI', sans-serif;
    }

    /* Pengaturan Dasar */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    html, body { background-color: var(--bg-body); }
    body {
        font-family: var(--font-sans);
        max-width: 480px; margin: 0 auto;
        min-height: 100vh;
        box-shadow: 0 0 40px rgba(0, 0, 0, 0.07);
        background-color: var(--bg-body);
    }
    .main-content { padding: 1.5rem; padding-bottom: 100px; }

    /* Header Halaman */
    .page-header {
        display: flex; align-items: center; gap: 1rem;
        margin-bottom: 2rem;
    }
    .page-header a { font-size: 1.5rem; color: var(--text-dark); }
    .page-header h3 { font-weight: 800; font-size: 1.75rem; color: var(--text-dark); margin: 0; }

    /* Avatar Upload */
    .avatar-upload-container {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto 2rem;
    }
    .avatar-preview {
        width: 100%; height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--bg-white);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .avatar-edit-button {
        position: absolute;
        bottom: 5px; right: 5px;
        width: 36px; height: 36px;
        background: var(--primary-color);
        color: white;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        border: 2px solid var(--white);
        cursor: pointer;
    }

    /* Kartu Section Form */
    .form-section-card {
        background-color: var(--bg-white);
        padding: 1.5rem;
        border-radius: 20px;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
    .form-section-card .section-title {
        font-weight: 600; font-size: 1.1rem;
        color: var(--text-dark); margin-bottom: 1.5rem;
    }

    /* Form Controls Modern */
    .form-label { font-weight: 600; font-size: 0.9rem; color: var(--text-dark); margin-bottom: 0.5rem; }
    .form-control, .form-select {
        border-radius: 12px;
        border: 1px solid var(--border-color);
        background-color: var(--bg-body);
        padding: 0.8rem 1rem;
        transition: all 0.2s ease;
    }
    .form-control:focus, .form-select:focus {
        background-color: var(--bg-white);
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    .form-control:disabled { background-color: #e9ecef; }
    
    #map { height: 200px; border-radius: 12px; margin-bottom: 1rem; }

    /* ▼▼▼ CSS BARU UNTUK GRUP TOMBOL ▼▼▼ */
    .action-buttons-container {
        display: grid;
        grid-template-columns: 1fr 2fr; /* Tombol batal lebih kecil */
        gap: 1rem;
        margin-top: 2rem;
    }
    .action-buttons-container .btn {
        width: 100%; padding: 0.9rem;
        border-radius: 16px; border: none;
        font-weight: 700; font-size: 1rem;
        transition: all 0.3s ease;
    }
    .action-buttons-container .btn-cancel {
        background-color: #e5e7eb;
        color: var(--text-light);
    }
    .action-buttons-container .btn-save {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 8px 20px -5px rgba(16, 185, 129, 0.5);
    }
    .action-buttons-container .btn-save:disabled {
        background: #d1d5db;
        box-shadow: none;
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')
    <div class="page-header">
        <a href="{{ route('profile') }}"><i class="fa-solid fa-arrow-left"></i></a>
        <h3>Ubah Profil</h3>
    </div>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profile-form">
        @csrf
        @method('PUT')

        <div class="avatar-upload-container">
            @php
                $avatarUrl = $user->resident->avatar;
                if ($avatarUrl && !Str::startsWith($avatarUrl, 'http')) {
                    $avatarUrl = asset('storage/' . $avatarUrl);
                } elseif (!$avatarUrl) {
                    $avatarUrl = asset('assets/app/images/default-avatar.png');
                }
            @endphp
            <img src="{{ $avatarUrl }}" alt="avatar" class="avatar-preview" id="avatar-preview">
            <label for="avatar-input" class="avatar-edit-button">
                <i class="fa-solid fa-camera"></i>
            </label>
            <input type="file" name="avatar" id="avatar-input" class="d-none" accept="image/*">
        </div>
        @error('avatar')<div class="invalid-feedback d-block text-center mb-3">{{ $message }}</div>@enderror

        <div class="form-section-card">
            <h6 class="section-title">Data Diri</h6>
            <div class="mb-3">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="name" value="{{ $user->name }}" readonly disabled>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" value="{{ $user->email }}" readonly disabled>
            </div>
        </div>

        <div class="form-section-card">
            <h6 class="section-title">Alamat Tinggal</h6>
            <div class="row">
                <div class="col-6">
                    <div class="mb-3">
                        <label for="rw_id" class="form-label">Pilih RW</label>
                        <select name="rw_id" id="rw_id" class="form-select" required>
                            <option value="" disabled {{ !$user->resident->rw_id ? 'selected' : '' }}>Pilih RW</option>
                            @foreach($rws as $rw)
                                <option value="{{ $rw->id }}" {{ old('rw_id', $user->resident->rw_id) == $rw->id ? 'selected' : '' }}>{{ $rw->number }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3">
                        <label for="rt_id" class="form-label">Pilih RT</label>
                        <select name="rt_id" id="rt_id" class="form-select" required {{ !$user->resident->rw_id ? 'disabled' : '' }}>
                            <option value="">Pilih RW Dulu</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Pin Lokasi Anda</label>
                <div id="map"></div>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Alamat Lengkap</label>
                <textarea name="address" id="address" class="form-control" rows="3" required>{{ old('address', $user->resident->address) }}</textarea>
                @error('address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>
        
        <div class="action-buttons-container">
            <a href="{{ route('profile') }}" class="btn btn-cancel">Batal</a>
            <button class="btn btn-save" type="submit" id="save-btn" disabled>Simpan Perubahan</button>
        </div>
        </form>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // (Skrip lain tetap sama)
            const form = document.getElementById('profile-form');
            const saveButton = document.getElementById('save-btn');
            let initialFormData = new FormData(form);

            const checkForChanges = () => {
                let currentFormData = new FormData(form);
                let hasChanged = false;
                if (document.getElementById('avatar-input').files.length > 0) {
                    hasChanged = true;
                } else {
                    for (let [key, value] of initialFormData.entries()) {
                        if (currentFormData.get(key) !== value) {
                            hasChanged = true;
                            break;
                        }
                    }
                }
                saveButton.disabled = !hasChanged;
            };

            form.addEventListener('input', checkForChanges);
            form.addEventListener('change', checkForChanges);

            const avatarInput = document.getElementById('avatar-input');
            const avatarPreview = document.getElementById('avatar-preview');
            avatarInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) { avatarPreview.src = e.target.result; }
                    reader.readAsDataURL(file);
                }
            });

            const rwSelect = document.getElementById('rw_id');
            const rtSelect = document.getElementById('rt_id');
            const addressInput = document.getElementById('address');
            const activeRtId = "{{ old('rt_id', $user->resident->rt_id) }}";

            const defaultLocation = [{{ old('latitude', $user->resident->latitude ?? -6.3816) }}, {{ old('longitude', $user->resident->longitude ?? 106.7420) }}];
            const map = L.map('map').setView(defaultLocation, 15);
            let marker = L.marker(defaultLocation, { draggable: true }).addTo(map);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

            const updateAddress = (lat, lng) => {
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.display_name) {
                            addressInput.value = data.display_name;
                            checkForChanges();
                        }
                    });
            };

            marker.on('dragend', function(e) {
                const latlng = e.target.getLatLng();
                updateAddress(latlng.lat, latlng.lng);
            });
            
            const fetchRts = (rwId, selectedRtId = null) => {
                rtSelect.disabled = true;
                rtSelect.innerHTML = '<option value="">Memuat...</option>';
                if (!rwId) {
                    rtSelect.innerHTML = '<option value="">Pilih RW Dulu</option>';
                    return;
                }
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
            };

            rwSelect.addEventListener('change', function() { fetchRts(this.value); });
            if (rwSelect.value) { fetchRts(rwSelect.value, activeRtId); }
        });
    </script>
@endsection