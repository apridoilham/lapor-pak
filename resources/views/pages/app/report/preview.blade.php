@extends('layouts.no-nav')

@section('title', 'Pratinjau Foto')

@section('content')
    <div class="header-nav mb-4">
        <a href="{{ route('report.take') }}" class="text-decoration-none">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1>Gunakan Foto Ini?</h1>
    </div>

    <div class="preview-container">
        <img alt="Pratinjau Laporan" id="image-preview" class="img-fluid rounded-3">

        <div class="d-flex justify-content-center gap-3 mt-4">
            <a href="{{ route('report.take') }}" class="btn btn-outline-secondary rounded-pill py-2 px-4">
                <i class="fa-solid fa-rotate-right me-2"></i>
                Ulangi
            </a>
            <a href="{{ route('report.create') }}" class="btn btn-primary rounded-pill py-2 px-4">
                <i class="fa-solid fa-check me-2"></i>
                Gunakan Foto
            </a>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const image = localStorage.getItem('image');
        const imagePreview = document.getElementById('image-preview');
        if (image) {
            imagePreview.src = image;
        } else {
            // Jika tidak ada gambar, kembali ke halaman ambil foto
            window.location.href = '{{ route('report.take') }}';
        }
    </script>
@endsection