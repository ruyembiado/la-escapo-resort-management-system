<div class="d-flex flex-wrap align-items-center gap-3 justify-content-center bg-theme-primary p-4">

    <a href="{{ url('services') }}"
        class="btn {{ ($filter ?? 'all') == 'all' ? 'bg-green-tertiary text-light' : 'btn-outline-light' }}">
        All
    </a>

    <a href="{{ url('services?filter=entrance_fee') }}"
        class="btn {{ ($filter ?? '') == 'entrance_fee' ? 'bg-green-tertiary text-light' : 'btn-outline-light' }}">
        Entrance Fee
    </a>

    <a href="{{ url('services?filter=foods') }}"
        class="btn {{ ($filter ?? '') == 'foods' ? 'bg-green-tertiary text-light' : 'btn-outline-light' }}">
        Foods
    </a>

    <a href="{{ url('services?filter=drinks') }}"
        class="btn {{ ($filter ?? '') == 'drinks' ? 'bg-green-tertiary text-light' : 'btn-outline-light' }}">
        Drinks
    </a>

    <a href="{{ url('services?filter=accommodation') }}"
        class="btn {{ ($filter ?? '') == 'accommodation' ? 'bg-green-tertiary text-light' : 'btn-outline-light' }}">
        Accommodation
    </a>

    <a href="{{ url('services?filter=massage') }}"
        class="btn {{ ($filter ?? '') == 'massage' ? 'bg-green-tertiary text-light' : 'btn-outline-light' }}">
        Massage
    </a>

    <a href="{{ url('services?filter=water_tubing') }}"
        class="btn {{ ($filter ?? '') == 'water_tubing' ? 'bg-green-tertiary text-light' : 'btn-outline-light' }}">
        Water Tubing
    </a>

    <a href="{{ url('services?filter=kawa_hot_bath') }}"
        class="btn {{ ($filter ?? '') == 'kawa_hot_bath' ? 'bg-green-tertiary text-light' : 'btn-outline-light' }}">
        Kawa Hot Bath
    </a>

    <a href="{{ url('services?filter=picnic_table') }}"
        class="btn {{ ($filter ?? '') == 'picnic_table' ? 'bg-green-tertiary text-light' : 'btn-outline-light' }}">
        Picnic Table
    </a>

</div>
