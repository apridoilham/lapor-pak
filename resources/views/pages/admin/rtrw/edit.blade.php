@extends('layouts.admin')
@section('title', 'Edit Data RT & RW')

@section('content')
    <div class="d-flex align-items-center mb-4">
         <a href="{{ route('admin.rtrw.index') }}" class="btn btn-outline-primary btn-circle mr-3" title="Kembali">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Edit Data RW {{ $rw->number }}</h1>
            <p class="mb-0 text-muted">Ubah nomor RW atau jumlah RT di dalamnya.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <form action="{{ route('admin.rtrw.update', $rw) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="number" class="font-weight-bold">Nomor RW</label>
                            <input type="text" name="number" id="rw_number_input" class="form-control @error('number') is-invalid @enderror" value="{{ old('number', $rw->number) }}" required maxlength="3" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            <div class="invalid-feedback" id="rw-error" style="display: none;">Nomor RW ini sudah ada.</div>
                            @error('number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="rt_count" class="font-weight-bold">Jumlah RT</label>
                            <input type="text" name="rt_count" class="form-control @error('rt_count') is-invalid @enderror" value="{{ old('rt_count', $rtCount) }}" required maxlength="2" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('rt_count')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.rtrw.index') }}" class="btn btn-light mr-2">Batal</a>
                            <button type="submit" class="btn btn-primary" id="update-btn">
                                <i class="fa fa-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rwInputPadding = document.getElementById('rw_number_input');
        rwInputPadding.addEventListener('blur', function() {
            let value = this.value;
            if (value && !isNaN(value)) {
                this.value = value.padStart(3, '0');
            }
        });

        const updateButton = document.getElementById('update-btn');
        const rwInput = document.getElementById('rw_number_input');
        const rwError = document.getElementById('rw-error');
        const initialRwNumber = '{{ $rw->number }}';
        const rwIdToIgnore = {{ $rw->id }};
        let debounceTimer;

        function checkFormValidity() {
            const isRwInvalid = rwInput.classList.contains('is-invalid');
            updateButton.disabled = isRwInvalid;
        }

        rwInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            rwInput.classList.remove('is-invalid');
            rwError.style.display = 'none';
            checkFormValidity();

            const currentRwNumber = this.value.padStart(3, '0');
            if (currentRwNumber === initialRwNumber) {
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
@endsection