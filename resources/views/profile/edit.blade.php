@extends('layouts.app')

@section('body')
<main class="app-main"> <!--begin::App Content Header-->
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Account Management</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route("dashboard") }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Update Profile
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
                    @session("success")
                    <div class="alert alert-success" role="alert">
                        {{ session()->get("success") }}
                    </div>
                    @endsession

                    @session("warning")
                    <div class="alert alert-warning" role="alert">
                        {{ session()->get("warning") }}
                    </div>
                    @endsession

                    @session("danger")
                    <div class="alert alert-danger" role="alert">
                        {{ session()->get("danger") }}
                    </div>
                    @endsession

                    <div class="card card-success bg-success-subtle">
                        <div class="card-header bg-success">
                            <h3 class="card-title">Update Profile Information</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('profile.edit') }}" novalidate>
                                @csrf
                                @method("PATCH")

                                <!-- Name -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ $user->name }}" required autofocus >

                                    @error("name")
                                    <div class="form-text">
                                        <font color="red">{{ $message }}</font>
                                    </div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ $user->email }}" required>

                                    @error("email")
                                    <div class="form-text">
                                        <font color="red">{{ $message }}</font>
                                    </div>
                                    @enderror
                                </div>

                                <!-- Phone No -->
                                <div class="mb-3">
                                    <label for="phone_no" class="form-label">Phone No</label>
                                    <input type="text" class="form-control @error('phone_no') is-invalid @enderror" id="phone_no" name="phone_no" value="{{ $user->phone_no }}" required>

                                    @error("phone_no")
                                    <div class="form-text">
                                        <font color="red">{{ $message }}</font>
                                    </div>
                                    @enderror
                                </div>

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
