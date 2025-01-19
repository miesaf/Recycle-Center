@extends('layouts.app')

@section('body')
<main class="app-main"> <!--begin::App Content Header-->
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">System Administrator</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route("dashboard") }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            System Administrator
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
                            <h3 class="card-title">Admin Information</h3>
                        </div>
                        <div class="card-body">
                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ $admin->name }}" disabled>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ $admin->email }}" disabled>
                            </div>

                            <!-- Phone Number -->
                            <div class="mb-3">
                                <label for="phone_no" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone_no') is-invalid @enderror" id="phone_no" name="phone_no" value="{{ $admin->phone_no }}" disabled>
                            </div>

                            <!-- Verified -->
                            <div class="mb-3">
                                <label for="verify" class="form-label">Verified</label>
                                <input type="text" class="form-control @error('verify') is-invalid @enderror" id="verify" name="verify" value="{{ $admin->is_verify ? 'Verified' : 'Not Verified' }}" disabled>
                            </div>

                            <!-- Linked With Google -->
                            <div class="mb-3">
                                <label for="google" class="form-label">Linked with Google Account</label>
                                <input type="text" class="form-control" id="google" name="google" value="{{ $admin->google_id ? "Linked" : "Not Linked" }}" disabled>
                            </div>
                        </div> <!-- /.card-body -->
                    </div> <!-- /.card -->
                </div>
            </div> <!--end::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content-->
</main> <!--end::App Main-->
@endsection
