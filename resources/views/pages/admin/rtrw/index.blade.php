@extends('layouts.admin')
@section('title', 'Data RT & RW')
@section('content')
    <h1 class="h3 mb-4 text-gray-800">Manajemen Data RT & RW</h1>
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Data RT</h6></div>
                <div class="card-body">
                    <form action="{{ route('admin.rt.store') }}" method="POST" class="d-flex mb-3">
                        @csrf
                        {{-- PERUBAHAN DI SINI: Tambahkan maxlength dan class --}}
                        <input type="text" name="number" class="form-control mr-2 rt-rw-input" placeholder="Nomor RT (contoh: 001)" required maxlength="3">
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            @forelse ($rts as $rt)
                                <tr>
                                    <td>RT {{ $rt->number }}</td>
                                    <td class="text-right" style="width: 80px;">
                                        <form action="{{ route('admin.rt.destroy', $rt->id) }}" method="POST" class="delete-form">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                    data-confirm-text="Yakin ingin menghapus RT {{ $rt->number }}?">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">Belum ada data RT.</td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Data RW</h6></div>
                <div class="card-body">
                    <form action="{{ route('admin.rw.store') }}" method="POST" class="d-flex mb-3">
                        @csrf
                        {{-- PERUBAHAN DI SINI: Tambahkan maxlength dan class --}}
                        <input type="text" name="number" class="form-control mr-2 rt-rw-input" placeholder="Nomor RW (contoh: 001)" required maxlength="3">
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </form>
                    <div class="table-responsive">
                         <table class="table table-bordered">
                            @forelse ($rws as $rw)
                                <tr>
                                    <td>RW {{ $rw->number }}</td>
                                    <td class="text-right" style="width: 80px;">
                                        <form action="{{ route('admin.rw.destroy', $rw->id) }}" method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                    data-confirm-text="Yakin ingin menghapus RW {{ $rw->number }}?">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">Belum ada data RW.</td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Skrip untuk konfirmasi hapus (sudah ada) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteForms = document.querySelectorAll('.delete-form');

            deleteForms.forEach(form => {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    const button = form.querySelector('button[type="submit"]');
                    const confirmText = button.dataset.confirmText || 'Apakah Anda yakin ingin menghapus data ini?';

                    Swal.fire({
                        title: 'Konfirmasi Hapus',
                        text: confirmText,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // ▼▼▼ TAMBAHKAN SCRIPT BARU INI UNTUK BATASAN INPUT ▼▼▼
            const numberInputs = document.querySelectorAll('.rt-rw-input');
            numberInputs.forEach(input => {
                input.addEventListener('input', function(event) {
                    // Hapus karakter selain angka
                    this.value = this.value.replace(/[^0-9]/g, '');

                    if (this.value.length > 3) {
                        // Potong nilainya jika lebih dari 3 karakter
                        this.value = this.value.slice(0, 3);
                        
                        // Tampilkan peringatan
                        Swal.fire({
                            title: 'Peringatan',
                            text: 'Nomor RT/RW tidak boleh lebih dari 3 karakter.',
                            icon: 'warning',
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    }
                });
            });
        });
    </script>
@endsection