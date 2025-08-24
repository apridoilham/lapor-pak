@extends('layouts.no-nav')

@section('title', 'Edit Laporan')

@push('styles')
<style>
    :root {
        --primary-color: #10B981;
        --primary-gradient: linear-gradient(135deg, #10B981 0%, #34D399 100%);
        --text-dark: #111827;
        --text-light: #6B7280;
        --bg-body: #F9FAFB;
        --bg-white: #FFFFFF;
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
    .main-content { padding: 1.5rem; padding-bottom: 100px; }
    .page-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
    .page-header .back-button { font-size: 1.5rem; color: var(--text-dark); text-decoration: none; }
    .page-header h3 { font-weight: 800; font-size: 1.75rem; color: var(--text-dark); margin: 0; }
    .page-description { font-size: 0.95rem; color: var(--text-light); margin-bottom: 2rem; }
    .form-section-card { background-color: var(--bg-white); padding: 1.5rem; border-radius: 20px; margin-bottom: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    .section-title { font-weight: 600; font-size: 1.1rem; color: var(--text-dark); margin-bottom: 1.5rem; }
    .form-label { font-weight: 600; font-size: 0.9rem; color: var(--text-dark); margin-bottom: 0.5rem; }
    .form-control, .form-select { border-radius: 12px; border: 1px solid var(--border-color); background-color: #F9FAFB; padding: 0.8rem 1rem; transition: all 0.2s ease; width: 100%; }
    .form-control:focus, .form-select:focus { background-color: var(--bg-white); border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); outline: none; }
    .image-upload-container { position: relative; border-radius: 12px; overflow: hidden; }
    .image-preview { width: 100%; height: auto; border-radius: 12px; display: block; border: 1px solid var(--border-color); }
    .image-edit-button { position: absolute; bottom: 12px; right: 12px; width: 44px; height: 44px; background: rgba(30,30,30,0.7); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid var(--white); cursor: pointer; transition: transform 0.2s ease; backdrop-filter: blur(5px); }
    .image-edit-button:hover { transform: scale(1.1); }
    #map { height: 200px; border-radius: 12px; z-index: 1; }
    .action-buttons-container { display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-top: 2rem; }
    .action-buttons-container .btn { width: 100%; padding: 0.9rem; border-radius: 16px; border: none; font-weight: 700; font-size: 1rem; transition: all 0.3s ease; }
    .action-buttons-container .btn-cancel { background-color: #e5e7eb; color: var(--text-light); text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
    .action-buttons-container .btn-save { background: var(--primary-gradient); color: white; box-shadow: 0 8px 20px -5px rgba(16, 185, 129, 0.5); }
    .action-buttons-container .btn-save:disabled { background: #d1d5db; box-shadow: none; cursor: not-allowed; }
</style>
@endpush

@section('content')
    <div class="page-header">
        <a href="{{ route('report.myreport') }}" class="back-button"><i class="fa-solid fa-arrow-left"></i></a>
        <h3>Edit Laporan</h3>
    </div>

    <p class="page-description">
        Anda dapat mengubah detail teks dan mengganti gambar bukti laporan.
    </p>

    <form action="{{ route('report.update', $report->id) }}" method="POST" enctype="multipart/form-data" id="edit-report-form">
        @csrf
        @method('PUT')

        <div class="form-section-card">
            <h6 class="section-title">Bukti Laporan</h6>
            <div class="image-upload-container">
                <img src="{{ asset('storage/' . $report->image) }}" alt="Pratinjau Laporan" class="image-preview" id="image-preview">
                <label for="image-input" class="image-edit-button" title="Ganti Foto Bukti">
                    <i class="fa-solid fa-camera"></i>
                </label>
                <input type="file" name="image" id="image-input" class="d-none" accept="image/*">
            </div>
            @error('image')<div class="invalid-feedback d-block mt-2">{{ $message }}</div>@enderror
        </div>

        <div class="form-section-card">
            <h6 class="section-title">Detail Laporan</h6>
            <div class="mb-3">
                <label for="title" class="form-label">Judul Laporan</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $report->title) }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
    
            <div class="mb-3">
                <label for="report_category_id" class="form-label">Kategori Laporan</label>
                <select name="report_category_id" class="form-select @error('report_category_id') is-invalid @enderror" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @if (old('report_category_id', $report->report_category_id) == $category->id) selected @endif> {{ $category->name }}</option>
                    @endforeach
                </select>
                @error('report_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
    
            <div class="mb-0">
                <label for="description" class="form-label">Deskripsi Laporan</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description', $report->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-section-card">
            <h6 class="section-title">Lokasi Kejadian (Tidak dapat diubah)</h6>
            <div class="mb-3">
                <label for="address" class="form-label">Alamat Lengkap</label>
                <textarea class="form-control" id="address" rows="3" readonly disabled>{{ $report->address }}</textarea>
            </div>
            <div id="map"></div>
        </div>

        <div class="form-section-card">
            <h6 class="section-title">Pengaturan Lanjutan</h6>
            <div class="mb-0">
                <label for="visibility" class="form-label">Tampilkan Laporan Kepada</label>
                <select name="visibility" id="visibility" class="form-select @error('visibility') is-invalid @enderror" required>
                    @foreach(\App\Enums\ReportVisibilityEnum::cases() as $visibility)
                        <option value="{{ $visibility->value }}" {{ old('visibility', $report->visibility->value) == $visibility->value ? 'selected' : '' }}>
                            {{ $visibility->label(Auth::user()) }}
                        </option>
                    @endforeach
                </select>
                @error('visibility')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="action-buttons-container">
            <a href="{{ route('report.myreport') }}" class="btn btn-cancel">Batal</a>
            <button class="btn btn-save" type="submit" id="save-btn" disabled>
                Simpan Perubahan
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('edit-report-form');
        const saveButton = document.getElementById('save-btn');
        const inputs = form.querySelectorAll('input, select, textarea');
        let initialFormState = {};

        const imageInput = document.getElementById('image-input');
        const imagePreview = document.getElementById('image-preview');

        inputs.forEach(input => {
            if (input.name === '_token' || input.name === '_method') return;
            initialFormState[input.name] = input.value;
        });

        function checkForChanges() {
            let hasChanged = false;
            for (const input of inputs) {
                if (input.name === '_token' || input.name === '_method') continue;

                if (input.type === 'file') {
                    if (input.files.length > 0) {
                        hasChanged = true;
                        break;
                    }
                } else if (initialFormState[input.name] !== input.value) {
                    hasChanged = true;
                    break;
                }
            }
            saveButton.disabled = !hasChanged;
        }

        inputs.forEach(input => {
            input.addEventListener('input', checkForChanges);
            input.addEventListener('change', checkForChanges);
        });

        imageInput.addEventListener('change', (event) => {
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                }
                reader.readAsDataURL(event.target.files[0]);
                checkForChanges();
            }
        });
        
        if (document.getElementById('map')) {
            const lat = {{ $report->latitude }};
            const lng = {{ $report->longitude }};
            const map = L.map('map', { dragging: false, tap: false, scrollWheelZoom: false }).setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            L.marker([lat, lng]).addTo(map);
            setTimeout(() => map.invalidateSize(), 200);
        }
    });
</script>
@endpush