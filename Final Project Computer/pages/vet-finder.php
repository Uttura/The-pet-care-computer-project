<?php
// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header('Location: ?page=login');
    exit();
}

// Get vets from database
try {
    $stmt = $pdo->prepare("SELECT * FROM vets");
    $stmt->execute();
    $vets = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $vets = [];
}

// Update sample vet data with Nepali locations
$sampleVets = [
    [
        'id' => 1,
        'name' => 'Animal Medical Center',
        'area' => 'Kathmandu',
        'address' => 'Chabahil, Kathmandu',
        'phone' => '01-4478177',
        'mobile' => '984-1234567',
        'hours' => [
            'Sun-Fri' => '9:00 AM - 6:00 PM',
            'Sat' => '10:00 AM - 4:00 PM'
        ],
        'services' => [
            'General Checkup',
            'Vaccination',
            'Surgery',
            'Pet Grooming',
            'Emergency Care'
        ],
        'rating' => 4.5,
        'reviews' => 128,
        'specialties' => ['Dogs', 'Cats', 'Birds'],
        'emergency' => true,
        'price_range' => 'Rs.500-2000'
    ],
    [
        'id' => 2,
        'name' => 'Central Veterinary Hospital',
        'area' => 'Tripureshwor',
        'address' => 'Tripureshwor, Kathmandu',
        'phone' => '01-4251369',
        'mobile' => '984-9876543',
        'hours' => [
            'Sun-Fri' => '8:00 AM - 5:00 PM',
            'Sat' => '9:00 AM - 3:00 PM'
        ],
        'services' => [
            'General Treatment',
            'Surgery',
            'Laboratory Services',
            'X-Ray',
            'Ultrasound'
        ],
        'rating' => 4.3,
        'reviews' => 95,
        'specialties' => ['All Pets', 'Livestock'],
        'emergency' => true,
        'price_range' => 'Rs.300-1500'
    ],
    [
        'id' => 3,
        'name' => 'Nepal Veterinary Clinic',
        'area' => 'Lalitpur',
        'address' => 'Kupondole, Lalitpur',
        'phone' => '01-5555555',
        'mobile' => '984-5555555',
        'hours' => [
            'Sun-Fri' => '9:00 AM - 7:00 PM',
            'Sat' => '10:00 AM - 4:00 PM'
        ],
        'services' => [
            'Pet Care',
            'Vaccination',
            'Dental Care',
            'Surgery',
            'Pet Food'
        ],
        'rating' => 4.7,
        'reviews' => 156,
        'specialties' => ['Dogs', 'Cats', 'Exotic Pets'],
        'emergency' => false,
        'price_range' => 'Rs.400-2500'
    ]
];

// Later, replace with actual database query
// $stmt = $pdo->query("SELECT * FROM vets ORDER BY rating DESC");
// $vets = $stmt->fetchAll();
$vets = $sampleVets;
?>

