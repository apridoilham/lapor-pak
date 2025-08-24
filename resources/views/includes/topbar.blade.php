@push('styles')
<style>
    /* Custom Topbar & Dropdown Styling */
    .topbar {
        height: 70px;
    }
    .img-profile {
        height: 40px;
        width: 40px;
        object-fit: cover;
    }
    .dropdown-menu {
        border: 1px solid #eaecf4;
        border-radius: 0.75rem;
        box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.1) !important;
        padding: 0.5rem;
        min-width: 16rem;
    }
    .dropdown-header.user-info-header {
        background-color: #f8f9fc;
        border-radius: 0.5rem;
        margin: 0.5rem;
        padding: 1rem;
    }
    .dropdown-header .img-profile {
        height: 45px;
        width: 45px;
    }
    .dropdown-item {
        padding: 0.75rem 1.25rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease-in-out;
        font-weight: 500;
        color: #5a5c69;
    }
    .dropdown-item i {
        color: #b7b9cc;
        transition: color 0.2s ease-in-out;
    }
    .dropdown-item:hover {
        background-color: #f8f9fc;
        color: #4e73df;
    }
    .dropdown-item:hover i {
        color: #4e73df;
    }
    .dropdown-item.logout-item:hover {
        background-color: #fcebeb;
        color: #e74a3b;
    }
    .dropdown-item.logout-item:hover i {
        color: #e74a3b;
    }
    .dropdown-divider {
        margin: 0.5rem 0;
    }
</style>
@endpush

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow-sm">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <ul class="navbar-nav ml-auto">
        <div class="topbar-divider d-none d-sm-block"></div>
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-3 d-none d-lg-inline text-gray-800 font-weight-bold">{{ Auth::user()->name }}</span>
                @php
                    $avatarUrl = Auth::user()->avatar;
                    if ($avatarUrl && !filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
                        $avatarUrl = asset('storage/' . $avatarUrl);
                    } elseif (empty($avatarUrl)) {
                        $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=4e73df&color=fff&size=60';
                    }
                @endphp
                <img class="img-profile rounded-circle" src="{{ $avatarUrl }}" alt="Foto Profil">
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <div class="dropdown-header user-info-header d-flex align-items-center px-3 py-2">
                    <img class="img-profile rounded-circle mr-3" width="40" src="{{ $avatarUrl }}" alt="Foto Profil">
                    <div>
                        <h6 class="font-weight-bold mb-0 text-gray-800">{{ Auth::user()->name }}</h6>
                        <small class="text-muted">Admin</small>
                    </div>
                </div>
                <div class="dropdown-divider my-0"></div>
                <a class="dropdown-item" href="{{ route('admin.profile.index') }}">
                    <i class="fas fa-user-cog fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profil
                </a>
                <a class="dropdown-item" href="{{ route('admin.activity-log.index') }}">
                    <i class="fas fa-list-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Log Aktivitas
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item logout-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i>
                    Keluar
                </a>
            </div>
        </li>
    </ul>
</nav>

<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Keluar</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">Apakah Anda yakin ingin mengakhiri sesi Anda saat ini?</div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Batal</button>
                <a class="btn btn-primary" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Keluar</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </div>
        </div>
    </div>
</div>