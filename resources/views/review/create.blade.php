@extends('layouts.app')

@section('body')
<main class="app-main"> <!--begin::App Content Header-->
    <div class="app-content-header"> <!--begin::Container-->
        <div class="container-fluid"> <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Recycle Center Review</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route("dashboard") }}">Home</a></li>
                        <li class="breadcrumb-item">Recycle Center</li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Review
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
                            <h3 class="card-title">Write New Recycle Center Review</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('review.store') }}" novalidate>
                                @csrf

                                <!-- Premise -->
                                <div class="mb-3">
                                    <label for="recycling_center" class="form-label">Recycle Center</label>
                                    <select id="recycling_center" class="form-select" name="recycling_center" required>
                                        <option value selected>Please select</option>
                                        <option disabled></option>
                                        @foreach($recyclingCenters as $recyclingCenter)
                                        <option value="{{ $recyclingCenter->id }}" >{{ $recyclingCenter->name }}</option>
                                        @endforeach
                                    </select>

                                    @error("recycling_center")
                                    <div class="form-text">
                                        <font color="red">{{ $message }}</font>
                                    </div>
                                    @enderror
                                </div>

                                <!-- Rating -->
                                <div class="mb-3">
                                    <label for="rating" class="form-label">Rating</label>
                                    {{-- <input type="range" class="custom-range @error('rating') is-invalid @enderror" id="rating" name="rating" value="{{ old('rating') }}" required autofocus > --}}
                                    <select id="rating" class="form-select" name="rating" required>
                                        <option value selected>Please select</option>
                                        <option disabled></option>
                                        <option value="1" > ⭐ </option>
                                        <option value="2" > ⭐ ⭐ </option>
                                        <option value="3" > ⭐ ⭐ ⭐ </option>
                                        <option value="4" > ⭐ ⭐ ⭐ ⭐ </option>
                                        <option value="5" > ⭐ ⭐ ⭐ ⭐ ⭐ </option>
                                    </select>

                                    @error("rating")
                                    <div class="form-text">
                                        <font color="red">{{ $message }}</font>
                                    </div>
                                    @enderror
                                </div>

                                <!-- Comment -->
                                <div class="mb-3">
                                    <label for="review" class="form-label">Comment</label>
                                    <textarea id="review" class="form-control" rows="5" name="review" required>{{ old('review') }}</textarea>

                                    @error("review")
                                    <div class="form-text">
                                        <font color="red">{{ $message }}</font>
                                    </div>
                                    @enderror
                                </div>

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
</main> <!--end::App Main-->
@endsection
