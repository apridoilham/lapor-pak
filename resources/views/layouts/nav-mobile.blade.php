<div class="floating-button-container d-flex" onclick="window.location.href = '{{ route('report.take') }}'">
    <button class="floating-button">
        <i class="fa-solid fa-camera"></i>
    </button>
</div>
<nav class="nav-mobile">
    <a href="{{ route('home')}}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
        <i class="fas fa-house"></i>
        <span>Beranda</span>
    </a>
    <a href="{{ route('report.myreport') }}" class="{{ request()->routeIs('report.myreport*') ? 'active' : '' }}">
        <i class="fas fa-solid fa-clipboard-list"></i>
        <span>Laporanmu</span>
    </a>
    
    <a href="#" class="nav-placeholder"></a>

    <a href="{{ route('notifications.index') }}" class="nav-notification {{ request()->routeIs('notifications.index') ? 'active' : '' }}">
        @auth
            @php
                // Ambil jumlah notifikasi yang belum dibaca
                $unreadNotificationsCount = Auth::user()->unreadNotifications->count();
            @endphp
            
            {{-- Tampilkan badge hanya jika ada notifikasi belum dibaca --}}
            @if ($unreadNotificationsCount > 0)
                <span class="notification-badge">{{ $unreadNotificationsCount }}</span>
            @endif
        @endauth
        <i class="fas fa-bell"></i>
        <span>Notifikasi</span>
    </a>
    
    @auth
        <a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span>Profil</span>
        </a>
    @else
        <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'active' : '' }}">
            <i class="fas fa-right-to-bracket"></i>
            <span>Daftar</span>
        </a>
    @endauth
</nav>