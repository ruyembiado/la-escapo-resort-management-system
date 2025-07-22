@extends('layouts.auth') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text">Picnic Table</h1>
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPicnicTableModal">Add Picnic Table
            Fee</a>
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
                        @foreach ($picnicTables as $picnictable)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $picnictable->visitor->first_name }} {{ $picnictable->visitor->middle_name }}
                                    {{ $picnictable->visitor->last_name }}</td>
                                <td style="padding: 10px;">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th style="padding: 5px;">Quantity</th>
                                                <th style="padding: 5px;">Fee</th>
                                                <th style="padding: 5px;">Sub-total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="padding: 5px;">{{ $picnictable->quantity }}</td>
                                                <td style="padding: 5px;">₱ {{ number_format($picnictable->fee, 2) }}</td>
                                                <td style="padding: 5px;">₱
                                                    {{ number_format($picnictable->total_payment, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td>₱ {{ number_format($picnictable->total_payment, 2) }}</td>
                                <td>
                                    @if ($picnictable->payment_status === 'pending')
                                        <span class="badge bg-danger">{{ ucfirst($picnictable->payment_status) }}</span>
                                    @else
                                        <span class="badge bg-success">{{ ucfirst($picnictable->payment_status) }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($picnictable->created_at)->format('F j, Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center justify-c gap-2">
                                        <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editPicnicTableModal" data-id="{{ $picnictable->id }}"
                                            data-visitor-id="{{ $picnictable->visitor_id }}"
                                            data-total-payment="{{ $picnictable->total_payment }}"
                                            data-quantity="{{ $picnictable->quantity }}"
                                            data-payment-status="{{ $picnictable->payment_status }}">
                                            Edit
                                        </a>
                                        <form action="{{ route('picnictable.destroy', $picnictable->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this picnic table record?')">
                                                Delete
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

    <!-- Add Picnic Table Fee Modal -->
    <div class="modal fade" id="addPicnicTableModal" tabindex="-1" role="dialog"
        aria-labelledby="addPicnicTableModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('picnictable.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPicnicTableModalLabel">Add Picnic Table Fee</h5>
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
                                        <input type="text" name="service_title" id="service_title" value="Picnic Table"
                                            class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="d-flex align-items-start gap-1">
                                <div class="form-group col-2">
                                    <label for="service_title">Quantity</label>
                                    <div class="col-12">
                                        <input type="number" name="quantity" id="quantity" class="form-control" required
                                            class="form-control" min="1">
                                    </div>
                                </div>
                                <div class="form-group col-2">
                                    <label for="service_title">Fee</label>
                                    <div class="col-12">
                                        <input type="text" name="fee" id="fee" class="form-control" readonly
                                            value="200.00">
                                    </div>
                                </div>
                                <div class="form-group col-2">
                                    <label for="service_title">Total Payment</label>
                                    <div class="col-12">
                                        <input type="text" name="total_payment" id="total_payment"
                                            class="form-control" readonly placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Picnic Table Fee</button>
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
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPicnicTableModalLabel">Edit Picnic Table Fee</h5>
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
                                    <label for="members">Payment Status</label>
                                    <div class="col-12">
                                        <select name="payment_status" class="form-control" id="edit_payment_status">
                                            <option value="">Select Status</option>
                                            <option value="pending">Pending</option>
                                            <option value="paid">Paid</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="service_title">Service</label>
                                    <div class="col-12">
                                        <input type="text" name="service_title" id="service_title" value="Picnic Table"
                                            class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="d-flex align-items-start gap-1">
                                <div class="form-group col-2">
                                    <label for="service_title">Quantity</label>
                                    <div class="col-12">
                                        <input type="number" name="quantity" id="edit_quantity" class="form-control"
                                            required class="form-control" min="1">
                                    </div>
                                </div>
                                <div class="form-group col-2">
                                    <label for="service_title">Fee</label>
                                    <div class="col-12">
                                        <input type="text" name="fee" id="fee" class="form-control"
                                            readonly value="200.00">
                                    </div>
                                </div>
                                <div class="form-group col-2">
                                    <label for="service_title">Total Payment</label>
                                    <div class="col-12">
                                        <input type="text" name="total_payment" id="edit_total_payment"
                                            class="form-control" readonly placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Picnic Table Fee</button>
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
        $('#addPicnicTableModal').on('shown.bs.modal', function() {
            $('#visitor_name').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: "Select a visitor",
                allowClear: true,
                dropdownParent: $('#addPicnicTableModal')
            });
        });

        const quantityInput = document.getElementById('quantity');
        const feeInput = document.getElementById('fee');
        const totalPaymentInput = document.getElementById('total_payment');

        function updateTotalPayment() {
            const quantity = parseInt(quantityInput.value) || 0;
            const fee = parseFloat(feeInput.value) || 0;
            const total = quantity * fee;

            totalPaymentInput.value = total.toFixed(2); // Format to 2 decimal places
        }

        // Trigger when quantity is changed
        quantityInput.addEventListener('input', updateTotalPayment);
    });
</script>

{{-- EDIT FORM SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editModal = document.getElementById('editPicnicTableModal');

        if (editModal) {
            // When the modal is about to be shown
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                // Extract data attributes from the triggering button
                const totalPayment = button.getAttribute('data-total-payment');
                const paymentStatus = button.getAttribute('data-payment-status');
                const visitorId = button.getAttribute('data-visitor-id');
                const picnicTableID = button.getAttribute('data-id');
                const quantity = button.getAttribute('data-quantity');

                // Set hidden inputs and field values
                document.getElementById('edit_picnic_table_id').value = picnicTableID;
                document.getElementById('_visitor_id').value = visitorId;
                $('#edit_visitor_id').val(visitorId).trigger('change');
                $('#edit_payment_status').val(paymentStatus);
                document.getElementById('edit_quantity').value = quantity;
                document.getElementById('edit_total_payment').value = totalPayment;

                // Also calculate total again just in case
                calculateEditTotalPayment();
            });

            // Add input event listener to recalculate total on quantity change
            const editQuantityInput = document.getElementById('edit_quantity');
            if (editQuantityInput) {
                editQuantityInput.addEventListener('input', calculateEditTotalPayment);
            }
        }

        // Function to calculate and set total payment
        function calculateEditTotalPayment() {
            const quantity = parseInt(document.getElementById('edit_quantity').value) || 0;
            const fee = parseFloat(document.querySelector('#editPicnicTableModal input[name="fee"]').value) ||
                0;
            const total = quantity * fee;

            document.getElementById('edit_total_payment').value = total.toFixed(2);
        }

        // Initialize Select2 for visitor (optional but included in your code)
        $('#editPicnicTableModal').on('shown.bs.modal', function() {
            $('#edit_visitor_id').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: "Select a visitor",
                allowClear: true,
                dropdownParent: $('#editPicnicTableModal')
            });

            // Calculate total when modal is opened with values already set
            calculateEditTotalPayment();
        });
    });
</script>
