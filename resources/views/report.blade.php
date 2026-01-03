@extends('layouts.auth') <!-- Extend the main layout -->

@php
    use Carbon\Carbon;

    $today = now();
    $currentYear = $today->year;
    $currentMonth = $today->month;

    // Start of the month
    $startOfMonth = Carbon::create($currentYear, $currentMonth, 1);

    // First week start (always Sunday)
    $firstWeekStart = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);

    // Week number (Sunday–Saturday based)
    $currentWeek = floor($firstWeekStart->diffInDays($today) / 7) + 1;
@endphp

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
                                <a class="btn text-light btn-light bg-transparent mt-2"
                                    href="{{ route('daily.report') }}"><i class="fa fa-eye"></i></a>
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
                                <a class="btn text-light btn-light bg-transparent mt-2"
                                    href="{{ route('weekly.report', ['year' => $currentYear, 'month' => $currentMonth, 'week' => $currentWeek]) }}">
                                    <i class="fa fa-eye"></i>
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
                                    <i class="fa fa-eye"></i>
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
                                    href="{{ route('yearly.report', ['year' => now()->year]) }}"><i class="fa fa-eye"></i></a>
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
                                <div class="mb-2 mt-3" style="color: #045b00;">
                                    <span
                                        class="border rounded p-2 border-text h5 bg-light">₱{{ number_format($dailyTotal, 2) }}</span>
                                </div>
                                <a class="btn text-light btn-light bg-transparent mt-2"
                                    href="{{ route('daily.income.report', ['year' => $currentYear, 'month' => $currentMonth, 'week' => $currentWeek]) }}"><i class="fa fa-eye"></i></a>
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
                                <div class="mb-2 mt-3" style="color: #045b00;">
                                    <span
                                        class="border rounded p-2 border-text h5 bg-light">₱{{ number_format($weeklyTotal, 2) }}</span>
                                </div>
                                <a class="btn text-light btn-light bg-transparent mt-2"
                                    href="{{ route('weekly.income.report', ['year' => $currentYear, 'month' => $currentMonth]) }}">
                                    <i class="fa fa-eye"></i>
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
                                <div class="mb-2 mt-3" style="color: #045b00;">
                                    <span
                                        class="border rounded p-2 border-text h5 bg-light">₱{{ number_format($monthlyTotal, 2) }}</span>
                                </div>
                                <a class="btn text-light btn-light bg-transparent mt-2"
                                    href="{{ route('monthly.income.report', ['year' => $currentYear]) }}">
                                    <i class="fa fa-eye"></i>
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
                                <div class="mb-2 mt-3" style="color: #045b00;">
                                    <span
                                        class="border rounded p-2 border-text h5 bg-light">₱{{ number_format($yearlyTotal, 2) }}</span>
                                </div>
                                <a class="btn text-light btn-light bg-transparent mt-2"
                                    href="{{ route('yearly.income.report') }}"><i class="fa fa-eye"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->
