@extends('layouts.web')

@section('body')
<main class="app-main">
    <div class="app-content"> <!--begin::Container-->
        <div class="container-lg"> <!--begin::Row-->
            <div class="row"> <!--begin::Col-->
                <div class="p-3 mb-4 mt-4 bg-success-subtle border rounded-3">
                    <div class="container-fluid py-5">
                        <h1 class="display-5 fw-bold">What is Recycling?</h1>

                        <p class="col-md-8 fs-4">Recycling is like giving old items a second life! It's the process of turning things we no longer need into something new, reducing waste and making sure we use fever natural resources.</p>

                        <h1 class="display-5 fw-bold">Why Should We Recycle?</h1>

                        <p class="col-md-8 fs-4">
                            <ul>
                                <li>Save Our Resources: When we recycle, we don't have to use as many raw materials, like trees or metals. This helps protect nature.</li>
                                <li>Less Trash, More Space: Recycling keeps trash out of landfills, so there's more room for other things.</li>
                                <li>Save Energy: It takes less energy to make new things from recycled materials than from scratch, which helps fight climate change.</li>
                                <li>Boost the Economy: Recycling creates jobs and supports businesses that make new things from old materials.</li>
                            </ul>
                        </p>

                        <h1 class="display-5 fw-bold">How to Recycle Right</h1>

                        <p class="col-md-8 fs-4">
                            <ul>
                                <li>Sort Your Stuff: Separate your recyclables - paper, plastic, glass, and metals. It makes recycling easier and more effective!</li>
                                <li>Clean It Up: Rinse your recyclables, so they're for reuse. No food, grease, or dirty leftovers!</li>
                                <li>Follow Local Rules: Recycling rules can be different where you live, so check your local recycling center or guidelines to make sure you're doing it right.</li>
                                <li>Don't Forget E-Waste: Electronics like phones, batteries, and computers need special care. Don't toss them in regular bins - find a center that handles e-waste!</li>
                            </ul>
                        </p>
                    </div>
                </div>
            </div> <!--end::Row--> <!--begin::Row-->
        </div> <!--end::Container-->
    </div> <!--end::App Content-->
</main>
@endsection