@extends('layouts.web')

@section('head')
<style>
    #map {
        height: 75vh;
        width: 100%;
    }

    .star-rating {
        display: inline-flex;
        align-items: center;
    }
    .star-rating i {
        margin-right: 2px;
    }

    .text-warning {
        color: #ffc107; /* Bootstrap's warning yellow */
    }

    .btn-xs {
        padding: 0.25rem 0.5rem; /* Adjust padding */
        font-size: 0.75rem; /* Smaller font size */
        line-height: 1; /* Adjust line height */
    }
</style>
@endsection

@section('body')
<main class="app-main">
    <div class="app-content">
        <div class="container-fluid" style="margin-top: 20px;">
            <div class="row">
                <!-- Map Settings Card -->
                <div class="col-md-3">
                    <div class="card bg-success-subtle">
                        <div class="card-header bg-success">
                            <h3 class="card-title text-white">Map Settings</h3>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('searchLocations') }}" onsubmit="search(); return false;">
                                @csrf
                                <div class="input-group input-group-sm">
                                    <input type="text" id="query" name="q" class="form-control" placeholder="Search locations" required>
                                    <button type="button" class="btn btn-info btn-flat" onclick="search();">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>

                                <div class="form-group mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="f[]" value="Paper">
                                        <label class="form-check-label">Paper</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="f[]" value="Metal">
                                        <label class="form-check-label">Metal</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="f[]" value="Fabric">
                                        <label class="form-check-label">Fabric</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="f[]" value="Glass">
                                        <label class="form-check-label">Glass</label>
                                    </div>
                                </div>
                            </form>

                            <a class="btn btn-success btn-sm w-100 mt-2" href="{{ route('map') }}">Search within my area <i class="bi bi-pin"></i></a>
                        </div>
                    </div>

                    <!-- Results Container -->
                    <div id="results-container" class="mt-3" style="height: 60vh; padding: 10px; overflow: auto;"></div>
                </div>

                <!-- Map Display Card -->
                <div class="col-md-9">
                    <div class="card bg-success-subtle">
                        <div class="card-header bg-success">
                            <h3 class="card-title text-white">Map Display</h3>
                        </div>
                        <div class="card-body">
                            <div id="map"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal modal-lg fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h1 class="modal-title fs-5" id="reviewModalLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-success-subtle">
                    <div class="timeline" id="reviews-container"> <!-- timeline time label --> <!-- timeline item -->
                        <div> <i class="timeline-icon bi bi-chat-text-fill text-bg-warning"></i>
                            <div class="timeline-item"> <span class="time"> <i class="bi bi-clock-fill"></i> 27 mins ago
                                </span>
                                <h3 class="timeline-header"> <a href="#">Jay White</a> commented on your post
                                </h3>
                                <div class="timeline-body">
                                    Take me to your leader! Switzerland is small and
                                    neutral! We are more like Germany, ambitious and
                                    misunderstood!
                                </div>
                            </div>
                        </div> <!-- END timeline item -->
                        <div> <i class="timeline-icon bi bi-clock-fill text-bg-secondary"></i> </div>
                    </div>
                </div>
                {{-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div> --}}
            </div>
        </div>
    </div>

    <script>
        let map;
        let markers = []; // Array to store all markers
        let infoWindows = {}; // Store info windows for each marker
        let activeInfoWindow = null; // Keep track of the currently active info window
        let userMarker = null; // Draggable marker for user's location

        // Clear all existing markers from the map
        function clearMarkers() {
            markers.forEach(marker => marker.setMap(null));
            markers = [];
            infoWindows = {};
            activeInfoWindow = null;
        }

        // Initialize the map
        window.initMap = function () {
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 3.078716, lng: 101.493990 }, // Default center
                zoom: 15,
                mapId: "27d4cb7b44e51a1a", // Replace with your actual Map ID
            });

           // Get user's current location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };

                        console.log(`Latitude: ${userLocation.lat}, Longitude: ${userLocation.lng}`);

                        // Center the map on the user's location
                        map.setCenter(userLocation);

                        // Add a draggable marker for the user's current location
                        addDraggableMarker(userLocation);

                        // Fetch nearby locations
                        fetchNearbyLocations(userLocation.lat, userLocation.lng);
                    },
                    (error) => {
                        console.error("Error getting location:", error.message);
                        alert("Unable to retrieve your location. Falling back to default center.");

                        // Fallback to a default location
                        const defaultLocation = { lat: 3.078716, lng: 101.493990 };
                        map.setCenter(defaultLocation);
                        addDraggableMarker(defaultLocation);

                        fetchNearbyLocations(defaultLocation.lat, defaultLocation.lng);
                    },
                    {
                        enableHighAccuracy: true, // Use GPS for better accuracy
                        timeout: 10000, // Wait a maximum of 10 seconds for the location
                        maximumAge: 0, // Do not use cached location
                    }
                );
            } else {
                alert("Geolocation is not supported by your browser.");

                // Fallback to a default location
                const defaultLocation = { lat: 3.078716, lng: 101.493990 };
                map.setCenter(defaultLocation);
                addDraggableMarker(defaultLocation);

                fetchNearbyLocations(defaultLocation.lat, defaultLocation.lng);
            }
        };

        let radiusCircle = null; // Store the circle object

        // Add a circle to the map
        function addRadiusCircle(center, radius = {{ env('SEARCH_RADIUS') }}) { // Radius in meters)
            if (radiusCircle) {
                radiusCircle.setMap(null); // Remove existing circle
            }

            radiusCircle = new google.maps.Circle({
                center: center,
                radius: radius, // Radius in meters
                map: map,
                fillColor: "#ADD8E6", // Light blue fill color
                fillOpacity: 0.3, // Transparency of the circle
                strokeColor: "#0000FF", // Blue stroke color
                strokeOpacity: 0.8, // Transparency of the stroke
                strokeWeight: 2, // Stroke width
            });

            // Adjust map bounds to fit the circle
            map.fitBounds(radiusCircle.getBounds());
        }

        // Update the draggable marker function
        function addDraggableMarker(location) {
            if (userMarker) {
                userMarker.setMap(null); // Remove existing marker
            }

            userMarker = new google.maps.Marker({
                position: location,
                map: map,
                title: "Drag to set your location",
                draggable: true,
            });

            // Add a radius circle around the user's location
            addRadiusCircle(location);

            // Add a drag event listener to update the location
            userMarker.addListener("dragend", (event) => {
                const newPosition = {
                    lat: event.latLng.lat(),
                    lng: event.latLng.lng(),
                };
                console.log(`Updated Position: Latitude: ${newPosition.lat}, Longitude: ${newPosition.lng}`);

                // Update the radius circle to the new location
                addRadiusCircle(newPosition);

                // Optional: Fetch locations based on the new position
                fetchNearbyLocations(newPosition.lat, newPosition.lng);
            });
        }

        // In the fetchNearbyLocations function, ensure it does not remove or affect the circle
        function fetchNearbyLocations(latitude, longitude) {
            const url = new URL('/api/search', window.location.origin);
            url.searchParams.append('latitude', latitude);
            url.searchParams.append('longitude', longitude);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    clearMarkers(); // Clear existing markers
                    const bounds = new google.maps.LatLngBounds();
                    const resultsContainer = document.getElementById('results-container');
                    resultsContainer.innerHTML = ""; // Clear previous results

                    if (data.length === 0) {
                        resultsContainer.innerHTML = "<p>No results found.</p>";
                        return;
                    }

                    data.forEach(location => {
                        const marker = new google.maps.Marker({
                            position: {
                                lat: parseFloat(location.latitude),
                                lng: parseFloat(location.longitude),
                            },
                            map: map,
                            title: location.name,
                            label: {
                                text: location.name,
                                color: "black",
                                fontSize: "12px",
                                fontWeight: "bold",
                            },
                        });

                        markers.push(marker); // Add marker to markers array
                        bounds.extend(marker.position);

                        const infoWindowContent = `
                            <h5>${location.name}</h5>
                            <p class="card-text">
                                ${starRating(location.reviews_avg_rating)} <small>(${location.reviews_avg_rating ?? 'No Rating'}) <a href="{{ route('review.index') }}/${location.id}/fast" target="_blank">Review this</a></small>
                            </p>
                            <p>${location.address}<br/>
                            Operation Hour: ${location.operation_hour}</p>
                            <a href="https://www.google.com/maps/dir/?api=1&destination=${location.latitude},${location.longitude}"
                            target="_blank"
                            class="btn btn-xs btn-success"><i class="bi bi-compass"></i> Navigate</a>
                            <button type="button" class="btn btn-xs btn-warning" onClick="viewReview(${location.id})">
                                <i class="bi bi-chat-text"></i> Reviews
                            </button>
                        `;

                        const infoWindow = new google.maps.InfoWindow({ content: infoWindowContent });
                        infoWindows[location.name] = { marker, infoWindow };

                        google.maps.event.addListener(marker, 'click', () => {
                            if (activeInfoWindow) activeInfoWindow.close();
                            infoWindow.open(map, marker);
                            activeInfoWindow = infoWindow;
                        });

                        const resultCard = document.createElement('div');
                        resultCard.className = "card mb-2";
                        resultCard.innerHTML = `
                            <div class="card-body bg-success-subtle">
                                <h5 class="card-title"><b>${location.name}</b></h5><br/>
                                <p class="card-text">
                                    ${starRating(location.reviews_avg_rating)} <small>(${location.reviews_avg_rating ?? 'No Rating'}) <a href="{{ route('review.index') }}/${location.id}/fast" target="_blank">Review this</a></small><br/>
                                    ${location.address}
                                </p>
                                <p class="card-text">
                                    <small>
                                        Operation Hours: ${location.operation_hour}<br/>
                                        Contact No.: <a href="tel:${location.phone_no}">${location.phone_no}</a><br/>
                                        Accepted Materials:
                                        <ul>
                                            ${generateServicesList(location.services)}
                                        </ul>
                                    </small>
                                </p>
                                <button class="btn btn-xs btn-primary" onclick="focusMarker('${location.name}');">
                                    <i class="bi bi-geo-alt"></i> View on Map
                                </button>
                                <a href="https://www.google.com/maps/dir/?api=1&destination=${location.latitude},${location.longitude}"
                                target="_blank"
                                class="btn btn-xs btn-success">
                                    <i class="bi bi-compass"></i> Navigate
                                </a>
                                <button type="button" class="btn btn-xs btn-warning" onClick="viewReview(${location.id})">
                                    <i class="bi bi-chat-text"></i> Reviews
                                </button>
                            </div>
                        `;
                        resultsContainer.appendChild(resultCard);
                    });

                    if (data.length > 0) {
                        map.fitBounds(bounds);
                    }
                })
                .catch(error => console.error("Error fetching locations:", error));
        }

        // Search functionality
        function search() {
            let query = document.getElementById('query').value.trim(); // Get the search query
            let filters = []; // Collect filters from checkboxes

            // Collect selected filters from checkboxes
            document.querySelectorAll('input[name="f[]"]:checked').forEach(checkbox => {
                filters.push(checkbox.value);
            });

            // Build the URL
            const url = new URL('/api/search', window.location.origin);
            if (query !== "") {
                url.searchParams.append('q', query); // Add `q` only if it's not empty
            }
            if (filters.length > 0) {
                // Add each filter as a separate `f` parameter
                filters.forEach(filter => {
                    url.searchParams.append('f[]', filter);
                });
            }

            // Fetch data from the server
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    clearMarkers(); // Clear existing markers
                    const bounds = new google.maps.LatLngBounds();
                    const resultsContainer = document.getElementById('results-container');
                    resultsContainer.innerHTML = ""; // Clear previous results

                    if (data.length === 0) {
                        resultsContainer.innerHTML = "<p>No results found.</p>";
                        return;
                    }

                    data.forEach(location => {
                        const marker = new google.maps.Marker({
                            position: {
                                lat: parseFloat(location.latitude),
                                lng: parseFloat(location.longitude),
                            },
                            map: map,
                            title: location.name,
                            label: {
                                text: location.name,
                                color: "black",
                                fontSize: "12px",
                                fontWeight: "bold",
                            },
                        });

                        markers.push(marker); // Add marker to markers array
                        bounds.extend(marker.position);

                        const infoWindowContent = `
                            <h5>${location.name}</h5>
                            <p class="card-text">
                                ${starRating(location.reviews_avg_rating)} <small>(${location.reviews_avg_rating ?? 'No Rating'}) <a href="{{ route('review.index') }}/${location.id}/fast" target="_blank">Review this</a></small>
                            </p>
                            <p>${location.address}<br/>Operation Hour: ${location.operation_hour}</p>
                            <a href="https://www.google.com/maps/dir/?api=1&destination=${location.latitude},${location.longitude}" target="_blank" class="btn btn-xs btn-success">
                                <i class="bi bi-compass"></i> Navigate
                            </a>
                            <button type="button" class="btn btn-xs btn-warning" onClick="viewReview(${location.id})">
                                <i class="bi bi-chat-text"></i> Reviews
                            </button>
                        `;

                        const infoWindow = new google.maps.InfoWindow({ content: infoWindowContent });
                        infoWindows[location.name] = { marker, infoWindow };

                        google.maps.event.addListener(marker, 'click', () => {
                            if (activeInfoWindow) activeInfoWindow.close(); // Close the currently open info window
                            infoWindow.open(map, marker);
                            activeInfoWindow = infoWindow;
                        });

                        const resultCard = document.createElement('div');
                        resultCard.className = "card mb-2";
                        resultCard.innerHTML = `
                            <div class="card-body bg-success-subtle">
                                <h5 class="card-title"><b>${location.name}</b></h5><br/>
                                <p class="card-text">
                                    ${starRating(location.reviews_avg_rating)} <small>(${location.reviews_avg_rating ?? 'No Rating'}) <a href="{{ route('review.index') }}/${location.id}/fast" target="_blank">Review this</a></small><br/>
                                    ${location.address}
                                </p>
                                <p class="card-text">
                                    <small>
                                        Operation Hours: ${location.operation_hour}<br/>
                                        Contact No.: <a href="tel:${location.phone_no}">${location.phone_no}</a><br/>
                                        Accepted Materials:
                                        <ul>
                                            ${generateServicesList(location.services)}
                                        </ul>
                                    </small>
                                </p>
                                <button class="btn btn-xs btn-primary" onclick="focusMarker('${location.name}');">
                                    <i class="bi bi-geo-alt"></i> View on Map
                                </button>
                                <a href="https://www.google.com/maps/dir/?api=1&destination=${location.latitude},${location.longitude}"
                                   target="_blank"
                                   class="btn btn-xs btn-success">
                                    <i class="bi bi-compass"></i> Navigate
                                </a>
                                <button type="button" class="btn btn-xs btn-warning" onClick="viewReview(${location.id})">
                                    <i class="bi bi-chat-text"></i> Reviews
                                </button>
                            </div>
                        `;
                        resultsContainer.appendChild(resultCard);
                    });

                    if (data.length > 0) {
                        map.fitBounds(bounds);
                    }
                })
                .catch(error => console.error("Error fetching locations:", error));
        }

        // Generate the services list dynamically
        function generateServicesList(servicesJson) {
            try {
                const services = JSON.parse(servicesJson)?.services;
                if (!Array.isArray(services) || services.length === 0) {
                    return '<li>No services listed</li>';
                }

                return services.map(service => `<li>${service}</li>`).join('');
            } catch (error) {
                console.error('Error parsing services JSON:', error);
                return '<li>Invalid services data</li>';
            }
        }

        // Generate star ratings dynamically
        function starRating(rating) {
            if (!rating) return "No Rating";
            let stars = "";
            for (let i = 1; i <= 5; i++) {
                if (i <= Math.floor(rating)) {
                    stars += '<i class="bi bi-star-fill text-warning"></i>'; // Full star
                } else if (i - rating < 1) {
                    stars += '<i class="bi bi-star-half text-warning"></i>'; // Half star
                } else {
                    stars += '<i class="bi bi-star text-warning"></i>'; // Empty star
                }
            }
            return stars;
        }

        // Focus on a specific marker and open its info window
        function focusMarker(name) {
            const { marker, infoWindow } = infoWindows[name];
            if (activeInfoWindow) activeInfoWindow.close(); // Close the currently open info window
            map.setCenter(marker.getPosition());
            map.setZoom(17);
            infoWindow.open(map, marker);
            activeInfoWindow = infoWindow; // Update the active info window
        }

        function viewReview(id) {
            const modal = new bootstrap.Modal(document.getElementById('reviewModal'));

            let reviews = [];
            document.getElementById('reviews-container').innerHTML = "<p>Fetching reviews . . .</p>";

            // Build the URL
            const url = new URL('/api/' + id + '/getReviews', window.location.origin);

            // Fetch data from the server
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const reviewContainer = document.getElementById('reviews-container');
                    reviewContainer.innerHTML = ""; // Clear previous results

                    if (data.length === 0) {
                        document.getElementById('reviewModalLabel').innerHTML = `Reviews`;
                        reviewContainer.innerHTML = "<p>No review found.</p>";
                        return;
                    }

                    data.forEach(review => {
                        document.getElementById('reviewModalLabel').innerHTML = `Reviews for ${review.center_info.name}`;

                        // Create a Date object
                        const date = new Date(review.created_at);

                        // Extract parts of the date and time
                        const year = date.getFullYear();
                        const month = date.getMonth(); // Month index (0 = January, 1 = February, etc.)
                        const day = date.getDate();
                        const hours = date.getHours();
                        const minutes = String(date.getMinutes()).padStart(2, "0");

                        // Array of month names
                        const monthNames = [
                            "January", "February", "March", "April", "May", "June",
                            "July", "August", "September", "October", "November", "December"
                        ];

                        // Format the readable date and time
                        const readableDate = `${day} ${monthNames[month]} ${year}`;
                        const readableTime = `${(hours % 12) || 12}:${minutes} ${hours >= 12 ? "PM" : "AM"}`;

                        const resultCard = document.createElement('div');
                        resultCard.innerHTML = `
                            <i class="timeline-icon bi bi-chat-text-fill text-bg-warning"></i>
                            <div class="timeline-item"> <span class="time"> <i class="bi bi-clock-fill"></i> ${readableTime}, ${readableDate}
                                </span>
                                <h3 class="timeline-header"> <a href="#">${review.user_info.name}</a> commented
                                </h3>
                                <div class="timeline-body">
                                    ${review.review}
                                </div>
                            </div>
                        `;
                        reviewContainer.appendChild(resultCard);
                    });
                })
                .then(() => {
                    modal.show();
                })
                .catch(error => console.error("Error fetching reviews:", error));
        }
    </script>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_API_KEY') }}&callback=initMap&libraries=maps"></script>
</main>
@endsection
