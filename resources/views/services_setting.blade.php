@extends('layouts.auth')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="d-flex">
            <i class="fas fa-cog fa-2x text-dark me-2"></i>
            <div class="d-flex flex-column">
                <h1 class="h3 mb-0 text">SERVICES</h1>
                <h6 class="mb-0">Settings | Services</h6>
            </div>
        </div>
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">Add Service</a>
    </div>

    <!-- Content Row -->
    @include('layouts.services-setting-nav')
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="bg-theme-primary text-light border-dark">No.</th>
                            <th class="bg-theme-primary text-light border-dark">Name</th>
                            <th class="bg-theme-primary text-light border-dark">Service Type</th>
                            @if (($filter ?? '') == 'foods')
                                <th class="bg-theme-primary text-light border-dark">Food Category</th>
                            @endif
                            {{-- <th class="bg-theme-primary text-light border-dark">Food/Drink Type</th> --}}
                            <th class="bg-theme-primary text-light border-dark">Fee</th>
                            <th class="bg-theme-primary text-light border-dark">Date Created</th>
                            <th class="bg-theme-primary text-light border-dark">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $index => $service)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $service->service_name }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $service->service_type)) }}</td>
                                @if (($filter ?? '') == 'foods')
                                    <td>{{ $service->food_category ? ucwords(str_replace('_', ' ', $service->food_category)) : 'N/A' }}
                                    </td>
                                @endif
                                {{-- <td>{{ $service->food_type ? ucfirst($service->food_type) : 'N/A' }}</td> --}}
                                <td>₱{{ number_format($service->fee, 2) }}</td>
                                <td>{{ $service->created_at->format('F j, Y') }}</td>
                                <td class="d-flex gap-1 justify-content-center">
                                    <!-- Edit Button -->
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editServiceModal{{ $service->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <!-- Delete -->
                                    <form action="{{ route('service.destroy', $service->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this service?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach ($services as $service)
        <!-- Update Modal -->
        <div class="modal fade" style="z-index: 9999 !important;" id="editServiceModal{{ $service->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form action="{{ route('service.update', $service->id) }}" method="POST">
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
                            <div
                                class="bg-theme-primary d-flex align-items-center gap-2 justify-content-center text-light p-2 mb-3">
                                <i class="fa fa-edit fa-2x"></i>
                                <h3 class="m-0">UPDATE SERVICE</h3>
                            </div>

                            @include('layouts.note')

                            <!-- Name + Service Type -->
                            <div class="form-group mb-2">
                                <div class="d-flex align-items-center gap-3">
                                    <label>Name:</label>
                                    <div class="col-4">
                                        <input type="text" name="service_name" class="form-control"
                                            value="{{ $service->service_name }}" required>
                                    </div>
                                    <label>Service Type:</label>
                                    <div class="col-3">
                                        <input type="hidden" class="form-control" name="service_type"
                                            value="{{ $service->service_type }}">
                                        <select disabled name="service_type" class="form-control edit_service_type"
                                            data-id="{{ $service->id }}" required>
                                            <option value="entrance_fee"
                                                {{ $service->service_type == 'entrance_fee' ? 'selected' : '' }}>
                                                Entrance Fee
                                            </option>
                                            <option value="foods"
                                                {{ $service->service_type == 'foods' ? 'selected' : '' }}>
                                                Foods
                                            </option>
                                            <option value="drinks"
                                                {{ $service->service_type == 'drinks' ? 'selected' : '' }}>
                                                Drinks
                                            </option>
                                            <option value="accommodation"
                                                {{ $service->service_type == 'accommodation' ? 'selected' : '' }}>
                                                Accommodation
                                            </option>
                                            <option value="massage"
                                                {{ $service->service_type == 'massage' ? 'selected' : '' }}>
                                                Massage
                                            </option>
                                            <option value="water_tubing"
                                                {{ $service->service_type == 'water_tubing' ? 'selected' : '' }}>
                                                Water Tubing
                                            </option>
                                            <option value="kawa_hot_bath"
                                                {{ $service->service_type == 'kawa_hot_bath' ? 'selected' : '' }}>
                                                Kawa Hot Bath
                                            </option>
                                            <option value="picnic_table"
                                                {{ $service->service_type == 'picnic_table' ? 'selected' : '' }}>
                                                Picnic Table
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Food/Drink Fields -->
                            <div class="form-group mb-2">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <!-- Foods: Category + Type -->
                                    <div class="col-7" id="editFoodFields{{ $service->id }}"
                                        style="{{ $service->service_type == 'foods' ? '' : 'display:none;' }}">
                                        <div class="d-flex flex-wrap align-items-center gap-3">
                                            <label>Food Category:</label>
                                            <div class="col-7">
                                                {{-- <input type="hidden" name="food_category" class="form-control"
                                                    value="{{ $service->food_category }}"> --}}
                                                <select name="food_category" class="form-control">
                                                    <option value="">Select Category
                                                    </option>
                                                    <option value="inasal"
                                                        {{ $service->food_category == 'inasal' ? 'selected' : '' }}>
                                                        Inasal</option>
                                                    <option value="namit_dishes"
                                                        {{ $service->food_category == 'namit_dishes' ? 'selected' : '' }}>
                                                        #Namit Dishes</option>
                                                    <option value="breakfast"
                                                        {{ $service->food_category == 'breakfast' ? 'selected' : '' }}>
                                                        Breakfast</option>
                                                    <option value="rice"
                                                        {{ $service->food_category == 'rice' ? 'selected' : '' }}>
                                                        Rice</option>
                                                </select>
                                            </div>
                                            {{-- <label>Food Type:</label>
                                            <div class="col-4">
                                                <select name="food_type" class="form-control">
                                                    <option value="">Select Type</option>
                                                    <option value="solo"
                                                        {{ $service->food_type == 'solo' ? 'selected' : '' }}>
                                                        Solo</option>
                                                    <option value="group"
                                                        {{ $service->food_type == 'group' ? 'selected' : '' }}>
                                                        Group</option>
                                                </select>
                                            </div> --}}
                                        </div>
                                    </div>
                                    <!-- Drinks: Only Type -->
                                    {{-- <div class="col-5" id="editDrinkFields{{ $service->id }}"
                                        style="{{ $service->service_type == 'drinks' ? '' : 'display:none;' }}">
                                        <div class="d-flex align-items-center gap-3">
                                            <label>Drink Type:</label>
                                            <div class="col-8">
                                                <select name="drink_type" class="form-control">
                                                    <option value="">Select Type</option>
                                                    <option value="solo"
                                                        {{ $service->food_type == 'solo' ? 'selected' : '' }}>
                                                        Solo</option>
                                                    <option value="group"
                                                        {{ $service->food_type == 'group' ? 'selected' : '' }}>
                                                        Group</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div> --}}
                                    <label>Fee:</label>
                                    <div class="col-2">
                                        <input type="number" name="fee" class="form-control"
                                            value="{{ $service->fee }}" min="0" required>
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
    @endforeach

    <!-- Add Service Modal -->
    <div class="modal fade" id="addServiceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('service.store') }}" method="POST" id="addServiceForm">
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
                                    <b class="modal-title mt-2 text-bold">La Escapo Mountain Resort</b>
                                    <span>Tuno, Tibiao, Antique</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-body">
                        <div
                            class="bg-theme-primary d-flex align-items-center gap-2 justify-content-center text-light p-2 mb-3">
                            <i class="fa fa-book fa-2x"></i>
                            <h3 class="m-0">ADD SERVICE</h3>
                        </div>

                        @if ($filter == 'entrance_fee')
                            @include('layouts.note')
                        @endif

                        @if ($filter)
                            <input type="hidden" name="service_type" value="{{ $filter ?? '' }}">
                        @endif

                        <div class="form-group mb-2">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <label>Name:</label>
                                <div class="col-4">
                                    <input type="text" name="service_name" class="form-control" placeholder="Name"
                                        required>
                                </div>
                                <label>Service Type:</label>
                                <div class="col-3">
                                    <select name="service_type" {{ $filter ? 'disabled' : '' }} id="service_type"
                                        class="form-control" required>
                                        <option value="">Select type</option>
                                        <option value="entrance_fee"
                                            {{ ($filter ?? '') == 'entrance_fee' ? 'selected' : '' }}>Entrance Fee</option>
                                        <option value="foods" {{ ($filter ?? '') == 'foods' ? 'selected' : '' }}>Foods
                                        </option>
                                        <option value="drinks" {{ ($filter ?? '') == 'drinks' ? 'selected' : '' }}>Drinks
                                        </option>
                                        <option value="accommodation"
                                            {{ ($filter ?? '') == 'accommodation' ? 'selected' : '' }}>Accommodation
                                        </option>
                                        <option value="massage" {{ ($filter ?? '') == 'massage' ? 'selected' : '' }}>
                                            Massage</option>
                                        <option value="water_tubing"
                                            {{ ($filter ?? '') == 'water_tubing' ? 'selected' : '' }}>Water Tubing</option>
                                        <option value="kawa_hot_bath"
                                            {{ ($filter ?? '') == 'kawa_hot_bath' ? 'selected' : '' }}>Kawa Hot Bath
                                        </option>
                                        <option value="picnic_table"
                                            {{ ($filter ?? '') == 'picnic_table' ? 'selected' : '' }}>Picnic Table</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Food Fields -->
                            <div class="form-group mb-2">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <div class="col-7" id="foodFields" style="display:none;">
                                        <div class="d-flex flex-wrap align-items-center gap-3">
                                            <label>Food Category:</label>
                                            <div class="col-7">
                                                <select name="food_category" class="form-control" disabled>
                                                    <option value="">Select Category</option>
                                                    <option value="inasal">Inasal</option>
                                                    <option value="namit_dishes">#Namit Dishes</option>
                                                    <option value="breakfast">Breakfast</option>
                                                    <option value="rice">Rice</option>
                                                </select>
                                            </div>
                                            {{-- <label>Food Type:</label>
                                        <div class="col-4">
                                            <select name="food_type" class="form-control" disabled>
                                                <option value="">Select Type</option>
                                                <option value="solo">Solo</option>
                                                <option value="group">Group</option>
                                            </select>
                                        </div> --}}
                                        </div>
                                    </div>
                                    {{-- <div class="col-5" id="drinkFields" style="display:none;">
                                    <div class="d-flex align-items-center gap-3">
                                        <label>Drink Type:</label>
                                        <div class="col-8">
                                            <select name="food_type" class="form-control" disabled>
                                                <option value="">Select Type</option>
                                                <option value="solo">Solo</option>
                                                <option value="group">Group</option>
                                            </select>
                                        </div>
                                    </div>
                                </div> --}}
                                    <label>Fee:</label>
                                    <div class="col-2">
                                        <input type="number" name="fee" class="form-control" min="0"
                                            placeholder="Amount" required>
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
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const serviceType = document.getElementById("service_type");
        const foodFields = document.getElementById("foodFields"); // restore this
        // const drinkFields = document.getElementById("drinkFields"); // still removed
        const foodCategorySelect = foodFields ? foodFields.querySelector('select[name="food_category"]') : null;

        function toggleAddFields() {
            if (!serviceType) return;
            if (serviceType.value === "foods") {
                if (foodFields) foodFields.style.display = "block";
                if (foodCategorySelect) foodCategorySelect.disabled = false;
            } else {
                if (foodFields) foodFields.style.display = "none";
                if (foodCategorySelect) {
                    foodCategorySelect.disabled = true;
                    foodCategorySelect.value = "";
                }
            }
        }

        toggleAddFields();
        serviceType.addEventListener("change", toggleAddFields);
        // FORM VALIDATION
        const addServiceForm = document.getElementById('addServiceForm');

        if (addServiceForm) {
            addServiceForm.addEventListener('submit', function(e) {
                if (serviceType.value === 'foods') {
                    const foodCategory = foodCategorySelect ? foodCategorySelect.value : '';

                    if (!foodCategory) {
                        e.preventDefault();
                        alert('Please select a food category.');
                    }
                }
            });
        }

        // EDIT MODALS
        document.querySelectorAll('.edit_service_type').forEach(select => {
            select.addEventListener('change', function() {

                let id = this.dataset.id;
                let foodFieldsDiv = document.getElementById('editFoodFields' + id);

                let foodCategorySelectEdit = foodFieldsDiv ?
                    foodFieldsDiv.querySelector('select[name="food_category"]') :
                    null;

                if (this.value === 'foods') {
                    if (foodFieldsDiv) foodFieldsDiv.style.display = 'block';
                    if (foodCategorySelectEdit) foodCategorySelectEdit.disabled = false;
                } else {
                    if (foodFieldsDiv) foodFieldsDiv.style.display = 'none';
                    if (foodCategorySelectEdit) {
                        foodCategorySelectEdit.disabled = true;
                        foodCategorySelectEdit.value = '';
                    }
                }
            });
        });

    });
</script>
