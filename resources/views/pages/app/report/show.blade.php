@extends('layouts.app')

@section('title', $report->code)

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
                <div class="col-4 text-secondary">Lokasi Laporan</div>
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

    <div class="card card-report-information mt-4">
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
@endsection