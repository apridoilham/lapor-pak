@extends('layouts.no-nav')

@section('title', 'Ringkasan Laporan')

@push('styles')
<style>
    .summary-header {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .summary-header h5 {
        font-weight: 700;
        color: #16752B;
    }
    .summary-header p {
        color: #6c757d;
        font-size: 0.9rem;
    }
    .summary-card {
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        background-color: #ffffff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .summary-card .report-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    .summary-card .report-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #212529;
    }
    .summary-card .detail-item {
        margin-bottom: 12px;
    }
    .summary-card .detail-label {
        font-size: 0.8rem;
        color: #6c757d;
        display: block;
        margin-bottom: 2px;
    }
    .summary-card .detail-value {
        font-weight: 500;
        color: #212529;
    }
    .summary-card .detail-value.description {
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    .summary-footer {
        text-align: center;
        border-top: 1px solid #f0f0f0;
        margin-top: 20px;
        padding-top: 10px;
    }
    .summary-footer small {
        color: #999;
    }
</style>
@endpush

@section('content')
    <div class="d-flex flex-column justify-content-center align-items-center vh-100">
        
        <div class="summary-header">
            <i class="fa-solid fa-check-circle fa-2x text-success mb-2"></i>
            <h5>Laporan Terkirim!</h5>
            <p>Bagikan ringkasan ini sebagai bukti pelaporan Anda.</p>
        </div>
        
        <div id="downloadable-content" class="summary-card mb-4">
            <img src="{{ asset('storage/' . $report->image) }}" class="report-image" alt="Bukti Laporan">
            
            <h5 class="report-title">{{ $report->title }}</h5>

            <div class="detail-item">
                <span class="detail-label">Tanggal Laporan</span>
                <span class="detail-value">
                    {{ \Carbon\Carbon::parse($report->created_at)->tz('Asia/Jakarta')->isoFormat('dddd, D MMMM YYYY, HH:mm') }}
                </span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Deskripsi Laporan</span>
                <p class="detail-value description">{{ $report->description }}</p>
            </div>
             <div class="detail-item">
                <span class="detail-label">Lokasi Laporan</span>
                <span class="detail-value">{{ $report->address }}</span>
            </div>

            <div class="summary-footer">
                <small>Dilaporkan melalui bsblapor.site</small>
            </div>
        </div>

        <div class="d-grid gap-2 w-100 px-4">
            <button id="download-button" class="btn btn-success py-2">
                <i class="fa-solid fa-download me-2"></i>
                Unduh Ringkasan
            </button>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary py-2">Kembali ke Beranda</a>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        document.getElementById('download-button').addEventListener('click', function() {
            const summaryCard = document.getElementById('downloadable-content');
            const button = this;
            
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengunduh...';

            html2canvas(summaryCard, {
                useCORS: true,
                scale: 2 
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'laporan-{{ $report->code }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                
                button.disabled = false;
                button.innerHTML = '<i class="fa-solid fa-download me-2"></i> Unduh Ringkasan';
            });
        });
    </script>
@endsection