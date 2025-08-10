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

    <form action="{{ route('report.update', $report->id) }}" method="POST">
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
                        {{ $visibility->label() }}
                    </label>
                </div>
            @endforeach
            @error('visibility')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="d-grid mt-4">
            <button class="btn btn-primary py-2" type="submit">
                Simpan Perubahan
            </button>
        </div>
    </form>
@endsection