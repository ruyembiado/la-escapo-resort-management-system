@extends('layouts.auth') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="d-flex">
            <i class="fas fa-file-invoice-dollar fa-2x text-dark me-2"></i>
            <div class="d-flex flex-column">
                <h1 class="h3 mb-0 text">AVAILED SERVICES</h1>
                <h6 class="mb-0">Guest | Bills</h6>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    @include('layouts.services-navigation')
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Date Filter -->
                <form method="GET" action="" id="dateRangeForm">
                    <div class="d-flex justify-content-start gap-2 align-items-end mb-4">
                        <div class="d-flex align-items-center">
                            <label class="mb-0 me-0 p-1 bg-theme-primary text-light">From:</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                class="form-control form-control-sm rounded-0"
                                onchange="document.getElementById('dateRangeForm').submit();">
                        </div>
                        <div class="d-flex align-items-center">
                            <label class="mb-0 me-0 p-1 bg-theme-primary text-light">To:</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                class="form-control form-control-sm rounded-0"
                                onchange="document.getElementById('dateRangeForm').submit();">
                        </div>
                    </div>
                    <!-- A-Z Filter -->
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        <a href="{{ request()->fullUrlWithQuery(['letter' => null]) }}"
                            class="btn btn-sm rounded-circle {{ request('letter') ? 'btn-dark' : 'btn-success' }}">
                            All
                        </a>
                        @foreach (range('A', 'Z') as $letter)
                            <a href="{{ request()->fullUrlWithQuery(['letter' => $letter]) }}"
                                class="btn btn-sm rounded-circle 
                                    {{ request('letter') == $letter ? 'btn-success' : 'btn-dark' }}"
                                style="width:32px;height:32px;line-height:22px;">
                                {{ $letter }}
                            </a>
                        @endforeach
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered border-dark" id="dataTable1" width="100%" cellspacing="0"
                    style="min-width:1500px;">
                    <thead>
                        <tr>
                            <th class="bg-theme-primary text-light">NO.</th>
                            <th class="bg-theme-primary text-light">NAME OF GUEST</th>
                            <th class="bg-theme-primary text-light">EF</th>
                            <th class="bg-theme-primary text-light">WT</th>
                            <th class="bg-theme-primary text-light">KHB</th>
                            <th class="bg-theme-primary text-light">PT</th>
                            <th class="bg-theme-primary text-light">MS</th>
                            <th class="bg-theme-primary text-light">AC</th>
                            <th class="bg-theme-primary text-light">FD</th>
                            <th class="bg-theme-primary text-light">DS</th>
                            <th class="bg-theme-primary text-light">TOTAL FEE</th>
                            <th class="bg-theme-primary text-light">STATUS</th>
                            <th class="bg-theme-primary text-light">DATE CREATED</th>
                            <th class="bg-theme-primary text-light sticky-action">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($visitors as $visitor)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ optional($visitor)->first_name }}
                                    {{ optional($visitor)->middle_name }}
                                    {{ optional($visitor)->last_name }}
                                </td>
                                <td>
                                    {{ $visitor->entrance ? '₱' . number_format($visitor->entrance->total_payment, 2) : 'none' }}
                                </td>
                                <td>
                                    {{ $visitor->watertubing ? '₱' . number_format($visitor->watertubing->total_payment, 2) : 'none' }}
                                </td>
                                <td>
                                    {{ $visitor->kawabath ? '₱' . number_format($visitor->kawabath->total_payment, 2) : 'none' }}
                                </td>
                                <td>
                                    {{ $visitor->picnictable ? '₱' . number_format($visitor->picnictable->total_payment, 2) : 'none' }}
                                </td>
                                <td>
                                    {{ $visitor->massage ? '₱' . number_format($visitor->massage->total_payment, 2) : 'none' }}
                                </td>
                                <td>
                                    {{ $visitor->accommodation ? '₱' . number_format($visitor->accommodation->total_payment, 2) : 'none' }}
                                </td>
                                <td>
                                    {{ $visitor->meal ? '₱' . number_format($visitor->meal->total_payment, 2) : 'none' }}
                                </td>
                                <td>
                                    {{ $visitor->beverage ? '₱' . number_format($visitor->beverage->total_payment, 2) : 'none' }}
                                </td>
                                @php
                                    $grand_total =
                                        ($visitor->entrance->total_payment ?? 0) +
                                        ($visitor->meal->total_payment ?? 0) +
                                        ($visitor->beverage->total_payment ?? 0) +
                                        ($visitor->kawabath->total_payment ?? 0) +
                                        ($visitor->watertubing->total_payment ?? 0) +
                                        ($visitor->picnictable->total_payment ?? 0) +
                                        ($visitor->accommodation->total_payment ?? 0);
                                @endphp
                                <td><b>₱{{ number_format($grand_total, 2) }}</b></td>
                                @php
                                    $statuses = [
                                        optional($visitor->entrance)->payment_status,
                                        optional($visitor->meal)->payment_status,
                                        optional($visitor->beverage)->payment_status,
                                        optional($visitor->kawabath)->payment_status,
                                        optional($visitor->watertubing)->payment_status,
                                        optional($visitor->picnictable)->payment_status,
                                        optional($visitor->accommodation)->payment_status,
                                        optional($visitor->massage)->payment_status,
                                    ];
                                    $filtered = array_filter($statuses);
                                    $finalStatus = 'Paid';
                                    foreach ($filtered as $status) {
                                        if ($status === 'Unpaid') {
                                            $finalStatus = 'Unpaid';
                                            break;
                                        }
                                    }
                                @endphp
                                <td>
                                    @if ($finalStatus === 'Unpaid')
                                        <span class="badge bg-danger">Unpaid</span>
                                    @else
                                        <span class="badge bg-success">Paid</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($visitor->created_at)->format('M d, Y') }}</td>
                                <td class="sticky-action"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->
