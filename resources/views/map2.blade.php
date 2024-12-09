@extends('layouts.web')

@section('head')
<style>
    #map {
        height: 90vh;
        width: 100%;
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

                                <div class="input-group input-group-sm">
                                    <span class="input-group-append">
                                        <button type="button" class="btn btn-info btn-flat" onclick="search2();">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
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

        // Clear all existing markers from the map
        function clearMarkers() {
            markers.forEach(marker => marker.setMap(null));
            markers = [];
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
                    },
                    (error) => {
                        console.error("Error getting location:", error);
                        alert("Unable to retrieve your location. Falling back to default center.");
                    }
                );
            } else {
                alert("Geolocation is not supported by your browser.");
            }

            // Load initial markers
            fetch('/api/locations')
                .then(response => response.json())
                .then(data => {
                    clearMarkers();
                    const bounds = new google.maps.LatLngBounds();

                    data.forEach(location => {
                        // const marker = new google.maps.Marker({
                        //     position: {
                        //         lat: parseFloat(location.latitude),
                        //         lng: parseFloat(location.longitude),
                        //     },
                        //     map: map,
                        //     title: location.name,
                        // });

                        const marker = new google.maps.Marker({
                            position: {
                                lat: parseFloat(location.latitude),
                                lng: parseFloat(location.longitude),
                            },
                            map: map,
                            title: location.name,
                            label: {
                                text: location.name, // Display the location's name
                                color: "black", // Set label color
                                fontSize: "12px", // Customize font size
                                fontWeight: "bold", // Make it bold if needed
                            },
                        });

                        markers.push(marker); // Add marker to markers array
                        bounds.extend(marker.position);

                        google.maps.event.addListener(marker, 'click', () => {
                            const infoWindow = new google.maps.InfoWindow({
                                content: `<h5>${location.name}</h5>
                                            <p>${location.address}<br/>
                                            Operation Hour: ${location.operation_hour}</p>`,
                            });
                            infoWindow.open(map, marker);
                        });
                    });

                    // Adjust map bounds to fit markers
                    if (data.length > 0) {
                        map.fitBounds(bounds);
                    }
                })
                .catch(error => console.error("Error fetching locations:", error));
        };

        // Search functionality
        function search() {
            let query = document.getElementById('query').value;
            const url = new URL('/api/search', window.location.origin);
            url.searchParams.append('q', query); // Add query parameters

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    clearMarkers(); // Clear existing markers
                    const bounds = new google.maps.LatLngBounds();

                    data.forEach(location => {
                        // const marker = new google.maps.Marker({
                        //     position: {
                        //         lat: parseFloat(location.latitude),
                        //         lng: parseFloat(location.longitude),
                        //     },
                        //     map: map,
                        //     title: location.name,
                        // });

                        const marker = new google.maps.Marker({
                            position: {
                                lat: parseFloat(location.latitude),
                                lng: parseFloat(location.longitude),
                            },
                            map: map,
                            title: location.name,
                            label: {
                                text: location.name, // Display the location's name
                                color: "black", // Set label color
                                fontSize: "12px", // Customize font size
                                fontWeight: "bold", // Make it bold if needed
                            },
                        });

                        markers.push(marker); // Add marker to markers array
                        bounds.extend(marker.position);

                        google.maps.event.addListener(marker, 'click', () => {
                            const infoWindow = new google.maps.InfoWindow({
                                content: `<h5>${location.name}</h5>
                                            <p>${location.address}<br/>
                                            Operation Hour: ${location.operation_hour}</p>`,
                            });
                            infoWindow.open(map, marker);
                        });
                    });

                    // Adjust map bounds to fit search results
                    if (data.length > 0) {
                        map.fitBounds(bounds);
                    } else {
                        alert("No results found for your search.");
                    }
                })
                .catch(error => console.error("Error fetching search results:", error));
        }
    </script>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_API_KEY') }}&callback=initMap&libraries=maps"></script>
</main>
@endsection
