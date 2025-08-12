@extends('layouts.admin')
@section('title', 'Data RT & RW')

@section('styles')
<style>
    .rt-list {
        background-color: #f8f9fc;
        border-radius: .35rem;
    }
</style>
@endsection

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Manajemen Data RW & RT</h1>

    <div class="row">

        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tambah Data RW Baru</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.rtrw.store') }}" method="POST" id="add-rtrw-form">
                        @csrf
                        <div class="form-group">
                            <label for="number">Nomor RW Baru</label>
                            <input type="text" name="number" id="rw_number_input" class="form-control @error('number') is-invalid @enderror" placeholder="Contoh: 005" required maxlength="3" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            <div class="invalid-feedback" id="rw-error">Nomor RW ini sudah ada.</div>
                            @error('number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label for="rt_count">Jumlah RT di Dalamnya</label>
                            <input type="text" name="rt_count" class="form-control @error('rt_count') is-invalid @enderror" placeholder="Contoh: 10" required maxlength="2" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                             @error('rt_count')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-block" id="tambah-btn" disabled>
                            <i class="fa fa-plus-circle"></i> Tambah RW & RT
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar RW dan RT yang Terdata</h6>
                </div>
                <div class="card-body">
                    @if($rws->isEmpty())
                        <p class="text-center text-muted my-3">Belum ada data RW. Silakan tambahkan data baru di form sebelah kiri.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($rws as $rw)
                                <li class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">RW {{ $rw->number }}</h6>
                                            <small class="text-muted">{{ $rw->rts->count() }} RT terdaftar</small>
                                        </div>
                                        <div>
                                            <button class="btn btn-info btn-sm mr-2" type="button" data-toggle="collapse" data-target="#collapse{{ $rw->id }}">
                                                <i class="fa fa-eye"></i> Detail RT
                                            </button>
                                            <form action="{{ route('admin.rtrw.destroy', $rw->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" data-title="Hapus RW?" data-text="Anda yakin ingin menghapus RW {{ $rw->number }} beserta seluruh RT di dalamnya?">
                                                    <i class="fa fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="collapse mt-3" id="collapse{{ $rw->id }}">
                                        <div class="p-3 rt-list">
                                            <h6 class="font-weight-bold">Daftar RT di RW {{ $rw->number }}:</h6>
                                            <ul class="list-unstyled mb-0">
                                                 @forelse ($rw->rts->sortBy('number') as $rt)
                                                    <li>- RT {{ $rt->number }}</li>
                                                @empty
                                                    <li class="text-muted">Belum ada data RT.</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addForm = document.getElementById('add-rtrw-form');
            const tambahButton = document.getElementById('tambah-btn');
            const requiredInputs = addForm.querySelectorAll('[required]');
            const rwNumberInput = document.getElementById('rw_number_input');
            const rwError = document.getElementById('rw-error');

            function checkFormValidity() {
                let allFieldsFilled = true;
                requiredInputs.forEach(input => {
                    if (input.value.trim() === '') {
                        allFieldsFilled = false;
                    }
                });

                const isRwInvalid = rwNumberInput.classList.contains('is-invalid');
                tambahButton.disabled = !allFieldsFilled || isRwInvalid;
            }

            requiredInputs.forEach(input => {
                input.addEventListener('input', checkFormValidity);
            });

            let debounceTimer;
            rwNumberInput.addEventListener('input', function() {
                checkFormValidity();
                clearTimeout(debounceTimer);
                
                const parentFormGroup = this.closest('.form-group');
                if (parentFormGroup) {
                    parentFormGroup.classList.remove('has-error');
                    this.classList.remove('is-invalid');
                    const errorDiv = parentFormGroup.querySelector('.invalid-feedback');
                    if(errorDiv) errorDiv.classList.remove('d-block');
                }


                debounceTimer = setTimeout(function() {
                    const rwNumber = rwNumberInput.value;
                    if (rwNumber.length > 0) {
                        fetch('/api/check-rw', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    number: rwNumber
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.is_taken) {
                                    rwNumberInput.classList.add('is-invalid');
                                    rwError.classList.add('d-block');
                                }
                                checkFormValidity();
                            });
                    }
                }, 500);
            });
        });
    </script>
@endsection