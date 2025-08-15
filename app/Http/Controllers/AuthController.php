<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Visitor;
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

    public function dashboard()
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();
        $currentYear = Carbon::now()->year;

        $visitorsToday = Visitor::whereDate('created_at', $today)->count();
        $visitorsThisWeek = Visitor::where('created_at', '>=', $startOfWeek)->count();
        $visitorsThisMonth = Visitor::where('created_at', '>=', $startOfMonth)->count();
        $visitorsThisYear = Visitor::where('created_at', '>=', $startOfYear)->count();

        // Get all Visitors for Visitor table this month
        $visitorsMonth = Visitor::where('created_at', '>=', $startOfMonth)
            ->orderBy('created_at', 'desc')
            ->get();

        // Chart: Monthly Visitors
        $monthlyVisitors = Visitor::query()
            ->selectRaw('strftime("%m", created_at) as month, count(*) as total')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Initialize all months with 0 counts
        $visitorsPerMonth = array_fill(0, 12, 0);

        // Fill in actual counts
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

        return view('dashboard', [
            'visitorsToday' => $visitorsToday,
            'visitorsThisWeek' => $visitorsThisWeek,
            'visitorsThisMonth' => $visitorsThisMonth,
            'visitorsThisYear' => $visitorsThisYear,
            'visitors' => $visitorsMonth,
            'visitorsPerMonth' => $visitorsPerMonth,
            'months' => $months,
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
            'password' => 'nullable|min:6',
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
