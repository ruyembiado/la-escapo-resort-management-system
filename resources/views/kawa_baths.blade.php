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
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKawaBathModal">Add Kawa Hot Bath
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
                <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Total Payment</th>
                            <th>Status</th>
                            <th>Date Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kawaBaths as $kawabath)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ optional($kawabath->visitor)->first_name }}
                                    {{ optional($kawabath->visitor)->middle_name }}
                                    {{ optional($kawabath->visitor)->last_name }}
                                </td>
                                @php
                                    $categories = json_decode($kawabath->category, true);
                                    $members = json_decode($kawabath->members, true);
                                    $ages = json_decode($kawabath->age, true);
                                    $fees = json_decode($kawabath->fee, true);
                                @endphp
                                <td style="padding: 10px;">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th style="padding: 5px;">Category</th>
                                                {{-- <th style="padding: 5px;">Quantity</th> --}}
                                                <th style="padding: 5px;">Age</th>
                                                <th style="padding: 5px;">Sub-total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($categories as $index => $cat)
                                                @php
                                                    $memberValue = $members[$index] ?? null;
                                                @endphp
                                                @if (!is_null($memberValue) && $memberValue !== 'null' && (int) $memberValue > 0)
                                                    <tr>
                                                        <td style="padding: 8px;">{{ $cat }}</td>
                                                        {{-- <td style="padding: 8px;">{{ $members[$index] }}</td> --}}
                                                        <td style="padding: 8px;">
                                                            {{ !isset($ages[$index]) || $ages[$index] === null || $ages[$index] === '' || $ages[$index] === 'null' ? 'N/A' : $ages[$index] }}
                                                        </td>
                                                        <td style="padding: 8px;">
                                                            ₱{{ number_format((float) ($members[$index] ?? 0) * (float) ($fees[$index] ?? 0), 2) }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                                <td>₱ {{ number_format($kawabath->total_payment, 2) }}</td>
                                <td>
                                    @if ($kawabath->payment_status === 'pending')
                                        <span class="badge bg-danger">{{ ucfirst($kawabath->payment_status) }}</span>
                                    @else
                                        <span class="badge bg-success">{{ ucfirst($kawabath->payment_status) }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($kawabath->created_at)->format('F j, Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center justify-c gap-2">
                                        <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editKawaBathModal" data-id="{{ $kawabath->id }}"
                                            data-visitor-id="{{ $kawabath->visitor_id }}"
                                            data-total-members='@json(json_decode($kawabath->members))'
                                            data-total-payment="{{ $kawabath->total_payment }}"
                                            data-payment-status="{{ $kawabath->payment_status }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('kawabath.destroy', $kawabath->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this kawa hot bath record?')">
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
                                            <th class="bg-theme-primary text-light">NO.</th>
                                            <th class="bg-theme-primary text-light">PICNIC TABLE</th>
                                            <th class="bg-theme-primary text-light">FEE</th>
                                            <th class="bg-theme-primary text-light">QUANTITY</th>
                                            <th class="bg-theme-primary text-light">SUB-TOTAL</th>
                                        </thead>
                                        <tbody>
                                            @foreach ($picnicTableFees as $index => $fee)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $fee->service_name }}</td>
                                                    <td>₱{{ number_format($fee->fee, 2) }}</td>
                                                    <td width="15%"><input type="number" name="picnic_table_quantity[]"
                                                            class="form-control" value="{{ $fee->quantity }}"
                                                            min="0"></td>
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
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('kawabath.update') }}" method="POST">
                <input type="hidden" name="kawabath_id" id="edit_kawabath_id">
                <input type="hidden" name="visitor_id" id="_visitor_id">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editKawaBathModalLabel">Edit Kawa Hot Bath Fee</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <div class="d-flex align-items-start gap-1">
                                <div class="form-group col-6">
                                    <label for="visitor_id">Name</label>
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
                                <div class="form-group">
                                    <small id="remaining_members_note" class="text-muted"></small>
                                </div>
                                <div class="form-group col-1">
                                    <label for="members">Age</label>
                                    <div class="">
                                        <input readonly type="text" id="edit_age" class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="members">Payment Status</label>
                                    <div class="col-12">
                                        <select name="payment_status" class="form-control" id="edit_payment_status">
                                            <option value="">Select Status</option>
                                            <option value="pending">Pending</option>
                                            <option value="paid">Paid</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr class="bg-secondary text-light">
                                        <th style="padding: 10px;">CATEGORY</th>
                                        <th style="padding: 10px;">QUANTITY</th>
                                        <th style="padding: 10px;">AGE</th>
                                        <th style="padding: 10px;">FEE</th>
                                        <th style="padding: 10px;">SUB-TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $categories = [
                                            [
                                                'name' => 'Children',
                                                'age' => '0-11',
                                                'checked' => false,
                                                'price' => '250.00',
                                            ],
                                            [
                                                'name' => 'Student',
                                                'age' => '12-21',
                                                'checked' => false,
                                                'price' => '250.00',
                                            ],
                                            [
                                                'name' => 'Regular',
                                                'age' => '22-59',
                                                'checked' => false,
                                                'price' => '250.00',
                                            ],
                                            [
                                                'name' => 'PWD',
                                                'age' => 'Any',
                                                'checked' => false,
                                                'price' => '250.00',
                                            ],
                                            [
                                                'name' => 'Senior Citizen',
                                                'age' => '60+',
                                                'checked' => false,
                                                'price' => '250.00',
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($categories as $index => $category)
                                        <tr>
                                            <td width="30%" style="padding: 5px;">
                                                <div class="d-flex align-items-center gap-1">
                                                    <input type="hidden" name="category[]"
                                                        value="{{ $category['name'] }}"
                                                        {{ $category['checked'] ? 'checked' : '' }}>
                                                    <span>{{ $category['name'] }}</span>
                                                </div>
                                            </td>
                                            <td width="25%" style="padding: 5px;">
                                                <input class="form-control" readonly type="number" name="members[]"
                                                    value="">
                                            </td>
                                            <td style="padding: 5px;">
                                                <input class="form-control" type="text" name="age[]"
                                                    value="{{ $category['age'] }}" readonly>
                                            </td>
                                            <td style="padding: 5px;">
                                                <input class="form-control" type="text" id="" name="fee[]"
                                                    min="0" value="{{ $category['price'] }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" readonly id="sub-total" class="form-control"
                                                    value="" readonly>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex align-items-center justify-content-end">
                                <div class="col-2">
                                    <label for="total_payment">Total Payment</label>
                                    <div class="d-flex align-items-center gap-1">
                                        <span>₱ </span>
                                        <span><input type="text" name="total_payment" id="edit_total_payment"
                                                class="form-control" readonly></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Kawa Hot Bath Fee</button>
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
                            name="members[${gIndex}][${row.sIndex}]"
                            value="${row.qty}"
                            min="0">
                    </td>

                    <!-- SUBTOTAL -->
                    <td>
                        <input type="text"
                            class="form-control ${isEdit ? 'edit-kawa-subtotal' : 'kawa-subtotal'}"
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

    });
</script>
