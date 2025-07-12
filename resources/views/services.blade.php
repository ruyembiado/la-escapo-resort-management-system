@extends('layouts.auth') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text">Visitor's Billing Payments</h1>
    </div>

    <div class="row">
    <!-- Entrance Fee -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="d-flex h-100 flex-column justify-content-center">
                    <div class="row align-items-center justify-content-between">
                        <div class="col mr-2 text-center">
                            <div class="col-auto">
                                <i class="fa fa-door-open fa-5x text-success"></i>
                            </div>
                            <div class="text-success text-uppercase mb-1 mt-3">
                                <strong>Entrance Fee</strong>
                            </div>
                            <a class="btn btn-sm btn-secondary mt-2" href="{{ url('/entrances') }}">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accommodation -->
    {{-- <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="d-flex h-100 flex-column justify-content-center">
                    <div class="row align-items-center justify-content-between">
                        <div class="col mr-2 text-center">
                            <div class="col-auto">
                                <i class="fa fa-bed fa-5x text-primary"></i>
                            </div>
                            <div class="text-primary text-uppercase mb-1 mt-3">
                                <strong>Accommodation</strong>
                            </div>
                            <a class="btn btn-sm btn-secondary mt-2" href="{{ url('/accommodations') }}">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Cottage Rental -->
    {{-- <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="d-flex h-100 flex-column justify-content-center">
                    <div class="row align-items-center justify-content-between">
                        <div class="col mr-2 text-center">
                            <div class="col-auto">
                                <i class="fa fa-home fa-5x text-warning"></i>
                            </div>
                            <div class="text-warning text-uppercase mb-1 mt-3">
                                <strong>Cottage Rental</strong>
                            </div>
                            <a class="btn btn-sm btn-secondary mt-2" href="{{ route('cottages') }}">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Meals -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="d-flex h-100 flex-column justify-content-center">
                    <div class="row align-items-center justify-content-between">
                        <div class="col mr-2 text-center">
                            <div class="col-auto">
                                <i class="fa fa-utensils fa-5x text-success"></i>
                            </div>
                            <div class="text-success text-uppercase mb-1 mt-3">
                                <strong>Meals</strong>
                            </div>
                            <a class="btn btn-sm btn-secondary mt-2" href="{{ route('meals') }}">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Beverages -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="d-flex h-100 flex-column justify-content-center">
                    <div class="row align-items-center justify-content-between">
                        <div class="col mr-2 text-center">
                            <div class="col-auto">
                                <i class="fa fa-mug-hot fa-5x text-success"></i>
                            </div>
                            <div class="text-success text-uppercase mb-1 mt-3">
                                <strong>Beverages</strong>
                            </div>
                            <a class="btn btn-sm btn-secondary mt-2" href="{{ route('beverages') }}">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection <!-- End the content section -->
