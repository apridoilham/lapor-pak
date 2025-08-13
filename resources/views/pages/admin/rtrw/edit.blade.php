@extends('layouts.admin')
@section('title', 'Edit Data RT & RW')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Edit Data RW & RT</h1>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Edit RW {{ $rw->number }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.rtrw.update', $rw->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="number">Nomor RW</label>
                            <input type="text" name="number" id="rw_number_input" class="form-control @error('number') is-invalid @enderror" value="{{ old('number', $rw->number) }}" required maxlength="3" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="rt_count">Jumlah RT</label>
                            <input type="text" name="rt_count" class="form-control @error('rt_count') is-invalid @enderror" value="{{ old('rt_count', $rtCount) }}" required maxlength="2" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('rt_count')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.rtrw.index') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- TAMBAHKAN BLOK SCRIPT INI --}}
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rwInput = document.getElementById('rw_number_input');

        rwInput.addEventListener('blur', function() {
            let value = this.value;
            if (value && !isNaN(value)) {
                this.value = value.padStart(3, '0');
            }
        });
    });
</script>
@endsection