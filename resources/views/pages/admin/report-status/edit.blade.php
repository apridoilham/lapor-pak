@extends('layouts.admin')

@section('title', 'Edit Data Progress Laporan')

@section('content')
    <a href="{{ route('admin.report.show', $status->report->id) }}" class="btn btn-danger mb-3">Kembali</a>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Ubah Data Progress Laporan {{ $status->report->code }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.report-status.update', $status->id)}}" method="POST" enctype="multipart/form-data" id="edit-status-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="report_id" value="{{ $status->report_id }}">
                <div class="form-group">
                    <label for="image">Bukti Progress Laporan (Opsional)</label>
                    @if ($status->image)
                        <img src="{{ asset('storage/' . $status->image) }}" alt="image" width="200" class="d-block mb-2">
                    @endif
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">

                    @error('image')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="status">Status Progress Laporan</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                        @foreach ($statuses as $enumStatus)
                            <option value="{{ $enumStatus->value }}" @if (old('status', $status->status->value) == $enumStatus->value) selected @endif>
                                {{ $enumStatus->label() }}
                            </option>
                        @endforeach
                    </select>

                    @error('status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi Progress Laporan</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $status->description) }}</textarea>

                    @error('description')
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
        const form = document.getElementById('edit-status-form');
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