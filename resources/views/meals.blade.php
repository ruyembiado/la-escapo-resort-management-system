@extends('layouts.auth')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="d-flex">
            <i class="fas fa-utensils fa-2x text-dark me-2"></i>
            <div class="d-flex flex-column">
                <h1 class="h3 mb-0 text">AVAILED SERVICES</h1>
                <h6 class="mb-0">Guest | Foods</h6>
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
                <a href="{{ url('meals') }}" class="btn bg-green-tertiary text-light d-flex align-items-center gap-2">
                    <i class="fa-solid fa-utensils"></i>
                    Foods
                </a>
                <a href="{{ url('beverages') }}" class="btn bg-theme-primary text-light d-flex align-items-center gap-2">
                    <i class="fa-solid fa-glass-water"></i>
                    Drinks
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered border-dark" id="dataTable1" width="100%" cellspacing="0"
                    style="min-width:2000px;">
                    <thead>
                        <tr>
                            <th class="bg-green-secondary text-light">NO.</th>
                            <th class="bg-green-secondary text-light">MAIN GUEST</th>
                            <th width="10%" class="bg-green-secondary text-light text-center">TOTAL MEMBERS</th>
                            <th class="bg-green-secondary text-light">SERVICE DETAILS</th>
                            <th class="bg-green-secondary text-light">TOTAL FEE</th>
                            <th class="bg-green-secondary text-light">STATUS</th>
                            <th class="bg-green-secondary text-light">DATE CREATED</th>
                            <th class="bg-green-secondary text-light sticky-action">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($meals as $meal)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ optional($meal->visitor)->first_name }}
                                    {{ optional($meal->visitor)->middle_name }}
                                    {{ optional($meal->visitor)->last_name }}
                                </td>
                                <td class="text-center">
                                    {{ count($meal->visitor->companions) + 1 }}
                                </td>
                                @php
                                    $food_names = json_decode($meal->item_name, true) ?? [];
                                    $fee = json_decode($meal->fee, true) ?? [];
                                    $quantity = json_decode($meal->quantity, true) ?? [];
                                @endphp
                                <td style="padding: 0;">
                                    <table class="table table-bordered m-0"
                                        style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th class="bg-green-tertiary text-light text-center" style="padding: 5px;">
                                                    No.</th>
                                                <th class="bg-green-tertiary text-light" style="padding: 5px;">Menu</th>
                                                <th class="bg-green-tertiary text-light" style="padding: 5px;">Fee</th>
                                                <th class="bg-green-tertiary text-light" style="padding: 5px;">Quantity</th>
                                                <th class="bg-green-tertiary text-light" style="padding: 5px;">Sub-Total
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($food_names as $index => $food)
                                                @php
                                                    $qty = $quantity[$index] ?? 0;
                                                    $price = $fee[$index] ?? 0;
                                                    $subtotal = $price * $qty;
                                                @endphp
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td width="40%">{{ $food }}</td>
                                                    <td>₱{{ number_format($price, 2) }}</td>
                                                    <td>{{ $qty }}</td>
                                                    <td>₱{{ number_format($subtotal, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                                <td>₱ {{ number_format($meal->total_payment, 2) }}</td>
                                <td>
                                    @if ($meal->payment_status === 'Unpaid')
                                        <span class="badge bg-danger">{{ ucfirst($meal->payment_status) }}</span>
                                    @else
                                        <span class="badge bg-success">{{ ucfirst($meal->payment_status) }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($meal->created_at)->format('F j, Y') }}</td>
                                <td class="sticky-action">
                                    <div class="d-flex align-items-center justify-c gap-2">
                                        <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editMealsModal" data-meal-id="{{ $meal->id }}"
                                            data-visitor-id="{{ $meal->visitor_id }}"
                                            data-items='{{ json_encode([
                                                'names' => json_decode($meal->item_name),
                                                'qty' => json_decode($meal->quantity),
                                                'fee' => json_decode($meal->fee),
                                            ]) }}'
                                            data-total-payment="{{ $meal->total_payment }}"
                                            data-payment-status="{{ $meal->payment_status }}"
                                            data-created-at="{{ $meal->created_at }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('meal.destroy', $meal->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this food record?')">
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
                    @include('layouts.modal-header')
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
                                    class="bg-green-secondary d-flex align-items-center gap-2 justify-content-center text-light p-3 mb-3">
                                    <i class="fa fa-utensils fa-2x"></i>
                                    <h3 class="m-0">FOODS</h3>
                                </div>

                                <div class="form-group mb-2">
                                    <table class="table table-bordered border-dark"
                                        style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th class="bg-green-tertiary text-light">CATEGORY</th>
                                                <th class="bg-green-tertiary text-light">ITEMS</th>
                                                <th class="bg-green-tertiary text-light">FEE</th>
                                                <th class="bg-green-tertiary text-light">QUANTITY</th>
                                                <th class="bg-green-tertiary text-light">SUB-TOTAL</th>
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
                                    class="bg-green-secondary d-flex align-items-center gap-2 justify-content-center text-light p-3 mb-3">
                                    <i class="fa fa-glass-water fa-2x"></i>
                                    <h3 class="m-0">DRINKS</h3>
                                </div>
                                <div class="form-group mb-2">
                                    <table class="table table-bordered border-dark"
                                        style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th class="bg-green-tertiary text-light" style="padding: 10px;">ITEMS</th>
                                                <th class="bg-green-tertiary text-light" style="padding: 10px;">FEE</th>
                                                <th class="bg-green-tertiary text-light" style="padding: 10px;">QUANTITY
                                                </th>
                                                <th class="bg-green-tertiary text-light" style="padding: 10px;">SUB-TOTAL
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
                        <button type="submit" class="btn bg-theme-primary text-light">Save</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Meals Modal -->
    <div class="modal fade" id="editMealsModal" tabindex="-1" role="dialog" aria-labelledby="editMealsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('meal.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="meal_id" id="edit_meal_id">
                <input type="hidden" name="visitor_id" id="edit_visitor_id_hidden">
                <div class="modal-content">
                    @include('layouts.modal-header')
                    <div class="modal-body">
                        <div
                            class="bg-green-secondary d-flex align-items-center gap-2 justify-content-center text-light p-3 mb-3">
                            <i class="fa fa-utensils fa-2x"></i>
                            <h3 class="m-0">FOODS</h3>
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
                                        <th class="bg-green-tertiary text-light" style="padding: 10px;">CATEGORY</th>
                                        <th class="bg-green-tertiary text-light" style="padding: 10px;">ITEMS</th>
                                        <th class="bg-green-tertiary text-light" style="padding: 10px;">FEE</th>
                                        <th class="bg-green-tertiary text-light" width="15%" style="padding: 10px;">
                                            QUANTITY</th>
                                        <th class="bg-green-tertiary text-light" width="15%" style="padding: 10px;">
                                            SUB-TOTAL</th>
                                    </tr>
                                </thead>
                                @php
                                    $groupedFoods = $foodFees->groupBy('food_category');
                                @endphp
                                <tbody id="edit_meal_items">
                                    @foreach ($groupedFoods as $category => $items)
                                        @php
                                            $rowCount = count($items);
                                        @endphp

                                        @foreach ($items as $index => $item)
                                            <tr>
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
                                                    ₱{{ $item->fee }}
                                                    <input type="hidden"
                                                        name="meal_items[{{ $category }}][{{ $index }}][fee]"
                                                        value="{{ $item->fee }}">
                                                </td>

                                                {{-- QTY --}}
                                                <td>
                                                    <input type="number" class="form-control edit-quantity-input"
                                                        name="meal_items[{{ $category }}][{{ $index }}][qty]"
                                                        min="0" value="0" data-price="{{ $item->fee }}">
                                                </td>

                                                {{-- SUBTOTAL --}}
                                                <td>
                                                    <input type="text" class="form-control edit-subtotal"
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
                                        <select name="food_payment_status" id="edit_food_payment_status"
                                            class="form-control">
                                            <option value="">Select status</option>
                                            <option value="Paid">Paid</option>
                                            <option value="Unpaid">Unpaid</option>
                                        </select>
                                    </div>

                                    <label>Total Fee:</label>
                                    <div class="col-2">
                                        <div class="d-flex">
                                            <span class="input-group-text bg-theme-primary text-light">₱</span>
                                            <input type="text" name="food_total_payment" id="edit_food_total_payment"
                                                value="0.00" class="form-control" readonly>
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
        // OPEN EDIT MODAL
        // =========================
        $('#editMealsModal').on('show.bs.modal', function(event) {

            const button = event.relatedTarget;

            const mealId = button.getAttribute('data-meal-id');
            const visitorId = button.getAttribute('data-visitor-id');
            const totalPayment = button.getAttribute('data-total-payment');
            const paymentStatus = button.getAttribute('data-payment-status');

            let data = {};
            try {
                data = JSON.parse(button.getAttribute('data-items')) || {};
            } catch (e) {
                data = {};
            }

            const names = data.names || [];
            const qtys = data.qty || [];
            const fees = data.fee || [];

            // reset first
            $('.edit-quantity-input').val(0);
            $('.edit-subtotal').val('0.00');

            // set hidden + select
            $('#edit_meal_id').val(mealId);
            $('#edit_visitor_id_hidden').val(visitorId);
            $('#edit_visitor_id').val(visitorId).trigger('change');
            $('#edit_food_payment_status').val(paymentStatus);

            // create map (name + fee = unique key)
            const itemMap = {};
            names.forEach((name, i) => {
                const key = name + '-' + fees[i];
                itemMap[key] = {
                    qty: parseFloat(qtys[i]) || 0,
                    fee: parseFloat(fees[i]) || 0
                };
            });

            // populate table
            $('#edit_meal_items tr').each(function() {
                const row = $(this);

                const itemName = row.find('input[name*="[name]"]').val();
                const itemFee = row.find('input[name*="[fee]"]').val();

                const key = itemName + '-' + itemFee;

                if (itemMap[key]) {
                    const qty = itemMap[key].qty;
                    const price = itemMap[key].fee;
                    const subtotal = qty * price;

                    row.find('.edit-quantity-input').val(qty);
                    row.find('.edit-subtotal').val(subtotal.toFixed(2));
                }
            });

            // set total
            updateEditTotal();

        });

        // =========================
        // EDIT CALCULATION
        // =========================
        $(document).on('input', '.edit-quantity-input', function() {

            const quantity = parseFloat($(this).val()) || 0;
            const price = parseFloat($(this).data('price')) || 0;
            const subtotal = quantity * price;

            $(this).closest('tr').find('.edit-subtotal').val(subtotal.toFixed(2));

            updateEditTotal();
        });

        function updateEditTotal() {
            let total = 0;

            $('.edit-subtotal').each(function() {
                total += parseFloat($(this).val()) || 0;
            });

            $('#edit_food_total_payment').val(total.toFixed(2));
        }

        // =========================
        // RESET EDIT MODAL
        // =========================
        $('#editMealsModal').on('hidden.bs.modal', function() {

            $('.edit-quantity-input').val(0);
            $('.edit-subtotal').val('0.00');

            $('#edit_food_total_payment').val('0.00');
            $('#edit_visitor_id').val(null).trigger('change');
            $('#edit_food_payment_status').val('');

        });

        // =========================
        // SELECT2 INIT (EDIT)
        // =========================
        $('#editMealsModal').on('shown.bs.modal', function() {
            $('#edit_visitor_id').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: "Select a visitor",
                allowClear: true,
                dropdownParent: $('#editMealsModal')
            });
        });
    });
</script>
