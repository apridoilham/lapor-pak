@extends('layouts.admin')

@section('title', 'Detail Laporan')

@section('content')
    <a href="{{ route('admin.report.index') }}" class="btn btn-danger mb-3">Kembali</a>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Laporan</h6>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <td style="width: 200px;">Kode Laporan</td>
                    <td>{{ $report->code }}</td>
                </tr>
                <tr>
                    <td>Pelapor</td>
                    <td>{{ $report->resident?->user?->email }} - {{ $report->resident?->user?->name }}</td>
                </tr>
                <tr>
                    <td>RT / RW Pelapor</td>
                    <td>RT {{ $report->resident?->rt?->number }} / RW {{ $report->resident?->rw?->number }}</td>
                </tr>
                <tr>
                    <td>Alamat Tinggal Pelapor</td>
                    <td>{{ $report->resident?->address }}</td>
                </tr>
                <tr>
                    <td>Kategori Laporan</td>
                    <td>{{ $report->reportCategory->name }}</td>
                </tr>
                <tr>
                    <td>Judul Laporan</td>
                    <td>{{ $report->title }}</td>
                </tr>
                <tr>
                    <td>Deskripsi Laporan</td>
                    <td>{{ $report->description }}</td>
                </tr>
                <tr>
                    <td>Bukti Laporan</td>
                    <td>
                        <img src="{{ asset('storage/' . $report->image) }}" alt="image" width="200">
                    </td>
                </tr>
                <tr>
                    <td>Latitude</td>
                    <td>{{ $report->latitude }}</td>
                </tr>
                <tr>
                    <td>Longitude</td>
                    <td>{{ $report->longitude }}</td>
                </tr>
                <tr>
                    <td>Map View</td>
                    <td>
                        <div id="map" style="height: 300px"></div>
                    </td>
                </tr>
                <tr>
                    <td>Alamat Laporan</td>
                    <td>{{ $report->address }}</td>
                </tr>
            </table>
        </div>
    </div>

        <div class="card shadow mb-5">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Progress Laporan</h6>
        </div>
        <div class="card-body">
            @can('manageStatus', $report)
                <a href="{{ route('admin.report-status.create', $report->id) }}" class="btn btn-primary mb-3">Tambah Progress</a>
            @else
                <button class="btn btn-primary mb-3" disabled>Tambah Progress</button>
                <small class="ms-2 text-danger">Hanya admin dari RW {{ $report->resident?->rw?->number }} yang bisa memperbarui progress.</small>
            @endcan

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Update</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report->reportStatuses as $status)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $status->updated_at->isoFormat('D MMM Y, HH:mm') }}</td>
                                <td>
                                    @if ($status->image)
                                        <img src="{{ asset('storage/' . $status->image) }}" alt="image" width="100">
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    {{ $status->status->label() }}
                                </td>
                                <td>
                                    {{ $status->description }}
                                </td>
                                <td>
                                    @can('manageStatus', $report)
                                        <a href="{{ route('admin.report-status.edit', $status->id) }}" class="btn btn-warning">Ubah</a>

                                        <form action="{{ route('admin.report-status.destroy', $status->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" data-title="Hapus Progress?" data-text="Anda yakin ingin menghapus progress ini?">Hapus</button>
                                        </form>
                                    @else
                                        -
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    var mymap = L.map('map').setView([{{ $report->latitude }}, {{ $report->longitude }}], 13);

    var marker = L.marker([{{ $report->latitude }}, {{ $report->longitude }}]).addTo(mymap);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
        maxZoom: 18,
    }).addTo(mymap);

    marker.bindPopup("<b>Lokasi Laporan</b><br />berada di {{ $report->address }}").openPopup();
</script>
@endsection