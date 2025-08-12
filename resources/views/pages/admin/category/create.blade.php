@extends('layouts.admin')

@section('title', 'Tambah Data Kategori')

@section('content')
    <a href="{{ route('admin.report-category.index') }}" class="btn btn-danger mb-3">Kembali</a>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Data</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.report-category.store')}}" method="POST" enctype="multipart/form-data" id="create-category-form">
                @csrf
                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    <div class="invalid-feedback" id="name-error">Nama kategori ini sudah ada.</div>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
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
                <button type="submit" class="btn btn-primary" id="submit-btn" disabled>Submit</button>
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
        const nameInput = document.getElementById('name');
        const nameError = document.getElementById('name-error');

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
            
            const isNameInvalid = nameInput.classList.contains('is-invalid');
            submitButton.disabled = !allFieldsFilled || isNameInvalid;
        }

        requiredInputs.forEach(input => {
            input.addEventListener('input', checkFormValidity);
            input.addEventListener('change', checkFormValidity);
        });

        let debounceTimer;
        nameInput.addEventListener('input', function() {
            checkFormValidity();
            clearTimeout(debounceTimer);

            nameInput.classList.remove('is-invalid');
            nameError.classList.remove('d-block');

            debounceTimer = setTimeout(function() {
                const name = nameInput.value;
                if (name.length > 2) {
                    fetch('/api/check-report-category', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ name: name })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.is_taken) {
                            nameInput.classList.add('is-invalid');
                            nameError.classList.add('d-block');
                        }
                        checkFormValidity();
                    });
                }
            }, 500);
        });
    });
</script>
@endsection