<div class="vet-finder-container">
    <h2>Find a Veterinarian</h2>
    
    <div class="search-container">
        <input type="text" id="searchLocation" placeholder="Enter your location" class="search-input">
        <button onclick="searchLocation()" class="search-button">
            <i class="fas fa-search"></i> Search
        </button>
    </div>

    <div class="vet-finder-grid">
        <div class="map-container">
            <div id="map"></div>
            <div class="current-location-info">
                <h3>Your Location</h3>
                <p id="current-address">Detecting location...</p>
                <p id="accuracy-info"></p>
            </div>
        </div>
        
        <div class="vets-list">
            <div class="list-header">
                <h3>Veterinary Clinics in Kathmandu</h3>
                <div class="filters">
                    <select id="areaFilter" onchange="filterVets()">
                        <option value="">All Areas</option>
                        <option value="Kathmandu">Kathmandu</option>
                        <option value="Lalitpur">Lalitpur</option>
                        <option value="Bhaktapur">Bhaktapur</option>
                        <option value="Pokhara">Pokhara</option>
                        <option value="Biratnagar">Biratnagar</option>
                        <option value="Birgunj">Birgunj</option>
                    </select>
                    <select id="sortBy" onchange="sortVets(this.value)">
                        <option value="rating">Highest Rated</option>
                        <option value="price">Price: Low to High</option>
                        <option value="reviews">Most Reviewed</option>
                    </select>
                </div>
            </div>
            
            <?php foreach ($vets as $vet): ?>
            <div class="vet-card" 
                 data-rating="<?php echo $vet['rating']; ?>"
                 data-name="<?php echo htmlspecialchars($vet['name']); ?>"
                 data-area="<?php echo $vet['area']; ?>">
                <div class="vet-card-header">
                    <h3><?php echo htmlspecialchars($vet['name']); ?></h3>
                    <span class="area-badge"><?php echo htmlspecialchars($vet['area']); ?></span>
                    <?php if($vet['emergency']): ?>
                        <span class="emergency-badge">24/7 Emergency</span>
                    <?php endif; ?>
                </div>
                
                <div class="vet-info">
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($vet['address']); ?></p>
                    <p>
                        <i class="fas fa-phone"></i> 
                        <a href="tel:<?php echo $vet['phone']; ?>"><?php echo $vet['phone']; ?></a>
                        <?php if(isset($vet['mobile'])): ?>
                            / <a href="tel:<?php echo $vet['mobile']; ?>"><?php echo $vet['mobile']; ?></a>
                        <?php endif; ?>
                    </p>
                    
                    <div class="hours-info">
                        <i class="fas fa-clock"></i>
                        <?php foreach($vet['hours'] as $days => $time): ?>
                            <div class="schedule">
                                <span class="days"><?php echo $days; ?>:</span>
                                <span class="time"><?php echo $time; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="services-list">
                        <i class="fas fa-stethoscope"></i> Services:
                        <div class="service-tags">
                            <?php foreach($vet['services'] as $service): ?>
                                <span class="service-tag"><?php echo htmlspecialchars($service); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="specialties">
                        <i class="fas fa-paw"></i> Specialties:
                        <?php foreach($vet['specialties'] as $specialty): ?>
                            <span class="specialty-tag"><?php echo htmlspecialchars($specialty); ?></span>
                        <?php endforeach; ?>
                    </div>

                    <div class="rating-section">
                        <div class="rating">
                            <?php for($i = 0; $i < 5; $i++): ?>
                                <?php if($i < floor($vet['rating'])): ?>
                                    <i class="fas fa-star"></i>
                                <?php elseif($i < $vet['rating']): ?>
                                    <i class="fas fa-star-half-alt"></i>
                                <?php else: ?>
                                    <i class="far fa-star"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <span>(<?php echo number_format($vet['rating'], 1); ?>) </span>
                            <span class="reviews-count"><?php echo $vet['reviews']; ?> reviews</span>
                        </div>
                    </div>

                    <p class="price-range">
                        <i class="fas fa-tag"></i> 
                        Consultation Fee: <?php echo $vet['price_range']; ?>
                    </p>
                </div>
                
                <div class="vet-actions">
                    <button class="btn-directions" onclick="getDirections('<?php echo htmlspecialchars($vet['address']); ?>')">
                        <i class="fas fa-directions"></i> Get Directions
                    </button>
                    <button class="btn-book" onclick="bookAppointment(<?php echo $vet['id']; ?>)">
                        <i class="fas fa-calendar-alt"></i> Book Appointment
                    </button>
                    <?php if(isset($vet['website'])): ?>
                        <a href="http://<?php echo $vet['website']; ?>" target="_blank" class="btn-website">
                            <i class="fas fa-globe"></i> Visit Website
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Add Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
let map;
let markers = [];
const defaultLocation = [27.7172, 85.3240]; // Kathmandu coordinates

function initMap() {
    // Initialize the map
    map = L.map('map').setView(defaultLocation, 15);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Get user's location with high accuracy
    if (navigator.geolocation) {
        const locationOptions = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        };

        showLoadingIndicator();

        navigator.geolocation.getCurrentPosition(
            (position) => {
                hideLoadingIndicator();
                const pos = [position.coords.latitude, position.coords.longitude];
                
                // Update map view and marker
                map.setView(pos, 16);
                updateUserMarker(pos, position.coords.accuracy);

                // Update accuracy info
                const accuracyInfo = document.getElementById('accuracy-info');
                if (accuracyInfo) {
                    accuracyInfo.textContent = `Accuracy: ±${Math.round(position.coords.accuracy)} meters`;
                }
                
                // Update address display
                reverseGeocode(pos[0], pos[1], (address) => {
                    document.getElementById('current-address').textContent = address;
                });
            },
            handleLocationError,
            locationOptions
        );
    }
}

