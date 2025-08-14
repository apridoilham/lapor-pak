@extends('layouts.app')

@section('title', 'Daftar Laporan')

@push('styles')
<style>
    :root {
        --primary: #16752B;
        --secondary-text: #6c757d;
        --light-gray-bg: #f8f9fa;
        --border-color: #e2e8f0;
    }

    body {
        background-color: var(--light-gray-bg);
        overflow: hidden;
    }

    .main-content {
        max-width: 480px;
        margin: 0 auto;
        background-color: #ffffff;
        height: 100vh;
        overflow-y: auto;
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.05);
        position: relative;
        padding: 1.5rem;
        padding-bottom: 100px;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .main-content::-webkit-scrollbar {
        display: none;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .page-header .header-title h5 {
        font-weight: 700;
        font-size: 1.25rem;
        color: #2d3748;
        margin-bottom: 0;
    }
    
    .page-header .header-title p {
        font-size: 0.9rem;
        color: var(--secondary-text);
        margin-bottom: 0;
    }
    
    .filter-toggle-button {
        background-color: var(--light-gray-bg);
        border: 1px solid var(--border-color);
        color: var(--secondary-text);
        font-size: 0.85rem;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 10px;
    }

    .filter-container {
        background-color: var(--light-gray-bg);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        display: none;
    }
    .filter-container.show {
        display: block;
    }
    .filter-active-info{background-color:#e8f5e9;color:#16752B;padding:.5rem 1rem;border-radius:8px;font-size:.8rem;display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem}

    .report-card{background-color:#fff;border-radius:16px;box-shadow:0 4px 25px rgba(17,24,39,.05);text-decoration:none;color:#2d3748;display:block;overflow:hidden;margin-bottom:1.5rem;border:1px solid var(--border-color)}.report-card:hover{transform:translateY(-3px);box-shadow:0 8px 25px rgba(17,24,39,.08)}.report-card .card-header{display:flex;align-items:center;gap:.75rem;padding:.75rem 1rem}.report-card .card-header .avatar{width:32px;height:32px;border-radius:50%;object-fit:cover}.report-card .card-header .user-info{font-size:.85rem}.report-card .card-header .user-name{font-weight:600}.report-card .card-header .user-location{font-size:.75rem;color:var(--secondary-text)}.report-card .card-image-container{position:relative}.report-card .card-image-container img{width:100%;height:200px;object-fit:cover}.report-card .card-content{padding:1rem}.report-card .card-title{font-weight:700;line-height:1.4;margin-bottom:.25rem;font-size:1.1rem}.report-card .card-description{font-size:.9rem;color:var(--secondary-text);margin-bottom:.75rem}.report-card .card-footer{display:flex;justify-content:space-between;align-items:center;font-size:.75rem;color:var(--secondary-text);padding:.75rem 1rem;background-color:var(--light-gray-bg);border-top:1px solid var(--border-color)}.badge-status{position:absolute;bottom:10px;left:10px;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;display:inline-flex;align-items:center;gap:6px;color:#fff;border:1px solid rgba(0,0,0,.1)}.badge-status.status-delivered{background-color:#3B82F6}.badge-status.status-processing{background-color:#F59E0B}.badge-status.status-completed{background-color:#10B981}.badge-status.status-rejected{background-color:#EF4444}
</style>
@endpush

@section('content')
    <div class="page-header">
        <div class="header-title">
            <h5>Daftar Laporan</h5>
            <p>{{ $reports->count() }} pengaduan ditemukan</p>
        </div>
        <button class="filter-toggle-button" id="filter-toggle-btn">
            <i class="fa-solid fa-filter me-1"></i> Filter
        </button>
    </div>

    <div class="filter-container" id="filter-section">
        <form action="{{ route('report.index') }}" method="GET">
            <div class="mb-3">
                <label for="category" class="form-label small fw-bold">Kategori</label>
                <select name="category" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->name }}" {{ request('category') == $category->name ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="row g-2">
                <div class="col">
                    <label for="rw_id" class="form-label small fw-bold">RW</label>
                    <select name="rw" id="rw_id" class="form-select form-select-sm">
                        <option value="">Semua RW</option>
                        @foreach($rws as $rw)
                            <option value="{{ $rw->id }}" {{ request('rw') == $rw->id ? 'selected' : '' }}>
                                RW {{ $rw->number }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <label for="rt_id" class="form-label small fw-bold">RT</label>
                    <select name="rt" id="rt_id" class="form-select form-select-sm" disabled>
                        <option value="">Pilih RW Dulu</option>
                    </select>
                </div>
            </div>
            <div class="d-grid mt-3">
                <button type="submit" class="btn btn-primary btn-sm">Terapkan Filter</button>
            </div>
        </form>
    </div>

    @if(request()->except('page'))
        <div class="filter-active-info">
            <span>Filter Aktif</span>
            <a href="{{ route('report.index') }}" class="btn-close" style="font-size: 0.6rem;"></a>
        </div>
    @endif

    <div class="d-flex flex-column gap-4">
        @forelse($reports as $report)
            <a href="{{ route('report.show', ['code' => $report->code, '_ref' => request()->fullUrl()]) }}" class="report-card">
                <div class="card-header">
                    @php
                        $reporterAvatar = $report->resident->avatar;
                        if ($reporterAvatar && !Str::startsWith($reporterAvatar, 'http')) {
                            $reporterAvatar = asset('storage/' . $reporterAvatar);
                        } elseif (!$reporterAvatar) {
                            $reporterAvatar = asset('assets/app/images/default-avatar.png');
                        }
                    @endphp
                    <img src="{{ $reporterAvatar }}" alt="avatar pelapor" class="avatar">
                    <div class="user-info">
                        <div class="user-name">{{ $report->resident->user->name }}</div>
                        <div class="user-location">RT {{ $report->resident->rt->number }}/RW {{ $report->resident->rw->number }}</div>
                    </div>
                </div>

                <div class="card-image-container">
                    <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}">
                    @if($report->latestStatus)
                        @php 
                            $status = $report->latestStatus->status;
                            $statusClass = 'status-' . $status->value;
                            $statusIcon = match($status) {
                                \App\Enums\ReportStatusEnum::DELIVERED => 'fa-paper-plane',
                                \App\Enums\ReportStatusEnum::IN_PROCESS => 'fa-spinner',
                                \App\Enums\ReportStatusEnum::COMPLETED => 'fa-check-double',
                                \App\Enums\ReportStatusEnum::REJECTED => 'fa-xmark',
                            };
                        @endphp
                        <div class="badge-status {{ $statusClass }}"><i class="fa-solid {{ $statusIcon }}"></i><span>{{ $status->label() }}</span></div>
                    @endif
                </div>
                
                <div class="card-content">
                    <h5 class="card-title">{{ $report->title }}</h5>
                    <p class="card-description">{{ Str::limit($report->description, 100) }}</p>
                </div>

                <div class="card-footer">
                    <span>
                        <i class="fa-solid fa-map-marker-alt me-1"></i>
                        @php
                            $addressParts = explode(',', $report->address);
                            $location = 'Lokasi tidak diketahui';
                            if (count($addressParts) >= 5) {
                                $kelurahan = trim($addressParts[count($addressParts) - 5]);
                                $kecamatan = trim($addressParts[count($addressParts) - 4]);
                                $location = $kelurahan . ', ' . $kecamatan;
                            } else {
                                $location = \Str::limit(explode(',', $report->address)[0], 25);
                            }
                        @endphp
                        {{ $location }}
                    </span>
                    <span>{{ \Carbon\Carbon::parse($report->created_at)->diffForHumans() }}</span>
                </div>
            </a>
        @empty
            <div class="d-flex flex-column justify-content-center align-items-center text-center py-5">
                <div id="lottie-empty-home" style="width: 250px; height: 250px;"></div>
                <h5 class="mt-3 fw-bold">Laporan Tidak Ditemukan</h5>
                <p class="text-secondary px-4">Tidak ada laporan yang cocok dengan filter yang Anda pilih.</p>
                <a href="{{ route('report.index') }}" class="btn btn-secondary rounded-pill py-2 px-4 mt-3">
                    Hapus Semua Filter
                </a>
            </div>
        @endforelse
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.12.2/lottie.min.js"></script>
    <script>
        var lottieContainer = document.getElementById('lottie-empty-home');
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
            const filterToggleButton = document.getElementById('filter-toggle-btn');
            const filterSection = document.getElementById('filter-section');
            
            if(filterToggleButton) {
                filterToggleButton.addEventListener('click', function() {
                    filterSection.classList.toggle('show');
                });
            }

            const rwSelect = document.getElementById('rw_id');
            const rtSelect = document.getElementById('rt_id');
            const currentRtId = "{{ request('rt') }}";

            function fetchRts(rwId, selectedRtId = null) {
                if (!rwId) {
                    rtSelect.innerHTML = '<option value="">Pilih RW Dulu</option>';
                    rtSelect.disabled = true;
                    return;
                }

                rtSelect.disabled = true;
                rtSelect.innerHTML = '<option value="">Memuat...</option>';

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
                if (filterSection) filterSection.classList.add('show');
            }
        });
    </script>
@endsection