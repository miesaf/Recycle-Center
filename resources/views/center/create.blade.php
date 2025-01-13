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
                            <h3 class="card-title">Register New Recycle Center</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('center.store') }}" novalidate>
                                @csrf

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Branch Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                    value="{{ old('name') }}" required autofocus oninput="this.value = this.value.toUpperCase();">

                                @error("name")
                                    <div class="form-text">
                                        <font color="red">{{ $message }}</font>
                                    </div>
                                @enderror
                            </div>

                                <!-- Phone Number -->
                                <div class="mb-3">
                                    <label for="phone_no" class="form-label">Branch Phone Number</label>
                                    <input type="text" class="form-control @error('phone_no') is-invalid @enderror" id="phone_no" name="phone_no"
                                        value="{{ old('phone_no') }}" required autofocus oninput="formatPhoneNumber(this);">

                                    @error("phone_no")
                                        <div class="form-text">
                                            <font color="red">{{ $message }}</font>
                                        </div>
                                    @enderror
                                </div>

                                <!-- Services -->
                                <div class="mb-3">
                                    <label for="services" class="form-label">Services Offered</label>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="service1" name="services[]" value="Paper">
                                        <label class="form-check-label" for="service1"> Paper</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="service2" name="services[]" value="Metal">
                                        <label class="form-check-label" for="service2"> Metal</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="service3" name="services[]" value="Fabric">
                                        <label class="form-check-label" for="service3"> Fabric</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="service4" name="services[]" value="Glass">
                                        <label class="form-check-label" for="service4"> Glass</label>
                                    </div>

                                    @error("services")
                                    <div class="form-text">
                                        <font color="red">{{ $message }}</font>
                                    </div>
                                    @enderror
                                </div>

                                 <!-- Branch Address -->
                                <div class="mb-3">
                                    <label for="address" class="form-label">Branch Address</label>
                                    <input type="text" id="address" class="form-control @error('address') is-invalid @enderror" name="address" placeholder="Enter the full address or just move the pin location on the map" required>
                                    @error("address")
                                    <div class="form-text">
                                        <font color="red">{{ $message }}</font>
                                    </div>
                                    @enderror
                                </div>

                                <!-- Latitude -->
                                <div class="mb-3">
                                    <label for="latitude" class="form-label">Branch Latitude</label>
                                    <input type="text" class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude" value="{{ old('latitude') }}" required readonly>
                                    @error("latitude")
                                    <div class="form-text">
                                        <font color="red">{{ $message }}</font>
                                    </div>
                                    @enderror
                                </div>

                                <!-- Longitude -->
                                <div class="mb-3">
                                    <label for="longitude" class="form-label">Branch Longitude</label>
                                    <input type="text" class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude" value="{{ old('longitude') }}" required readonly>
                                    @error("longitude")
                                    <div class="form-text">
                                        <font color="red">{{ $message }}</font>
                                    </div>
                                    @enderror
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
                                            draggable: true,
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

                                        // When the user drags the marker, update the fields accordingly
                                        google.maps.event.addListener(marker, "dragend", function (event) {
                                            const lat = event.latLng.lat();
                                            const lng = event.latLng.lng();
                                            document.getElementById("latitude").value = lat;
                                            document.getElementById("longitude").value = lng;
                                            getAddress(lat, lng);
                                        });

                                        // When the user clicks on the map, place the marker and update fields
                                        google.maps.event.addListener(map, "click", function (event) {
                                            const lat = event.latLng.lat();
                                            const lng = event.latLng.lng();
                                            marker.setPosition(event.latLng);
                                            document.getElementById("latitude").value = lat;
                                            document.getElementById("longitude").value = lng;
                                            getAddress(lat, lng);
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
                                    <select id="is_dropbox" class="form-select" name="is_dropbox" required>
                                        <option value selected>Please select</option>
                                        <option disabled></option>
                                        <option value="0" >Premise</option>
                                        <option value="1" >Dropbox</option>
                                    </select>

                                    @error("is_dropbox")
                                    <div class="form-text">
                                        <font color="red">{{ $message }}</font>
                                    </div>
                                    @enderror
                                </div>
                               <!-- Operational Hour -->
                            <div class="mb-3">
                                <label for="operation_hour" class="form-label">Branch Operational Hour</label>
                                <input type="text" class="form-control" id="operation_hour" name="operation_hour"
                                    value="{{ old('operation_hour') }}" required autofocus oninput="this.value = this.value.toUpperCase();" <!--
                                    Auto capitalize the input -->

                            </div>

                                @if (auth()->user()->is_admin)
                                <!-- Premise Owner -->
                                <div class="mb-3">
                                    <label for="owner" class="form-label">Branch Owner</label>
                                    <select id="owner" class="form-select" name="owner" required>
                                        <option value selected>Please select</option>
                                        <option disabled></option>
                                        @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}" >{{ $owner->name }} ({{ $owner->email }})</option>
                                        @endforeach
                                    </select>

                                    @error("owner")
                                    <div class="form-text">
                                        <font color="red">{{ $message }}</font>
                                    </div>
                                    @enderror
                                </div>
                                @endif

                                <div class="flex items-center justify-end mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Submit') }}
                                    </button>
                                </div>
                            </form>
                        </div> <!-- /.card-body -->
                    </div> <!-- /.card -->
                </div>
            </div> <!--end::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content-->
    <script>
        // Function to format the phone number as `011-56431284` or `03-80008000`
        function formatPhoneNumber(input) {
            // Remove any non-numeric characters
            let cleaned = input.value.replace(/\D/g, '');

            // Check if the phone number starts with `03` (2-digit prefix)
            if (cleaned.startsWith('03')) {
                if (cleaned.length <= 2) {
                    input.value = cleaned;
                } else if (cleaned.length <= 8) {
                    input.value = cleaned.slice(0, 2) + '-' + cleaned.slice(2);
                } else {
                    input.value = cleaned.slice(0, 2) + '-' + cleaned.slice(2, 10);
                }
            }
            // Handle phone numbers starting with `011` (3-digit prefix)
            else if (cleaned.startsWith('011')) {
                if (cleaned.length <= 3) {
                    input.value = cleaned;
                } else {
                    input.value = cleaned.slice(0, 3) + '-' + cleaned.slice(3, 11);
                }
            }
            // Default formatting for other cases (XXX-XXXXXXXX)
            else {
                if (cleaned.length <= 3) {
                    input.value = cleaned;
                } else if (cleaned.length <= 7) {
                    input.value = cleaned.slice(0, 3) + '-' + cleaned.slice(3);
                } else {
                    input.value = cleaned.slice(0, 3) + '-' + cleaned.slice(3, 11);
                }
            }
        }
    </script>

</main> <!--end::App Main-->
@endsection
