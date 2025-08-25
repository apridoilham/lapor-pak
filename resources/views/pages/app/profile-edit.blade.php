@extends('layouts.app')

@section('title', 'Ubah Profil')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
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
    .page-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem; }
    .page-header .back-button { font-size: 1.5rem; color: var(--text-dark); text-decoration: none; }
    .page-header h3 { font-weight: 800; font-size: 1.75rem; color: var(--text-dark); margin: 0; }
    .avatar-upload-container { position: relative; width: 120px; height: 120px; margin: 0 auto 2rem; }
    .avatar-preview { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 4px solid var(--bg-white); box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
    .avatar-edit-button { position: absolute; bottom: 5px; right: 5px; width: 36px; height: 36px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid var(--white); cursor: pointer; transition: transform 0.2s ease; }
    .avatar-edit-button:hover { transform: scale(1.1); }
    .form-section-card { background-color: var(--bg-white); padding: 1.5rem; border-radius: 20px; margin-bottom: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    .form-section-card .section-title { font-weight: 600; font-size: 1.1rem; color: var(--text-dark); margin-bottom: 1.5rem; }
    .form-label { font-weight: 600; font-size: 0.9rem; color: var(--text-dark); margin-bottom: 0.5rem; }
    .form-control, .form-select { border-radius: 12px; border: 1px solid var(--border-color); background-color: #F9FAFB; padding: 0.8rem 1rem; transition: all 0.2s ease; width: 100%; }
    .form-control:focus, .form-select:focus { background-color: var(--bg-white); border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); outline: none; }
    .form-control:disabled { background-color: #e9ecef; }
    #map { height: 200px; border-radius: 12px; z-index: 1; }
    .location-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .btn-detect-location { border: 1px solid var(--border-color); font-size: 0.8rem; font-weight: 600; }
    .action-buttons-container { display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-top: 2rem; }
    .action-buttons-container .btn { width: 100%; padding: 0.9rem; border-radius: 16px; border: none; font-weight: 700; font-size: 1rem; transition: all 0.3s ease; }
    .action-buttons-container .btn-cancel { background-color: #e5e7eb; color: var(--text-light); text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
    .action-buttons-container .btn-save { background: var(--primary-gradient); color: white; box-shadow: 0 8px 20px -5px rgba(16, 185, 129, 0.5); }
    .action-buttons-container .btn-save:disabled { background: #d1d5db; box-shadow: none; cursor: not-allowed; }
</style>
@endpush

@section('content')
    @php
        $isProfileIncomplete = !$user->resident->rw_id
                                 || !$user->resident->rt_id
                                 || empty(trim($user->resident->address))
                                 || !$user->resident->phone;

        $originalDataForJs = [
            'name' => $user->name,
            'phone' => $user->resident->phone ?? '',
            'rw_id' => $user->resident->rw_id ?? '',
            'rt_id' => $user->resident->rt_id ?? '',
            'address' => $user->resident->address,
        ];
    @endphp

    <div class="page-header">
        <a href="{{ route('profile') }}" class="back-button"><i class="fa-solid fa-arrow-left"></i></a>
        <h3>Ubah Profil</h3>
    </div>

    @if($isProfileIncomplete && $rws->isNotEmpty())
        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation flex-shrink-0 me-2"></i>
            <div>
                Harap lengkapi seluruh data diri Anda untuk dapat menggunakan semua fitur aplikasi.
            </div>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profile-form">
        @csrf
        @method('PUT')

        <div class="avatar-upload-container">
            @php
                $avatarUrl = !empty(Auth::user()->avatar) ? Auth::user()->avatar : optional(Auth::user()->resident)->avatar;
                if ($avatarUrl && !filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
                    $avatarUrl = asset('storage/' . $avatarUrl);
                } elseif (empty($avatarUrl)) {
                    $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=10B981&color=fff&size=128';
                }
            @endphp
            <img src="{{ $avatarUrl }}" alt="avatar" class="avatar-preview" id="avatar-preview">
            <label for="avatar-input" class="avatar-edit-button"><i class="fa-solid fa-camera"></i></label>
            <input type="file" name="avatar" id="avatar-input" class="d-none" accept="image/*">
        </div>
        @error('avatar')<div class="invalid-feedback d-block text-center mb-3">{{ $message }}</div>@enderror

        <div class="form-section-card">
            <h6 class="section-title">Data Diri</h6>
            <div class="mb-3">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" value="{{ $user->email }}" readonly disabled>
                <small class="form-text text-muted">Email tidak dapat diubah.</small>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Nomor Telepon</label>
                <input 
                    type="tel" 
                    class="form-control @error('phone') is-invalid @enderror" 
                    id="phone" 
                    name="phone" 
                    value="{{ old('phone', $user->resident->phone) }}" 
                    placeholder="Contoh: 081234567890" 
                    required
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                >
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        @if($rws->isNotEmpty())
            <div class="form-section-card">
                <h6 class="section-title">Alamat Tinggal</h6>
                <div class="row">
                    <div class="col-6"><div class="mb-3"><label for="rw_id" class="form-label">Pilih RW</label><select name="rw_id" id="rw_id" class="form-select" required><option value="" disabled {{ !$user->resident->rw_id ? 'selected' : '' }}>Pilih</option>@foreach($rws as $rw)<option value="{{ $rw->id }}" {{ old('rw_id', $user->resident->rw_id) == $rw->id ? 'selected' : '' }}>{{ $rw->number }}</option>@endforeach</select></div></div>
                    <div class="col-6"><div class="mb-3"><label for="rt_id" class="form-label">Pilih RT</label><select name="rt_id" id="rt_id" class="form-select" required {{ !$user->resident->rw_id ? 'disabled' : '' }}><option value="">Pilih RW</option></select></div></div>
                </div>
                <div class="mb-3">
                    <div class="location-header">
                        <label class="form-label">Pin Lokasi Anda</label>
                        <button type="button" class="btn btn-sm btn-outline-primary btn-detect-location" id="detect-location-btn">
                            <i class="fa-solid fa-location-crosshairs me-1"></i> Deteksi Lokasi Saya
                        </button>
                    </div>
                    <div id="map"></div>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Alamat Lengkap</label>
                    <textarea name="address" id="address" class="form-control" rows="3" required placeholder="Contoh: Jl. Merdeka No. 10, RT 01/RW 01...">{{ old('address', $user->resident->address) }}</textarea>
                    @error('address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        @else
            <div class="form-section-card">
                <h6 class="section-title">Alamat Tinggal</h6>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fa-solid fa-circle-info flex-shrink-0 me-2"></i>
                    <div>
                        Saat ini data wilayah (RW & RT) belum tersedia. Harap hubungi Administrator untuk melengkapi data terlebih dahulu.
                    </div>
                </div>
            </div>
        @endif
        
        <div class="action-buttons-container">
            <a href="{{ route('profile') }}" class="btn btn-cancel">Batal</a>
            <button class="btn btn-save" type="submit" id="save-btn" disabled>
                @if($isProfileIncomplete)
                    Simpan Data Diri
                @else
                    Simpan Perubahan
                @endif
            </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('profile-form');
            const saveButton = document.getElementById('save-btn');
            const isProfileIncomplete = {{ $isProfileIncomplete ? 'true' : 'false' }};
            const originalData = @json($originalDataForJs);
            
            const normalize = (value) => (value || '').toString().trim();

            const checkFormForChanges = () => {
                let hasChanged = false;
                if (normalize(form.elements['name'].value) !== normalize(originalData.name)) hasChanged = true;
                if (normalize(form.elements['phone'].value) !== normalize(originalData.phone)) hasChanged = true;
                if (form.elements['rw_id'] && normalize(form.elements['rw_id'].value) !== normalize(originalData.rw_id)) hasChanged = true;
                if (form.elements['rt_id'] && normalize(form.elements['rt_id'].value) !== normalize(originalData.rt_id)) hasChanged = true;
                if (form.elements['address'] && normalize(form.elements['address'].value) !== normalize(originalData.address)) hasChanged = true;
                if (document.getElementById('avatar-input').files.length > 0) hasChanged = true;
                
                saveButton.disabled = !hasChanged;
            };

            const checkFormForCompletion = () => {
                let allFieldsFilled = true;
                const requiredInputs = form.querySelectorAll('[required]');
                requiredInputs.forEach(input => {
                    if (!input.value || input.value.trim() === '') {
                        allFieldsFilled = false;
                    }
                });
                saveButton.disabled = !allFieldsFilled;
            };

            const updateButtonState = () => {
                if (isProfileIncomplete) {
                    checkFormForCompletion();
                } else {
                    checkFormForChanges();
                }
            };

            form.addEventListener('input', updateButtonState);
            form.addEventListener('change', updateButtonState);

            const avatarInput = document.getElementById('avatar-input');
            const avatarPreview = document.getElementById('avatar-preview');
            avatarInput.addEventListener('change', (event) => {
                if (event.target.files && event.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        avatarPreview.src = e.target.result;
                    }
                    reader.readAsDataURL(event.target.files[0]);
                    updateButtonState();
                }
            });
            
            if (document.getElementById('map')) {
                const rwSelect = document.getElementById('rw_id');
                const rtSelect = document.getElementById('rt_id');
                const addressInput = document.getElementById('address');
                const detectButton = document.getElementById('detect-location-btn');
                const activeRtId = "{{ old('rt_id', $user->resident->rt_id) }}";
                
                const defaultLat = {{ optional($user->resident)->latitude ?? -6.3816 }};
                const defaultLng = {{ optional($user->resident)->longitude ?? 106.7420 }};
                
                const map = L.map('map').setView([defaultLat, defaultLng], 15);
                let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

                // FIX 1: Panggil map.invalidateSize() setelah jeda singkat untuk memastikan map tampil
                setTimeout(() => {
                    map.invalidateSize();
                }, 200);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

                const updateAddress = (lat, lng, zoom = 17) => {
                    map.setView([lat, lng], zoom);
                    marker.setLatLng([lat, lng]);
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.display_name) {
                                addressInput.value = data.display_name;
                                updateButtonState();
                            }
                        });
                };

                marker.on('dragend', e => updateAddress(e.target.getLatLng().lat, e.target.getLatLng().lng));
                
                detectButton.addEventListener('click', () => {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            updateAddress(position.coords.latitude, position.coords.longitude);
                            Swal.fire({ icon: 'success', title: 'Lokasi Ditemukan!', timer: 1500, showConfirmButton: false });
                        },
                        (error) => {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat mendeteksi lokasi. Pastikan izin lokasi sudah diberikan.' });
                        }
                    );
                });
                
                const fetchRts = (rwId, selectedRtId = null) => {
                    if (!rwId) { 
                        rtSelect.innerHTML = '<option value="">Pilih RW</option>'; 
                        rtSelect.disabled = true; 
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
                                    if (selectedRtId && rt.id == selectedRtId) option.selected = true;
                                    rtSelect.appendChild(option);
                                });
                                rtSelect.disabled = false;
                            } else {
                                rtSelect.innerHTML = '<option value="" disabled selected>Tidak ada RT</option>';
                            }
                            updateButtonState();
                        });
                };
                
                rwSelect.addEventListener('change', function() { fetchRts(this.value); });
                
                // FIX 2: Panggil fetchRts() saat halaman dimuat jika RW sudah terpilih
                if (rwSelect.value) {
                    fetchRts(rwSelect.value, activeRtId);
                } else {
                   updateButtonState();
                }
            } else {
                updateButtonState();
            }
        });
    </script>
@endpush