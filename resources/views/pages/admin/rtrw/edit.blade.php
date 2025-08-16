@extends('layouts.admin')
@section('title', 'Edit Data RT & RW')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Edit Data RW & RT</h1>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Edit RW {{ $rw->number }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.rtrw.update', $rw->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="number">Nomor RW</label>
                            <input type="text" name="number" id="rw_number_input" class="form-control @error('number') is-invalid @enderror" value="{{ old('number', $rw->number) }}" required maxlength="3" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            {{-- TAMBAHKAN DIV UNTUK PESAN ERROR DARI JAVASCRIPT --}}
                            <div class="invalid-feedback" id="rw-error" style="display: none;">Nomor RW ini sudah ada.</div>
                            @error('number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="rt_count">Jumlah RT</label>
                            <input type="text" name="rt_count" class="form-control @error('rt_count') is-invalid @enderror" value="{{ old('rt_count', $rtCount) }}" required maxlength="2" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('rt_count')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        {{-- TAMBAHKAN ID PADA TOMBOL SUBMIT --}}
                        <button type="submit" class="btn btn-primary" id="update-btn">
                            <i class="fa fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.rtrw.index') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Skrip untuk padding nol (sudah ada)
        const rwInputPadding = document.getElementById('rw_number_input');
        rwInputPadding.addEventListener('blur', function() {
            let value = this.value;
            if (value && !isNaN(value)) {
                this.value = value.padStart(3, '0');
            }
        });

        // --- SKRIP VALIDASI REAL-TIME BARU ---
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
            // Tidak perlu cek jika nilainya masih sama dengan nilai awal
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
                            ignore_rw_id: rwIdToIgnore // Kirim ID untuk diabaikan
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