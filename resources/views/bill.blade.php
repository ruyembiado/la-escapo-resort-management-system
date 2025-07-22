@extends('layouts.auth') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text">Visitor's Bill Summary</h1>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-body">
            {{-- <form method="GET" action="" class="" id="dateRangeForm">
                <div class="d-flex justify-content-start gap-2 align-items-end mb-4">
                    <div class="d-flex flex-column align-items-start" style="width: auto;">
                        <label for="date" class="mb-0">Start Date:</label>
                        <input type="date" name="start_date" value="{{ $start_date }}"
                            class="form-control form-control-sm" style="width: auto;" id="start_date" />
                    </div>
                    <div class="d-flex flex-column align-items-start" style="width: auto;">
                        <label for="date" class="mb-0">End Date:</label>
                        <input type="date" name="end_date" value="{{ $end_date }}"
                            class="form-control form-control-sm" style="width: auto;" id="end_date" />
                    </div>

                    <a href="{{ url()->current() }}" class="btn btn-sm btn-danger">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form> --}}

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Entrance Fee</th>
                            <th>Status</th>
                            <th>Meals</th>
                            <th>Status</th>
                            <th>Beverages</th>
                            <th>Status</th>
                            <th>Kawa Hot Bath</th>
                            <th>Status</th>
                            <th>Water Tubing</th>
                            <th>Status</th>
                            <th>Picnic Table</th>
                            <th>Status</th>
                            <th>Accommodation</th>
                            <th>Status</th>
                            <th>Total Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($visitors as $visitor)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $visitor->first_name . ' ' . $visitor->middle_name . ' ' . $visitor->last_name }}
                                </td>
                                <td>
                                    {{ $visitor->entrance ? '₱' . number_format($visitor->entrance->total_payment, 2) : 'N/A' }}
                                </td>
                                <td>
                                    @if ($visitor->entrance)
                                        @if ($visitor->entrance->payment_status === 'pending')
                                            <span
                                                class="badge bg-danger">{{ ucfirst($visitor->entrance->payment_status) }}</span>
                                        @elseif ($visitor->entrance->payment_status === 'paid')
                                            <span
                                                class="badge bg-success">{{ ucfirst($visitor->entrance->payment_status) }}</span>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    {{ $visitor->meal ? '₱' . number_format($visitor->meal->total_payment, 2) : 'N/A' }}
                                </td>
                                <td>
                                    @if ($visitor->meal)
                                        @if ($visitor->meal->payment_status === 'pending')
                                            <span
                                                class="badge bg-danger">{{ ucfirst($visitor->meal->payment_status) }}</span>
                                        @elseif ($visitor->meal->payment_status === 'paid')
                                            <span
                                                class="badge bg-success">{{ ucfirst($visitor->meal->payment_status) }}</span>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    {{ $visitor->beverage ? '₱' . number_format($visitor->beverage->total_payment, 2) : 'N/A' }}
                                </td>
                                <td>
                                    @if ($visitor->beverage)
                                        @if ($visitor->beverage->payment_status === 'pending')
                                            <span
                                                class="badge bg-danger">{{ ucfirst($visitor->beverage->payment_status) }}</span>
                                        @elseif ($visitor->beverage->payment_status === 'paid')
                                            <span
                                                class="badge bg-success">{{ ucfirst($visitor->beverage->payment_status) }}</span>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    {{ $visitor->kawabath ? '₱' . number_format($visitor->kawabath->total_payment, 2) : 'N/A' }}
                                </td>
                                <td>
                                    @if ($visitor->kawabath)
                                        @if ($visitor->kawabath->payment_status === 'pending')
                                            <span
                                                class="badge bg-danger">{{ ucfirst($visitor->kawabath->payment_status) }}</span>
                                        @elseif ($visitor->kawabath->payment_status === 'paid')
                                            <span
                                                class="badge bg-success">{{ ucfirst($visitor->kawabath->payment_status) }}</span>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    {{ $visitor->watertubing ? '₱' . number_format($visitor->watertubing->total_payment, 2) : 'N/A' }}
                                </td>
                                <td>
                                    @if ($visitor->watertubing)
                                        @if ($visitor->watertubing->payment_status === 'pending')
                                            <span
                                                class="badge bg-danger">{{ ucfirst($visitor->watertubing->payment_status) }}</span>
                                        @elseif ($visitor->watertubing->payment_status === 'paid')
                                            <span
                                                class="badge bg-success">{{ ucfirst($visitor->watertubing->payment_status) }}</span>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    {{ $visitor->picnictable ? '₱' . number_format($visitor->picnictable->total_payment, 2) : 'N/A' }}
                                </td>
                                <td>
                                    @if ($visitor->picnictable)
                                        @if ($visitor->picnictable->payment_status === 'pending')
                                            <span
                                                class="badge bg-danger">{{ ucfirst($visitor->picnictable->payment_status) }}</span>
                                        @elseif ($visitor->picnictable->payment_status === 'paid')
                                            <span
                                                class="badge bg-success">{{ ucfirst($visitor->picnictable->payment_status) }}</span>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    {{ $visitor->accommodation ? '₱' . number_format($visitor->accommodation->total_payment, 2) : 'N/A' }}
                                </td>
                                <td>
                                    @if ($visitor->accommodation)
                                        @if ($visitor->accommodation->payment_status === 'pending')
                                            <span
                                                class="badge bg-danger">{{ ucfirst($visitor->accommodation->payment_status) }}</span>
                                        @elseif ($visitor->accommodation->payment_status === 'paid')
                                            <span
                                                class="badge bg-success">{{ ucfirst($visitor->accommodation->payment_status) }}</span>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                @php
                                    $grand_total =
                                        ($visitor->entrance->total_payment ?? 0) +
                                        ($visitor->meal->total_payment ?? 0) +
                                        ($visitor->beverage->total_payment ?? 0) +
                                        ($visitor->kawabath->total_payment ?? 0) +
                                        ($visitor->watertubing->total_payment ?? 0) +
                                        ($visitor->picnictable->total_payment ?? 0)+
                                        ($visitor->accommodation->total_payment ?? 0);
                                @endphp
                                <td><b>₱{{ number_format($grand_total, 2) }}</b></td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->
