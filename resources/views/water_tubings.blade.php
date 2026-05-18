@extends('layouts.auth') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="d-flex">
            <i class="fa fa-water fa-2x text-dark me-2"></i>
            <div class="d-flex flex-column">
                <h1 class="h3 mb-0 text">AVAILED SERVICES</h1>
                <h6 class="mb-0">Guest | Water Tubing</h6>
            </div>
        </div>
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWaterTubingModal">Add Water Tubing
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
                    style="min-width:2000px;">
                    <thead>
                        <tr>
                            <th class="bg-theme-primary text-light text-center">NO.</th>
                            <th class="bg-theme-primary text-light">MAIN GUEST</th>
                            <th class="bg-theme-primary text-light text-center">AGE</th>
                            <th class="bg-theme-primary text-light text-center">TOTAL MEMBERS</th>
                            <th class="bg-theme-primary text-light">TOTAL FEE</th>
                            <th class="bg-theme-primary text-light">STATUS</th>
                            <th class="bg-theme-primary text-light">DATE CREATED</th>
                            <th class="bg-theme-primary text-light sticky-action">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($waterTubings as $watertubing)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    {{ optional($watertubing->visitor)->first_name }}
                                    {{ optional($watertubing->visitor)->middle_name }}
                                    {{ optional($watertubing->visitor)->last_name }}
                                </td>
                                <td class="text-center">{{ $watertubing->visitor->age ?? 'N/A' }}</td>
                                <td class="text-center px-0 pb-0">
                                    {{ $watertubing->visitor->members + 1 }}
                                    <table class="border-dark table table-bordered m-0 mt-2"
                                        style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th class="bg-green-tertiary text-light" style="padding: 5px;">No.</th>
                                                <th class="bg-green-tertiary text-light" style="padding: 5px;">Guest</th>
                                                <th class="bg-green-tertiary text-light" style="padding: 5px;">Age</th>
                                                <th class="bg-green-tertiary text-light" style="padding: 5px;">Category</th>
                                                <th class="bg-green-tertiary text-light" style="padding: 5px;">Quantity</th>
                                                <th class="bg-green-tertiary text-light" style="padding: 5px;">Fee</th>
                                                <th class="bg-green-tertiary text-light" style="padding: 5px;">Sub-Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $membersData = json_decode($watertubing->members, true) ?? [];
                                            @endphp
                                            @foreach ($membersData as $index => $row)
                                                @if (!empty($row['services']))
                                                    @foreach ($row['services'] as $sIndex => $service)
                                                        <tr>
                                                            @if ($sIndex === 0)
                                                                <td class="text-center"
                                                                    rowspan="{{ count($row['services']) }}">
                                                                    {{ $loop->parent->iteration ?? $index + 1 }}
                                                                </td>
                                                                <td rowspan="{{ count($row['services']) }}">
                                                                    {{ $row['guest'] }}
                                                                    @if (!empty($row['is_main']))
                                                                        (Main Guest)
                                                                    @endif
                                                                </td>
                                                                <td class="text-center"
                                                                    rowspan="{{ count($row['services']) }}">
                                                                    {{ $row['age'] }}
                                                                </td>
                                                            @endif
                                                            <td>{{ $service['service_name'] }}</td>
                                                            <td class="text-center">{{ $service['qty'] }}</td>
                                                            <td>₱{{ number_format($service['fee'], 2) }}</td>
                                                            <td>₱{{ number_format($service['subtotal'], 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                                <td>₱ {{ number_format($watertubing->total_payment, 2) }}</td>
                                <td>
                                    @if ($watertubing->payment_status === 'Unpaid')
                                        <span class="badge bg-danger">{{ ucfirst($watertubing->payment_status) }}</span>
                                    @else
                                        <span class="badge bg-success">{{ ucfirst($watertubing->payment_status) }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($watertubing->created_at)->format('F j, Y') }}</td>
                                <td class="sticky-action">
                                    <div class="d-flex align-items-center justify-c gap-2">
                                        <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editWaterTubingModal" data-id="{{ $watertubing->id }}"
                                            data-visitor-id="{{ $watertubing->visitor_id }}"
                                            data-members='@json(json_decode($watertubing->members))'
                                            data-total-payment="{{ $watertubing->total_payment }}"
                                            data-payment-status="{{ $watertubing->payment_status }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('watertubing.destroy', $watertubing->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this water tubing record?')">
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

    <!-- Add Water Tubing Fee Modal -->
    <div class="modal fade" id="addWaterTubingModal" tabindex="-1" role="dialog"
        aria-labelledby="addWaterTubingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <form action="{{ route('watertubing.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-12 p-2 pb-4 text-light bg-theme-primary">
                            <div class="text-end">
                                <button type="button" class="btn-close btn-close-white"
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
                            <i class="fa fa-water fa-2x"></i>
                            <h3 class="m-0">WATER TUBING</h3>
                        </div>
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

                        <div class="form-group mb-2">
                            <table class="table table-bordered border-dark"
                                style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th class="bg-green-tertiary text-light" style="padding: 10px;">NO.</th>
                                        <th class="bg-green-tertiary text-light" style="padding: 10px;">GUEST</th>
                                        <th class="bg-green-tertiary text-light" style="padding: 10px;">AGE</th>
                                        <th class="bg-green-tertiary text-light" style="padding: 10px;">CATEGORY</th>
                                        <th class="bg-green-tertiary text-light" style="padding: 10px;">FEE</th>
                                        <th class="bg-green-tertiary text-light" style="padding: 10px;">QUANTITY</th>
                                        <th class="bg-green-tertiary text-light" style="padding: 10px;">SUB-TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody id="addWaterTubingTableBody"></tbody>
                            </table>
                        </div>

                        <div class="form-group mb-2">
                            <div class="d-flex align-items-center gap-3">
                                <label>Payment Status:</label>
                                <div class="col-2">
                                    <select name="payment_status" class="form-control" required>
                                        <option value="">Select status</option>
                                        <option value="Paid">Paid</option>
                                        <option value="Unpaid">Unpaid</option>
                                    </select>
                                </div>
                                <label>Total Fee:</label>
                                <div class="col-2">
                                    <div class="d-flex">
                                        <span class="input-group-text bg-theme-primary text-light">₱</span>
                                        <input type="text" name="total_payment" id="total_payment" value="0.00"
                                            class="form-control" readonly required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Water Tubing Fee Modal -->
    <div class="modal fade" id="editWaterTubingModal" tabindex="-1" role="dialog"
        aria-labelledby="editWaterTubingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <form action="{{ route('watertubing.update') }}" method="POST">
                <input type="hidden" name="water_tubing_id" id="edit_watertubing_id">
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
                            <i class="fa fa-water fa-2x"></i>
                            <h3 class="m-0">WATER TUBING</h3>
                        </div>
                        <div class="form-group mb-3">
                            <div class="d-flex align-items-start gap-1">
                                <div class="col-6 d-flex align-items-center gap-3">
                                    <label for="visitor_id">Name:</label>
                                    <select name="visitor_id" class="form-control select2" id="edit_visitor_id" required
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

                        <div class="form-group mb-2">
                            <table class="table table-bordered border-dark"
                                style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th class="bg-green-tertiary text-light" style="padding: 10px;">NO.</th>
                                        <th class="bg-green-tertiary text-light" style="padding: 10px;">GUEST</th>
                                        <th class="bg-green-tertiary text-light" style="padding: 10px;">AGE</th>
                                        <th class="bg-green-tertiary text-light" style="padding: 10px;">CATEGORY</th>
                                        <th class="bg-green-tertiary text-light" style="padding: 10px;">FEE</th>
                                        <th class="bg-green-tertiary text-light" style="padding: 10px;">QUANTITY</th>
                                        <th class="bg-green-tertiary text-light" style="padding: 10px;">SUB-TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody id="editWaterTubingTableBody"></tbody>
                            </table>
                            <div class="form-group mb-2">
                                <div class="d-flex align-items-center gap-3">
                                    <label>Payment Status:</label>
                                    <div class="col-2">
                                        <select name="payment_status" id="edit_payment_status" class="form-control"
                                            required>
                                            <option value="">Select status</option>
                                            <option value="Paid">Paid</option>
                                            <option value="Unpaid">Unpaid</option>
                                        </select>
                                    </div>
                                    <label>Total Fee:</label>
                                    <div class="col-2">
                                        <div class="d-flex">
                                            <span class="input-group-text bg-theme-primary text-light">₱</span>
                                            <input type="text" name="total_payment" id="edit_total_payment"
                                                value="0.00" class="form-control" readonly required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection <!-- End the content section -->
<script>
    window.waterTubingServices = @json($waterTubingFees);
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const services = window.waterTubingServices;
        // =========================
        // SELECT2 INIT
        // =========================
        $('#addWaterTubingModal').on('shown.bs.modal', function() {
            $('#visitor_name').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: "Select a visitor",
                allowClear: true,
                dropdownParent: $('#addWaterTubingModal')
            });
        });

        $('#editWaterTubingModal').on('shown.bs.modal', function() {
            $('#edit_visitor_id').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('#editWaterTubingModal')
            });
        });

        // =========================
        // RENDER TABLE (REUSABLE)
        // =========================
        function renderRows(target, guests, isEdit = false) {

            let html = '';

            guests.forEach((guest, gIndex) => {

                let servicesData = services.map((service, sIndex) => {

                    let qty = 0;

                    if (isEdit && guest.services) {
                        const match = guest.services.find(s =>
                            s.service_name === service.service_name
                        );
                        if (match) qty = match.qty;
                    }

                    return {
                        service,
                        sIndex,
                        qty
                    };
                });

                servicesData.forEach((row, index) => {

                    html += `
                <tr>

                    ${index === 0 ? `
                        <td class="text-center" rowspan="${servicesData.length}">
                            ${gIndex + 1}
                        </td>

                        <td rowspan="${servicesData.length}">
                            ${guest.name || guest.guest || ''} 
                            ${guest.is_main ? '(Main Guest)' : ''}
                        </td>

                        <td class="text-center" rowspan="${servicesData.length}">
                            ${guest.age || ''}
                        </td>
                    ` : ''}

                    <!-- CATEGORY -->
                    <td>
                        ${row.service.service_name}
                    </td>

                    <!-- FEE -->
                    <td>
                        ₱${parseFloat(row.service.fee).toFixed(2)}
                    </td>

                    <!-- QTY -->
                    <td>
                        <input type="number"
                            class="form-control ${isEdit ? 'edit-qty' : 'qty'}"
                            style="width:70px;"
                            data-fee="${row.service.fee}"
                            data-service-name="${row.service.service_name}"
                            name="members[${gIndex}][${row.sIndex}]"
                            value="${row.qty}"
                            min="0">
                    </td>

                    <!-- SUBTOTAL -->
                    <td>
                        <input type="text"
                            class="form-control ${isEdit ? 'edit-subtotal' : 'subtotal'}"
                            readonly
                            value="0.00">
                    </td>

                </tr>
            `;
                });
            });

            $(target).html(html);
        }

        // =========================
        // CALCULATE TOTALS (REUSABLE)
        // =========================
        function updateTotals(tableSelector, totalSelector, qtyClass, subtotalClass) {

            let grandTotal = 0;

            $(`${tableSelector} tr`).each(function() {

                let qtyInput = $(this).find(`.${qtyClass}`);

                if (qtyInput.length) {
                    const qty = parseInt(qtyInput.val()) || 0;
                    const fee = parseFloat(qtyInput.data('fee')) || 0;

                    let subtotal = qty * fee;

                    $(this).find(`.${subtotalClass}`).val(subtotal.toFixed(2));

                    grandTotal += subtotal;
                }
            });

            $(totalSelector).val(grandTotal.toFixed(2));
        }

        // =========================
        // ADD FLOW
        // =========================
        $('#visitor_name').on('change', function() {

            const visitor_id = $(this).val();
            if (!visitor_id) return;

            const baseUrl = window.location.origin;
            const folder = window.location.pathname.split('/')[1];
            const url = `${baseUrl}/${folder}/get-visitor-members/${visitor_id}`;

            $.get(url, function(res) {
                renderRows('#addWaterTubingTableBody', res.guests, false);
                updateTotals('#addWaterTubingTableBody', '#total_payment', 'qty', 'subtotal');
            });
        });

        $(document).on('input', '.qty', function() {
            updateTotals('#addWaterTubingTableBody', '#total_payment', 'qty', 'subtotal');
        });

        // =========================
        // EDIT FLOW
        // =========================
        let editCache = [];

        $('#editWaterTubingModal').on('show.bs.modal', function(event) {

            const button = $(event.relatedTarget);

            const members = button.data('members');

            let parsedMembers = [];

            try {
                parsedMembers = typeof members === 'string' ?
                    JSON.parse(members) :
                    members;
            } catch (e) {
                parsedMembers = [];
            }

            $('#edit_watertubing_id').val(button.data('id'));
            $('#edit_visitor_id').val(button.data('visitor-id'));
            $('#edit_payment_status').val(button.data('payment-status'));

            renderRows('#editWaterTubingTableBody', parsedMembers, true);

            updateTotals('#editWaterTubingTableBody', '#edit_total_payment', 'edit-qty',
                'edit-subtotal');
        });

        $(document).on('input', '.edit-qty', function() {
            updateTotals('#editWaterTubingTableBody', '#edit_total_payment', 'edit-qty',
                'edit-subtotal');
        });

        $('#editWaterTubingModal').on('hidden.bs.modal', function() {
            $('#editWaterTubingTableBody').html('');
            $('#edit_total_payment').val('0.00');
            editCache = [];
        });

    });
</script>
