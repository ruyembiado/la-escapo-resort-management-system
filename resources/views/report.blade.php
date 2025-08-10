@extends('layouts.auth') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Summary Reports</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex h-100 flex-column justify-content-center">
                        <div class="row align-items-center justify-content-between">
                            <div class="col mr-2 text-center">
                                <div class="col-auto">
                                    <i class="fa fa-book fa-5x text-light"></i>
                                </div>
                                <div class="text-light text-uppercase mb-1 mt-3">
                                    <strong>Daily Summary Report</strong>
                                </div>
                                <a class="btn text-light btn-light bg-transparent mt-2" href="{{ route('daily.report') }}">View Report</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex h-100 flex-column justify-content-center">
                        <div class="row align-items-center justify-content-between">
                            <div class="col mr-2 text-center">
                                <div class="col-auto">
                                    <i class="fa fa-book fa-5x text-light"></i>
                                </div>
                                <div class="text-light text-uppercase mb-1 mt-3">
                                    <strong>Weekly Summary Report</strong>
                                </div>

                                @php
                                    $currentYear = now()->year;
                                    $currentMonth = now()->month;
                                    // Get the start of the current month
                                    $startOfMonth = now()->startOfMonth();
                                    // Get the current date's ISO week number relative to the start of the month
                                    $currentWeek = now()->diffInWeeks($startOfMonth) + 1; // Add 1 to ensure the week starts at 1
                                @endphp

                                <a class="btn text-light btn-light bg-transparent mt-2"
                                    href="{{ route('weekly.report', ['year' => $currentYear, 'month' => $currentMonth, 'week' => $currentWeek]) }}">
                                    View Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex h-100 flex-column justify-content-center">
                        <div class="row align-items-center justify-content-between">
                            <div class="col mr-2 text-center">
                                <div class="col-auto">
                                    <i class="fa fa-book fa-5x text-light"></i>
                                </div>
                                <div class="text-light text-uppercase mb-1 mt-3">
                                    <strong>Monthly Summary Report</strong>
                                </div>
                                <a class="btn text-light btn-light bg-transparent mt-2"
                                    href="{{ route('monthly.report', ['year' => now()->year, 'month' => now()->month]) }}">
                                    View Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex h-100 flex-column justify-content-center">
                        <div class="row align-items-center justify-content-between">
                            <div class="col mr-2 text-center">
                                <div class="col-auto">
                                    <i class="fa fa-book fa-5x text-light"></i>
                                </div>
                                <div class="text-light text-uppercase mb-1 mt-3">
                                    <strong>Yearly Summary Report</strong>
                                </div>
                                <a class="btn text-light btn-light bg-transparent mt-2"
                                    href="{{ route('yearly.report', ['year' => now()->year]) }}">View Report</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Row -->

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">La Escapo Resort Bill Income Reports</h1>
    </div>
    <!-- Content Row -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex h-100 flex-column justify-content-center">
                        <div class="row align-items-center justify-content-between">
                            <div class="col mr-2 text-center">
                                <div class="col-auto">
                                    <i class="fa fa-book fa-5x text-light"></i>
                                </div>
                                <div class="text-light text-uppercase mb-1 mt-3">
                                    <strong>Daily Income Report</strong>
                                </div>
                                {{-- <div class="text-light mb-3 mt-3">
                                    <span class="border rounded p-2 border-text h5">P5000.00</span>
                                </div> --}}
                                <a class="btn text-light btn-light bg-transparent mt-2" href="{{ route('daily.income.report') }}">View Report</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex h-100 flex-column justify-content-center">
                        <div class="row align-items-center justify-content-between">
                            <div class="col mr-2 text-center">
                                <div class="col-auto">
                                    <i class="fa fa-book fa-5x text-light"></i>
                                </div>
                                <div class="text-light text-uppercase mb-1 mt-3">
                                    <strong>Weekly Income Report</strong>
                                </div>
                                {{-- <div class="text-light mb-3 mt-3">
                                    <span class="border rounded p-2 border-text h5">P5000.00</span>
                                </div> --}}
                                @php
                                    $currentYear = now()->year;
                                    $currentMonth = now()->month;
                                    // Get the start of the current month
                                    $startOfMonth = now()->startOfMonth();
                                    // Get the current date's ISO week number relative to the start of the month
                                    $currentWeek = now()->diffInWeeks($startOfMonth) + 1; // Add 1 to ensure the week starts at 1
                                @endphp

                                <a class="btn text-light btn-light bg-transparent mt-2"
                                    href="{{ route('weekly.report', ['year' => $currentYear, 'month' => $currentMonth, 'week' => $currentWeek]) }}">
                                    View Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex h-100 flex-column justify-content-center">
                        <div class="row align-items-center justify-content-between">
                            <div class="col mr-2 text-center">
                                <div class="col-auto">
                                    <i class="fa fa-book fa-5x text-light"></i>
                                </div>
                                <div class="text-light text-uppercase mb-1 mt-3">
                                    <strong>Monthly Income Report</strong>
                                </div>
                                {{-- <div class="text-light mb-3 mt-3">
                                    <span class="border rounded p-2 border-text h5">P5000.00</span>
                                </div> --}}
                                <a class="btn text-light btn-light bg-transparent mt-2"
                                    href="{{ route('monthly.report', ['year' => now()->year, 'month' => now()->month]) }}">
                                    View Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex h-100 flex-column justify-content-center">
                        <div class="row align-items-center justify-content-between">
                            <div class="col mr-2 text-center">
                                <div class="col-auto">
                                    <i class="fa fa-book fa-5x text-light"></i>
                                </div>
                                <div class="text-light text-uppercase mb-1 mt-3">
                                    <strong>Yearly Income Report</strong>
                                </div>
                                {{-- <div class="text-light mb-3 mt-3">
                                    <span class="border rounded p-2 border-text h5">P5000.00</span>
                                </div> --}}
                                <a class="btn text-light btn-light bg-transparent mt-2"
                                    href="{{ route('yearly.report', ['year' => now()->year]) }}">View Report</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->
