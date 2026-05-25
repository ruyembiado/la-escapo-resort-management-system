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
                            class="btn btn-sm rounded-circle {{ request('letter') ? 'btn-dark' : 'btn bg-green-tertiary text-light' }}">
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
                    style="min-width:2000px;">
                    <thead>
                        <tr>
                            <th class="bg-green-secondary text-light text-center">NO.</th>
                            <th class="bg-green-secondary text-light">NAME OF GUEST</th>
                            <th class="bg-green-secondary text-light">ENTRANCE FEE</th>
                            <th class="bg-green-secondary text-light">WATER TUBING</th>
                            <th class="bg-green-secondary text-light">KAWA BATH</th>
                            <th class="bg-green-secondary text-light">PICNIC TABLE</th>
                            <th class="bg-green-secondary text-light">MASSAGE</th>
                            <th class="bg-green-secondary text-light">ACCOMMODATION</th>
                            <th class="bg-green-secondary text-light">FOODS</th>
                            <th class="bg-green-secondary text-light">DRINKS</th>
                            <th class="bg-green-secondary text-light">TOTAL FEE</th>
                            <th class="bg-green-secondary text-light">STATUS</th>
                            <th class="bg-green-secondary text-light">DATE CREATED</th>
                            <th class="bg-green-secondary text-light sticky-action">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($visitors as $visitor)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    {{ optional($visitor)->first_name }}
                                    {{ optional($visitor)->middle_name }}
                                    {{ optional($visitor)->last_name }}
                                </td>
                                <td>
                                    {{ $visitor->entrance ? '₱' . number_format($visitor->entrance->total_payment, 2) : 'N/A' }}
                                </td>
                                <td>
                                    {{ $visitor->watertubing ? '₱' . number_format($visitor->watertubing->total_payment, 2) : 'N/A' }}
                                </td>
                                <td>
                                    {{ $visitor->kawabath ? '₱' . number_format($visitor->kawabath->total_payment, 2) : 'N/A' }}
                                </td>
                                <td>
                                    {{ $visitor->picnictable ? '₱' . number_format($visitor->picnictable->total_payment, 2) : 'N/A' }}
                                </td>
                                <td>
                                    {{ $visitor->massage ? '₱' . number_format($visitor->massage->total_payment, 2) : 'N/A' }}
                                </td>
                                <td>
                                    {{ $visitor->accommodation ? '₱' . number_format($visitor->accommodation->total_payment, 2) : 'N/A' }}
                                </td>
                                <td>
                                    {{ $visitor->meal ? '₱' . number_format($visitor->meal->total_payment, 2) : 'N/A' }}
                                </td>
                                <td>
                                    {{ $visitor->beverage ? '₱' . number_format($visitor->beverage->total_payment, 2) : 'N/A' }}
                                </td>
                                @php
                                    $grand_total =
                                        ($visitor->entrance->total_payment ?? 0) +
                                        ($visitor->meal->total_payment ?? 0) +
                                        ($visitor->beverage->total_payment ?? 0) +
                                        ($visitor->kawabath->total_payment ?? 0) +
                                        ($visitor->watertubing->total_payment ?? 0) +
                                        ($visitor->picnictable->total_payment ?? 0) +
                                        ($visitor->accommodation->total_payment ?? 0) +
                                        ($visitor->massage->total_payment ?? 0);
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
                                <td class="sticky-action text-center">
                                    <a href="#" class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#viewBillModal_{{ $visitor->id }}"
                                        data-visitor="{{ $visitor->id }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @foreach ($visitors as $visitor)
                    <!-- View Bill Modal -->
                    <div class="modal fade" id="viewBillModal_{{ $visitor->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="viewBillModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md" role="document">
                            <div class="modal-content">
                                <div class="modal-header p-3">
                                    <div class="col-12 p-2 pb-4 text-light bg-theme-primary">
                                        <div class="text-end">
                                            <button type="button" class="btn-close btn-close-white btn-circle"
                                                data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 justify-content-center">
                                            <img src="{{ asset('public/img/logo.png') }}" width="70"
                                                alt="la-escapo-logo">
                                            <div class="d-flex flex-column">
                                                <b class="modal-title mt-2 h5 text-bold">La Escapo Mountain
                                                    Resort</b>
                                                <span>Tuno, Tibiao, Antique</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-body ">
                                    <div class="row p-2">
                                        <div
                                            class="bg-green-secondary d-flex align-items-center gap-2 justify-content-center text-light p-2">
                                            <h3 class="m-0">BILL RECEIPT</h3>
                                        </div>
                                        <div class="visitor-name my-2 d-flex align-items-center gap-2">
                                            <label class="col-3" for="visitorName">Name:</label>
                                            <input type="text" class="form-control" id="visitorName"
                                                value="{{ $visitor->first_name . ' ' . $visitor->middle_name . ' ' . $visitor->last_name }}"
                                                readonly>
                                        </div>
                                        <div class="table-responsive p-0">
                                            <table class="table table-bordered border-N/A m-0">
                                                <thead class="bg-success text-light">
                                                    <tr>
                                                        <th style="border-width: 0px"
                                                            class="text-start bg-green-tertiary text-light">
                                                            AVAILED SERVICES</th>
                                                        <th style="border-width: 0px"
                                                            class="text-start bg-green-tertiary text-light">
                                                            FEE STATUS
                                                        </th>
                                                        <th style="border-width: 0px"
                                                            class="text-center bg-green-tertiary text-light">
                                                            AMOUNT
                                                            FEE</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $services = [
                                                            'entrance' => 'Entrance Fee',
                                                            'watertubing' => 'Water Tubing',
                                                            'kawabath' => 'Kawa Hot Bath',
                                                            'picnictable' => 'Picnic Table',
                                                            'massage' => 'Massage',
                                                            'accommodation' => 'Accommodation',
                                                            'meal' => 'Foods',
                                                            'beverage' => 'Drinks',
                                                        ];
                                                        $modal_total = 0;
                                                    @endphp
                                                    @foreach ($services as $key => $label)
                                                        @if ($visitor->$key)
                                                            @php $modal_total += $visitor->$key->total_payment; @endphp

                                                            @php
                                                                $status =
                                                                    $visitor->$key->payment_status ??
                                                                    ($visitor->$key->status ?? 'Unpaid');
                                                            @endphp

                                                            <tr>
                                                                <td style="border-width: 0px"
                                                                    class="{{ $status == 'Unpaid' ? 'text-danger' : 'text-dark' }}">
                                                                    {{ $label }}
                                                                </td>

                                                                <td style="border-width: 0px" class="text-center">
                                                                    <span
                                                                        class="badge {{ $status == 'Unpaid' ? 'bg-danger' : 'bg-success' }}">
                                                                        {{ $status }}
                                                                    </span>
                                                                </td>

                                                                <td style="border-width: 0px" class="text-center">
                                                                    ₱{{ number_format($visitor->$key->total_payment, 2) }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach

                                                    <!-- TOTAL ROW -->
                                                    <tr class="bg-dark text-light">
                                                        <td style="border-width: 0px" colspan="2"
                                                            class="text-start fw-bold">
                                                            TOTAL
                                                            FEE
                                                        </td>
                                                        <td style="border-width: 0px" class="text-center fw-semibold">
                                                            ₱{{ number_format($modal_total, 2) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->
