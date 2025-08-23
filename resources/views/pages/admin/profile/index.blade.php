@extends('layouts.admin')

@section('title', 'Profil Saya')

@push('styles')
<style>
    .profile-card {
        border-radius: .75rem;
        transition: all 0.3s ease;
    }
    .profile-card .profile-avatar-wrapper {
        position: relative;
    }
    .profile-card .profile-avatar {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #fff;
        box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,.1);
    }
    .profile-card .card-body {
        padding: 2rem;
    }
    .info-list-item {
        display: flex;
        align-items: center;
        padding: 1.1rem 0;
        border-bottom: 1px solid #eaecf4;
        transition: background-color 0.2s ease-in-out;
    }
    .info-list-item:hover {
        background-color: #f8f9fc;
    }
    .info-list-item:last-child {
        border-bottom: none;
    }
    .info-list-item .info-label {
        width: 200px;
        font-weight: 600;
        color: #858796;
        flex-shrink: 0;
    }
    .info-list-item .info-value {
        font-weight: 500;
        color: #5a5c69;
    }
    .btn-edit-profile {
        font-weight: bold;
    }
</style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-900 font-weight-bold">Profil Akun</h1>
        <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary shadow-sm btn-edit-profile">
            <i class="fas fa-pencil-alt fa-sm mr-2"></i>Ubah Profil & Password
        </a>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow-sm profile-card mb-4 text-center">
                <div class="card-body">
                    <div class="profile-avatar-wrapper">
                        <img class="profile-avatar"
                             src="{{ Auth::user()->avatar ? Auth::user()->avatar : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=4e73df&color=fff&size=130&font-size=0.4' }}"
                             alt="Foto Profil {{ Auth::user()->name }}">
                    </div>
                    <h4 class="font-weight-bold text-gray-800 mt-3 mb-1">{{ $user->name }}</h4>
                    <p class="text-primary font-weight-bold">Admin</p>
                    @if ($user->rw)
                        <span class="badge badge-pill badge-light text-gray-600 border px-3 py-2 mt-2">
                            <i class="fas fa-map-marker-alt fa-sm mr-1"></i>
                            Wilayah RW {{ $user->rw->number }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm profile-card mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Akun</h6>
                </div>
                <div class="card-body px-4">
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
                            <span class="info-label">Peran</span>
                            <span class="info-value">Admin</span>
                        </div>
                        <div class="info-list-item">
                            <span class="info-label">Bergabung Sejak</span>
                            <span class="info-value">{{ $user->created_at->isoFormat('dddd, D MMMM YYYY') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection