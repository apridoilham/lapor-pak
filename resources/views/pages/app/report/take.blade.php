@extends('layouts.no-nav')

@section('title', 'Ambil Foto Laporan')

@section('content')
    <div class="header-nav mb-4">
        <a href="{{ route('home') }}" class="text-decoration-none">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1>Ambil Foto Bukti</h1>
    </div>

    <div class="camera-container">
        <div class="camera-preview">
            <video autoplay="true" id="video-webcam">
                Browser Anda tidak mendukung kamera.
            </video>
        </div>

        <div class="camera-footer">
            <button class="btn-snap" id="snap-button">
                <i class="fas fa-camera"></i>
            </button>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('video-webcam');
            const snapButton = document.getElementById('snap-button');
            let stream;

            async function startCamera() {
                try {
                    const constraints = {
                        video: {
                            facingMode: { ideal: 'environment' },
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
                        },
                        audio: false
                    };
                    stream = await navigator.mediaDevices.getUserMedia(constraints);
                    video.srcObject = stream;
                } catch (err) {
                    console.error("Error accessing camera:", err);
                    alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin.');
                }
            }

            function takeSnapshot() {
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                const imageDataURL = canvas.toDataURL('image/jpeg');
                
                // Simpan ke localStorage dan pindah ke halaman preview
                localStorage.setItem('image', imageDataURL);
                window.location.href = '{{ route('report.preview') }}';
            }
            
            snapButton.addEventListener('click', takeSnapshot);

            startCamera();
        });
    </script>
@endsection