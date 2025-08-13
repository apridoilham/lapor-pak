@extends('layouts.admin')

@section('title', 'Data Laporan')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Data Laporan</h6>
        <a href="{{ route('admin.report.create') }}" class="btn btn-primary btn-sm">Tambah Data Laporan</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <div class="d-flex justify-content-between flex-column flex-md-row mb-4">
                <form action="{{ route('admin.report.index') }}" method="GET" class="d-flex flex-grow-1 mr-md-2 mb-2 mb-md-0">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari laporan..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">Cari</button>
                        </div>
                    </div>
                </form>
                <form action="{{ route('admin.report.index') }}" method="GET" class="d-flex flex-grow-1">
                    <div class="row w-100">
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label for="rw_id">Filter berdasarkan RW</label>
                                <select name="rw" id="rw_id" class="form-control">
                                    <option value="">Semua RW</option>
                                    @foreach ($rws as $rw)
                                        <option value="{{ $rw->id }}" {{ request('rw') == $rw->id ? 'selected' : '' }}>
                                            RW {{ $rw->number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label for="rt_id">Filter berdasarkan RT</label>
                                <select name="rt" id="rt_id" class="form-control" disabled>
                                    <option value="">Semua RT</option>
                                    {{-- Data RT akan dimuat di sini menggunakan JavaScript --}}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-group mb-0 w-100">
                                <button class="btn btn-outline-primary btn-block" type="submit">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Pelapor</th>
                        <th>Judul Laporan</th>
                        <th>Status</th>
                        <th>Waktu Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $report)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $report->code }}</td>
                            <td>{{ $report->resident->user->name }}</td>
                            <td>{{ $report->title }}</td>
                            <td>
                                @php
                                    // Mengambil nilai string dari Enum
                                    $statusValue = $report->latestStatus->status->value;
                                    $statusClass = '';
                                    switch ($statusValue) {
                                        case 'delivered':
                                            $statusClass = 'badge-primary';
                                            break;
                                        case 'in_process':
                                            $statusClass = 'badge-warning';
                                            break;
                                        case 'completed':
                                            $statusClass = 'badge-success';
                                            break;
                                        case 'rejected':
                                            $statusClass = 'badge-danger';
                                            break;
                                        default:
                                            $statusClass = 'badge-secondary';
                                            break;
                                    }
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ str_replace('_', ' ', $statusValue) }}
                                </span>
                            </td>
                            <td>{{ $report->created_at->format('d-m-Y H:i') }}</td>
                            <td class="d-flex">
                                <a href="{{ route('admin.report.show', $report->id) }}" class="btn btn-info btn-sm mr-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.report.edit', $report->id) }}" class="btn btn-warning btn-sm mr-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.report.destroy', $report->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data laporan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rwSelect = document.getElementById('rw_id');
        const rtSelect = document.getElementById('rt_id');
        const allRts = @json($rts);

        function populateRtSelect(rwId) {
            rtSelect.innerHTML = '<option value="">Semua RT</option>';
            if (rwId) {
                const filteredRts = allRts.filter(rt => rt.rw_id == rwId);
                filteredRts.forEach(rt => {
                    const option = document.createElement('option');
                    option.value = rt.id;
                    option.textContent = `RT ${rt.number}`;
                    if (rt.id == {{ request('rt') ?? 'null' }}) {
                        option.selected = true;
                    }
                    rtSelect.appendChild(option);
                });
                rtSelect.disabled = false;
            } else {
                rtSelect.disabled = true;
            }
        }

        rwSelect.addEventListener('change', function() {
            populateRtSelect(this.value);
        });

        // Panggil saat halaman dimuat untuk mengisi RT jika RW sudah dipilih
        populateRtSelect(rwSelect.value);
    });
</script>
@endsection