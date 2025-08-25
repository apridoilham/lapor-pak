@extends('layouts.admin')
@section('title', 'Detail Kategori: ' . $report_category->name)

@push('styles')
<style>
    .kpi-card {
        border-left: 4px solid #4e73df;
    }
    .table thead th { background-color: #f8f9fc; border-bottom-width: 1px; font-weight: 600; color: #5a5c69; }
    .table td, .table th { vertical-align: middle; }
    .table tbody tr:hover { background-color: #f8f9fc; }
    .avatar-in-table { width: 40px; height: 40px; object-fit: cover; }
    .soft-badge { font-size: 0.8rem; font-weight: 600; padding: .4em .8em; border-radius: 20px; }
    .soft-badge.badge-success { background-color: #d1fae5; color: #065f46; }
    .soft-badge.badge-warning { background-color: #fef3c7; color: #92400e; }
    .soft-badge.badge-danger { background-color: #fee2e2; color: #991b1b; }
    .soft-badge.badge-primary { background-color: #dbeafe; color: #1e40af; }
    .soft-badge.badge-secondary { background-color: #e5e7eb; color: #4b5563; }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.report-categories.index') }}" class="btn btn-outline-primary btn-circle mr-3" title="Kembali">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Detail Kategori</h1>
                <p class="mb-0 text-muted">{{ $report_category->name }}</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.report-categories.edit', $report_category) }}" class="btn btn-sm btn-outline-warning shadow-sm">
                <i class="fas fa-edit fa-sm mr-2"></i>Ubah Kategori
            </a>
            <form action="{{ route('admin.report-categories.destroy', $report_category) }}" method="POST" class="d-inline delete-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm"
                        data-title="Hapus Kategori {{ $report_category->name }}?" 
                        data-text="Kategori hanya dapat dihapus jika tidak ada laporan yang menggunakannya. Lanjutkan?">
                    <i class="fas fa-trash fa-sm mr-2"></i>Hapus Kategori
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card kpi-card shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Laporan Terkait</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $report_category->reports->count() }} Laporan</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Laporan dengan Kategori "{{ $report_category->name }}"</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Pelapor</th>
                            <th>Judul Laporan</th>
                            <th class="text-center">Status</th>
                            <th>Tanggal Update</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($report_category->reports as $report)
                            <tr>
                                <td><a href="{{ route('admin.reports.show', $report->id) }}" class="font-weight-bold">{{ $report->code }}</a></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @php
                                            $avatarUrl = optional($report->resident->user)->avatar ?? optional($report->resident)->avatar;
                                            if ($avatarUrl && !filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
                                                $avatarUrl = asset('storage/' . $avatarUrl);
                                            } elseif (empty($avatarUrl)) {
                                                $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode(optional($report->resident->user)->name) . '&background=1a202c&color=fff&size=60';
                                            }
                                        @endphp
                                        <img class="img-profile rounded-circle avatar-in-table mr-3" src="{{ $avatarUrl }}">
                                        <div>
                                            <div class="font-weight-bold text-dark">{{ optional($report->resident->user)->name }}</div>
                                            <div class="small text-muted">RT {{ optional($report->resident->rt)->number }} / RW {{ optional($report->resident->rw)->number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ Str::limit($report->title, 40) }}</td>
                                <td class="text-center">
                                     @if ($report->latestStatus)
                                        @php $status = $report->latestStatus->status; @endphp
                                        <span class="soft-badge badge-{{ $status->colorClass() }}">{{ $status->label() }}</span>
                                    @else
                                        <span class="soft-badge badge-primary">Baru</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $displayTime = optional($report->latestStatus)->created_at ?? $report->created_at;
                                    @endphp
                                    <div class="text-dark">{{ $displayTime->isoFormat('D MMM YYYY') }}</div>
                                    <div class="small text-muted">{{ $displayTime->format('H:i') }} WIB</div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye fa-sm mr-1"></i>Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-folder-open fa-2x text-gray-400 mb-2"></i>
                                    <p class="text-muted">Belum ada laporan yang menggunakan kategori ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const button = this.querySelector('button[type="submit"]');
            Swal.fire({
                title: button.dataset.title || 'Anda yakin?',
                text: button.dataset.text || 'Tindakan ini tidak dapat dibatalkan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush