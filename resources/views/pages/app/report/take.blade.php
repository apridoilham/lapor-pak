@extends('layouts.no-nav')

@section('title', 'Ambil Foto')

@section('content')
    <div class="take-photo-container">
        <div class="camera-header">
            <a href="{{ route('home') }}" class="btn-back-circle">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>

        <div class="camera-preview">
            <video autoplay="true" id="video-webcam">
                Browser Anda tidak mendukung kamera.
            </video>
            <canvas id="canvas" style="display:none;"></canvas>
            <div id="result" class="image-result" style="display: none;">
                <img id="photo" alt="Hasil Foto">
            </div>
        </div>

        <div class="camera-footer">
            <div class="button-group" id="action-buttons" style="display: none;">
                <button class="btn-retake" onclick="retakePhoto()">
                    <i class="fa-solid fa-rotate-right"></i>
                    <span>Ulangi</span>
                </button>
                <button class="btn-use" onclick="usePhoto()">
                    <i class="fa-solid fa-check"></i>
                    <span>Gunakan</span>
                </button>
            </div>
            <button class="btn-snap" id="snap-button" onclick="takeSnapshot()">
                <i class="fas fa-camera"></i>
            </button>
        </div>
    </div>

    <script>
        const video = document.getElementById('video-webcam');
        const canvas = document.getElementById('canvas');
        const photo = document.getElementById('photo');
        const resultDiv = document.getElementById('result');
        const snapButton = document.getElementById('snap-button');
        const actionButtons = document.getElementById('action-buttons');
        let stream;

        async function startCamera() {
            try {
                // Prioritaskan kamera belakang untuk HP
                const constraints = {
                    video: {
                        facingMode: {
                            ideal: 'environment'
                        }
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
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageDataURL = canvas.toDataURL('image/jpeg');
            photo.src = imageDataURL;
            
            // Perbaikan Logika Tampilan
            resultDiv.style.display = 'block';
            video.style.display = 'none'; // Sembunyikan video
            snapButton.style.display = 'none';
            actionButtons.style.display = 'flex';
        }

        function retakePhoto() {
            resultDiv.style.display = 'none';
            video.style.display = 'block'; // Tampilkan kembali video
            snapButton.style.display = 'block';
            actionButtons.style.display = 'none';
        }

        function usePhoto() {
            localStorage.setItem('image', photo.src);
            window.location.href = '{{ route('report.create') }}';
        }

        startCamera();
    </script>
@endsection