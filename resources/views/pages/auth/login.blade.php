@extends('layouts.no-nav')

@section('title', 'Selamat Datang di BSB Lapor')

@push('styles')
<style>
    :root {
        --bsb-primary: #16752B;
        --bsb-text-dark: #1a202c;
        --bsb-text-light: #4a5568;
        --bsb-bg: #f7fafc;
        --bsb-white: #ffffff;
        --bsb-border: #e2e8f0;
    }

    /* Menggunakan font Plus Jakarta Sans */
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap');

    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: var(--bsb-bg);
    }

    .login-screen {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .login-main {
        flex-grow: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 2rem 1.5rem;
    }

    .login-card {
        background-color: var(--bsb-white);
        max-width: 480px;
        width: 100%;
        padding: 3rem 2.5rem;
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
        text-align: center;
        border: 1px solid var(--bsb-border);
    }

    .logo-container {
        margin-bottom: 1.5rem;
    }

    .logo-container img {
        height: 180px;
        width: auto;
    }

    .login-card h1 {
        font-weight: 800;
        font-size: 2.5rem;
        color: var(--bsb-text-dark);
        margin-bottom: 0.75rem;
        letter-spacing: -0.03em;
    }

    .login-card p.subtitle {
        color: var(--bsb-text-light);
        font-size: 1.1rem;
        margin-bottom: 3rem;
        line-height: 1.6;
        max-width: 380px;
        margin-left: auto;
        margin-right: auto;
    }

    .btn-google {
        font-size: 1rem;
        color: var(--bsb-text-dark);
        background-color: var(--bsb-white);
        border: 1px solid var(--bsb-border);
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.8rem;
        padding: 1rem;
        border-radius: 12px;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .btn-google:hover {
        transform: translateY(-2px);
        box-shadow: 0 7px 20px -5px rgba(0,0,0,0.08);
        border-color: #cbd5e0;
    }

    .login-footer {
        flex-shrink: 0;
        text-align: center;
        padding: 1.5rem;
    }

    .login-footer p {
        font-size: 0.9rem;
        color: #a0aec0;
    }

    .login-footer strong {
        color: #718096;
    }
</style>
@endpush

@section('content')
<div class="login-screen">
    
    <main class="login-main">
        <div class="login-card">
            <div class="logo-container">
                <img src="{{ asset('assets/app/images/logo.jpg') }}" alt="Logo BSB Lapor">
            </div>

            <h1>Selamat Datang</h1>
            <p class="subtitle">Satu klik untuk masuk dan mulai melaporkan masalah di sekitar wilayah Kelurahan Bojongsari Baru.</p>
            
            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="d-grid">
                <a href="{{ route('google.redirect') }}" class="btn btn-google btn-lg">
                    <img src="https://www.google.com/favicon.ico" alt="Google" width="24">
                    Masuk dengan Akun Google
                </a>
            </div>
        </div>
    </main>
    
    <footer class="login-footer">
        <p>Sebuah Inisiatif oleh <strong>Kelompok KKN Depok 31 <br> UIN Syarif Hidayatullah Jakarta 2025</strong></p>
    </footer>

</div>
@endsection