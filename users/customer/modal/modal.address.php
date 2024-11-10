<style>
/* Centering container and adding spacing */
.radio-wrapper {
    display: flex;
    justify-content: center;
    gap: 15px;
    align-items: center;
    margin-bottom: 15px;
}

/* Rectangular styling for labels, smaller in height but wider */
.radio-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 14px;
    color: #333;
    font-weight: 600;
    cursor: pointer;
    padding: 8px;
    border-radius: 8px;
    box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s, background-color 0.3s ease;
    width: 100px;
    height: 60px;
    text-align: center;
}

/* Hover effect */
.radio-label:hover {
    background-color: #f7f7f7;
    transform: scale(1.03);
}

/* Hide native radio input */
.radio-label input[type="radio"] {
    display: none;
}

/* Custom circle for checked/unchecked state */
.radio-label .radio-custom {
    width: 18px;
    height: 18px;
    border: 2px solid #7A1E1E;
    /* Primary theme color */
    border-radius: 50%;
    margin-bottom: 6px;
    position: relative;
    background-color: #fff;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

/* Inner dot for checked state */
.radio-label .radio-custom:after {
    content: "";
    width: 8px;
    height: 8px;
    background-color: #7A1E1E;
    border-radius: 50%;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0);
    transition: transform 0.3s ease;
}

.radio-label input[type="radio"]:checked+.radio-custom {
    border-color: #7A1E1E;
    background-color: #7A1E1E;
}

.radio-label input[type="radio"]:checked+.radio-custom:after {
    transform: translate(-50%, -50%) scale(1);
}

/* Required border highlight if not selected */
.radio-wrapper.required .radio-label input[type="radio"]:required:invalid+.radio-custom {
    border-color: #d9534f;
    /* Red color for required */
}

/* Error message styling */
.radio-wrapper .error-message {
    font-size: 12px;
    color: #d9534f;
    text-align: center;
    display: none;
}

.radio-wrapper.required .radio-label input[type="radio"]:required:invalid~.error-message {
    display: block;
    margin-top: 5px;
}
</style>
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
            <!-- Radio Buttons for Address Type -->

            <div class="radio-wrapper required">
                <label class="radio-label">
                    <input type="radio" name="addressType" value="Home" required />
                    <span class="radio-custom"></span>
                    Home
                </label>
                <label class="radio-label">
                    <input type="radio" name="addressType" value="Work" required />
                    <span class="radio-custom"></span>
                    Work
                </label>
                <div class="error-message">Please select an address type.</div>
            </div>

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
            <button id="add-address-btn" class="btn-solid font-md" aria-label="Close">Add</button>
            <!-- Added id here -->
        </div>
    </div>

</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const zamboangaCityCoords = [6.9214, 122.0790];
    const map = L.map('map').setView(zamboangaCityCoords, 14);
    const locateButton = document.getElementById('locate-me');

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    const marker = L.marker(zamboangaCityCoords, { draggable: true }).addTo(map);

    // Function to update button text while locating
    function toggleLocateButton(loading) {
        if (loading) {
            locateButton.textContent = 'Please wait, locating!';
            locateButton.disabled = true;
        } else {
            locateButton.textContent = 'Locate Me';
            locateButton.disabled = false;
        }
    }

    function fetchAddress(lat, lng) {
        toggleLocateButton(true); // Start loading
        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
            .then(response => response.json())
            .then(data => {
                const address = data.address;
                document.getElementById('street').value = address.road || '';
                document.getElementById('apartment').value = '';
                document.getElementById('city').innerHTML = `<option selected>${address.city || address.town || ''}</option>`;
                document.getElementById('state').innerHTML = `<option selected>${address.state || ''}</option>`;
                document.getElementById('zip').value = address.postcode || '';
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                toggleLocateButton(false); // Stop loading
            })
            .catch(error => {
                console.error('Error fetching address:', error);
                toggleLocateButton(false); // Stop loading on error
            });
    }

    function locateUser() {
        toggleLocateButton(true); // Start loading
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;
                    map.setView([latitude, longitude], 15);
                    marker.setLatLng([latitude, longitude]);
                    fetchAddress(latitude, longitude);
                },
                (error) => {
                    console.error('Error getting location:', error);
                    alert("Unable to retrieve location. Please allow location access or select a location manually.");
                    toggleLocateButton(false); // Stop loading on error
                }
            );
        } else {
            alert("Geolocation is not supported by this browser.");
            toggleLocateButton(false); // Stop loading if geolocation isn't supported
        }
    }

    locateButton.addEventListener('click', locateUser);

    marker.on('dragend', function() {
        const position = marker.getLatLng();
        fetchAddress(position.lat, position.lng);
    });

    map.on('click', function(event) {
        const { lat, lng } = event.latlng;
        marker.setLatLng([lat, lng]);
        fetchAddress(lat, lng);
    });

    // Add address submission functionality
    document.getElementById('add-address-btn').addEventListener('click', function(event) {
        event.preventDefault();

        const addressData = {
            street: document.getElementById('street').value,
            apartment: document.getElementById('apartment').value,
            city: document.getElementById('city').value,
            state: document.getElementById('state').value,
            zip: document.getElementById('zip').value,
            latitude: document.getElementById('latitude').value,
            longitude: document.getElementById('longitude').value,
            label: document.querySelector('input[name="addressType"]:checked').value
        };

        fetch('functions/add_address.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(addressData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload(); // Reload page to update address list
                } else {
                    alert("Error adding address: " + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    });
});
</script>
