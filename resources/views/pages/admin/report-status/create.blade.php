@extends('layouts.admin')

@section('title', 'Tambah Progress Laporan')

@push('styles')
<style>
    .report-summary-card img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: .35rem;
    }
    .file-input-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
        width: 100%;
        height: 150px;
        background-color: #f8f9fc;
        border: 2px dashed #e3e6f0;
        border-radius: .35rem;
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
    }
    #image-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
        position: absolute;
        top: 0;
        left: 0;
        border-radius: .35rem;
    }
</style>
@endpush

@section('content')
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.report.show', $report->id) }}" class="btn btn-outline-primary btn-circle mr-3" title="Kembali">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 text-gray-900 font-weight-bold">Tambah Progress Laporan</h1>
            <p class="mb-0 text-muted">Untuk Laporan Kode: <strong>{{ $report->code }}</strong></p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt mr-2"></i> Ringkasan Laporan
                    </h6>
                </div>
                <div class="card-body report-summary-card">
                    <img src="{{ asset('storage/' . $report->image) }}" alt="Foto Laporan" class="mb-3">
                    <h5 class="font-weight-bold">{{ $report->title }}</h5>
                    <p class="small text-muted mb-2">
                        <i class="fas fa-user mr-2"></i>{{ $report->resident->user->name }}
                    </p>
                    <p class="small text-muted">
                        <i class="far fa-clock mr-2"></i>{{ $report->created_at->isoFormat('D MMMM YYYY, HH:mm') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-pencil-alt mr-2"></i> Form Tambah Progress
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.report-status.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="report_id" value="{{ $report->id }}">

                        <div class="form-group">
                            <label for="image" class="font-weight-bold">Bukti Progress (Opsional)</label>
                            <div class="file-input-wrapper">
                                <input type="file" name="image" id="image" class="@error('image') is-invalid @enderror" onchange="previewImage(event)">
                                <div id="image-placeholder">
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

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Simpan Progress
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function previewImage(event) {
        const reader = new FileReader();
        const imagePreview = document.getElementById('image-preview');
        const imagePlaceholder = document.getElementById('image-placeholder');

        reader.onload = function(){
            imagePreview.src = reader.result;
            imagePreview.style.display = 'block';
            imagePlaceholder.style.display = 'none';
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endpush