@extends('layouts.admin')

@section('title', 'Ekspor Laporan')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Ekspor Laporan Kustom</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.report.export.store') }}" method="POST" id="export-form">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_date">Dari Tanggal</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_date">Sampai Tanggal</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="resident_id">Pelapor</label>
                            <select name="resident_id" id="resident_id" class="form-control">
                                <option value="">Semua Pelapor</option>
                                @foreach ($residents as $resident)
                                    <option value="{{ $resident->id }}" {{ old('resident_id') == $resident->id ? 'selected' : '' }}>
                                        {{ $resident->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="report_category_id">Kategori</label>
                            <select name="report_category_id" id="report_category_id" class="form-control">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('report_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Semua Status</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}" {{ old('status') == $status->value ? 'selected' : '' }}>
                                        {{ $status->value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-success" id="export-btn" disabled>
                    <i class="fa fa-file-excel"></i>
                    <span id="export-btn-text">Ekspor ke Excel</span>
                </button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    @include('sweetalert::alert')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- ▼▼▼ KODE JAVASCRIPT LENGKAP YANG SUDAH DIPERBAIKI ▼▼▼ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('export-form');
            const exportButton = document.getElementById('export-btn');
            const buttonText = document.getElementById('export-btn-text');
            const startDateInput = document.getElementById('start_date');

            function toggleExportButtonState() {
                if (startDateInput.value) {
                    exportButton.disabled = false;
                } else {
                    exportButton.disabled = true;
                }
            }

            startDateInput.addEventListener('change', toggleExportButtonState);
            toggleExportButtonState();

            form.addEventListener('submit', function(event) {
                // Tampilkan status loading
                buttonText.textContent = 'Mempersiapkan file...';
                exportButton.disabled = true;

                // Trik: Gunakan setTimeout untuk memberi waktu pada browser memulai unduhan
                // sebelum menampilkan notifikasi dan mengaktifkan kembali tombolnya.
                setTimeout(function() {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'File Excel Anda telah diunduh.',
                        icon: 'success',
                        timer: 2000, // Notifikasi akan hilang setelah 2 detik
                        showConfirmButton: false
                    });

                    // Kembalikan tombol ke keadaan normal
                    buttonText.textContent = 'Ekspor ke Excel';
                    toggleExportButtonState();
                }, 1500); // Jeda 1.5 detik
            });
        });
    </script>
@endsection