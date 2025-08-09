@extends('layouts.admin')

@section('title', 'Detail Data Masyarakat')

@section('content')
    <a href="{{ route('admin.resident.index') }}" class="btn btn-danger mb-3">Kembali</a>

    <div class="row">
        <div class="col-lg-4">
            {{-- Kartu Informasi Pribadi --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pribadi</h6>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('storage/' . $resident->avatar) }}" alt="Avatar" class="img-thumbnail rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <h4 class="font-weight-bold">{{ $resident->user->name }}</h4>
                    <p class="text-muted">{{ $resident->user->email }}</p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.resident.edit', $resident->id) }}" class="btn btn-warning btn-block">Edit Data Ini</a>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            {{-- Kartu Detail Alamat & Riwayat Laporan --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail & Riwayat Laporan</h6>
                </div>
                <div class="card-body">
                    <h5>Alamat</h5>
                    <table class="table table-bordered mb-4">
                        <tr>
                            <th style="width: 150px;">Alamat Lengkap</th>
                            <td>{{ $resident->address }}</td>
                        </tr>
                        <tr>
                            <th>RT / RW</th>
                            <td>RT {{ $resident->rt->number }} / RW {{ $resident->rw->number }}</td>
                        </tr>
                    </table>

                    <hr>

                    <h5 class="mt-4">Riwayat Laporan ({{ $resident->reports->count() }} Laporan)</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($resident->reports as $report)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.report.show', $report->id) }}">{{ $report->code }}</a>
                                        </td>
                                        <td>{{ Str::limit($report->title, 25) }}</td>
                                        <td>{{ $report->reportCategory->name }}</td>
                                        <td>
                                            @if($report->latestStatus)
                                                <span class="badge badge-info">{{ $report->latestStatus->status->value }}</span>
                                            @else
                                                <span class="badge badge-secondary">Baru</span>
                                            @endif
                                        </td>
                                        <td>{{ $report->created_at->isoFormat('D MMM Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Masyarakat ini belum pernah membuat laporan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection