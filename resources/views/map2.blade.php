@extends('layouts.web')

@section('head')
<style>
    #map {
        height: 90vh;
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
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Map Settings</h3>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('searchLocations') }}" onsubmit="return false;">
                                @csrf
                                <div class="input-group input-group-sm">
                                    <input type="text" id="query" name="q" class="form-control" placeholder="Search locations" required>
                                    <span class="input-group-append">
                                        <button type="button" class="btn btn-info btn-flat" onclick="search();">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </form>

                            <a class="btn btn-success btn-sm w-100 mt-3" href="{{ route('map') }}">Search within my area <i class="bi bi-pin"></i></a>
                        </div>
                    </div>

                    <!-- Results Container -->
                    <div id="results-container" class="mt-3"></div>
                </div>

                <!-- Map Display Card -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Map Display</h3>
                        </div>
                        <div class="card-body">
                            <div id="map"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let map;
        let markers = []; // Array to store all markers
        let infoWindows = {}; // Store info windows for each marker
        let activeInfoWindow = null; // Keep track of the currently active info window

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

                        // Center the map on the user's location
                        map.setCenter(userLocation);

                        // Add a marker for the user's current location
                        new google.maps.Marker({
                            position: userLocation,
                            map: map,
                            title: "Your Location",
                        });

                        // Fetch nearby locations based on the user's location
                        fetchNearbyLocations(userLocation.lat, userLocation.lng);
                    },
                    (error) => {
                        console.error("Error getting location:", error);
                        alert("Unable to retrieve your location. Falling back to default center.");
                        fetchNearbyLocations(3.078716, 101.493990); // Fetch locations near the default center
                    }
                );
            } else {
                alert("Geolocation is not supported by your browser.");
                fetchNearbyLocations(3.078716, 101.493990); // Fetch locations near the default center
            }
        };

        // Generate the services list dynamically
        function generateServicesList(servicesJson) {
            try {
                const services = JSON.parse(servicesJson)?.services; // Parse the JSON string and extract services array
                if (!Array.isArray(services) || services.length === 0) {
                    return '<li>No services listed</li>'; // Fallback if the array is empty
                }

                // Generate list items for each service
                return services.map(service => `<li>${service}</li>`).join('');
            } catch (error) {
                console.error('Error parsing services JSON:', error);
                return '<li>Invalid services data</li>'; // Fallback if parsing fails
            }
        }

        // Fetch nearby locations
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
                            <p>
                                ${starRating(location.reviews_avg_rating)}
                                <small>(${location.reviews_avg_rating ?? 'No Rating'})</small>
                            </p>
                            <p>${location.address}<br/>
                            Operation Hour: ${location.operation_hour}</p>
                            <a href="https://www.google.com/maps/dir/?api=1&destination=${location.latitude},${location.longitude}"
                               target="_blank"
                               class="btn btn-xs btn-success"><i class="bi bi-compass"></i> Navigate</a>
                        `;

                        const infoWindow = new google.maps.InfoWindow({ content: infoWindowContent });
                        infoWindows[location.name] = { marker, infoWindow };

                        google.maps.event.addListener(marker, 'click', () => {
                            if (activeInfoWindow) activeInfoWindow.close(); // Close the currently open info window
                            infoWindow.open(map, marker);
                            activeInfoWindow = infoWindow;
                        });

                        // Create a result card
                        const resultCard = document.createElement('div');
                        resultCard.className = "card mb-2";
                        resultCard.innerHTML = `
                            <div class="card-body">
                                <h5 class="card-title"><b>${location.name}</b></h5><br/>
                                <p class="card-text">
                                    ${starRating(location.reviews_avg_rating)} <small>(${location.reviews_avg_rating ?? 'No Rating'})</small><br/>
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
                            </div>
                        `;
                        resultsContainer.appendChild(resultCard);
                    });

                    // Adjust map bounds to fit markers
                    if (data.length > 0) {
                        map.fitBounds(bounds);
                    }
                })
                .catch(error => console.error("Error fetching locations:", error));
        }

        // Search functionality
        function search() {
            let query = document.getElementById('query').value;
            const url = new URL('/api/search', window.location.origin);
            url.searchParams.append('q', query); // Add query parameter

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    clearMarkers(); // Clear existing markers
                    const bounds = new google.maps.LatLngBounds();
                    const resultsContainer = document.getElementById('results-container');
                    resultsContainer.innerHTML = ""; // Clear previous results

                    if (data.length === 0) {
                        resultsContainer.innerHTML = "<p>No results found.</p>";
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
                            <p>
                                ${starRating(location.reviews_avg_rating)}
                                <small>(${location.reviews_avg_rating ?? 'No Rating'})</small>
                            </p>
                            <p>${location.address}<br/>
                            Operation Hour: ${location.operation_hour}</p>
                            <a href="https://www.google.com/maps/dir/?api=1&destination=${location.latitude},${location.longitude}"
                               target="_blank"
                               class="btn btn-xs btn-success"><i class="bi bi-compass"></i> Navigate</a>
                        `;

                        const infoWindow = new google.maps.InfoWindow({ content: infoWindowContent });
                        infoWindows[location.name] = { marker, infoWindow };

                        google.maps.event.addListener(marker, 'click', () => {
                            if (activeInfoWindow) activeInfoWindow.close(); // Close the currently open info window
                            infoWindow.open(map, marker);
                            activeInfoWindow = infoWindow;
                        });

                        // Create a result card
                        const resultCard = document.createElement('div');
                        resultCard.className = "card mb-2";
                        resultCard.innerHTML = `
                            <div class="card-body">
                                <h5 class="card-title"><b>${location.name}</b></h5><br/>
                                <p class="card-text">
                                    ${starRating(location.reviews_avg_rating)} <small>(${location.reviews_avg_rating ?? 'No Rating'})</small><br/>
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
                            </div>
                        `;
                        resultsContainer.appendChild(resultCard);
                    });

                    // Adjust map bounds to fit search results
                    if (data.length > 0) {
                        map.fitBounds(bounds);
                    }
                })
                .catch(error => console.error("Error fetching search results:", error));
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
    </script>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_API_KEY') }}&callback=initMap&libraries=maps"></script>
</main>
@endsection
