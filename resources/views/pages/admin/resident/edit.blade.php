@extends('layouts.admin')
@section('title', 'Ubah Data Pelapor')
@section('content')
    <a href="{{ route('admin.resident.index') }}" class="btn btn-danger mb-3">Kembali</a>
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Form Ubah Data Pelapor</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.resident.update', $resident->id) }}" method="POST" enctype="multipart/form-data" id="edit-resident-form">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" class="form-control bg-light" name="name" value="{{ $resident->user->name }}" readonly disabled>
                    <small class="form-text text-muted">Nama dan Email diambil dari akun Google dan tidak bisa diubah.</small>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control bg-light" name="email" value="{{ $resident->user->email }}" readonly disabled>
                </div>

                <div class="form-group">
                    <label>Foto Profil (Kosongkan jika tidak diubah)</label>
                    @php
                        $avatarUrl = $resident->avatar;
                        if ($avatarUrl && !Str::startsWith($avatarUrl, 'http')) {
                            $avatarUrl = asset('storage/' . $avatarUrl);
                        } elseif (!$avatarUrl) {
                            $avatarUrl = asset('assets/app/images/default-avatar.png');
                        }
                    @endphp
                    <img src="{{ $avatarUrl }}" alt="avatar" width="100" class="d-block mb-2">
                    <input type="file" class="form-control" name="avatar">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="rw_id">RW</label>
                            <select name="rw_id" id="rw_id" class="form-control" required>
                                <option value="" disabled>Pilih RW</option>
                                @foreach($rws as $rw)
                                    <option value="{{ $rw->id }}" {{ old('rw_id', $resident->rw_id) == $rw->id ? 'selected' : '' }}>{{ $rw->number }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="rt_id">RT</label>
                            <select name="rt_id" id="rt_id" class="form-control" required>
                                <option value="" disabled>Pilih RW Dulu</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Alamat Lengkap</label>
                    <textarea name="address" class="form-control" rows="3" required>{{ old('address', $resident->address) }}</textarea>
                </div>
                <hr>
                <p class="text-muted">Kosongkan jika tidak ingin mereset password pengguna.</p>
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input type="password" class="form-control" name="password" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-primary" id="update-btn">Simpan Perubahan</button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rwSelect = document.getElementById('rw_id');
            const rtSelect = document.getElementById('rt_id');
            const activeRtId = "{{ old('rt_id', $resident->rt_id) }}";

            function fetchRts(rwId, selectedRtId = null) {
                rtSelect.disabled = true;
                rtSelect.innerHTML = '<option value="">Memuat...</option>';
                if (rwId) {
                    fetch(`/api/get-rts-by-rw/${rwId}`)
                        .then(response => response.json())
                        .then(data => {
                            rtSelect.innerHTML = '<option value="" disabled selected>Pilih RT</option>';
                            data.forEach(rt => {
                                const option = document.createElement('option');
                                option.value = rt.id;
                                option.textContent = rt.number;
                                if (selectedRtId && rt.id == selectedRtId) {
                                    option.selected = true;
                                }
                                rtSelect.appendChild(option);
                            });
                            rtSelect.disabled = false;
                        });
                }
            }
            
            rwSelect.addEventListener('change', function() {
                fetchRts(this.value);
            });

            if (rwSelect.value) {
                fetchRts(rwSelect.value, activeRtId);
            }
        });
    </script>
@endsection