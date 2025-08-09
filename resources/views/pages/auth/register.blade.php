@extends('layouts.no-nav')

@section('title', 'Daftar Akun Baru')

@section('content')
    <div class="header-nav mb-4">
        <a href="{{ route('login') }}" class="text-decoration-none">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1>Daftar Pengguna Baru</h1>
    </div>
    
    <p class="text-muted mb-4">
        Silakan mengisi form di bawah ini untuk mendaftar sebagai warga.
    </p>

    <form action="{{ route('register.store') }}" method="POST" class="mt-4" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autocomplete="name">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="avatar" class="form-label">Foto Profil</label>
            <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" required>
            @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        
        <hr class="my-4">

        <div class="row">
            <div class="col-6">
                 <div class="mb-3">
                    <label for="rw_id" class="form-label">Pilih RW</label>
                    <select name="rw_id" id="rw_id" class="form-select @error('rw_id') is-invalid @enderror" required>
                        <option value="" disabled selected>Pilih RW Anda</option>
                        @foreach($rws as $rw)
                            <option value="{{ $rw->id }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>{{ $rw->number }}</option>
                        @endforeach
                    </select>
                    @error('rw_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-6">
                <div class="mb-3">
                    <label for="rt_id" class="form-label">Pilih RT</label>
                    <select name="rt_id" id="rt_id" class="form-select @error('rt_id') is-invalid @enderror" required disabled>
                        <option value="" disabled selected>Pilih RW terlebih dahulu</option>
                    </select>
                    @error('rt_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Alamat Lengkap (Nama Jalan, No. Rumah)</label>
            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3" required placeholder="Contoh: Jl. Merdeka No. 12">{{ old('address') }}</textarea>
            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        
        <hr class="my-4">

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
        </div>
        
        <div class="d-grid mt-4">
            <button class="btn btn-primary py-2" type="submit">Daftar</button>
        </div>
        
        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-decoration-none text-primary">Sudah punya akun? Masuk</a>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rwSelect = document.getElementById('rw_id');
            const rtSelect = document.getElementById('rt_id');
            // Menyimpan nilai RT lama jika ada error validasi
            const oldRtId = "{{ old('rt_id') }}";

            function fetchRts(rwId, selectedRtId = null) {
                rtSelect.disabled = true;
                rtSelect.innerHTML = '<option value="" disabled selected>Memuat RT...</option>';

                if (!rwId) return;

                // Panggil API yang sudah kita buat
                fetch(`/api/get-rts-by-rw/${rwId}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        rtSelect.innerHTML = '<option value="" disabled selected>Pilih RT</option>';
                        if (data.length > 0) {
                            data.forEach(rt => {
                                const option = document.createElement('option');
                                option.value = rt.id;
                                option.textContent = rt.number;
                                // Jika ada nilai RT lama (dari error validasi), pilih kembali
                                if (selectedRtId && rt.id == selectedRtId) {
                                    option.selected = true;
                                }
                                rtSelect.appendChild(option);
                            });
                            rtSelect.disabled = false; // Aktifkan kembali dropdown RT
                        } else {
                            rtSelect.innerHTML = '<option value="" disabled selected>Tidak ada RT di RW ini</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching RTs:', error);
                        rtSelect.innerHTML = '<option value="" disabled selected>Gagal memuat RT</option>';
                    });
            }

            rwSelect.addEventListener('change', function() {
                fetchRts(this.value);
            });
            
            // Jika ada nilai RW lama (karena error validasi),
            // picu event change untuk otomatis memuat RT yang berelasi
            if (rwSelect.value) {
                fetchRts(rwSelect.value, oldRtId);
            }
        });
    </script>
@endsection