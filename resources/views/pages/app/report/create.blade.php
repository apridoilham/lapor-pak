@extends('layouts.no-nav')

@section('title', 'Buat Laporan Baru')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    :root {
        --primary-color: #0ea5e9; /* Sky Blue */
        --text-dark: #1e2b3b;
        --text-light: #64748b;
        --bg-body: #f1f5f9;
        --bg-white: #FFFFFF;
        --border-color: #e2e8f0;
        --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        --font-sans: 'Inter', sans-serif;
    }
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    body {
        font-family: var(--font-sans);
        background-color: var(--bg-body);
    }
    .main-content {
        padding: 1.5rem;
        padding-bottom: 100px;
    }

    .header-nav {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: var(--text-dark);
    }
    .header-nav a {
        font-size: 1.5rem;
        color: var(--text-dark);
    }
    .header-nav h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
    }
    .text-description {
        color: var(--text-light);
    }

    form .mb-3 {
        padding: 1.25rem;
        background: var(--bg-white);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: var(--card-shadow);
    }
    .form-label {
        font-weight: 600 !important;
        color: var(--text-dark);
    }
    .form-control, .form-select {
        border-radius: 12px;
        background-color: var(--bg-body);
        border-color: var(--border-color);
        padding: 0.8rem 1rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
    }

    #image-preview-container {
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
    }
    .image-placeholder-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 200px;
        background-color: var(--bg-body);
        border: 2px dashed var(--border-color);
        border-radius: 12px;
    }
    
    #map {
        height: 250px;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        z-index: 1;
    }

    .d-grid .btn-primary {
        padding: 0.9rem !important;
        border-radius: 12px !important;
        font-weight: 700;
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .d-grid .btn-primary:disabled {
        background-color: #94a3b8;
        border-color: #94a3b8;
    }
</style>
@endpush

@section('content')
    <div class="header-nav mb-4">
        <a href="{{ route('report.take') }}" class="text-decoration-none">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1>Lengkapi Laporan Anda</h1>
    </div>

    <p class="text-description mb-4">
        Isi form di bawah ini dengan detail yang benar agar laporan Anda dapat segera kami proses.
    </p>

    <form action="{{ route('report.store') }}" method="POST" enctype="multipart/form-data" id="create-report-form">
        @csrf
        <input type="hidden" id="latitude" name="latitude" required>
        <input type="hidden" id="longitude" name="longitude" required>

        <div class="mb-3">
            <label for="image" class="form-label fw-bold">Bukti Laporan</label>
            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" style="display: none" required>
            <div id="image-preview-container">
                <div id="image-placeholder" class="image-placeholder-box">
                    <i class="fa-solid fa-image fa-2x text-secondary"></i>
                    <p class="text-secondary small mt-2">Gambar pratinjau akan tampil di sini</p>
                </div>
                <img alt="Pratinjau Laporan" id="image-preview" class="img-fluid rounded-3 mb-3 border" style="display: none; width: 100%; height: 200px; object-fit: cover;">
            </div>
            @error('image')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="title" class="form-label fw-bold">Judul Laporan</label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="report_category_id" class="form-label fw-bold">Kategori Laporan</label>
            <select name="report_category_id" class="form-select @error('report_category_id') is-invalid @enderror" required @if($categories->isEmpty()) disabled @endif>
                <option value="" selected disabled>Pilih Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @if (old('report_category_id') == $category->id) selected @endif> {{ $category->name }}</option>
                @endforeach
            </select>
            @if($categories->isEmpty())
                <small class="form-text text-danger mt-1">Saat ini belum ada kategori laporan yang tersedia. Harap hubungi admin.</small>
            @endif
            @error('report_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label fw-bold">Ceritakan Laporan Kamu</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label for="map" class="form-label fw-bold">Lokasi Laporan</label>
                <button type="button" class="btn btn-outline-primary btn-sm" id="detect-location-btn">
                    <i class="fa-solid fa-map-marker-alt"></i> Cek Lokasi Saya
                </button>
            </div>
            <div id="map" class="rounded-3"></div>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label fw-bold">Alamat Lengkap</label>
            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="5" required>{{ old('address') }}</textarea>
            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="visibility" class="form-label fw-bold">Tampilkan Laporan Kepada</label>
            <select name="visibility" id="visibility" class="form-select @error('visibility') is-invalid @enderror" required>
                {{-- PERUBAHAN 1: Menambahkan placeholder --}}
                <option value="" selected disabled>Pilih visibilitas laporan</option>
                @foreach(\App\Enums\ReportVisibilityEnum::cases() as $visibility)
                    <option value="{{ $visibility->value }}" {{ old('visibility') == $visibility->value ? 'selected' : '' }}>
                        {{ $visibility->label(Auth::user()) }}
                    </option>
                @endforeach
            </select>
            @error('visibility')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="d-grid mt-4">
            <button class="btn btn-primary py-2" type="submit" color="primary" id="report-btn" disabled>
                Laporkan
            </button>
        </div>
    </form>
@endsection

@section('scripts')
    {{-- PERUBAHAN 2: Menambahkan SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('create-report-form');
            
            // ... (Kode Javascript lain yang sudah ada tidak perlu diubah) ...
            
            // PERUBAHAN 3: Menambahkan event listener untuk alert konfirmasi
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Mencegah form dikirim secara langsung

                Swal.fire({
                    title: 'Konfirmasi Laporan',
                    html: "Anda dapat mengubah atau menghapus laporan ini **hanya sebelum** diproses oleh admin. Pastikan semua data sudah benar.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0ea5e9',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Lanjutkan & Laporkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Jika dikonfirmasi, kirim form
                    }
                });
            });

            const reportButton = document.getElementById('report-btn');
            const requiredInputs = form.querySelectorAll('[required]');

            function checkFormValidity() {
                let allFieldsFilled = true;
                requiredInputs.forEach(input => {
                    if (input.disabled) return;
                    if (input.type === 'file') {
                        if (input.files.length === 0) {
                            allFieldsFilled = false;
                        }
                    } else if (input.value.trim() === '') {
                        allFieldsFilled = false;
                    }
                });
                reportButton.disabled = !allFieldsFilled;
            }

            requiredInputs.forEach(input => {
                input.addEventListener('input', checkFormValidity);
                input.addEventListener('change', checkFormValidity);
            });

            const imageBase64 = localStorage.getItem('image');
            const imagePreview = document.getElementById('image-preview');
            const imagePlaceholder = document.getElementById('image-placeholder');
            const imageInput = document.getElementById('image');

            if (imageBase64) {
                function base64ToBlob(base64, mime) {
                    const byteString = atob(base64.split(',')[1]);
                    const ab = new ArrayBuffer(byteString.length);
                    const ia = new Uint8Array(ab);
                    for (let i = 0; i < byteString.length; i++) {
                        ia[i] = byteString.charCodeAt(i);
                    }
                    return new Blob([ab], { type: mime });
                }
                const blob = base64ToBlob(imageBase64, 'image/jpeg');
                const file = new File([blob], 'captured_image.jpg', { type: 'image/jpeg' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                imageInput.files = dataTransfer.files;
                imagePreview.src = URL.createObjectURL(file);
                imagePreview.style.display = 'block';
                if(imagePlaceholder) { imagePlaceholder.style.display = 'none'; }
                checkFormValidity();
            } else {
                if(imagePlaceholder) { imagePlaceholder.style.display = 'flex'; }
            }

            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');
            const addressInput = document.getElementById('address');
            const detectButton = document.getElementById('detect-location-btn');
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
                            checkFormValidity();
                        }
                    }).catch(error => {
                        console.error('Error fetching address:', error);
                        addressInput.value = 'Gagal mendapatkan alamat.';
                    });
            }

            function updateInputs(latlng) {
                latitudeInput.value = latlng.lat.toFixed(8);
                longitudeInput.value = latlng.lng.toFixed(8);
                updateAddress(latlng.lat, latlng.lng);
            }

            marker.on('dragend', function(e) { updateInputs(e.target.getLatLng()); });
            function detectLocation() {
                if ('geolocation' in navigator) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        const newLatLng = new L.LatLng(lat, lng);
                        map.setView(newLatLng, 17);
                        marker.setLatLng(newLatLng);
                        updateInputs(newLatLng);
                    }, function(error) {
                        alert('Gagal mendeteksi lokasi. Pastikan izin lokasi telah diberikan.');
                        console.error(error);
                    });
                } else {
                    alert('Geolocation tidak didukung oleh browser Anda.');
                }
            }
            detectButton.addEventListener('click', detectLocation);
            detectLocation();
        });
    </script>
@endsection