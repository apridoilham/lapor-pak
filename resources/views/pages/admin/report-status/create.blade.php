@extends('layouts.admin')

@section('title', 'Tambah Progress Laporan')

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
        <a href="{{ route('admin.report.show', $report->id) }}" class="btn btn-primary btn-circle mr-3" title="Kembali">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Tambah Progress Laporan</h1>
            <p class="mb-0 text-muted">Untuk Laporan Kode: <strong>{{ $report->code }}</strong></p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-body p-4">
                    <div class="report-summary-box mb-4">
                        <img src="{{ asset('storage/' . $report->image) }}" alt="Foto Laporan">
                        <div class="summary-details">
                            <h6>{{ $report->title }}</h6>
                            <p>Oleh: {{ $report->resident->user->name }}</p>
                        </div>
                    </div>

                    <hr class="my-4">
                    
                    <form action="{{ route('admin.report-status.store') }}" method="POST" enctype="multipart/form-data" id="progress-form">
                        @csrf
                        <input type="hidden" name="report_id" value="{{ $report->id }}">

                        <div class="form-group">
                            <label for="image" class="font-weight-bold">Bukti Progress (Opsional)</label>
                            <div class="file-input-wrapper">
                                <input type="file" name="image" id="image" class="@error('image') is-invalid @enderror">
                                <div id="image-placeholder" style="display: flex; flex-direction: column; align-items: center;">
                                    <i class="fas fa-camera fa-2x text-gray-400"></i>
                                    <p class="text-gray-500 mt-2">Klik untuk memilih gambar</p>
                                </div>
                                <img id="image-preview" style="display: none;" alt="Pratinjau Gambar"/>
                            </div>
                             @error('image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="status" class="font-weight-bold">Status Progress</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="" disabled selected>-- Pilih Status --</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}" @if(old('status') == $status->value) selected @endif>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Pilih status yang paling sesuai dengan perkembangan saat ini.</small>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="description" class="font-weight-bold">Catatan / Deskripsi Progress</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="5" placeholder="Contoh: Tim kebersihan sudah dihubungi dan akan segera menuju lokasi." required>{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="text-right">
                            <a href="{{ route('admin.report.show', $report->id) }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary" id="save-btn" disabled>
                                <i class="fas fa-save mr-2"></i>Simpan Progress
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
    document.addEventListener('DOMContentLoaded', function() {
        const saveButton = document.getElementById('save-btn');
        const statusSelect = document.getElementById('status');
        const descriptionTextarea = document.getElementById('description');
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('image-preview');
        const imagePlaceholder = document.getElementById('image-placeholder');

        function checkFormValidity() {
            const statusIsValid = statusSelect.value && statusSelect.value.trim() !== '';
            const descriptionIsValid = descriptionTextarea.value.trim() !== '';
            saveButton.disabled = !(statusIsValid && descriptionIsValid);
        }

        function previewImage(event) {
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e){
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    imagePlaceholder.style.display = 'none';
                }
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        statusSelect.addEventListener('change', checkFormValidity);
        descriptionTextarea.addEventListener('input', checkFormValidity);
        imageInput.addEventListener('change', previewImage);
    });
</script>
@endpush