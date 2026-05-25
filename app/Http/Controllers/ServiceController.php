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
    public function index(Request $request)
    {
        $filter = $request->filter;

        $services = Service::when(
            $filter && $filter !== 'all',
            fn($query) => $query->where('service_type', $filter)
        )
            ->orderBy('created_at', 'desc')
            ->get();

        return view('services_setting', compact('services', 'filter'));
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
            'is_pwd' => $request->guest_is_pwd ? 1 : 0,
            'address' => $request->guest_address,
            'date_visit' => $request->date_visit,
            'members' => $request->guest_members,
        ]);

        $entrance = Entrance::create([
            'visitor_id' => $visitor->id,
            'status' => $request->payment_status ?? 'Unpaid',
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
            'is_pwd' => $request->edit_guest_is_pwd ? 1 : 0,
            'address' => $request->edit_guest_address,
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

    public function accommodations(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $letter = $request->letter;

        $massageFees = Service::where('service_type', 'massage')->get();
        $accommodationFees = Service::where('service_type', 'accommodation')->get();

        $visitors = Visitor::orderBy('created_at', 'desc')->limit(100)->get();
        $accommodations = Accommodation::with('visitor')
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

        return view('accommodations', compact('visitors', 'accommodations', 'accommodationFees', 'massageFees'));
    }

    public function updateAccommodation(Request $request)
    {
        $request->validate([
            'accommodation_id' => 'required|exists:accommodations,id',
            'visitor_id' => 'required|exists:visitors,id',
            'rooms' => 'nullable|array',
            'nights' => 'nullable|array',
            'fees' => 'nullable|array',
            'accommodation_total_payment' => 'required|numeric',
            'accommodation_payment_status' => 'required',
        ]);

        $rooms = $request->rooms ?? [];
        $nights = $request->nights ?? [];
        $fees = $request->fees ?? [];

        $structuredAccommodation = [];

        foreach ($rooms as $i => $room) {

            $night = (int) ($nights[$i] ?? 0);
            $fee = (float) ($fees[$i] ?? 0);

            if ($night <= 0) continue;

            $structuredAccommodation[] = [
                'room' => $room,
                'fee' => $fee,
                'nights' => $night,
                'subtotal' => $night * $fee,
            ];
        }

        $accommodation = Accommodation::findOrFail($request->accommodation_id);

        $accommodation->update([
            'visitor_id' => $request->visitor_id,
            'room' => json_encode($structuredAccommodation),
            'fee' => 0,
            'num_nights' => 0,
            'total_payment' => $request->accommodation_total_payment,
            'payment_status' => $request->accommodation_payment_status,
        ]);

        return redirect()->route('accommodations')
            ->with('success', 'Accommodation record updated successfully.');
    }

    public function destroyAccommodation($id)
    {
        $accommodation = Accommodation::findOrFail($id);
        $accommodation->delete();
        return redirect()->route('accommodations')->with('success', 'Accommodation record deleted successfully.');
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

    public function meals(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $letter = $request->letter;

        $foodFees = Service::where('service_type', 'foods')->get();
        $drinkFees = Service::where('service_type', 'drinks')->get();

        $visitors = Visitor::orderBy('created_at', 'desc')->limit(100)->get();
        $meals = Meal::with('visitor')
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

        return view('meals', compact('visitors', 'meals', 'foodFees', 'drinkFees'));
    }

    public function storeMealBeverage(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',

            'meal_items' => 'nullable|array',
            'beverage_items' => 'nullable|array',

            'food_total_payment' => 'required|numeric',
            'drink_total_payment' => 'required|numeric',

            'drink_payment_status' => 'nullable',
            'food_payment_status' => 'nullable',
        ]);

        /*
    |--------------------------------------------------------------------------
    | MEALS
    |--------------------------------------------------------------------------
    */
        $mealNames = [];
        $mealQty = [];
        $mealFees = [];
        $hasMealItems = false;

        if ($request->meal_items) {
            foreach ($request->meal_items as $category => $items) {
                foreach ($items as $item) {

                    $qty = (int) ($item['qty'] ?? 0);

                    if ($qty <= 0) continue;

                    $hasMealItems = true;

                    $mealNames[] = $item['name'] ?? '';
                    $mealQty[]   = $qty;
                    $mealFees[]  = (float) ($item['fee'] ?? 0);
                }
            }

            if ($hasMealItems) {
                Meal::create([
                    'visitor_id'     => $request->visitor_id,
                    'item_name'      => json_encode($mealNames),
                    'fee'            => json_encode($mealFees),
                    'quantity'       => json_encode($mealQty),
                    'total_payment'  => $request->food_total_payment ?? 0,
                    'payment_status' => $request->food_payment_status ?? 'Unpaid',
                ]);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | BEVERAGES
    |--------------------------------------------------------------------------
    */
        $bevNames = [];
        $bevQty = [];
        $bevFees = [];
        $hasBevItems = false;

        if ($request->beverage_items) {
            foreach ($request->beverage_items as $item) {

                $qty = (int) ($item['qty'] ?? 0);

                if ($qty <= 0) continue;

                $hasBevItems = true;

                $bevNames[] = $item['name'] ?? '';
                $bevQty[]   = $qty;
                $bevFees[]  = (float) ($item['fee'] ?? 0);
            }

            if ($hasBevItems) {
                Beverage::create([
                    'visitor_id'     => $request->visitor_id,
                    'item_name'      => json_encode($bevNames),
                    'fee'            => json_encode($bevFees),
                    'quantity'       => json_encode($bevQty),
                    'total_payment'  => $request->drink_total_payment ?? 0,
                    'payment_status' => $request->drink_payment_status ?? 'Unpaid',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Food and Drink saved successfully.');
    }

    public function updateMeal(Request $request)
    {
        $request->validate([
            'meal_id' => 'required|exists:meals,id',
            'visitor_id' => 'required|exists:visitors,id',

            'meal_items' => 'nullable|array',

            'food_total_payment' => 'required|numeric',
            'food_payment_status' => 'nullable',
        ]);

        $meal = Meal::findOrFail($request->meal_id);

        $names = [];
        $fees = [];
        $qtys = [];

        if ($request->meal_items) {
            foreach ($request->meal_items as $category => $items) {
                foreach ($items as $item) {

                    $qty = (int) ($item['qty'] ?? 0);
                    if ($qty <= 0) continue;

                    $names[] = $item['name'] ?? '';
                    $fees[]  = (float) ($item['fee'] ?? 0);
                    $qtys[]  = $qty;
                }
            }
        }

        $meal->update([
            'visitor_id'     => $request->visitor_id,
            'item_name'      => json_encode($names),
            'fee'            => json_encode($fees),
            'quantity'       => json_encode($qtys),
            'total_payment'  => $request->food_total_payment ?? 0,
            'payment_status' => $request->food_payment_status ?? 'Unpaid',
        ]);

        return redirect()->back()->with('success', 'Food record updated successfully.');
    }

    public function destroyMeal($id)
    {
        $meal = Meal::findOrFail($id);
        $meal->delete();
        return redirect()->route('meals')->with('success', 'Food record deleted successfully.');
    }

    public function beverages(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $letter = $request->letter;

        $foodFees = Service::where('service_type', 'foods')->get();
        $drinkFees = Service::where('service_type', 'drinks')->get();

        $visitors = Visitor::orderBy('created_at', 'desc')->limit(100)->get();
        $beverages = Beverage::with('visitor')
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

        return view('beverages', compact('visitors', 'beverages', 'foodFees', 'drinkFees'));
    }

    public function updateBeverage(Request $request)
    {
        $request->validate([
            'beverage_id' => 'required|exists:beverages,id',
            'visitor_id' => 'required|exists:visitors,id',

            'beverage_items' => 'nullable|array',

            'drink_total_payment' => 'required|numeric',
            'drink_payment_status' => 'nullable',
        ]);

        $beverage = Beverage::findOrFail($request->beverage_id);

        $bevNames = [];
        $bevQty   = [];
        $bevFees  = [];
        $hasBevItems = false;

        if ($request->beverage_items) {

            foreach ($request->beverage_items as $item) {

                $qty = (int) ($item['qty'] ?? 0);

                if ($qty <= 0) continue;

                $hasBevItems = true;

                $bevNames[] = $item['name'] ?? '';
                $bevQty[]   = $qty;
                $bevFees[]  = (float) ($item['fee'] ?? 0);
            }
        }

        $beverage->update([
            'visitor_id'     => $request->visitor_id,
            'item_name'      => json_encode($bevNames),
            'fee'            => json_encode($bevFees),
            'quantity'       => json_encode($bevQty),
            'total_payment'  => $request->drink_total_payment ?? 0,
            'payment_status' => $request->drink_payment_status ?? 'Unpaid',
        ]);

        return redirect()->back()->with('success', 'Drink record updated successfully.');
    }

    public function destroyBeverage($id)
    {
        $beverage = Beverage::findOrFail($id);
        $beverage->delete();
        return redirect()->route('beverages')->with('success', 'Drink record deleted successfully.');
    }

    public function kawabaths(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $letter = $request->letter;

        $kawaHotBathFees = Service::where('service_type', 'kawa_hot_bath')->get();
        $picnicTableFees = Service::where('service_type', 'picnic_table')->get();

        $visitors = Visitor::orderBy('created_at', 'desc')->limit(100)->get();
        $kawaBaths = KawaBath::with('visitor')
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

        return view('kawa_baths', compact('visitors', 'kawaBaths', 'kawaHotBathFees', 'picnicTableFees'));
    }

    public function storeKawaPicnic(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',

            'members' => 'nullable|array',
            'kawabath_total_payment' => 'nullable|numeric',
            'kawabath_payment_status' => 'nullable',
            'picnic_table_quantity' => 'nullable|array',
            'picnic_table_services' => 'nullable|array',
            'picnic_table_name' => 'nullable|array',
            'picnictable_total_payment' => 'nullable|numeric',
            'picnictable_payment_status' => 'nullable',
        ]);

        $visitorId = $request->visitor_id;

        // =====================================
        // FETCH VISITOR + GUESTS (SAME AS YOUR WATER TUBING)
        // =====================================
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

        // =====================================
        // KAWA BATH SAVE (LIKE WATER TUBING)
        // =====================================
        $kawaServices = Service::where('service_type', 'kawa_hot_bath')->get();
        $membersInput = $request->input('members', []);

        $structuredKawa = [];

        foreach ($guests as $gIndex => $guest) {

            $serviceRows = [];

            foreach ($kawaServices as $sIndex => $service) {

                $qty = isset($membersInput[$gIndex]['services'][$sIndex]['qty'])
                    ? (int) $membersInput[$gIndex]['services'][$sIndex]['qty']
                    : 0;

                $fee = (float) $service->fee;

                if ($qty <= 0) {
                    continue;
                }

                $serviceRows[] = [
                    'service_name' => $service->service_name,
                    'fee' => $fee,
                    'qty' => $qty,
                    'subtotal' => $qty * $fee,
                ];
            }

            if (empty($serviceRows)) {
                continue;
            }

            $structuredKawa[] = [
                'guest' => $guest->name,
                'age' => $guest->age,
                'is_main' => $guest->is_main,
                'services' => $serviceRows,
            ];
        }

        if (!empty($structuredKawa)) {
            KawaBath::create([
                'visitor_id' => $visitorId,
                'members' => json_encode($structuredKawa),
                'total_payment' => $request->kawabath_total_payment,
                'payment_status' => $request->kawabath_payment_status ?? 'Unpaid',
            ]);
        }

        $names = $request->picnic_table_services ?? [];
        $fees = $request->picnic_table_fees ?? [];
        $qtys = $request->picnic_table_quantity ?? [];

        $structuredPicnic = [];

        foreach ($names as $i => $name) {

            $qty = (int) ($qtys[$i] ?? 0);
            $fee = (float) ($fees[$i] ?? 0);

            if ($qty <= 0) continue;

            $structuredPicnic[] = [
                'service_name' => $name,
                'fee' => $fee,
                'qty' => $qty,
                'subtotal' => $qty * $fee,
            ];
        }

        if (!empty($structuredPicnic)) {
            PicnicTable::create([
                'visitor_id' => $request->visitor_id,
                'details' => json_encode($structuredPicnic),
                'total_payment' => $request->picnictable_total_payment ?? 0,
                'payment_status' => $request->picnictable_payment_status ?? 'Unpaid',
            ]);
        }

        return redirect()->back()->with('success', 'Kawa Bath and Picnic Table fee saved successfully.');
    }

    public function updateKawaBath(Request $request)
    {
        $request->validate([
            'kawabath_id' => 'required|exists:kawa_baths,id',
            'visitor_id' => 'required|exists:visitors,id',
            'members' => 'nullable|array',
            'kawabath_total_payment' => 'required|numeric',
            'kawabath_payment_status' => 'required|string',
        ]);

        $kawaBath = KawaBath::findOrFail($request->kawabath_id);

        $kawaServices = Service::where('service_type', 'kawa_hot_bath')->get();

        $visitor = Visitor::with('companions')->findOrFail($request->visitor_id);

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
        );

        $membersInput = $request->members ?? [];
        $structured = [];

        foreach ($guests as $gIndex => $guest) {

            $serviceRows = [];

            foreach ($kawaServices as $sIndex => $service) {

                $qty = (int) data_get($membersInput, "{$gIndex}.services.{$sIndex}.qty", 0);
                $fee = (float) $service->fee;

                if ($qty <= 0) {
                    continue;
                }

                $serviceRows[] = [
                    'service_name' => $service->service_name,
                    'fee' => $fee,
                    'qty' => $qty,
                    'subtotal' => $qty * $fee,
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

        $kawaBath->update([
            'visitor_id' => $request->visitor_id,
            'members' => json_encode($structured),
            'total_payment' => $request->kawabath_total_payment,
            'payment_status' => $request->kawabath_payment_status,
        ]);

        return redirect()->route('kawabaths')
            ->with('success', 'Kawa Hot Bath record updated successfully.');
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

    public function picnictables(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $letter = $request->letter;

        $kawaHotBathFees = Service::where('service_type', 'kawa_hot_bath')->get();
        $picnicTableFees = Service::where('service_type', 'picnic_table')->get();

        $visitors = Visitor::orderBy('created_at', 'desc')->limit(100)->get();
        $picnicTables = PicnicTable::with('visitor')
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

        return view('picnic_tables', compact('visitors', 'picnicTables', 'kawaHotBathFees', 'picnicTableFees'));
    }

    public function updatePicnicTable(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
            'picnic_table_id' => 'required|exists:picnic_tables,id',
            'picnictable_total_payment' => 'required|numeric',
            'picnictable_payment_status' => 'nullable',

            'picnic_table_services' => 'required|array',
            'picnic_table_fees' => 'required|array',
            'picnic_table_quantity' => 'required|array',
        ]);

        $details = [];

        foreach ($request->picnic_table_services as $index => $service) {

            $fee = $request->picnic_table_fees[$index] ?? 0;
            $qty = $request->picnic_table_quantity[$index] ?? 0;

            $details[] = [
                'service_name' => $service,
                'fee' => (float) $fee,
                'qty' => (int) $qty,
                'subtotal' => (float) $fee * (int) $qty,
            ];
        }

        $picnictable = PicnicTable::findOrFail($request->picnic_table_id);

        $picnictable->update([
            'visitor_id' => $request->visitor_id,
            'details' => json_encode($details),
            'total_payment' => $request->picnictable_total_payment,
            'payment_status' => $request->picnictable_payment_status ?? 'Unpaid',
        ]);

        return redirect()
            ->route('picnictables')
            ->with('success', 'Picnic Table record updated successfully.');
    }

    public function destroyPicnicTable($id)
    {
        $picnictable = PicnicTable::findOrFail($id);
        $picnictable->delete();
        return redirect()->route('picnictables')->with('success', 'Picnic Table record deleted successfully.');
    }

    public function massages(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $letter = $request->letter;

        $massageFees = Service::where('service_type', 'massage')->get();
        $accommodationFees = Service::where('service_type', 'accommodation')->get();

        $visitors = Visitor::orderBy('created_at', 'desc')->limit(100)->get();
        $massages = Massage::with('visitor')
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

        return view('massages', compact('visitors', 'massages', 'massageFees', 'accommodationFees'));
    }

    public function storeMassageAccommodation(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',

            // Massage
            'members' => 'nullable|array',
            'massage_total_payment' => 'nullable|numeric',
            'massage_payment_status' => 'nullable',

            // Accommodation
            'rooms' => 'nullable|array',
            'nights' => 'nullable|array',
            'fees' => 'nullable|array',
            'accommodation_total_payment' => 'nullable|numeric',
            'accommodation_payment_status' => 'nullable',
        ]);

        $visitorId = $request->visitor_id;

        /*
        |--------------------------------------------------------------------------
        | FETCH VISITOR + COMPANIONS (SAME AS KAWA)
        |--------------------------------------------------------------------------
        */
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

        /*
    |--------------------------------------------------------------------------
    | MASSAGE (LIKE KAWA BATH)
    |--------------------------------------------------------------------------
    */
        $massageServices = Service::where('service_type', 'massage')->get();
        $membersInput = $request->input('members', []);

        $structuredMassage = [];

        foreach ($guests as $gIndex => $guest) {

            $serviceRows = [];

            foreach ($massageServices as $sIndex => $service) {

                $qty = isset($membersInput[$gIndex]['services'][$sIndex]['qty'])
                    ? (int) $membersInput[$gIndex]['services'][$sIndex]['qty']
                    : 0;

                $fee = (float) $service->fee;

                if ($qty <= 0) continue;

                $serviceRows[] = [
                    'service_name' => $service->service_name,
                    'fee' => $fee,
                    'qty' => $qty,
                    'subtotal' => $qty * $fee,
                ];
            }

            if (empty($serviceRows)) continue;

            $structuredMassage[] = [
                'guest' => $guest->name,
                'age' => $guest->age,
                'is_main' => $guest->is_main,
                'services' => $serviceRows,
            ];
        }

        if (!empty($structuredMassage)) {
            Massage::create([
                'visitor_id' => $visitorId,
                'members' => json_encode($structuredMassage),
                'total_payment' => $request->massage_total_payment ?? 0,
                'payment_status' => $request->massage_payment_status ?? 'Unpaid',
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | ACCOMMODATION (LIKE PICNIC TABLE)
    |--------------------------------------------------------------------------
    */
        $rooms = $request->rooms ?? [];
        $nights = $request->nights ?? [];
        $fees = $request->fees ?? [];

        $structuredAccommodation = [];

        foreach ($rooms as $i => $room) {

            $night = (int) ($nights[$i] ?? 0);
            $fee = (float) ($fees[$i] ?? 0);

            if ($night <= 0) continue;

            $structuredAccommodation[] = [
                'room' => $room,
                'fee' => $fee,
                'nights' => $night,
                'subtotal' => $night * $fee,
            ];
        }

        if (!empty($structuredAccommodation)) {
            Accommodation::create([
                'visitor_id' => $visitorId,
                'room' => json_encode($structuredAccommodation),
                'fee' => 0,
                'num_nights' => 0,
                'total_payment' => $request->accommodation_total_payment ?? 0,
                'payment_status' => $request->accommodation_payment_status ?? 'Unpaid',
            ]);
        }

        return redirect()->back()->with('success', 'Massage and Accommodation saved successfully.');
    }

    public function updateMassage(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,id',
            'members' => 'required|array',
            'total_payment' => 'required|numeric',
            'payment_status' => 'nullable|string',
            'massage_id' => 'required|exists:massages,id',
        ]);

        $members = $request->input('members', []);

        $cleanMembers = [];

        foreach ($members as $guest) {

            if (!isset($guest['services'])) continue;
            $cleanServices = [];
            foreach ($guest['services'] as $service) {

                $qty = (int) ($service['qty'] ?? 0);
                $fee = (float) ($service['fee'] ?? 0);
                if ($qty <= 0) continue;
                $cleanServices[] = [
                    'service_name' => $service['service_name'],
                    'qty' => $qty,
                    'fee' => $fee,
                    'subtotal' => $qty * $fee,
                ];
            }
            if (empty($cleanServices)) continue;

            $cleanMembers[] = [
                'guest' => $guest['guest'] ?? '',
                'age' => $guest['age'] ?? null,
                'is_main' => $guest['is_main'] ?? false,
                'services' => $cleanServices,
            ];
        }

        $massage = Massage::findOrFail($request->massage_id);

        $massage->update([
            'visitor_id' => $request->visitor_id,
            'members' => json_encode($cleanMembers),
            'total_payment' => $request->total_payment,
            'payment_status' => $request->payment_status ?? '',
        ]);

        return redirect()->route('massages')
            ->with('success', 'Massage record updated successfully.');
    }

    public function destroyMassage($id)
    {
        $massages = Massage::findOrFail($id);
        $massages->delete();
        return redirect()->route('massages')->with('success', 'Massage record deleted successfully.');
    }
}
