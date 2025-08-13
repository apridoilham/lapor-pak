@extends('layouts.admin')

@section('title', 'Tambah Admin Baru')

@section('content')
    <a href="{{ route('admin.admin-user.index') }}" class="btn btn-danger mb-3">Kembali</a>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Admin RW</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.admin-user.store')}}" method="POST" id="create-admin-form">
                @csrf
                <div class="form-group">
                    <label for="name">Nama Calon Admin</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Masukkan nama lengkap calon admin">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="email">Email Google Calon Admin</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="contoh: admin.rw01@gmail.com">
                    <div class="invalid-feedback" id="email-error-message"></div>
                    @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="rw_id">Wilayah RW</label>
                    @if($rws->isEmpty())
                        <select class="form-control" disabled>
                            <option>Tidak ada data RW. Silakan buat terlebih dahulu.</option>
                        </select>
                        <small class="form-text text-muted">
                            <a href="{{ route('admin.rtrw.index') }}">Klik di sini untuk menambah data RT/RW baru.</a>
                        </small>
                    @else
                        <select name="rw_id" id="rw_id" class="form-control @error('rw_id') is-invalid @enderror" required>
                            <option value="" disabled selected>Pilih RW untuk admin ini</option>
                            @foreach ($rws as $rw)
                                <option value="{{ $rw->id }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>RW {{ $rw->number }}</option>
                            @endforeach
                        </select>
                        @error('rw_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @endif
                </div>
                <button type="submit" class="btn btn-primary" id="submit-btn">Simpan</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.getElementById('email');
        const emailErrorMessage = document.getElementById('email-error-message');
        const submitButton = document.getElementById('submit-btn');
        let debounceTimer;

        emailInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            
            const email = this.value;
            if (email.length < 5 || !email.includes('@')) {
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch('/api/check-admin-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email: email })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.is_taken) {
                        emailInput.classList.add('is-invalid');
                        emailErrorMessage.textContent = data.message;
                        emailErrorMessage.style.display = 'block';
                        submitButton.disabled = true;
                    } else {
                        emailInput.classList.remove('is-invalid');
                        emailErrorMessage.style.display = 'none';
                        submitButton.disabled = false;
                    }
                });
            }, 500);
        });
    });
</script>
@endsection