function updateUserMarker(position, accuracy) {
    // Clear existing markers
    clearMarkers();

    // Add user marker
    addUserMarker(position);

    // Add accuracy circle
    showAccuracyRadius(position, accuracy);
}

function addUserMarker(position) {
    // Create custom icon for user location
    const userIcon = L.divIcon({
        html: '<i class="fas fa-user-circle" style="color: #2ecc71; font-size: 24px;"></i>',
        className: 'user-marker',
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });

    const marker = L.marker(position, {
        icon: userIcon,
        title: 'Your Location'
    }).addTo(map);
    
    markers.push(marker);
}

function addVetMarker(position, title) {
    // Create custom icon for vet locations
    const vetIcon = L.divIcon({
        html: '<i class="fas fa-clinic-medical" style="color: #e74c3c; font-size: 24px;"></i>',
        className: 'vet-marker',
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });

    const marker = L.marker(position, {
        icon: vetIcon,
        title: title
    }).addTo(map);
    
    marker.bindPopup(`<b>${title}</b>`);
    markers.push(marker);
}

function searchLocation() {
    const input = document.getElementById('searchLocation');
    const geocoder = L.Control.Geocoder.nominatim();
    
    geocoder.geocode(input.value, results => {
        if (results.length > 0) {
            const result = results[0];
            const latlng = result.center;
            map.setView(latlng, 13);
            clearMarkers();
            addUserMarker([latlng.lat, latlng.lng]);
            showNearbyVets([latlng.lat, latlng.lng]);
        } else {
            alert('Location not found');
        }
    });
}

function clearMarkers() {
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
}

function showNearbyVets(position) {
    // We don't need to calculate distances anymore since we're not showing vet locations on map
    const vetCards = document.querySelectorAll('.vet-card');
    vetCards.forEach(card => {
        card.style.display = 'block'; // Show all vet cards
    });

    // Update current location info
    const locationInfo = document.getElementById('current-address');
    if (locationInfo) {
        reverseGeocode(position[0], position[1], (address) => {
            locationInfo.textContent = address;
        });
    }
}

function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Radius of the earth in km
    const dLat = deg2rad(lat2 - lat1);
    const dLon = deg2rad(lon2 - lon1);
    const a = 
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
        Math.sin(dLon/2) * Math.sin(dLon/2); 
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
    const d = R * c; // Distance in km
    return d;
}

function deg2rad(deg) {
    return deg * (Math.PI/180);
}

function getDistanceCategory(distance) {
    if (distance <= 2) {
        return {
            color: '#2ecc71', // Green
            icon: 'fa-clinic-medical',
            label: 'Very Close'
        };
    } else if (distance <= 5) {
        return {
            color: '#f1c40f', // Yellow
            icon: 'fa-hospital',
            label: 'Nearby'
        };
    } else {
        return {
            color: '#e74c3c', // Red
            icon: 'fa-hospital-alt',
            label: 'Further'
        };
    }
}

function addVetMarker(position, title, distanceInfo) {
    // Create custom icon for vet locations
    const vetIcon = L.divIcon({
        html: `<i class="fas ${distanceInfo.icon}" style="color: ${distanceInfo.color}; font-size: 24px;"></i>`,
        className: 'vet-marker',
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });

    const marker = L.marker(position, {
        icon: vetIcon,
        title: title
    }).addTo(map);
    
    marker.bindPopup(`
        <div class="popup-content">
            <h4>${title}</h4>
            <span class="distance-label" style="color: ${distanceInfo.color}">
                ${distanceInfo.label}
            </span>
        </div>
    `);
    markers.push(marker);
}

function bookAppointment(vetId) {
    // Add booking logic
    alert('Booking functionality will be implemented soon!');
}

