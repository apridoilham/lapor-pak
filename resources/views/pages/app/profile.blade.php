@extends('layouts.app')
@section('title', 'Profil Saya')

{{-- Hapus semua @push('styles') dan <style> dari sini --}}

@section('content')
    @php
        $user = Auth::user()->load('resident.rt', 'resident.rw');
        $resident = $user->resident;
    @endphp

    <div class="profile-header">
        {{-- ... Konten HTML ... --}}
    </div>

    <div class="stats-card text-center">
        {{-- ... Konten HTML ... --}}
    </div>

    @if(!$resident->rt_id || !$resident->rw_id || $resident->address === 'Alamat belum diatur')
        <div class="alert alert-warning ...">
            {{-- ... Konten HTML ... --}}
        </div>
    @endif

    <div class="d-flex flex-column gap-3 profile-menu">
        {{-- ... Konten HTML ... --}}
    </div>
@endsection