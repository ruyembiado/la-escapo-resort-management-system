<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $today = now();

        /** 1. DAILY (Current week total — Sunday to Saturday) **/

        // Get the start of this week (Sunday)
        $startOfWeek = $today->copy()->startOfWeek(Carbon::SUNDAY);
        // Get the end of this week (Saturday)
        $endOfWeek = $today->copy()->endOfWeek(Carbon::SATURDAY);
        $dailyTotal = Visitor::with(
            'entrance',
            'accommodation',
            'meal',
            'beverage',
            'massage',
            'watertubing',
            'picnictable',
            'kawabath'
        )
            ->whereBetween(
                DB::raw('DATE(date_visit)'),
                [$startOfWeek->toDateString(), $endOfWeek->toDateString()]
            )->get()
            ->sum(function ($visitor) {
                return ($visitor->entrance->total_payment ?? 0) +
                    ($visitor->accommodation->total_payment ?? 0) +
                    ($visitor->meal->total_payment ?? 0) +
                    ($visitor->beverage->total_payment ?? 0) +
                    ($visitor->massage->total_payment ?? 0) +
                    ($visitor->watertubing->total_payment ?? 0) +
                    ($visitor->picnictable->total_payment ?? 0) +
                    ($visitor->kawabath->total_payment ?? 0);
            });

        /** 2. WEEKLY (current month total) **/
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        $weeklyTotal = Visitor::with(
            'entrance',
            'accommodation',
            'meal',
            'beverage',
            'massage',
            'watertubing',
            'picnictable',
            'kawabath'
        )
            ->whereBetween('date_visit', [$startOfMonth, $endOfMonth])
            ->get()
            ->sum(function ($visitor) {
                return ($visitor->entrance->total_payment ?? 0) +
                    ($visitor->accommodation->total_payment ?? 0) +
                    ($visitor->meal->total_payment ?? 0) +
                    ($visitor->beverage->total_payment ?? 0) +
                    ($visitor->massage->total_payment ?? 0) +
                    ($visitor->watertubing->total_payment ?? 0) +
                    ($visitor->picnictable->total_payment ?? 0) +
                    ($visitor->kawabath->total_payment ?? 0);
            });

        /** 3. MONTHLY (current year total) **/
        $startOfYear = $today->copy()->startOfYear();
        $endOfYear = $today->copy()->endOfYear();

        $monthlyTotal = Visitor::with(
            'entrance',
            'accommodation',
            'meal',
            'beverage',
            'massage',
            'watertubing',
            'picnictable',
            'kawabath'
        )
            ->whereBetween('date_visit', [$startOfYear, $endOfYear])
            ->get()
            ->sum(function ($visitor) {
                return ($visitor->entrance->total_payment ?? 0) +
                    ($visitor->accommodation->total_payment ?? 0) +
                    ($visitor->meal->total_payment ?? 0) +
                    ($visitor->beverage->total_payment ?? 0) +
                    ($visitor->massage->total_payment ?? 0) +
                    ($visitor->watertubing->total_payment ?? 0) +
                    ($visitor->picnictable->total_payment ?? 0) +
                    ($visitor->kawabath->total_payment ?? 0);
            });

        /** 4. YEARLY (all years total) **/
        $yearlyTotal = Visitor::with(
            'entrance',
            'accommodation',
            'meal',
            'beverage',
            'massage',
            'watertubing',
            'picnictable',
            'kawabath'
        )
            ->get()
            ->sum(function ($visitor) {
                return ($visitor->entrance->total_payment ?? 0) +
                    ($visitor->accommodation->total_payment ?? 0) +
                    ($visitor->meal->total_payment ?? 0) +
                    ($visitor->beverage->total_payment ?? 0) +
                    ($visitor->massage->total_payment ?? 0) +
                    ($visitor->watertubing->total_payment ?? 0) +
                    ($visitor->picnictable->total_payment ?? 0) +
                    ($visitor->kawabath->total_payment ?? 0);
            });

        return view('report', [
            'dailyTotal'   => $dailyTotal,
            'weeklyTotal'  => $weeklyTotal,
            'monthlyTotal' => $monthlyTotal,
            'yearlyTotal'  => $yearlyTotal
        ]);
    }

    public function dailyReport(Request $request)
    {
        $date = $request->input('date') ?? Carbon::today()->toDateString();
        $visitors = Visitor::with('entrance', 'accommodation', 'meal', 'beverage', 'kawabath', 'watertubing', 'picnictable', 'massage')
            ->whereDate('date_visit', $date)
            ->get();

        // Summarize values
        $totalVisitors = 1;
        foreach ($visitors as $visitor) {
            if ($visitor->members) {
                $totalVisitors += (int) $visitor->members;
            }
        }

        $totalEntrance = $visitors->sum(function ($visitor) {
            return $visitor->entrance ? (float) ($visitor->entrance->total_payment ?? 0) : 0;
        });

        $totalAccommodation = $visitors->sum(function ($visitor) {
            return $visitor->accommodation ? (float) ($visitor->accommodation->total_payment ?? 0) : 0;
        });

        $totalMeal = $visitors->sum(function ($visitor) {
            return $visitor->meal ? (float) ($visitor->meal->total_payment ?? 0) : 0;
        });

        $totalBeverage = $visitors->sum(function ($visitor) {
            return $visitor->beverage ? (float) ($visitor->beverage->total_payment ?? 0) : 0;
        });

        $totalKawabath = $visitors->sum(function ($visitor) {
            return $visitor->kawabath ? (float) ($visitor->kawabath->total_payment ?? 0) : 0;
        });

        $totalWatertubing = $visitors->sum(function ($visitor) {
            return $visitor->watertubing ? (float) ($visitor->watertubing->total_payment ?? 0) : 0;
        });

        $totalPicnicTable = $visitors->sum(function ($visitor) {
            return $visitor->picnictable ? (float) ($visitor->picnictable->total_payment ?? 0) : 0;
        });

        $totalMassage = $visitors->sum(function ($visitor) {
            return $visitor->massage ? (float) ($visitor->massage->total_payment ?? 0) : 0;
        });

        // Final report
        $report = [
            'visitors' => $totalVisitors,
            'entrance_fee' => $totalEntrance,
            'accommodation' => $totalAccommodation,
            'meal' => $totalMeal,
            'beverage' => $totalBeverage,
            'kawabath' => $totalKawabath,
            'watertubing' => $totalWatertubing,
            'picnictable' => $totalPicnicTable,
            'massage' => $totalMassage,
            'total' => $totalEntrance + $totalAccommodation + $totalMeal + $totalBeverage + $totalKawabath + $totalWatertubing + $totalPicnicTable + $totalMassage,
        ];

        $dayName = Carbon::parse($date)->format('l');

        return view('daily_report', [
            'report' => $report,
            'date' => $date,
            'day' => $dayName,
        ]);
    }

    public function dailyIncomeReport(Request $request)
    {
        $selectedYear = $request->input('year') ?? now()->year;
        $selectedMonth = $request->input('month') ?? now()->month;
        $selectedWeek = $request->input('week');

        $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // If week is selected, adjust to Sunday–Saturday
        if ($selectedWeek) {
            $weekStart = $startDate->copy()->addWeeks($selectedWeek - 1)->startOfWeek(Carbon::SUNDAY);
            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SATURDAY);

            // Bound inside the month
            $weekStart = $weekStart->lt($startDate) ? $startDate : $weekStart;
            $weekEnd = $weekEnd->gt($endDate) ? $endDate : $weekEnd;

            $startDate = $weekStart;
            $endDate = $weekEnd;
        }

        // Pre-fill all days with zero data
        $dailyReport = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateKey = $currentDate->format('Y-m-d');
            $dailyReport[$dateKey] = [
                'date' => $dateKey,
                'day' => $currentDate->format('l'),
                'visitors' => 0,
                'total' => 0
            ];
            $currentDate->addDay();
        }

        // Get visitors for the selected range
        $visitors = Visitor::with(
            'entrance',
            'accommodation',
            'meal',
            'beverage',
            'kawabath',
            'watertubing',
            'picnictable',
            'massage'
        )
            ->whereBetween('date_visit', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        // Add actual data to the pre-filled days
        foreach ($visitors as $visitor) {
            $dateKey = Carbon::parse($visitor->date_visit)->format('Y-m-d');
            $dailyReport[$dateKey]['visitors'] += (int) ($visitor->members ?? 0);
            $dailyReport[$dateKey]['total'] +=
                ($visitor->entrance->total_payment ?? 0) +
                ($visitor->accommodation->total_payment ?? 0) +
                ($visitor->meal->total_payment ?? 0) +
                ($visitor->beverage->total_payment ?? 0) +
                ($visitor->kawabath->total_payment ?? 0) +
                ($visitor->watertubing->total_payment ?? 0) +
                ($visitor->picnictable->total_payment ?? 0) +
                ($visitor->massage->total_payment ?? 0);
        }

        return view('daily_income_report', [
            'dailyReport' => $dailyReport,
            'selected_year' => $selectedYear,
            'selected_month' => $selectedMonth,
            'selected_week' => $selectedWeek,
        ]);
    }

    public function weeklyReport(Request $request)
    {
        $selectedYear  = $request->input('year') ?? Carbon::now()->year;
        $selectedMonth = $request->input('month') ?? Carbon::now()->month;
        $selectedWeek  = $request->input('week');

        $startDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        if ($selectedWeek) {
            $weekStart = $startDate->copy()->addWeeks($selectedWeek - 1)->startOfWeek(Carbon::SUNDAY);
            $weekEnd   = $weekStart->copy()->addDays(6); // Saturday = Sunday + 6

            // Bound inside the month
            if ($weekStart->lt($startDate)) {
                $weekStart = $startDate;
            }
            if ($weekEnd->gt($endDate)) {
                $weekEnd = $endDate;
            }

            $startDate = $weekStart;
            $endDate   = $weekEnd;
        }

        $visitors = Visitor::with(
            'entrance',
            'accommodation',
            'meal',
            'beverage',
            'kawabath',
            'watertubing',
            'picnictable',
            'massage'
        )
            ->whereBetween('date_visit', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $emptyDayData = [
            'visitors'      => 0,
            'entrance_fee'  => 0,
            'accommodation' => 0,
            'meal'          => 0,
            'beverage'      => 0,
            'massage'       => 0,
            'watertubing'   => 0,
            'picnictable'   => 0,
            'kawabath'      => 0,
            'total'         => 0,
        ];

        // Build daily structure for the selected week or whole month
        $report = collect();
        $currentDay = $startDate->copy();
        while ($currentDay->lte($endDate)) {
            $dateKey = $currentDay->format('Y-m-d');
            $report->put($dateKey, array_merge([
                'date' => $dateKey,
                'day'  => $currentDay->format('l'),
            ], $emptyDayData));

            $currentDay->addDay();
        }

        // Fill with data
        foreach ($visitors as $visitor) {
            $dateKey = Carbon::parse($visitor->date_visit)->format('Y-m-d');

            if ($report->has($dateKey)) {
                $dayData = $report->get($dateKey); // get current day's data

                $dayData['visitors']      += (int) $visitor->members + 1;
                $dayData['entrance_fee']  += (float) ($visitor->entrance->total_payment ?? 0);
                $dayData['accommodation'] += (float) ($visitor->accommodation->total_payment ?? 0);
                $dayData['meal']          += (float) ($visitor->meal->total_payment ?? 0);
                $dayData['beverage']      += (float) ($visitor->beverage->total_payment ?? 0);
                $dayData['massage']       += (float) ($visitor->massage->total_payment ?? 0);
                $dayData['watertubing']   += (float) ($visitor->watertubing->total_payment ?? 0);
                $dayData['picnictable']   += (float) ($visitor->picnictable->total_payment ?? 0);
                $dayData['kawabath']      += (float) ($visitor->kawabath->total_payment ?? 0);

                $dayData['total'] =
                    $dayData['entrance_fee'] +
                    $dayData['accommodation'] +
                    $dayData['meal'] +
                    $dayData['beverage'] +
                    $dayData['massage'] +
                    $dayData['watertubing'] +
                    $dayData['picnictable'] +
                    $dayData['kawabath'];

                $report->put($dateKey, $dayData); // put back into collection
            }
        }

        // Totals
        $grandTotal = [
            'visitors'      => $report->sum('visitors'),
            'entrance_fee'  => $report->sum('entrance_fee'),
            'accommodation' => $report->sum('accommodation'),
            'meal'          => $report->sum('meal'),
            'beverage'      => $report->sum('beverage'),
            'massage'       => $report->sum('massage'),
            'watertubing'   => $report->sum('watertubing'),
            'picnictable'   => $report->sum('picnictable'),
            'kawabath'      => $report->sum('kawabath'),
            'total'         => $report->sum('total'),
        ];

        return view('weekly_report', [
            'report'         => $report,
            'grandTotal'     => $grandTotal,
            'start_date'     => $startDate->format('F d, Y'),
            'end_date'       => $endDate->format('F d, Y'),
            'selected_year'  => $selectedYear,
            'selected_month' => $selectedMonth,
            'selected_week'  => $selectedWeek,
            'month_name'     => $startDate->format('F'),
        ]);
    }

    public function weeklyIncomeReport(Request $request)
    {
        $selectedYear = $request->input('year') ?? Carbon::now()->year;
        $selectedMonth = $request->input('month') ?? Carbon::now()->month;

        $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfDay();
        $endDate   = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()->endOfDay();

        // Get all visitors in the month
        $visitors = Visitor::with(
            'entrance',
            'accommodation',
            'meal',
            'beverage',
            'kawabath',
            'watertubing',
            'picnictable',
            'massage'
        )
            ->whereYear('date_visit', $selectedYear)
            ->whereMonth('date_visit', $selectedMonth)
            ->get();

        $weeks = collect();
        $weekNumber = 1;
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // First week starts from the 1st of the month (even if mid-week)
            if ($weekNumber === 1) {
                $weekStart = $startDate->copy();
            } else {
                $weekStart = $currentDate->copy()->startOfWeek(Carbon::SUNDAY);
            }

            // Week ends on Saturday OR last day of month
            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SATURDAY);
            if ($weekEnd->gt($endDate)) {
                $weekEnd = $endDate->copy();
            }

            // Filter visitors for this week
            $weekVisitors = $visitors->filter(function ($v) use ($weekStart, $weekEnd) {
                $date = Carbon::parse($v->date_visit);
                return $date->between($weekStart, $weekEnd);
            });

            $totalIncome = $weekVisitors->sum(function ($v) {
                return ($v->entrance->total_payment ?? 0) +
                    ($v->accommodation->total_payment ?? 0) +
                    ($v->meal->total_payment ?? 0) +
                    ($v->beverage->total_payment ?? 0) +
                    ($v->massage->total_payment ?? 0) +
                    ($v->watertubing->total_payment ?? 0) +
                    ($v->picnictable->total_payment ?? 0) +
                    ($v->kawabath->total_payment ?? 0);
            });

            $weeks->push([
                'week' => $weekNumber,
                'visitors' => $weekVisitors->sum(fn($v) => (int) $v->members),
                'total' => $totalIncome,
            ]);

            // Move to next week
            $currentDate = $weekEnd->copy()->addDay();
            $weekNumber++;
        }

        // Grand totals
        $grandTotal = [
            'visitors' => $weeks->sum('visitors'),
            'total'    => $weeks->sum('total'),
        ];

        return view('weekly_income_report', [
            'weeks'         => $weeks,
            'grandTotal'    => $grandTotal,
            'selected_year' => $selectedYear,
            'selected_month' => $selectedMonth,
            'month_name'    => $startDate->format('F'),
        ]);
    }

    public function monthlyReport(Request $request)
    {
        $selectedYear = $request->input('year') ?? Carbon::now()->year;
        $selectedMonth = $request->input('month') ?? Carbon::now()->month;

        $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfDay();
        $endDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()->endOfDay();

        // Get all visitors in the month with related payments
        $visitors = Visitor::with(
            'entrance',
            'accommodation',
            'meal',
            'beverage',
            'kawabath',
            'watertubing',
            'picnictable',
            'massage'
        )
            ->whereYear('date_visit', $selectedYear)
            ->whereMonth('date_visit', $selectedMonth)
            ->get();

        // Initialize monthly totals
        $monthlyData = [
            'visitors' => $visitors->sum(fn($v) => ((int) $v->members) + 1),
            'entrance_fee' => $visitors->sum(fn($v) => (float) ($v->entrance->total_payment ?? 0)),
            'accommodation' => $visitors->sum(fn($v) => (float) ($v->accommodation->total_payment ?? 0)),
            'meal' => $visitors->sum(fn($v) => (float) ($v->meal->total_payment ?? 0)),
            'beverage' => $visitors->sum(fn($v) => (float) ($v->beverage->total_payment ?? 0)),
            'massage' => $visitors->sum(fn($v) => (float) ($v->massage->total_payment ?? 0)),
            'watertubing' => $visitors->sum(fn($v) => (float) ($v->watertubing->total_payment ?? 0)),
            'picnictable' => $visitors->sum(fn($v) => (float) ($v->picnictable->total_payment ?? 0)),
            'kawabath' => $visitors->sum(fn($v) => (float) ($v->kawabath->total_payment ?? 0)),
        ];

        $monthlyData['total'] = collect($monthlyData)->except('visitors')->sum();
        $weeklyBreakdown = collect();

        $weekNumber = 1;
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // First week starts from the 1st of the month (even if mid-week)
            if ($weekNumber === 1) {
                $weekStart = $startDate->copy();
            } else {
                $weekStart = $currentDate->copy()->startOfWeek(Carbon::SUNDAY);
            }

            // End of the week = Saturday OR end of month
            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SATURDAY);
            if ($weekEnd->gt($endDate)) {
                $weekEnd = $endDate->copy();
            }

            // Filter visitors for this week
            $weekVisitors = $visitors->filter(function ($v) use ($weekStart, $weekEnd) {
                $date = Carbon::parse($v->date_visit);
                return $date->gte($weekStart) && $date->lte($weekEnd);
            });

            $weekData = [
                'start_date' => $weekStart->format('M d'),
                'end_date' => $weekEnd->format('M d'),
                'visitors' => $weekVisitors->sum(fn($v) => ((int) $v->members) + 1),
                'entrance_fee' => $weekVisitors->sum(fn($v) => (float) ($v->entrance->total_payment ?? 0)),
                'accommodation' => $weekVisitors->sum(fn($v) => (float) ($v->accommodation->total_payment ?? 0)),
                'meal' => $weekVisitors->sum(fn($v) => (float) ($v->meal->total_payment ?? 0)),
                'beverage' => $weekVisitors->sum(fn($v) => (float) ($v->beverage->total_payment ?? 0)),
                'massage' => $weekVisitors->sum(fn($v) => (float) ($v->massage->total_payment ?? 0)),
                'watertubing' => $weekVisitors->sum(fn($v) => (float) ($v->watertubing->total_payment ?? 0)),
                'picnictable' => $weekVisitors->sum(fn($v) => (float) ($v->picnictable->total_payment ?? 0)),
                'kawabath' => $weekVisitors->sum(fn($v) => (float) ($v->kawabath->total_payment ?? 0)),
            ];

            $weekData['total'] = collect($weekData)->except(['total', 'start_date', 'end_date', 'visitors'])->sum();

            $weeklyBreakdown->put($weekNumber, $weekData);

            // Move to next week
            $currentDate = $weekEnd->copy()->addDay();
            $weekNumber++;
        }

        return view('monthly_report', [
            'monthlyData' => $monthlyData,
            'weeklyBreakdown' => $weeklyBreakdown,
            'selected_year' => $selectedYear,
            'selected_month' => $selectedMonth,
            'month_name' => $startDate->format('F'),
            'start_date' => $startDate->format('F d, Y'),
            'end_date' => $endDate->format('F d, Y'),
        ]);
    }

    public function monthlyIncomeReport(Request $request)
    {
        $selectedYear = $request->input('year') ?? now()->year;

        // Get all visitors for that year
        $visitors = Visitor::with(
            'entrance',
            'accommodation',
            'meal',
            'beverage',
            'kawabath',
            'watertubing',
            'picnictable',
            'massage'
        )
            ->whereYear('date_visit', $selectedYear)
            ->get();

        // Prepare Jan–Dec with default 0 values
        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        $monthlyBreakdown = [];
        foreach ($months as $num => $name) {
            $monthlyBreakdown[$num] = [
                'month' => $name,
                'visitors' => 0,
                'total' => 0
            ];
        }

        // Fill in actual data
        foreach ($visitors as $visitor) {
            $monthNum = (int) \Carbon\Carbon::parse($visitor->date_visit)->month;

            $monthlyBreakdown[$monthNum]['visitors'] += (int) $visitor->members;
            $monthlyBreakdown[$monthNum]['total'] +=
                ($visitor->entrance->total_payment ?? 0) +
                ($visitor->accommodation->total_payment ?? 0) +
                ($visitor->meal->total_payment ?? 0) +
                ($visitor->beverage->total_payment ?? 0) +
                ($visitor->massage->total_payment ?? 0) +
                ($visitor->watertubing->total_payment ?? 0) +
                ($visitor->picnictable->total_payment ?? 0) +
                ($visitor->kawabath->total_payment ?? 0);
        }

        $grandTotal = collect($monthlyBreakdown)->sum('total');
        $totalVisitors = collect($monthlyBreakdown)->sum('visitors');

        return view('monthly_income_report', [
            'monthlyBreakdown' => $monthlyBreakdown,
            'grandTotal' => $grandTotal,
            'totalVisitors' => $totalVisitors,
            'selectedYear' => $selectedYear,
        ]);
    }

    public function yearlyReport(Request $request)
    {
        $selectedYear = $request->input('year') ?? now()->year;

        $startDate = Carbon::createFromDate($selectedYear, 1, 1)->startOfYear();
        $endDate = $startDate->copy()->endOfYear();

        $visitors = Visitor::with(
            'entrance',
            'accommodation',
            'meal',
            'beverage',
            'kawabath',
            'watertubing',
            'picnictable',
            'massage'
        )
            ->whereYear('date_visit', $selectedYear)
            ->get();

        $yearlyData = [
            'visitors' => 0,
            'entrance_fee' => 0,
            'accommodation' => 0,
            'meal' => 0,
            'beverage' => 0,
            'massage' => 0,
            'watertubing' => 0,
            'picnictable' => 0,
            'kawabath' => 0,
            'total' => 0,
        ];

        $monthlyBreakdown = collect();

        // Loop through all 12 months
        for ($month = 1; $month <= 12; $month++) {
            $monthVisitors = $visitors->filter(function ($visitor) use ($month) {
                return Carbon::parse($visitor->date_visit)->month == $month;
            });

            $monthData = [
                'visitors' => 0,
                'entrance_fee' => 0,
                'accommodation' => 0,
                'meal' => 0,
                'beverage' => 0,
                'massage' => 0,
                'watertubing' => 0,
                'picnictable' => 0,
                'kawabath' => 0,
                'total' => 0,
                'month_name' => Carbon::create()->month($month)->format('F'),
            ];

            foreach ($monthVisitors as $visitor) {
                $monthData['visitors'] += (int) $visitor->members + 1;
                $monthData['entrance_fee'] += (float) ($visitor->entrance->total_payment ?? 0);
                $monthData['accommodation'] += (float) ($visitor->accommodation->total_payment ?? 0);
                $monthData['meal'] += (float) ($visitor->meal->total_payment ?? 0);
                $monthData['beverage'] += (float) ($visitor->beverage->total_payment ?? 0);
                $monthData['massage'] += (float) ($visitor->massage->total_payment ?? 0);
                $monthData['watertubing'] += (float) ($visitor->watertubing->total_payment ?? 0);
                $monthData['picnictable'] += (float) ($visitor->picnictable->total_payment ?? 0);
                $monthData['kawabath'] += (float) ($visitor->kawabath->total_payment ?? 0);
            }

            $monthData['total'] = $monthData['entrance_fee'] + $monthData['accommodation'] +
                $monthData['meal'] + $monthData['beverage'] + $monthData['massage'] +
                $monthData['watertubing'] + $monthData['picnictable'] + $monthData['kawabath'];

            // Add to monthly breakdown
            $monthlyBreakdown->put($month, $monthData);

            // Update yearly totals
            $yearlyData['visitors'] += $monthData['visitors'];
            $yearlyData['entrance_fee'] += $monthData['entrance_fee'];
            $yearlyData['accommodation'] += $monthData['accommodation'];
            $yearlyData['meal'] += $monthData['meal'];
            $yearlyData['beverage'] += $monthData['beverage'];
            $yearlyData['massage'] += $monthData['massage'];
            $yearlyData['watertubing'] += $monthData['watertubing'];
            $yearlyData['picnictable'] += $monthData['picnictable'];
            $yearlyData['kawabath'] += $monthData['kawabath'];
        }

        $yearlyData['total'] = $yearlyData['entrance_fee'] + $yearlyData['accommodation'] +
            $yearlyData['meal'] + $yearlyData['beverage'] + $yearlyData['massage'] +
            $yearlyData['watertubing'] + $yearlyData['picnictable'] + $yearlyData['kawabath'];

        return view('yearly_report', [
            'yearlyData' => $yearlyData,
            'monthlyBreakdown' => $monthlyBreakdown,
            'selected_year' => $selectedYear,
            'year_name' => $selectedYear,
            'start_date' => $startDate->format('F d, Y'),
            'end_date' => $endDate->format('F d, Y'),
        ]);
    }

    public function yearlyIncomeReport()
    {
        $visitors = Visitor::with(
            'entrance',
            'accommodation',
            'meal',
            'beverage',
            'kawabath',
            'watertubing',
            'picnictable',
            'massage'
        )->get();

        // Group by year
        $yearlyBreakdown = $visitors->groupBy(function ($visitor) {
            return Carbon::parse($visitor->date_visit)->format('Y'); // group by year
        })->map(function ($yearVisitors, $year) {
            $data = [
                'year' => $year,
                'visitors' => 0,
                'entrance_fee' => 0,
                'accommodation' => 0,
                'meal' => 0,
                'beverage' => 0,
                'massage' => 0,
                'watertubing' => 0,
                'picnictable' => 0,
                'kawabath' => 0,
                'total' => 0,
            ];

            foreach ($yearVisitors as $visitor) {
                $data['visitors'] += (int) $visitor->members;
                $data['entrance_fee'] += (float) ($visitor->entrance->total_payment ?? 0);
                $data['accommodation'] += (float) ($visitor->accommodation->total_payment ?? 0);
                $data['meal'] += (float) ($visitor->meal->total_payment ?? 0);
                $data['beverage'] += (float) ($visitor->beverage->total_payment ?? 0);
                $data['massage'] += (float) ($visitor->massage->total_payment ?? 0);
                $data['watertubing'] += (float) ($visitor->watertubing->total_payment ?? 0);
                $data['picnictable'] += (float) ($visitor->picnictable->total_payment ?? 0);
                $data['kawabath'] += (float) ($visitor->kawabath->total_payment ?? 0);
            }

            $data['total'] = $data['entrance_fee'] + $data['accommodation'] +
                $data['meal'] + $data['beverage'] + $data['massage'] +
                $data['watertubing'] + $data['picnictable'] + $data['kawabath'];

            return $data;
        })->sortKeys(); // sort by year

        // Grand totals
        $totalVisitors = $yearlyBreakdown->sum('visitors');
        $grandTotal = $yearlyBreakdown->sum('total');

        return view('yearly_income_report', [
            'yearlyBreakdown' => $yearlyBreakdown,
            'totalVisitors' => $totalVisitors,
            'grandTotal' => $grandTotal
        ]);
    }

    public function reportType(Request $request)
    {
        $now = Carbon::now();
        $route = match ($request->report_type) {
            'daily' => route('daily.report', [
                'year' => $now->year,
                'month' => $now->month,
                'day' => $now->day,
            ]),
            'weekly' => route('weekly.report', [
                'year' => $now->year,
                'month' => $now->month,
                'week' => $now->weekOfMonth,
            ]),
            'monthly' => route('monthly.report', [
                'year' => $now->year,
                'month' => $now->month,
            ]),
            'yearly' => route('yearly.report', [
                'year' => $now->year,
            ]),
            default => abort(404, 'Invalid report type'),
        };

        return redirect($route);
    }
}