// Add these new functions
function showLoadingIndicator() {
    const loading = document.createElement('div');
    loading.id = 'location-loading';
    loading.innerHTML = `
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <span>Getting your location...</span>
        </div>
    `;
    document.querySelector('.map-container').appendChild(loading);
}

function hideLoadingIndicator() {
    const loading = document.getElementById('location-loading');
    if (loading) {
        loading.remove();
    }
}

function handleLocationError(error) {
    let message = '';
    switch(error.code) {
        case error.PERMISSION_DENIED:
            message = "Location access was denied. Please enable location services to find nearby vets.";
            break;
        case error.POSITION_UNAVAILABLE:
            message = "Location information is unavailable. Please try again or enter your location manually.";
            break;
        case error.TIMEOUT:
            message = "Location request timed out. Please try again or enter your location manually.";
            break;
        default:
            message = "An unknown error occurred. Please try again or enter your location manually.";
    }
    
    showErrorMessage(message);
}

function showErrorMessage(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'location-error';
    errorDiv.innerHTML = `
        <div class="error-content">
            <i class="fas fa-exclamation-circle"></i>
            <p>${message}</p>
        </div>
    `;
    document.querySelector('.map-container').appendChild(errorDiv);
    
    // Remove error message after 5 seconds
    setTimeout(() => {
        errorDiv.remove();
    }, 5000);
}

function showAccuracyRadius(position, accuracy) {
    // Add a circle to show location accuracy
    const accuracyCircle = L.circle(position, {
        radius: accuracy,
        color: '#2ecc71',
        fillColor: '#2ecc71',
        fillOpacity: 0.1,
        weight: 1
    }).addTo(map);
    markers.push(accuracyCircle);
}

function reverseGeocode(lat, lon, callback) {
    const geocoder = L.Control.Geocoder.nominatim();
    geocoder.reverse(
        { lat: lat, lng: lon },
        map.getZoom(),
        results => {
            if (results.length > 0) {
                callback(results[0].name);
            }
        }
    );
}

// Initialize map when the page loads
window.onload = initMap;

// Add this function for sorting
function sortVets(criteria) {
    const vetsList = document.querySelector('.vets-list');
    const cards = Array.from(vetsList.getElementsByClassName('vet-card'));
    
    cards.sort((a, b) => {
        switch(criteria) {
            case 'distance':
                return parseFloat(a.querySelector('.distance-badge').textContent) -
                       parseFloat(b.querySelector('.distance-badge').textContent);
            case 'rating':
                return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
            case 'name':
                return a.dataset.name.localeCompare(b.dataset.name);
            default:
                return 0;
        }
    });
    
    cards.forEach(card => vetsList.appendChild(card));
}

// Add this function for directions
function getDirections(address) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;
            // Use address instead of coordinates for destination
            const url = `https://www.openstreetmap.org/directions?from=${userLat},${userLng}&to=${encodeURIComponent(address)}`;
            window.open(url, '_blank');
        });
    }
}

// Update the vet list display to show distance from user
function updateVetDistances(userPosition) {
    const vetCards = document.querySelectorAll('.vet-card');
    vetCards.forEach(card => {
        const distance = calculateDistance(
            userPosition[0],
            userPosition[1],
            parseFloat(card.dataset.lat),
            parseFloat(card.dataset.lng)
        );
        
        const distanceBadge = card.querySelector('.distance-badge');
        if (distanceBadge) {
            distanceBadge.textContent = `${distance.toFixed(1)} km`;
        }
    });
}

