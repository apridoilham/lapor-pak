@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
    <div class="header-nav mb-4">
        <a href="{{ url()->previous() }}" class="text-decoration-none">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1>Notifikasi</h1>
    </div>

    <div class="notification-list">
        @forelse ($notifications as $notification)
            {{-- Link sekarang mengarah ke route 'notifications.read' --}}
            <a href="{{ route('notifications.read', $notification->id) }}" class="notification-item text-decoration-none {{ !$notification->read_at ? 'unread' : '' }}">
                <div class="notification-icon">
                    <i class="fa-solid fa-file-alt"></i>
                </div>
                <div class="notification-content">
                    <p class="mb-1 fw-bold">{{ $notification->data['title'] }}</p>
                    <p class="text-secondary mb-1">{{ $notification->data['message'] }}</p>
                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                </div>
            </a>
        @empty
            <div class="text-center py-5 mt-5">
                <i class="fa-solid fa-bell-slash fa-3x text-secondary mb-3"></i>
                <p class="text-secondary">Belum ada notifikasi.</p>
            </div>
        @endforelse
    </div>

    {{-- Link Paginasi --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $notifications->links() }}
    </div>
@endsection