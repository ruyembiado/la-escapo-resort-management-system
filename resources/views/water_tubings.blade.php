@extends('layouts.auth') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="d-flex">
            <i class="fa fa-ticket fa-2x text-dark me-2"></i>
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
            <div class="table-responsive">
                <table class="table table-bordered border-dark" id="dataTable1" width="100%" cellspacing="0"
                    style="min-width:2000px;">
                    <thead>
                        <tr>
                            <th class="bg-theme-primary text-light text-center">NO.</th>
                            <th class="bg-theme-primary text-light">MAIN GUEST</th>
                            <th class="bg-theme-primary text-light text-center">AGE</th>
                            <th class="bg-theme-primary text-light">TOTAL MEMBERS</th>
                            <th class="bg-theme-primary text-light">TOTAL FEE</th>
                            <th class="bg-theme-primary text-light">STATUS</th>
                            <th class="bg-theme-primary text-light">DATE CREATED</th>
                            <th class="bg-theme-primary text-light">ACTION</th>
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
                                                <th class="bg-theme-primary text-light" style="padding: 5px;">NO.</th>
                                                <th class="bg-theme-primary text-light" style="padding: 5px;">GUEST</th>
                                                <th class="bg-theme-primary text-light" style="padding: 5px;">CATEGORY</th>
                                                <th class="bg-theme-primary text-light" style="padding: 5px;">AGE</th>
                                                <th class="bg-theme-primary text-light" style="padding: 5px;">SUB-TOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $membersData = json_decode($watertubing->members, true);
                                            @endphp
                                            @foreach ($membersData as $index => $row)
                                                @if (!empty($row['services']))
                                                    <tr>
                                                        <td style="padding: 8px;">
                                                            {{ $row['guest'] }} {{ $row['is_main'] ? '(Main Guest)' : '' }}
                                                        </td>
                                                        <td style="padding: 8px;">
                                                            {{ $row['age'] }}
                                                        </td>
                                                        <td style="padding: 8px;">
                                                            @foreach ($row['services'] as $service)
                                                                <div>
                                                                    {{ $service['service_name'] }}
                                                                    (x{{ $service['qty'] }})
                                                                </div>
                                                            @endforeach
                                                        </td>
                                                        <td style="padding: 8px;">
                                                            @foreach ($row['services'] as $service)
                                                                <div>
                                                                    ₱{{ number_format($service['fee'], 2) }}
                                                                </div>
                                                            @endforeach
                                                        </td>
                                                        <td style="padding: 8px;">
                                                            ₱{{ number_format(collect($row['services'])->sum('subtotal'), 2) }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                                <td>₱ {{ number_format($watertubing->total_payment, 2) }}</td>
                                <td>
                                    @if ($watertubing->payment_status === 'pending')
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
                                            data-total-members='@json(json_decode($watertubing->members))'
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
                                        <th class="bg-theme-primary text-light" style="padding: 10px;">NO.</th>
                                        <th class="bg-theme-primary text-light" style="padding: 10px;">GUEST</th>
                                        <th class="bg-theme-primary text-light" style="padding: 10px;">AGE</th>
                                        <th class="bg-theme-primary text-light" style="padding: 10px;">CATEGORY</th>
                                        <th class="bg-theme-primary text-light" style="padding: 10px;">FEE</th>
                                        <th class="bg-theme-primary text-light" style="padding: 10px;">QUANTITY</th>
                                        <th class="bg-theme-primary text-light" style="padding: 10px;">SUB-TOTAL</th>
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
                                <div class="col-3">
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
                                    <select disabled name="visitor_id" class="form-control select2" id="edit_visitor_id"
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
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th class="bg-theme-primary text-light" style="padding: 10px;">NO.</th>
                                        <th class="bg-theme-primary text-light" style="padding: 10px;">GUEST</th>
                                        <th class="bg-theme-primary text-light" style="padding: 10px;">AGE</th>
                                        <th class="bg-theme-primary text-light" style="padding: 10px;">CATEGORY</th>
                                        <th class="bg-theme-primary text-light" style="padding: 10px;">FEE</th>
                                        <th class="bg-theme-primary text-light" style="padding: 10px;">QUANTITY</th>
                                        <th class="bg-theme-primary text-light" style="padding: 10px;">SUB-TOTAL</th>
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
                                    <div class="col-3">
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
    const waterTubingServices = @json($waterTubingFees);
</script>
{{-- ADD FORM SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

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
            $('#visitor_name').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: "Select a visitor",
                allowClear: true,
                dropdownParent: $('#editWaterTubingModal')
            });
        });

        function renderRows(guests) {
            let html = '';

            guests.forEach((guest, gIndex) => {

                // CATEGORY LABELS (700M | 1.5KM)
                let categoryLabels = '';
                waterTubingServices.forEach((service, sIndex) => {
                    categoryLabels += `<span>${service.service_name}</span>`;
                });

                // QUANTITY INPUTS (0 | 0)
                let qtyInputs = '';
                waterTubingServices.forEach((service, sIndex) => {
                    qtyInputs += `
                <input type="number"
                    class="form-control qty"
                    style="width:70px;"
                    data-fee="${service.fee}"
                    name="members[${gIndex}][${sIndex}]"
                    value="0" min="0">
            `;
                });

                html += `
            <tr>
                <td class="align-middle text-center">${gIndex + 1}</td>

                <td class="align-middle">
                    ${guest.name} ${guest.is_main ? '(Main Guest)' : ''}
                </td>

                <td class="align-middle text-center">${guest.age}</td>

                <!-- CATEGORY -->
                <td class="align-middle text-center">
                    <div style="display:flex; gap:15px;">
                        ${categoryLabels}
                    </div>
                </td>

                <!-- FEE -->
                <td class="align-middle text-center">
                    <div style="display:flex; gap:15px;">
                        ${waterTubingServices.map(s => `<span>₱${parseFloat(s.fee).toFixed(2)}</span>`).join('')}
                    </div>
                </td>

                <!-- QUANTITY -->
                <td>
                    <div style="display:flex; gap:10px;">
                        ${qtyInputs}
                    </div>
                </td>

                <td>
                    <input type="text" class="form-control subtotal" readonly value="0.00">
                </td>
            </tr>
        `;
            });

            document.getElementById('addWaterTubingTableBody').innerHTML = html;
        }

        function updateTotals() {
            let grandTotal = 0;

            document.querySelectorAll('#addWaterTubingTableBody tr').forEach(row => {

                let rowTotal = 0;

                row.querySelectorAll('.qty').forEach(input => {
                    const qty = parseInt(input.value) || 0;
                    const fee = parseFloat(input.dataset.fee) || 0;

                    rowTotal += qty * fee;
                });

                const subtotalField = row.querySelector('.subtotal');
                if (subtotalField) {
                    subtotalField.value = rowTotal.toFixed(2);
                }

                grandTotal += rowTotal;
            });

            document.getElementById('total_payment').value = grandTotal.toFixed(2);
        }

        // SELECT VISITOR
        $('#visitor_name').on('change', function() {

            const visitor_id = $(this).val();
            if (!visitor_id) return;

            const baseUrl = window.location.origin;
            const folderName = window.location.pathname.split('/')[1];
            const url = `${baseUrl}/${folderName}/get-visitor-members/${visitor_id}`;

            $.get(url, function(res) {

                renderRows(res.guests);
                updateTotals();

            });
        });

        // INPUT CHANGE
        $(document).on('input', '.qty', function() {
            updateTotals();
        });

    });
