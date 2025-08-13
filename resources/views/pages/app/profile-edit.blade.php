@extends('layouts.app')
@section('title', 'Edit Profil')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* ... (semua style dari jawaban sebelumnya tetap sama) ... */
    body {
        background-color: #f8f9fa;
    }
    .profile-header {
        background: linear-gradient(135deg, var(--primary), #2c5282);
        color: white;
        padding: 2rem 1.5rem 4rem;
        border-bottom-left-radius: 30px;
        border-bottom-right-radius: 30px;
        margin: -1rem -1rem 0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .profile-header .avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 4px solid white;
        object-fit: cover;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    .profile-header .profile-info h4 {
        margin: 0;
        font-weight: 700;
        font-size: 1.25rem;
    }
    .profile-header .profile-info p {
        margin: 0;
        opacity: 0.8;
        font-size: 0.9rem;
    }
    .stats-card {
        display: flex;
        justify-content: space-around;
        background-color: white;
        padding: 1.25rem 1rem;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-top: -50px;
        position: relative;
        z-index: 10;
    }
    .stats-card .stat-item h5 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
    }
    .stats-card .stat-item p {
        font-size: 0.8rem;
        color: var(--secondary-text);
        margin-bottom: 0;
    }
    .profile-menu {
        margin-top: 1.5rem;
        padding-bottom: 80px;
    }
    #map {
        height: 250px;
        border-radius: 8px;
        border: 1px solid #ddd;
        margin-top: 0.5rem;
    }
</style>
@endpush

