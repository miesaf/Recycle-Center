@extends('layouts.app')

@section('body')
<main class="app-main"> <!--begin::App Content Header-->
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Recycle Center Reviews</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Recycle Center</li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Reviews
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
                        <div class="card-header">
                            <h3 class="card-title">List of Recycle Center Reviews</h3>
                        </div>
                        <div class="card-body">
                            <a class="btn btn-primary btn-sm" href="{{ route('review.create') }}" >
                                {{ __('Add Recycle Center Review') }}
                            </a>

                            <br/>
                            <br/>

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Recycle Center</th>
                                        <th>Review</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($recyclingCenters as $idx => $recyclingCenter)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td><b>{{ $recyclingCenter->name }}</b>@if (!auth()->user()->is_center) <br/>by {{ $recyclingCenter->ownerInfo->name }} @endif</td>
                                        <td>
                                            @foreach ($recyclingCenter->reviews as $idx2 => $review)
                                            {{ $review->rating }} ⭐
                                            @endforeach
                                        </td>
                                        <td>
                                            <a class="btn btn-warning btn-sm" href="{{ route('review.edit', $review->id) }}">Edit</a>

                                            <form id="del_{{ $review->id }}" method="POST" action="{{ route('review.destroy', $review->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" form="del_{{ $review->id }}">Delete</button>
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
