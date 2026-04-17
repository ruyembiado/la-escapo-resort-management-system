<div class="d-flex align-items-center justify-content-center gap-2 bg-theme-primary p-2">
    
    <a href="{{ url('bills') }}"
        class="btn {{ Request::is('bills') ? 'btn-success' : 'btn-outline-light ' }} d-flex align-items-center gap-2">
        <i class="fas fa-file-invoice-dollar"></i>
        Bills
    </a>

    <a href="{{ url('food-drinks') }}"
        class="btn {{ Request::is('food-drinks') ? 'btn-success' : 'btn-outline-light ' }} d-flex align-items-center gap-2">
        <i class="fas fa-utensils"></i>
        Food & Drinks
    </a>

    <a href="{{ url('meals') }}"
        class="btn {{ Request::is('meals') || Request::is('beverages') ? 'btn-success' : 'btn-outline-light ' }} d-flex align-items-center gap-2">
        <i class="fas fa-spa"></i>
        Retreat & Relaxation
    </a>

    <a href="{{ url('accommodations') }}"
        class="btn {{ Request::is('accommodations') || Request::is('function-halls') ? 'btn-success' : 'btn-outline-light ' }} d-flex align-items-center gap-2">
        <i class="fas fa-hot-tub-person"></i>
        Bathing & Wellness
    </a>

    <a href="{{ url('water-tubings') }}"
        class="btn {{ Request::is('water-tubings') ? 'btn-success' : 'btn-outline-light ' }} d-flex align-items-center gap-2">
        <i class="fas fa-water"></i>
        Water Tubing
    </a>

    <a href="{{ url('entrances') }}"
        class="btn {{ Request::is('entrances') ? 'btn-success' : 'btn-outline-light ' }} d-flex align-items-center gap-2">
        <i class="fas fa-ticket"></i>
        Entrance Fee
    </a>

</div>