<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Visitor;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('report');
    }

    public function dailyReport(Request $request)
    {
        $date = $request->input('date') ?? Carbon::today()->toDateString();
        $visitors = Visitor::with('entrance', 'accommodation', 'cottage', 'meal', 'beverage', 'kawabath', 'watertubing', 'picnictable', 'massage')
            ->whereDate('date_visit', $date)
            ->get();

        // Summarize values
        $totalVisitors = 0;
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

        $totalRental = $visitors->sum(function ($visitor) {
            return $visitor->cottage ? (float) ($visitor->cottage->total_payment ?? 0) : 0;
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
            'rental' => $totalRental,
            'meal' => $totalMeal,
            'beverage' => $totalBeverage,
            'kawabath' => $totalKawabath,
            'watertubing' => $totalWatertubing,
            'picnictable' => $totalPicnicTable,
            'massage' => $totalMassage,
            'total' => $totalEntrance + $totalAccommodation + $totalRental + $totalMeal + $totalBeverage + $totalKawabath + $totalWatertubing + $totalPicnicTable + $totalMassage,
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
        $selectedYear = $request->input('year') ?? Carbon::now()->year;
        $selectedMonth = $request->input('month') ?? Carbon::now()->month;

        $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $visitors = Visitor::with(
            'entrance',
            'accommodation',
            'cottage',
            'meal',
            'beverage',
            'kawabath',
            'watertubing',
            'picnictable',
            'massage'
        )
            ->whereBetween('date_visit', [$startDate, $endDate])
            ->get();

        $dailyReport = [];

        foreach ($visitors as $visitor) {
            $date = Carbon::parse($visitor->date_visit)->format('Y-m-d');

            if (!isset($dailyReport[$date])) {
                $dailyReport[$date] = [
                    'date' => $date,
                    'day' => Carbon::parse($date)->format('l'),
                    'visitors' => 0,
                    'total' => 0
                ];
            }

            $dailyReport[$date]['visitors'] += (int) ($visitor->members ?? 0);

            $dailyReport[$date]['total'] +=
                ($visitor->entrance->total_payment ?? 0) +
                ($visitor->accommodation->total_payment ?? 0) +
                ($visitor->cottage->total_payment ?? 0) +
                ($visitor->meal->total_payment ?? 0) +
                ($visitor->beverage->total_payment ?? 0) +
                ($visitor->kawabath->total_payment ?? 0) +
                ($visitor->watertubing->total_payment ?? 0) +
                ($visitor->picnictable->total_payment ?? 0) +
                ($visitor->massage->total_payment ?? 0);
        }

        // Sort by date
        ksort($dailyReport);

        return view('daily_income_report', [
            'dailyReport' => $dailyReport,
            'selected_year' => $selectedYear,
            'selected_month' => $selectedMonth,
        ]);
    }

    public function weeklyReport(Request $request)
    {
        $selectedYear = $request->input('year') ?? Carbon::now()->year;
        $selectedMonth = $request->input('month') ?? Carbon::now()->month;
        $selectedWeek = $request->input('week'); // optional filtering

        $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $visitors = Visitor::with('entrance', 'accommodation', 'cottage', 'meal', 'beverage', 'kawabath', 'watertubing', 'picnictable', 'massage')
            ->whereDate('date_visit', '>=', $startDate)
            ->whereDate('date_visit', '<=', $endDate)
            ->get();

        // Group visitors by week and day
        $report = collect();

        foreach ($visitors as $visitor) {
            $weekNumber = Carbon::parse($visitor->date_visit)->weekOfMonth;
            $dayName = Carbon::parse($visitor->date_visit)->format('l');

            $entrance = $visitor->entrance->total_payment ?? 0;
            $accommodation = $visitor->accommodation->total_payment ?? 0;
            $rental = $visitor->cottage->total_payment ?? 0;
            $meal = $visitor->meal->total_payment ?? 0;
            $kawabath = $visitor->kawabath->total_payment ?? 0;
            $watertubing = $visitor->watertubing->total_payment ?? 0;
            $picnictable = $visitor->picnictable->total_payment ?? 0;
            $massage = $visitor->massage->total_payment ?? 0;
            $beverage = $visitor->beverage->total_payment ?? 0;

            if (!$report->has($weekNumber)) {
                $report->put($weekNumber, collect());
            }

            $weekData = $report->get($weekNumber);

            if (!$weekData->has($dayName)) {
                $weekData->put($dayName, [
                    'visitors' => 0,
                    'entrance_fee' => 0,
                    'accommodation' => 0,
                    'rental' => 0,
                    'meal' => 0,
                    'beverage' => 0,
                    'massage' => 0,
                    'watertubing' => 0,
                    'picnictable' => 0,
                    'kawabath' => 0,
                    'total' => 0,
                ]);
            }

            $dayData = $weekData->get($dayName);

            $dayData['visitors'] += (int) $visitor->members;
            $dayData['entrance_fee'] += (float) $entrance;
            $dayData['accommodation'] += (float) $accommodation;
            $dayData['rental'] += (float) $rental;
            $dayData['meal'] += (float) $meal;
            $dayData['beverage'] += (float) $beverage;
            $dayData['massage'] += (float) $massage;
            $dayData['watertubing'] += (float) $watertubing;
            $dayData['picnictable'] += (float) $picnictable;
            $dayData['kawabath'] += (float) $kawabath;
            $dayData['total'] = $dayData['entrance_fee'] + $dayData['accommodation'] + $dayData['rental'] + $dayData['meal'] + $dayData['beverage'] + $dayData['massage'] + $dayData['watertubing'] + $dayData['picnictable'] + $dayData['kawabath'];

            $weekData->put($dayName, $dayData);
            $report->put($weekNumber, $weekData);
        }

        // Filter to selected week
        if ($selectedWeek) {
            $report = $report->only([$selectedWeek]);
        }

        // Compute weekly totals and grand totals
        $weeklyTotal = $report->map(function ($weekDays) {
            return [
                'visitors' => $weekDays->sum('visitors'),
                'entrance_fee' => $weekDays->sum('entrance_fee'),
                'accommodation' => $weekDays->sum('accommodation'),
                'rental' => $weekDays->sum('rental'),
                'meal' => $weekDays->sum('meal'),
                'beverage' => $weekDays->sum('beverage'),
                'massage' => $weekDays->sum('massage'),
                'watertubing' => $weekDays->sum('watertubing'),
                'picnictable' => $weekDays->sum('picnictable'),
                'kawabath' => $weekDays->sum('kawabath'),
                'total' => $weekDays->sum('total'),
            ];
        });

        $grandTotal = [
            'visitors' => $weeklyTotal->sum('visitors'),
            'entrance_fee' => $weeklyTotal->sum('entrance_fee'),
            'accommodation' => $weeklyTotal->sum('accommodation'),
            'rental' => $weeklyTotal->sum('rental'),
            'meal' => $weeklyTotal->sum('meal'),
            'beverage' => $weeklyTotal->sum('beverage'),
            'massage' => $weeklyTotal->sum('massage'),
            'watertubing' => $weeklyTotal->sum('watertubing'),
            'picnictable' => $weeklyTotal->sum('picnictable'),
            'kawabath' => $weeklyTotal->sum('kawabath'),
            'total' => $weeklyTotal->sum('total'),
        ];

        return view('weekly_report', [
            'report' => $report,
            'weeklyTotal' => $weeklyTotal,
            'grandTotal' => $grandTotal,
            'start_date' => $startDate->format('F d, Y'),
            'end_date' => $endDate->format('F d, Y'),
            'selected_year' => $selectedYear,
            'selected_month' => $selectedMonth,
            'selected_week' => $selectedWeek,
            'month_name' => $startDate->format('F'),
        ]);
    }


    public function monthlyReport(Request $request)
    {
        $selectedYear = $request->input('year') ?? Carbon::now()->year;
        $selectedMonth = $request->input('month') ?? Carbon::now()->month;

        $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $visitors = Visitor::with('entrance', 'accommodation', 'cottage', 'meal', 'beverage', 'kawabath', 'watertubing', 'picnictable', 'massage')
            ->whereDate('date_visit', '>=', $startDate)
            ->whereDate('date_visit', '<=', $endDate)
            ->get();

        // Initialize monthly totals
        $monthlyData = [
            'visitors' => 0,
            'entrance_fee' => 0,
            'accommodation' => 0,
            'rental' => 0,
            'meal' => 0,
            'beverage' => 0,
            'massage' => 0,
            'watertubing' => 0,
            'picnictable' => 0,
            'kawabath' => 0,
            'total' => 0,
        ];

        // Calculate totals for the month
        foreach ($visitors as $visitor) {
            $monthlyData['visitors'] += (int) $visitor->members;
            $monthlyData['entrance_fee'] += (float) ($visitor->entrance->total_payment ?? 0);
            $monthlyData['accommodation'] += (float) ($visitor->accommodation->total_payment ?? 0);
            $monthlyData['rental'] += (float) ($visitor->rental->total_payment ?? 0);
            $monthlyData['meal'] += (float) ($visitor->meal->total_payment ?? 0);
            $monthlyData['beverage'] += (float) ($visitor->beverage->total_payment ?? 0);
            $monthlyData['massage'] += (float) ($visitor->massage->total_payment ?? 0);
            $monthlyData['watertubing'] += (float) ($visitor->watertubing->total_payment ?? 0);
            $monthlyData['picnictable'] += (float) ($visitor->picnictable->total_payment ?? 0);
            $monthlyData['kawabath'] += (float) ($visitor->kawabath->total_payment ?? 0);
        }

        $monthlyData['total'] = $monthlyData['entrance_fee'] + $monthlyData['accommodation'] + $monthlyData['rental'] + $monthlyData['meal'] + $monthlyData['beverage'] + $monthlyData['massage'] + $monthlyData['watertubing'] + $monthlyData['picnictable'] + $monthlyData['kawabath'];

        // Also group by week for weekly breakdown within the month
        $weeklyBreakdown = $visitors->groupBy(function ($visitor) {
            return Carbon::parse($visitor->date_visit)->weekOfMonth;
        })->map(function ($weekVisitors, $weekNumber) {
            $weekData = [
                'visitors' => 0,
                'entrance_fee' => 0,
                'accommodation' => 0,
                'rental' => 0,
                'meal' => 0,
                'beverage' => 0,
                'massage' => 0,
                'watertubing' => 0,
                'picnictable' => 0,
                'kawabath' => 0,
                'total' => 0,
            ];

            foreach ($weekVisitors as $visitor) {
                $weekData['visitors'] += (int) $visitor->members;
                $weekData['entrance_fee'] += (float) ($visitor->entrance->total_payment ?? 0);
                $weekData['accommodation'] += (float) ($visitor->accommodation->total_payment ?? 0);
                $weekData['rental'] += (float) ($visitor->cottage->total_payment ?? 0);
                $weekData['meal'] += (float) ($visitor->meal->total_payment ?? 0);
                $weekData['massage'] += (float) ($visitor->massage->total_payment ?? 0);
                $weekData['watertubing'] += (float) ($visitor->watertubing->total_payment ?? 0);
                $weekData['picnictable'] += (float) ($visitor->picnictable->total_payment ?? 0);
                $weekData['kawabath'] += (float) ($visitor->kawabath->total_payment ?? 0);
                $weekData['beverage'] += (float) ($visitor->beverage->total_payment ?? 0);
            }

            $weekData['total'] = $weekData['entrance_fee'] + $weekData['accommodation'] + $weekData['rental'] + $weekData['meal'] + $weekData['beverage'] + $weekData['massage'] + $weekData['watertubing'] + $weekData['picnictable'] + $weekData['kawabath'];

            return $weekData;
        });

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

    public function yearlyReport(Request $request)
    {
        $selectedYear = $request->input('year') ?? Carbon::now()->year;

        $startDate = Carbon::createFromDate($selectedYear, 1, 1)->startOfYear();
        $endDate = $startDate->copy()->endOfYear();

        $visitors = Visitor::with('entrance', 'accommodation', 'cottage', 'meal', 'beverage', 'kawabath', 'watertubing', 'picnictable', 'massage')
            ->whereBetween('date_visit', [$startDate, $endDate])
            ->get();

        // Initialize yearly totals
        $yearlyData = [
            'visitors' => 0,
            'entrance_fee' => 0,
            'accommodation' => 0,
            'rental' => 0,
            'meal' => 0,
            'beverage' => 0,
            'massage' => 0,
            'watertubing' => 0,
            'picnictable' => 0,
            'kawabath' => 0,
            'total' => 0,
        ];

        // Group by month for monthly breakdown
        $monthlyBreakdown = $visitors->groupBy(function ($visitor) {
            return Carbon::parse($visitor->date_visit)->format('m'); // Group by month number
        })->map(function ($monthVisitors, $monthNumber) {
            $monthData = [
                'visitors' => 0,
                'entrance_fee' => 0,
                'accommodation' => 0,
                'rental' => 0,
                'meal' => 0,
                'beverage' => 0,
                'massage' => 0,
                'watertubing' => 0,
                'picnictable' => 0,
                'kawabath' => 0,
                'total' => 0,
                'month_name' => Carbon::create()->month($monthNumber)->format('F')
            ];

            foreach ($monthVisitors as $visitor) {
                $monthData['visitors'] += (int) $visitor->members;
                $monthData['entrance_fee'] += (float) ($visitor->entrance->total_payment ?? 0);
                $monthData['accommodation'] += (float) ($visitor->accommodation->total_payment ?? 0);
                $monthData['rental'] += (float) ($visitor->cottage->total_payment ?? 0);
                $monthData['meal'] += (float) ($visitor->meal->total_payment ?? 0);
                $monthData['beverage'] += (float) ($visitor->beverage->total_payment ?? 0);
                $monthData['massage'] += (float) ($visitor->massage->total_payment ?? 0);
                $monthData['watertubing'] += (float) ($visitor->watertubing->total_payment ?? 0);
                $monthData['picnictable'] += (float) ($visitor->picnictable->total_payment ?? 0);
                $monthData['kawabath'] += (float) ($visitor->kawabath->total_payment ?? 0);
            }

            $monthData['total'] = $monthData['entrance_fee'] + $monthData['accommodation'] + $monthData['rental'] + $monthData['meal'] + $monthData['beverage'] + $monthData['massage'] + $monthData['watertubing'] + $monthData['picnictable'] + $monthData['kawabath'];

            return $monthData;
        });

        // Calculate yearly totals from monthly breakdown
        foreach ($monthlyBreakdown as $monthData) {
            $yearlyData['visitors'] += $monthData['visitors'];
            $yearlyData['entrance_fee'] += $monthData['entrance_fee'];
            $yearlyData['accommodation'] += $monthData['accommodation'];
            $yearlyData['rental'] += $monthData['rental'];
            $yearlyData['meal'] += $monthData['meal'];
            $yearlyData['beverage'] += $monthData['beverage'];
            $yearlyData['massage'] += $monthData['massage'];
            $yearlyData['watertubing'] += $monthData['watertubing'];
            $yearlyData['picnictable'] += $monthData['picnictable'];
            $yearlyData['kawabath'] += $monthData['kawabath'];
        }
        $yearlyData['total'] = $yearlyData['entrance_fee'] + $yearlyData['accommodation'] + $yearlyData['rental'] + $yearlyData['meal'] + $yearlyData['beverage'] + $yearlyData['massage'] + $yearlyData['watertubing'] + $yearlyData['picnictable'] + $yearlyData['kawabath'];

        // Sort monthly breakdown by month number (01-12)
        $monthlyBreakdown = $monthlyBreakdown->sortBy(function ($item, $key) {
            return (int)$key;
        });

        return view('yearly_report', [
            'yearlyData' => $yearlyData,
            'monthlyBreakdown' => $monthlyBreakdown,
            'selected_year' => $selectedYear,
            'year_name' => $selectedYear,
            'start_date' => $startDate->format('F d, Y'),
            'end_date' => $endDate->format('F d, Y'),
        ]);
    }
}
