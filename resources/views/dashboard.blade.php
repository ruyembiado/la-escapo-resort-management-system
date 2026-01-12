@extends('layouts.auth') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text">Dashboard</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex h-100 flex-column justify-content-between">
                        <div class="row align-items-center justify-content-between">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Visitors | Today</div>
                                <div class="h3 count-number mb-0 font-weight-bold text-success">{{ $visitorsToday }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-users fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex h-100 flex-column justify-content-between">
                        <div class="row align-items-center justify-content-between">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Visitors | This Week</div>
                                <div class="h3 count-number mb-0 font-weight-bold text-success">{{ $visitorsThisWeek }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-users fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex h-100 flex-column justify-content-between">
                        <div class="row align-items-center justify-content-between">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Visitors | This Month</div>
                                <div class="h3 count-number mb-0 font-weight-bold text-success">{{ $visitorsThisMonth }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-users fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex h-100 flex-column justify-content-between">
                        <div class="row align-items-center justify-content-between">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Visitors | This Year</div>
                                <div class="h3 count-number mb-0 font-weight-bold text-success">{{ $visitorsThisYear }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-users fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex h-100 flex-column justify-content-between">
                        <div class="row align-items-center justify-content-between">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Visitors | This Month</div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>Age</th>
                                            <th class="text-start">Members</th>
                                            <th class="text-start">Contact No.</th>
                                            <th>Address</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($visitors as $visitor)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $visitor->first_name . ' ' . $visitor->middle_name . ' ' . $visitor->last_name }}
                                                </td>
                                                <td class="text-start">{{ $visitor->gender }}</td>
                                                <td class="text-start">{{ $visitor->age }}</td>
                                                <td class="text-start">{{ $visitor->members }}</td>
                                                <td class="text-start">{{ $visitor->contact_number }}</td>
                                                <td>{{ $visitor->address }}</td>
                                                <td>{{ \Carbon\Carbon::parse($visitor->date_visit)->format('F j, Y') }}
                                                </td>
                                                {{-- <td>{{ \Carbon\Carbon::parse($visitor->created_at)->format('F j, Y \a\t h:i A') }}</td> --}}
                                                <td>
                                                    <div class="d-flex align-items-center justify-c gap-2">
                                                        <a href="#" class="btn btn-warning btn-sm"
                                                            data-bs-toggle="modal" data-bs-target="#editVisitorModal"
                                                            data-id="{{ $visitor->id }}"
                                                            data-first_name="{{ $visitor->first_name }}"
                                                            data-middle_name="{{ $visitor->middle_name }}"
                                                            data-last_name="{{ $visitor->last_name }}"
                                                            data-contact_number="{{ $visitor->contact_number }}"
                                                            data-gender="{{ $visitor->gender }}"
                                                            data-age="{{ $visitor->age }}"
                                                            data-members="{{ $visitor->members }}"
                                                            data-address="{{ $visitor->address }}"
                                                            data-date_visit="{{ $visitor->date_visit }}"
                                                            data-check_in="{{ $visitor->check_in }}"
                                                            data-check_out="{{ $visitor->check_out }}"
                                                            data-is_pwd="{{ $visitor->is_pwd }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('visitor.destroy', $visitor->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Are you sure you want to delete this visitor record?')">
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
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="d-flex h-100 flex-column justify-content-between">
                        <div class="row align-items-center justify-content-between">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Monthly Visitors Data Chart</div>
                            </div>
                            <canvas id="visitorsChart" height="100"></canvas>
                            <div class="text-center mt-3">
                                <p>Year {{ now()->year }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Visitor Modal -->
    <div class="modal fade" id="editVisitorModal" tabindex="-1" role="dialog" aria-labelledby="editVisitorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('visitor.update') }}" id="editVisitorForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editVisitorModalLabel">Edit Visitor</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="visitor_id" id="visitor_id">

                        <div class="form-group mb-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="col-4 date-visit">
                                    <label for="date_visit">Date</label>
                                    <input type="date" name="date_visit" id="edit_date_visit" value=""
                                        class="form-control" required />
                                </div>
                                <div class="col-3 date-visit">
                                    <label for="check_in">Check-In Time</label>
                                    <input type="time" name="check_in" id="edit_check_in" value=""
                                        class="form-control" required />
                                </div>
                                <div class="col-3 date-visit">
                                    <label for="check_out">Check-Out Time</label>
                                    <input type="time" name="check_out" id="edit_check_out" value=""
                                        class="form-control" />
                                </div>
                            </div>

                            <div class="form-group mb-2">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="col-4 first-name">
                                        <label for="first_name">First Name</label>
                                        <input type="text" name="first_name" id="edit_first_name"
                                            class="form-control" required>
                                    </div>
                                    <div class="col-3 middle-name">
                                        <label for="middle_name">Middle Name</label>
                                        <input type="text" name="middle_name" id="edit_middle_name"
                                            class="form-control">
                                    </div>
                                    <div class="col-4 last-name">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" name="last_name" id="edit_last_name" class="form-control"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-2">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="contact_number col-3">
                                        <label for="contact_number">Contact No.</label>
                                        <input type="text" name="contact_number" id="edit_contact_number"
                                            class="form-control" required>
                                    </div>

                                    <div class="gender col-3">
                                        <label for="gender">Gender</label>
                                        <select name="gender" id="edit_gender" class="form-control" required>
                                            <option value="">Select gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>

                                    <div class="age col-2">
                                        <label for="age">Age</label>
                                        <input type="number" name="age" id="edit_age" class="form-control"
                                            required>
                                    </div>

                                    <div class="is_pwd">
                                        <label class="form-check-label" for="is_pwd">
                                            Is PWD?
                                        </label>
                                        <input class="form-check-input" type="checkbox" name="is_pwd" id="edit_is_pwd"
                                            value="1">
                                    </div>

                                    <div class="members col-2">
                                        {{-- <label for="members">Members</label> --}}
                                        <input type="hidden" name="members" id="edit_members" class="form-control"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-2">
                                <label for="address">Address</label>
                                <textarea name="address" id="edit_address" class="form-control" required></textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Visitor</button>
                        </div>
                    </div>
            </form>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->

<script src="{{ asset('public/js/chart.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('visitorsChart')?.getContext('2d');
        if (!ctx) return;

        const visitorsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [{
                    label: 'Total Visitors',
                    data: {!! json_encode($visitorsPerMonth) !!},
                    backgroundColor: '#198754',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Edit Visitor Modal Populating
        const modal = document.getElementById('editVisitorModal');
        modal.addEventListener('show.bs.modal', function(e) {
            var button = e.relatedTarget; // Button that triggered the modal

            // Fetch data-* attributes
            var id = button.getAttribute('data-id');
            var first_name = button.getAttribute('data-first_name');
            var middle_name = button.getAttribute('data-middle_name');
            var last_name = button.getAttribute('data-last_name');
            var contact_number = button.getAttribute('data-contact_number');
            var gender = button.getAttribute('data-gender');
            var age = button.getAttribute('data-age');
            var members = button.getAttribute('data-members');
            var address = button.getAttribute('data-address');
            var date_visit = button.getAttribute('data-date_visit');
            var check_in = button.getAttribute('data-check_in');
            var check_out = button.getAttribute('data-check_out');
            var is_pwd = button.getAttribute('data-is_pwd');

            // Log the data to the console for debugging
            console.log("Edit Modal Data:", {
                id,
                first_name,
                middle_name,
                last_name,
                contact_number,
                gender,
                age,
                members,
                address,
                date_visit,
                check_in,
                check_out,
                is_pwd,
            });

            // Set the values of the modal fields
            document.getElementById('visitor_id').value = id;
            document.getElementById('edit_first_name').value = first_name;
            document.getElementById('edit_middle_name').value = middle_name;
            document.getElementById('edit_last_name').value = last_name;
            document.getElementById('edit_contact_number').value = contact_number;
            document.getElementById('edit_gender').value = gender;
            document.getElementById('edit_age').value = age;
            document.getElementById('edit_members').value = members;
            document.getElementById('edit_address').value = address;
            document.getElementById('edit_date_visit').value = date_visit;
            document.getElementById('edit_check_in').value = check_in;
            document.getElementById('edit_check_out').value = check_out;
            document.getElementById('edit_is_pwd').checked = is_pwd == '1' ? true : false;
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners for input changes
        document.getElementById('start_date').addEventListener('change', validateAndSubmit);
        document.getElementById('end_date').addEventListener('change', validateAndSubmit);

        function validateAndSubmit() {
            var startDate = document.getElementById('start_date').value;
            var endDate = document.getElementById('end_date').value;

            // Check if both dates are filled out
            if (startDate && endDate) {
                document.getElementById('dateRangeForm').submit(); // Submit the form if both dates are provided
            }
        }
    });
</script>
