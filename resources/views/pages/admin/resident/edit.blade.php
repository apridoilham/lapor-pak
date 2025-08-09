@extends('layouts.admin')
@section('title', 'Edit Data Masyarakat')
@section('content')
    <a href="{{ route('admin.resident.index') }}" class="btn btn-danger mb-3">Kembali</a>
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Data Masyarakat</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.resident.update', $resident->id) }}" method="POST" enctype="multipart/form-data" id="edit-resident-form">
                @csrf
                @method('PUT')
                <div class="form-group"><label for="name">Nama</label><input type="text" class="form-control" id="name" name="name" value="{{ old('name', $resident->user->name) }}" required></div>
                <div class="form-group"><label for="email">Email</label><input type="email" class="form-control" id="email" name="email" value="{{ old('email', $resident->user->email) }}" required></div>
                <div class="form-group">
                    <label for="avatar">Foto Profil (Kosongkan jika tidak diubah)</label>
                    <img src="{{ asset('storage/' . $resident->avatar) }}" width="100" class="d-block mb-2">
                    <input type="file" class="form-control" id="avatar" name="avatar">
                </div>
                <div class="row">
                    <div class="col-md-6"><div class="form-group"><label for="rw_id">RW</label><select name="rw_id" id="rw_id" class="form-control" required>@foreach($rws as $rw)<option value="{{ $rw->id }}" {{ old('rw_id', $resident->rw_id) == $rw->id ? 'selected' : '' }}>{{ $rw->number }}</option>@endforeach</select></div></div>
                    <div class="col-md-6"><div class="form-group"><label for="rt_id">RT</label><select name="rt_id" id="rt_id" class="form-control" required><option value="">Pilih RW Dulu</option></select></div></div>
                </div>
                <div class="form-group"><label for="address">Alamat Lengkap</label><textarea name="address" id="address" class="form-control" rows="3" required>{{ old('address', $resident->address) }}</textarea></div>
                <hr>
                <p class="text-muted">Kosongkan password jika tidak ingin meresetnya.</p>
                <div class="form-group"><label for="password">Password Baru</label><input type="password" class="form-control" id="password" name="password" autocomplete="new-password"></div>
                <div class="form-group"><label for="password_confirmation">Konfirmasi Password Baru</label><input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password"></div>
                <button type="submit" class="btn btn-primary" id="update-btn" disabled>Update</button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logika Tombol Aktif/Nonaktif
            const form = document.getElementById('edit-resident-form');
            const updateButton = document.getElementById('update-btn');
            const inputs = form.querySelectorAll('input, select, textarea');
            let initialValues = {};
            inputs.forEach(input => { initialValues[input.name] = input.value; });

            function checkForChanges() {
                let hasChanged = false;
                inputs.forEach(input => {
                    if (input.type === 'file') { if (input.files.length > 0) hasChanged = true; }
                    else if(input.type === 'password') { if (input.value.length > 0) hasChanged = true; }
                    else if (initialValues[input.name] !== input.value) { hasChanged = true; }
                });
                updateButton.disabled = !hasChanged;
            }
            inputs.forEach(input => {
                input.addEventListener('input', checkForChanges);
                if(input.tagName.toLowerCase() === 'select' || input.type === 'file') {
                    input.addEventListener('change', checkForChanges);
                }
            });

            // Logika Cascading Dropdown
            const rwSelect = document.getElementById('rw_id');
            const rtSelect = document.getElementById('rt_id');
            const activeRtId = "{{ $resident->rt_id }}";

            function fetchRts(rwId, selectedRtId = null) {
                if (!rwId) return;
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
                    });
            }
            rwSelect.addEventListener('change', function() { fetchRts(this.value); });
            if (rwSelect.value) { fetchRts(rwSelect.value, activeRtId); }
        });
    </script>
@endsection