@extends('layouts.app')

@section('title', 'Profil Saya')

@push('styles')
<style>
    /* Variabel Desain "Vibrant" */
    :root {
        --primary-color: #10B981;
        --text-dark: #111827;
        --text-light: #6B7280;
        --bg-body: #F9FAFB;
        --bg-white: #FFFFFF;
        --border-color: #e5e7eb;
        --font-sans: 'Inter', 'Poppins', 'Segoe UI', sans-serif;
    }

    /* Pengaturan Dasar */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    html, body { background-color: var(--bg-body); }
    body {
        font-family: var(--font-sans);
        max-width: 480px;
        margin: 0 auto;
        min-height: 100vh;
        box-shadow: 0 0 40px rgba(0, 0, 0, 0.07);
        background-color: var(--bg-body);
    }
    .main-content { padding: 1.5rem; padding-bottom: 100px; }
    .page-title {
        font-weight: 800;
        font-size: 2rem;
        color: var(--text-dark);
        margin-bottom: 1.5rem;
    }

    /* Kartu Profil Utama */
    .profile-card-main {
        background: linear-gradient(135deg, #059669 0%, #10B981 100%);
        border-radius: 24px;
        padding: 1.5rem;
        color: white;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 15px 30px -10px rgba(16, 185, 129, 0.4);
        margin-bottom: 2rem;
    }
    .profile-card-main .avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 4px solid var(--white);
        object-fit: cover;
        flex-shrink: 0;
    }
    .profile-card-main .user-info .name {
        font-weight: 700;
        font-size: 1.5rem;
        margin: 0;
    }
    .profile-card-main .user-info .address {
        font-size: 0.9rem;
        opacity: 0.8;
        margin-top: 0.25rem;
    }

    /* Section Title */
    .section-title {
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 1rem;
        font-size: 1.25rem;
    }

    /* Kartu Statistik Berwarna */
    .stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
        margin-bottom: 2.5rem;
    }
    .stat-card {
        padding: 1rem;
        border-radius: 16px;
        text-align: center;
        color: white;
        box-shadow: 0 8px 20px -5px rgba(0,0,0,0.2);
    }
    .stat-card.active { background: linear-gradient(135deg, #3B82F6, #60A5FA); }
    .stat-card.completed { background: linear-gradient(135deg, #16A34A, #34D399); }
    .stat-card.rejected { background: linear-gradient(135deg, #EAB308, #FBBF24); }

    .stat-card .stat-icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        opacity: 0.8;
    }
    .stat-card .stat-count {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
    }
    .stat-card .stat-label {
        font-size: 0.75rem;
        font-weight: 500;
        opacity: 0.9;
        margin-top: 0.25rem;
    }

    /* Menu Aksi Model Kartu */
    .action-menu .action-card {
        display: flex; align-items: center; gap: 1rem;
        padding: 1rem; border-radius: 16px;
        margin-bottom: 1rem; border: none;
        background-color: var(--bg-white);
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        font-weight: 600; font-size: 1rem; color: var(--text-dark);
        text-decoration: none; transition: all 0.2s ease-in-out;
    }
    .action-menu .action-card:hover { transform: scale(1.03); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
    .action-menu .menu-icon {
        width: 48px; height: 48px; border-radius: 14px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
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

    <h3 class="page-title">Profil Saya</h3>

    <div class="profile-card-main">
        @php
            $avatarUrl = $resident->avatar;
            if ($avatarUrl && !Str::startsWith($avatarUrl, 'http')) {
                $avatarUrl = asset('storage/' . $avatarUrl);
            } elseif (!$avatarUrl) {
                $avatarUrl = asset('assets/app/images/default-avatar.png');
            }
        @endphp
        <img src="{{ $avatarUrl }}" alt="avatar" class="avatar">
        <div class="user-info">
            <h4 class="name">{{ $user->name }}</h4>
            <p class="address">
                @if($resident->rt && $resident->rw)
                    RT {{ $resident->rt->number }} / RW {{ $resident->rw->number }}
                @else
                    {{ $user->email }}
                @endif
            </p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card active">
            <div class="stat-icon"><i class="fa-solid fa-spinner"></i></div>
            <div class="stat-count" data-count="{{ $activeReportsCount }}">0</div>
            <div class="stat-label">Aktif</div>
        </div>
        <div class="stat-card completed">
            <div class="stat-icon"><i class="fa-solid fa-check-double"></i></div>
            <div class="stat-count" data-count="{{ $completedReportsCount }}">0</div>
            <div class="stat-label">Selesai</div>
        </div>
        <div class="stat-card rejected">
            <div class="stat-icon"><i class="fa-solid fa-circle-xmark"></i></div>
            <div class="stat-count" data-count="{{ $rejectedReportsCount }}">0</div>
            <div class="stat-label">Ditolak</div>
        </div>
    </div>

    <div class="action-menu">
        <h5 class="section-title">Pengaturan</h5>
        <a href="{{ route('profile.edit') }}" class="action-card">
            <div class="menu-icon icon-edit">
                <i class="fa-solid fa-user-pen"></i>
            </div>
            <span>Ubah Profil & Alamat</span>
            <i class="fa-solid fa-chevron-right"></i>
        </a>
        <a href="#" class="action-card" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <div class="menu-icon icon-logout">
                <i class="fa-solid fa-right-from-bracket"></i>
            </div>
            <span class="text-danger">Keluar</span>
            <i class="fa-solid fa-chevron-right"></i>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animasi Hitung Angka Statistik
            const counters = document.querySelectorAll('.stat-count');
            const speed = 100; // Durasi animasi

            counters.forEach(counter => {
                const updateCount = () => {
                    const target = +counter.getAttribute('data-count');
                    const count = +counter.innerText;
                    const increment = target / speed;

                    if (count < target) {
                        counter.innerText = Math.ceil(count + increment);
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