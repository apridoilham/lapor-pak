@extends('layouts.admin')
@section('title', 'Edit Data RT & RW')

@push('styles')
<style>
    .input-group-text {
        width: 45px;
        justify-content: center;
    }
</style>
@endpush

@section('content')
    <div class="d-flex align-items-center mb-4">
         <a href="{{ route('admin.rtrw.show', $rw) }}" class="btn btn-outline-primary btn-circle mr-3" title="Kembali">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Edit Data RW {{ $rw->number }}</h1>
            <p class="mb-0 text-muted">Ubah nomor RW atau jumlah RT di dalamnya.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center">
                    <i class="fas fa-edit fa-fw text-primary mr-2"></i>
                    <h6 class="m-0 font-weight-bold text-primary">Formulir Edit Data</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.rtrw.update', $rw) }}" method="POST" class="p-3">
                        @csrf
                        @method('PUT')
                        <div class="form-group row">
                            <label for="number" class="col-sm-3 col-form-label font-weight-bold">Nomor RW</label>
                            <div class="col-sm-9">
                                <input type="text" name="number" id="rw_number_input" class="form-control form-control-lg @error('number') is-invalid @enderror" value="{{ old('number', $rw->number) }}" required maxlength="2" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <div class="invalid-feedback" id="rw-error" style="display: none;">Nomor RW ini sudah ada.</div>
                                @error('number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rt_count" class="col-sm-3 col-form-label font-weight-bold">Jumlah RT</label>
                            <div class="col-sm-9">
                                <input type="text" name="rt_count" id="rt_count_input" class="form-control form-control-lg @error('rt_count') is-invalid @enderror" value="{{ old('rt_count', $rtCount) }}" required maxlength="2" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <small class="form-text text-muted">Mengurangi jumlah RT hanya bisa jika RT yang akan dihapus tidak memiliki data warga.</small>
                                @error('rt_count')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.rtrw.show', $rw) }}" class="btn btn-secondary mr-2">Batal</a>
                            <button type="submit" class="btn btn-primary" id="update-btn" disabled>
                                <i class="fa fa-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rwInput = document.getElementById('rw_number_input');
        const rtCountInput = document.getElementById('rt_count_input');
        const updateButton = document.getElementById('update-btn');
        const rwError = document.getElementById('rw-error');
        
        const initialRwNumber = '{{ $rw->number }}';
        const initialRtCount = '{{ $rtCount }}'.padStart(2, '0');
        const rwIdToIgnore = {{ $rw->id }};
        let debounceTimer;

        function checkForChanges() {
            const currentRwValue = rwInput.value.padStart(2, '0');
            const currentRtCountValue = rtCountInput.value.padStart(2, '0');
            
            const rwChanged = currentRwValue !== initialRwNumber;
            const rtCountChanged = currentRtCountValue !== initialRtCount;

            const isRwEmptyOrZero = rwInput.value.trim() === '' || parseInt(rwInput.value, 10) === 0;
            const isRtCountEmptyOrZero = rtCountInput.value.trim() === '' || parseInt(rtCountInput.value, 10) === 0;

            updateButton.disabled = !(rwChanged || rtCountChanged) || isRwEmptyOrZero || isRtCountEmptyOrZero;
        }

        rwInput.addEventListener('blur', function() {
            if (this.value) {
                this.value = this.value.padStart(2, '0');
            }
        });

        rtCountInput.addEventListener('blur', function() {
            if (this.value) {
                this.value = this.value.padStart(2, '0');
            }
        });
        
        function checkFormValidity() {
            const isRwInvalid = rwInput.classList.contains('is-invalid');
            if (isRwInvalid) {
                updateButton.disabled = true;
            } else {
                checkForChanges();
            }
        }

        [rwInput, rtCountInput].forEach(input => {
            input.addEventListener('input', checkFormValidity);
        });

        rwInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            rwInput.classList.remove('is-invalid');
            rwError.style.display = 'none';
            checkFormValidity();

            const currentRwNumberPadded = this.value.padStart(2, '0');
            if (currentRwNumberPadded === initialRwNumber) {
                return;
            }

            debounceTimer = setTimeout(() => {
                const rwNumber = rwInput.value;
                if (rwNumber.length > 0) {
                    fetch('/api/check-rw', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            number: rwNumber,
                            ignore_rw_id: rwIdToIgnore
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.is_taken) {
                            rwInput.classList.add('is-invalid');
                            rwError.style.display = 'block';
                        }
                        checkFormValidity();
                    });
                }
            }, 500);
        });
    });
</script>
@endpush