</script>

{{-- EDIT FORM SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        let editCache = [];
        let waterTubingServices = window.waterTubingServices || [];

        // =========================
        // RENDER EDIT TABLE
        // =========================
        function renderEditRows(data) {

            let html = '';

            data.forEach((guest, gIndex) => {

                let qtyInputs = waterTubingServices.map(service => {

                    let existingQty = 0;

                    if (guest.services && Array.isArray(guest.services)) {
                        const match = guest.services.find(s =>
                            Number(s.service_id) === Number(service.id)
                        );
                        if (match) existingQty = match.qty;
                    }

                    return `
                    <input type="number"
                        class="form-control edit-qty"
                        style="width:70px;"
                        data-service-id="${service.id}"
                        data-fee="${service.fee}"
                        value="${existingQty}"
                        min="0">
                `;
                }).join('');

                let feeLabels = waterTubingServices
                    .map(s => `₱${parseFloat(s.fee).toFixed(2)}`)
                    .join(' | ');

                html += `
                <tr>
                    <td class="text-center">${gIndex + 1}</td>

                    <td>
                        ${guest.guest ?? ''} 
                        ${guest.is_main ? '(Main Guest)' : ''}
                    </td>

                    <td class="text-center">${guest.age ?? ''}</td>

                    <td>
                        ${waterTubingServices.map(s => `<div>${s.service_name}</div>`).join('')}
                    </td>

                    <td class="text-center">
                        ${feeLabels}
                    </td>

                    <td class="d-flex gap-2">
                        ${qtyInputs}
                    </td>

                    <td>
                        <input type="text"
                            class="form-control edit-subtotal"
                            readonly
                            value="0.00">
                    </td>
                </tr>
            `;
            });

            $('#editWaterTubingTableBody').html(html);
        }

        // =========================
        // CALCULATE TOTALS
        // =========================
        function updateEditTotals() {

            let grandTotal = 0;

            $('#editWaterTubingTableBody tr').each(function() {

                let rowTotal = 0;

                $(this).find('.edit-qty').each(function() {

                    const qty = parseInt(this.value || 0);
                    const fee = parseFloat(this.dataset.fee || 0);

                    rowTotal += qty * fee;
                });

                $(this).find('.edit-subtotal').val(rowTotal.toFixed(2));

                grandTotal += rowTotal;
            });

            $('#edit_total_payment').val(grandTotal.toFixed(2));
        }

        // =========================
        // OPEN MODAL
        // =========================
        $('#editWaterTubingModal').on('show.bs.modal', function(event) {

            const button = $(event.relatedTarget);

            const waterTubingId = button.data('id');
            const visitorId = button.data('visitor-id');
            const paymentStatus = button.data('payment-status');

            $('#edit_watertubing_id').val(waterTubingId);
            $('#edit_visitor_id').val(visitorId);
            $('#_visitor_id').val(visitorId);
            $('#edit_payment_status').val(paymentStatus);

            const baseUrl = window.location.origin;
            const folder = window.location.pathname.split('/')[1];
            const url = `${baseUrl}/${folder}/get-water-tubing/${waterTubingId}`;

            $.get(url, function(res) {

                editCache = res.data || [];

                renderEditRows(editCache);
                updateEditTotals();
            });
        });

        // =========================
        // LIVE INPUT UPDATE
        // =========================
        $(document).on('input', '#editWaterTubingTableBody .edit-qty', function() {
            updateEditTotals();
        });

        // =========================
        // RESET ON CLOSE
        // =========================
        $('#editWaterTubingModal').on('hidden.bs.modal', function() {
            $('#editWaterTubingTableBody').html('');
            $('#edit_total_payment').val('0.00');
            editCache = [];
        });

    });
</script>
