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
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
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

                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">List of Recycle Centers</h3>
                        </div>
                        <div class="card-body">
                            <a class="btn btn-primary btn-sm" href="{{ route('center.create') }}" >
                                {{ __('Add Recycle Center') }}
                            </a>

                            <br/>
                            <br/>

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Services</th>
                                        <th>Address</th>
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
                                        <td>{{ $recyclingCenter->address }}</td>
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
            </div> <!--end::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content-->
</main> <!--end::App Main-->
@endsection
