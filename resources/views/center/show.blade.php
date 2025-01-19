@extends('layouts.app')

@section('body')
<main class="app-main"> <!--begin::App Content Header-->
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Recycle Center</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route("dashboard") }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Recycle Center
                        </li>
                    </ol>
                </div>
            </div> <!--end::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content Header--> <!--begin::App Content-->
    <div class="app-content"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-12"> <!-- Default box -->
                    <div class="card card-success bg-success-subtle">
                        <div class="card-header">
                            <h3 class="card-title">Update Recycle Center Information</h3>
                        </div>
                        <div class="card-body">
                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Branch Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $recyclingCenter->name }}" disabled>
                            </div>

                            <!-- Phone Number -->
                            <div class="mb-3">
                                <label for="phone_no" class="form-label">Branch Phone Number</label>
                                <input type="text" class="form-control" id="phone_no" name="phone_no" value="{{ $recyclingCenter->phone_no }}" disabled>
                            </div>

                            <!-- Services -->
                            <div class="mb-3">
                                <label for="services" class="form-label">Services Offered</label>

                                @php
                                    $servicesArr = json_decode($recyclingCenter->services)->services;
                                @endphp

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="service1" name="services[]" value="Paper" {{ in_array("Paper", $servicesArr) ? "checked" : null }} disabled>
                                    <label class="form-check-label" for="service1"> Paper</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="service2" name="services[]" value="Metal" {{ in_array("Metal", $servicesArr) ? "checked" : null }} disabled>
                                    <label class="form-check-label" for="service2"> Metal</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="service3" name="services[]" value="Textiles (Clothing and Fabric)" {{ in_array("Textiles (Clothing and Fabric)", $servicesArr) ? "checked" : null }} disabled>
                                    <label class="form-check-label" for="service3"> Textiles (Clothing and Fabric)</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="service4" name="services[]" value="Glass" {{ in_array("Glass", $servicesArr) ? "checked" : null }} disabled>
                                    <label class="form-check-label" for="service4"> Glass</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="service5" name="services[]" value="Plastic" {{ in_array("Plastic", $servicesArr) ? "checked" : null }} disabled>
                                    <label class="form-check-label" for="service5"> Plastic</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="service6" name="services[]" value="Electronics (E-Waste)" {{ in_array("Electronics (E-Waste)", $servicesArr) ? "checked" : null }} disabled>
                                    <label class="form-check-label" for="service6"> Electronics (E-Waste)</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="service7" name="services[]" value="Cardboard" {{ in_array("Cardboard", $servicesArr) ? "checked" : null }} disabled>
                                    <label class="form-check-label" for="service7"> Cardboard</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="service9" name="services[]" value="Batteries" {{ in_array("Batteries", $servicesArr) ? "checked" : null }} disabled>
                                    <label class="form-check-label" for="service9"> Batteries</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="service10" name="services[]" value="Tires" {{ in_array("Tires", $servicesArr) ? "checked" : null }} disabled>
                                    <label class="form-check-label" for="service10"> Tires</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="service11" name="services[]" value="Used Cooking Oil" {{ in_array("Used Cooking Oil", $servicesArr) ? "checked" : null }} disabled>
                                    <label class="form-check-label" for="service11"> Used Cooking Oil</label>
                                </div>
                            </div>

                            <!-- Branch Address -->
                            <div class="mb-3">
                                <label for="address" class="form-label">Branch Address</label>
                                <textarea id="address" class="form-control" rows="5" name="address" disabled>{{ $recyclingCenter->address }}</textarea>
                            </div>

                            <!-- Latitude -->
                            <div class="mb-3">
                                <label for="latitude" class="form-label">Branch Latitude</label>
                                <input type="text" class="form-control" id="latitude" name="latitude" value="{{ $recyclingCenter->latitude }}" disabled>
                            </div>

                            <!-- Longitude -->
                            <div class="mb-3">
                                <label for="longitude" class="form-label">Branch Longitude</label>
                                <input type="text" class="form-control" id="longitude" name="longitude" value="{{ $recyclingCenter->longitude }}" disabled>
                            </div>

                            <!-- Google Maps and Places API Integration -->
                            <script>
                                let autocomplete;
                                let geocoder;
                                let map;
                                let marker;

                                // Initialize map and geocoder
                                function initMap() {
                                    geocoder = new google.maps.Geocoder();
                                        // Change default location to Shah Alam Section 7
                                    const defaultLocation = { lat: 3.07258580566939, lng: 101.5183278411865 };  // Shah Alam Section 7 coordinates

                                    // Initialize the map
                                    map = new google.maps.Map(document.getElementById("map"), {
                                        center: defaultLocation,
                                        zoom: 15,
                                    });

                                    // Add a draggable marker
                                    marker = new google.maps.Marker({
                                        position: defaultLocation,
                                        map: map,
                                        draggable: false,
                                    });

                                    // Initialize the Autocomplete for the address input field
                                    const addressInput = document.getElementById("address");
                                    autocomplete = new google.maps.places.Autocomplete(addressInput, {
                                        types: ["geocode"],
                                        componentRestrictions: { country: "MY" },  // Restrict to Malaysia (or another country if needed)
                                    });

                                    // Listener when a user selects a place from the autocomplete suggestions
                                    autocomplete.addListener("place_changed", function () {
                                        const place = autocomplete.getPlace();

                                        if (place.geometry) {
                                            const latitude = place.geometry.location.lat();
                                            const longitude = place.geometry.location.lng();

                                            // Update the latitude and longitude fields
                                            document.getElementById("latitude").value = latitude;
                                            document.getElementById("longitude").value = longitude;

                                            // Update the address field and make it uppercase
                                            document.getElementById("address").value = place.formatted_address.toUpperCase();

                                            // Move the map center and update the marker position
                                            marker.setPosition(place.geometry.location);
                                            map.setCenter(place.geometry.location);
                                        } else {
                                            alert("No details available for input: " + addressInput.value);
                                        }
                                    });
                                }

                                // Function to get address from latitude and longitude using Geocoding API
                                function getAddress(lat, lng) {
                                    const latLng = new google.maps.LatLng(lat, lng);
                                    geocoder.geocode({ location: latLng }, function (results, status) {
                                        if (status === google.maps.GeocoderStatus.OK) {
                                            if (results[0]) {
                                                document.getElementById("address").value = results[0].formatted_address.toUpperCase(); // Auto capitalize the address
                                            }
                                        } else {
                                            alert("Geocoder failed: " + status);
                                        }
                                    });
                                }
                            </script>

                            <!-- Google Map Display -->
                            <div class="mb-3">
                                <label for="map" class="form-label">Select Branch Location</label>
                                <div id="map" style="height: 600px; width: 100%;"></div>
                            </div>

                            <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_API_KEY') }}&callback=initMap&libraries=places"></script>

                            <!-- Premise Type -->
                            <div class="mb-3">
                                <label for="is_dropbox" class="form-label">Branch Premise Type</label>
                                <select id="is_dropbox" class="form-select" name="is_dropbox" disabled>
                                    <option value selected>Please select</option>
                                    <option disabled></option>
                                    <option value="0" {{ $recyclingCenter->is_dropbox ? null : "selected" }}>Premise</option>
                                    <option value="1" {{ $recyclingCenter->is_dropbox ? "selected" : null }}>Dropbox</option>
                                </select>
                            </div>

                            <!-- Operational Hour -->
                            <div class="mb-3">
                                <label for="operation_hour" class="form-label">Branch Operation Hour</label>
                                <input type="text" class="form-control" id="operation_hour" name="operation_hour" value="{{ $recyclingCenter->operation_hour }}" disabled>
                            </div>

                            @if (auth()->user()->is_admin)
                            <!-- Premise Owner -->
                            <div class="mb-3">
                                <label for="owner" class="form-label">Branch Owner</label>
                                <input type="text" class="form-control" id="owner" name="owner" value="{{ ($recyclingCenter->ownerInfo)->name }}" disabled>
                            </div>
                            @endif

                            <!-- Verified -->
                            <div class="mb-3">
                                <label for="verify" class="form-label">Verified</label>
                                <input type="text" class="form-control @error('verify') is-invalid @enderror" id="verify" name="verify" value="{{ $recyclingCenter->is_verify ? 'Verified' : 'Not Verified' }}" disabled>
                            </div>
                        </div> <!-- /.card-body -->
                    </div> <!-- /.card -->
                </div>
            </div> <!--end::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content-->
</main> <!--end::App Main-->
@endsection