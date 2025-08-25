@extends('layouts.admin')
@section('title', 'Manajemen Wilayah')

@push('styles')
<style>
    .table thead th {
        font-weight: 600;
        color: #5a5c69;
        background-color: #f8f9fc;
        border-bottom-width: 1px;
    }
    .table td, .table th {
        vertical-align: middle;
    }
    .table tbody tr:hover {
        background-color: #f8f9fc;
    }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Manajemen Wilayah RW & RT</h1>
            <p class="mb-0 text-muted">Atur data wilayah untuk pendaftaran warga dan admin.</p>
        </div>
        <button type="button" class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addRwModal">
            <i class="fas fa-plus fa-sm mr-2"></i>Tambah Wilayah RW Baru
        </button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Wilayah RW</h6>
        </div>
        <div class="card-body">
            @if($rws->isEmpty())
                <div class="text-center py-5">
                    <h5 class="mt-3 font-weight-bold">Belum Ada Data Wilayah</h5>
                    <p class="text-muted">Silakan tambahkan data RW pertama Anda untuk memulai.</p>
                    <button type="button" class="btn btn-primary mt-2" data-toggle="modal" data-target="#addRwModal">
                        <i class="fas fa-plus mr-1"></i> Tambah Data RW
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nomor RW</th>
                                <th>Jumlah RT</th>
                                <th>Total Warga Terdaftar</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rws as $rw)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.rtrw.show', $rw) }}" class="font-weight-bold text-primary text-decoration-none">
                                            RW {{ $rw->number }}
                                        </a>
                                    </td>
                                    <td>{{ $rw->rts->count() }} RT</td>
                                    <td>{{ $rw->residents_count }} Warga</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.rtrw.show', $rw) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                            <i class="fas fa-eye fa-sm mr-1"></i>Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="addRwModal" tabindex="-1" role="dialog" aria-labelledby="addRwModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title font-weight-bold" id="addRwModalLabel">Tambah Data RW Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.rtrw.store') }}" method="POST" id="add-rtrw-form">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="number_modal" class="font-weight-bold small">Nomor RW Baru</label>
                            <input type="text" name="number" id="rw_number_input_modal" class="form-control @error('number', 'store') is-invalid @enderror" placeholder="Contoh: 5" required maxlength="2" value="{{ old('number') }}">
                            <div class="invalid-feedback" id="rw-error-modal">Nomor RW ini sudah ada.</div>
                            @error('number', 'store')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="rt_count_modal" class="font-weight-bold small">Jumlah RT di Dalamnya</label>
                            <input type="number" name="rt_count" id="rt_count_modal" class="form-control @error('rt_count', 'store') is-invalid @enderror" placeholder="Contoh: 10" required min="1" max="99" value="{{ old('rt_count') }}">
                            <small class="form-text text-muted">Jumlah RT minimal 1 dan maksimal 99.</small>
                            @error('rt_count', 'store')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="tambah-btn-modal" disabled>
                            <i class="fa fa-plus-circle"></i> Tambah RW & RT
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rwNumberInputModal = document.getElementById('rw_number_input_modal');
        const rtCountInput = document.getElementById('rt_count_modal');
        const tambahButton = document.getElementById('tambah-btn-modal');
        const rwError = document.getElementById('rw-error-modal');
        let debounceTimer;

        function checkModalFormValidity() {
            const isRwNumberValid = rwNumberInputModal.value.trim() !== '' && parseInt(rwNumberInputModal.value, 10) > 0;
            const isRtCountValid = rtCountInput.value.trim() !== '' && parseInt(rtCountInput.value, 10) > 0;
            const isRwNotTaken = !rwNumberInputModal.classList.contains('is-invalid');
            tambahButton.disabled = !(isRwNumberValid && isRtCountValid && isRwNotTaken);
        }
        
        rwNumberInputModal.addEventListener('input', function() {
            rwNumberInputModal.classList.remove('is-invalid');
            rwError.style.display = 'none';
            
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const number = this.value.trim();
                if (number.length > 0) {
                    fetch('/api/check-rw', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        body: JSON.stringify({ number: number })
                    }).then(res => res.json()).then(data => {
                        if (data.is_taken) {
                            rwNumberInputModal.classList.add('is-invalid');
                            rwError.style.display = 'block';
                        }
                        checkModalFormValidity();
                    });
                } else {
                    checkModalFormValidity();
                }
            }, 500);
        });

        rwNumberInputModal.addEventListener('blur', function() {
            if (this.value) {
                this.value = this.value.padStart(2, '0');
            }
        });
        
        rtCountInput.addEventListener('input', checkModalFormValidity);

        if ("{{ $errors->store->any() }}") {
            $('#addRwModal').modal('show');
        }
        
        checkModalFormValidity();
    });
</script>
@endpush