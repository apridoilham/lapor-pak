@extends('layouts.admin')

@section('title', 'Tambah Data Laporan')

@section('content')
    <a href="{{ route('admin.report.index') }}" class="btn btn-danger mb-3">Kembali</a>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Data Laporan Pelapor</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.report.store')}}" method="POST" enctype="multipart/form-data" id="create-report-form">
                @csrf
                <div class="form-group">
                    <label for="code">Kode</label>
                    <input type="text" class="form-control" id="code" name="code" value="AUTO" disabled>
                </div>

                <div class="form-group">
                    <label for="resident_id">Pelapor</label>
                    @if($residents->isEmpty())
                        <select class="form-control" disabled>
                            <option>Tidak ada data pelapor. Silakan buat terlebih dahulu.</option>
                        </select>
                        <small class="form-text text-muted">
                            <a href="{{ route('admin.resident.create') }}">Klik di sini untuk menambah data pelapor baru.</a>
                        </small>
                    @else
                        <select name="resident_id" class="form-control @error('resident_id') is-invalid @enderror" required>
                            <option value="" disabled selected>Pilih Pelapor</option>
                            @foreach ($residents as $resident)
                                <option value="{{ $resident->id }}" @if (old('resident_id') == $resident->id) selected @endif> {{ $resident->user->email }} - {{ $resident->user->name }} </option>
                            @endforeach
                        </select>
                        @error('resident_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    @endif
                </div>

                <div class="form-group">
                    <label for="report_category_id">Kategori Laporan</label>
                    @if($categories->isEmpty())
                        <select class="form-control" disabled>
                            <option>Tidak ada data kategori. Silakan buat terlebih dahulu.</option>
                        </select>
                        <small class="form-text text-muted">
                            <a href="{{ route('admin.report-category.create') }}">Klik di sini untuk menambah data kategori baru.</a>
                        </small>
                    @else
                        <select name="report_category_id" class="form-control @error('report_category_id') is-invalid @enderror" required>
                            <option value="" disabled selected>Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @if (old('report_category_id') == $category->id) selected @endif> {{ $category->name }} </option>
                            @endforeach
                        </select>
                        @error('report_category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    @endif
                </div>

                <div class="form-group">
                    <label for="title">Judul Laporan</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi Laporan</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="image">Bukti Laporan</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" required>
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <hr>

                <div class="form-group">
                    <label>Lokasi Laporan</label>
                    <button type="button" class="btn btn-info btn-sm d-block mb-2" id="detect-location-btn">
                        <i class="fa fa-map-marker-alt"></i> Deteksi Lokasi Saya
                    </button>
                    <div id="map" style="height: 300px; border-radius: 5px; border: 1px solid #ddd;"></div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="latitude">Latitude</label>
                            <input type="text" class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude" value="{{ old('latitude') }}" required readonly>
                            @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="longitude">Longitude</label>
                            <input type="text" class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude" value="{{ old('longitude') }}" required readonly>
                            @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Alamat Laporan</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <button type="submit" class="btn btn-primary" id="submit-btn" disabled>Tambah Laporan Pelapor</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('create-report-form');
        const submitButton = document.getElementById('submit-btn');
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
            submitButton.disabled = !allFieldsFilled;
        }

        requiredInputs.forEach(input => {
            input.addEventListener('input', checkFormValidity);
            input.addEventListener('change', checkFormValidity);
        });

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

        marker.on('dragend', function(e) {
            updateInputs(e.target.getLatLng());
        });
        
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
    });
</script>
@endsection