@extends('layouts.admin')
@section('title', 'Tambah Data Masyarakat')
@section('content')
    <a href="{{ route('admin.resident.index') }}" class="btn btn-danger mb-3">Kembali</a>
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Data Masyarakat</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.resident.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group"><label for="name">Nama</label><input type="text" class="form-control" name="name" value="{{ old('name') }}" required></div>
                <div class="form-group"><label for="email">Email</label><input type="email" class="form-control" name="email" value="{{ old('email') }}" required></div>
                <div class="form-group"><label for="avatar">Foto Profil</label><input type="file" class="form-control" name="avatar" required></div>
                <div class="row">
                    <div class="col-md-6"><div class="form-group"><label for="rw_id">RW</label><select name="rw_id" id="rw_id" class="form-control" required><option value="" disabled selected>Pilih RW</option>@foreach($rws as $rw)<option value="{{ $rw->id }}">{{ $rw->number }}</option>@endforeach</select></div></div>
                    <div class="col-md-6"><div class="form-group"><label for="rt_id">RT</label><select name="rt_id" id="rt_id" class="form-control" required disabled><option value="" disabled selected>Pilih RW Dulu</option></select></div></div>
                </div>
                <div class="form-group"><label for="address">Alamat Lengkap</label><textarea name="address" class="form-control" rows="3" required>{{ old('address') }}</textarea></div>
                <hr>
                <div class="form-group"><label for="password">Password</label><input type="password" class="form-control" name="password" required autocomplete="new-password"></div>
                <div class="form-group"><label for="password_confirmation">Konfirmasi Password</label><input type="password" class="form-control" name="password_confirmation" required autocomplete="new-password"></div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rwSelect = document.getElementById('rw_id');
            const rtSelect = document.getElementById('rt_id');
            rwSelect.addEventListener('change', function() {
                const rwId = this.value;
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
                                rtSelect.appendChild(option);
                            });
                            rtSelect.disabled = false;
                        });
                }
            });
        });
    </script>
@endsection