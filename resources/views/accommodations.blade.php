@extends('layouts.auth')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text">Overnight Accommodations</h1>
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAccommodationModal">Add
            Overnight Accommodation</a>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Rooms</th>
                            <th>Total Payment</th>
                            <th>Status</th>
                            <th>Date Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($accommodations as $accommodation)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ optional($accommodation->visitor)->first_name }}
                                    {{ optional($accommodation->visitor)->middle_name }}
                                    {{ optional($accommodation->visitor)->last_name }}
                                </td>
                                <td>
                                    @php
                                        $rooms = json_decode($accommodation->room, true);
                                        $fees = json_decode($accommodation->fee, true);
                                    @endphp
                                    <ul style="list-style-type: none; padding: 5px; margin: 0;">
                                        @foreach ($rooms as $index => $room)
                                            <li>{{ $room }} - ₱{{ number_format($fees[$index], 2) }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>₱ {{ number_format($accommodation->total_payment, 2) }}</td>
                                <td>
                                    @if ($accommodation->payment_status === 'pending')
                                        <span class="badge bg-danger">{{ ucfirst($accommodation->payment_status) }}</span>
                                    @else
                                        <span class="badge bg-success">{{ ucfirst($accommodation->payment_status) }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($accommodation->created_at)->format('F j, Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center justify-c gap-2">
                                        <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editAccommodationModal" data-id="{{ $accommodation->id }}"
                                            data-visitor-id="{{ $accommodation->visitor_id }}"
                                            data-num-night="{{ $accommodation->num_nights }}"
                                            data-rooms="{{ $accommodation->room }}" data-fees="{{ $accommodation->fee }}" data-payment-status="{{ $accommodation->payment_status }}"
                                            data-total-payment="{{ $accommodation->total_payment }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('accommodation.destroy', $accommodation->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this overnight accommodation record?')">
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

    <!-- Add Accommodation Modal -->
    <div class="modal fade" id="addAccommodationModal" tabindex="-1" role="dialog"
        aria-labelledby="addAccommodationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('accommodation.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAccommodationModalLabel">Add Overnight Accommodation</h5>
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
                                        <select name="payment_status" class="form-control" id="edit_payment_status">
                                            <option value="">Select Status</option>
                                            <option value="pending">Pending</option>
                                            <option value="paid">Paid</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-4">
                                    <label for="service_title">Service</label>
                                    <div class="col-12">
                                        <input type="text" name="service_title" id="service_title"
                                            value="Overnight Accommodation" class="form-control" readonly="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="form-group col-2">
                                <label for="num_nights">No. of Nights</label>
                                <div class="col-12">
                                    <input type="number" name="num_nights" class="form-control" id="num_nights"
                                        min="1" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr class="bg-secondary text-light">
                                        <th style="padding: 10px;">SELECT</th>
                                        <th style="padding: 10px;">ROOM</th>
                                        <th style="padding: 10px;">FEE</th>
                                        <th style="padding: 10px;">SUB-TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $rooms = [
                                            [
                                                'name' => '(Good for 2 Persons)',
                                                'price' => '500.00',
                                            ],
                                            [
                                                'name' => '(Good for 5 Persons)',
                                                'price' => '1250.00',
                                            ],
                                            [
                                                'name' => '(Good for 10 Persons)',
                                                'price' => '2500.00',
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($rooms as $index => $room)
                                        <tr>
                                            <td width="5%" style="padding: 5px; text-align: center;">
                                                <input type="checkbox" name="checked[]" value="{{ $room['name'] }}"
                                                    class="room-checkbox">
                                            </td>
                                            <td width="" style="padding: 5px;">
                                                <input class="form-control" name="rooms[]" type="text"
                                                    value="{{ $room['name'] }}" readonly>
                                            </td>
                                            <td width="15%" style="padding: 5px;">
                                                <input class="form-control room-fee" type="text" name="fees[]"
                                                    min="0" value="{{ $room['price'] }}" readonly>
                                            </td>
                                            <td width="15%">
                                                <input type="text" readonly id="sub-total" class="form-control"
                                                    value="" readonly>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex align-items-center justify-content-end mt-3">
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
                        <button type="submit" class="btn btn-primary">Add Overnight Accommodation</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Accommodation Modal -->
    <div class="modal fade" id="editAccommodationModal" tabindex="-1" role="dialog"
        aria-labelledby="editAccommodationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('accommodation.update') }}" method="POST">
                <input type="hidden" name="accommodation_id" id="edit_accommodation_id">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAccommodationModalLabel">Edit Overnight Accommodation</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <div class="d-flex align-items-start gap-1">
                                <div class="form-group">
                                    <label for="visitor_id">Name</label>
                                    <select disabled name="visitor_id" class="form-control" id="edit_visitor_id">
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
                                        <select name="edit_payment_status" class="form-control" id="edit_payment_status">
                                            <option value="">Select Status</option>
                                            <option value="pending">Pending</option>
                                            <option value="paid">Paid</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-4">
                                    <label for="service_title">Service</label>
                                    <div class="col-12">
                                        <input type="text" name="service_title" id="service_title"
                                            value="Overnight Accommodation" class="form-control" readonly="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="form-group col-2">
                                <label for="num_nights">No. of Nights</label>
                                <div class="col-12">
                                    <input type="number" name="edit_num_nights" class="form-control" id="edit_num_nights"
                                        min="1" value="1" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr class="bg-secondary text-light">
                                        <th style="padding: 10px;">SELECT</th>
                                        <th style="padding: 10px;">ROOM</th>
                                        <th style="padding: 10px;">FEE</th>
                                        <th style="padding: 10px;">SUB-TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $rooms = [
                                            [
                                                'name' => '(Good for 2 Persons)',
                                                'price' => '500.00',
                                            ],
                                            [
                                                'name' => '(Good for 5 Persons)',
                                                'price' => '1250.00',
                                            ],
                                            [
                                                'name' => '(Good for 10 Persons)',
                                                'price' => '2500.00',
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($rooms as $index => $room)
                                        <tr>
                                            <td width="5%" style="padding: 5px; text-align: center;">
                                                <input type="checkbox" name="checked[]" value="{{ $room['name'] }}"
                                                    class="edit-room-checkbox">
                                            </td>
                                            <td width="" style="padding: 5px;">
                                                <input class="form-control" name="edit_rooms[]" type="text"
                                                    value="{{ $room['name'] }}" readonly>
                                            </td>
                                            <td width="15%" style="padding: 5px;">
                                                <input class="form-control edit-room-fee" type="text"
                                                    name="edit_fees[]" min="0" value="{{ $room['price'] }}"
                                                    readonly>
                                            </td>
                                            <td width="15%">
                                                <input type="text" readonly id="sub-total" class="form-control"
                                                    value="" readonly>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex align-items-center justify-content-end mt-3">
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
                        <button type="submit" class="btn btn-primary">Update Overnight Accommodation</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateTotalPayment() {
            const numNights = parseInt($('#num_nights').val()) || 1;
            const editNumNights = parseInt($('#edit_num_nights').val()) || 1;

            // Add Modal
            let total = 0;
            $('#addAccommodationModal tbody tr').each(function() {
                const checkbox = $(this).find('.room-checkbox');
                const fee = parseFloat($(this).find('.room-fee').val()) || 0;
                const subTotalInput = $(this).find('input[id="sub-total"]');

                if (checkbox.is(':checked')) {
                    const subTotal = fee * numNights;
                    subTotalInput.val(subTotal.toFixed(2));
                    total += subTotal;
                } else {
                    subTotalInput.val('');
                }
            });
            $('#total_payment').val(total.toFixed(2));

            // Edit Modal
            let editTotal = 0;
            $('#editAccommodationModal tbody tr').each(function() {
                const checkbox = $(this).find('.edit-room-checkbox');
                const fee = parseFloat($(this).find('.edit-room-fee').val()) || 0;
                const subTotalInput = $(this).find('input[id="sub-total"]');

                if (checkbox.is(':checked')) {
                    const subTotal = fee * editNumNights;
                    subTotalInput.val(subTotal.toFixed(2));
                    editTotal += subTotal;
                } else {
                    subTotalInput.val('');
                }
            });
            $('#edit_total_payment').val(editTotal.toFixed(2));
        }

        // Trigger recalculation
        $(document).on('change', '.room-checkbox, #num_nights', updateTotalPayment);
        $(document).on('change', '.edit-room-checkbox', updateTotalPayment);

        $('#addAccommodationModal').on('shown.bs.modal', function() {
            $('#visitor_name').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: "Select a visitor",
                allowClear: true,
                dropdownParent: $('#addAccommodationModal')
            });

            // Reset
            $('#total_payment').val('0.00');
            $('#num_nights').val(1);
            $('.room-checkbox').prop('checked', false);
            $('input[id="sub-total"]').val('');
        });

        $('#editAccommodationModal').on('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const modal = $(this);

            try {
                const accommodationId = button.getAttribute('data-id');
                const visitorId = button.getAttribute('data-visitor-id');
                const totalPayment = button.getAttribute('data-total-payment');
                const numNights = button.getAttribute('data-num-night') || 1;
                const paymentStatus = button.getAttribute('data-payment-status');

                let rooms = [];
                let fees = [];

                try {
                    rooms = JSON.parse(button.getAttribute('data-rooms') || '[]');
                    fees = JSON.parse(button.getAttribute('data-fees') || '[]');
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                }

                modal.find('#edit_accommodation_id').val(accommodationId);
                modal.find('#edit_visitor_id').val(visitorId).trigger('change');
                modal.find('#edit_total_payment').val(totalPayment);
                modal.find('#edit_num_nights').val(numNights);
                modal.find('#edit_payment_status').val(paymentStatus);
                modal.find('.edit-room-checkbox').prop('checked', false);
                modal.find('input[id="sub-total"]').val('');

                rooms.forEach(room => {
                    modal.find('.edit-room-checkbox[value="' + room + '"]').prop('checked',
                        true);
                });

                updateTotalPayment();

            } catch (error) {
                console.error('Error initializing edit modal:', error);
                alert('Error loading accommodation data. Please try again.');
                $(this).modal('hide');
            }
        });

        $('#submit-accommodation, #submit-edit-accommodation').on('click', function(e) {
            const form = $(this).closest('form');
            const checkedBoxes = form.find('.room-checkbox:checked, .edit-room-checkbox:checked');

            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one room.');
                return false;
            }
        });

        // Recalculate when number of nights in edit modal changes
        $('#edit_num_nights').on('input', updateTotalPayment);

        // Initial call
        updateTotalPayment();
    });
</script>
