@extends('layouts.app')

@section('body')
<main class="app-main"> <!--begin::App Content Header-->
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Dashboard</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Dashboard
                        </li>
                    </ol>
                </div>
            </div> <!--end::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content Header--> <!--begin::App Content-->
    <div class="app-content"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row"> <!--begin::Col-->
                <div class="col-lg-4 col-4"> <!--begin::Small Box Widget 1-->
                    <div class="small-box text-bg-primary">
                        <div class="inner">
                            <h3>{{ $totCenters }}</h3>
                            <p>Recycling Centers</p>
                        </div> <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z"></path>
                        </svg>
                    </div> <!--end::Small Box Widget 1-->
                </div> <!--end::Col-->
                <div class="col-lg-4 col-4"> <!--begin::Small Box Widget 2-->
                    <div class="small-box text-bg-success">
                        <div class="inner">
                            <h3>{{ $totAdmins }}</h3>
                            <p>Admins</p>
                        </div> <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z"></path>
                        </svg>
                    </div> <!--end::Small Box Widget 2-->
                </div> <!--end::Col-->
                <div class="col-lg-4 col-4"> <!--begin::Small Box Widget 3-->
                    <div class="small-box text-bg-warning">
                        <div class="inner">
                            <h3>{{ $totOwners }}</h3>
                            <p>Registered Owners</p>
                        </div> <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M6.25 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM3.25 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM19.75 7.5a.75.75 0 00-1.5 0v2.25H16a.75.75 0 000 1.5h2.25v2.25a.75.75 0 001.5 0v-2.25H22a.75.75 0 000-1.5h-2.25V7.5z"></path>
                        </svg>
                    </div> <!--end::Small Box Widget 3-->
                </div> <!--end::Col-->
            </div> <!--end::Row--> <!--begin::Row-->
            @if(!auth()->user()->is_admin)
            <div class="row">
                <div class="col">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">List of Recycle Centers</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Services</th>
                                        <th>Address / Phone Number / Coordinate</th>
                                        <th>Type</th>
                                        <th>Operational Hour</th>
                                        <th>Verified</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($recyclingCenters as $idx => $recyclingCenter)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>{{ $recyclingCenter->name }}</td>
                                        <td>
                                            <ul>
                                                @php
                                                    $services = json_decode($recyclingCenter->services)->services;
                                                @endphp
                                                @foreach ($services as $service)
                                                <li>{{ $service }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td>
                                            {{ $recyclingCenter->address }}
                                            <br/><br/>
                                            Phone Number: {{ $recyclingCenter->phone_no }}
                                            <br/>
                                            Lat: {{ $recyclingCenter->latitude }} | Long: {{ $recyclingCenter->longitude }}
                                        </td>
                                        <td>{{ $recyclingCenter->is_dropbox ? "Dropbox" : "Premise" }}</td>
                                        <td>{{ $recyclingCenter->operation_hour }}</td>
                                        <td>{{ $recyclingCenter->is_verified ? "Verified" : null }}</td>
                                        <td>
                                            @if(($recyclingCenter->is_verified != 1) && (auth()->user()->is_admin))
                                            <form id="verify_{{ $recyclingCenter->id }}" method="POST" action="{{ route('center.verify', $recyclingCenter->id) }}">
                                                @csrf
                                                @method("PUT")
                                                <button type="submit" class="btn btn-success btn-sm" form="verify_{{ $recyclingCenter->id }}">Verify</button>
                                            </form>
                                            @endif

                                            <a class="btn btn-warning btn-sm" href="{{ route('center.edit', $recyclingCenter->id) }}">Edit</a>

                                            <form id="del_{{ $recyclingCenter->id }}" method="POST" action="{{ route('center.destroy', $recyclingCenter->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" form="del_{{ $recyclingCenter->id }}">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> <!-- /.card-body -->
                    </div> <!-- /.card -->
                </div>
            </div> <!-- /.row -->
            @endif
        </div> <!--end::Container-->
    </div> <!--end::App Content-->
</main> <!--end::App Main--> <!--begin::Footer-->
@endsection('body')