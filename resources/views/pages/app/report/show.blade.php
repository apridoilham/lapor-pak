@extends('layouts.app')

@section('title', 'Detail Laporan ' . $report->code)

@push('styles')
<style>
    :root {
        --primary-color: #10B981;
        --primary-gradient: linear-gradient(135deg, #10B981 0%, #34D399 100%);
        --text-dark: #111827;
        --text-light: #6B7280;
        --bg-body: #F9FAFB;
        --bg-white: #FFFFFF;
        --border-color: #e5e7eb;
        --font-sans: 'Inter', 'Poppins', 'Segoe UI', sans-serif;
    }

    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    html, body { background-color: var(--bg-body); }
    body {
        font-family: var(--font-sans);
        max-width: 480px;
        margin: 0 auto;
        min-height: 100vh;
        box-shadow: 0 0 30px rgba(0, 0, 0, 0.05);
        background-color: var(--bg-white);
    }
    .main-content { padding: 0; padding-bottom: 80px; }
    .hero-container { position: relative; }
    .hero-image {
        width: 100%;
        height: auto;
        display: block;
        cursor: pointer;
    }
    .hero-gradient-overlay { position: absolute; bottom: 0; left: 0; right: 0; height: 150px; background: linear-gradient(180deg, rgba(249, 250, 251, 0) 0%, var(--bg-body) 100%); }
    .hero-overlay-header { position: absolute; top: 0; left: 0; right: 0; display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; }
    .overlay-button { background-color: rgba(30, 30, 30, 0.5); color: white; width: 44px; height: 44px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; font-size: 1.1rem; backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.2); transition: all 0.2s ease; }
    .content-container { padding: 0 1.5rem; margin-top: -50px; position: relative; z-index: 10; }
    .report-title { font-weight: 800; font-size: 1.75rem; color: var(--text-dark); margin-bottom: 1.5rem; line-height: 1.3; background: var(--bg-white); padding: 1.5rem; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.07); }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem; }
    .info-grid.three-items { grid-template-columns: repeat(3, 1fr); }
    .info-grid.three-items .info-card .info-label { font-size: 0.75rem; }
    .info-grid.three-items .info-card .info-value { font-size: 0.85rem; word-break: break-all; }
    .info-card { background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 16px; padding: 1rem; box-shadow: 0 4px 15px rgba(0,0,0,0.04); display: flex; flex-direction: column; justify-content: space-between; }
    .info-card .info-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 0.75rem; }
    .info-card .info-label { font-size: 0.8rem; color: var(--text-light); margin-bottom: 0.25rem; }
    .info-card .info-value { font-weight: 600; color: var(--text-dark); font-size: 0.95rem; }
    .icon-status { background-color: #F0FDF4; color: #10B981; } .icon-category { background-color: #EFF6FF; color: #3B82F6; } .icon-reporter { background-color: #FEF3C7; color: #D97706; } .icon-date { background-color: #F3E8FF; color: #9333EA; }
    .section { margin-bottom: 2.5rem; }
    .section-title { font-weight: 700; font-size: 1.25rem; color: var(--text-dark); margin-bottom: 1rem; }
    .report-description-text { color: var(--text-light); line-height: 1.7; font-size: 0.95rem; white-space: pre-wrap; }
    .timeline-block { background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 18px; margin-bottom: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.04); overflow: hidden; }
    .timeline-header { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); }
    .timeline-header h6 { margin: 0; font-weight: 600; font-size: 0.95rem; }
    .header-delivered { background-color: #EFF6FF; color: #3B82F6; } .header-in_process { background-color: #FFFBEB; color: #D97706; } .header-completed { background-color: #F0FDF4; color: #10B981; } .header-rejected { background-color: #FEF2F2; color: #EF4444; }
    .timeline-body { padding: 1rem; }
    .timeline-body .description { font-size: 0.9rem; color: var(--text-light); line-height: 1.6; margin-bottom: 1rem; }
    .proof-image-container { position: relative; display: inline-block; margin-bottom: 1rem; }
    .timeline-body .proof-image { display: block; max-width: 150px; height: auto; border-radius: 12px; cursor: pointer; border: 1px solid var(--border-color); }
    .view-image-overlay { position: absolute; bottom: 8px; right: 8px; background-color: rgba(0, 0, 0, 0.6); color: white; padding: 0.3rem 0.6rem; border-radius: 8px; font-size: 0.75rem; font-weight: 500; display: flex; align-items: center; gap: 0.3rem; pointer-events: none; }
    .timeline-body .date { font-size: 0.8rem; font-weight: 500; color: var(--text-light); text-align: right; }
    .avatar-placeholder { width: 40px; height: 40px; border-radius: 50%; background-color: var(--border-color); display: flex; align-items: center; justify-content: center; color: var(--text-light); flex-shrink: 0; }
    .comment-form-container { display: flex; align-items: flex-start; gap: 0.75rem; margin-top: 1.5rem; }
    .comment-form-container .avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
    .comment-form-container .input-wrapper { flex-grow: 1; position: relative; }
    .comment-form-container textarea { width: 100%; border-radius: 20px; border: 1px solid var(--border-color); background-color: var(--bg-body); padding: 0.75rem 3.5rem 0.75rem 1rem; resize: none; transition: all 0.2s ease; }
    .comment-form-container textarea:focus { background-color: var(--bg-white); border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); }
    .comment-form-container .btn-send-comment { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); width: 38px; height: 38px; border-radius: 50%; border: none; background: var(--primary-color); color: white; font-size: 1rem; transition: all 0.2s ease; }
    .comment-item { display: flex; gap: 0.75rem; margin-bottom: 1.5rem; }
    .comment-avatar { width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0; object-fit: cover; }
    .comment-content { flex-grow: 1; }
    .comment-bubble { padding: 0.75rem 1rem 0.5rem; border-radius: 18px; line-height: 1.6; font-size: 0.9rem; position: relative; }
    .comment-item.is-other .comment-bubble { background-color: #F3F4F6; color: var(--text-dark); border-top-left-radius: 4px; }
    .comment-item.is-owner { flex-direction: row-reverse; }
    .comment-item.is-owner .comment-bubble { background: var(--primary-gradient); color: white; border-top-right-radius: 4px; }
    .comment-bubble .comment-author { font-weight: 600; font-size: 0.9rem; }
    .comment-item.is-other .comment-author { color: var(--text-dark); }
    .comment-item.is-owner .comment-author { color: white; opacity: 0.9;}
    .comment-bubble .comment-body { margin: 0.25rem 0 1rem; white-space: pre-wrap; word-wrap: break-word; }
    .comment-meta-wrapper { position: absolute; bottom: 6px; right: 12px; }
    .comment-meta { font-size: 0.75rem; }
    .comment-item.is-other .comment-meta { color: var(--text-light); }
    .comment-item.is-owner .comment-meta { color: rgba(255, 255, 255, 0.8); }
    .lightbox-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.85); display: flex; align-items: center; justify-content: center; z-index: 9999; opacity: 0; visibility: hidden; transition: opacity 0.3s ease; backdrop-filter: blur(5px); }
    .lightbox-overlay.show { opacity: 1; visibility: visible; }
    .lightbox-content img { max-width: 90vw; max-height: 90vh; object-fit: contain; border-radius: 8px; }
    .lightbox-close-btn { position: absolute; top: 20px; right: 30px; color: white; font-size: 2.5rem; border: none; background: transparent; cursor: pointer; }
