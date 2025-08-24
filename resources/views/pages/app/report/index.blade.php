@extends('layouts.app')

@section('title', 'Daftar Laporan')

@push('styles')
<style>
    :root {
        --primary-color: #16752B; --text-dark: #1f2937; --text-light: #6b7280;
        --bg-body: #f3f4f6; --bg-white: #ffffff; --border-color: #e5e7eb;
    }
    html { background-color: var(--bg-body); }
    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--bg-white);
        max-width: 480px; margin: 0 auto;
        min-height: 100vh; box-shadow: 0 0 40px rgba(0, 0, 0, 0.1);
    }
    .main-content { padding: 1.5rem; padding-bottom: 100px; }
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
    .page-header .header-title { display: flex; align-items: center; gap: 1rem; }
    .page-header .header-title a { font-size: 1.5rem; color: var(--text-dark); text-decoration: none; }
    .page-header .header-title h5 { font-weight: 700; font-size: 1.25rem; margin-bottom: 0; }
    .filter-toggle-button {
        background-color: transparent; border: 1px solid var(--border-color); color: var(--text-light);
        font-size: 0.85rem; font-weight: 600; padding: 0.5rem 1rem; border-radius: 10px;
    }
    .filter-container {
        background-color: var(--bg-body); border-radius: 16px; padding: 1.25rem;
        margin-bottom: 1.5rem; border: 1px solid var(--border-color);
    }
    .filter-group { margin-bottom: 1rem; }
    .filter-group label { font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem; }
    .report-card-professional {
        background-color: var(--bg-white); border-radius: 18px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06); text-decoration: none;
        display: block; overflow: hidden; margin-bottom: 1.5rem;
        border: 1px solid var(--border-color);
    }
    .card-image { width: 100%; height: 220px; object-fit: cover; }
    .card-body { padding: 1rem; }
    .card-top-info { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; }
    .card-category-pill { display: inline-block; background-color: #eef2ff; color: #4338ca; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; }
    .card-title { font-weight: 700; line-height: 1.4; margin-bottom: 1rem; font-size: 1.2rem; color: var(--text-dark); }
    .card-meta-grid { display: grid; gap: 0.75rem; }
    .card-meta-item { display: flex; align-items: center; gap: 0.75rem; font-size: 0.85rem; color: var(--text-light); }
    .card-meta-item i { width: 16px; text-align: center; color: var(--text-light); }
    .card-footer { padding: 0.75rem 1rem; border-top: 1px solid var(--border-color); background-color: #fafafa; display: flex; justify-content: space-between; align-items: center; }
    .user-details { display: flex; align-items: center; gap: 0.75rem; }
    .user-details .avatar-placeholder { width: 28px; height: 28px; border-radius: 50%; background-color: var(--border-color); display: flex; align-items: center; justify-content: center; color: var(--text-light); font-size: 0.8rem; }
    .user-details .avatar { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; }
    .user-details .user-name { font-size: 0.8rem; font-weight: 500; color: var(--text-dark); }
    .status-badge { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.35rem 0.8rem; border-radius: 8px; font-size: 0.8rem; font-weight: 600; }
    .status-badge.delivered { background-color: #dbeafe; color: #2563eb; }
    .status-badge.in_process { background-color: #fef3c7; color: #b45309; }
    .status-badge.completed { background-color: #dcfce7; color: #166534; }
    .status-badge.rejected { background-color: #fee2e2; color: #b91c1c; }
</style>
@endpush

@section('content')
    <div class="page-header">
        <div class="header-title">
            <a href="{{ route('home') }}"><i class="fa-solid fa-arrow-left"></i></a>
            <h5>Semua Laporan</h5>
        </div>
        <button class="filter-toggle-button" type="button" data-bs-toggle="collapse" data-bs-target="#filter-section" aria-expanded="{{ request()->except('page') ? 'true' : 'false' }}" aria-controls="filter-section">
            <i class="fa-solid fa-filter me-1"></i> Filter
        </button>
    </div>

    <div class="filter-container collapse @if(request()->except('page')) show @endif" id="filter-section">
        <form action="{{ route('report.index') }}" method="GET">
            <div class="filter-group">
                <label for="sort">Urutkan</label>
                <select name="sort" class="form-select">
                    <option value="terbaru" {{ request('sort', 'terbaru') == 'terbaru' ? 'selected' : '' }}>Laporan Terbaru</option>
                    <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Laporan Terlama</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="category">Kategori</label>
                <select name="category" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->name }}" {{ request('category') == $category->name ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="filter-group">
                        <label for="rw_id">RW</label>
                        <select name="rw" id="rw_id" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua</option>
                            @foreach($rws as $rw)
                                <option value="{{ $rw->id }}" {{ request('rw') == $rw->id ? 'selected' : '' }}>{{ $rw->number }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="filter-group">
                        <label for="rt_id">RT</label>
                        <select name="rt" id="rt_id" class="form-select" {{ !request('rw') ? 'disabled' : '' }}>
                            <option value="">Semua RT</option>
                            @foreach($rts as $rt)
                                <option value="{{ $rt->id }}" {{ request('rt') == $rt->id ? 'selected' : '' }}>RT {{ $rt->number }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="d-grid gap-2 mt-3" style="grid-template-columns: {{ count(request()->except('page')) > 0 ? '1fr 2fr' : '1fr' }};">
                @if(count(request()->except('page')) > 0)
                    <a href="{{ route('report.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
            </div>
        </form>
    </div>

    @forelse($reports as $report)
        @php $isOwner = Auth::check() && Auth::id() === $report->resident->user_id; @endphp
        <a href="{{ route('report.show', ['code' => $report->code, '_ref' => request()->fullUrl()]) }}" class="report-card-professional">
            <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}" class="card-image">
            <div class="card-body">
                <div class="card-top-info">
                    <span class="card-category-pill">{{ $report->reportCategory->name }}</span>
                </div>
                <h6 class="card-title">{{ $report->title }}</h6>
                <div class="card-meta-grid">
                    <div class="card-meta-item">
                        <i class="fa-solid fa-map-marker-alt"></i>
                        <span>{{ Str::limit($report->address, 40) }}</span>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="user-details">
                    @php
                        $reporter = $report->resident->user;
                        $avatarUrl = $reporter->avatar ?? optional($reporter->resident)->avatar;

                        if ($avatarUrl && !filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
                            $avatarUrl = asset('storage/' . $avatarUrl);
                        }
                    @endphp
                    @if($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="Avatar Pelapor" class="avatar">
                    @else
                        <div class="avatar-placeholder"><i class="fa-solid fa-user"></i></div>
                    @endif
                    <span class="user-name">{{ $isOwner ? $report->resident->user->name : $report->resident->user->censored_name }}</span>
                </div>
                @if ($isOwner && $report->latestStatus)
                    @php $status = $report->latestStatus->status; @endphp
                    <div class="status-badge {{ $status->value }}">
                        <span>{{ $status->label() }}</span>
                    </div>
                @endif
            </div>
        </a>
    @empty
        <div class="d-flex flex-column justify-content-center align-items-center text-center py-5">
            <div id="lottie-empty-list" style="width: 250px; height: 250px;"></div>
            <h5 class="mt-3 fw-bold">Laporan Tidak Ditemukan</h5>
            <p class="text-secondary px-4">Tidak ada laporan yang cocok dengan filter yang Anda pilih.</p>
        </div>
    @endforelse

    <div class="d-flex justify-content-center mt-4">
        {{ $reports->links() }}
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.2/lottie.min.js"></script>
    <script>
        const lottieContainer = document.getElementById('lottie-empty-list');
        if (lottieContainer) {
            bodymovin.loadAnimation({
                container: lottieContainer, renderer: 'svg', loop: true, autoplay: true,
                path: '{{ asset('assets/app/lottie/not-found.json') }}'
            });
        }
    </script>
@endpush