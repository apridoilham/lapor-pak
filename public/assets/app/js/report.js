var mapElement = document.getElementById('map');

if (mapElement) {
    // Perbaikan: Mencari ID 'latitude' dan 'longitude'
    var latitudeInput = document.getElementById('latitude');
    var longitudeInput = document.getElementById('longitude');
    var addressInput = document.getElementById('address');

    navigator.geolocation.getCurrentPosition(function (position) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;

        // Perbaikan: Mengisi nilai ke input yang benar
        latitudeInput.value = lat;
        longitudeInput.value = lng;

        var mymap = L.map('map').setView([lat, lng], 17);

        var marker = L.marker([lat, lng]).addTo(mymap);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
            maxZoom: 18,
        }).addTo(mymap);

        var geocodingUrl =
            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;

        fetch(geocodingUrl)
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    addressInput.value = data.display_name;
                    marker.bindPopup(`<b>Lokasi Awal Anda</b><br />${data.display_name}`).openPopup();
                }
            })
            .catch(error => console.error('Error fetching reverse geocoding data:', error));
    });
}