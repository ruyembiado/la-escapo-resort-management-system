<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Beverage;
use App\Models\Companion;
use App\Models\Cottage;
use App\Models\Entrance;
use App\Models\KawaBath;
use App\Models\Massage;
use App\Models\Meal;
use App\Models\PicnicTable;
use App\Models\Service;
use App\Models\Visitor;
use App\Models\WaterTubing;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('created_at', 'desc')->get();

        return view('services_setting', compact('services'));
    }

    public function add_service(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'service_category' => 'nullable|string|max:255',
            'service_type' => 'required|string|max:255',
            'food_category' => 'nullable|string|max:255',
            'food_type' => 'nullable|string|max:255',
            'fee' => 'required|numeric|min:0',
        ]);

        Service::create([
            'service_name' => $request->service_name,
            'service_category' => $request->service_category ?? null,
            'service_type' => $request->service_type,
            'food_category' => $request->food_category ?? null,
            'food_type' => $request->food_type ?? null,
            'fee' => $request->fee,
        ]);

        return redirect()->back()->with('success', 'Service added successfully.');
    }

    public function update_service(Request $request, $id)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'service_category' => 'nullable|string|max:255',
            'service_type' => 'required|string|max:255',
            'food_category' => 'nullable|string|max:255',
            'food_type' => 'nullable|string|max:255',
            'fee' => 'required|numeric|min:0',
        ]);

        $service = Service::findOrFail($id);
        $service->update([
            'service_name' => $request->service_name,
            'service_category' => $request->service_category ?? null,
            'service_type' => $request->service_type,
            'food_category' => $request->food_category ?? null,
            'food_type' => $request->service_type == 'foods' ? $request->food_type : ($request->service_type == 'drinks' ? $request->drink_type : null),
            'fee' => $request->fee,
        ]);

        return redirect()->back()->with('success', 'Service updated successfully.');
    }

    public function delete_service($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        return redirect()->back()->with('success', 'Service deleted successfully.');
    }

    public function other_services()
    {
        return view('other_services');
    }

    public function entrances(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $letter = $request->letter;

        $entranceFees = Service::where('service_type', 'entrance_fee')->get();

        $entrances = Entrance::with('visitor', 'companions')
            ->when($start_date, function ($query) use ($start_date) {
                $query->whereDate('created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                $query->whereDate('created_at', '<=', $end_date);
            })
            ->when($letter, function ($query) use ($letter) {
                $query->whereHas('visitor', function ($q) use ($letter) {
                    $q->where('first_name', 'like', $letter . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('entrances', compact('entrances', 'start_date', 'end_date', 'letter', 'entranceFees'));
    }

    public function create_entrance_bill(Request $request)
    {
        $visitor = Visitor::create([
            'first_name' => $request->guest_first_name,
            'middle_name' => $request->guest_middle_name ?? '',
            'last_name' => $request->guest_last_name,
            'contact_number' => $request->guest_contact_number,
            'gender' => $request->guest_gender,
            'age' => $request->guest_age,
            'isPWD' => $request->guest_is_pwd ? 1 : 0,
            'address' => $request->guest_address,
            'date_visit' => $request->date_visit,
            'members' => $request->guest_members,
        ]);

        $entrance = Entrance::create([
            'visitor_id' => $visitor->id,
            'status' => $request->payment_status ?? 'unpaid',
            'total_payment' => $request->total_fee,
        ]);

        if ($request->guest_members > 0) {
            foreach ($request->companion_name as $index => $name) {
                Companion::create([
                    'visitor_id' => $visitor->id,
                    'entrance_id' => $entrance->id,
                    'name' => $name,
                    'gender' => $request->companion_gender[$index],
                    'age' => $request->companion_age[$index],
                    'isPWD' => $request->companion_is_pwd[$index] ?? 0,
                    'address' => $request->companion_address[$index],
                    'fee' => $request->companion_fee[$index],
                ]);
            }
        }

        return redirect()->route('entrances')->with('success', 'Visitor and entrance added successfully.');
    }

    public function update_entrance_bill(Request $request)
    {
        $entrance = Entrance::findOrFail($request->entrance_id);

        $visitor = $entrance->visitor;
        $visitor->update([
            'first_name' => $request->edit_guest_first_name,
            'middle_name' => $request->edit_guest_middle_name ?? '',
            'last_name' => $request->edit_guest_last_name,
            'contact_number' => $request->edit_guest_contact_number,
            'gender' => $request->edit_guest_gender,
            'age' => $request->edit_guest_age,
            'isPWD' => $request->edit_guest_is_pwd ? 1 : 0,
            'address' => $request->edit_guest_address,
            'date_visit' => $request->edit_date_visit,
            'members' => $request->edit_guest_members,
        ]);

        $entrance->update([
            'status' => $request->edit_payment_status ?? 'Unpaid',
            'total_payment' => $request->edit_total_fee,
        ]);

        Companion::where('entrance_id', $entrance->id)->delete();
        if ($request->edit_guest_members > 0 && $request->has('edit_companion_name')) {
            foreach ($request->edit_companion_name as $index => $name) {
                if (empty($name)) continue;

                Companion::create([
                    'visitor_id' => $visitor->id,
                    'entrance_id' => $entrance->id,
                    'name' => $name,
                    'gender' => $request->edit_companion_gender[$index] ?? 'Male',
                    'age' => $request->edit_companion_age[$index] ?? 0,
                    'isPWD' => isset($request->edit_companion_is_pwd[$index]) ? 1 : 0,
                    'address' => $request->edit_companion_address[$index] ?? '',
                    'fee' => $request->edit_companion_fee[$index] ?? 0,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Visitor and entrance updated successfully.');
    }

    public function delete_visitor_entrance($id)
    {
        $visitor = Visitor::findOrFail($id);
        $visitor->delete();

        return redirect()->route('entrances')->with('success', 'Visitor data deleted successfully.');
    }

    public function accommodations()
    {
        $visitors = Visitor::orderBy('created_at', 'desc')->limit(100)->get();
        $accommodations = Accommodation::orderBy('created_at', 'desc')->with('visitor')->get();

        return view('accommodations', compact('visitors', 'accommodations'));
    }

    public function storeAccommodation(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
            'num_nights' => 'required',
            'rooms' => 'required|array',
            'fees' => 'required|array',
            'payment_status' => 'required',
            'total_payment' => 'required',
        ]);

        $checked = $request->input('checked');
        $rooms = $request->input('rooms');
        $fees = $request->input('fees');

        $filteredRooms = [];
        $filteredFees = [];

        foreach ($rooms as $index => $room) {
            if (in_array($room, $checked)) {
                $filteredRooms[] = $room;
                $filteredFees[] = $fees[$index] ?? 0;
            }
        }

        // Check if at least one room was selected
        if (empty($filteredRooms)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please select at least one room.');
        }

        Accommodation::create([
            'visitor_id' => $request->visitor_id,
            'num_nights' => $request->num_nights,
            'room' => json_encode($filteredRooms),
            'fee' => json_encode($filteredFees),
            'payment_status' => $request->payment_status ?? 'pending',
            'total_payment' => $request->total_payment,
        ]);

        return redirect()->route('accommodations')->with('success', 'Overnight Accommodation added successfully.');
    }

    public function updateAccommodation(Request $request)
    {
        $request->validate([
            'accommodation_id' => 'required|exists:accommodations,id',
            'edit_num_nights' => 'required',
            'edit_rooms' => 'required|array',
            'edit_fees' => 'required|array',
            'edit_payment_status' => 'required',
            'total_payment' => 'required',
        ]);

        $checked = $request->input('checked', []);
        $rooms = $request->input('edit_rooms');
        $fees = $request->input('edit_fees');

        $filteredRooms = [];
        $filteredFees = [];

        foreach ($rooms as $index => $room) {
            if (in_array($room, $checked)) {
                $filteredRooms[] = $room;
                $filteredFees[] = $fees[$index] ?? 0;
            }
        }

        $accommodation = Accommodation::findOrFail($request->accommodation_id);
        $accommodation->update([
            'num_nights' => $request->edit_num_nights,
            'room' => json_encode($filteredRooms),
            'fee' => json_encode($filteredFees),
            'payment_status' => $request->edit_payment_status,
            'total_payment' => $request->total_payment,
        ]);

        return redirect()->route('accommodations')->with('success', 'Overnight Accommodation record updated successfully.');
    }

    public function destroyAccommodation($id)
    {
        $accommodation = Accommodation::findOrFail($id);
        $accommodation->delete();
        return redirect()->route('accommodations')->with('success', 'Overnight Accommodation record deleted successfully.');
    }

    public function cottages()
    {
        $visitors = Visitor::orderBy('created_at', 'desc')->limit(100)->get();
        $cottages = Cottage::orderBy('created_at', 'desc')->with('visitor')->get();

        return view('cottages', compact('visitors', 'cottages'));
    }

    public function storeCottage(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
            'cottage_area' => 'required',
            'cottage_type' => 'required|array',
            'quantity' => 'required|array',
            'fees' => 'required|array',
            'total_payment' => 'required',
        ]);

        $types = $request->input('cottage_type');
        $quantities = $request->input('quantity');
        $fees = $request->input('fees');

        $finalCottages = [];
        $finalQuantities = [];
        $finalFees = [];

        foreach ($types as $index => $type) {
            // If quantity is empty or not set, set it to 0
            $qty = isset($quantities[$index]) && is_numeric($quantities[$index])
                ? (int) $quantities[$index]
                : 0;

            // If fee is empty or not set, set it to 0.0
            $fee = isset($fees[$index]) && is_numeric($fees[$index])
                ? (float) $fees[$index]
                : 0.0;

            $finalCottages[] = $type;
            $finalQuantities[] = $qty;
            $finalFees[] = $fee;
        }

        Cottage::create([
            'visitor_id' => $request->visitor_id,
            'cottage_area' => $request->cottage_area,
            'cottage_type' => json_encode($finalCottages),
            'quantity' => json_encode($finalQuantities),
            'fee' => json_encode($finalFees),
            'total_payment' => $request->total_payment,
        ]);

        return redirect()->route('cottages')->with('success', 'Cottage Rental added successfully.');
    }

    public function updateCottage(Request $request)
    {
        $request->validate([
            'cottage_id' => 'required|exists:cottages,id',
            'edit_cottage_area' => 'required|string',
            'edit_cottage_types' => 'required|array',
            'quantity' => 'required|array',
            'cottage_fees' => 'required|array',
            'total_payment' => 'required|numeric',
        ]);

        $cottage = Cottage::findOrFail($request->cottage_id);

        $types = $request->input('edit_cottage_types');
        $quantities = $request->input('quantity');
        $fees = $request->input('cottage_fees');

        $finalCottages = [];
        $finalQuantities = [];
        $finalFees = [];

        foreach ($types as $index => $type) {
            $qty = isset($quantities[$index]) && is_numeric($quantities[$index]) ? (int) $quantities[$index] : 0;
            $fee = isset($fees[$index]) && is_numeric($fees[$index]) ? (float) $fees[$index] : 0.0;

            $finalCottages[] = $type;
            $finalQuantities[] = $qty;
            $finalFees[] = $fee;
        }

        $cottage->update([
            'cottage_area' => $request->input('edit_cottage_area'),
            'cottage_type' => json_encode($finalCottages),
            'quantity' => json_encode($finalQuantities),
            'fee' => json_encode($finalFees),
            'total_payment' => $request->input('total_payment'),
        ]);

        return redirect()->route('cottages')->with('success', 'Cottage Rental updated successfully.');
    }

    public function destroyCottage($id)
    {
        $cottage = Cottage::findOrFail($id);
        $cottage->delete();
        return redirect()->route('cottages')->with('success', 'Cottage Rental deleted successfully.');
    }

    public function meals()
    {
        $visitors = Visitor::orderBy('created_at', 'desc')->limit(100)->get();
        $meals = Meal::orderBy('created_at', 'desc')->with('visitor')->get();

        return view('meals', compact('visitors', 'meals'));
    }

    public function storeMeal(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
            'meal_items' => 'required|array',
            'meal_items.*.name' => 'required|string',
            'meal_items.*.price' => 'nullable|numeric',
            'meal_items.*.quantity' => 'nullable|integer|min:0',
            'meal_items.*.subtotal' => 'nullable|numeric',
            'total_payment' => 'required|numeric',
            'payment_status' => 'nullable',
        ]);

        $itemNames = [];
        $quantities = [];
        $fees = [];

        foreach ($request->meal_items as $item) {
            $name = isset($item['name']) ? $item['name'] : '';
            $qty  = isset($item['quantity']) && is_numeric($item['quantity']) ? (int) $item['quantity'] : 0;
            $fee  = isset($item['price']) && is_numeric($item['price']) ? (float) $item['price'] : 0.00;

            $itemNames[] = $name;
            $quantities[] = $qty;
            $fees[] = $fee;
        }

        Meal::create([
            'visitor_id'    => $request->visitor_id,
            'item_name'     => json_encode($itemNames),
            'fee'           => json_encode($fees),
            'quantity'      => json_encode($quantities),
            'total_payment' => $request->total_payment,
            'payment_status' => $request->payment_status ?? 'pending',
        ]);

        return redirect()->route('meals')->with('success', 'Meal(s) added successfully.');
    }

    public function updateMeal(Request $request)
    {
        $request->validate([
            'meal_id' => 'required|exists:meals,id',
            'visitor_id' => 'required|exists:visitors,id',
            'meal_items' => 'required|array',
            'meal_items.*.name' => 'required|string',
            'meal_items.*.price' => 'required|numeric',
            'meal_items.*.quantity' => 'required|integer|min:0',
            'total_payment' => 'required|numeric',
            'payment_status' => 'nullable',
        ]);

        $meal = Meal::findOrFail($request->meal_id);

        $items = $request->input('meal_items');

        $itemNames = [];
        $prices = [];
        $quantities = [];
        $subtotals = [];

        foreach ($items as $item) {
            if ($item['quantity'] > 0) {
                $itemNames[] = $item['name'];
                $prices[] = (float) $item['price'];
                $quantities[] = (int) $item['quantity'];
                $subtotals[] = (float) $item['price'] * (int) $item['quantity'];
            }
        }

        $meal->update([
            'visitor_id' => $request->input('visitor_id'),
            'item_name' => json_encode($itemNames),
            'fee' => json_encode($prices),
            'quantity' => json_encode($quantities),
            'subtotal' => json_encode($subtotals),
            'total_payment' => $request->input('total_payment'),
            'payment_status' => $request->payment_status ?? 'pending',
        ]);

        return redirect()->route('meals')->with('success', 'Meal record updated successfully.');
    }

    public function destroyMeal($id)
    {
        $meal = Meal::findOrFail($id);
        $meal->delete();
        return redirect()->route('meals')->with('success', 'Meal(s) record deleted successfully.');
    }

    public function beverages()
    {
        $visitors = Visitor::orderBy('created_at', 'desc')->limit(100)->get();
        $beverages = Beverage::orderBy('created_at', 'desc')->with('visitor')->get();

        return view('beverages', compact('visitors', 'beverages'));
    }

    public function storeBeverage(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
            'beverage_items' => 'required|array',
            'beverage_items.*.name' => 'required|string',
            'beverage_items.*.price' => 'nullable|numeric',
            'beverage_items.*.quantity' => 'nullable|integer|min:0',
            'beverage_items.*.subtotal' => 'nullable|numeric',
            'total_payment' => 'required|numeric',
            'payment_status' => 'nullable',
        ]);

        $itemNames = [];
        $quantities = [];
        $fees = [];

        foreach ($request->beverage_items as $item) {
            $name = isset($item['name']) ? $item['name'] : '';
            $qty  = isset($item['quantity']) && is_numeric($item['quantity']) ? (int) $item['quantity'] : 0;
            $fee  = isset($item['price']) && is_numeric($item['price']) ? (float) $item['price'] : 0.00;

            $itemNames[] = $name;
            $quantities[] = $qty;
            $fees[] = $fee;
        }

        Beverage::create([
            'visitor_id'    => $request->visitor_id,
            'item_name'     => json_encode($itemNames),
            'fee'           => json_encode($fees),
            'quantity'      => json_encode($quantities),
            'total_payment' => $request->total_payment,
            'payment_status' => $request->payment_status ?? 'pending',
        ]);

        return redirect()->route('beverages')->with('success', 'Beverage(s) added successfully.');
    }

    public function updateBeverage(Request $request)
    {
        $request->validate([
            'beverage_id' => 'required|exists:beverages,id',
            'visitor_id' => 'required|exists:visitors,id',
            'beverage_items' => 'required|array',
            'beverage_items.*.name' => 'required|string',
            'beverage_items.*.price' => 'required|numeric',
            'beverage_items.*.quantity' => 'required|integer|min:0',
            'total_payment' => 'required|numeric',
            'payment_status' => 'nullable',
        ]);

        $beverage = Beverage::findOrFail($request->beverage_id);

        $items = $request->input('beverage_items');

        $itemNames = [];
        $prices = [];
        $quantities = [];
        $subtotals = [];

        foreach ($items as $item) {
            if ($item['quantity'] > 0) {
                $itemNames[] = $item['name'];
                $prices[] = (float) $item['price'];
                $quantities[] = (int) $item['quantity'];
                $subtotals[] = (float) $item['price'] * (int) $item['quantity'];
            }
        }

        $beverage->update([
            'visitor_id' => $request->input('visitor_id'),
            'item_name' => json_encode($itemNames),
            'fee' => json_encode($prices),
            'quantity' => json_encode($quantities),
            'subtotal' => json_encode($subtotals),
            'total_payment' => $request->input('total_payment'),
            'payment_status' => $request->payment_status ?? 'pending',
        ]);

        return redirect()->route('beverages')->with('success', 'Beverage record updated successfully.');
    }

    public function destroyBeverage($id)
    {
        $beverage = Beverage::findOrFail($id);
        $beverage->delete();
        return redirect()->route('beverages')->with('success', 'Beverage(s) record deleted successfully.');
    }

    public function kawabaths()
    {   
        $kawaHotBathFees = Service::where('service_type', 'kawa_hot_bath')->get();
        $picnicTableFees = Service::where('service_type', 'picnic_table')->get();

        $visitors = Visitor::orderBy('created_at', 'desc')->limit(100)->get();
        $kawaBaths = KawaBath::orderBy('created_at', 'desc')->with('visitor')->get();

        return view('kawa_baths', compact('visitors', 'kawaBaths', 'kawaHotBathFees', 'picnicTableFees'));
    }

    public function storeKawaBath(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
            'category' => 'required|array',
            'members' => 'required|array',
            'age' => 'nullable|array',
            'fee' => 'nullable|array',
            'total_payment' => 'required',
            'payment_status' => 'nullable',
        ]);

        $categories = $request->input('category');
        $members = $request->input('members');
        $ages = $request->input('age', []);
        $fees = $request->input('fee', []);

        $filteredCategories = [];
        $filteredMembers = [];
        $filteredAges = [];
        $filteredFees = [];

        $count = count($members);

        for ($i = 0; $i < $count; $i++) {
            $filteredCategories[] = isset($categories[$i]) && $categories[$i] !== null ? $categories[$i] : 'null';
            $filteredMembers[] = isset($members[$i]) && $members[$i] !== null ? $members[$i] : 'null';
            $filteredAges[] = isset($ages[$i]) && $ages[$i] !== null ? $ages[$i] : 'null';
            $filteredFees[] = isset($fees[$i]) && $fees[$i] !== null ? $fees[$i] : 'null';
        }

        KawaBath::create([
            'visitor_id' => $request->visitor_id,
            'category' => json_encode($filteredCategories),
            'members' => json_encode($filteredMembers),
            'age' => json_encode($filteredAges),
            'fee' => json_encode($filteredFees),
            'total_payment' => $request->total_payment,
            'payment_status' => $request->payment_status ?? 'pending',
        ]);

        return redirect()->route('kawabaths')->with('success', 'Kawa Hot Bath fee added successfully.');
    }

    public function updateKawaBath(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
            'category' => 'required|array',
            'members' => 'required|array',
            'age' => 'nullable|array',
            'fee' => 'nullable|array',
            'total_payment' => 'required',
            'payment_status' => 'nullable',
            'kawabath_id' => 'required|exists:kawa_baths,id',
        ]);

        $categories = $request->input('category');
        $members = $request->input('members');
        $ages = $request->input('age', []);
        $fees = $request->input('fee', []);

        $filteredCategories = [];
        $filteredMembers = [];
        $filteredAges = [];
        $filteredFees = [];

        $count = count($members);

        for ($i = 0; $i < $count; $i++) {
            $filteredCategories[] = isset($categories[$i]) && $categories[$i] !== null ? $categories[$i] : 'null';
            $filteredMembers[] = isset($members[$i]) && $members[$i] !== null ? $members[$i] : 'null';
            $filteredAges[] = isset($ages[$i]) && $ages[$i] !== null ? $ages[$i] : 'null';
            $filteredFees[] = isset($fees[$i]) && $fees[$i] !== null ? $fees[$i] : 'null';
        }

        $kawabath = KawaBath::findOrFail($request->kawabath_id);
        $kawabath->update([
            'visitor_id' => $request->visitor_id,
            'category' => json_encode($filteredCategories),
            'members' => json_encode($filteredMembers),
            'age' => json_encode($filteredAges),
            'fee' => json_encode($filteredFees),
            'total_payment' => $request->total_payment,
            'payment_status' => $request->payment_status ?? 'pending',
        ]);

        return redirect()->route('kawabaths')->with('success', 'Kawa Hot Bath record updated successfully.');
    }

    public function destroyKawaBath($id)
    {
        $kawaBath = KawaBath::findOrFail($id);
        $kawaBath->delete();
        return redirect()->route('kawabaths')->with('success', 'Kawa Hot Bath record deleted successfully.');
    }

    public function watertubings(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $letter = $request->letter;

        $waterTubingFees = Service::where('service_type', 'water_tubing')->get();

        $visitors = Visitor::orderBy('created_at', 'desc')->limit(100)->get();
        $waterTubings = WaterTubing::with('visitor')
            ->when($start_date, function ($query) use ($start_date) {
                $query->whereDate('created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                $query->whereDate('created_at', '<=', $end_date);
            })
            ->when($letter, function ($query) use ($letter) {
                $query->whereHas('visitor', function ($q) use ($letter) {
                    $q->where('first_name', 'like', $letter . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('water_tubings', compact('visitors', 'waterTubings', 'waterTubingFees'));
    }

    public function storeWaterTubing(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
            'members' => 'required|array',
            'total_payment' => 'required|numeric',
            'payment_status' => 'required',
        ]);

        $visitorId = $request->visitor_id;
        $membersInput = $request->input('members');

        $services = Service::where('service_type', 'water_tubing')->get();

        $visitor = Visitor::with('companions')->findOrFail($visitorId);

        $guests = collect([
            (object)[
                'name' => trim($visitor->first_name . ' ' . $visitor->middle_name . ' ' . $visitor->last_name),
                'age' => $visitor->age,
                'is_main' => true,
            ]
        ])->merge(
            $visitor->companions->map(function ($companion) {
                return (object)[
                    'name' => $companion->name,
                    'age' => $companion->age,
                    'is_main' => false,
                ];
            })
        );
        $structured = [];

        foreach ($guests as $gIndex => $guest) {
            $serviceRows = [];
            foreach ($services as $sIndex => $service) {
                $qty = $membersInput[$gIndex][$sIndex] ?? 0;
                if ($qty <= 0) continue;
                $serviceRows[] = [
                    'service_name' => $service->service_name,
                    'fee' => (float) $service->fee,
                    'qty' => (int) $qty,
                    'subtotal' => (float) $qty * $service->fee,
                ];
            }

            if (empty($serviceRows)) continue;

            $structured[] = [
                'guest' => $guest->name,
                'age' => $guest->age,
                'services' => $serviceRows,
                'is_main' => $guest->is_main,
            ];
        }

        WaterTubing::create([
            'visitor_id' => $visitorId,
            'members' => json_encode($structured),
            'total_payment' => $request->total_payment,
            'payment_status' => $request->payment_status,
        ]);

        return redirect()->route('watertubings')
            ->with('success', 'Water Tubing fee added successfully.');
    }

    public function updateWaterTubing(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
            'members' => 'required|array',
            'total_payment' => 'required|numeric',
            'payment_status' => 'required',
            'water_tubing_id' => 'required|exists:water_tubings,id',
        ]);

        $visitorId = $request->visitor_id;
        $membersInput = $request->members;

        $services = Service::where('service_type', 'water_tubing')->get();
        $visitor = Visitor::with('companions')->findOrFail($visitorId);

        $guests = collect([
            (object)[
                'name' => trim($visitor->first_name . ' ' . $visitor->middle_name . ' ' . $visitor->last_name),
                'age' => $visitor->age,
                'is_main' => true,
            ]
        ])->merge(
            $visitor->companions->map(function ($c) {
                return (object)[
                    'name' => $c->name,
                    'age' => $c->age,
                    'is_main' => false,
                ];
            })
        )->values();

        $structured = [];
        foreach ($guests as $gIndex => $guest) {
            $serviceRows = [];
            foreach ($services as $sIndex => $service) {
                $qty = $membersInput[$gIndex][$sIndex] ?? 0;

                if ($qty <= 0) continue;

                $serviceRows[] = [
                    'service_name' => $service->service_name,
                    'fee' => (float) $service->fee,
                    'qty' => (int) $qty,
                    'subtotal' => (float) $qty * $service->fee,
                ];
            }

            if (!empty($serviceRows)) {
                $structured[] = [
                    'guest' => $guest->name,
                    'age' => $guest->age,
                    'is_main' => $guest->is_main,
                    'services' => $serviceRows,
                ];
            }
        }

        WaterTubing::findOrFail($request->water_tubing_id)->update([
            'visitor_id' => $visitorId,
            'members' => json_encode($structured),
            'total_payment' => $request->total_payment,
            'payment_status' => $request->payment_status,
        ]);

        return redirect()->route('watertubings')
            ->with('success', 'Water Tubing record updated successfully.');
    }

    public function destroyWaterTubing($id)
    {
        $waterTubing = WaterTubing::findOrFail($id);
        $waterTubing->delete();
        return redirect()->route('watertubings')->with('success', 'Water Tubing record deleted successfully.');
    }

    public function picnictables()
    {
        $visitors = Visitor::orderBy('created_at', 'desc')->limit(100)->get();
        $picnicTables = PicnicTable::orderBy('created_at', 'desc')->with('visitor')->get();

        return view('picnic_tables', compact('visitors', 'picnicTables'));
    }

    public function storePicnicTable(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
            'quantity' => 'required',
            'fee' => 'required',
            'total_payment' => 'required',
            'payment_status' => 'nullable',
        ]);

        PicnicTable::create([
            'visitor_id' => $request->visitor_id,
            'quantity' => $request->quantity,
            'fee' => $request->fee,
            'total_payment' => $request->total_payment,
            'payment_status' => $request->payment_status ?? 'pending',
        ]);

        return redirect()->route('picnictables')->with('success', 'Picnic Table fee added successfully.');
    }

    public function updatePicnicTable(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
            'quantity' => 'required',
            'fee' => 'required',
            'total_payment' => 'required',
            'payment_status' => 'nullable',
            'picnic_table_id' => 'required|exists:picnic_tables,id',
        ]);

        $picnictable = PicnicTable::findOrFail($request->picnic_table_id);
        $picnictable->update([
            'visitor_id' => $request->visitor_id,
            'quantity' => $request->quantity,
            'fee' => $request->fee,
            'total_payment' => $request->total_payment,
            'payment_status' => $request->payment_status ?? 'pending',
        ]);

        return redirect()->route('picnictables')->with('success', 'Picnic Table record updated successfully.');
    }

    public function destroyPicnicTable($id)
    {
        $picnictable = PicnicTable::findOrFail($id);
        $picnictable->delete();
        return redirect()->route('picnictables')->with('success', 'Picnic Table record deleted successfully.');
    }

    public function massages()
    {
        $visitors = Visitor::orderBy('created_at', 'desc')->limit(100)->get();
        $massages = Massage::orderBy('created_at', 'desc')->with('visitor')->get();

        return view('massages', compact('visitors', 'massages'));
    }

    public function storeMassage(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
            'category' => 'required|array',
            'members' => 'required|array',
            'age' => 'nullable|array',
            'fee' => 'nullable|array',
            'total_payment' => 'required',
            'no_of_hours' => 'required',
            'payment_status' => 'nullable',
        ]);

        $categories = $request->input('category');
        $members = $request->input('members');
        $ages = $request->input('age', []);
        $fees = $request->input('fee', []);

        $filteredCategories = [];
        $filteredMembers = [];
        $filteredAges = [];
        $filteredFees = [];

        $count = count($members);

        for ($i = 0; $i < $count; $i++) {
            $filteredCategories[] = isset($categories[$i]) && $categories[$i] !== null ? $categories[$i] : 'null';
            $filteredMembers[] = isset($members[$i]) && $members[$i] !== null ? $members[$i] : 'null';
            $filteredAges[] = isset($ages[$i]) && $ages[$i] !== null ? $ages[$i] : 'null';
            $filteredFees[] = isset($fees[$i]) && $fees[$i] !== null ? $fees[$i] : 'null';
        }

        Massage::create([
            'visitor_id' => $request->visitor_id,
            'category' => json_encode($filteredCategories),
            'members' => json_encode($filteredMembers),
            'age' => json_encode($filteredAges),
            'fee' => json_encode($filteredFees),
            'total_payment' => $request->total_payment,
            'no_of_hours' => $request->no_of_hours,
            'payment_status' => $request->payment_status ?? 'pending',
        ]);

        return redirect()->route('massages')->with('success', 'Massage fee added successfully.');
    }

    public function updateMassage(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
            'category' => 'required|array',
            'members' => 'required|array',
            'age' => 'nullable|array',
            'fee' => 'nullable|array',
            'total_payment' => 'required',
            'payment_status' => 'nullable',
            'no_of_hours' => 'required',
            'massage_id' => 'required|exists:massages,id',
        ]);

        $categories = $request->input('category');
        $members = $request->input('members');
        $ages = $request->input('age', []);
        $fees = $request->input('fee', []);

        $filteredCategories = [];
        $filteredMembers = [];
        $filteredAges = [];
        $filteredFees = [];

        $count = count($members);

        for ($i = 0; $i < $count; $i++) {
            $filteredCategories[] = isset($categories[$i]) && $categories[$i] !== null ? $categories[$i] : 'null';
            $filteredMembers[] = isset($members[$i]) && $members[$i] !== null ? $members[$i] : 'null';
            $filteredAges[] = isset($ages[$i]) && $ages[$i] !== null ? $ages[$i] : 'null';
            $filteredFees[] = isset($fees[$i]) && $fees[$i] !== null ? $fees[$i] : 'null';
        }

        $massage = Massage::findOrFail($request->massage_id);
        $massage->update([
            'visitor_id' => $request->visitor_id,
            'category' => json_encode($filteredCategories),
            'members' => json_encode($filteredMembers),
            'age' => json_encode($filteredAges),
            'fee' => json_encode($filteredFees),
            'total_payment' => $request->total_payment,
            'no_of_hours' => $request->no_of_hours,
            'payment_status' => $request->payment_status ?? 'pending',
        ]);

        return redirect()->route('massages')->with('success', 'Massage record updated successfully.');
    }

    public function destroyMassage($id)
    {
        $massages = Massage::findOrFail($id);
        $massages->delete();
        return redirect()->route('massages')->with('success', 'Massage record deleted successfully.');
    }
}
