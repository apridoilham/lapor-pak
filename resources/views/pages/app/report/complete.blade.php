@extends('layouts.no-nav')

@section('title', 'Selesaikan Laporan')

@section('content')
    <div class="header-nav mb-4">
        <a href="{{ route('report.myreport') }}" class="text-decoration-none">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1>Selesaikan Laporan</h1>
    </div>

    <p class="text-description mb-4">
        Laporan dengan kode <strong>{{ $report->code }}</strong> akan ditandai sebagai "Selesai". Mohon berikan catatan penyelesaian.
    </p>

    <form action="{{ route('report.complete', $report->id) }}" method="POST" id="complete-report-form">
        @csrf
        <div class="mb-3">
            <label for="description" class="form-label fw-bold">Catatan Penyelesaian</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" placeholder="Contoh: Masalah sudah diselesaikan secara mandiri." required>{{ old('description') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-grid mt-4">
            <button class="btn btn-success py-2" type="submit" id="complete-btn" disabled>
                Tandai Sebagai Selesai
            </button>
        </div>
    </form>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('complete-report-form');
        const completeButton = document.getElementById('complete-btn');
        const descriptionInput = document.getElementById('description');

        function checkFormValidity() {
            completeButton.disabled = descriptionInput.value.trim() === '';
        }

        descriptionInput.addEventListener('input', checkFormValidity);

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            Swal.fire({
                title: 'Anda yakin?',
                text: "Anda akan menandai laporan ini sebagai selesai dan tidak dapat diubah lagi.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Selesaikan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                } else {
                    window.location.href = "{{ route('report.myreport') }}";
                }
            });
        });
    });
</script>
@endsection