@extends('layouts.admin')
@section('title', 'Data RT & RW')
@section('content')
    <h1 class="h3 mb-4 text-gray-800">Manajemen Data RT & RW</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Data RW Baru</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.rtrw.store') }}" method="POST">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="number">Nomor RW Baru</label>
                            <input type="text" name="number" class="form-control" placeholder="Contoh: 005" required maxlength="3">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="rt_count">Jumlah RT di Dalamnya</label>
                            <input type="number" name="rt_count" class="form-control" placeholder="Contoh: 10" required min="1">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary w-100">Tambah</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar RW dan RT yang Terdata</h6>
        </div>
        <div class="card-body">
            <div class="accordion" id="rwAccordion">
                @forelse ($rws as $rw)
                    <div class="card">
                        <div class="card-header" id="heading{{ $rw->id }}">
                            <h2 class="mb-0 d-flex justify-content-between align-items-center">
                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse{{ $rw->id }}">
                                    RW {{ $rw->number }} ({{ $rw->rts->count() }} RT)
                                </button>
                                <form action="{{ route('admin.rtrw.destroy', $rw->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" data-confirm-text="Yakin ingin menghapus RW {{ $rw->number }} beserta seluruh RT di dalamnya?">
                                        <i class="fa fa-trash"></i> Hapus RW
                                    </button>
                                </form>
                            </h2>
                        </div>
                        <div id="collapse{{ $rw->id }}" class="collapse" data-parent="#rwAccordion">
                            <div class="card-body">
                                <ul class="list-group">
                                    @forelse ($rw->rts as $rt)
                                        <li class="list-group-item">RT {{ $rt->number }}</li>
                                    @empty
                                        <li class="list-group-item text-muted">Belum ada RT di dalam RW ini.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">Belum ada data RW. Silakan tambahkan data baru.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection