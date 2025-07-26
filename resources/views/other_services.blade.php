@extends('layouts.auth') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text">Services</h1>
    </div>

    <div class="row">
    <!-- Kawa Hot Bath -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="d-flex h-100 flex-column justify-content-center">
                    <div class="row align-items-center justify-content-between">
                        <div class="col mr-2 text-center">
                            <div class="col-auto">
                                <i class="fa fa-hot-tub-person fa-5x text-success"></i>
                            </div>
                            <div class="text-success text-uppercase mb-1 mt-3">
                                <strong>Kawa Hot Bath</strong>
                            </div>
                            <a class="btn btn-sm btn-secondary mt-2" href="{{ url('/kawa-hot-baths') }}">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Water Tubing -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="d-flex h-100 flex-column justify-content-center">
                    <div class="row align-items-center justify-content-between">
                        <div class="col mr-2 text-center">
                            <div class="col-auto">
                                <i class="fa fa-water fa-5x text-success"></i>
                            </div>
                            <div class="text-success text-uppercase mb-1 mt-3">
                                <strong>Water Tubing</strong>
                            </div>
                            <a class="btn btn-sm btn-secondary mt-2" href="{{ url('/water-tubings') }}">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Picnic Table -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="d-flex h-100 flex-column justify-content-center">
                    <div class="row align-items-center justify-content-between">
                        <div class="col mr-2 text-center">
                            <div class="col-auto">
                                <i class="fa fa-table fa-5x text-success"></i>
                            </div>
                            <div class="text-success text-uppercase mb-1 mt-3">
                                <strong>Picnic Table</strong>
                            </div>
                            <a class="btn btn-sm btn-secondary mt-2" href="{{ route('picnictables') }}">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Massage -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="d-flex h-100 flex-column justify-content-center">
                    <div class="row align-items-center justify-content-between">
                        <div class="col mr-2 text-center">
                            <div class="col-auto">
                                <i class="fa fa-spa fa-5x text-success"></i>
                            </div>
                            <div class="text-success text-uppercase mb-1 mt-3">
                                <strong>Massage</strong>
                            </div>
                            <a class="btn btn-sm btn-secondary mt-2" href="{{ route('massages') }}">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overnight Accomodation -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="d-flex h-100 flex-column justify-content-center">
                    <div class="row align-items-center justify-content-between">
                        <div class="col mr-2 text-center">
                            <div class="col-auto">
                                <i class="fa fa-bed fa-5x text-success"></i>
                            </div>
                            <div class="text-success text-uppercase mb-1 mt-3">
                                <strong>Overnight Accomodation</strong>
                            </div>
                            <a class="btn btn-sm btn-secondary mt-2" href="{{ route('accommodations') }}">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection <!-- End the content section -->
