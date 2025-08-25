@extends('layouts.app')

@section('title', 'Notifikasi')

@push('styles')
<style>
    :root {
        --primary-color: #10B981;
        --text-dark: #111827;
        --text-light: #6B7280;
        --bg-body: #F9FAFB;
        --bg-white: #FFFFFF;
        --border-color: #e5e7eb;
        --blue-color: #3B82F6;
        --font-sans: 'Inter', 'Poppins', 'Segoe UI', sans-serif;
    }
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    
    .main-content { padding: 0; }
    .page-header { background-color: var(--bg-white); padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
    .page-header h3 { font-weight: 800; font-size: 2rem; color: var(--text-dark); margin: 0; }
    .page-header .btn-select { font-size: 0.9rem; font-weight: 600; color: var(--primary-color); background: transparent; border: none; padding: 0.5rem; }
    .filter-tabs { display: flex; gap: 0.75rem; margin-top: 1.5rem; }
    .filter-tabs .tab-item { flex-grow: 1; text-align: center; padding: 0.6rem 1rem; border-radius: 20px; font-size: 0.9rem; font-weight: 600; color: var(--text-light); text-decoration: none; background-color: var(--bg-body); border: 1px solid var(--border-color); transition: all 0.2s ease; }
    .filter-tabs .tab-item.active { background-color: var(--primary-color); color: var(--bg-white); border-color: var(--primary-color); box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2); }
    .notifications-container { padding: 1.5rem; padding-bottom: 100px; }
    .notification-group-title { font-size: 0.85rem; font-weight: 600; color: var(--text-light); text-transform: uppercase; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color); }
    .notification-item { display: flex; align-items: flex-start; gap: 1rem; text-decoration: none; color: inherit; padding: 1rem 0; transition: background-color 0.2s; }
    .notification-item:not(:last-child) { border-bottom: 1px solid var(--border-color); }
    .selection-mode .notification-item .item-link { pointer-events: none; }
    .notification-item .checkbox-container { display: none; margin-top: 12px; transition: all 0.2s; }
    .selection-mode .notification-item .checkbox-container { display: block; }
    .notification-item .form-check-input { border-radius: 50%; width: 1.25em; height: 1.25em; }
    .notification-item .icon-container { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.2rem; }
    .icon-container.comment { background-color: #E0F2FE; color: #0284C7; }
    .icon-container.status { background-color: #F0FDF4; color: #16A34A; }
    .icon-container.deleted { background-color: #FEF2F2; color: #EF4444; }
    .notification-item .content { flex-grow: 1; }
    .notification-item .content p { margin: 0; font-size: 0.95rem; color: var(--text-dark); line-height: 1.5; }
    .notification-item .content .time { font-size: 0.8rem; color: var(--text-light); margin-top: 0.25rem; }
    .notification-item .unread-dot { width: 10px; height: 10px; background-color: var(--blue-color); border-radius: 50%; flex-shrink: 0; margin-top: 5px; }
    .bulk-action-bar { display: none; background-color: var(--bg-white); padding: 0.75rem 1.5rem; border-bottom: 1px solid var(--border-color); }
    .selection-mode .filter-tabs { display: none; }
    .selection-mode .bulk-action-bar { display: flex; justify-content: space-between; align-items: center; }
    .bulk-action-bar button { font-size: 0.85rem; font-weight: 600; padding: 0.5rem 1rem; border-radius: 8px; }
    .bulk-action-bar button:disabled { opacity: 0.5; cursor: not-allowed; }
    .empty-state { text-align: center; padding: 3rem 1rem; }
    .empty-state h5 { font-weight: 700; color: var(--text-dark); margin-top: 1rem; }
    .empty-state p { color: var(--text-light); max-width: 300px; margin: 0.5rem auto 0; }
</style>
@endpush

@section('content')
    @php
        $filter = request('filter');
        $notificationsForView = $notifications;
        if ($filter === 'unread') {
            $notificationsForView = $notifications->where('read_at', null);
        }
        $groupedNotifications = $notificationsForView->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
        });
        $today = now()->format('Y-m-d'); $yesterday = now()->subDay()->format('Y-m-d');
        $finalGroups = [];
        foreach ($groupedNotifications as $date => $group) {
            if ($date === $today) { $finalGroups['Hari Ini'] = $group; }
            elseif ($date === $yesterday) { $finalGroups['Kemarin'] = $group; }
            else { $finalGroups[\Carbon\Carbon::parse($date)->isoFormat('D MMMM YYYY')] = $group; }
        }
    @endphp
    <div id="notification-page-container">
        <div class="page-header">
            <h3>Notifikasi</h3>
            <button class="btn-select" id="select-mode-toggle">Pilih</button>
        </div>
        <div style="padding: 0 1.5rem;">
            <div class="filter-tabs">
                <a href="{{ route('notifications.index') }}" class="tab-item {{ !$filter ? 'active' : '' }}">Semua</a>
                <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="tab-item {{ $filter === 'unread' ? 'active' : '' }}">Belum Dibaca</a>
            </div>
        </div>
        <div class="bulk-action-bar">
            <button class="btn btn-outline-secondary btn-sm" id="select-all-btn">Pilih Semua</button>
            <div>
                <button class="btn btn-outline-primary btn-sm me-2" id="read-selected-btn" disabled>Tandai Dibaca</button>
                <button class="btn btn-outline-danger btn-sm" id="delete-selected-btn" disabled>Hapus</button>
            </div>
        </div>
        <div class="notifications-container">
            @forelse($finalGroups as $date => $group)
                <div class="notification-group">
                    <h6 class="notification-group-title">{{ $date }}</h6>
                    @foreach($group as $notification)
                        @php
                            $actionUser = isset($notification->data['action_by_user_id']) ? \App\Models\User::find($notification->data['action_by_user_id']) : null;
                            $iconClass = 'status';
                            $icon = 'fa-solid fa-file-circle-check';
                            $message = $notification->data['message'] ?? '';

                            if ($notification->data['type'] === 'new_comment') {
                                $iconClass = 'comment';
                                $icon = 'fa-solid fa-comment-dots';
                                $message = '<strong>' . ($actionUser ? $actionUser->censored_name : 'Seseorang') . '</strong> mengomentari laporan Anda.';
                            } elseif ($notification->data['type'] === 'progress_deleted') {
                                $iconClass = 'deleted';
                                $icon = 'fa-solid fa-trash-can';
                            }
                        @endphp
                        <div class="notification-item" data-id="{{ $notification->id }}">
                            <div class="checkbox-container">
                                <input class="form-check-input notification-checkbox" type="checkbox" value="{{ $notification->id }}">
                            </div>
                            <a href="{{ route('notifications.read', $notification->id) }}" class="item-link d-flex align-items-start gap-3 text-decoration-none flex-grow-1">
                                <div class="icon-container {{ $iconClass }}"><i class="{{ $icon }}"></i></div>
                                <div class="content"><p>{!! $message !!}</p><p class="time">{{ $notification->created_at->diffForHumans() }}</p></div>
                                @if(!$notification->read_at)<div class="unread-dot"></div>@endif
                            </a>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="empty-state">
                    <div id="lottie-empty" style="width: 250px; height: 250px; margin: 0 auto;"></div>
                    <h5>Tidak Ada Notifikasi</h5>
                    <p>@if($filter === 'unread') Anda sudah membaca semua notifikasi. @else Saat ini belum ada notifikasi untuk Anda. @endif</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lottieContainer = document.getElementById('lottie-empty');
        if (lottieContainer) {
            lottie.loadAnimation({
                container: lottieContainer,
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: '{{ asset('assets/app/lottie/empty-notification.json') }}'
            });
        }

        const container = document.getElementById('notification-page-container');
        const toggleButton = document.getElementById('select-mode-toggle');
        const selectAllButton = document.getElementById('select-all-btn');
        const readButton = document.getElementById('read-selected-btn');
        const deleteButton = document.getElementById('delete-selected-btn');
        const checkboxes = document.querySelectorAll('.notification-checkbox');

        let isSelectionMode = false;

        function updateButtonStates() {
            const selectedCheckboxes = document.querySelectorAll('.notification-checkbox:checked');
            const hasSelection = selectedCheckboxes.length > 0;
            readButton.disabled = !hasSelection;
            deleteButton.disabled = !hasSelection;

            if (checkboxes.length > 0 && selectedCheckboxes.length === checkboxes.length) {
                selectAllButton.textContent = 'Batal Pilih Semua';
            } else {
                selectAllButton.textContent = 'Pilih Semua';
            }
        }

        function toggleSelectionMode() {
            isSelectionMode = !isSelectionMode;
            container.classList.toggle('selection-mode', isSelectionMode);
            toggleButton.textContent = isSelectionMode ? 'Batal' : 'Pilih';
            if (!isSelectionMode) {
                checkboxes.forEach(cb => cb.checked = false);
                updateButtonStates();
            }
        }

        function handleSelectAll() {
            const allSelected = document.querySelectorAll('.notification-checkbox:checked').length === checkboxes.length;
            checkboxes.forEach(cb => cb.checked = !allSelected);
            updateButtonStates();
        }

        async function performBulkAction(url, method) {
            const selectedIds = Array.from(document.querySelectorAll('.notification-checkbox:checked')).map(cb => cb.value);
            if (selectedIds.length === 0) return;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids: selectedIds })
                });

                const result = await response.json();

                if (response.ok) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: result.message, timer: 1500, showConfirmButton: false })
                        .then(() => window.location.reload());
                } else {
                    throw new Error(result.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: error.message });
            }
        }

        if (toggleButton) {
            toggleButton.addEventListener('click', toggleSelectionMode);
        }
        if (selectAllButton) {
            selectAllButton.addEventListener('click', handleSelectAll);
        }
        if (readButton) {
            readButton.addEventListener('click', () => performBulkAction('{{ route("notifications.read.selected") }}'));
        }
        if (deleteButton) {
            deleteButton.addEventListener('click', () => performBulkAction('{{ route("notifications.delete.selected") }}'));
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateButtonStates);
        });
    });
</script>
@endpush