<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <li class="nav-item {{ request()->is('admin/resident*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.resident.index') }}">
            <i class="fas fa-fw fa-table"></i>
            <span>Data Masyarakat</span></a>
    </li>

    <li class="nav-item {{ request()->is('admin/report-category*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.report-category.index') }}">
            <i class="fas fa-fw fa-table"></i>
            <span>Data kategori</span></a>
    </li>

    <li class="nav-item {{ request()->is('admin/report*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.report.index') }}">
            <i class="fas fa-fw fa-table"></i>
            <span>Data Laporan</span></a>
    </li>

    {{-- Menu ini hanya akan tampil jika user adalah super-admin --}}
    @role('super-admin')
    <li class="nav-item {{ request()->is('admin/admin-user*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.admin-user.index') }}">
            <i class="fas fa-fw fa-user-shield"></i>
            <span>Manajemen Admin</span></a>
    </li>
    @endrole
</ul>