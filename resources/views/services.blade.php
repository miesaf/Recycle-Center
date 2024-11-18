@extends('layouts.web')

@section('body')
<main class="app-main">
    <div class="app-content"> <!--begin::Container-->
        <div class="container-lg"> <!--begin::Row-->
            <div class="row"> <!--begin::Col-->
                <div class="row align-items-md-stretch">
                    <div class="container-fluid py-5">
                        <h1 class="display-5 fw-bold">Start your eco-friendly journey with GeoRecycle today by becoming out partner!</h1>
                    </div>

                    <div class="col-md-6">
                        <div class="h-100 p-5 bg-body-tertiary border rounded-3">
                            <h2>Recycling Center Locations</h2>
                            <p>Find convenient recycling centers near you. We provide a comprehensive list of locations to make recycling easy and accessible.</p>
                            <a class="btn btn-primary" href="{{ route('map') }}">Find Now</a>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="h-100 p-5 bg-body-tertiary border rounded-3">
                            <h2>Register you own recycle center</h2>
                            <p>Register your recycling center and contribute to building a sustainable and eco-friendly community.</p>
                            <a class="btn btn-primary" href="{{ route('register') }}">Get Started</a>
                        </div>
                    </div>
                </div>
            </div> <!--end::Row--> <!--begin::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content-->
</main>
@endsection