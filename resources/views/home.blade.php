@extends('layouts.web')

@section('body')
<main class="app-main">
    <div class="app-content"> <!--begin::Container-->
        <div class="container-lg"> <!--begin::Row-->
            <div class="row"> <!--begin::Col-->
                <div class="p-3 mb-4 mt-4 bg-body-tertiery border rounded-3">
                    <div class="container-fluid py-5">
                        <img src="{{ asset('assets/img/georecycle-logo.jpg') }}" height="100px">

                        <br/><br/>

                        <h1 class="display-5 fw-bold">Welcome to our Recycling Community</h1>

                        <p class="col-md-8 fs-4">At GeoRecycle, we are dedicated to promote a sustainable and eco friednly environment through recycling.</p>

                        <p class="col-md-8 fs-4">Recycling is an essential practice that helps reduce waste, conserve resources, and minmize our impact on the planet. By participating in recycling, you contribute to creating a cleaner and healthier world for future generations.</p>

                        <p class="col-md-8 fs-4">Join us in our mission to make a positive impact on the environemtn. Whether you're a recycling enthusiast or just geting started, we provide tools and information to help you recycle effectively and make a difference.</p>
                        {{-- <button class="btn btn-primary btn-lg" type="button">Example button</button> --}}
                    </div>
                </div>
            </div> <!--end::Row--> <!--begin::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content-->
</main>
@endsection