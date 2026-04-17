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
                        @foreach ($waterTubings as $watertubing)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ optional($watertubing->visitor)->first_name }}
                                    {{ optional($watertubing->visitor)->middle_name }}
                                    {{ optional($watertubing->visitor)->last_name }}
                                </td>
                                @php
                                    $categories = json_decode($watertubing->category, true);
                                    $members = json_decode($watertubing->members, true);
                                    $ages = json_decode($watertubing->age, true);
                                    $fees = json_decode($watertubing->fee, true);
                                @endphp
                                <td style="padding: 10px;">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th style="padding: 5px;">Category</th>
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
                                <td>₱ {{ number_format($watertubing->total_payment, 2) }}</td>
                                <td>
                                    @if ($watertubing->payment_status === 'pending')
                                        <span class="badge bg-danger">{{ ucfirst($watertubing->payment_status) }}</span>
                                    @else
                                        <span class="badge bg-success">{{ ucfirst($watertubing->payment_status) }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($watertubing->created_at)->format('F j, Y') }}</td>
                                <td>
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
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('watertubing.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addWaterTubingModalLabel">Add Water Tubing Fee</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <div class="d-flex align-items-start gap-1">
                                <div class="form-group col-6">
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
                                <div class="form-group">
                                    <small id="remaining_members_note" class="text-muted"></small>
                                </div>
                                <div class="form-group col-1">
                                    <label for="age">Age</label>
                                    <div class="">
                                        <input readonly type="text" id="age" class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group col-2">
                                    <label for="members">Payment Status</label>
                                    <div class="col-12">
                                        <select name="payment_status" class="form-control" id="payment_status">
                                            <option value="">Select Status</option>
                                            <option value="pending">Pending</option>
                                            <option value="paid">Paid</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="service_title">Service</label>
                                    <div class="col-12">
                                        <input type="text" name="service_title" id="service_title" value="Water Tubing"
                                            class="form-control" readonly>
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
                                                'name' => 'Children 700M',
                                                'age' => '0-11',
                                                'checked' => false,
                                                'price' => '250.00',
                                            ],
                                            [
                                                'name' => 'Student 700M',
                                                'age' => '12-21',
                                                'checked' => false,
                                                'price' => '250.00',
                                            ],
                                            [
                                                'name' => 'Regular 700M',
                                                'age' => '22-59',
                                                'checked' => false,
                                                'price' => '250.00',
                                            ],
                                            [
                                                'name' => 'PWD 700M',
                                                'age' => 'Any',
                                                'checked' => false,
                                                'price' => '250.00',
                                            ],
                                            [
                                                'name' => 'Senior Citizen 700M',
                                                'age' => '60+',
                                                'checked' => false,
                                                'price' => '250.00',
                                            ],
                                            [
                                                'name' => 'Children 1.5KM',
                                                'age' => '0-11',
                                                'checked' => false,
                                                'price' => '499.00',
                                            ],
                                            [
                                                'name' => 'Student 1.5KM',
                                                'age' => '12-21',
                                                'checked' => false,
                                                'price' => '499.00',
                                            ],
                                            [
                                                'name' => 'Regular 1.5KM',
                                                'age' => '22-59',
                                                'checked' => false,
                                                'price' => '499.00',
                                            ],
                                            [
                                                'name' => 'PWD 1.5KM',
                                                'age' => 'Any',
                                                'checked' => false,
                                                'price' => '499.00',
                                            ],
                                            [
                                                'name' => 'Senior Citizen 1.5KM',
                                                'age' => '60+',
                                                'checked' => false,
                                                'price' => '499.00',
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
                                                    value="" max="1">
                                            </td>
                                            <td style="padding: 5px;">
                                                <input class="form-control" type="text" name="age[]"
                                                    value="{{ $category['age'] }}" readonly>
                                            </td>
                                            <td style="padding: 5px;">
                                                <input class="form-control" type="text" name="fee[]" min="0"
                                                    value="{{ $category['price'] }}" readonly>
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
                                        <span><input type="text" name="total_payment" id="total_payment"
                                                class="form-control" readonly></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Water Tubing Fee</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Water Tubing Fee Modal -->
    <div class="modal fade" id="editWaterTubingModal" tabindex="-1" role="dialog"
        aria-labelledby="editWaterTubingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('watertubing.update') }}" method="POST">
                <input type="hidden" name="water_tubing_id" id="edit_watertubing_id">
                <input type="hidden" name="visitor_id" id="_visitor_id">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editWaterTubingModalLabel">Edit Water Tubing Fee</h5>
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
                                <div class="form-group col-2">
                                    <label for="service_title">Service</label>
                                    <div class="col-12">
                                        <input type="text" name="service_title" id="service_title"
                                            value="Water Tubing" class="form-control" readonly>
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
                                                'name' => 'Children 700M',
                                                'age' => '0-11',
                                                'checked' => false,
                                                'price' => '250.00',
                                            ],
                                            [
                                                'name' => 'Student 700M',
                                                'age' => '12-21',
                                                'checked' => false,
                                                'price' => '250.00',
                                            ],
                                            [
                                                'name' => 'Regular 700M',
                                                'age' => '22-59',
                                                'checked' => false,
                                                'price' => '250.00',
                                            ],
                                            [
                                                'name' => 'PWD 700M',
                                                'age' => 'Any',
                                                'checked' => false,
                                                'price' => '250.00',
                                            ],
                                            [
                                                'name' => 'Senior Citizen 700M',
                                                'age' => '60+',
                                                'checked' => false,
                                                'price' => '250.00',
                                            ],
                                            [
                                                'name' => 'Children 1.5KM',
                                                'age' => '0-11',
                                                'checked' => false,
                                                'price' => '499.00',
                                            ],
                                            [
                                                'name' => 'Student 1.5KM',
                                                'age' => '12-21',
                                                'checked' => false,
                                                'price' => '499.00',
                                            ],
                                            [
                                                'name' => 'Regular 1.5KM',
                                                'age' => '22-59',
                                                'checked' => false,
                                                'price' => '499.00',
                                            ],
                                            [
                                                'name' => 'PWD 1.5KM',
                                                'age' => 'Any',
                                                'checked' => false,
                                                'price' => '499.00',
                                            ],
                                            [
                                                'name' => 'Senior Citizen 1.5KM',
                                                'age' => '60+',
                                                'checked' => false,
                                                'price' => '499.00',
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
                                                    value="" max="1">
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
                        <button type="submit" class="btn btn-primary">Update Water Tubing Fee</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection <!-- End the content section -->
{{-- ADD FORM SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        function getAllowedIndexes(age, isPwd = false) {
            if (isPwd) return [3, 8]; // PWD

            if (age <= 11) return [0, 5]; // Children
            if (age <= 21) return [1, 6]; // Student
            if (age <= 59) return [2, 7]; // Regular
            return [4, 9]; // Senior
        }

        function resetAddRows() {
            $('#addWaterTubingModal tbody tr').each(function() {
                $(this).hide();
                $(this).find('input[name="members[]"]').val('').prop('readonly', true);
                $(this).find('input[id="sub-total"]').val('');
            });
            $('#total_payment').val('0.00');
        }

        function showAllowedAddRows(age, isPwd) {
            resetAddRows();

            const allowed = getAllowedIndexes(age, isPwd);

            allowed.forEach(index => {
                const row = $('#addWaterTubingModal tbody tr').eq(index);
                row.show();
                row.find('input[name="members[]"]').prop('readonly', false).val(0);
            });

            // default: first option = 1
            const firstRow = $('#addWaterTubingModal tbody tr').eq(allowed[0]);
            firstRow.find('input[name="members[]"]').val(1);

            updateAddTotals();
        }

        function enforceSingleChoice($changedInput) {
            const $visibleInputs = $('#addWaterTubingModal tbody tr:visible input[name="members[]"]');

            $visibleInputs.each(function() {
                if (this !== $changedInput[0]) {
                    $(this).val(0);
                }
            });

            if (parseInt($changedInput.val()) !== 1) {
                $changedInput.val(1);
            }
        }

        function updateAddTotals() {
            let total = 0;

            $('#addWaterTubingModal tbody tr:visible').each(function() {
                const members = parseInt($(this).find('input[name="members[]"]').val()) || 0;
                const fee = parseFloat($(this).find('input[name="fee[]"]').val()) || 0;
                const subtotal = members * fee;

                $(this).find('input[id="sub-total"]').val(subtotal.toFixed(2));
                total += subtotal;
            });

            $('#total_payment').val(total.toFixed(2));
        }

        // ---------------- EVENTS ----------------

        $('#visitor_name').on('change', function() {
            const visitor_id = $(this).val();
            if (!visitor_id) return;

            const baseUrl = window.location.origin;
            const folderName = window.location.pathname.split('/')[1];
            const url = `${baseUrl}/${folderName}/get-visitor-members/${visitor_id}`;

            $.get(url, function(res) {
                const age = parseInt(res.age) || 0;
                const isPwd = res.is_pwd || false;

                $('#age').val(age);
                showAllowedAddRows(age, isPwd);
            });
        });

        // when user clicks qty
        $(document).on('input', '#addWaterTubingModal input[name="members[]"]', function() {
            enforceSingleChoice($(this));
            updateAddTotals();
        });

        $('#addWaterTubingModal').on('hidden.bs.modal', function() {
            resetAddRows();
            $('#visitor_name').val(null).trigger('change');
        });

    });
</script>

{{-- EDIT FORM SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        function getAllowedIndexes(age, isPwd = false) {
            if (isPwd) return [3, 8];

            if (age <= 11) return [0, 5];
            if (age <= 21) return [1, 6];
            if (age <= 59) return [2, 7];
            return [4, 9];
        }

        function resetEditRows() {
            $('#editWaterTubingModal tbody tr').each(function() {
                $(this).hide();
                $(this).find('input[name="members[]"]').val('').prop('readonly', true);
                $(this).find('input[id="sub-total"]').val('');
            });
            $('#edit_total_payment').val('0.00');
        }

        function showAllowedEditRows(age, isPwd, existingMembers = []) {
            resetEditRows();

            const allowed = getAllowedIndexes(age, isPwd);

            allowed.forEach(index => {
                const row = $('#editWaterTubingModal tbody tr').eq(index);
                row.show();
                row.find('input[name="members[]"]').prop('readonly', false).val(0);
            });

            // restore saved choice if exists
            let selectedIndex = allowed[0];

            allowed.forEach(i => {
                if (parseInt(existingMembers[i]) === 1) {
                    selectedIndex = i;
                }
            });

            $('#editWaterTubingModal tbody tr')
                .eq(selectedIndex)
                .find('input[name="members[]"]').val(1);

            updateEditTotals();
        }

        function enforceSingleChoiceEdit($changedInput) {
            const $visibleInputs = $('#editWaterTubingModal tbody tr:visible input[name="members[]"]');

            $visibleInputs.each(function() {
                if (this !== $changedInput[0]) {
                    $(this).val(0);
                }
            });

            if (parseInt($changedInput.val()) !== 1) {
                $changedInput.val(1);
            }
        }

        function updateEditTotals() {
            let total = 0;

            $('#editWaterTubingModal tbody tr:visible').each(function() {
                const members = parseInt($(this).find('input[name="members[]"]').val()) || 0;
                const fee = parseFloat($(this).find('input[name="fee[]"]').val()) || 0;
                const subtotal = members * fee;

                $(this).find('input[id="sub-total"]').val(subtotal.toFixed(2));
                total += subtotal;
            });

            $('#edit_total_payment').val(total.toFixed(2));
        }

        const editModal = document.getElementById('editWaterTubingModal');

        if (editModal) {
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                const membersArr = JSON.parse(button.getAttribute('data-total-members') || '[]');
                const visitorId = button.getAttribute('data-visitor-id');
                const waterTubingId = button.getAttribute('data-id');
                const paymentStatus = button.getAttribute('data-payment-status');

                $('#edit_watertubing_id').val(waterTubingId);
                $('#edit_visitor_id').val(visitorId).trigger('change');
                $('#_visitor_id').val(visitorId);
                $('#edit_payment_status').val(paymentStatus);

                const baseUrl = window.location.origin;
                const folderName = window.location.pathname.split('/')[1];
                const url = `${baseUrl}/${folderName}/get-visitor-members/${visitorId}`;

                $.get(url, function(res) {
                    const age = parseInt(res.age) || 0;
                    const isPwd = res.is_pwd || false;

                    $('#edit_age').val(age);
                    showAllowedEditRows(age, isPwd, membersArr);
                });
            });
        }

        $(document).on('input', '#editWaterTubingModal input[name="members[]"]', function() {
            enforceSingleChoiceEdit($(this));
            updateEditTotals();
        });

        $('#editWaterTubingModal').on('hidden.bs.modal', function() {
            resetEditRows();
        });

    });
</script>
