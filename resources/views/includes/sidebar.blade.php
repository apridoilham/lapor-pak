<style>
    .sidebar-brand-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.5);
    }
    .sidebar-brand-text {
        text-align: left;
        line-height: 1.2;
    }
    .sidebar-brand-text .admin-name {
        font-size: 0.85rem;
        font-weight: bold;
        display: block;
    }
    .sidebar-brand-text .admin-role-badge {
        font-size: 0.65rem;
        padding: 2px 8px;
        border-radius: 6px;
        margin-top: 4px;
        font-weight: 500;
        display: inline-block;
        letter-spacing: 0.5px;
    }
    .role-badge-super-admin {
        background-color: #f6c23e;
        color: #5a5c69;
    }
    .role-badge-admin {
        background-color: rgba(255, 255, 255, 0.2);
        color: #ffffff;
    }
</style>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-start" href="{{ route('admin.dashboard') }}">
        <div class="sidebar-brand-icon">
            <img class="sidebar-brand-img" src="{{ asset('assets/admin/img/haeritage.jpeg') }}" alt="Profile">
        </div>
        <div class="sidebar-brand-text mx-3">
            <span class="admin-name">{{ Auth::user()->name }}</span>
            @role('super-admin')
                <small class="admin-role-badge role-badge-super-admin">Super Admin</small>
            @else
                @if(Auth::user()->rw)
                <small class="admin-role-badge role-badge-admin">Admin RW {{ Auth::user()->rw->number }}</small>
                @else
                <small class="admin-role-badge role-badge-admin">Admin</small>
                @endif
            @endrole
        </div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.resident.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.resident.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Data Pelapor</span></a>
    </li>

    @role('super-admin')
    <li class="nav-item {{ request()->routeIs('admin.rtrw.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.rtrw.index') }}">
            <i class="fas fa-fw fa-map-marker-alt"></i>
            <span>Data RW/RT</span></a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.report-category.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.report-category.index') }}">
            <i class="fas fa-fw fa-tags"></i>
            <span>Data Kategori</span></a>
    </li>
    @endrole

    <li class="nav-item {{ (request()->routeIs('admin.report.*') && !request()->routeIs('admin.report.export.*')) ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.report.index') }}">
            <i class="fas fa-fw fa-file-alt"></i>
            <span>Data Laporan</span></a>
    </li>

    <li class="nav-item {{ request()->routeIs('admin.report.export.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.report.export.create') }}">
            <i class="fas fa-fw fa-download"></i>
            <span>Ekspor Laporan</span></a>
    </li>

    @role('super-admin')
    <li class="nav-item {{ request()->routeIs('admin.admin-user.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.admin-user.index') }}">
            <i class="fas fa-fw fa-user-shield"></i>
            <span>Manajemen Admin</span></a>
    </li>
    @endrole
</ul>