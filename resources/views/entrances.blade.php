@extends('layouts.auth') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text">Entrances</h1>
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEntranceModal">Add Entrance Fee</a>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-body">
            {{-- <form method="GET" action="" class="" id="dateRangeForm">
                <div class="d-flex justify-content-start gap-2 align-items-end mb-4">
                    <div class="d-flex flex-column align-items-start" style="width: auto;">
                        <label for="date" class="mb-0">Start Date:</label>
                        <input type="date" name="start_date" value="{{ $start_date }}"
                            class="form-control form-control-sm" style="width: auto;" id="start_date" />
                    </div>
                    <div class="d-flex flex-column align-items-start" style="width: auto;">
                        <label for="date" class="mb-0">End Date:</label>
                        <input type="date" name="end_date" value="{{ $end_date }}"
                            class="form-control form-control-sm" style="width: auto;" id="end_date" />
                    </div>

                    <a href="{{ url()->current() }}" class="btn btn-sm btn-danger">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form> --}}

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
                        @foreach ($entrances as $entrance)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ optional($entrance->visitor)->first_name }}
                                    {{ optional($entrance->visitor)->middle_name }}
                                    {{ optional($entrance->visitor)->last_name }}
                                </td>
                                @php
                                    $categories = json_decode($entrance->category, true);
                                    $members = json_decode($entrance->members, true);
                                    $ages = json_decode($entrance->age, true);
                                    $fees = json_decode($entrance->fee, true);
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
                                <td>₱ {{ number_format($entrance->total_payment, 2) }}</td>
                                <td>
                                    @if ($entrance->payment_status === 'pending')
                                        <span class="badge bg-danger">{{ ucfirst($entrance->payment_status) }}</span>
                                    @else
                                        <span class="badge bg-success">{{ ucfirst($entrance->payment_status) }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($entrance->created_at)->format('F j, Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center justify-c gap-2">
                                        <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editEntranceModal" data-id="{{ $entrance->id }}"
                                            data-visitor-id="{{ $entrance->visitor_id }}"
                                            data-total-members='@json(json_decode($entrance->members))'
                                            data-total-payment="{{ $entrance->total_payment }}"
                                            data-payment-status="{{ $entrance->payment_status }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('entrance.destroy', $entrance->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this entrance record?')">
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

    <!-- Add Entrance Fee Modal -->
    <div class="modal fade" id="addEntranceModal" tabindex="-1" role="dialog" aria-labelledby="addEntranceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('entrance.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEntranceModalLabel">Add Entrance Fee</h5>
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
                                <div class="form-group">
                                    <label for="members">Payment Status</label>
                                    <div class="col-12">
                                        <select name="payment_status" class="form-control" id="payment_status">
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
                                                'price' => '30.00',
                                            ],
                                            [
                                                'name' => 'Student',
                                                'age' => '12-21',
                                                'checked' => false,
                                                'price' => '30.00',
                                            ],
                                            [
                                                'name' => 'Regular',
                                                'age' => '22-59',
                                                'checked' => false,
                                                'price' => '30.00',
                                            ],
                                            [
                                                'name' => 'PWD',
                                                'age' => 'Any',
                                                'checked' => false,
                                                'price' => '30.00',
                                            ],
                                            [
                                                'name' => 'Senior Citizen',
                                                'age' => '60+',
                                                'checked' => false,
                                                'price' => '30.00',
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($categories as $index => $category)
                                        <tr>
                                            <td width="30%" style="padding: 5px;">
                                                <div class="d-flex align-items-center gap-1">
                                                    <input type="hidden" name="category[]" value="{{ $category['name'] }}"
                                                        {{ $category['checked'] ? 'checked' : '' }}>
                                                    <span>{{ $category['name'] }}</span>
                                                </div>
                                            </td>
                                            <td width="25%" style="padding: 5px;">
                                                <input class="form-control" readonly type="number" name="members[]"
                                                    min="0" value="">
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
                        <button type="submit" class="btn btn-primary">Add Entrance Fee</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Entrance Fee Modal -->
    <div class="modal fade" id="editEntranceModal" tabindex="-1" role="dialog"
        aria-labelledby="editEntranceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('entrance.update') }}" method="POST">
                <input type="hidden" name="entrance_id" id="edit_entrance_id">
                <input type="hidden" name="visitor_id" id="_visitor_id">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEntranceModalLabel">Edit Entrance Fee</h5>
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
                                                'price' => '30.00',
                                            ],
                                            [
                                                'name' => 'Student',
                                                'age' => '12-21',
                                                'checked' => false,
                                                'price' => '30.00',
                                            ],
                                            [
                                                'name' => 'Regular',
                                                'age' => '22-59',
                                                'checked' => false,
                                                'price' => '30.00',
                                            ],
                                            [
                                                'name' => 'PWD',
                                                'age' => 'Any',
                                                'checked' => false,
                                                'price' => '30.00',
                                            ],
                                            [
                                                'name' => 'Senior Citizen',
                                                'age' => '60+',
                                                'checked' => false,
                                                'price' => '30.00',
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
                        <button type="submit" class="btn btn-primary">Update Entrance Fee</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection <!-- End the content section -->
{{-- ADD FORM SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2 for visitor_name for add form
        $('#addEntranceModal').on('shown.bs.modal', function() {
            $('#visitor_name').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: "Select a visitor",
                allowClear: true,
                dropdownParent: $('#addEntranceModal')
            });
        });

        // Get total members based on selected visitor
        $('#visitor_name').on('change', function() {
            var visitor_id = $(this).val();
            if (visitor_id) {
                var baseUrl = window.location.origin;
                var pathParts = window.location.pathname.split('/');
                var folderName = pathParts[1];
                var url = window.location.origin + '/' + folderName + '/get-visitor-members/' +
                    visitor_id;

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        const age = response.age || 0;
                        const isPwd = response.is_pwd || false;
                        $('#age').val(response.age).trigger('input');
                        autoCategorizeByAge(age, isPwd);
                    }
                });
            } else {
                $('#total_members').val('');
            }
        });

        let totalMembers = 0;

        // When total_members changes (manual or from AJAX)
        $('#total_members').on('input', function() {
            totalMembers = parseInt($(this).val()) || 0;
            resetMemberInputs();
        });

        // When any members[] input changes
        $(document).on('input', '#addEntranceModal input[name="members[]"]', function() {
            // updateMemberInputLimitsAddForm();
            updateSubtotalsAndTotalAddForm();
        });

        function autoCategorizeByAge(age, isPwd) {
            // Reset all
            $('#addEntranceModal input[name="members[]"]').each(function() {
                $(this).val('');
                $(this).prop('readonly', true);
            });

            let categoryIndex = -1;

            if (isPwd) {
                categoryIndex = 3; // PWD
            } else if (age >= 0 && age <= 11) {
                categoryIndex = 0; // Children
            } else if (age >= 12 && age <= 21) {
                categoryIndex = 1; // Student
            } else if (age >= 22 && age <= 59) {
                categoryIndex = 2; // Regular
            } else if (age >= 60) {
                categoryIndex = 4; // Senior Citizen
            }

            if (categoryIndex >= 0) {
                const row = $('#addEntranceModal tbody tr').eq(categoryIndex);
                const memberInput = row.find('input[name="members[]"]');
                memberInput.val(1);
                memberInput.prop('readonly', true);
            }

            updateSubtotalsAndTotalAddForm();
        }

        function resetMemberInputs() {
            $('#addEntranceModal input[name="members[]"]').each(function() {
                $(this).val('');
                $(this).attr('max', totalMembers);
                $(this).prop('readonly', false);
            });
        }

        // function updateMemberInputLimitsAddForm() {
        //     let used = 0;

        //     // First, calculate the total used
        //     $('#addEntranceModal input[name="members[]"]').each(function() {
        //         used += parseInt($(this).val()) || 0;
        //     });

        //     if (used > totalMembers) {
        //         // Reset all to 0 if over limit
        //         $('#addEntranceModal input[name="members[]"]').each(function() {
        //             $(this).val(0);
        //             $(this).attr('max', totalMembers);
        //             $(this).prop('readonly', totalMembers === 0);
        //         });

        //         // Optional: show feedback
        //         alert('Total members exceeded. All inputs have been reset to 0.');
        //         return;
        //     }

        //     // Otherwise, set max per input dynamically
        //     let remaining = totalMembers - used;

        //     $('#addEntranceModal input[name="members[]"]').each(function() {
        //         let currentVal = parseInt($(this).val()) || 0;
        //         let max = currentVal + remaining;
        //         $(this).attr('max', max);
        //         $(this).prop('readonly', totalMembers === 0);
        //     });

        //     // Lock further increments if full
        //     if (remaining <= 0) {
        //         $('#addEntranceModal input[name="members[]"]').each(function() {
        //             let val = parseInt($(this).val()) || 0;
        //             $(this).attr('max', val); // lock to current value
        //         });
        //     }
        // }

        function updateSubtotalsAndTotalAddForm() {
            let totalPayment = 0;

            $('#addEntranceModal tbody tr').each(function() {
                const memberInput = $(this).find('input[name="members[]"]');
                const feeInput = $(this).find('input[name="fee[]"]');
                const subtotalInput = $(this).find('input[id="sub-total"]');

                const members = parseInt(memberInput.val()) || 0;
                const fee = parseFloat(feeInput.val()) || 0;
                const subtotal = members * fee;

                subtotalInput.val(subtotal.toFixed(2));
                totalPayment += subtotal;
            });

            $('#total_payment').val(totalPayment.toFixed(2));
        }
    });
