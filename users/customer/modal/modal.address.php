<div class="offcanvas add-address offcanvas-bottom" tabindex="-1" id="add-address" aria-labelledby="add-address">
    <div class="offcanvas-header">
        <h5 class="title-color font-md fw-600">Add Address</h5>
    </div>
    <div class="offcanvas-body small">
        <!-- Map Container -->
        <div id="map" style="height: 300px; margin-bottom: 15px;"></div>

        <!-- Locate Me Button -->
        <button id="locate-me" class="btn-solid font-md" style="width: 100%; margin-bottom: 15px;">
            <i class='bx bx-map' style="margin-right: 8px;"></i> Locate Me
        </button>


        <form class="custom-form">
            <div class="input-box">
                <input type="text" id="street" placeholder="1234 Main St" class="form-control" />
                <input type="text" id="apartment" placeholder="Apartment, studio, or floor" class="form-control" />
            </div>

            <div class="input-box">
                <select id="city" class="select form-control">
                    <option disabled selected value="">City</option>
                </select>
            </div>

            <div class="input-box">
                <select id="state" class="select form-control">
                    <option disabled selected value="">State</option>
                </select>
            </div>

            <div class="input-box mb-0">
                <input type="number" id="zip" placeholder="Zip" class="form-control" />
            </div>

            <div class="input-box">
                <input type="text" id="latitude" placeholder="Latitude" class="form-control" readonly />
                <input type="text" id="longitude" placeholder="Longitude" class="form-control" readonly />
            </div>
        </form>
    </div>

    <div class="offcanvas-footer">
        <div class="btn-box">
            <button class="btn-outline font-md" data-bs-dismiss="offcanvas" aria-label="Close">Close</button>
            <button class="btn-solid font-md" data-bs-dismiss="offcanvas" aria-label="Close">Add</button>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Default Zamboanga City coordinates
    const zamboangaCityCoords = [6.9214, 122.0790]; // Latitude, Longitude for Zamboanga City

    // Initialize Leaflet map centered on Zamboanga City
    const map = L.map('map').setView(zamboangaCityCoords, 14); // Zoom level 14

    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Create a draggable marker, initially positioned at Zamboanga City center
    const marker = L.marker(zamboangaCityCoords, {
        draggable: true
    }).addTo(map);

    // Function to fetch and display the address using reverse geocoding
    function fetchAddress(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
            .then(response => response.json())
            .then(data => {
                const address = data.address;

                // Populate form fields with the address data
                document.getElementById('street').value = address.road || '';
                document.getElementById('apartment').value = ''; // Apartment left for manual input
                document.getElementById('city').innerHTML =
                    `<option selected>${address.city || address.town || ''}</option>`;
                document.getElementById('state').innerHTML =
                    `<option selected>${address.state || ''}</option>`;
                document.getElementById('zip').value = address.postcode || '';

                // Update latitude and longitude fields
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
            })
            .catch(error => console.error('Error fetching address:', error));
    }

    // Function to get user's current location and set marker
    function locateUser() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const {
                        latitude,
                        longitude
                    } = position.coords;
                    map.setView([latitude, longitude], 15); // Zoom in on user's location
                    marker.setLatLng([latitude, longitude]); // Move marker to user's location
                    fetchAddress(latitude, longitude); // Fetch address for user's location
                },
                (error) => {
                    console.error('Error getting location:', error);
                    alert(
                        "Unable to retrieve location. Please allow location access or select a location manually."
                    );
                }
            );
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    // Event listener for the "Locate Me" button
    document.getElementById('locate-me').addEventListener('click', locateUser);

    // Update address fields and lat/lng when marker is dragged
    marker.on('dragend', function() {
        const position = marker.getLatLng();
        fetchAddress(position.lat, position.lng);
    });

    // Add click event to map to move marker and fetch address
    map.on('click', function(event) {
        const {
            lat,
            lng
        } = event.latlng;
        marker.setLatLng([lat, lng]);
        fetchAddress(lat, lng);
    });
});
</script>