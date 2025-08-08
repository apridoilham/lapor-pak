@extends('layouts.admin')

@section('content')
<h1>Dashboard</h1>

    <div class="row">

        <div class="col-12 col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Total Kategori Laporan</h6>
                    <p class="card-text">{{ \App\Models\ReportCategory::count() }}</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Total Laporan</h6>
                    <p class="card-text">{{ \App\Models\Report::count() }}</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Total Masyarakat</h6>
                    <p class="card-text">{{ \App\Models\Resident::count() }}</p>
                </div>
            </div>
        </div>

    </div>
@endsection