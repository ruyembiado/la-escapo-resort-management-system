@extends('layouts.auth') 

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="d-flex">
            <i class="fa fa-money-bill fa-2x text-dark me-2"></i>
            <div class="d-flex flex-column">
                <h1 class="h3 mb-0 text">AVAILED SERVICES</h1>
                <h6 class="mb-0">Guest | Bill History</h6>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row justify-content-center">
        <div class="card col-8 shadow mb-4 text-center">
            <div class="card-body">
                <div id="calendar"></div>
                <button class="btn btn-primary mt-3 d-none" data-bs-toggle="modal" data-bs-target="#viewBillModal">
                    View Bill
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewBillModal" tabindex="-1" role="dialog" aria-labelledby="viewBillModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mt-3" style="max-width:1500px; margin:auto;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="d-flex gap-3 bg-light p-3 rounded overflow-auto" style="white-space: nowrap;">
                    @foreach ($visitors as $visitor)
                        <div class="card visitor-card col-4 px-1 py-2 shadow-sm mb-0"
                            data-date="{{ \Carbon\Carbon::parse($visitor->created_at)->format('Y-m-d') }}">
                            <div class="col-12">
                                <div class="d-flex align-items-center gap-2 justify-content-center">
                                    <img src="{{ asset('public/img/logo.png') }}" width="70" alt="la-escapo-logo">
                                    <div class="d-flex flex-column">
                                        <b class="modal-title mt-2 text-bold">La Escapo Mountain
                                            Resort</b>
                                        <span>Tuno, Tibiao, Antique</span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div
                                class="bg-theme-primary d-flex align-items-center gap-2 justify-content-center text-light p-2">
                                <h3 class="m-0">BILL RECEIPT</h3>
                            </div>
                            <div class="visitor-name my-2 d-flex align-items-center gap-2">
                                <label class="col-4" for="visitorName">Visitor Name:</label>
                                <input type="text" class="form-control" id="visitorName"
                                    value="{{ $visitor->first_name . ' ' . $visitor->middle_name . ' ' . $visitor->last_name }}"
                                    readonly>
                            </div>
                            <div class="table-responsive p-0">
                                <table class="table table-bordered border-N/A m-0">
                                    <thead class="bg-success text-light">
                                        <tr>
                                            <th style="border-width: 0px" class="text-start bg-green-tertiary text-light">
                                                AVAILED SERVICES</th>
                                            <th style="border-width: 0px" class="text-start bg-green-tertiary text-light">
                                                FEE STATUS
                                            </th>
                                            <th style="border-width: 0px" class="text-center bg-green-tertiary text-light">
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
                                            <td style="border-width: 0px" colspan="2" class="text-start fw-bold">
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
                    @endforeach
                    <div id="noVisitorsMessage" class="w-100 text-center py-5">
                        <h5 class="text-muted">No visitors found</h5>
                        <small class="text-secondary">There are no records for this selected date.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
@endsection <!-- End the content section -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const visitors = @json($visitors);

        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: visitors.map(v => ({
                allDay: true
            })),

            dateClick: function(info) {
                let selectedDate = info.dateStr;
                let hasVisitor = false;

                document.querySelectorAll('.visitor-card').forEach(card => {
                    if (card.dataset.date === selectedDate) {
                        card.style.display = 'block';
                        hasVisitor = true;
                    } else {
                        card.style.display = 'none';
                    }
                });

                const message = document.getElementById('noVisitorsMessage');
                if (hasVisitor) {
                    message.style.display = 'none';
                } else {
                    message.style.display = 'block';
                }

                var modal = new bootstrap.Modal(document.getElementById('viewBillModal'));
                modal.show();
            }
        });
        calendar.render();
    });
</script>
