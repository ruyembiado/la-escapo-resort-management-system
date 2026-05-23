<div class="d-flex align-items-center justify-content-between bg-theme-primary p-4">

    <a href="{{ url()->current() }}"
        class="btn btn-reload d-flex align-items-center gap-2">
        <i class="fas fa-sync-alt text-dark"></i>
        Reload
    </a>

    <a href="{{ url('bills') }}"
        class="btn {{ Request::is('bills') ? 'bg-green-tertiary text-light' : 'btn-outline-light ' }} d-flex align-items-center gap-2">
        <i class="fas fa-file-invoice-dollar"></i>
        Bills
    </a>

    <a href="{{ url('meals') }}"
        class="btn {{ Request::is('meals') || Request::is('beverages') ? 'bg-green-tertiary text-light' : 'btn-outline-light ' }} d-flex align-items-center gap-2">
        <i class="fas fa-utensils"></i>
        Food & Drinks
    </a>

    <a href="{{ url('massages') }}"
        class="btn {{ Request::is('massages') || Request::is('accommodations') ? 'bg-green-tertiary text-light' : 'btn-outline-light ' }} d-flex align-items-center gap-2">
        <i class="fas fa-spa"></i>
        Retreat & Relaxation
    </a>

    <a href="{{ url('kawa-hot-baths') }}"
        class="btn {{ Request::is('kawa-hot-baths') || Request::is('picnic-tables') ? 'bg-green-tertiary text-light' : 'btn-outline-light ' }} d-flex align-items-center gap-2">
        <i class="fas fa-hot-tub-person"></i>
        Bathing & Wellness
    </a>

    <a href="{{ url('water-tubings') }}"
        class="btn {{ Request::is('water-tubings') ? 'bg-green-tertiary text-light' : 'btn-outline-light ' }} d-flex align-items-center gap-2">
        <i class="fas fa-water"></i>
        Water Tubing
    </a>

    <a href="{{ url('entrances') }}"
        class="btn {{ Request::is('entrances') ? 'bg-green-tertiary text-light' : 'btn-outline-light ' }} d-flex align-items-center gap-2">
        <i class="fas fa-ticket"></i>
        Entrance Fee
    </a>

</div>
