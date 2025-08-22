@extends('layouts.app')

@section('title', 'Beranda')

@push('styles')
<style>
    :root {
        --primary-color: #16752B;
        --header-gradient: linear-gradient(135deg, #22c55e 0%, #15803d 100%);
        --text-dark: #1f2937;
        --text-light: #6b7280;
        --bg-body: #f3f4f6;
        --bg-white: #ffffff;
        --border-color: #e5e7eb;
        --font-sans: 'Inter', sans-serif;
    }

    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    html { background-color: var(--bg-body); }
    body {
        font-family: var(--font-sans);
        background-color: var(--bg-white);
        max-width: 480px;
        margin: 0 auto;
        min-height: 100vh;
        box-shadow: 0 0 40px rgba(0, 0, 0, 0.1);
    }
    .main-content { padding: 0; padding-bottom: 100px; }

    .curved-header {
        background: var(--header-gradient);
        color: white;
        padding: 1.5rem 1.5rem 3.5rem;
        border-bottom-left-radius: 24px;
        border-bottom-right-radius: 24px;
        background-image: var(--header-gradient), url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'%3E%3Cpath d='M0 40L40 0H20L0 20M40 40V20L20 40'/%3E%3C/g%3E%3C/svg%3E");
    }
    .user-greeting { display: flex; align-items: center; justify-content: space-between; }
    .user-greeting .greeting-left { display: flex; align-items: center; gap: 1rem; }
    .user-greeting .avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255, 255, 255, 0.7); }
    .user-greeting .greeting-subtitle { font-size: 0.9rem; color: #d1d5db; margin: 0; }
    .user-greeting .greeting-title { font-weight: 700; font-size: 1.25rem; margin: 0; }
    
    .content-area { 
        padding: 1.5rem; 
        /* PENJELASAN: Properti 'margin-top' dihapus dari sini untuk memberikan jarak antara header dan konten */
    }
    .search-card {
        background: var(--bg-white);
        padding: 0.5rem;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
        margin-top: -3.5rem; /* Margin negatif dipindahkan ke sini agar hanya search-card yang naik */
        position: relative;
        z-index: 10;
    }
    .search-form .form-control { border-radius: 12px; padding-left: 2.75rem; border: none; height: 48px; }
    .search-form .search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-light); }

    .section-header { margin-bottom: 1rem; }
    .section-title { font-weight: 700; font-size: 1.1rem; color: var(--text-dark); }
    
    .category-pills { display: flex; gap: 0.75rem; overflow-x: auto; padding-bottom: 1.5rem; scrollbar-width: none; }
    .category-pills::-webkit-scrollbar { display: none; }
    .category-pills .pill-item {
        display: inline-block; padding: 0.6rem 1.2rem; border-radius: 20px; font-size: 0.9rem;
        font-weight: 600; text-decoration: none; background-color: #F3F4F6; /* Warna diubah agar kontras dengan background putih */
        color: var(--text-light); border: 1px solid var(--border-color); white-space: nowrap;
    }
    .category-pills .pill-item.active { background-color: var(--primary-color); color: var(--bg-white); border-color: var(--primary-color); }

    .report-card-professional {
        background-color: var(--bg-white); border-radius: 18px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06); text-decoration: none;
        display: block; overflow: hidden; margin-bottom: 1.5rem;
        border: 1px solid var(--border-color);
    }
    .card-header-info { padding: 1rem; display: flex; align-items: center; justify-content: space-between; }
    .user-details { display: flex; align-items: center; gap: 0.75rem; }
    .user-details .avatar-placeholder { 
        width: 32px; height: 32px; border-radius: 50%;
        background-color: var(--border-color); display: flex; align-items: center; justify-content: center;
        color: var(--text-light);
    }
    .user-details .avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; }
    .user-details .user-name { font-size: 0.9rem; font-weight: 600; color: var(--text-dark); }
    .card-image { width: 100%; height: 220px; object-fit: cover; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); }
    .card-body { padding: 1rem; }
    .card-title { font-weight: 700; line-height: 1.4; margin-bottom: 1rem; font-size: 1.2rem; color: var(--text-dark); }
    .card-meta-info { display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--text-light); }
    .card-meta-info:not(:last-child) { margin-bottom: 0.5rem; }
    .card-meta-info i { width: 16px; text-align: center; color: var(--primary-color);}
    .card-footer { padding: 0.75rem 1rem; border-top: 1px solid var(--border-color); background-color: #fafafa; }
    .status-badge {
        display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.35rem 0.8rem;
        border-radius: 8px; font-size: 0.8rem; font-weight: 600;
    }
    .status-badge.delivered { background-color: #dbeafe; color: #2563eb; }
    .status-badge.in_process { background-color: #fef3c7; color: #b45309; }
    .status-badge.completed { background-color: #dcfce7; color: #166534; }
    .status-badge.rejected { background-color: #fee2e2; color: #b91c1c; }
</style>
@endpush

@section('content')
    <header class="curved-header">
        <div class="user-greeting">
            <div class="greeting-left">
                @php
                    $user = Auth::user();
                    $avatarUrl = $user->resident->avatar ? asset('storage/' . $user->resident->avatar) : null;
                @endphp
                @if($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="avatar" class="avatar">
                @else
                    <div class="avatar-placeholder"><i class="fa-solid fa-user"></i></div>
                @endif
                <div>
                    <h6 class="greeting-title">{{ $user->name }}</h6>
                    <p class="greeting-subtitle">Warga RT {{ optional($user->resident->rt)->number }} / RW {{ optional($user->resident->rw)->number }}</p>
                </div>
            </div>
        </div>
    </header>

    <div class="content-area">
        <div class="search-card">
            <form action="{{ route('home') }}" method="GET" class="search-form position-relative">
                <i class="fa-solid fa-search search-icon"></i>
                <input type="text" name="search" class="form-control" placeholder="Cari laporan..." value="{{ request('search') }}">
            </form>
        </div>

        <div class="category-section">
            <div class="section-header">
                <h5 class="section-title">Kategori</h5>
            </div>
            <div class="category-pills">
                <a href="{{ route('home') }}" class="pill-item {{ !request('category') ? 'active' : '' }}">Semua</a>
                @foreach ($categories as $category)
                    <a href="{{ route('home', ['category' => $category->name]) }}" class="pill-item {{ request('category') == $category->name ? 'active' : '' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="report-feed">
            <div class="section-header d-flex justify-content-between align-items-center">
                <h5 class="section-title">Laporan Terbaru</h5>
                <a href="{{ route('report.index') }}" class="small fw-bold text-decoration-none" style="color: var(--primary-color);">Lihat Semua</a>
            </div>

            @forelse($reports as $report)
                @php
                    $isOwner = Auth::id() === $report->resident->user_id;
                @endphp
                <a href="{{ route('report.show', ['code' => $report->code, '_ref' => request()->fullUrl()]) }}" class="report-card-professional">
                    <div class="card-header-info">
                        <div class="user-details">
                            @if($isOwner && $report->resident->avatar)
                                <img src="{{ asset('storage/' . $report->resident->avatar) }}" alt="Avatar Pelapor" class="avatar">
                            @else
                                <div class="avatar-placeholder"><i class="fa-solid fa-user"></i></div>
                            @endif
                            <span class="user-name">{{ $isOwner ? $report->resident->user->name : $report->resident->user->censored_name }}</span>
                        </div>
                    </div>
                    <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}" class="card-image">
                    <div class="card-body">
                        <div class="card-category-pill">{{ $report->reportCategory->name }}</div>
                        <h6 class="card-title">{{ $report->title }}</h6>
                        <div class="card-meta-grid">
                            <div class="card-meta-item">
                                <i class="fa-solid fa-map-marker-alt"></i>
                                <span>{{ Str::limit($report->address, 40) }}</span>
                            </div>
                            <div class="card-meta-item">
                                <i class="fa-solid fa-clock"></i>
                                <span>Dilaporkan {{ $report->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        @if($report->latestStatus)
                            @php $status = $report->latestStatus->status; @endphp
                            <div class="status-badge {{ $status->value }}">
                                <span>{{ $status->label() }}</span>
                            </div>
                        @endif
                    </div>
                </a>
            @empty
                <div class="d-flex flex-column justify-content-center align-items-center text-center py-5">
                    <div id="lottie-empty-home" style="width: 250px; height: 250px;"></div>
                    <h5 class="mt-3 fw-bold">Belum Ada Laporan</h5>
                    <p class="text-secondary px-4">Jadilah yang pertama membuat laporan di lingkungan Anda!</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.2/lottie.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lottieContainer = document.getElementById('lottie-empty-home');
        if (lottieContainer) {
            bodymovin.loadAnimation({
                container: lottieContainer, renderer: 'svg', loop: true, autoplay: true,
                path: '{{ asset('assets/app/lottie/not-found.json') }}'
            });
        }
    });
</script>
@endpush