@extends('layouts.admin')

@section('title', 'Edit Progress Laporan')

@push('styles')
<style>
    .report-summary-box {
        background-color: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: .5rem;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .report-summary-box img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: .35rem;
        flex-shrink: 0;
    }
    .report-summary-box .summary-details h6 {
        font-weight: 700;
        margin-bottom: .25rem;
    }
    .report-summary-box .summary-details p {
        font-size: 0.85rem;
        color: #858796;
        margin-bottom: 0;
    }
    
    .file-input-wrapper {
        position: relative;
        overflow: hidden;
        width: 100%;
        height: 180px;
        background-color: #f8f9fc;
        border: 2px dashed #e3e6f0;
        border-radius: .5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        cursor: pointer;
        transition: border-color 0.2s ease-in-out;
    }
    .file-input-wrapper:hover {
        border-color: #4e73df;
    }
    .file-input-wrapper input[type=file] {
        font-size: 100px;
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
    #image-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
        position: absolute;
        top: 0;
        left: 0;
        border-radius: .5rem;
    }
</style>
@endpush

@section('content')
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.report.show', $status->report->id) }}" class="btn btn-primary btn-circle mr-3" title="Kembali">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Edit Progress Laporan</h1>
            <p class="mb-0 text-muted">Untuk Laporan Kode: <strong>{{ $status->report->code }}</strong></p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-body p-4">
                    <div class="report-summary-box mb-4">
                        <img src="{{ asset('storage/' . $status->report->image) }}" alt="Foto Laporan">
                        <div class="summary-details">
                            <h6>{{ $status->report->title }}</h6>
                            <p>Oleh: {{ $status->report->resident->user->name }}</p>
                        </div>
                    </div>
                    
                    <hr class="my-4">

                    <form action="{{ route('admin.report-status.update', $status->id) }}" method="POST" enctype="multipart/form-data" id="progress-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="report_id" value="{{ $status->report_id }}">

                        <div class="form-group">
                            <label for="image" class="font-weight-bold">Bukti Progress (Opsional)</label>
                            <div class="file-input-wrapper">
                                <input type="file" name="image" id="image" class="@error('image') is-invalid @enderror" onchange="previewImage(event)">
                                <div id="image-placeholder" style="{{ $status->image ? 'display: none;' : 'display: flex; flex-direction: column; align-items: center;' }}">
                                    <i class="fas fa-camera fa-2x text-gray-400"></i>
                                    <p class="text-gray-500 mt-2">Klik untuk mengganti gambar</p>
                                </div>
                                <img id="image-preview" src="{{ $status->image ? asset('storage/' . $status->image) : '' }}" style="{{ $status->image ? 'display: block;' : 'display: none;' }}" alt="Pratinjau Gambar"/>
                            </div>
                             @error('image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="status" class="font-weight-bold">Status Progress</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                @foreach ($statuses as $enumStatus)
                                    <option value="{{ $enumStatus->value }}" @if (old('status', $status->status->value) == $enumStatus->value) selected @endif>
                                        {{ $enumStatus->label() }}
                                    </option>
                                @endforeach
                            </select>
                             @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="description" class="font-weight-bold">Catatan / Deskripsi Progress</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="5" required>{{ old('description', $status->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="text-right">
                            <a href="{{ route('admin.report.show', $status->report_id) }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary" id="save-btn">
                                <i class="fas fa-save mr-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function previewImage(event) {
        if (event.target.files && event.target.files[0]) {
            const reader = new FileReader();
            const imagePreview = document.getElementById('image-preview');
            const imagePlaceholder = document.getElementById('image-placeholder');

            reader.onload = function(e){
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                if (imagePlaceholder) {
                    imagePlaceholder.style.display = 'none';
                }
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const saveButton = document.getElementById('save-btn');
        const descriptionTextarea = document.getElementById('description');

        // Untuk halaman edit, kita buat tombol selalu aktif
        // karena pengguna mungkin hanya ingin mengubah gambar.
        // Jika ingin validasi yang lebih ketat, bisa ditambahkan di sini.
        saveButton.disabled = false; 

        // Jika Anda ingin tombol dinonaktifkan jika deskripsi dikosongkan:
        /*
        function checkEditFormValidity() {
            const descriptionIsValid = descriptionTextarea.value.trim() !== '';
            saveButton.disabled = !descriptionIsValid;
        }
        descriptionTextarea.addEventListener('input', checkEditFormValidity);
        checkEditFormValidity();
        */
    });
</script>
@endpush