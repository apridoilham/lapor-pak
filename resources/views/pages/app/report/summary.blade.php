@extends('layouts.no-nav')

@section('title', 'Ringkasan Laporan')

@push('styles')
<style>
    :root {
        --primary-color: #16752B;
        --text-dark: #1f2937;
        --text-light: #6b7280;
        --bg-body: #f3f4f6;
        --bg-white: #ffffff;
        --border-color: #e5e7eb;
        --font-sans: 'Inter', 'Poppins', 'Segoe UI', sans-serif;
    }
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    
    html, body { background-color: var(--bg-body); }
    body {
        font-family: var(--font-sans);
        max-width: 480px; margin: 0 auto;
        box-shadow: 0 0 40px rgba(0, 0, 0, 0.07);
        background-color: var(--bg-body);
    }
    .main-content { padding: 1.5rem; }
    .page-container { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; }
    #downloadable-content { background-color: var(--bg-white); padding: 1.5rem; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.07); width: 100%; max-width: 420px; border: 1px solid var(--border-color); }
    .summary-header { display: flex; align-items: center; gap: 0.75rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1rem; }
    .summary-header img.logo { height: 40px; width: 40px; border-radius: 8px; }
    .summary-header .header-text h5 { font-weight: 700; font-size: 1.1rem; color: var(--text-dark); margin: 0; }
    .summary-header .header-text p { font-size: 0.8rem; color: var(--text-light); margin: 0; }
    .report-image {
        width: 100%;
        height: auto; /* PERBAIKAN: Memastikan tinggi otomatis */
        object-fit: contain; /* PERBAIKAN: Memastikan gambar tidak terpotong */
        border-radius: 12px;
        margin-bottom: 1rem;
        border: 1px solid var(--border-color);
    }
    .report-title { font-size: 1.25rem; font-weight: 700; color: var(--text-dark); margin-bottom: 1rem; line-height: 1.4; }
    .report-description { font-size: 0.95rem; color: var(--text-light); line-height: 1.6; white-space: pre-wrap; word-wrap: break-word; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); }
    .detail-list { list-style: none; padding: 0; margin: 0; }
    .detail-item { display: flex; align-items: flex-start; gap: 1rem; }
    .detail-item:not(:last-child) { margin-bottom: 1.25rem; }
    .detail-icon { flex-shrink: 0; width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background-color: var(--bg-body); color: var(--primary-color); font-size: 1rem; }
    .detail-text .label { font-size: 0.8rem; color: var(--text-light); margin-bottom: 0.1rem; }
    .detail-text .value { font-weight: 600; color: var(--text-dark); font-size: 0.95rem; line-height: 1.5; white-space: pre-wrap; word-wrap: break-word; }
    .action-buttons { width: 100%; max-width: 420px; margin-top: 1.5rem; }
</style>
@endpush

@section('content')
    <div class="page-container">
        <div id="downloadable-content">
            <div class="summary-header">
                <img src="{{ asset('assets/app/images/logo.jpg') }}" alt="Logo BSB Lapor" class="logo">
                <div class="header-text">
                    <h5>Bukti Laporan Warga</h5>
                    <p>bsblapor.site</p>
                </div>
            </div>
            <img src="{{ asset('storage/' . $report->image) }}" class="report-image" alt="Bukti Laporan" crossorigin="anonymous">
            <h4 class="report-title">{{ $report->title }}</h4>
            <p class="report-description">{{ $report->description }}</p>
            <ul class="detail-list">
                <li class="detail-item">
                    <div class="detail-icon"><i class="fa-solid fa-calendar-day"></i></div>
                    <div class="detail-text">
                        <p class="label">Tanggal Laporan</p>
                        <p class="value">{{ \Carbon\Carbon::parse($report->created_at)->tz('Asia/Jakarta')->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB</p>
                    </div>
                </li>
                <li class="detail-item">
                    <div class="detail-icon"><i class="fa-solid fa-tag"></i></div>
                    <div class="detail-text">
                        <p class="label">Kategori</p>
                        <p class="value">{{ $report->reportCategory->name }}</p>
                    </div>
                </li>
                <li class="detail-item">
                    <div class="detail-icon"><i class="fa-solid fa-map-marker-alt"></i></div>
                    <div class="detail-text">
                        <p class="label">Lokasi Laporan</p>
                        <p class="value">{{ $report->address }}</p>
                    </div>
                </li>
            </ul>
        </div>
        <div class="action-buttons">
            <div class="d-grid gap-2">
                <button id="download-button" class="btn btn-success py-2 fw-bold">
                    <i class="fa-solid fa-download me-2"></i>
                    Unduh Bukti Laporan
                </button>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary py-2">Kembali ke Beranda</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const downloadButton = document.getElementById('download-button');
            const downloadableContent = document.getElementById('downloadable-content');

            downloadButton.addEventListener('click', function() {
                downloadButton.disabled = true;
                downloadButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...`;

                setTimeout(() => {
                    html2canvas(downloadableContent, {
                        useCORS: true,
                        scale: 2.5,
                        backgroundColor: '#f3f4f6'
                    }).then(canvas => {
                        const link = document.createElement('a');
                        link.download = 'bukti-laporan-{{ $report->code }}.png';
                        link.href = canvas.toDataURL('image/png');
                        link.click();
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Bukti laporan berhasil diunduh.', timer: 2000, showConfirmButton: false });
                    }).catch(error => {
                        console.error('oops, something went wrong!', error);
                        Swal.fire({ icon: 'error', title: 'Gagal Mengunduh', text: 'Terjadi kesalahan saat membuat gambar. Silakan coba lagi.' });
                    }).finally(() => {
                        downloadButton.disabled = false;
                        downloadButton.innerHTML = '<i class="fa-solid fa-download me-2"></i> Unduh Bukti Laporan';
                    });
                }, 500);
            });
        });
    </script>
@endpush