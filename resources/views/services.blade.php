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
            <div class="card bg-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex h-100 flex-column justify-content-center">
                        <div class="row align-items-center justify-content-between">
                            <div class="col mr-2 text-center">
                                <div class="col-auto">
                                    <img class="img-fluid" src="{{ asset('public/img/Entrance Fee.png') }}"
                                        alt="Entrance Fee">
                                </div>
                                <div class="text-light text-uppercase mb-1 mt-3">
                                    <strong>Entrance Fee</strong>
                                </div>
                                <a class="btn text-light btn-light bg-transparent mt-2"
                                    href="{{ url('/entrances') }}"><i class="fa fa-eye"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Other Services -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex h-100 flex-column justify-content-center">
                        <div class="row align-items-center justify-content-between">
                            <div class="col mr-2 text-center">
                                <div class="col-auto">
                                    <img class="img-fluid" src="{{ asset('public/img/Services.png') }}" alt="Services">
                                </div>
                                <div class="text-light text-uppercase mb-1 mt-3">
                                    <strong>Services</strong>
                                </div>
                                <a class="btn text-light btn-light bg-transparent mt-2"
                                    href="{{ route('other.services') }}"><i class="fa fa-eye"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meals -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex h-100 flex-column justify-content-center">
                        <div class="row align-items-center justify-content-between">
                            <div class="col mr-2 text-center">
                                <div class="col-auto">
                                    <img class="img-fluid" src="{{ asset('public/img/Meals.png') }}" alt="Meals">
                                </div>
                                <div class="text-light text-uppercase mb-1 mt-3">
                                    <strong>Meals</strong>
                                </div>
                                <a class="btn text-light btn-light bg-transparent mt-2" href="{{ route('meals') }}"><i class="fa fa-eye"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Beverages -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex h-100 flex-column justify-content-center">
                        <div class="row align-items-center justify-content-between">
                            <div class="col mr-2 text-center">
                                <div class="col-auto">
                                    <img class="img-fluid" src="{{ asset('public/img/Beverages.png') }}" alt="Beverages">
                                </div>
                                <div class="text-light text-uppercase mb-1 mt-3">
                                    <strong>Beverages</strong>
                                </div>
                                <a class="btn text-light btn-light bg-transparent mt-2"
                                    href="{{ route('beverages') }}"><i class="fa fa-eye"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection <!-- End the content section -->
