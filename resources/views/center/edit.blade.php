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
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Update Recycle Center Information</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('center.update', $recyclingCenter->id) }}" novalidate>
                                @csrf
                                @method("PUT")

                                <!-- Name -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">Branch Name</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="name" name="name" value="{{ $recyclingCenter->name }}" required autofocus >

                                    @error("name")
                                    <div class="form-text">
                                        <font color="red">{{ $message }}</font>
                                    </div>
                                    @enderror
                                </div>

                                <!-- Services -->
                                <div class="mb-3">
                                    <label for="services" class="form-label">Services Offered</label>

                                    @php
                                        $servicesArr = json_decode($recyclingCenter->services)->services;
                                    @endphp

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="service1" name="services[]" value="Paper" {{ in_array("Paper", $servicesArr) ? "checked" : null }}>
                                        <label class="form-check-label" for="service1"> Paper</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="service2" name="services[]" value="Metal" {{ in_array("Metal", $servicesArr) ? "checked" : null }}>
                                        <label class="form-check-label" for="service2"> Metal</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="service3" name="services[]" value="Fabric" {{ in_array("Fabric", $servicesArr) ? "checked" : null }}>
                                        <label class="form-check-label" for="service3"> Fabric</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="service4" name="services[]" value="Glass" {{ in_array("Glass", $servicesArr) ? "checked" : null }}>
                                        <label class="form-check-label" for="service4"> Glass</label>
                                    </div>

                                    @error("services")
                                    <div class="form-text">
                                        <font color="red">{{ $message }}</font>
                                    </div>
                                    @enderror
                                </div>

                                <!-- Address -->
                                <div class="mb-3">
                                    <label for="address" class="form-label">Branch Address</label>
                                    <textarea id="address" class="form-control" rows="5" name="address" required>{{ $recyclingCenter->address }}</textarea>

                                    @error("address")
                                    <div class="form-text">
                                        <font color="red">{{ $message }}</font>
                                    </div>
                                    @enderror
                                </div>

                                <!-- Premise Type -->
                                <div class="mb-3">
                                    <label for="is_dropbox" class="form-label">Branch Premise Type</label>
                                    <select id="is_dropbox" class="form-select" name="is_dropbox" required>
                                        <option value selected>Please select</option>
                                        <option disabled></option>
                                        <option value="0" {{ $recyclingCenter->is_dropbox ? null : "selected" }}>Premise</option>
                                        <option value="1" {{ $recyclingCenter->is_dropbox ? "selected" : null }}>Dropbox</option>
                                    </select>

                                    @error("is_dropbox")
                                    <div class="form-text">
                                        <font color="red">{{ $message }}</font>
                                    </div>
                                    @enderror
                                </div>

                                <!-- Operational Hour -->
                                <div class="mb-3">
                                    <label for="operation_hour" class="form-label">Branch Operation Hour</label>
                                    <input type="text" class="form-control" id="operation_hour" name="operation_hour" value="{{ $recyclingCenter->operation_hour }}" >

                                    @error("operation_hour")
                                    <div class="form-text">
                                        <font color="red">{{ $message }}</font>
                                    </div>
                                    @enderror
                                </div>

                                @if (auth()->user()->is_admin)
                                <!-- Premise Owner -->
                                <div class="mb-3">
                                    <label for="owner" class="form-label">Branch Owner</label>
                                    <select id="owner" class="form-select" name="owner" required>
                                        <option value selected>Please select</option>
                                        <option disabled></option>
                                        @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}"  {{ ($recyclingCenter->owner == $owner->id) ? "selected" : null }}>{{ $owner->name }} ({{ $owner->email }})</option>
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
                                        {{ __('Update') }}
                                    </button>
                                </div>
                            </form>
                        </div> <!-- /.card-body -->
                    </div> <!-- /.card -->
                </div>
            </div> <!--end::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content-->
</main> <!--end::App Main-->
@endsection
