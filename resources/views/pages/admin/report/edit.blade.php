@extends('layouts.admin')

@section('title', 'Ubah Data Laporan')

@section('content')
    <a href="{{ route('admin.report.index') }}" class="btn btn-danger mb-3">Kembali</a>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Ubah Data Laporan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.report.update', $report->id)}}" method="POST" enctype="multipart/form-data" id="edit-report-form">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="code">Kode</label>
                    <input type="text" class="form-control" id="code" name="code" value="{{ $report->code }}" disabled>
                </div>
                <div class="form-group">
                    <label for="resident_id">Pelapor / Masyarakat</label>
                    <select name="resident_id" class="form-control @error('resident_id') is-invalid @enderror">
                        @foreach ($residents as $resident)
                            <option value="{{ $resident->id }}" @if (old('resident_id', $report->resident_id) == $resident->id) selected @endif> {{ $resident->user->email }} - {{ $resident->user->name }} </option>
                        @endforeach
                    </select>
                    @error('resident_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="report_category_id">Kategori Laporan</label>
                    <select name="report_category_id" class="form-control @error('report_category_id') is-invalid @enderror">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @if (old('report_category_id', $report->report_category_id) == $category->id) selected @endif> {{ $category->name }} </option>
                        @endforeach
                    </select>
                    @error('report_category_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="title">Judul Laporan</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $report->title) }}">
                    @error('title')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="description">Deskripsi Laporan</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $report->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Bukti Laporan Lama</label>
                    <img src="{{ asset('storage/' . $report->image) }}" alt="image" width="100" class="d-block">
                </div>
                <div class="form-group">
                    <label for="image">Bukti Laporan Baru (Opsional)</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                    @error('image')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="latitude">Latitude</label>
                    <input type="text" class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude" value="{{ old('latitude', $report->latitude) }}">
                    @error('latitude')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="longitude">Longitude</label>
                    <input type="text" class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude" value="{{ old('longitude', $report->longitude) }}">
                    @error('longitude')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="address">Alamat Laporan</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="5">{{ old('address', $report->address) }}</textarea>
                    @error('address')
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
        const form = document.getElementById('edit-report-form');
        const updateButton = document.getElementById('update-btn');
        const inputs = form.querySelectorAll('input, select, textarea');
        let initialFormState = {};

        inputs.forEach(input => {
            if (input.type === 'file' || input.name === '_token' || input.name === '_method') return;
            initialFormState[input.name] = input.value;
        });

        function checkForChanges() {
            let hasChanged = false;
            inputs.forEach(input => {
                if (input.type === 'file' && input.files.length > 0) {
                    hasChanged = true;
                } else if (initialFormState.hasOwnProperty(input.name) && initialFormState[input.name] !== input.value) {
                    hasChanged = true;
                }
            });
            updateButton.disabled = !hasChanged;
        }

        inputs.forEach(input => {
            input.addEventListener('input', checkForChanges);
            input.addEventListener('change', checkForChanges);
        });
    });
</script>
@endsection