</script>

{{-- EDIT FORM SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editModal = document.getElementById('editEntranceModal');
        let editTotalMembers = 0; // Track total members for edit form

        if (editModal) {
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                const totalMembersArray = JSON.parse(button.getAttribute('data-total-members') || '[]');
                const visitorId = button.getAttribute('data-visitor-id');
                const entranceId = button.getAttribute('data-id');
                const totalPayment = button.getAttribute('data-total-payment');
                const paymentStatus = button.getAttribute('data-payment-status');

                // DEBUG: Log what you get
                console.log("Total Members:", totalMembersArray);

                // Set hidden fields
                document.getElementById('edit_entrance_id').value = entranceId;
                $('#edit_visitor_id').val(visitorId).trigger('change');
                $('#_visitor_id').val(visitorId);
                $('#edit_payment_status').val(paymentStatus);

                // All members[] inputs inside the modal
                const memberInputs = editModal.querySelectorAll('input[name="members[]"]');
                const feeInputs = editModal.querySelectorAll('input[name="fee[]"]');
                const subTotalInputs = editModal.querySelectorAll('input[id="sub-total"]');

                let totalPaymentCalculated = 0;
                let totalMembers = 0;

                memberInputs.forEach((input, index) => {
                    let raw = totalMembersArray[index];
                    let members = (raw === "null" || raw === null) ? "0" : parseInt(raw) || 0;
                    input.value = members;
                    const fee = parseFloat(feeInputs[index]?.value || 0);
                    const subtotal = members * fee;

                    subTotalInputs[index].value = subtotal.toFixed(2);
                    totalPaymentCalculated += subtotal;
                    totalMembers += members || 0;
                });

                // document.getElementById('edit_total_members').value = totalMembers;
                document.getElementById('edit_total_payment').value = totalPayment;
            });
        }

        // Initialize Select2 for visitor_name for edit form
        $('#editEntranceModal').on('shown.bs.modal', function() {
            $('#edit_visitor_id').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: "Select a visitor",
                allowClear: true,
                dropdownParent: $('#editEntranceModal')
            });
        });

        // Get total members from selected visitor for edit form
        $('#edit_visitor_id').on('change', function() {
            const visitor_id = $(this).val();
            if (visitor_id) {
                const baseUrl = window.location.origin;
                const pathParts = window.location.pathname.split('/');
                const folderName = pathParts[1];
                const url = `${baseUrl}/${folderName}/get-visitor-members/${visitor_id}`;

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        age = parseInt(response.age) || 0;
                        $('#edit_age').val(age);
                        resetEditMemberInputs();
                    }
                });
            } else {
                age = 0;
                $('#edit_age').val('');
                resetEditMemberInputs();
            }
        });

        // When any members[] input changes in edit form
        $(document).on('input', '#editEntranceModal input[name="members[]"]', function() {
            // updateMemberInputLimitsEditForm();
            updateEditSubtotalsAndTotal();
        });

        function resetEditMemberInputs() {
            $('#editEntranceModal input[name="members[]"]').each(function() {
                $(this).attr('max', 1);
                $(this).prop('readonly', true);
            });
        }

        function updateEditSubtotalsAndTotal() {
            let totalPayment = 0;
            let currentTotalMembers = 0;

            $('#editEntranceModal tbody tr').each(function() {
                const memberInput = $(this).find('input[name="members[]"]');
                const feeInput = $(this).find('input[name="fee[]"]');
                const subtotalInput = $(this).find('input[id="sub-total"]');

                const members = parseInt(memberInput.val()) || 0;
                const fee = parseFloat(feeInput.val()) || 0;
                const subtotal = members * fee;

                subtotalInput.val(subtotal.toFixed(2));
                totalPayment += subtotal;
                currentTotalMembers += members;
            });

            $('#edit_total_payment').val(totalPayment.toFixed(2));
        }

        // function updateMemberInputLimitsEditForm() {
        //     let used = 0;

        //     // First, calculate the total used
        //     $('#editEntranceModal input[name="members[]"]').each(function() {
        //         used += parseInt($(this).val()) || 0;
        //     });

        //     if (used > editTotalMembers) {
        //         // Reset all to 0 if over limit
        //         $('#editEntranceModal input[name="members[]"]').each(function() {
        //             $(this).val(0);
        //         });

        //         // Recalculate totals
        //         updateEditSubtotalsAndTotal();

        //         // Optional: show feedback
        //         alert('Total members exceeded. All inputs have been reset to 0.');
        //         return;
        //     }

        //     // Otherwise, set max per input dynamically
        //     let remaining = editTotalMembers - used;

        //     $('#editEntranceModal input[name="members[]"]').each(function() {
        //         let currentVal = parseInt($(this).val()) || 0;
        //         let max = currentVal + remaining;
        //         $(this).attr('max', max);
        //     });

        //     // Lock further increments if full
        //     if (remaining <= 0) {
        //         $('#editEntranceModal input[name="members[]"]').each(function() {
        //             let val = parseInt($(this).val()) || 0;
        //             $(this).attr('max', val); // lock to current value
        //         });
        //     }
        // }
    });
</script>
