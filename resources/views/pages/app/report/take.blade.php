@extends('layouts.no-nav')

@section('title', 'Ambil Foto Laporan')

@push('styles')
<style>
    :root {
        --primary-color: #0ea5e9;
        --text-dark: #1e293b;
        --bg-body: #000000;
        --bg-white: #FFFFFF;
        --font-sans: 'Inter', sans-serif;
    }
    
    body {
        background-color: var(--bg-body);
        overflow: hidden;
    }

    .main-content {
        padding: 0;
        height: 100vh;
        max-height: 100svh;
        display: flex;
        flex-direction: column;
    }

    .camera-container {
        position: relative;
        width: 100%;
        height: 100%;
        background-color: #000;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .camera-header {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        padding: 1.5rem;
        z-index: 10;
        background: linear-gradient(180deg, rgba(0,0,0,0.4) 0%, transparent 100%);
    }

    .camera-header a {
        color: white;
        font-size: 1.5rem;
        text-decoration: none;
    }

    #video-webcam {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .camera-footer {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 1.5rem;
        padding-bottom: 2rem;
        display: flex;
        justify-content: space-around;
        align-items: center;
        z-index: 10;
        background: linear-gradient(0deg, rgba(0,0,0,0.6) 0%, transparent 100%);
    }
    
    .camera-control-button {
        background: rgba(255,255,255,0.2);
        color: white;
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        backdrop-filter: blur(5px);
    }
    
    .btn-snap {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background-color: white;
        border: 4px solid rgba(255,255,255,0.5);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-snap-inner {
        width: 56px;
        height: 56px;
        background-color: white;
        border-radius: 50%;
    }

    #gallery-input {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="camera-container">
    <div class="camera-header">
        <a href="{{ route('home') }}">
            <i class="fa-solid fa-times"></i>
        </a>
    </div>

    <video autoplay="true" id="video-webcam" playsinline>
        Browser Anda tidak mendukung kamera.
    </video>
    
    <div class="camera-footer">
        <button class="camera-control-button" id="gallery-button" title="Pilih dari Galeri">
            <i class="fa-regular fa-image"></i>
        </button>

        <button class="btn-snap" id="snap-button" title="Ambil Foto">
            <div class="btn-snap-inner"></div>
        </button>

        <button class="camera-control-button" id="flip-camera-button" title="Ganti Kamera">
            <i class="fa-solid fa-rotate"></i>
        </button>
    </div>
    
    {{-- Input file tersembunyi untuk upload dari galeri --}}
    <input type="file" id="gallery-input" accept="image/*">
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const video = document.getElementById('video-webcam');
        const snapButton = document.getElementById('snap-button');
        const flipCameraButton = document.getElementById('flip-camera-button');
        const galleryButton = document.getElementById('gallery-button');
        const galleryInput = document.getElementById('gallery-input');
        
        let stream;
        let currentFacingMode = 'environment';

        function stopStream() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        }

        async function startCamera(facingMode) {
            stopStream();

            const constraints = {
                video: {
                    facingMode: { ideal: facingMode },
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            };

            try {
                stream = await navigator.mediaDevices.getUserMedia(constraints);
                video.srcObject = stream;
            } catch (err) {
                console.error("Error accessing camera:", err);
                alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin dan tidak ada aplikasi lain yang sedang menggunakannya.');
            }
        }

        function takeSnapshot() {
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageDataURL = canvas.toDataURL('image/jpeg');
            
            localStorage.setItem('image', imageDataURL);
            window.location.href = '{{ route('report.preview') }}';
        }

        snapButton.addEventListener('click', takeSnapshot);

        flipCameraButton.addEventListener('click', () => {
            currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
            startCamera(currentFacingMode);
        });

        galleryButton.addEventListener('click', () => {
            galleryInput.click();
        });

        galleryInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    localStorage.setItem('image', e.target.result);
                    window.location.href = '{{ route('report.preview') }}';
                }
                reader.readAsDataURL(file);
            }
        });

        startCamera(currentFacingMode);
    });
</script>
@endsection