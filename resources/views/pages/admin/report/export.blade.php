@extends('layouts.admin')

@section('title', 'Ekspor Laporan')

@push('styles')
<style>
    .form-control, .form-select {
        border-radius: .5rem;
        padding: .65rem 1rem;
        border: 1px solid #d1d3e2;
        height: auto;
    }
    .form-control:focus, .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }
    .form-label {
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: .5rem;
    }
    .btn-export {
        padding: .75rem 1.5rem;
        font-weight: 600;
        font-size: 1rem;
        border-radius: .5rem;
    }
    .filter-heading {
        font-size: 1rem;
        font-weight: 700;
        color: #3a3b45;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Ekspor Laporan</h1>
            <p class="mb-0 text-muted">Unduh data laporan dalam format Excel sesuai filter yang Anda pilih.</p>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-2"></i>Filter Laporan</h6>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.report.export.store') }}" method="POST" id="export-form">
                @csrf
                <h6 class="filter-heading">Filter Berdasarkan Tanggal</h6>
                <p class="text-muted small mt-n2 mb-3">Pilih rentang tanggal laporan yang ingin Anda ekspor. Minimal **"Dari Tanggal"** harus diisi.</p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">Dari Tanggal</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}">
                    </div>
                </div>

                <hr class="my-3">

                <h6 class="filter-heading">Filter Berdasarkan Detail Laporan</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="resident_id" class="form-label">Pelapor</label>
                        <select name="resident_id" id="resident_id" class="form-control">
                            <option value="">Semua Pelapor</option>
                            @foreach ($residents as $resident)
                                <option value="{{ $resident->id }}" {{ old('resident_id') == $resident->id ? 'selected' : '' }}>
                                    {{ $resident->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="report_category_id" class="form-label">Kategori</label>
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
                
                <hr class="my-3">

                <h6 class="filter-heading">Filter Berdasarkan Wilayah & Status</h6>
                @if (Auth::user()->hasRole('super-admin'))
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="rw_id" class="form-label">Wilayah RW</label>
                        <select name="rw_id" id="rw_id" class="form-control">
                            <option value="">Semua RW</option>
                            @foreach($rws as $rw)
                                <option value="{{ $rw->id }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>
                                    RW {{ $rw->number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="rt_id" class="form-label">Wilayah RT</label>
                        <select name="rt_id" id="rt_id" class="form-control" disabled>
                            <option value="">Pilih RW terlebih dahulu</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Status Laporan</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Semua Status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" {{ old('status') == $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @else
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="rt_id" class="form-label">Wilayah RT</label>
                        <select name="rt_id" id="rt_id" class="form-control">
                            <option value="">Semua RT di RW Anda</option>
                            @foreach($rts as $rt)
                                <option value="{{ $rt->id }}" {{ old('rt_id') == $rt->id ? 'selected' : '' }}>
                                    RT {{ $rt->number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status Laporan</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Semua Status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" {{ old('status') == $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                
                <div class="text-right mt-4">
                    <button type="submit" class="btn btn-success btn-export" id="export-btn" disabled>
                        <i class="fas fa-file-excel mr-2"></i>
                        <span id="export-btn-text">Ekspor ke Excel</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('sweetalert::alert')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('export-form');
            const exportButton = document.getElementById('export-btn');
            const startDateInput = document.getElementById('start_date');

            function toggleExportButtonState() {
                exportButton.disabled = startDateInput.value.trim() === '';
            }

            toggleExportButtonState();
            ['input', 'change'].forEach(event => {
                startDateInput.addEventListener(event, toggleExportButtonState);
            });

            form.addEventListener('submit', function(event) {
                event.preventDefault();

                exportButton.disabled = true;
                exportButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengekspor...`;

                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': formData.get('_token'),
                        'Accept': 'application/json, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async response => {
                    if (response.ok && response.headers.get('content-type').includes('spreadsheet')) {
                        const disposition = response.headers.get('content-disposition');
                        let filename = 'laporan.xlsx';
                        if (disposition && disposition.includes('attachment')) {
                            const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                            if (matches != null && matches[1]) { 
                                filename = matches[1].replace(/['"]/g, '');
                            }
                        }
                        const blob = await response.blob();
                        return { blob, filename, success: true };
                    } else {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Gagal memuat data. Periksa kembali filter Anda.');
                    }
                })
                .then(result => {
                    if(result && result.success) {
                        const { blob, filename } = result;
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = url;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        a.remove();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data laporan telah berhasil diekspor.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.message
                    });
                })
                .finally(() => {
                    exportButton.innerHTML = `<i class="fas fa-file-excel mr-2"></i> <span id="export-btn-text">Ekspor ke Excel</span>`;
                    toggleExportButtonState();
                });
            });

            @if (Auth::user()->hasRole('super-admin'))
            const rwSelect = document.getElementById('rw_id');
            const rtSelect = document.getElementById('rt_id');
            const currentRtId = "{{ old('rt_id') }}";

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
            @endif
        });
    </script>
@endpush