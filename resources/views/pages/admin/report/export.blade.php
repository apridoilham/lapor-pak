@extends('layouts.admin')

@section('title', 'Ekspor Laporan')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Ekspor Laporan</h1>

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
                    <div class="col-md-6">
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
                    <div class="col-md-6">
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
                </div>
                
                @role('super-admin')
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="rw_id">Wilayah RW</label>
                            <select name="rw_id" id="rw_id" class="form-control">
                                <option value="">Semua RW</option>
                                @foreach($rws as $rw)
                                    <option value="{{ $rw->id }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>
                                        RW {{ $rw->number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                     <div class="col-md-4">
                        <div class="form-group">
                            <label for="rt_id">Wilayah RT</label>
                            <select name="rt_id" id="rt_id" class="form-control" disabled>
                                <option value="">Pilih RW terlebih dahulu</option>
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
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                @else
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="rt_id">Wilayah RT</label>
                            <select name="rt_id" id="rt_id" class="form-control">
                                <option value="">Semua RT</option>
                                @foreach($rts as $rt)
                                    <option value="{{ $rt->id }}" {{ old('rt_id') == $rt->id ? 'selected' : '' }}>
                                        RT {{ $rt->number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                     <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status</label>
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
                </div>
                @endrole
                
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
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const exportButton = document.getElementById('export-btn');
            const startDateInput = document.getElementById('start_date');

            function toggleExportButtonState() {
                exportButton.disabled = !startDateInput.value;
            }

            startDateInput.addEventListener('input', toggleExportButtonState);
        });
    </script>

    @role('super-admin')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
    @endrole
@endsection