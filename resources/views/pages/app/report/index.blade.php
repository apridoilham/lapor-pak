@extends('layouts.app')

@section('title', 'Daftar Pengaduan')

@section('content')
    <div class="py-3" id="reports">
        <div class="d-flex justify-content-between align-items-center">
            <p class="text-muted">{{ $reports->count() }} List Pengaduan</p>
            
            <button class="btn btn-filter" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fa-solid fa-filter me-2"></i>
                Filter
            </button>
        </div>

        @if (request('category') || request('rw'))
            <div class="d-flex justify-content-between align-items-center mt-3 p-2 bg-light rounded">
                <small>
                    Filter aktif: 
                    @if(request('category'))
                        <strong>Kategori: {{ request('category') }}</strong>
                    @endif
                    @if(request('rw'))
                        @php
                            $selectedRw = $rws->firstWhere('id', request('rw'));
                        @endphp
                        <strong class="ms-2">RW: {{ $selectedRw?->number }}</strong>
                    @endif
                    @if(request('rt'))
                        <strong> / RT: {{ request('rt') }}</strong>
                    @endif
                </small>
                <a href="{{ route('report.index') }}" class="btn btn-sm btn-outline-danger" style="font-size: 0.7rem;">Hapus Filter</a>
            </div>
        @endif

        <div class="d-flex flex-column gap-3 mt-3">
            @forelse($reports as $report)
                <div class="card card-report border-0 shadow-none">
                    <a href="{{ route('report.show', $report->code) }}" class="text-decoration-none text-dark">
                        <div class="card-body p-0">
                            <div class="card-report-image position-relative mb-2">
                                <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}">

                                @if($report->latestStatus)
                                    @php $statusValue = $report->latestStatus->status; @endphp
                                    @if ($statusValue === \App\Enums\ReportStatusEnum::DELIVERED)
                                        <div class="badge-status status-delivered"><i class="fa-solid fa-paper-plane"></i><span>Terkirim</span></div>
                                    @elseif ($statusValue === \App\Enums\ReportStatusEnum::IN_PROCESS)
                                        <div class="badge-status status-processing"><i class="fa-solid fa-spinner"></i><span>Diproses</span></div>
                                    @elseif ($statusValue === \App\Enums\ReportStatusEnum::COMPLETED)
                                        <div class="badge-status status-completed"><i class="fa-solid fa-check-double"></i><span>Selesai</span></div>
                                    @elseif ($statusValue === \App\Enums\ReportStatusEnum::REJECTED)
                                        <div class="badge-status status-rejected"><i class="fa-solid fa-xmark"></i><span>Ditolak</span></div>
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
            @empty
                <div class="d-flex flex-column justify-content-center align-items-center text-center" style="margin-top: 50px;">
                    <div id="lottie-empty-reports" style="width: 250px; height: 250px;"></div>
                    <h5 class="mt-3 fw-bold">Tidak Ada Laporan</h5>
                    <p class="text-secondary px-4">
                        @if (request()->hasAny(['category', 'rw', 'rt']))
                            Tidak ada laporan yang ditemukan sesuai dengan filter yang Anda pilih.
                        @else
                            Jadilah yang pertama melaporkan masalah di lingkunganmu!
                        @endif
                    </p>
                    <a href="{{ route('report.index') }}" class="btn btn-secondary rounded-pill py-2 px-4 mt-3">
                        Hapus Semua Filter
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h1 class="modal-title fs-5" id="filterModalLabel">Filter Laporan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('report.index') }}" method="GET">
                        <div class="mb-3">
                            <label for="category" class="form-label">Kategori</label>
                            <select name="category" class="form-select">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->name }}" {{ request('category') == $category->name ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                             <label for="rw_id" class="form-label">RW</label>
                             <select name="rw" id="rw_id" class="form-select">
                                <option value="">Semua RW</option>
                                @foreach($rws as $rw)
                                    <option value="{{ $rw->id }}" {{ request('rw') == $rw->id ? 'selected' : '' }}>
                                        RW {{ $rw->number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                         <div class="mb-3">
                            <label for="rt_id" class="form-label">RT</label>
                            <select name="rt" id="rt_id" class="form-select" disabled>
                                <option value="">Pilih RW terlebih dahulu</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.2/lottie.min.js"></script>
    <script>
        var lottieContainer = document.getElementById('lottie-empty-reports');
        if (lottieContainer) {
            var animation = bodymovin.loadAnimation({
                container: lottieContainer,
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: '{{ asset('assets/app/lottie/not-found.json') }}'
            });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const rwSelect = document.getElementById('rw_id');
            const rtSelect = document.getElementById('rt_id');
            const currentRtId = "{{ request('rt') }}";

            function fetchRts(rwId, selectedRtId = null) {
                if (!rwId) {
                    rtSelect.innerHTML = '<option value="">Pilih RW terlebih dahulu</option>';
                    rtSelect.disabled = true;
                    return;
                }

                fetch(`/api/get-rts-by-rw/${rwId}`)
                    .then(response => response.json())
                    .then(data => {
                        rtSelect.innerHTML = '<option value="">Semua RT</option>';
                        data.forEach(rt => {
                            const option = document.createElement('option');
                            option.value = rt.id;
                            option.textContent = `RT ${rt.number}`;
                            if (selectedRtId && rt.id == selectedRtId) {
                                option.selected = true;
                            }
                            rtSelect.appendChild(option);
                        });
                        rtSelect.disabled = false;
                    });
            }

            rwSelect.addEventListener('change', function() {
                fetchRts(this.value);
            });

            if (rwSelect.value) {
                fetchRts(rwSelect.value, currentRtId);
            }
        });
    </script>
@endsection