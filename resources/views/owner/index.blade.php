@extends('layouts.app')

@section('body')
<main class="app-main"> <!--begin::App Content Header-->
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Recycle Center Owners</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Registered Owner
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
                            <h3 class="card-title">List of Recycle Centers Owner</h3>
                        </div>
                        <div class="card-body">
                            {{-- <a class="btn btn-primary btn-sm" href="{{ route('owner.create') }}" >
                                {{ __('Add Recycle Center Owner') }}
                            </a>

                            <br/>
                            <br/> --}}

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Verified</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($owners as $owner)
                                    <tr>
                                        <td>{{ $owner->name }}</td>
                                        <td>{{ $owner->email }}</td>
                                        <td>{{ $owner->is_verified ? "Verified" : null }}</td>
                                        <td>
                                            {{-- <button class="btn btn-success btn-sm" href="{{ route('center.edit', $owner->id) }}">Approve</button> --}}
                                            @if($owner->is_verified != 1)
                                            <form id="verify_{{ $owner->id }}" method="POST" action="{{ route('owner.verify', $owner->id) }}">
                                                @csrf
                                                @method("PUT")
                                                <button type="submit" class="btn btn-success btn-sm" form="verify_{{ $owner->id }}">Verify</button>
                                            </form>
                                            @endif

                                            <a class="btn btn-warning btn-sm" href="{{ route('owner.edit', $owner->id) }}">Edit</a>

                                            <form id="del_{{ $owner->id }}" method="POST" action="{{ route('owner.destroy', $owner->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" form="del_{{ $owner->id }}">Delete</button>
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
