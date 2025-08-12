@extends('layouts.admin')

@section('title', 'Edit Data Kategori')

@section('content')
    <a href="{{ route('admin.report-category.index') }}" class="btn btn-danger mb-3">Kembali</a>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Data</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.report-category.update', $category->id)}}" method="POST" enctype="multipart/form-data" id="edit-category-form">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                    <div class="invalid-feedback" id="name-error">Nama kategori ini sudah ada.</div>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Gambar / Ikon Lama</label>
                    <img src="{{ asset('storage/' . $category->image) }}" alt="image" width="100" class="d-block">
                </div>
                <div class="form-group">
                    <label for="image">Gambar / Ikon Baru (Opsional)</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                    @error('image')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary" id="update-btn" disabled>Simpan Perubahan</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('edit-category-form');
        const updateButton = document.getElementById('update-btn');
        const nameInput = document.getElementById('name');
        const imageInput = document.getElementById('image');
        const nameError = document.getElementById('name-error');
        
        const initialName = nameInput.value;

        function checkForChanges() {
            const nameChanged = nameInput.value !== initialName;
            const imageSelected = imageInput.files.length > 0;
            const isNameInvalid = nameInput.classList.contains('is-invalid');

            updateButton.disabled = (!nameChanged && !imageSelected) || isNameInvalid;
        }

        nameInput.addEventListener('input', checkForChanges);
        imageInput.addEventListener('change', checkForChanges);

        let debounceTimer;
        nameInput.addEventListener('input', function() {
            if (nameInput.value === initialName) {
                nameInput.classList.remove('is-invalid');
                nameError.classList.remove('d-block');
                checkForChanges();
                return;
            }

            clearTimeout(debounceTimer);
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
                        } else {
                            nameInput.classList.remove('is-invalid');
                            nameError.classList.remove('d-block');
                        }
                        checkForChanges();
                    });
                }
            }, 500);
        });
    });
</script>
@endsection