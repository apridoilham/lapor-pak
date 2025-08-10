@extends('layouts.app')

@section('title', $report->code)

@push('styles')
<style>
    .comment-container {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
    }
    .comment-container.is-owner {
        flex-direction: row-reverse;
    }
    .comment-content {
        display: flex;
        flex-direction: column;
    }
    .comment-container.is-owner .comment-content {
        align-items: flex-end;
    }
    .comment-container.is-other .comment-content {
        align-items: flex-start;
    }
    .comment-bubble {
        padding: 0.6rem 0.8rem;
        border-radius: 12px;
        max-width: 100%;
        overflow-wrap: break-word;
    }
    .comment-bubble.is-owner {
        background-color: #DCF8C6;
    }
    .comment-bubble.is-other {
        background-color: #F3F4F6;
    }
    .comment-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-top: 2px;
        flex-shrink: 0;
    }
    .comment-meta {
        font-size: 0.8rem;
        margin-top: 0.25rem;
        padding: 0 0.5rem;
    }
</style>
@endpush

@section('content')
    <div class="header-nav">
        <a href="{{ url()->previous() }}">
            <img src="{{ asset('assets/app/images/icons/ArrowLeft.svg') }}" alt="arrow-left">
        </a>
        <h1>Detail Laporan {{ $report->code }}</h1>
    </div>

    <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}" class="report-image mt-5">

    <h1 class="report-title mt-3">{{ $report->title }}</h1>

    <div class="card card-report-information mt-4">
        <div class="card-body">
            <div class="card-title mb-4 fw-bold">Detail Informasi</div>
            <div class="row mb-3">
                <div class="col-4 text-secondary">Kode</div>
                <div class="col-8">
                    <p class="mb-0">: {{ $report->code }}</p>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-4 text-secondary">Tanggal</div>
                <div class="col-8">
                    <p class="mb-0">: {{ \Carbon\Carbon::parse($report->created_at)->tz('Asia/Jakarta')->isoFormat('dddd, D MMMM YYYY, HH:mm') }}</p>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-4 text-secondary">Pelapor</div>
                <div class="col-8">
                    <p class="mb-0">: {{ $report->resident?->user?->name }} (RT {{ $report->resident?->rt?->number }} / RW {{ $report->resident?->rw?->number }})</p>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-4 text-secondary">Kategori</div>
                <div class="col-8">
                    <p class="mb-0">: {{ $report->reportCategory->name }}</p>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-4 text-secondary">Deskripsi</div>
                <div class="col-8">
                    <p class="mb-0">: {{ $report->description }}</p>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-4 text-secondary">Lokasi</div>
                <div class="col-8">
                    <p class="mb-0">: {{ $report->address }}</p>
                </div>
            </div>
            <div class="row mb-3 align-items-center">
                <div class="col-4 text-secondary">Status</div>
                <div class="col-8 d-flex align-items-center">
                    <span class="me-2">:</span>
                    @if($report->latestStatus)
                        @php
                            $statusValue = $report->latestStatus->status;
                        @endphp

                        @if ($statusValue === \App\Enums\ReportStatusEnum::DELIVERED)
                            <div class="badge-status status-delivered">
                                <i class="fa-solid fa-paper-plane"></i>
                                <span>Terkirim</span>
                            </div>
                        @elseif ($statusValue === \App\Enums\ReportStatusEnum::IN_PROCESS)
                            <div class="badge-status status-processing">
                                <i class="fa-solid fa-spinner"></i>
                                <span>Diproses</span>
                            </div>
                        @elseif ($statusValue === \App\Enums\ReportStatusEnum::COMPLETED)
                            <div class="badge-status status-completed">
                                <i class="fa-solid fa-check-double"></i>
                                <span>Selesai</span>
                            </div>
                        @elseif ($statusValue === \App\Enums\ReportStatusEnum::REJECTED)
                            <div class="badge-status status-rejected">
                                <i class="fa-solid fa-xmark"></i>
                                <span>Ditolak</span>
                            </div>
                        @endif
                    @else
                        <span>Belum ada status</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card card-report-information mt-4" id="riwayat-perkembangan">
        <div class="card-body">
            <div class="card-title mb-4 fw-bold">Riwayat Perkembangan</div>
            <ul class="timeline">
                @forelse ($report->reportStatuses as $status)
                    <li class="timeline-item">
                        <div class="timeline-item-content">
                            @if ($status->image)
                                <img src="{{ asset('storage/' . $status->image) }}" alt="status" class="img-fluid">
                            @endif
                            <span class="timeline-date">{{ \Carbon\Carbon::parse($status->created_at)->tz('Asia/Jakarta')->isoFormat('dddd, D MMMM YYYY, HH:mm') }}</span>
                            
                            @php
                                $statusLabel = $status->status->label();
                                if ($status->status === \App\Enums\ReportStatusEnum::COMPLETED) {
                                    if ($status->created_by_role === 'resident') {
                                        $statusLabel .= ' (oleh Pelapor)';
                                    } elseif ($status->created_by_role === 'admin') {
                                        $statusLabel .= ' (oleh Admin)';
                                    }
                                }
                            @endphp
                            <h6 class="timeline-status">{{ $statusLabel }}</h6>
                            
                            <span class="timeline-event">{{ $status->description }}</span>
                        </div>
                    </li>
                @empty
                    <li class="text-center text-secondary">Belum ada riwayat perkembangan.</li>
                @endforelse
            </ul>
        </div>
    </div>
    
    @if($report->visibility !== \App\Enums\ReportVisibilityEnum::PRIVATE)
        <div class="card card-report-information mt-4" id="komentar">
            <div class="card-body">
                <div class="card-title mb-4 fw-bold">Komentar ({{ $report->comments->count() }})</div>
                
                @can('create', [\App\Models\Comment::class, $report])
                <form action="{{ route('report.comments.store', $report) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="mb-2">
                        <textarea name="body" class="form-control" rows="3" placeholder="Tulis komentar Anda..." required></textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-sm">Kirim Komentar</button>
                    </div>
                </form>
                @else
                <div class="alert alert-warning small">
                    Anda tidak memiliki izin untuk berkomentar pada laporan ini.
                </div>
                @endcan

                @forelse ($report->comments as $comment)
                    @php
                        $isOwner = $comment->user_id === auth()->id();
                    @endphp
                    <div class="comment-container {{ $isOwner ? 'is-owner' : '' }}">
                        <img src="{{ $comment->user->resident->avatar ? asset('storage/' . $comment->user->resident->avatar) : asset('assets/app/images/default-avatar.png') }}" alt="avatar" class="comment-avatar">
                        <div class="comment-content">
                            <div class="comment-bubble {{ $isOwner ? 'is-owner' : 'is-other' }}">
                                <span class="fw-bold small d-block">{{ $isOwner ? 'Anda' : $comment->user->name }}</span>
                                <p class="mb-0">{{ $comment->body }}</p>
                            </div>
                             <small class="text-muted comment-meta">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-secondary">Belum ada komentar.</p>
                @endforelse
            </div>
        </div>
    @endif
@endsection