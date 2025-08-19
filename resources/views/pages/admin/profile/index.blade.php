@extends('layouts.admin')

@section('title', 'Profil Saya')

@push('styles')
<style>
    .profile-card .profile-avatar {
        width: 120px;
        height: 120px;
        border: 4px solid #fff;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
    }
    .info-list-item {
        display: flex;
        padding: 1.25rem 0;
        border-bottom: 1px solid #e3e6f0;
    }
    .info-list-item:first-child {
        padding-top: 0.5rem;
    }
    .info-list-item:last-child {
        border-bottom: none;
        padding-bottom: 0.5rem;
    }
    .info-list-item .info-label {
        width: 200px;
        font-weight: 600;
        color: #5a5c69;
        flex-shrink: 0;
    }
    .info-list-item .info-value {
        color: #2c3e50;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
    <h1 class="h3 mb-4 text-gray-900 font-weight-bold">Profil Akun</h1>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4 text-center">
                <div class="card-body profile-card">
                    <img class="img-profile rounded-circle profile-avatar mb-3"
                         src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=1a202c&color=fff&size=128&font-size=0.33">
                    <h4 class="font-weight-bold text-gray-800">{{ $user->name }}</h4>
                    <p class="text-muted text-capitalize">{{ $user->getRoleNames()->first() }}</p>
                    @if ($user->rw)
                        <span class="badge badge-pill badge-primary px-3 py-2" style="font-size: 0.8rem;">Admin RW {{ $user->rw->number }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Akun</h6>
                    <a href="{{ route('admin.profile.edit') }}" class="btn btn-sm btn-primary shadow-sm">
                        <i class="fas fa-pencil-alt fa-sm mr-1"></i> Ubah Profil & Password
                    </a>
                </div>
                <div class="card-body">
                    <div class="info-list">
                        <div class="info-list-item">
                            <span class="info-label">Nama Lengkap</span>
                            <span class="info-value">{{ $user->name }}</span>
                        </div>
                        <div class="info-list-item">
                            <span class="info-label">Alamat Email</span>
                            <span class="info-value">{{ $user->email }}</span>
                        </div>
                        <div class="info-list-item">
                            <span class="info-label">Peran (Role)</span>
                            <span class="info-value text-capitalize">{{ $user->getRoleNames()->first() }}</span>
                        </div>
                        @if ($user->rw)
                        <div class="info-list-item">
                            <span class="info-label">Wilayah Tugas</span>
                            <span class="info-value">RW {{ $user->rw->number }}</span>
                        </div>
                        @endif
                        <div class="info-list-item">
                            <span class="info-label">Tanggal Bergabung</span>
                            <span class="info-value">{{ $user->created_at->isoFormat('dddd, D MMMM YYYY') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection