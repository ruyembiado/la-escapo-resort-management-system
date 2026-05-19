@extends('layouts.auth') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="d-flex">
            <i class="fas fa-spa fa-2x text-dark me-2"></i>
            <div class="d-flex flex-column">
                <h1 class="h3 mb-0 text">AVAILED SERVICES</h1>
                <h6 class="mb-0">Guest | Massage</h6>
            </div>
        </div>
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMassageModal">Add Massage &
            Accommodation
            Fee</a>
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
            <div class="d-flex gap-2">
                <a href="{{ url('massages') }}" class="btn bg-green-tertiary text-light d-flex align-items-center gap-2">
                    <i class="fa-solid fa-spa"></i>
                    Massage
                </a>
                <a href="{{ url('accommodations') }}"
                    class="btn bg-theme-primary text-light d-flex align-items-center gap-2">
                    <i class="fa-solid fa-bed"></i>
                    Accommodation
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered border-dark" id="dataTable1" width="100%" cellspacing="0"
                    style="min-width:2000px;">
                    <thead>
                        <tr>
                            <th class="bg-theme-primary text-light">NO.</th>
                            <th class="bg-theme-primary text-light">MAIN GUEST</th>
                            <th class="bg-theme-primary text-light text-center">TOTAL MEMBERS</th>
                            <th class="bg-theme-primary text-light text-center">SERVICE DETAILS</th>
                            <th class="bg-theme-primary text-light">TOTAL FEE</th>
                            <th class="bg-theme-primary text-light">STATUS</th>
                            <th class="bg-theme-primary text-light">DATE CREATED</th>
                            <th class="bg-theme-primary text-light sticky-action">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($massages as $massage)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ optional($massage->visitor)->first_name }}
                                    {{ optional($massage->visitor)->middle_name }}
                                    {{ optional($massage->visitor)->last_name }}
                                </td>
                                <td class="text-center">
                                    {{ $massage->visitor->companions->count() + 1 }}
                                </td>
                                @php
                                    $members = json_decode($massage->members, true);
                                @endphp
                                <td class="p-0">
                                    <table class="table table-bordered border-dark m-0" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th class="bg-green-tertiary text-light">No.</th>
                                                <th class="bg-green-tertiary text-light">Guest</th>
                                                <th class="bg-green-tertiary text-light">Age</th>
                                                <th class="bg-green-tertiary text-light">Item</th>
                                                <th class="bg-green-tertiary text-light">Quantity</th>
                                                <th class="bg-green-tertiary text-light">Fee</th>
                                                <th class="bg-green-tertiary text-light">Sub-Total</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @php
                                                $members = json_decode($massage->members, true) ?? [];
                                            @endphp
                                            @foreach ($members as $gIndex => $guest)
                                                @if (!empty($guest['services']))
                                                    @foreach ($guest['services'] as $sIndex => $service)
                                                        <tr>
                                                            @if ($sIndex === 0)
                                                                <td rowspan="{{ count($guest['services']) }}"
                                                                    class="text-center">
                                                                    {{ $gIndex + 1 }}
                                                                </td>
                                                                <td rowspan="{{ count($guest['services']) }}">
                                                                    {{ $guest['guest'] }}
                                                                    @if (!empty($guest['is_main']))
                                                                        (Main Guest)
                                                                    @endif
                                                                </td>
                                                                <td rowspan="{{ count($guest['services']) }}"
                                                                    class="text-center">
                                                                    {{ $guest['age'] ?? 'N/A' }}
                                                                </td>
                                                            @endif
                                                            <td>{{ $service['service_name'] }}</td>
                                                            <td class="text-center">
                                                                {{ $service['qty'] }}
                                                            </td>
                                                            <td>
                                                                ₱{{ number_format($service['fee'], 2) }}
                                                            </td>
                                                            <td>
                                                                ₱{{ number_format($service['subtotal'], 2) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                                <td>₱ {{ number_format($massage->total_payment, 2) }}</td>
                                <td>
                                    @if ($massage->payment_status === 'Unpaid')
                                        <span class="badge bg-danger">{{ ucfirst($massage->payment_status) }}</span>
                                    @else
                                        <span class="badge bg-success">{{ ucfirst($massage->payment_status) }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($massage->created_at)->format('F j, Y') }}</td>
                                <td class="sticky-action">
                                    <div class="d-flex align-items-center justify-c gap-2">
                                        <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editMassageModal" data-id="{{ $massage->id }}"
                                            data-visitor-id="{{ $massage->visitor_id }}"
                                            data-total-members='@json(json_decode($massage->members))'
                                            data-total-payment="{{ $massage->total_payment }}"
                                            data-payment-status="{{ $massage->payment_status }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('massage.destroy', $massage->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this massage record?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Content Row -->

    <!-- Add Massage Fee Modal -->
    <div class="modal fade" id="addMassageModal" tabindex="-1" role="dialog" aria-labelledby="addMassageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="max-width: 1500px;">
            <form action="{{ route('massage.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-12 p-2 pb-4 text-light bg-theme-primary">
                            <div class="text-end">
                                <button type="button" class="btn-close btn-close-white btn-circle"
                                    data-bs-dismiss="modal"></button>
                            </div>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                <img src="{{ asset('public/img/logo.png') }}" width="70" alt="la-escapo-logo">
                                <div class="d-flex flex-column">
                                    <b class="modal-title mt-2 h5 text-bold">La Escapo Mountain
                                        Resort</b>
                                    <span>Tuno, Tibiao, Antique</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <div class="d-flex align-items-start gap-1">
                                <div class="col-6 d-flex align-items-center gap-3">
                                    <label for="visitor_id">Name:</label>
                                    <select name="visitor_id" class="form-control select2" id="visitor_name" required
                                        data-placeholder="Select a visitor">
                                        <option></option>
                                        @foreach ($visitors as $visitor)
                                            <option value="{{ $visitor->id }}">{{ $visitor->first_name }}
                                                {{ $visitor->middle_name }}
                                                {{ $visitor->last_name }} -
                                                {{ \Carbon\Carbon::parse($visitor->date_visit)->format('F j, Y') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div
                                    class="bg-green-secondary d-flex align-items-center gap-2 justify-content-center text-light p-2 mb-3">
                                    <i class="fa fa-spa fa-2x"></i>
                                    <h3 class="m-0">MASSAGE</h3>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered border-dark"
                                        style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th class="bg-green-tertiary text-light" style="padding: 10px;">NO.</th>
                                                <th class="bg-green-tertiary text-light" style="padding: 10px;">GUEST.
                                                </th>
                                                <th class="bg-green-tertiary text-light" style="padding: 10px;">AGE</th>
                                                <th class="bg-green-tertiary text-light" style="padding: 10px;">CATEGORY
                                                </th>
                                                <th width="15%" class="bg-green-tertiary text-light"
                                                    style="padding: 10px;">QUANTITY
                                                </th>
                                                <th class="bg-green-tertiary text-light" style="padding: 10px;">FEE</th>
                                                <th width="18%" class="bg-green-tertiary text-light"
                                                    style="padding: 10px;">SUB-TOTAL
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="addMassageTableBody">
                                        </tbody>
                                    </table>
                                    <!-- TOTAL -->
                                    <div class="form-group mt-2">
                                        <div class="d-flex align-items-center justify-content-end gap-3">
                                            <label>Payment Status:</label>
                                            <div class="col-3">
                                                <select name="massage_payment_status" class="form-control">
                                                    <option value="">Select status</option>
                                                    <option value="Paid">Paid</option>
                                                    <option value="Unpaid">Unpaid</option>
                                                </select>
                                            </div>

                                            <label>Total Fee:</label>
                                            <div class="col-3">
                                                <div class="d-flex">
                                                    <span class="input-group-text bg-theme-primary text-light">₱</span>
                                                    <input type="text" name="massage_total_payment"
                                                        id="massage_total_payment" value="0.00" class="form-control"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <div
                                        class="bg-green-secondary d-flex align-items-center gap-2 justify-content-center text-light p-2 mb-3">
                                        <i class="fa fa-bed fa-2x"></i>
                                        <h3 class="m-0">ACCOMMODATION</h3>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered border-dark"
                                            style="width: 100%; border-collapse: collapse;">
                                            <thead>
                                                <tr>
                                                    <th class="bg-green-tertiary text-light" style="padding: 10px;">ROOM
                                                    </th>
                                                    <th class="bg-green-tertiary text-light" style="padding: 10px;">NO. OF
                                                        NIGHTS</th>
                                                    <th class="bg-green-tertiary text-light" style="padding: 10px;">FEE
                                                    </th>
                                                    <th class="bg-green-tertiary text-light" style="padding: 10px;">
                                                        SUB-TOTAL</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($accommodationFees as $index => $room)
                                                    <tr>
                                                        <td class="align-middle" width="" style="padding: 5px;">
                                                            {{ $room['service_name'] }}
                                                            <input class="form-control" name="rooms[]" type="hidden"
                                                                value="{{ $room['service_name'] }}" readonly>
                                                        </td>
                                                        <td width="20%">
                                                            <input class="form-control" type="number" name="nights[]"
                                                                min="0" value="0">
                                                        </td>
                                                        <td class="align-middle" width="15%" style="padding: 5px;">
                                                            ₱{{ number_format($room['fee'], 2) }}
                                                            <input class="form-control room-fee" type="hidden"
                                                                name="fees[]" min="0"
                                                                value="{{ $room['fee'] }}" readonly>
                                                        </td>
                                                        <td width="18%">
                                                            <input type="text"
                                                                class="form-control accommodation-subtotal" value="0.00"
                                                                readonly>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- TOTAL -->
                                    <div class="form-group mt-0">
                                        <div class="d-flex align-items-center justify-content-end gap-3">
                                            <label>Payment Status:</label>
                                            <div class="col-3">
                                                <select name="accommodation_payment_status" class="form-control">
                                                    <option value="">Select status</option>
                                                    <option value="Paid">Paid</option>
                                                    <option value="Unpaid">Unpaid</option>
                                                </select>
                                            </div>

                                            <label>Total Fee:</label>
                                            <div class="col-3">
                                                <div class="d-flex">
                                                    <span class="input-group-text bg-theme-primary text-light">₱</span>
                                                    <input type="text" name="accommodation_total_payment"
                                                        id="accommodation_total_payment" value="0.00"
                                                        class="form-control" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-theme-primary text-light">Save</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Massage Fee Modal -->
    <div class="modal fade" id="editMassageModal" tabindex="-1" role="dialog" aria-labelledby="editMassageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('massage.update') }}" method="POST">
                <input type="hidden" name="massage_id" id="edit_massage_id">
                <input type="hidden" name="visitor_id" id="_visitor_id">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-12 p-2 pb-4 text-light bg-theme-primary">
                            <div class="text-end">
                                <button type="button" class="btn-close btn-close-white btn-circle"
                                    data-bs-dismiss="modal"></button>
                            </div>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                <img src="{{ asset('public/img/logo.png') }}" width="70" alt="la-escapo-logo">
                                <div class="d-flex flex-column">
                                    <b class="modal-title mt-2 h5 text-bold">La Escapo Mountain
                                        Resort</b>
                                    <span>Tuno, Tibiao, Antique</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div
                            class="bg-green-secondary d-flex align-items-center gap-2 justify-content-center text-light p-2 mb-3">
                            <i class="fa fa-spa fa-2x"></i>
                            <h3 class="m-0">MASSAGE</h3>
                        </div>
                        <div class="form-group mb-3">
                            <div class="d-flex align-items-start gap-1">
                                <div class="col-6 d-flex align-items-center gap-3">
                                    <label for="visitor_id">Name:</label>
                                    <select name="visitor_id" class="form-control select2" id="edit_visitor_name"
                                        required data-placeholder="Select a visitor">
                                        <option></option>
                                        @foreach ($visitors as $visitor)
                                            <option value="{{ $visitor->id }}">{{ $visitor->first_name }}
                                                {{ $visitor->middle_name }}
                                                {{ $visitor->last_name }} -
                                                {{ \Carbon\Carbon::parse($visitor->date_visit)->format('F j, Y') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered border-dark" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th class="bg-green-tertiary text-light">NO.</th>
                                                <th class="bg-green-tertiary text-light">GUEST</th>
                                                <th class="bg-green-tertiary text-light">AGE</th>
                                                <th class="bg-green-tertiary text-light">SERVICE</th>
                                                <th class="bg-green-tertiary text-light">QTY</th>
                                                <th class="bg-green-tertiary text-light">FEE</th>
                                                <th width="15%" class="bg-green-tertiary text-light">SUBTOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody id="editMassageTableBody"></tbody>
                                    </table>

                                    <!-- TOTAL -->
                                    <div class="d-flex justify-content-end align-items-center gap-3 mt-2">
                                        <label>Status:</label>
                                        <select name="payment_status" id="edit_payment_status"
                                            class="form-control w-auto">
                                            <option value="Paid">Paid</option>
                                            <option value="Unpaid">Unpaid</option>
                                        </select>

                                        <label>Total:</label>
                                        <div class="d-flex">
                                            <span class="input-group-text bg-theme-primary text-light">₱</span>
                                            <input type="text" id="edit_total_payment" name="total_payment"
                                                class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-theme-primary text-light">Update</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection <!-- End the content section -->

<script>
    window.massageServices = @json($massageFees);
    document.addEventListener('DOMContentLoaded', function() {
        const services = window.massageServices;
        console.log(services);
        // =========================
        // SELECT2
        // =========================
        $('#addMassageModal').on('shown.bs.modal', function() {
            $('#visitor_name').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: "Select a visitor",
                allowClear: true,
                dropdownParent: $('#addMassageModal')
            });
        });

        $('#editMassageModal').on('shown.bs.modal', function() {
            $('#edit_visitor_name').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: "Select a visitor",
                allowClear: true,
                dropdownParent: $('#editMassageModal')
            });
        });

        // =========================
        // RENDER MASSAGE TABLE
        // =========================
        function renderMassageRows(target, guests) {
            let html = '';
            guests.forEach((guest, gIndex) => {
                window.massageServices.forEach((service, sIndex) => {
                    html += `
                <tr>
                    ${sIndex === 0 ? `
                        <td class="text-center" rowspan="${window.massageServices.length}">
                            ${gIndex + 1}
                        </td>

                        <td rowspan="${window.massageServices.length}">
                            ${guest.name ?? guest.guest ?? ''} 
                            ${guest.is_main ? '(Main Guest)' : ''}
                        </td>
                    ` : ''}
                    <td>
                        ${guest.age ?? 'N/A'}
                    </td>

                    <!-- SERVICE -->
                    <td>
                        ${service.service_name}

                        <input type="hidden"
                            name="members[${gIndex}][services][${sIndex}][service_name]"
                            value="${service.service_name}">
                    </td>

                    <!-- QTY -->
                    <td>
                        <input type="number"
                            class="form-control massage-qty"
                            name="members[${gIndex}][services][${sIndex}][qty]"
                            data-fee="${service.fee}"
                            value="0"
                            min="0">
                    </td>

                    <!-- FEE -->
                    <td>
                        ₱${parseFloat(service.fee).toFixed(2)}

                        <input type="hidden"
                            name="members[${gIndex}][services][${sIndex}][fee]"
                            value="${service.fee}">
                    </td>

                    <!-- SUBTOTAL -->
                    <td>
                        <input type="text"
                            class="form-control massage-subtotal"
                            readonly value="0.00">
                    </td>

                </tr>
            `;
                });
            });

            $(target).html(html);
        }

        // =========================
        // CALCULATE TOTAL
        // =========================
        function updateMassageTotals() {

            let total = 0;

            $('#addMassageTableBody tr').each(function() {

                const qtyInput = $(this).find('.massage-qty');

                const qty = parseInt(qtyInput.val()) || 0;
                const fee = parseFloat(qtyInput.data('fee')) || 0;

                const subtotal = qty * fee;

                $(this).find('.massage-subtotal').val(subtotal.toFixed(2));

                total += subtotal;
            });

            $('#massage_total_payment').val(total.toFixed(2));
        }

        // =========================
        // LOAD VISITOR → GENERATE ROWS
        // =========================
        $('#visitor_name').on('change', function() {

            const visitor_id = $(this).val();
            if (!visitor_id) return;

            const baseUrl = window.location.origin;
            const folder = window.location.pathname.split('/')[1];
            const url = `${baseUrl}/${folder}/get-visitor-members/${visitor_id}`;

            $.get(url, function(res) {

                renderMassageRows('#addMassageTableBody', res.guests);

                updateMassageTotals();
            });
        });

        // =========================
        // QTY CHANGE
        // =========================
        $(document).on('input', '.massage-qty', function() {
            updateMassageTotals();
        });

        function updateAccommodationTotals() {
            let total = 0;
            $('table tbody tr').each(function() {
                const nightsInput = $(this).find('input[name="nights[]"]');
                const feeInput = $(this).find('.room-fee');
                if (nightsInput.length === 0) return;

                const nights = parseFloat(nightsInput.val()) || 0;
                const fee = parseFloat(feeInput.val()) || 0;

                const subtotal = nights * fee;

                $(this).find('.accommodation-subtotal').val(subtotal.toFixed(2));

                total += subtotal;
            });

            $('#accommodation_total_payment').val(total.toFixed(2));
        }

        // Trigger on input
        $(document).on('input', 'input[name="nights[]"]', function() {
            updateAccommodationTotals();
        });

        // =========================
        // OPEN EDIT MODAL
        // =========================
        $('#editMassageModal').on('show.bs.modal', function(event) {

            const button = $(event.relatedTarget);

            const id = button.data('id');
            const visitorId = button.data('visitor-id');
            const members = button.data('total-members') || [];
            const total = button.data('total-payment');
            const status = button.data('payment-status');

            $('#edit_massage_id').val(id);
            $('#_visitor_id').val(visitorId);
            $('#edit_total_payment').val(parseFloat(total).toFixed(2));
            $('#edit_payment_status').val(status);

            $('#edit_visitor_name').val(visitorId).trigger('change');

            renderEditRows(members);
        });


        // =========================
        // RENDER EDIT TABLE
        // =========================
        function renderEditRows(members) {

            let html = '';

            members.forEach((guest, gIndex) => {

                const savedServices = guest.services || [];

                window.massageServices.forEach((service, sIndex) => {

                    const existing = savedServices.find(s =>
                        s.service_name === service.service_name
                    );

                    const qty = existing ? existing.qty : 0;
                    const subtotal = existing ? existing.subtotal : 0;

                    html += `
            <tr>
                ${sIndex === 0 ? `
                    <td rowspan="${window.massageServices.length}" class="text-center">
                        ${gIndex + 1}
                    </td>

                    <td rowspan="${window.massageServices.length}">
                        ${guest.guest} ${guest.is_main ? '(Main Guest)' : ''}

                        <input type="hidden" 
                            name="members[${gIndex}][guest]" 
                            value="${guest.guest}">

                        <input type="hidden" 
                            name="members[${gIndex}][age]" 
                            value="${guest.age ?? ''}">

                        <input type="hidden" 
                            name="members[${gIndex}][is_main]" 
                            value="${guest.is_main ? 1 : 0}">
                    </td>

                    <td rowspan="${window.massageServices.length}" class="text-center">
                        ${guest.age ?? 'N/A'}
                    </td>
                ` : ''}

                <!-- SERVICE -->
                <td>
                    ${service.service_name}

                    <input type="hidden"
                        name="members[${gIndex}][services][${sIndex}][service_name]"
                        value="${service.service_name}">
                </td>

                <!-- QTY -->
                <td>
                    <input type="number"
                        class="form-control edit-qty"
                        name="members[${gIndex}][services][${sIndex}][qty]"
                        value="${qty}"
                        data-fee="${service.fee}"
                        min="0">
                </td>

                <!-- FEE -->
                <td>
                    ₱${parseFloat(service.fee).toFixed(2)}

                    <input type="hidden"
                        name="members[${gIndex}][services][${sIndex}][fee]"
                        value="${service.fee}">
                </td>

                <!-- SUBTOTAL -->
                <td>
                    <input type="text"
                        class="form-control edit-subtotal"
                        value="${parseFloat(subtotal).toFixed(2)}"
                        readonly>
                </td>
            </tr>
            `;
                });
            });

            $('#editMassageTableBody').html(html);

            updateEditTotals();
        }
        // =========================
        // UPDATE TOTAL (EDIT)
        // =========================
        function updateEditTotals() {

            let total = 0;

            $('#editMassageTableBody tr').each(function() {

                const qtyInput = $(this).find('.edit-qty');

                const qty = parseFloat(qtyInput.val()) || 0;
                const fee = parseFloat(qtyInput.data('fee')) || 0;

                const subtotal = qty * fee;

                $(this).find('.edit-subtotal').val(subtotal.toFixed(2));

                total += subtotal;
            });

            $('#edit_total_payment').val(total.toFixed(2));
        }

        // =========================
        // QTY CHANGE (EDIT)
        // =========================
        $(document).on('input', '.edit-qty', function() {
            updateEditTotals();
        });
    });
</script>
