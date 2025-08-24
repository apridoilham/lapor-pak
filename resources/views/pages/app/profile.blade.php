@extends('layouts.app')

@section('title', 'Profil Saya')

@push('styles')
<style>
    :root {
        --primary-color: #10B981;
        --text-dark: #1f2937;
        --text-light: #6b7280;
        --bg-body: #f1f5f9;
        --bg-white: #ffffff;
        --border-color: #e5e7eb;
        --font-sans: 'Inter', 'Poppins', 'Segoe UI', sans-serif;
    }

    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    body {
        font-family: var(--font-sans);
        max-width: 480px; margin: 0 auto;
        min-height: 100vh;
        box-shadow: 0 0 40px rgba(0, 0, 0, 0.07);
        background-color: var(--bg-body);
    }
    .main-content { padding: 1.5rem; padding-bottom: 100px; }

    .page-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    .page-header h3 {
        font-weight: 800;
        font-size: 1.75rem;
        color: var(--text-dark);
    }

    .neumorphic-card {
        background-color: var(--bg-body);
        border-radius: 24px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 8px 8px 16px #d1d9e6, -8px -8px 16px #ffffff;
    }

    .profile-info-main {
        text-align: center;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .profile-info-main .avatar {
        width: 120px; height: 120px; border-radius: 50%;
        object-fit: cover;
        border: 5px solid var(--bg-white);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        margin-bottom: 1rem;
    }
    .profile-info-main .name { font-weight: 700; font-size: 1.75rem; color: var(--text-dark); }
    
    .info-list .info-item {
        display: flex; align-items: flex-start; gap: 1rem;
    }
    .info-list .info-item:not(:last-child) { margin-bottom: 1rem; }
    .info-list .info-icon {
        flex-shrink: 0; width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        background-color: var(--bg-white);
        color: var(--primary-color);
        font-size: 1.2rem;
        box-shadow: inset 4px 4px 8px #d1d9e6, inset -4px -4px 8px #ffffff;
    }
    .info-list .info-text .label { font-size: 0.8rem; color: var(--text-light); }
    .info-list .info-text .value { font-weight: 600; color: var(--text-dark); }

    .stats-summary-card {
        display: flex;
        justify-content: space-around;
        text-align: center;
    }
    .stats-summary-card .stat-item .stat-count { font-size: 1.75rem; font-weight: 700; color: var(--text-dark); }
    .stats-summary-card .stat-item .stat-label { font-size: 0.8rem; color: var(--text-light); margin-top: 0.25rem; }

    .action-menu .action-card {
        display: flex; align-items: center; gap: 1rem; padding: 1rem;
        border-radius: 16px; margin-bottom: 1rem;
        background-color: var(--bg-white); box-shadow: 4px 4px 8px #d1d9e6, -4px -4px 8px #ffffff;
        font-weight: 600; color: var(--text-dark);
        text-decoration: none; transition: all 0.2s ease-in-out;
    }
    .action-menu .action-card:active {
        box-shadow: inset 4px 4px 8px #d1d9e6, inset -4px -4px 8px #ffffff;
    }
    .action-menu .menu-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem;
    }
    .icon-edit { background-color: #DBEAFE; color: #2563EB; }
    .icon-logout { background-color: #FEE2E2; color: #DC2626; }
    .action-menu .action-card .fa-chevron-right { margin-left: auto; color: var(--text-light); }
</style>
@endpush

@section('content')
    @php
        $user = Auth::user();
        $resident = $user->resident;
    @endphp

    @include('sweetalert::alert')

    <div class="page-header">
        <h3>Profil Saya</h3>
    </div>

    <div class="neumorphic-card">
        <div class="profile-info-main">
            @php
                $avatarUrl = Auth::user()->avatar ?? optional(Auth::user()->resident)->avatar;
                if ($avatarUrl && !filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
                    $avatarUrl = asset('storage/' . $avatarUrl);
                } elseif (empty($avatarUrl)) {
                    $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=10B981&color=fff&size=128';
                }
            @endphp
            <img src="{{ $avatarUrl }}" alt="avatar" class="avatar">
            <h4 class="name">{{ $user->name }}</h4>
        </div>
        <div class="info-list">
            <div class="info-item">
                <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
                <div class="info-text">
                    <p class="label">Email</p>
                    <p class="value">{{ $user->email }}</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                <div class="info-text">
                    <p class="label">Nomor Telepon</p>
                    <p class="value">{{ $resident->phone ?? 'Belum diisi' }}</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="fa-solid fa-map-marker-alt"></i></div>
                <div class="info-text">
                    <p class="label">Alamat</p>
                    <p class="value">
                        @if($resident->rt && $resident->rw)
                            Warga RW {{ $resident->rw->number }} / RT {{ $resident->rt->number }}
                        @else
                            Alamat belum lengkap
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="neumorphic-card stats-summary-card">
        <div class="stat-item">
            <p class="stat-count" data-count="{{ $deliveredCount }}">0</p>
            <p class="stat-label">Terkirim</p>
        </div>
        <div class="stat-item">
            <p class="stat-count" data-count="{{ $inProcessCount }}">0</p>
            <p class="stat-label">Diproses</p>
        </div>
        <div class="stat-item">
            <p class="stat-count" data-count="{{ $completedCount }}">0</p>
            <p class="stat-label">Selesai</p>
        </div>
        <div class="stat-item">
            <p class="stat-count" data-count="{{ $rejectedCount }}">0</p>
            <p class="stat-label">Ditolak</p>
        </div>
    </div>

    <div class="action-menu">
        <a href="{{ route('profile.edit') }}" class="action-card">
            <div class="menu-icon icon-edit"><i class="fa-solid fa-user-pen"></i></div>
            <span>Ubah Profil & Alamat</span>
            <i class="fa-solid fa-chevron-right"></i>
        </a>
        <a href="#" class="action-card" onclick="event.preventDefault(); document.getElementById('logout-form-user').submit();">
            <div class="menu-icon icon-logout"><i class="fa-solid fa-right-from-bracket"></i></div>
            <span class="text-danger">Keluar</span>
            <i class="fa-solid fa-chevron-right"></i>
        </a>
        <form id="logout-form-user" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.stat-count');
            counters.forEach(counter => {
                const updateCount = () => {
                    const target = +counter.getAttribute('data-count');
                    const count = +counter.innerText;
                    const increment = Math.max(1, Math.ceil(target / 100));

                    if (count < target) {
                        counter.innerText = Math.min(count + increment, target);
                        setTimeout(updateCount, 15);
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCount();
            });
        });
    </script>
@endsection