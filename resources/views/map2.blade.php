@extends('layouts.web')

@section('head')
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env("GOOGLE_MAP_API_KEY") }}&callback=initMap&libraries=maps,marker"></script>
<style>
    #map {
        height: 100%;
        width: 100%;
    }
</style>
@endsection

@section('body')
<main class="app-main">
    <div id="map"></div>
    <script>
        let map;

        // Initialize the Google Map
        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 3.078716, lng: 101.493990 }, // Default center
                // center: { lat: 37.7749, lng: -122.4194 }, // Default center
                zoom: 15,
                mapId: "27d4cb7b44e51a1a", // Replace with your actual Map ID
            });

            fetch('/api/locations')
                .then(response => response.json())
                .then(data => {
                    data.forEach(location => {
                        // Create a new DOM element for the marker content (if needed)
                        const markerContent = document.createElement('div');
                        markerContent.className = 'custom-marker';
                        markerContent.textContent = location.name;

                        // Add an Advanced Marker for each location
                        const marker = new google.maps.marker.AdvancedMarkerElement({
                            position: {
                                lat: parseFloat(location.latitude),
                                lng: parseFloat(location.longitude),
                            },
                            map: map,
                            title: location.name,
                            content: markerContent, // Optional: Use custom content
                        });

                        google.maps.event.addListener(marker, 'click', () => {
                            const infoWindow = new google.maps.InfoWindow({
                                content: `<h3>${location.name}</h3><p>${location.address}</p>`,
                            });
                            infoWindow.open(map);
                        });
                    });
                })
                .catch(error => console.error("Error fetching locations:", error));

            // data.forEach(location => {
            //     // Add a marker for each location
            //     new google.maps.Marker({
            //         position: { lat: parseFloat(location.latitude), lng: parseFloat(location.longitude) },
            //         map,
            //         title: location.name,
            //     });
            // });

            // Fetch location data from the server
            // fetch('/api/locations')
            //     .then(response => response.json())
            //     .then(data => {
            //         data.forEach(location => {
            //             // Add a marker for each location
            //             new google.maps.Marker({
            //                 position: { lat: parseFloat(location.latitude), lng: parseFloat(location.longitude) },
            //                 map,
            //                 title: location.name,
            //             });
            //         });
            //     })
            //     .catch(error => console.error("Error fetching locations:", error));
        }
    </script>
</main>
@endsection