</style>
@endpush

@section('content')
    <div class="page-container">
        <div class="hero-container">
            <div class="hero-overlay-header">
                <a href="{{ request()->query('_ref', route('home')) }}" class="overlay-button"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}" class="hero-image">
            <div class="hero-gradient-overlay"></div>
        </div>
        <div class="content-container">
            <h1 class="report-title">{{ $report->title }}</h1>
            <div class="info-grid {{ !$isReportOwner ? 'three-items' : '' }}">
                @if ($isReportOwner)
                <div class="info-card">
                    <div class="info-icon icon-status"><i class="fa-solid fa-flag"></i></div>
                    <div>
                        <p class="info-label">Status</p>
                        <h6 class="info-value">{{ $report->latestStatus ? $report->latestStatus->status->label() : 'Baru' }}</h6>
                    </div>
                </div>
                @endif
                <div class="info-card">
                    <div class="info-icon icon-reporter"><i class="fa-solid fa-user"></i></div>
                    <div>
                        <p class="info-label">Pelapor</p>
                        <h6 class="info-value">{{ $isReportOwner ? $report->resident->user->name : $report->resident->user->censored_name }}</h6>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-icon icon-category"><i class="fa-solid fa-tag"></i></div>
                    <div>
                        <p class="info-label">Kategori</p>
                        <h6 class="info-value">{{ $report->reportCategory->name }}</h6>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-icon icon-date"><i class="fa-solid fa-calendar"></i></div>
                    <div>
                        <p class="info-label">Tanggal</p>
                        <h6 class="info-value">{{ $report->created_at->isoFormat('D MMM YYYY') }}</h6>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h5 class="section-title">Lokasi Kejadian</h5>
                <p class="report-description-text">{{ $report->address }}</p>
            </div>

            <div class="section">
                <h5 class="section-title">Detail Laporan</h5>
                <p class="report-description-text">{{ $report->description }}</p>
            </div>

            @if ($isReportOwner)
            <div class="section" id="riwayat-perkembangan">
                <h5 class="section-title">Riwayat Perkembangan</h5>
                <div class="timeline">
                    @forelse ($report->reportStatuses->sortBy('created_at') as $status)
                        <div class="timeline-block">
                            <div class="timeline-header header-{{$status->status->value}}">
                                @php $icon = match($status->status) { \App\Enums\ReportStatusEnum::DELIVERED => 'fa-paper-plane', \App\Enums\ReportStatusEnum::IN_PROCESS => 'fa-spinner', \App\Enums\ReportStatusEnum::COMPLETED => 'fa-check-double', \App\Enums\ReportStatusEnum::REJECTED => 'fa-xmark' }; @endphp
                                <h6 class="m-0"><i class="fa-solid {{ $icon }} me-2"></i>{{ $status->status->label() }}</h6>
                            </div>
                            <div class="timeline-body">
                                <p class="description">{{ $status->description }}</p>
                                @if($status->image)
                                <div class="proof-image-container">
                                    <img src="{{ asset('storage/' . $status->image) }}" class="proof-image" alt="Bukti Progress">
                                    <div class="view-image-overlay">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                                        <span>Lihat</span>
                                    </div>
                                </div>
                                @endif
                                <p class="date"><i class="fa-solid fa-clock fa-xs me-1"></i> {{ $status->created_at->isoFormat('D MMMM YYYY, HH:mm') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-secondary">Belum ada riwayat perkembangan.</p>
                    @endforelse
                </div>
            </div>
            @endif

            @if ($report->visibility !== \App\Enums\ReportVisibilityEnum::PRIVATE)
                <div class="section" id="komentar">
                    <h5 class="section-title">Diskusi & Komentar (<span id="comment-count">{{ $report->comments->count() }}</span>)</h5>
                    @can('create', [\App\Models\Comment::class, $report])
                        <form action="{{ route('report.comments.store', $report) }}" method="POST" class="comment-form-container" id="comment-form">
                            @csrf
                            @php
                                $currentUserAvatar = Auth::user()->avatar ?? optional(Auth::user()->resident)->avatar;
                                if ($currentUserAvatar && !filter_var($currentUserAvatar, FILTER_VALIDATE_URL)) {
                                    $currentUserAvatar = asset('storage/' . $currentUserAvatar);
                                } elseif (!$currentUserAvatar) {
                                    $currentUserAvatar = 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=10B981&color=fff';
                                }
                            @endphp
                            <img src="{{ $currentUserAvatar }}" alt="avatar" class="avatar">
                            <div class="input-wrapper">
                                <textarea name="body" id="comment-body" class="form-control" rows="1" placeholder="Tulis komentar..." required></textarea>
                                <button type="submit" class="btn-send-comment" id="comment-send-btn"><i class="fa-solid fa-paper-plane"></i></button>
                            </div>
                        </form>
                    @endcan
                    <div class="comments-list mt-4" id="comments-list">
                        @forelse($report->comments as $comment)
                            @php 
                                $isCommentOwner = auth()->check() && auth()->id() === $comment->user_id;
                                $commenterAvatar = $comment->user->avatar ?? optional($comment->user->resident)->avatar;
                                if ($commenterAvatar && !filter_var($commenterAvatar, FILTER_VALIDATE_URL)) {
                                    $commenterAvatar = asset('storage/' . $commenterAvatar);
                                }
                            @endphp
                            <div class="comment-item {{ $isCommentOwner ? 'is-owner' : 'is-other' }}">
                                @if($commenterAvatar)
                                    <img src="{{ $commenterAvatar }}" alt="avatar" class="comment-avatar">
                                @else
                                    <div class="avatar-placeholder"><i class="fa-solid fa-user"></i></div>
                                @endif
                                <div class="comment-content">
                                    <div class="comment-bubble">
                                        <p class="comment-author">
                                            {{ $isCommentOwner ? 'Anda' : ($isReportOwner ? $comment->user->name : $comment->user->censored_name) }}
                                        </p>
                                        <p class="comment-body">{{ $comment->body }}</p>
                                        <div class="comment-meta-wrapper">
                                            <span class="comment-meta">{{ $comment->created_at->format('H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-secondary small py-4" id="no-comment-message">Jadilah yang pertama berkomentar di laporan ini.</p>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="lightbox-overlay" id="lightbox">
        <button class="lightbox-close-btn" id="lightbox-close">&times;</button>
        <div class="lightbox-content"><img src="" alt="Gambar Laporan" id="lightbox-image"></div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const lightbox = document.getElementById('lightbox');
            if(lightbox) {
                const allImages = document.querySelectorAll('.hero-image, .proof-image');
                const lightboxImage = document.getElementById('lightbox-image');
                const lightboxClose = document.getElementById('lightbox-close');
                allImages.forEach(image => {
                    image.addEventListener('click', function() {
                        lightboxImage.src = this.src;
                        lightbox.classList.add('show');
                    });
                });
                const closeLightbox = () => lightbox.classList.remove('show');
                lightboxClose.addEventListener('click', closeLightbox);
                lightbox.addEventListener('click', (e) => { if (e.target === lightbox) closeLightbox(); });
                document.addEventListener('keydown', (e) => { if (e.key === "Escape" && lightbox.classList.contains('show')) closeLightbox(); });
            }

            const commentForm = document.getElementById('comment-form');
            if (commentForm) {
                const commentBody = document.getElementById('comment-body');
                const sendButton = document.getElementById('comment-send-btn');
                const commentsList = document.getElementById('comments-list');
                const noCommentMessage = document.getElementById('no-comment-message');
                const commentCountSpan = document.getElementById('comment-count');

                const checkInputValidity = () => {
                    sendButton.disabled = commentBody.value.trim() === '';
                    sendButton.classList.toggle('is-disabled', sendButton.disabled);
                };
                
                commentBody.addEventListener('input', () => {
                    commentBody.style.height = 'auto';
                    commentBody.style.height = (commentBody.scrollHeight) + 'px';
                    checkInputValidity();
                });

                checkInputValidity();

                const createCommentElement = (comment) => {
                    const isOwner = comment.user_id === {{ auth()->id() }};
                    const isReportOwner = {{ $isReportOwner ? 'true' : 'false' }};
                    
                    let avatarHtml = '';
                    const userAvatar = comment.user.avatar || (comment.user.resident ? comment.user.resident.avatar : null);
                    const isAvatarUrl = userAvatar && userAvatar.startsWith('http');
                    
                    if (userAvatar) {
                        const finalAvatarSrc = isAvatarUrl ? userAvatar : `/storage/${userAvatar}`;
                        avatarHtml = `<img src="${finalAvatarSrc}" alt="avatar" class="comment-avatar">`;
                    } else {
                        avatarHtml = `<div class="avatar-placeholder"><i class="fa-solid fa-user"></i></div>`;
                    }
                    
                    let authorName = isOwner ? 'Anda' : (isReportOwner ? comment.user.name : comment.user.censored_name);
                    
                    const now = new Date();
                    const hours = now.getHours().toString().padStart(2, '0');
                    const minutes = now.getMinutes().toString().padStart(2, '0');
                    const newTimestamp = `${hours}:${minutes}`;

                    const item = document.createElement('div');
                    item.className = `comment-item ${isOwner ? 'is-owner' : 'is-other'}`;
                    item.innerHTML = `
                        ${avatarHtml}
                        <div class="comment-content">
                            <div class="comment-bubble">
                                <p class="comment-author">${authorName}</p>
                                <p class="comment-body">${comment.body.replace(/</g, "&lt;").replace(/>/g, "&gt;")}</p>
                                <div class="comment-meta-wrapper">
                                    <span class="comment-meta">${newTimestamp}</span>
                                </div>
                            </div>
                        </div>
                    `;
                    return item;
                };

                commentForm.addEventListener('submit', function(event) {
                    event.preventDefault(); 
                    const formData = new FormData(this);
                    const actionUrl = this.action;
                    
                    sendButton.disabled = true;
                    sendButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

                    fetch(actionUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': formData.get('_token'),
                            'Accept': 'application/json',
                        },
                        body: formData,
                    })
                    .then(response => {
                        if (!response.ok) { return response.json().then(err => { throw err; }); }
                        return response.json();
                    })
                    .then(comment => {
                        const newCommentElement = createCommentElement(comment);
                        commentsList.prepend(newCommentElement);
                        commentBody.value = '';
                        commentBody.style.height = 'auto';
                        if (noCommentMessage) { noCommentMessage.style.display = 'none'; }
                        commentCountSpan.textContent = parseInt(commentCountSpan.textContent) + 1;
                        newCommentElement.scrollIntoView({ behavior: 'smooth', block: 'end' });
                    })
                    .catch(error => {
                        let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                        if (error.errors && error.errors.body) { errorMessage = error.errors.body[0]; }
                        Swal.fire({ icon: 'error', title: 'Gagal', text: errorMessage });
                    })
                    .finally(() => {
                        sendButton.innerHTML = '<i class="fa-solid fa-paper-plane"></i>';
                        checkInputValidity();
                    });
                });
            }
        });
    </script>
@endsection