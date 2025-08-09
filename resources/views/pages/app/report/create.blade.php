@extends('layouts.no-nav')

@section('title', 'Buat Laporan Baru')

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

    <form action="{{ route('report.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">

        <div class="mb-3">
            <label for="image" class="form-label fw-bold">Bukti Laporan</label>
            {{-- Input file yang tersembunyi --}}
            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" style="display: none">
            
            {{-- Container untuk pratinjau dan placeholder --}}
            <div id="image-preview-container">
                {{-- Placeholder yang tampil secara default --}}
                <div id="image-placeholder" class="image-placeholder-box">
                    <i class="fa-solid fa-image fa-2x text-secondary"></i>
                    <p class="text-secondary small mt-2">Gambar pratinjau akan tampil di sini</p>
                </div>
                {{-- Pratinjau gambar yang awalnya tersembunyi --}}
                <img alt="Pratinjau Laporan" id="image-preview" class="img-fluid rounded-3 mb-3 border" style="display: none; width: 100%; height: 200px; object-fit: cover;">
            </div>
            
            @error('image')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="title" class="form-label fw-bold">Judul Laporan</label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}">
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="report_category_id" class="form-label fw-bold">Kategori Laporan</label>
            <select name="report_category_id" class="form-select @error('report_category_id') is-invalid @enderror">
                <option selected disabled>Pilih Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @if (old('report_category_id') == $category->id) selected @endif> {{ $category->name }}</option>
                @endforeach
            </select>
            @error('report_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label fw-bold">Ceritakan Laporan Kamu</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="map" class="form-label fw-bold">Lokasi Laporan</label>
            <div id="map" class="rounded-3"></div>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label fw-bold">Alamat Lengkap</label>
            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="5">{{ old('address') }}</textarea>
            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-grid mt-4">
            <button class="btn btn-primary py-2" type="submit" color="primary">
                Laporkan
            </button>
        </div>
    </form>
@endsection

@section('scripts')
    {{-- Script lama untuk Leaflet.js tidak perlu diubah, jadi kita sisipkan di sini --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('assets/app/js/report.js') }}"></script>
    
    {{-- Skrip baru untuk memperbaiki pratinjau gambar --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageBase64 = localStorage.getItem('image');
            const imagePreview = document.getElementById('image-preview');
            const imagePlaceholder = document.getElementById('image-placeholder');
            const imageInput = document.getElementById('image');

            if (imageBase64) {
                // Fungsi untuk mengubah base64 menjadi file
                function base64ToBlob(base64, mime) {
                    const byteString = atob(base64.split(',')[1]);
                    const ab = new ArrayBuffer(byteString.length);
                    const ia = new Uint8Array(ab);
                    for (let i = 0; i < byteString.length; i++) {
                        ia[i] = byteString.charCodeAt(i);
                    }
                    return new Blob([ab], { type: mime });
                }

                // Buat file dari base64
                const blob = base64ToBlob(imageBase64, 'image/jpeg');
                const file = new File([blob], 'captured_image.jpg', { type: 'image/jpeg' });

                // Masukkan file ke dalam input file
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                imageInput.files = dataTransfer.files;

                // Tampilkan pratinjau gambar
                imagePreview.src = URL.createObjectURL(file);
                imagePreview.style.display = 'block';
                imagePlaceholder.style.display = 'none';

                // Hapus gambar dari localStorage agar tidak digunakan lagi
                // localStorage.removeItem('image');
            }
        });
    </script>
@endsection