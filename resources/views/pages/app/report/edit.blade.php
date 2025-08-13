@extends('layouts.no-nav')

@section('title', 'Edit Laporan')

@section('content')
    <div class="header-nav mb-4">
        <a href="{{ route('report.myreport') }}" class="text-decoration-none">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1>Edit Laporan</h1>
    </div>

    <p class="text-description mb-4">
        Anda hanya dapat mengubah detail teks laporan. Gambar dan lokasi tidak dapat diubah.
    </p>

    <form action="{{ route('report.update', $report->id) }}" method="POST" id="edit-report-form">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label fw-bold">Bukti Laporan (Tidak dapat diubah)</label>
            <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}" class="img-fluid rounded-3 border">
        </div>

        <div class="mb-3">
            <label for="title" class="form-label fw-bold">Judul Laporan</label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $report->title) }}">
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="report_category_id" class="form-label fw-bold">Kategori Laporan</label>
            <select name="report_category_id" class="form-select @error('report_category_id') is-invalid @enderror">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @if (old('report_category_id', $report->report_category_id) == $category->id) selected @endif> {{ $category->name }}</option>
                @endforeach
            </select>
            @error('report_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label fw-bold">Ceritakan Laporan Kamu</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $report->description) }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Tampilkan Laporan Kepada</label>
            @foreach(\App\Enums\ReportVisibilityEnum::cases() as $visibility)
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="visibility" id="visibility-{{ $visibility->value }}" value="{{ $visibility->value }}" {{ old('visibility', $report->visibility->value) == $visibility->value ? 'checked' : '' }}>
                    <label class="form-check-label" for="visibility-{{ $visibility->value }}">
                        {{-- PERUBAHAN DI SINI --}}
                        {{ $visibility->label(Auth::user()) }}
                    </label>
                </div>
            @endforeach
            @error('visibility')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="d-grid mt-4">
            <button class="btn btn-primary py-2" type="submit" id="save-btn" disabled>
                Simpan Perubahan
            </button>
        </div>
    </form>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('edit-report-form');
        const saveButton = document.getElementById('save-btn');
        const inputs = form.querySelectorAll('input, select, textarea');
        let initialFormState = {};

        const getRadioValue = (name) => {
            const selectedRadio = form.querySelector(`input[name="${name}"]:checked`);
            return selectedRadio ? selectedRadio.value : null;
        };

        inputs.forEach(input => {
            if (input.name === '_token' || input.name === '_method') return;
            if (input.type === 'radio') {
                if (!initialFormState.hasOwnProperty(input.name)) {
                    initialFormState[input.name] = getRadioValue(input.name);
                }
            } else {
                initialFormState[input.name] = input.value;
            }
        });

        function checkForChanges() {
            let hasChanged = false;
            for (const input of inputs) {
                if (input.name === '_token' || input.name === '_method') continue;

                let currentValue;
                if (input.type === 'radio') {
                    currentValue = getRadioValue(input.name);
                } else {
                    currentValue = input.value;
                }

                if (initialFormState[input.name] !== currentValue) {
                    hasChanged = true;
                    break;
                }
            }
            saveButton.disabled = !hasChanged;
        }

        inputs.forEach(input => {
            input.addEventListener('input', checkForChanges);
            input.addEventListener('change', checkForChanges);
        });
    });
</script>
@endsection