// Add area filtering function
function filterVets() {
    const selectedArea = document.getElementById('areaFilter').value;
    const vetCards = document.querySelectorAll('.vet-card');
    
    vetCards.forEach(card => {
        const area = card.dataset.area;
        if (!selectedArea || area === selectedArea) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Update price display format
function formatPrice(price) {
    return price.replace('Rs.', 'रु ');
}
</script>

<style>
.vet-finder-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.search-container {
    margin-bottom: 20px;
    display: flex;
    gap: 10px;
}

.search-input {
    flex: 1;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
}

.search-button {
    padding: 10px 20px;
    background: #2ecc71;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.search-button:hover {
    background: #27ae60;
}

.vet-finder-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 20px;
    height: 600px;
}

.map-container {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: relative;
}

#map {
    height: 100%;
    width: 100%;
}

.vets-list {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    height: 600px;
    overflow-y: auto;
}

.list-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.sort-options select {
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ddd;
    background: white;
}

.vet-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.vet-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.vet-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.vet-info {
    margin-bottom: 15px;
}

.vet-info p {
    margin: 8px 0;
    color: #666;
}

.vet-info i {
    width: 20px;
    color: #2ecc71;
}

.vet-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.btn-directions {
    padding: 8px;
    background: #f1c40f;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.btn-directions:hover {
    background: #f39c12;
}

.rating {
    margin: 10px 0;
}

.rating i {
    color: #f1c40f;
    margin-right: 2px;
}

/* Custom marker styles */
.user-marker, .vet-marker {
    background: none;
    border: none;
}

.distance-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    padding: 4px 8px;
    border-radius: 12px;
    color: white;
    font-size: 0.8rem;
    font-weight: bold;
}

.popup-content {
    text-align: center;
    padding: 5px;
}

.popup-content h4 {
    margin: 0 0 5px 0;
    color: #2c3e50;
}

.distance-label {
    font-size: 0.9rem;
    font-weight: bold;
}

/* Legend styles */
.map-legend {
    position: absolute;
    bottom: 20px;
    right: 20px;
    background: white;
    padding: 10px;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    z-index: 1000;
}

.legend-item {
    display: flex;
    align-items: center;
    margin: 5px 0;
    font-size: 0.9rem;
}

.legend-color {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    margin-right: 8px;
}

@media (max-width: 768px) {
    .vet-actions {
        grid-template-columns: 1fr;
    }
}

/* Add to existing styles */
.loading-spinner {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 15px 25px;
    border-radius: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 1000;
}

.loading-spinner i {
    color: #2ecc71;
    font-size: 1.2rem;
}

.location-error {
    position: absolute;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: #fff3cd;
    padding: 10px 20px;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    z-index: 1000;
    animation: slideDown 0.3s ease;
}

.error-content {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #856404;
}

.error-content i {
    font-size: 1.2rem;
}

@keyframes slideDown {
    from {
        transform: translate(-50%, -100%);
        opacity: 0;
    }
    to {
        transform: translate(-50%, 0);
        opacity: 1;
    }
}

/* Add to existing styles */
.emergency-badge {
    background: #e74c3c;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: bold;
    margin-left: 10px;
}

.hours-info {
    margin: 10px 0;
}

.schedule {
    display: flex;
    justify-content: space-between;
    margin-left: 25px;
    color: #666;
}

.service-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 5px;
    margin-left: 25px;
}

.service-tag {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
}

.specialty-tag {
    background: #e3f2fd;
    color: #1565c0;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    margin-right: 5px;
}

.reviews-count {
    color: #666;
    font-size: 0.9rem;
    margin-left: 5px;
}

.btn-website {
    text-decoration: none;
    text-align: center;
    padding: 8px;
    background: #3498db;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.btn-website:hover {
    background: #2980b9;
}

/* Add these styles */
.current-location-info {
    position: absolute;
    bottom: 20px;
    left: 20px;
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    z-index: 1000;
    max-width: 300px;
}

.current-location-info h3 {
    margin: 0 0 10px 0;
    color: #2c3e50;
}

.current-location-info p {
    margin: 5px 0;
    color: #666;
    font-size: 0.9rem;
}

#accuracy-info {
    color: #2ecc71;
    font-weight: bold;
}

.filters {
    display: flex;
    gap: 10px;
}

.filters select {
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ddd;
    background: white;
}

.price-range {
    color: #666;
    font-size: 0.9rem;
    margin-top: 5px;
}

.area-badge {
    background: #3498db;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    margin-left: 10px;
}
</style>

<!-- Add legend to map container -->
<div class="map-legend">
    <div class="legend-item">
        <div class="legend-color" style="background: #2ecc71;"></div>
        <span>Within 2km</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background: #f1c40f;"></div>
        <span>2-5km</span>
    </div>
    <div class="legend-item">
        <div class="legend-color" style="background: #e74c3c;"></div>
        <span>5-10km</span>
    </div>
</div> 