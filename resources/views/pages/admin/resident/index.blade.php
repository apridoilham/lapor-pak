@extends('layouts.admin')

@section('title', 'Data Pelapor')

@section('content')
    <a href="{{ route('admin.resident.create') }}" class="btn btn-primary mb-3">Tambah Pelapor</a>

    @role('super-admin')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Data Pelapor</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.resident.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label for="rw_id">Filter berdasarkan RW</label>
                            <select name="rw" id="rw_id" class="form-control">
                                <option value="">Semua RW</option>
                                @foreach ($rws as $rw)
                                    <option value="{{ $rw->id }}" {{ request('rw') == $rw->id ? 'selected' : '' }}>
                                        RW {{ $rw->number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                     <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label for="rt_id">Filter berdasarkan RT</label>
                            <select name="rt" id="rt_id" class="form-control" disabled>
                                <option value="">Pilih RW terlebih dahulu</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                     <div class="col-md-2">
                        <a href="{{ route('admin.resident.index') }}" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endrole
    
    @role('admin')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Data Pelapor</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.resident.index') }}" method="GET">
                <div class="row align-items-end">
                     <div class="col-md-8">
                        <div class="form-group mb-0">
                            <label for="rt_id">Filter berdasarkan RT</label>
                            <select name="rt" id="rt_id" class="form-control">
                                <option value="">Semua RT di RW Anda</option>
                                @foreach ($rts as $rt)
                                     <option value="{{ $rt->id }}" {{ request('rt') == $rt->id ? 'selected' : '' }}>
                                        RT {{ $rt->number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                     <div class="col-md-2">
                        <a href="{{ route('admin.resident.index') }}" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endrole

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pelapor</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Email</th>
                            <th>Nama</th>
                            <th>Avatar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($residents as $resident)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $resident->user->email }}</td>
                                <td>{{ $resident->user->name }}</td>
                                <td>
                                    <img src="{{ asset('storage/' . $resident->avatar) }}" alt="avatar" width="100">
                                </td>
                                <td>
                                    <a href="{{ route('admin.resident.edit', $resident->id) }}" class="btn btn-warning">Ubah</a>
                                    <a href="{{ route('admin.resident.show', $resident->id) }}" class="btn btn-info">Lihat</a>
                                    <form action="{{ route('admin.resident.destroy', $resident->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" data-title="Hapus Pelapor?" data-text="Anda yakin ingin menghapus pelapor bernama {{ $resident->user->name }}?">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@role('super-admin')
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rwSelect = document.getElementById('rw_id');
        const rtSelect = document.getElementById('rt_id');
        const currentRtId = "{{ request('rt') }}";

        function fetchRts(rwId, selectedRtId = null) {
            if (!rwId) {
                rtSelect.innerHTML = '<option value="">Pilih RW terlebih dahulu</option>';
                rtSelect.disabled = true;
                return;
            }

            fetch(`/api/get-rts-by-rw/${rwId}`)
                .then(response => response.json())
                .then(data => {
                    rtSelect.innerHTML = '<option value="">Semua RT</option>';
                    data.forEach(rt => {
                        const option = document.createElement('option');
                        option.value = rt.id;
                        option.textContent = `RT ${rt.number}`;
                        if (selectedRtId && rt.id == selectedRtId) {
                            option.selected = true;
                        }
                        rtSelect.appendChild(option);
                    });
                    rtSelect.disabled = false;
                });
        }

        rwSelect.addEventListener('change', function() {
            fetchRts(this.value);
        });

        if (rwSelect.value) {
            fetchRts(rwSelect.value, currentRtId);
        }
    });
</script>
@endsection
@endrole