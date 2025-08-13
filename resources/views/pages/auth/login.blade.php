@extends('layouts.no-nav')

@section('title', 'Masuk')

@push('styles')
<style>
    .login-container {
        max-width: 400px;
        width: 100%;
        padding: 2rem;
    }
    .btn-google {
        color: #495057;
        background-color: #fff;
        border-color: #ced4da;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        transition: all 0.2s ease-in-out;
    }
    .btn-google:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
    <div class="d-flex flex-column justify-content-center align-items-center vh-100 px-3">
        <div class="login-container">
            <div class="text-center mb-5">
                <h1 class="fw-bolder" style="font-size: 2.5rem;">Selamat Datang</h1>
                <p class="text-secondary">Satu klik untuk masuk dan mulai melaporkan masalah di sekitar Anda.</p>
            </div>
            
            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="d-grid my-4">
                <a href="{{ route('google.redirect') }}" class="btn btn-google btn-lg">
                    <img src="https://www.google.com/favicon.ico" alt="Google" width="20">
                    Masuk dengan Google
                </a>
            </div>
        </div>
    </div>
@endsection