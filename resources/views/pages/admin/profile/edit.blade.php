@extends('layouts.admin')

@section('title', 'Ubah Profil')

@push('styles')
<style>
    .avatar-upload-container {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto 2rem;
    }
    .avatar-preview {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .avatar-edit-button {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 40px;
        height: 40px;
        background: #4e73df;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid var(--white);
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    .avatar-edit-button:hover {
        transform: scale(1.1);
    }
</style>
@endpush

@section('content')
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.profile.index') }}" class="btn btn-primary btn-circle mr-3" title="Kembali ke Profil">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 text-gray-900 font-weight-bold">Ubah Data Profil</h1>
            <p class="mb-0 text-muted">Perbarui informasi personal dan foto profil Anda.</p>
        </div>
    </div>

    <div class="card shadow border-0 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" id="profile-edit-form">
                @csrf
                @method('PUT')

                <div class="avatar-upload-container">
                    <img src="{{ Auth::user()->avatar ? (filter_var(Auth::user()->avatar, FILTER_VALIDATE_URL) ? Auth::user()->avatar : asset('storage/' . Auth::user()->avatar)) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=1a202c&color=fff&size=150' }}"
                         alt="avatar" class="avatar-preview" id="avatar-preview">
                    <label for="avatar-input" class="avatar-edit-button" title="Ubah Foto Profil">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" name="avatar" id="avatar-input" class="d-none" accept="image/*">
                </div>
                @error('avatar')<div class="invalid-feedback d-block text-center mb-3">{{ $message }}</div>@enderror

                <div class="form-group">
                    <label for="name" class="font-weight-bold">Nama Lengkap</label>
                    <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="email" class="font-weight-bold">Email</label>
                    <input type="email" class="form-control form-control-lg" id="email" value="{{ $user->email }}" disabled>
                    <small class="form-text text-muted">Email tidak dapat diubah.</small>
                </div>

                @if ($user->rw)
                <div class="form-group">
                    <label for="rw" class="font-weight-bold">Wilayah RW</label>
                    <input type="text" class="form-control form-control-lg" id="rw" value="RW {{ $user->rw->number }}" disabled>
                </div>
                @endif

                <hr class="my-4">

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.profile.index') }}" class="btn btn-light mr-2">Batal</a>
                    <button type="submit" class="btn btn-primary" id="update-btn" disabled>
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('profile-edit-form');
    const updateButton = document.getElementById('update-btn');
    const nameInput = document.getElementById('name');
    const avatarInput = document.getElementById('avatar-input');
    const avatarPreview = document.getElementById('avatar-preview');

    const initialName = nameInput.value;

    function checkForChanges() {
        const nameChanged = nameInput.value !== initialName;
        const avatarChanged = avatarInput.files.length > 0;
        updateButton.disabled = !nameChanged && !avatarChanged;
    }

    form.addEventListener('input', checkForChanges);

    avatarInput.addEventListener('change', function(event) {
        if (event.target.files && event.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarPreview.src = e.target.result;
            }
            reader.readAsDataURL(event.target.files[0]);
            checkForChanges();
        }
    });
});
</script>
@endpush