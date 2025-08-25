@extends('layouts.no-nav')

@section('title', 'Pratinjau Foto')

@push('styles')
<style>
    :root {
        --primary-color: #10B981;
        --text-dark: #111827;
        --text-light: #6B7280;
        --bg-body: #F9FAFB;
        --border-color: #e5e7eb;
        --font-sans: 'Inter', sans-serif;
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
    .main-content { padding: 1.5rem; }
    .page-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem; }
    .page-header .back-button { font-size: 1.5rem; color: var(--text-dark); text-decoration: none; }
    .page-header h3 { font-weight: 800; font-size: 1.75rem; color: var(--text-dark); margin: 0; }
    
    .preview-card {
        background-color: #fff;
        padding: 1.5rem;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        text-align: center;
    }
    .preview-card img {
        width: 100%;
        height: auto; /* PERBAIKAN: Memastikan tinggi otomatis */
        object-fit: contain; /* PERBAIKAN: Memastikan gambar tidak terpotong */
        border-radius: 12px;
        border: 1px solid var(--border-color);
        margin-bottom: 1.5rem;
    }
    .action-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    .action-buttons .btn {
        padding: 0.9rem;
        border-radius: 16px;
        font-weight: 700;
        font-size: 1rem;
    }
</style>
@endpush

@section('content')
    <div class="page-header">
        <a href="{{ route('report.take') }}" class="back-button">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h3>Gunakan Foto Ini?</h3>
    </div>

    <div class="preview-card">
        <p class="text-secondary mb-3">Pratinjau Laporan</p>
        <img alt="Pratinjau Laporan" id="image-preview" class="img-fluid">

        <div class="action-buttons mt-4">
            <a href="{{ route('report.take') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-rotate-right me-2"></i>
                Ulangi
            </a>
            <a href="{{ route('report.create') }}" class="btn btn-success">
                <i class="fa-solid fa-check me-2"></i>
                Gunakan Foto
            </a>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const image = localStorage.getItem('image');
        const imagePreview = document.getElementById('image-preview');
        if (image) {
            imagePreview.src = image;
        } else {
            window.location.href = '{{ route('report.take') }}';
        }
    });
</script>
@endpush