@section('content')
    @include('sweetalert::alert')

    <div class="header-nav mb-4">
        <a href="{{ route('profile') }}" class="text-decoration-none">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1>Lengkapi Profil Anda</h1>
    </div>

    <form action="{{ route('profile.update') }}" method="POST" class="mt-4" enctype="multipart/form-data" id="profile-form">
        @csrf
        @method('PUT')

        <div class="avatar-upload-container mb-4">
            @php
                $avatarUrl = $user->resident->avatar;
                if ($avatarUrl && !Str::startsWith($avatarUrl, 'http')) {
                    $avatarUrl = asset('storage/' . $avatarUrl);
                } elseif (!$avatarUrl) {
                    $avatarUrl = asset('assets/app/images/default-avatar.png');
                }
            @endphp
            <img src="{{ $avatarUrl }}" alt="avatar" class="avatar-preview" id="avatar-preview">
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
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" readonly disabled>
        </div>
        
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" readonly disabled>
        </div>

        <hr class="my-4">

        <div class="row">
            <div class="col-6">
                <div class="mb-3">
                    <label for="rw_id" class="form-label">Pilih RW</label>
                    <select name="rw_id" id="rw_id" class="form-select @error('rw_id') is-invalid @enderror" required>
                        <option value="" disabled selected>Pilih RW Anda</option>
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
            <div class="d-flex justify-content-between align-items-center">
                <label for="address" class="form-label">Alamat Lengkap</label>
                <button type="button" class="btn btn-outline-primary btn-sm" id="detect-location-btn">
                    <i class="fa-solid fa-map-marker-alt"></i> Cek Lokasi Saya
                </button>
            </div>
            <div id="map"></div>
            <textarea name="address" id="address" class="form-control mt-2 @error('address') is-invalid @enderror" rows="3" required placeholder="Contoh: Jl. Merdeka No. 12">{{ old('address', $user->resident->address) }}</textarea>
            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        
        <div class="d-grid gap-3 mt-4">
            <button class="btn btn-primary py-2" type="submit" id="save-changes-btn" disabled>Simpan Perubahan</button>
            <a href="{{ route('home') }}" class="btn btn-link text-secondary text-decoration-none">Kembali ke Beranda</a>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const avatarInput = document.getElementById('avatar');
            const rwSelect = document.getElementById('rw_id');
            const rtSelect = document.getElementById('rt_id');
            const addressInput = document.getElementById('address');
            const saveButton = document.getElementById('save-changes-btn');
            const detectButton = document.getElementById('detect-location-btn');
            const activeRtId = "{{ old('rt_id', $user->resident->rt_id) }}";

            // Logic untuk avatar preview
            avatarInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) { document.getElementById('avatar-preview').src = e.target.result; }
                    reader.readAsDataURL(file);
                }
            });

            // Logic untuk dropdown RT/RW
            function fetchRts(rwId, selectedRtId = null) {
                rtSelect.disabled = true;
                rtSelect.innerHTML = '<option value="">Memuat RT...</option>';
                if (!rwId) {
                    rtSelect.innerHTML = '<option value="" disabled selected>Pilih RW terlebih dahulu</option>';
                    return;
                };

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
                        checkFormValidity();
                    });
            }

            rwSelect.addEventListener('change', function() { fetchRts(this.value); });
            if (rwSelect.value) { fetchRts(rwSelect.value, activeRtId); }
            
            // Logic untuk Map dan Lokasi
            const defaultLocation = [-6.3816, 106.7420];
            const map = L.map('map').setView(defaultLocation, 13);
            let marker = L.marker(defaultLocation, { draggable: true }).addTo(map);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
            }).addTo(map);

            function updateAddress(lat, lng) {
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.display_name) {
                            addressInput.value = data.display_name;
                        } else {
                            addressInput.value = 'Alamat tidak ditemukan.';
                        }
                        checkFormValidity();
                    }).catch(error => {
                        console.error('Error fetching address:', error);
                        addressInput.value = 'Gagal mendapatkan alamat.';
                        checkFormValidity();
                    });
            }
            
            function detectLocation() {
                if ('geolocation' in navigator) {
                    detectButton.disabled = true;
                    detectButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mencari...`;

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            const newLatLng = new L.LatLng(lat, lng);
                            
                            map.setView(newLatLng, 17);
                            marker.setLatLng(newLatLng);
                            updateAddress(lat, lng);
                            
                            detectButton.disabled = false;
                            detectButton.innerHTML = '<i class="fa-solid fa-map-marker-alt"></i> Cek Lokasi Saya';
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Lokasi Anda berhasil ditemukan.', timer: 2000, showConfirmButton: false });
                        },
                        (error) => {
                            let errorMessage;
                            switch(error.code) {
                                case error.PERMISSION_DENIED:
                                    errorMessage = 'Izin lokasi ditolak. Mohon aktifkan izin lokasi di pengaturan browser Anda.';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    errorMessage = 'Informasi lokasi tidak tersedia. Coba lagi atau pindahkan pin secara manual.';
                                    break;
                                case error.TIMEOUT:
                                    errorMessage = 'Waktu permintaan lokasi habis. Periksa koneksi internet Anda.';
                                    break;
                                default:
                                    errorMessage = 'Terjadi kesalahan tidak terduga saat mendeteksi lokasi.';
                                    break;
                            }
                            Swal.fire({ icon: 'error', title: 'Gagal', text: errorMessage });
                            detectButton.disabled = false;
                            detectButton.innerHTML = '<i class="fa-solid fa-map-marker-alt"></i> Cek Lokasi Saya';
                        },
                        { timeout: 10000, enableHighAccuracy: true }
                    );
                } else {
                    Swal.fire({ icon: 'error', title: 'Tidak Didukung', text: 'Fitur geolocation tidak didukung oleh browser Anda.' });
                }
            }

            detectButton.addEventListener('click', detectLocation);
            
            marker.on('dragend', function(e) {
                const latlng = e.target.getLatLng();
                updateAddress(latlng.lat, latlng.lng);
            });

            // Logic untuk mengaktifkan tombol simpan
            const fieldsToMonitor = [rwSelect, rtSelect, addressInput, avatarInput];

            function checkFormValidity() {
                const isRwSelected = rwSelect.value !== '';
                const isRtSelected = rtSelect.value !== '' && !rtSelect.disabled;
                const isAddressFilled = addressInput.value.trim() !== '' && addressInput.value.trim() !== 'Alamat belum diatur';

                saveButton.disabled = !(isRwSelected && isRtSelected && isAddressFilled);
            }
            
            fieldsToMonitor.forEach(element => {
                element.addEventListener('input', checkFormValidity);
                element.addEventListener('change', checkFormValidity);
            });
            
            checkFormValidity();

            // BARIS PEMANGGIL OTOMATIS TELAH DIHAPUS DARI SINI
        });
    </script>
@endsection