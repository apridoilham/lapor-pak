@extends('layouts.app')

@section('title', 'Profil Saya')

@push('styles')
<style>
    /* Styling ini bisa dipindahkan ke app.css jika diinginkan */
    body {
        background-color: #f8f9fa;
    }

    .profile-header {
        background: linear-gradient(135deg, #16752B, #2c5282); /* Gradient dari hijau ke biru gelap */
        color: white;
        padding: 2rem 1.5rem 4rem; /* Padding bawah lebih besar */
        border-bottom-left-radius: 30px;
        border-bottom-right-radius: 30px;
        margin: -1rem -1rem 0; /* Menarik header ke tepi layar */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .profile-header .avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 4px solid white;
        object-fit: cover;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    .profile-header .profile-info h4 {
        margin-bottom: 0;
        font-weight: 700;
        font-size: 1.25rem;
    }
    
    .profile-header .profile-info p {
        margin-bottom: 0;
        opacity: 0.8;
        font-size: 0.9rem;
    }

    .stats-card {
        display: flex;
        justify-content: space-around;
        background-color: white;
        padding: 1.25rem 1rem;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-top: -50px; /* Membuat kartu melayang di atas header */
        position: relative;
        z-index: 10;
    }

    .stats-card .stat-item h5 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
    }

    .stats-card .stat-item p {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 0;
    }
    
    .profile-menu {
        margin-top: 1.5rem;
        padding-bottom: 80px;
    }

    .btn-profile-menu {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        text-decoration: none;
        color: #495057;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.2s ease-in-out;
    }
</style>
@endpush

@section('content')
    @php
        // Variabel $user, $activeReportsCount, dll sudah dikirim dari controller
        $resident = $user->resident;
    @endphp

    <div class="profile-header">
        <div class="d-flex align-items-center gap-3">
            @php
                $avatarUrl = $resident->avatar;
                if ($avatarUrl && !Str::startsWith($avatarUrl, 'http')) {
                    $avatarUrl = asset('storage/' . $avatarUrl);
                } elseif (!$avatarUrl) {
                    $avatarUrl = asset('assets/app/images/default-avatar.png');
                }
            @endphp
            <img src="{{ $avatarUrl }}" alt="avatar" class="avatar">
            <div class="profile-info">
                <h4>{{ $user->name }}</h4>
                <p>{{ $user->email }}</p>
            </div>
        </div>
    </div>

    <div class="stats-card text-center">
        <div class="stat-item">
            <h5>{{ $activeReportsCount }}</h5>
            <p>Laporan Aktif</p>
        </div>
        <div class="stat-item">
            <h5>{{ $completedReportsCount }}</h5>
            <p>Selesai</p>
        </div>
        <div class="stat-item">
            <h5>{{ $rejectedReportsCount }}</h5>
            <p>Ditolak</p>
        </div>
    </div>

    @if(!$resident->rt_id || !$resident->rw_id || $resident->address === 'Alamat belum diatur')
        <div class="alert alert-warning d-flex flex-column align-items-center text-center p-3 mt-4 border-0 shadow-sm" style="border-radius: 15px; background-color: #fffbeb;">
            <i class="fa-solid fa-triangle-exclamation fa-2x mb-2 text-warning"></i>
            <h6 class="fw-bold mb-1">Profil Anda Belum Lengkap!</h6>
            <p class="small text-secondary mb-2 px-3">Harap lengkapi data RT, RW, dan Alamat Anda untuk dapat menggunakan semua fitur aplikasi.</p>
            <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-warning fw-bold shadow-sm">Lengkapi Sekarang</a>
        </div>
    @endif

    <div class="d-flex flex-column gap-3 profile-menu">
        <a href="{{ route('profile.edit') }}" class="btn-profile-menu">
            <i class="fa-solid fa-user-pen text-primary"></i>
            <span>Ubah Profil & Alamat</span>
            <i class="fa-solid fa-chevron-right ms-auto text-secondary"></i>
        </a>
        
        <a href="#" class="btn-profile-menu" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa-solid fa-right-from-bracket text-danger"></i>
            <span class="text-danger">Keluar</span>
            <i class="fa-solid fa-chevron-right ms-auto text-secondary"></i>
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
@endsection