@extends('layouts.auth')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="d-flex">
            <i class="fas fa-glass-water fa-2x text-dark me-2"></i>
            <div class="d-flex flex-column">
                <h1 class="h3 mb-0 text">AVAILED SERVICES</h1>
                <h6 class="mb-0">Guest | Drinks</h6>
            </div>
        </div>
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMeals">Add Foods & Drinks Fee</a>
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
                <a href="{{ url('meals') }}" class="btn bg-theme-primary text-light d-flex align-items-center gap-2">
                    <i class="fa-solid fa-utensils"></i>
                    Foods
                </a>
                <a href="{{ url('beverages') }}" class="btn btn-success text-light d-flex align-items-center gap-2">
                    <i class="fa-solid fa-glass-water"></i>
                    Drinks
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered border-dark" id="dataTable1" width="100%" cellspacing="0"
                    style="min-width:2000px;">
                    <thead>
                        <tr>
                            <th width="5%" class="bg-theme-primary text-light text-center">NO.</th>
                            <th class="bg-theme-primary text-light">MAIN GUEST</th>
                            <th width="10%" class="bg-theme-primary text-light text-center">TOTAL MEMBERS</th>
                            <th class="bg-theme-primary text-light">SERVICE DETAILS</th>
                            <th class="bg-theme-primary text-light">TOTAL FEE</th>
                            <th class="bg-theme-primary text-light">STATUS</th>
                            <th class="bg-theme-primary text-light">DATE CREATED</th>
                            <th class="bg-theme-primary text-light sticky-action">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($beverages as $beverage)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>
                                    {{ optional($beverage->visitor)->first_name }}
                                    {{ optional($beverage->visitor)->middle_name }}
                                    {{ optional($beverage->visitor)->last_name }}
                                </td>
                                <td class="text-center">
                                    {{ count($beverage->visitor->companions) + 1 }}
                                </td>
                                @php
                                    $item_names = json_decode($beverage->item_name, true);
                                    $fee = json_decode($beverage->fee, true);
                                    $quantity = json_decode($beverage->quantity, true);
                                @endphp
                                <td style="padding: 0;">
                                    <table class="table table-bordered border-dark m-0"
                                        style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th class="bg-theme-primary text-light" style="padding: 5px;">Item</th>
                                                <th class="bg-theme-primary text-light" style="padding: 5px;">Fee</th>
                                                <th class="bg-theme-primary text-light" style="padding: 5px;">Quantity</th>
                                                <th class="bg-theme-primary text-light" style="padding: 5px;">Sub-total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($item_names as $index => $item)
                                                @if (!empty($quantity[$index]))
                                                    <tr>
                                                        <td width="50%" style="padding: 8px;">{{ $item }}</td>
                                                        <td style="padding: 8px;"> ₱{{ number_format($fee[$index], 2) }}
                                                        </td>
                                                        <td style="padding: 8px;">{{ $quantity[$index] ?? 'N/A' }}</td>
                                                        <td style="padding: 8px;">
                                                            ₱{{ number_format((float) ($fee[$index] ?? 0) * (float) ($quantity[$index] ?? 0), 2) }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                                <td>₱ {{ number_format($beverage->total_payment, 2) }}</td>
                                <td>
                                    @if ($beverage->payment_status === 'Unpaid')
                                        <span class="badge bg-danger">{{ ucfirst($beverage->payment_status) }}</span>
                                    @else
                                        <span class="badge bg-success">{{ ucfirst($beverage->payment_status) }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($beverage->created_at)->format('F j, Y') }}</td>
                                <td class="sticky-action">
                                    <div class="d-flex align-items-center justify-c gap-2">
                                        <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editBeveragesModal" data-meal-id="{{ $beverage->id }}"
                                            data-visitor-id="{{ $beverage->visitor_id }}" data-items='<?php echo json_encode([
                                                'item_name' => $item_names,
                                                'fee' => $fee,
                                                'quantity' => $quantity,
                                            ]); ?>'
                                            data-total-payment="{{ $beverage->total_payment }}"
                                            data-payment-status="{{ $beverage->payment_status }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('beverage.destroy', $beverage->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this beverage(s) record?')">
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

    <!-- Add Meals Modal -->
    <div class="modal fade" id="addMeals" tabindex="-1" role="dialog" aria-labelledby="addMealsLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="max-width: 1500px;">
            <form action="{{ route('meal.store') }}" method="POST">
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

                        <div class="row">
                            <div class="col-md-6">
                                <div
                                    class="bg-theme-primary d-flex align-items-center gap-2 justify-content-center text-light p-2">
                                    <i class="fa fa-utensils fa-2x"></i>
                                    <h3 class="m-0">FOODS</h3>
                                </div>

                                <div class="form-group mb-2">
                                    <table class="table table-bordered border-dark"
                                        style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th class="bg-theme-primary text-light">CATEGORY</th>
                                                <th class="bg-theme-primary text-light">ITEMS</th>
                                                <th class="bg-theme-primary text-light">FEE</th>
                                                <th class="bg-theme-primary text-light">QUANTITY</th>
                                                <th class="bg-theme-primary text-light">SUB-TOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $groupedFoods = $foodFees->groupBy('food_category');
                                            @endphp
                                            @foreach ($groupedFoods as $category => $items)
                                                @php
                                                    $items = collect($items);
                                                    $rowCount = $items->count();
                                                @endphp

                                                @foreach ($items as $index => $item)
                                                    <tr>
                                                        {{-- CATEGORY (ROWSPAN ADDED) --}}
                                                        @if ($loop->first)
                                                            <td rowspan="{{ $rowCount }}" class="align-middle">
                                                                {{ ucwords(str_replace('_', ' ', $category)) }}
                                                            </td>
                                                        @endif

                                                        {{-- ITEM --}}
                                                        <td class="align-middle">
                                                            {{ $item->service_name }}
                                                            <input type="hidden"
                                                                name="meal_items[{{ $category }}][{{ $index }}][name]"
                                                                value="{{ $item->service_name }}">
                                                        </td>

                                                        {{-- PRICE --}}
                                                        <td class="align-middle">
                                                            ₱{{ number_format($item->fee, 2) }}
                                                            <input type="hidden"
                                                                name="meal_items[{{ $category }}][{{ $index }}][fee]"
                                                                value="{{ $item->fee }}">
                                                        </td>

                                                        {{-- QUANTITY --}}
                                                        <td width="15%">
                                                            <input type="number" class="form-control quantity-input"
                                                                name="meal_items[{{ $category }}][{{ $index }}][qty]"
                                                                min="0" value="0"
                                                                data-price="{{ $item->fee }}">
                                                        </td>

                                                        {{-- SUBTOTAL --}}
                                                        <td width="20%">
                                                            <input type="text" class="form-control subtotal"
                                                                name="meal_items[{{ $category }}][{{ $index }}][subtotal]"
                                                                value="0.00" readonly>
                                                        </td>

                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <!-- TOTAL -->
                                    <div class="form-group mt-2">
                                        <div class="d-flex align-items-center justify-content-end gap-3">
                                            <label>Payment Status:</label>
                                            <div class="col-2">
                                                <select name="food_payment_status" class="form-control">
                                                    <option value="">Select status</option>
                                                    <option value="Paid">Paid</option>
                                                    <option value="Unpaid">Unpaid</option>
                                                </select>
                                            </div>

                                            <label>Total Fee:</label>
                                            <div class="col-2">
                                                <div class="d-flex">
                                                    <span class="input-group-text bg-theme-primary text-light">₱</span>
                                                    <input type="text" name="food_total_payment"
                                                        id="food_total_payment" value="0.00" class="form-control"
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
                                    <i class="fa fa-glass-water fa-2x"></i>
                                    <h3 class="m-0">DRINKS</h3>
                                </div>
                                <div class="form-group mb-2">
                                    <table class="table table-bordered border-dark"
                                        style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th class="bg-theme-primary text-light" style="padding: 10px;">ITEMS</th>
                                                <th class="bg-theme-primary text-light" style="padding: 10px;">FEE</th>
                                                <th class="bg-theme-primary text-light" style="padding: 10px;">QUANTITY
                                                </th>
                                                <th class="bg-theme-primary text-light" style="padding: 10px;">SUB-TOTAL
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($drinkFees as $index => $item)
                                                <tr>
                                                    <td class="align-middle" width="50%">
                                                        {{ $item->service_name }}
                                                        <input type="hidden"
                                                            name="beverage_items[{{ $index }}][name]"
                                                            value="{{ $item->service_name }}">
                                                    </td>

                                                    <td class="align-middle" width="15%">
                                                        ₱{{ number_format($item->fee, 2) }}
                                                        <input type="hidden"
                                                            name="beverage_items[{{ $index }}][fee]"
                                                            value="{{ $item->fee }}">
                                                    </td>

                                                    <td width="15%">
                                                        <input class="form-control quantity-input" type="number"
                                                            name="beverage_items[{{ $index }}][qty]"
                                                            min="0" value="0"
                                                            data-price="{{ $item->fee }}">
                                                    </td>

                                                    <td width="20%">
                                                        <input type="text" class="form-control subtotal"
                                                            name="beverage_items[{{ $index }}][subtotal]"
                                                            value="0.00" readonly>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <!-- TOTAL -->
                                    <div class="form-group mt-2">
                                        <div class="d-flex align-items-center justify-content-end gap-3">
                                            <label>Payment Status:</label>
                                            <div class="col-2">
                                                <select name="drink_payment_status" class="form-control">
                                                    <option value="">Select status</option>
                                                    <option value="Paid">Paid</option>
                                                    <option value="Unpaid">Unpaid</option>
                                                </select>
                                            </div>

                                            <label>Total Fee:</label>
                                            <div class="col-2">
                                                <div class="d-flex">
                                                    <span class="input-group-text bg-theme-primary text-light">₱</span>
                                                    <input type="text" name="drink_total_payment"
                                                        id="drink_total_payment" value="0.00" class="form-control"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
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

    <!-- Edit Beverages Modal -->
    <div class="modal fade" id="editBeveragesModal" tabindex="-1" role="dialog"
        aria-labelledby="editBeveragesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('beverage.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="beverage_id" id="edit_beverage_id">
                <input type="hidden" name="visitor_id" id="edit_visitor_id_hidden">
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
                                    <select name="visitor_id" class="form-control select2" id="edit_visitor_id" required
                                        data-placeholder="Select a visitor">
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

                        <div class="col-md-12">
                            <div
                                class="bg-theme-primary d-flex align-items-center gap-2 justify-content-center text-light p-2">
                                <i class="fa fa-glass-water fa-2x"></i>
                                <h3 class="m-0">DRINKS</h3>
                            </div>
                            <div class="form-group mb-2">
                                <table class="table table-bordered border-dark"
                                    style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr>
                                            <th class="bg-theme-primary text-light" style="padding: 10px;">ITEMS</th>
                                            <th class="bg-theme-primary text-light" style="padding: 10px;">FEE</th>
                                            <th class="bg-theme-primary text-light" style="padding: 10px;">QUANTITY
                                            </th>
                                            <th class="bg-theme-primary text-light" style="padding: 10px;">SUB-TOTAL
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($drinkFees as $index => $item)
                                            <tr>
                                                <td class="align-middle" width="50%">
                                                    {{ $item->service_name }}
                                                    <input type="hidden"
                                                        name="beverage_items[{{ $index }}][name]"
                                                        value="{{ $item->service_name }}">
                                                </td>

                                                <td class="align-middle" width="15%">
                                                    ₱{{ number_format($item->fee, 2) }}
                                                    <input type="hidden" name="beverage_items[{{ $index }}][fee]"
                                                        value="{{ $item->fee }}">
                                                </td>

                                                <td width="15%">
                                                    <input class="form-control quantity-input" type="number"
                                                        name="beverage_items[{{ $index }}][qty]" min="0"
                                                        value="0" data-price="{{ $item->fee }}">
                                                </td>

                                                <td width="20%">
                                                    <input type="text" class="form-control subtotal"
                                                        name="beverage_items[{{ $index }}][subtotal]"
                                                        value="0.00" readonly>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- TOTAL -->
                                <div class="form-group mt-2">
                                    <div class="d-flex align-items-center justify-content-end gap-3">
                                        <label>Payment Status:</label>
                                        <div class="col-2">
                                            <select name="drink_payment_status" class="form-control">
                                                <option value="">Select status</option>
                                                <option value="Paid">Paid</option>
                                                <option value="Unpaid">Unpaid</option>
                                            </select>
                                        </div>

                                        <label>Total Fee:</label>
                                        <div class="col-2">
                                            <div class="d-flex">
                                                <span class="input-group-text bg-theme-primary text-light">₱</span>
                                                <input type="text" name="drink_total_payment" id="drink_total_payment"
                                                    value="0.00" class="form-control" readonly>
                                            </div>
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
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // =========================
        // SELECT2 (ADD MODAL)
        // =========================
        $('#addMeals').on('shown.bs.modal', function() {
            $('#visitor_name').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: "Select a visitor",
                allowClear: true,
                dropdownParent: $('#addMeals')
            });
        });

        // =========================
        // FOOD + DRINK CALCULATION (FIXED)
        // =========================
        $(document).on('input', '.quantity-input', function() {

            const quantity = parseFloat($(this).val()) || 0;
            const price = parseFloat($(this).data('price')) || 0;
            const subtotal = quantity * price;

            // update row subtotal
            $(this).closest('tr').find('.subtotal').val(subtotal.toFixed(2));

            updateTotals();
        });

        function updateTotals() {
            let foodTotal = 0;
            let drinkTotal = 0;

            // FOOD SECTION (first table)
            $('#addMeals .col-md-6:first .subtotal').each(function() {
                foodTotal += parseFloat($(this).val()) || 0;
            });

            // DRINK SECTION (second table)
            $('#addMeals .col-md-6:last .subtotal').each(function() {
                drinkTotal += parseFloat($(this).val()) || 0;
            });

            $('#food_total_payment').val(foodTotal.toFixed(2));
            $('#drink_total_payment').val(drinkTotal.toFixed(2));
        }

        // =========================
        // RESET ADD MODAL
        // =========================
        $('#addMeals').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();

            $('.subtotal').val('0.00');

            $('#food_total_payment').val('0.00');
            $('#drink_total_payment').val('0.00');

            $('#visitor_name').val(null).trigger('change');
        });

        // =========================
        // OPEN EDIT BEVERAGES MODAL (FIXED)
        // =========================
        $('#editBeveragesModal').on('show.bs.modal', function(event) {

            const button = event.relatedTarget;

            const beverageId = button.getAttribute('data-meal-id');
            const visitorId = button.getAttribute('data-visitor-id');
            const totalPayment = button.getAttribute('data-total-payment');
            const paymentStatus = button.getAttribute('data-payment-status');

            let data = {};
            try {
                data = JSON.parse(button.getAttribute('data-items')) || {};
            } catch (e) {
                data = {};
            }

            const names = data.item_name || [];
            const qtys = data.quantity || [];
            const fees = data.fee || [];

            // SET BASIC DATA
            $('#edit_beverage_id').val(beverageId);
            $('#edit_visitor_id_hidden').val(visitorId);
            $('#edit_visitor_id').val(visitorId).trigger('change');
            $('#editBeveragesModal select[name="drink_payment_status"]').val(paymentStatus);

            // RESET TABLE
            $('#editBeveragesModal .quantity-input').val(0);
            $('#editBeveragesModal .subtotal').val('0.00');

            // CREATE MAP
            const itemMap = {};
            names.forEach((name, i) => {
                const key = name + '-' + fees[i];
                itemMap[key] = {
                    qty: parseFloat(qtys[i]) || 0,
                    fee: parseFloat(fees[i]) || 0
                };
            });

            // POPULATE TABLE
            $('#editBeveragesModal tbody tr').each(function() {

                const row = $(this);

                const itemName = row.find('input[name*="[name]"]').val();
                const itemFee = row.find('input[name*="[fee]"]').val();

                const key = itemName + '-' + itemFee;

                if (itemMap[key]) {
                    const qty = itemMap[key].qty;
                    const price = itemMap[key].fee;
                    const subtotal = qty * price;

                    row.find('.quantity-input').val(qty);
                    row.find('.subtotal').val(subtotal.toFixed(2));
                }
            });

            updateEditDrinkTotal();
        });

        // =========================
        // EDIT DRINK CALCULATION
        // =========================
        $(document).on('input', '#editBeveragesModal .quantity-input', function() {

            const quantity = parseFloat($(this).val()) || 0;
            const price = parseFloat($(this).data('price')) || 0;
            const subtotal = quantity * price;

            $(this).closest('tr').find('.subtotal').val(subtotal.toFixed(2));

            updateEditDrinkTotal();
        });

        function updateEditDrinkTotal() {
            let total = 0;

            $('#editBeveragesModal .subtotal').each(function() {
                total += parseFloat($(this).val()) || 0;
            });

            $('#editBeveragesModal input[name="drink_total_payment"]').val(total.toFixed(2));
        }

        // =========================
        // RESET EDIT MODAL
        // =========================
        $('#editBeveragesModal').on('hidden.bs.modal', function() {

            $(this).find('form')[0].reset();

            $('#editBeveragesModal .subtotal').val('0.00');

            $('#edit_visitor_id').val(null).trigger('change');
        });

        // =========================
        // SELECT2 INIT (EDIT)
        // =========================
        $('#editBeveragesModal').on('shown.bs.modal', function() {
            $('#edit_visitor_id').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: "Select a visitor",
                allowClear: true,
                dropdownParent: $('#editBeveragesModal')
            });
        });
    });
</script>
