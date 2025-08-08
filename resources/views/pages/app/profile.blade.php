@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="d-flex flex-column justify-content-center align-items-center gap-2 mt-4">
        @php
            $resident = Auth::user()->resident;
        @endphp

        @if ($resident && $resident->avatar)
            <img src="{{ asset('storage/' . $resident->avatar) }}" alt="avatar" class="avatar">
        @else
            <img src="{{ asset('assets/app/images/default-avatar.png') }}" alt="avatar" class="avatar">
        @endif

        <h5 class="mt-2">{{ Auth::user()->name }}</h5>
        <p class="text-secondary">{{ Auth::user()->email }}</p>
    </div>

    <div class="row mt-4">
        <div class="col-4">
            <div class="card profile-stats">
                <div class="card-body">
                    <h5 class="card-title">{{ $activeReportsCount }}</h5>
                    <p class="card-text">Aktif</p>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="card profile-stats">
                <div class="card-body">
                    <h5 class="card-title">{{ $completedReportsCount }}</h5>
                    <p class="card-text">Selesai</p>
                </div>
            </div>
        </div>
        
        <div class="col-4">
            <div class="card profile-stats">
                <div class="card-body">
                    <h5 class="card-title">{{ $rejectedReportsCount }}</h5>
                    <p class="card-text">Ditolak</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
        <button class="btn btn-outline-danger w-100 rounded-pill" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Keluar
        </button>
    </div>
@endsection