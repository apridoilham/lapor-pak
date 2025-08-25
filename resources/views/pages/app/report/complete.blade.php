@extends('layouts.no-nav')

@section('title', 'Selesaikan Laporan')

@push('styles')
<style>
    :root {
        --primary-color: #10B981;
        --text-dark: #111827;
        --text-light: #6B7280;
        --bg-body: #F9FAFB;
        --bg-white: #FFFFFF;
        --border-color: #e5e7eb;
        --font-sans: 'Inter', sans-serif;
    }
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    html, body { background-color: var(--bg-body); }
    body {
        font-family: var(--font-sans);
        max-width: 480px; margin: 0 auto;
        min-height: 100vh;
        box-shadow: 0 0 40px rgba(0, 0, 0, 0.07);
        background-color: var(--bg-body);
    }
    .main-content { padding: 1.5rem; padding-bottom: 100px; }
    .page-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
    .page-header .back-button { font-size: 1.5rem; color: var(--text-dark); text-decoration: none; }
    .page-header h3 { font-weight: 800; font-size: 1.75rem; color: var(--text-dark); margin: 0; }
    .page-description { font-size: 0.95rem; color: var(--text-light); margin-bottom: 2rem; }
    .form-section-card { background-color: var(--bg-white); padding: 1.5rem; border-radius: 20px; margin-bottom: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    .report-summary { background-color: var(--bg-body); border: 1px solid var(--border-color); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; }
    .report-summary .summary-title { font-weight: 600; font-size: 1rem; color: var(--text-dark); margin-bottom: 0.25rem; }
    .report-summary .summary-code { font-size: 0.85rem; color: var(--text-light); }
    .form-label { font-weight: 600; font-size: 0.9rem; color: var(--text-dark); margin-bottom: 0.5rem; }
    .form-control { border-radius: 12px; border: 1px solid var(--border-color); background-color: #F9FAFB; padding: 0.8rem 1rem; transition: all 0.2s ease; width: 100%; }
    .form-control:focus { background-color: var(--bg-white); border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); outline: none; }
    .btn-submit { width: 100%; padding: 0.9rem; border-radius: 16px; border: none; font-weight: 700; font-size: 1rem; transition: all 0.3s ease; }
    .btn-submit:disabled { background: #d1d5db; box-shadow: none; cursor: not-allowed; }
</style>
@endpush

@section('content')
    <div class="page-header">
        <a href="{{ route('report.myreport') }}" class="back-button"><i class="fa-solid fa-arrow-left"></i></a>
        <h3>Selesaikan Laporan</h3>
    </div>

    <p class="page-description">
        Laporan Anda akan ditandai sebagai "Selesai". Mohon berikan catatan akhir sebagai konfirmasi.
    </p>

    <div class="form-section-card">
        <div class="report-summary">
            <p class="summary-title">{{ $report->title }}</p>
            <p class="summary-code mb-0">Kode Laporan: {{ $report->code }}</p>
        </div>
        <form action="{{ route('report.complete', $report->id) }}" method="POST" id="complete-report-form">
            @csrf
            <div class="mb-3">
                <label for="description" class="form-label">Catatan Penyelesaian</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" placeholder="Contoh: Masalah sudah diselesaikan secara mandiri." required>{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
    
            <div class="d-grid mt-4">
                <button class="btn btn-success btn-submit" type="submit" id="complete-btn" disabled>
                    Tandai Sebagai Selesai
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
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
                }
            });
        });

        checkFormValidity();
    });
</script>
@endpush