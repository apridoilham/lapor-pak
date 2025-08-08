@extends('layouts.app')

@section('title', 'Daftar Pengaduan')

@section('content')
    <div class="py-3" id="reports">
        <div class="d-flex justify-content-between align-items-center">
            <p class="text-muted">{{ $reports->count() }} List Pengaduan</p>
            <button class="btn btn-filter" type="button">
                <i class="fa-solid fa-filter me-2"></i>
                Filter
            </button>
        </div>

        @if (request()->category)
            <p>Kategori {{ request()->category }}</p>
        @endif
        <div class="d-flex flex-column gap-3 mt-3">
            @foreach($reports as $report)
                <div class="card card-report border-0 shadow-none">
                    <a href="{{ route('report.show', $report->code) }}" class="text-decoration-none text-dark">
                        <div class="card-body p-0">
                            <div class="card-report-image position-relative mb-2">
                                <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}">

                                @if($report->latestStatus)
                                    @php
                                        $statusValue = $report->latestStatus->status->value;
                                    @endphp

                                    @if ($statusValue === 'delivered')
                                        <div class="badge-status status-delivered">
                                            <i class="fa-solid fa-paper-plane"></i>
                                            <span>Terkirim</span>
                                        </div>
                                    @elseif ($statusValue === 'in_process')
                                        <div class="badge-status status-processing">
                                            <i class="fa-solid fa-spinner"></i>
                                            <span>Diproses</span>
                                        </div>
                                    @elseif ($statusValue === 'completed')
                                        <div class="badge-status status-completed">
                                            <i class="fa-solid fa-check-double"></i>
                                            <span>Selesai</span>
                                        </div>
                                    @elseif ($statusValue === 'rejected')
                                        <div class="badge-status status-rejected">
                                            <i class="fa-solid fa-xmark"></i>
                                            <span>Ditolak</span>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <div class="d-flex align-items-center ">
                                    <img src="{{ asset('assets/app/images/icons/MapPin.png') }}" alt="map pin" class="icon me-2">
                                    <p class="text-primary city">
                                        {{ $report->address }}
                                    </p>
                                </div>
                                <p class="text-secondary date">
                                    {{ \Carbon\Carbon::parse($report->created_at)->diffForHumans() }}
                                </p>
                            </div>
                            <h1 class="card-title">
                                {{ $report->title }}
                            </h1>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection