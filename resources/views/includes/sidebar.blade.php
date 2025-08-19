<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-bullhorn"></i>
        </div>
        <div class="sidebar-brand-text mx-3">BSB Lapor</div>
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Manajemen Data</div>
    <li class="nav-item {{ request()->routeIs('admin.resident.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.resident.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Data Pelapor</span></a>
    </li>
    <li class="nav-item {{ (request()->routeIs('admin.report.*') && !request()->routeIs('admin.report.export.*')) ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.report.index') }}">
            <i class="fas fa-fw fa-file-alt"></i>
            <span>Data Laporan</span></a>
    </li>
    @role('super-admin')
    <li class="nav-item {{ request()->routeIs('admin.rtrw.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.rtrw.index') }}">
            <i class="fas fa-fw fa-map-signs"></i>
            <span>Data RW/RT</span></a>
    </li>
    <li class="nav-item {{ request()->routeIs('admin.report-category.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.report-category.index') }}">
            <i class="fas fa-fw fa-tags"></i>
            <span>Data Kategori</span></a>
    </li>
    @endrole
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Lainnya</div>
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
    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>