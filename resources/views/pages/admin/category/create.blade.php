@extends('layouts.admin')

@section('title', 'Tambah Data Kategori')

@section('content')
    <a href="{{ route('admin.report-category.index') }}" class="btn btn-danger mb-3">Kembali</a>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Data Kategori Laporan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.report-category.store')}}" method="POST" enctype="multipart/form-data" id="create-category-form">
                @csrf
                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="image">Gambar / Ikon</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" required>
                    @error('image')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary" id="submit-btn" disabled>Tambah Kategori Laporan</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('create-category-form');
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
    });
</script>
@endsection