@extends('layouts.app')

@section('title', 'Laporan Saya')

@push('styles')
<style>
    :root {
        --primary-color: #10B981;
        --primary-dark: #059669;
        --text-dark: #111827;
        --text-light: #6B7280;
        --bg-body: #F9FAFB;
        --bg-white: #FFFFFF;
        --border-color: #F3F4F6;
        --font-sans: 'Inter', 'Poppins', 'Segoe UI', sans-serif;
    }

    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    html, body { background-color: var(--bg-body); }
    body {
        font-family: var(--font-sans);
        max-width: 480px;
        margin: 0 auto;
        min-height: 100vh;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.05);
        background-color: var(--bg-white);
    }
    .main-content {
        padding: 1.5rem;
        padding-bottom: 120px;
    }

    .page-header h3 {
        font-weight: 800;
        font-size: 2rem;
        color: var(--text-dark);
        margin-bottom: 1.5rem;
    }

    .stat-filter-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    .stat-card {
        background-color: var(--bg-body);
        padding: 1rem;
        border-radius: 16px;
        text-decoration: none;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease-in-out;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }
    .stat-card.active {
        background-color: var(--primary-color);
        color: var(--bg-white);
        border-color: var(--primary-color);
        box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);
        transform: translateY(-4px);
    }
    .stat-card .card-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-bottom: 0.75rem;
    }
    .stat-card .card-icon.delivered { background-color: #e0f2fe; color: #0284c7; }
    .stat-card .card-icon.in_process { background-color: #fef3c7; color: #d97706; }
    .stat-card .card-icon.completed { background-color: #dcfce7; color: #16a34a; }
    .stat-card .card-icon.rejected { background-color: #fee2e2; color: #dc2626; }
    .stat-card.active .card-icon { background-color: rgba(255,255,255,0.2); color: var(--bg-white); }
    
    .stat-card .card-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-light);
        margin-bottom: 0.25rem;
    }
    .stat-card.active .card-title { color: rgba(255,255,255,0.8); }

    .stat-card .card-count {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-dark);
    }
    .stat-card.active .card-count { color: var(--bg-white); }

    .content-title-header {
        font-weight: 700;
        font-size: 1.25rem;
        color: var(--text-dark);
        margin: 2.5rem 0 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .report-list-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        background-color: var(--bg-white);
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
    .report-list-item .item-thumbnail {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        object-fit: cover;
        flex-shrink: 0;
    }
    .report-list-item .item-details { flex-grow: 1; }
    .report-list-item .item-title {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.25rem;
        line-height: 1.4;
    }
    .report-list-item .item-meta {
        font-size: 0.8rem;
        color: var(--text-light);
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .report-list-item .item-actions .dropdown-toggle { color: var(--text-light); }
    .report-list-item .item-actions .dropdown-toggle::after { display: none; }

    .report-link-wrapper {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-grow: 1;
        text-decoration: none;
        color: inherit;
    }

    .empty-state-container { text-align: center; padding: 2rem 1rem; }
    .empty-state-container h5 { font-weight: 700; color: var(--text-dark); margin-top: 1rem; }
    .empty-state-container p { color: var(--text-light); max-width: 300px; margin: 0.5rem auto 1.5rem; }
    .empty-state-container .btn-create-report {
        background-color: var(--primary-color);
        color: var(--bg-white);
        border-radius: 50px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        text-decoration: none;
    }
</style>
@endpush

@section('content')
    @php
        $active_status = request('status', 'delivered');
    @endphp

    @include('sweetalert::alert')

    <div class="page-header">
        <h3>Laporan Saya</h3>
        
        <div class="stat-filter-grid">
            <a href="{{ route('report.myreport', ['status' => 'delivered']) }}" class="stat-card {{ $active_status === 'delivered' ? 'active' : '' }}">
                <div class="card-icon delivered"><i class="fa-solid fa-paper-plane"></i></div>
                <p class="card-title">Terkirim</p>
                <h4 class="card-count">{{ $statusCounts['delivered'] }}</h4>
            </a>
            <a href="{{ route('report.myreport', ['status' => 'in_process']) }}" class="stat-card {{ $active_status === 'in_process' ? 'active' : '' }}">
                <div class="card-icon in_process"><i class="fa-solid fa-spinner"></i></div>
                <p class="card-title">Diproses</p>
                <h4 class="card-count">{{ $statusCounts['in_process'] }}</h4>
            </a>
            <a href="{{ route('report.myreport', ['status' => 'completed']) }}" class="stat-card {{ $active_status === 'completed' ? 'active' : '' }}">
                <div class="card-icon completed"><i class="fa-solid fa-check-double"></i></div>
                <p class="card-title">Selesai</p>
                <h4 class="card-count">{{ $statusCounts['completed'] }}</h4>
            </a>
            <a href="{{ route('report.myreport', ['status' => 'rejected']) }}" class="stat-card {{ $active_status === 'rejected' ? 'active' : '' }}">
                <div class="card-icon rejected"><i class="fa-solid fa-circle-xmark"></i></div>
                <p class="card-title">Ditolak</p>
                <h4 class="card-count">{{ $statusCounts['rejected'] }}</h4>
            </a>
        </div>
    </div>
    
    <h5 class="content-title-header">
        Laporan {{ \App\Enums\ReportStatusEnum::tryFrom($active_status)->label() }}
    </h5>

    <div class="report-list-container">
        @forelse ($reports as $report)
            <div class="report-list-item">
                <a href="{{ route('report.show', ['report' => $report, '_ref' => request()->fullUrl()]) }}" class="report-link-wrapper">
                    <img src="{{ asset('storage/' . $report->image) }}" alt="Thumbnail" class="item-thumbnail">
                    <div class="item-details">
                        <p class="item-title">{{ Str::limit($report->title, 50) }}</p>
                        <p class="item-meta">
                            <i class="fa-solid fa-calendar-alt fa-xs"></i>
                            <span>{{ \Carbon\Carbon::parse($report->created_at)->isoFormat('D MMM YYYY') }}</span>
                        </p>
                    </div>
                </a>
                <div class="item-actions">
                    @canany(['complete', 'update', 'delete'], $report)
                        <div class="dropdown">
                            <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @can('complete', $report)
                                    <li><a class="dropdown-item" href="{{ route('report.complete.form', $report) }}">Selesaikan</a></li>
                                @endcan
                                @can('update', $report)
                                    <li><a class="dropdown-item" href="{{ route('report.edit', $report) }}">Edit</a></li>
                                @endcan
                                @can('delete', $report)
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('report.destroy', $report) }}" method="POST" class="delete-form-myreport">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">Hapus</button>
                                        </form>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    @endcanany
                </div>
            </div>
        @empty
            @php
                $title = 'Belum Ada Laporan';
                $message = 'Saat ini tidak ada laporan dengan status "' . \App\Enums\ReportStatusEnum::tryFrom($active_status)->label() . '".';
            @endphp
            <div class="empty-state-container">
                <div id="lottie-empty" style="width: 250px; height: 250px; margin: 0 auto;"></div>
                <h5>{{ $title }}</h5>
                <p>{{ $message }}</p>
                <a href="{{ route('report.take') }}" class="btn-create-report mt-3">
                    <i class="fa-solid fa-plus me-1"></i> Buat Laporan Baru
                </a>
            </div>
        @endforelse
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.2/lottie.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var lottieContainer = document.getElementById('lottie-empty');
            if (lottieContainer) {
                bodymovin.loadAnimation({
                    container: lottieContainer,
                    renderer: 'svg',
                    loop: true,
                    autoplay: true,
                    path: '{{ asset('assets/app/lottie/not-found.json') }}'
                });
            }

            document.querySelectorAll('.delete-form-myreport').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    
                    Swal.fire({
                        title: 'Anda yakin ingin menghapus?',
                        text: "Laporan yang sudah dihapus tidak dapat dikembalikan.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus Saja!',
                        cancelButtonText: 'Batalkan'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush