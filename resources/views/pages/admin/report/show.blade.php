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
                    <td>Kode Laporan</td>
                    <td>{{ $report->code }}</td>
                </tr>
                <tr>
                    <td>Pelapor</td>
                    <td>{{ $report->resident->user->email }} - {{ $report->resident->user->name }}</td>
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