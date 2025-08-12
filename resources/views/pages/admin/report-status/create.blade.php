@extends('layouts.admin')

@section('title', 'Tambah Data Progress Laporan')

@section('content')
    <a href="{{ url()->previous() }}" class="btn btn-danger mb-3">Kembali</a>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Data Progress Laporan {{ $report->code }} </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.report-status.store') }}" method="POST" enctype="multipart/form-data" id="create-status-form">
                @csrf
                <input type="hidden" name="report_id" value="{{ $report->id }}">
                <div class="form-group">
                    <label for="image">Bukti Progress Laporan (Opsional)</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">

                    @error('image')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="status">Status Progress Laporan</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @if (old('status') == $status->value) selected @endif>
                                {{ $status->label() }}
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
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary" id="submit-btn" disabled>Tambah Progress Laporan</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('create-status-form');
        const submitButton = document.getElementById('submit-btn');
        const requiredInputs = form.querySelectorAll('[required]');

        function checkFormValidity() {
            let allFieldsFilled = true;
            requiredInputs.forEach(input => {
                if (input.value.trim() === '') {
                    allFieldsFilled = false;
                }
            });
            submitButton.disabled = !allFieldsFilled;
        }

        requiredInputs.forEach(input => {
            input.addEventListener('input', checkFormValidity);
            input.addEventListener('change', checkFormValidity);
        });
    });
</script>
@endsection