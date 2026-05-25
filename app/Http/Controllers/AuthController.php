<?php

namespace App\Http\Controllers;

use App\Models\Entrance;
use App\Models\Service;
use App\Models\User;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return redirect('/dashboard');
        }

        return view('welcome');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        if (auth()->attempt($credentials)) {
            return redirect('/dashboard')->with('success', 'Login successful');
        }

        return back()->withErrors(['error' => 'The provided credentials do not match our records.']);
    }

    public function dashboard(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $letter = $request->letter;

        $selectedYear = $request->year ?? Carbon::now()->year;

        $currentYear = Carbon::now()->year;

        /*
    |--------------------------------------------------------------------------
    | ENTRANCES
    |--------------------------------------------------------------------------
    */
        $entrances = Entrance::with('visitor', 'companions')
            ->when($start_date, fn($q) => $q->whereDate('created_at', '>=', $start_date))
            ->when($end_date, fn($q) => $q->whereDate('created_at', '<=', $end_date))
            ->when($letter, function ($q) use ($letter) {
                $q->whereHas(
                    'visitor',
                    fn($sub) =>
                    $sub->where('first_name', 'like', $letter . '%')
                );
            })
            ->orderBy('created_at', 'desc')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | DATE RANGES
    |--------------------------------------------------------------------------
    */
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();

        /*
    |--------------------------------------------------------------------------
    | VISITOR COUNTS (FIXED: members + 1)
    |--------------------------------------------------------------------------
    */
        $visitorsToday = Visitor::whereDate('created_at', $today)
            ->selectRaw('SUM(COALESCE(members,0) + 1) as total')
            ->value('total') ?? 0;

        $visitorsThisWeek = Visitor::where('created_at', '>=', $startOfWeek)
            ->selectRaw('SUM(COALESCE(members,0) + 1) as total')
            ->value('total') ?? 0;

        $visitorsThisMonth = Visitor::where('created_at', '>=', $startOfMonth)
            ->selectRaw('SUM(COALESCE(members,0) + 1) as total')
            ->value('total') ?? 0;

        $visitorsThisYear = Visitor::where('created_at', '>=', $startOfYear)
            ->selectRaw('SUM(COALESCE(members,0) + 1) as total')
            ->value('total') ?? 0;

        /*
        |--------------------------------------------------------------------------
        | VISITORS TODAY
        |--------------------------------------------------------------------------
        */

        $todayVisitors = Visitor::whereDate('date_visit', now())->get();
        /*
        |--------------------------------------------------------------------------
        | VISITORS WITH UNPAID BILLS
        |--------------------------------------------------------------------------
        */
        $visitorsWithUnpaidBills = $todayVisitors->filter(function ($visitor) {

            $services = [
                $visitor->entrance,
                $visitor->accommodation,
                $visitor->meal,
                $visitor->beverage,
                $visitor->kawabath,
                $visitor->watertubing,
                $visitor->massage,
                $visitor->picnictable,
            ];

            foreach ($services as $service) {
                if ($service) {
                    $status = $service->payment_status ?? $service->status ?? 'Unpaid';

                    if ($status !== 'Paid') {
                        return true;
                    }
                }
            }

            return false;
        });

        /*
        |--------------------------------------------------------------------------
        | VISITORS WITH FULLY PAID BILLS
        |--------------------------------------------------------------------------
        */
        $visitorsWithPaidBills = $todayVisitors->filter(function ($visitor) {
            $services = [
                $visitor->entrance,
                $visitor->accommodation,
                $visitor->meal,
                $visitor->beverage,
                $visitor->kawabath,
                $visitor->watertubing,
                $visitor->massage,
                $visitor->picnictable,
            ];

            foreach ($services as $service) {
                if (!$service) {
                    continue;
                }

                $status = $service->payment_status ?? $service->status ?? 'Unpaid';
                if ($status !== 'Paid') {
                    return false;
                }
            }
            return true;
        });

        /*
        |--------------------------------------------------------------------------
        | VISITORS FOR TOTAL BILL COMPUTATION OF CURRENT YEAR
        |--------------------------------------------------------------------------
        */
        $allVisitors = Visitor::with([
            'entrance',
            'accommodation',
            'meal',
            'beverage',
            'kawabath',
            'watertubing',
            'massage',
            'picnictable',
        ])
            ->whereYear('created_at', $currentYear)
            ->orderBy('created_at', 'desc')
            ->get();
        /*
        |--------------------------------------------------------------------------
        | BILL COMPUTATION
        |--------------------------------------------------------------------------
        */
        $totalBills = 0;
        $paidBills = 0;
        $unpaidBills = 0;

        $visitorsForBills = $allVisitors;

        foreach ($visitorsForBills as $visitor) {

            $services = [
                $visitor->entrance,
                $visitor->accommodation,
                $visitor->meal,
                $visitor->beverage,
                $visitor->kawabath,
                $visitor->watertubing,
                $visitor->massage,
                $visitor->picnictable,
            ];

            $hasUnpaid = false;
            $hasService = false;

            foreach ($services as $service) {

                if (!$service) {
                    continue;
                }

                $hasService = true;

                $status = $service->payment_status
                    ?? $service->status
                    ?? 'Unpaid';

                $totalBills += $service->total_payment ?? 0;

                if ($status !== 'Paid') {
                    $hasUnpaid = true;
                }
            }

            if ($hasService) {

                if ($hasUnpaid) {
                    $unpaidBills++;
                } else {
                    $paidBills++;
                }
            }
        }

        /*
    |--------------------------------------------------------------------------
    | CHART DATA (FIXED: members + 1)
    |--------------------------------------------------------------------------
    */
        $monthlyVisitors = Visitor::query()
            ->selectRaw('strftime("%m", created_at) as month, SUM(COALESCE(members,0) + 1) as total')
            ->whereYear('created_at', $selectedYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $visitorsPerMonth = array_fill(0, 12, 0);

        foreach ($monthlyVisitors as $row) {
            $monthIndex = (int)$row->month - 1;
            $visitorsPerMonth[$monthIndex] = $row->total;
        }

        $months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        /*
    |--------------------------------------------------------------------------
    | SERVICES
    |--------------------------------------------------------------------------
    */
        $entranceFees = Service::where('service_type', 'entrance_fee')->get();

        /*
    |--------------------------------------------------------------------------
    | MONTHLY BILL DATA 
    |--------------------------------------------------------------------------
    */
        $monthlyBills = Visitor::with([
            'entrance',
            'accommodation',
            'meal',
            'beverage',
            'kawabath',
            'watertubing',
            'massage',
            'picnictable',
        ])
            ->whereYear('created_at', $currentYear)
            ->orderBy('created_at', 'desc')
            ->get();

        $billsPerMonth = array_fill(0, 12, 0);

        foreach ($monthlyBills as $visitor) {

            $monthIndex = (int) date('m', strtotime($visitor->created_at)) - 1;

            $total = 0;
            
            $total += optional($visitor->entrance)->total_payment ?? 0;
            $total += optional($visitor->accommodation)->total_payment ?? 0;
            $total += optional($visitor->meal)->total_payment ?? 0;
            $total += optional($visitor->beverage)->total_payment ?? 0;
            $total += optional($visitor->kawabath)->total_payment ?? 0;
            $total += optional($visitor->watertubing)->total_payment ?? 0;
            $total += optional($visitor->massage)->total_payment ?? 0;
            $total += optional($visitor->picnictable)->total_payment ?? 0;

            $billsPerMonth[$monthIndex] += $total;
        }
        /*
    |--------------------------------------------------------------------------
    | RETURN VIEW
    |--------------------------------------------------------------------------
    */
        return view('dashboard', [
            'visitorsToday' => $visitorsToday,
            'visitorsThisWeek' => $visitorsThisWeek,
            'visitorsThisMonth' => $visitorsThisMonth,
            'visitorsThisYear' => $visitorsThisYear,

            'totalBills' => $totalBills,
            'paidBills' => $paidBills,
            'unpaidBills' => $unpaidBills,

            // 'visitors' => $visitorsMonth,
            'visitorsPerMonth' => $visitorsPerMonth,
            'months' => $months,
            'billsPerMonth' => $billsPerMonth,

            'entrances' => $entrances,
            'selectedYear' => $selectedYear,
            'visitorsWithUnpaidBills' => $visitorsWithUnpaidBills,
            'visitorsWithPaidBills' => $visitorsWithPaidBills,
            'entranceFees' => $entranceFees,
        ]);
    }

    public function profile()
    {
        $user = auth()->user();
        return view('profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        // Validation rules
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'birth_date' => 'required|date',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,' . $request->user_id,
            'username' => 'required|string|max:50|unique:users,username,' . $request->user_id,
            'password' => 'nullable',
            'confirm_password' => 'nullable|same:password',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp',
        ]);

        // Get user
        $user = User::findOrFail($request->user_id);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && file_exists(public_path('img/' . $user->avatar))) {
                unlink(public_path('img/' . $user->avatar));
            }
            $avatarName = time() . '.' . $request->avatar->extension();
            // Move directly to public/img
            $request->avatar->move(public_path('img'), $avatarName);
            $user->avatar = $avatarName;
        }

        // Update other fields
        $user->name = $request->name;
        $user->address = $request->address;
        $user->birth_date = $request->birth_date;
        $user->phone_number = $request->phone_number;
        $user->email = $request->email;
        $user->username = $request->username;

        // Only update password if provided
        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/')->with('success', 'Logout successful');
    }
}
