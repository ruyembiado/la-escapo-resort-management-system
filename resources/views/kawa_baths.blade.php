@extends('layouts.auth') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="d-flex">
            <i class="fas fa-hot-tub-person fa-2x text-dark me-2"></i>
            <div class="d-flex flex-column">
                <h1 class="h3 mb-0 text">AVAILED SERVICES</h1>
                <h6 class="mb-0">Guest | Kawa Hot Bath</h6>
            </div>
        </div>
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKawaBathModal">Add Kawa Hot Bath &
            Picnic Table
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
            <div class="d-flex gap-2">
                <a href="{{ url('kawa-hot-baths') }}" class="btn btn-success text-light d-flex align-items-center gap-2">
                    <i class="fa-solid fa-hot-tub-person"></i>
                    Kawa Hot Bath
                </a>
                <a href="{{ url('picnic-tables') }}"
                    class="btn bg-theme-primary text-light d-flex align-items-center gap-2">
                    <i class="fa-solid fa-table"></i>
                    Picnic Table
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered border-dark" id="dataTable1" width="100%" cellspacing="0"
                    style="min-width:2000px;">
                    <thead>
                        <th class="bg-theme-primary text-light">NO.</th>
                        <th class="bg-theme-primary text-light">MAIN GUEST</th>
                        <th class="bg-theme-primary text-light">TOTAL MEMBERS</th>
                        <th class="text-center bg-theme-primary text-light">SERVICE DETAILS</th>
                        <th class="bg-theme-primary text-light">TOTAL FEE</th>
                        <th class="bg-theme-primary text-light">STATUS</th>
                        <th class="bg-theme-primary text-light">DATE CREATED</th>
                        <th class="bg-theme-primary text-light">ACTION</th>
                    </thead>
                    <tbody>
                        @foreach ($kawaBaths as $kawabath)
                            @php
                                $membersData = json_decode($kawabath->members, true) ?? [];
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    {{ optional($kawabath->visitor)->first_name }}
                                    {{ optional($kawabath->visitor)->middle_name }}
                                    {{ optional($kawabath->visitor)->last_name }}
                                </td>
                                <td class="text-center">
                                    {{ count($membersData) }}
                                </td>
                                <td class="p-0">
                                    <table class="table table-bordered border-dark m-0 mt-0" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th class="bg-theme-primary text-light">NO.</th>
                                                <th class="bg-theme-primary text-light">GUEST</th>
                                                <th class="bg-theme-primary text-light">AGE</th>
                                                <th class="bg-theme-primary text-light">ITEM</th>
                                                <th class="bg-theme-primary text-light">QTY</th>
                                                <th class="bg-theme-primary text-light">FEE</th>
                                                <th class="bg-theme-primary text-light">SUBTOTAL</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($membersData as $index => $row)
                                                @if (!empty($row['services']))
                                                    @foreach ($row['services'] as $sIndex => $service)
                                                        <tr>
                                                            @if ($sIndex === 0)
                                                                <td rowspan="{{ count($row['services']) }}"
                                                                    class="text-center">
                                                                    {{ $index + 1 }}
                                                                </td>

                                                                <td rowspan="{{ count($row['services']) }}">
                                                                    {{ $row['guest'] }}
                                                                    @if (!empty($row['is_main']))
                                                                        (Main Guest)
                                                                    @endif
                                                                </td>

                                                                <td rowspan="{{ count($row['services']) }}"
                                                                    class="text-center">
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

                                <td>₱ {{ number_format($kawabath->total_payment, 2) }}</td>

                                <td>
                                    @if ($kawabath->payment_status === 'pending')
                                        <span class="badge bg-danger">Pending</span>
                                    @else
                                        <span class="badge bg-success">{{ ucfirst($kawabath->payment_status) }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($kawabath->created_at)->format('F j, Y') }}</td>
                                <td class="sticky-action">
                                    <div class="d-flex gap-2">
                                        <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editKawaBathModal" data-id="{{ $kawabath->id }}"
                                            data-visitor-id="{{ $kawabath->visitor_id }}"
                                            data-members='@json($membersData)'
                                            data-total-payment="{{ $kawabath->total_payment }}"
                                            data-payment-status="{{ $kawabath->payment_status }}">
                                            <i class="fa fa-edit"></i>
                                        </a>

                                        <form action="{{ route('kawabath.destroy', $kawabath->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Delete this record?')">
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

    <!-- Add Kawa Hot Bath Fee Modal -->
    <div class="modal fade" id="addKawaBathModal" tabindex="-1" role="dialog" aria-labelledby="addKawaBathModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="max-width: 1500px;">
            <form action="{{ route('kawabath.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-12">
                            <div class="text-end">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                <img src="{{ asset('public/img/logo.png') }}" width="70" alt="la-escapo-logo">
                                <div class="d-flex flex-column">
                                    <b class="modal-title mt-2 text-bold">La Escapo Mountain
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
                                    <label for="visitor_id">Name</label>
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
                                    class="bg-theme-primary d-flex align-items-center gap-2 justify-content-center text-light p-2">
                                    <i class="fa fa-hot-tub-person fa-2x"></i>
                                    <h3 class="m-0">KAWA HOT BATH</h3>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered border-dark">
                                        <thead>
                                            <th class="bg-theme-primary text-light">NO.</th>
                                            <th class="bg-theme-primary text-light">GUEST</th>
                                            <th class="bg-theme-primary text-light">AGE</th>
                                            <th class="bg-theme-primary text-light">CATEGORY</th>
                                            <th class="bg-theme-primary text-light">FEE</th>
                                            <th class="bg-theme-primary text-light">QUANTITY</th>
                                            <th class="bg-theme-primary text-light">SUB-TOTAL</th>
                                        </thead>
                                        <tbody id="addKawaBathTableBody"></tbody>
                                    </table>

                                    <!-- TOTAL -->
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-end gap-3">
                                            <label>Payment Status:</label>
                                            <div class="col-3">
                                                <select name="kawabath_payment_status" class="form-control">
                                                    <option value="">Select status</option>
                                                    <option value="Paid">Paid</option>
                                                    <option value="Unpaid">Unpaid</option>
                                                </select>
                                            </div>

                                            <label>Total Fee:</label>
                                            <div class="col-3">
                                                <div class="d-flex">
                                                    <span class="input-group-text bg-theme-primary text-light">₱</span>
                                                    <input type="text" name="kawabath_total_payment"
                                                        id="kawabath_total_payment" value="0.00" class="form-control"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div
                                    class="bg-theme-primary d-flex align-items-center gap-2 justify-content-center text-light p-2">
                                    <i class="fa fa-table fa-2x"></i>
                                    <h3 class="m-0">PICNIC TABLE</h3>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered border-dark">
                                        <thead>
                                            <th class="bg-theme-primary text-light text-center">NO.</th>
                                            <th class="bg-theme-primary text-light">PICNIC TABLE</th>
                                            <th class="bg-theme-primary text-light">FEE</th>
                                            <th class="bg-theme-primary text-light">QUANTITY</th>
                                            <th class="bg-theme-primary text-light">SUB-TOTAL</th>
                                        </thead>
                                        <tbody>
                                            @foreach ($picnicTableFees as $index => $fee)
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td>{{ $fee->service_name }}
                                                        <input type="hidden" name="picnic_table_services[]"
                                                            value="{{ $fee->service_name }}">
                                                    </td>
                                                    <td>
                                                        ₱{{ number_format($fee->fee, 2) }}
                                                        <input type="hidden" name="picnic_table_fees[]"
                                                            class="picnic-fee" value="{{ $fee->fee }}">
                                                    </td>
                                                    <td width="15%"><input type="number"
                                                            name="picnic_table_quantity[]" class="form-control"
                                                            value="{{ $fee->quantity }}" min="0"></td>
                                                    <td width="20%"><input type="text"
                                                            class="form-control subtotal" readonly="" value="0.00">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- TOTAL -->
                                <div class="form-group mt-2">
                                    <div class="d-flex align-items-center justify-content-end gap-3">
                                        <label>Payment Status:</label>
                                        <div class="col-3">
                                            <select name="picnictable_payment_status" class="form-control">
                                                <option value="">Select status</option>
                                                <option value="Paid">Paid</option>
                                                <option value="Unpaid">Unpaid</option>
                                            </select>
                                        </div>

                                        <label>Total Fee:</label>
                                        <div class="col-3">
                                            <div class="d-flex">
                                                <span class="input-group-text bg-theme-primary text-light">₱</span>
                                                <input type="text" name="picnictable_total_payment"
                                                    id="picnictable_total_payment" value="0.00" class="form-control"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="grand_total" id="grand_total" value="0.00">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Kawa Hot Bath Fee Modal -->
    <div class="modal fade" id="editKawaBathModal" tabindex="-1" role="dialog"
        aria-labelledby="editKawaBathModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <form action="{{ route('kawabath.update') }}" method="POST">
                <input type="hidden" name="kawabath_id" id="edit_kawabath_id">
                <input type="hidden" name="visitor_id" id="_visitor_id">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-12">
                            <div class="text-end">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="d-flex align-items-center gap-2 justify-content-center">
                                <img src="{{ asset('public/img/logo.png') }}" width="70" alt="la-escapo-logo">
                                <div class="d-flex flex-column">
                                    <b class="modal-title mt-2 text-bold">La Escapo Mountain
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

                        <div class="form-group mb-2">
                            <table class="table table-bordered border-dark"
                                style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <th class="bg-theme-primary text-light">NO.</th>
                                    <th class="bg-theme-primary text-light">GUEST</th>
                                    <th class="bg-theme-primary text-light">AGE</th>
                                    <th class="bg-theme-primary text-light">CATEGORY</th>
                                    <th class="bg-theme-primary text-light">FEE</th>
                                    <th class="bg-theme-primary text-light">QUANTITY</th>
                                    <th class="bg-theme-primary text-light">SUB-TOTAL</th>
                                </thead>
                                <tbody id="editKawaBathTableBody"></tbody>
                            </table>
                            <div class="form-group mt-2">
                                <div class="d-flex align-items-center justify-content-end gap-3">
                                    <label>Payment Status:</label>
                                    <div class="col-3">
                                        <select name="kawabath_payment_status" id="edit_payment_status"
                                            class="form-control">
                                            <option value="">Select status</option>
                                            <option value="Paid">Paid</option>
                                            <option value="Unpaid">Unpaid</option>
                                        </select>
                                    </div>

                                    <label>Total Fee:</label>
                                    <div class="col-3">
                                        <div class="d-flex">
                                            <span class="input-group-text bg-theme-primary text-light">₱</span>
                                            <input type="text" name="kawabath_total_payment" id="edit_total_payment"
                                                value="0.00" class="form-control" readonly>
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
    window.kawaBathServices = @json($kawaHotBathFees);

    document.addEventListener('DOMContentLoaded', function() {

        const services = window.kawaBathServices;
        // =========================
        // SELECT2 INIT
        // =========================
        $('#addKawaBathModal').on('shown.bs.modal', function() {
            $('#visitor_name').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: "Select a visitor",
                allowClear: true,
                dropdownParent: $('#addKawaBathModal')
            });
        });

        $('#editKawaBathModal').on('shown.bs.modal', function() {
            $('#edit_visitor_name').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: "Select a visitor",
                allowClear: true,
                dropdownParent: $('#editKawaBathModal')
            });
        });

        // =========================
        // RENDER TABLE
        // =========================
        function renderKawaRows(target, guests, isEdit = false) {

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

                    <!-- SERVICE -->
                    <td>
                        ${row.service.service_name}
                        <input type="hidden"
                            name="members[${gIndex}][${row.sIndex}][service_name]"
                            value="${row.service.service_name}">

                        <input type="hidden"
                            name="members[${gIndex}][services][${row.sIndex}][fee]"
                            value="${row.service.fee}">
                    </td>
                    <!-- FEE -->
                    <td>
                        ₱${parseFloat(row.service.fee).toFixed(2)}
                    </td>

                    <!-- QTY -->
                    <td>
                        <input type="number"
                            class="form-control ${isEdit ? 'edit-kawa-qty' : 'kawa-qty'}"
                            style="width:70px;"
                            data-fee="${row.service.fee}"
                            data-service-name="${row.service.service_name}"
                            name="members[${gIndex}][services][${row.sIndex}][qty]"
                            value="${row.qty}"
                            min="0">
                    </td>

                    <!-- SUBTOTAL -->
                    <td>
                        <input type="text" class="form-control ${isEdit ? 'edit-kawa-subtotal' : 'kawa-subtotal'}" readonly
                            value="${(row.qty * row.service.fee).toFixed(2)}">
                    </td>

                </tr>
            `;
                });
            });

            $(target).html(html);
        }

        // =========================
        // CALCULATE TOTALS
        // =========================
        function updateKawaTotals(tableSelector, totalSelector, qtyClass, subtotalClass) {

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
        // ADD FLOW (VISITOR → AUTO LOAD)
        // =========================
        $('#visitor_name').on('change', function() {

            const visitor_id = $(this).val();
            if (!visitor_id) return;

            const baseUrl = window.location.origin;
            const folder = window.location.pathname.split('/')[1];
            const url = `${baseUrl}/${folder}/get-visitor-members/${visitor_id}`;

            console.log('Fetching data from:', url);

            $.get(url, function(res) {
                renderKawaRows('#addKawaBathTableBody', res.guests, false);

                updateKawaTotals(
                    '#addKawaBathTableBody',
                    '#kawabath_total_payment',
                    'kawa-qty',
                    'kawa-subtotal'
                );
            });
        });

        // =========================
        // QTY CHANGE
        // =========================
        $(document).on('input', '.kawa-qty', function() {

            updateKawaTotals(
                '#addKawaBathTableBody',
                '#kawabath_total_payment',
                'kawa-qty',
                'kawa-subtotal'
            );
        });

        function updatePicnicTotals() {
            let grandTotal = 0;

            document.querySelectorAll('.table tbody tr').forEach(row => {

                let feeText = row.children[2]?.innerText.replace('₱', '').replace(',', '')
                    .trim();
                let fee = parseFloat(row.querySelector('.picnic-fee')?.value) || 0;

                let qtyInput = row.querySelector('input[name="picnic_table_quantity[]"]');
                let qty = parseInt(qtyInput?.value) || 0;

                let subtotal = fee * qty;

                let subtotalInput = row.querySelector('.subtotal');
                if (subtotalInput) {
                    subtotalInput.value = subtotal.toFixed(2);
                }

                grandTotal += subtotal;
            });

            document.getElementById('picnictable_total_payment').value = grandTotal.toFixed(2);
        }

        document.addEventListener('input', function(e) {
            if (e.target.name === 'picnic_table_quantity[]') {
                updatePicnicTotals();
            }
        });

        // =========================
        // EDIT MODAL LOAD (FIXED)
        // =========================
        $('#editKawaBathModal').on('show.bs.modal', function(event) {

            const button = $(event.relatedTarget);

            const kawabathId = button.data('id');
            const visitorId = button.data('visitor-id');
            const members = button.data('members') || [];
            const totalPayment = button.data('total-payment') || 0;
            const paymentStatus = button.data('payment-status') || '';

            // Set IDs
            $('#edit_kawabath_id').val(kawabathId);

            // FIX: Select2 properly update
            $('#edit_visitor_name')
                .val(visitorId)
                .trigger('change');

            // FIX: payment status
            $('#edit_payment_status').val(paymentStatus);

            // FIX: total payment field
            $('#edit_total_payment').val(parseFloat(totalPayment).toFixed(2));

            // Render table
            renderKawaRows('#editKawaBathTableBody', members, true);

            // Recalculate AFTER render
            setTimeout(() => {
                updateKawaTotals(
                    '#editKawaBathTableBody',
                    '#edit_total_payment',
                    'edit-kawa-qty',
                    'edit-kawa-subtotal'
                );
            }, 300);
        });

        // =========================
        // EDIT QTY CHANGE
        // =========================
        $(document).on('input', '.edit-kawa-qty', function() {
            updateKawaTotals(
                '#editKawaBathTableBody',
                '#edit_total_payment',
                'edit-kawa-qty',
                'edit-kawa-subtotal'
            );
        });

    });
</script>