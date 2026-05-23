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
        <div class="d-flex">
            <i class="fa fa-address-book fa-2x text-dark me-2"></i>
            <div class="d-flex flex-column">
                <h1 class="h3 mb-0 text">LIST OF GUEST</h1>
                <h6 class="mb-0">Report | Guest Report</h6>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="card col-5 m-auto shadow mb-4 px-0">
            <div class="card-body">
                <div class="col-12 p-4 text-light bg-theme-primary">
                    <div class="d-flex align-items-center gap-2 justify-content-center">
                        <img src="{{ asset('public/img/logo.png') }}" width="70" alt="la-escapo-logo">
                        <div class="d-flex flex-column">
                            <b class="modal-title mt-2 h5 text-bold">La Escapo Mountain
                                Resort</b>
                            <span>Tuno, Tibiao, Antique</span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <form action="{{ route('guestReportDate') }}">
                        <div class="form-group mb-2">
                            <div class="d-flex flex-column align-items-start gap-3">
                                <label>Select Date:</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-theme-primary text-light">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </span>
                                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                                        class="form-control rounded-0"
                                        onchange="document.getElementById('dateRangeForm').submit();">
                                </div>
                                <button type="submit" class="btn btn-success bg-green-secondary w-100">View Report</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    {{-- <div class="row">
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
                                    href="{{ route('daily.report') }}">View Report</a>
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
    </div> --}}
    <!-- Content Row -->

    {{-- <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">La Escapo Resort Bill Income Reports</h1>
    </div> --}}
    <!-- Content Row -->
    {{-- <div class="row">
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
                                    href="{{ route('daily.income.report', ['year' => $currentYear, 'month' => $currentMonth, 'week' => $currentWeek]) }}">View Report</a>
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
                                <div class="mb-2 mt-3" style="color: #045b00;">
                                    <span
                                        class="border rounded p-2 border-text h5 bg-light">₱{{ number_format($monthlyTotal, 2) }}</span>
                                </div>
                                <a class="btn text-light btn-light bg-transparent mt-2"
                                    href="{{ route('monthly.income.report', ['year' => $currentYear]) }}">
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
                                <div class="mb-2 mt-3" style="color: #045b00;">
                                    <span
                                        class="border rounded p-2 border-text h5 bg-light">₱{{ number_format($yearlyTotal, 2) }}</span>
                                </div>
                                <a class="btn text-light btn-light bg-transparent mt-2"
                                    href="{{ route('yearly.income.report') }}">View Report</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <!-- Content Row -->
@endsection <!-- End the content section -->
