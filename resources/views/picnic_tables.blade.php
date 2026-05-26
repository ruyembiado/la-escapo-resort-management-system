@extends('layouts.auth') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="d-flex">
            <i class="fas fa-table fa-2x text-dark me-2"></i>
            <div class="d-flex flex-column">
                <h1 class="h3 mb-0 text">AVAILED SERVICES</h1>
                <h6 class="mb-0">Guest | Picnic Table</h6>
            </div>
        </div>
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKawaBathModal">Add Kawa Hot Bath &
            Picnic Table Fee
        </a>
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
                <a href="{{ url('kawa-hot-baths') }}"
                    class="btn bg-theme-primary text-light d-flex align-items-center gap-2">
                    <i class="fa-solid fa-hot-tub-person"></i>
                    Kawa Hot Bath
                </a>
                <a href="{{ url('picnic-tables') }}" class="btn bg-green-tertiary text-light d-flex align-items-center gap-2">
                    <i class="fa-solid fa-table"></i>
                    Picnic Table
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0"
                    style="min-width:1400px;">
                    <thead>
                        <tr>
                            <th class="bg-green-secondary text-light">NO.</th>
                            <th class="bg-green-secondary text-light">MAIN GUEST</th>
                            <th class="bg-green-secondary text-light">TOTAL MEMBERS</th>
                            <th class="bg-green-secondary text-light text-center">SERVICES DETAILS</th>
                            <th class="bg-green-secondary text-light">TOTAL FEE</th>
                            <th class="bg-green-secondary text-light">STATUS</th>
                            <th class="bg-green-secondary text-light">DATE CREATED</th>
                            <th class="bg-green-secondary text-light sticky-action">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($picnicTables as $picnictable)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ optional($picnictable->visitor)->first_name }}
                                    {{ optional($picnictable->visitor)->middle_name }}
                                    {{ optional($picnictable->visitor)->last_name }}
                                </td>
                                <td class="text-center">
                                    {{ $picnictable->visitor->companions->count() + 1 }}
                                </td>
                                <td style="padding: 0;">
                                    <table class="table table-bordered m-0"
                                        style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th class="bg-green-tertiary text-light" style="padding: 5px;">Item</th>
                                                <th class="bg-green-tertiary text-light" style="padding: 5px;">Quantity</th>
                                                <th class="bg-green-tertiary text-light" style="padding: 5px;">Fee</th>
                                                <th class="bg-green-tertiary text-light" style="padding: 5px;">Sub-Total
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $details = json_decode($picnictable->details, true);
                                            @endphp

                                            @if (!empty($details) && collect($details)->where('qty', '>', 0)->count())
                                                @foreach ($details as $item)
                                                    @if ($item['qty'] > 0)
                                                        <tr>
                                                            <td style="padding: 5px;">{{ $item['service_name'] }}</td>
                                                            <td class="text-center" style="padding: 5px;">
                                                                {{ $item['qty'] }}</td>
                                                            <td style="padding: 5px;">₱
                                                                {{ number_format($item['fee'], 2) }}</td>
                                                            <td style="padding: 5px;">₱
                                                                {{ number_format($item['subtotal'], 2) }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="4" class="text-center">No data</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </td>
                                <td>₱ {{ number_format($picnictable->total_payment, 2) }}</td>
                                <td>
                                    @if ($picnictable->payment_status === 'Unpaid')
                                        <span class="badge bg-danger">{{ ucfirst($picnictable->payment_status) }}</span>
                                    @else
                                        <span class="badge bg-success">{{ ucfirst($picnictable->payment_status) }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($picnictable->created_at)->format('F j, Y') }}</td>
                                <td class="sticky-action">
                                    <div class="d-flex align-items-center justify-c gap-2">
                                        <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editPicnicTableModal" data-id="{{ $picnictable->id }}"
                                            data-visitor-id="{{ $picnictable->visitor_id }}"
                                            data-total-payment="{{ $picnictable->total_payment }}"
                                            data-payment-status="{{ $picnictable->payment_status }}"
                                            data-details='@json(json_decode($picnictable->details, true))'>
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('picnictable.destroy', $picnictable->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this picnic table record?')">
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
                    @include('layouts.modal-header')
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
                                    class="bg-green-secondary d-flex align-items-center gap-2 justify-content-center text-light p-3 mb-3">
                                    <i class="fa fa-hot-tub-person fa-2x"></i>
                                    <h3 class="m-0">KAWA HOT BATH</h3>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <th class="bg-green-tertiary text-light">NO.</th>
                                            <th class="bg-green-tertiary text-light">GUEST</th>
                                            <th class="bg-green-tertiary text-light">AGE</th>
                                            <th class="bg-green-tertiary text-light">CATEGORY</th>
                                            <th class="bg-green-tertiary text-light">FEE</th>
                                            <th class="bg-green-tertiary text-light">QUANTITY</th>
                                            <th class="bg-green-tertiary text-light">SUB-TOTAL</th>
                                        </thead>
                                        <tbody id="addKawaBathTableBody"></tbody>
                                    </table>

                                    <!-- TOTAL -->
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-end gap-3">
                                            <label>Payment Status:</label>
                                            <div class="col-2">
                                                <select name="kawabath_payment_status" class="form-control">
                                                    <option value="">Select status</option>
                                                    <option value="Paid">Paid</option>
                                                    <option value="Unpaid">Unpaid</option>
                                                </select>
                                            </div>

                                            <label>Total Fee:</label>
                                            <div class="col-2">
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
                                    class="bg-green-secondary d-flex align-items-center gap-2 justify-content-center text-light p-3 mb-3">
                                    <i class="fa fa-table fa-2x"></i>
                                    <h3 class="m-0">PICNIC TABLE</h3>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <th class="bg-green-tertiary text-light text-center">NO.</th>
                                            <th class="bg-green-tertiary text-light">PICNIC TABLE</th>
                                            <th class="bg-green-tertiary text-light">FEE</th>
                                            <th class="bg-green-tertiary text-light">QUANTITY</th>
                                            <th class="bg-green-tertiary text-light">SUB-TOTAL</th>
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
                                                            name="picnic_table_quantity[]" class="form-control picnic-qty"
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
                                        <div class="col-2">
                                            <select name="picnictable_payment_status" class="form-control">
                                                <option value="">Select status</option>
                                                <option value="Paid">Paid</option>
                                                <option value="Unpaid">Unpaid</option>
                                            </select>
                                        </div>

                                        <label>Total Fee:</label>
                                        <div class="col-2">
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
                        <button type="submit" class="btn bg-theme-primary text-light">Save</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Picnic Table Fee Modal -->
    <div class="modal fade" id="editPicnicTableModal" tabindex="-1" role="dialog"
        aria-labelledby="editPicnicTableModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('picnictable.update') }}" method="POST">
                <input type="hidden" name="picnic_table_id" id="edit_picnic_table_id">
                <input type="hidden" name="visitor_id" id="_visitor_id">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    @include('layouts.modal-header')
                    <div class="modal-body">
                        <div
                            class="bg-green-secondary d-flex align-items-center gap-2 justify-content-center text-light p-3 mb-3">
                            <i class="fa fa-table fa-2x"></i>
                            <h3 class="m-0">PICNIC TABLE</h3>
                        </div>
                        <div class="form-group mb-3">
                            <div class="d-flex align-items-start gap-1">
                                <div class="col-8 d-flex align-items-center gap-3">
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
                        <div class="">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <th class="bg-green-tertiary text-light text-center">NO.</th>
                                        <th class="bg-green-tertiary text-light">PICNIC TABLE</th>
                                        <th class="bg-green-tertiary text-light">FEE</th>
                                        <th width="15%" class="bg-green-tertiary text-light">QUANTITY</th>
                                        <th width="15%" class="bg-green-tertiary text-light">SUB-TOTAL</th>
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
                                                    <input type="hidden" name="picnic_table_fees[]" class="picnic-fee"
                                                        value="{{ $fee->fee }}">
                                                </td>
                                                <td><input type="number" name="picnic_table_quantity[]"
                                                        class="form-control picnic-qty" value="{{ $fee->quantity }}"
                                                        min="0"></td>
                                                <td><input type="text" class="form-control subtotal" readonly=""
                                                        value="0.00">
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
                                    <div class="col-2">
                                        <select name="picnictable_payment_status" class="form-control">
                                            <option value="">Select status</option>
                                            <option value="Paid">Paid</option>
                                            <option value="Unpaid">Unpaid</option>
                                        </select>
                                    </div>

                                    <label>Total Fee:</label>
                                    <div class="col-2">
                                        <div class="d-flex">
                                            <span class="input-group-text bg-theme-primary text-light">₱</span>
                                            <input type="text" name="picnictable_total_payment"
                                                id="edit_picnictable_total_payment" value="0.00" class="form-control"
                                                readonly>
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
    window.kawaBathServices = @json($kawaHotBathFees);
    window.picnicServices = @json($picnicTableFees);

    document.addEventListener('DOMContentLoaded', function() {

        // =========================
        // SELECT2
        // =========================
        function initSelect2(modalId, selector) {
            $(modalId).on('shown.bs.modal', function() {
                $(selector).select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: "Select a visitor",
                    allowClear: true,
                    dropdownParent: $(modalId)
                });
            });
        }

        initSelect2('#addKawaBathModal', '#visitor_name');
        initSelect2('#editPicnicTableModal', '#edit_visitor_name');

        function renderKawaRows(target, guests, isEdit = false) {

            let html = '';

            guests.forEach((guest, gIndex) => {

                window.kawaBathServices.forEach((service, sIndex) => {

                    let qty = 0;

                    if (isEdit && guest.services) {
                        const match = guest.services.find(s =>
                            s.service_name === service.service_name
                        );
                        if (match) qty = parseInt(match.qty);
                    }

                    const subtotal = qty * parseFloat(service.fee);

                    html += `
                <tr>

                    ${sIndex === 0 ? `
                        <td class="text-center" rowspan="${window.kawaBathServices.length}">
                            ${gIndex + 1}
                        </td>

                        <td rowspan="${window.kawaBathServices.length}">
                            ${guest.name ?? guest.guest ?? ''} 
                            ${guest.is_main ? '(Main Guest)' : ''}
                        </td>

                        <td class="text-center" rowspan="${window.kawaBathServices.length}">
                            ${guest.age ?? ''}
                        </td>
                    ` : ''}

                    <td>
                        ${service.service_name}

                        <input type="hidden"
                            name="members[${gIndex}][services][${sIndex}][service_name]"
                            value="${service.service_name}">
                    </td>

                    <td>
                        ₱${parseFloat(service.fee).toFixed(2)}

                        <input type="hidden"
                            name="members[${gIndex}][services][${sIndex}][fee]"
                            value="${service.fee}">
                    </td>

                    <td>
                        <input type="number"
                            class="form-control kawa-qty"
                            data-fee="${service.fee}"
                            name="members[${gIndex}][services][${sIndex}][qty]"
                            value="${qty}"
                            min="0">
                    </td>

                    <td>
                        <input type="text"
                            class="form-control kawa-subtotal"
                            readonly
                            value="${subtotal.toFixed(2)}">
                    </td>

                </tr>
            `;
                });
            });

            $(target).html(html);

            updateKawaTotals();
        }

        function updateKawaTotals() {

            let total = 0;

            $('#addKawaBathTableBody tr').each(function() {

                const qtyInput = $(this).find('.kawa-qty');

                const qty = parseInt(qtyInput.val()) || 0;
                const fee = parseFloat(qtyInput.data('fee')) || 0;

                const subtotal = qty * fee;

                $(this).find('.kawa-subtotal').val(subtotal.toFixed(2));

                total += subtotal;
            });

            $('#kawabath_total_payment').val(total.toFixed(2));
        }

        // =========================
        // PICNIC TOTAL CALC (ADD + EDIT SHARED)
        // =========================
        function calculatePicnicTotals(scope, totalInput) {

            let grandTotal = 0;

            $(`${scope} tbody tr`).each(function() {

                const fee = parseFloat($(this).find('.picnic-fee').val()) || 0;
                const qty = parseInt($(this).find('.picnic-qty').val()) || 0;

                const subtotal = fee * qty;

                $(this).find('.subtotal').val(subtotal.toFixed(2));

                grandTotal += subtotal;
            });

            $(totalInput).val(grandTotal.toFixed(2));
        }

        $(document).on('input', '.kawa-qty', function() {
            updateKawaTotals();
        });

        $('#visitor_name').on('change', function() {

            const visitor_id = $(this).val();
            if (!visitor_id) return;

            const baseUrl = window.location.origin;
            const folder = window.location.pathname.split('/')[1];

            $.get(`${baseUrl}/${folder}/get-visitor-members/${visitor_id}`, function(res) {
                renderKawaRows('#addKawaBathTableBody', res.guests || [], false);
            });
        });

        // =========================
        // ADD PICNIC QTY CHANGE
        // =========================
        $(document).on('input', '#addKawaBathModal .picnic-qty', function() {
            calculatePicnicTotals('#addKawaBathModal', '#picnictable_total_payment');
        });

        // =========================
        // EDIT PICNIC QTY CHANGE
        // =========================
        $(document).on('input', '#editPicnicTableModal .picnic-qty', function() {
            calculatePicnicTotals('#editPicnicTableModal', '#edit_picnictable_total_payment');
        });

        // =========================
        // EDIT MODAL LOAD
        // =========================
        $('#editPicnicTableModal').on('show.bs.modal', function(event) {

            const button = $(event.relatedTarget);

            const id = button.data('id');
            const visitorId = button.data('visitor-id');
            const totalPayment = button.data('total-payment') || 0;
            const paymentStatus = button.data('payment-status') || '';
            const details = button.data('details') || [];

            const services = window.picnicServices;

            $('#edit_picnic_table_id').val(id);
            $('#edit_visitor_name').val(visitorId).trigger('change');
            $('select[name="picnictable_payment_status"]').val(paymentStatus);

            let html = '';

            services.forEach((service, index) => {

                const match = details.find(d => d.service_name === service.service_name);

                const qty = match ? parseInt(match.qty) : 0;
                const fee = parseFloat(service.fee);
                const subtotal = qty * fee;

                html += `
                <tr>
                    <td class="text-center">${index + 1}</td>

                    <td>
                        ${service.service_name}
                        <input type="hidden" name="picnic_table_services[]" value="${service.service_name}">
                    </td>

                    <td>
                        ₱${fee.toFixed(2)}
                        <input type="hidden" class="picnic-fee" name="picnic_table_fees[]" value="${fee}">
                    </td>

                    <td>
                        <input type="number"
                            name="picnic_table_quantity[]"
                            class="form-control picnic-qty"
                            value="${qty}"
                            min="0">
                    </td>

                    <td>
                        <input type="text"
                            class="form-control subtotal"
                            readonly
                            value="${subtotal.toFixed(2)}">
                    </td>
                </tr>
            `;
            });

            $('#editPicnicTableModal tbody').html(html);

            $('#edit_picnictable_total_payment').val(parseFloat(totalPayment).toFixed(2));

            setTimeout(() => {
                calculatePicnicTotals('#editPicnicTableModal',
                    '#edit_picnictable_total_payment');
            }, 100);
        });

    });
</script>
