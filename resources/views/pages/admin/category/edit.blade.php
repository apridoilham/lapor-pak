@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content')
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.report-category.index') }}" class="btn btn-outline-primary btn-circle mr-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Edit Kategori</h1>
            <p class="mb-0 text-muted">Ubah nama kategori yang sudah ada.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center">
                    <i class="fas fa-edit fa-fw text-primary mr-2"></i>
                    <h6 class="m-0 font-weight-bold text-primary">Formulir Edit Kategori</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.report-category.update', $category->id)}}" method="POST" class="p-3">
                        @csrf
                        @method('PUT')
                        <div class="form-group row">
                            <label for="name" class="col-sm-3 col-form-label font-weight-bold">Nama Kategori</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.report-category.index') }}" class="btn btn-secondary mr-2">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
