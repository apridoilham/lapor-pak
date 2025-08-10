@extends('layouts.app')

@section('title', 'Notifikasi')

@push('styles')
<style>
    .notification-item-container {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
    }
    .notification-link-main {
        flex-grow: 1;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        text-decoration: none;
        color: inherit;
    }
    .delete-notification-form button {
        margin-top: 0.25rem;
    }
</style>
@endpush

@section('content')
    @include('sweetalert::alert')
    <div class="header-nav mb-4">
        <a href="{{ url()->previous() }}" class="text-decoration-none">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1>Notifikasi</h1>
    </div>

    <div class="notification-list">
        @forelse ($notifications as $notification)
            <div class="notification-item-container notification-item {{ !$notification->read_at ? 'unread' : '' }}">
                <a href="{{ route('notifications.read', $notification->id) }}" class="notification-link-main">
                    <div class="notification-icon">
                        <i class="fa-solid fa-file-alt"></i>
                    </div>
                    <div class="notification-content">
                        <p class="mb-1 fw-bold">{{ $notification->data['title'] }}</p>
                        
                        <p class="text-secondary mb-1">
                            @php
                                $message = $notification->data['message'];
                                $statusEnum = \App\Enums\ReportStatusEnum::tryFrom($message);
                            @endphp

                            @if ($statusEnum)
                                Status terbaru: {{ $statusEnum->label() }}
                            @else
                                {{ $message }}
                            @endif
                        </p>
                        
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                </a>
                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="delete-notification-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-close btn-sm" aria-label="Hapus notifikasi"></button>
                </form>
            </div>
        @empty
            <div class="text-center py-5 mt-5">
                <i class="fa-solid fa-bell-slash fa-3x text-secondary mb-3"></i>
                <p class="text-secondary">Belum ada notifikasi.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $notifications->links() }}
    </div>
@endsection