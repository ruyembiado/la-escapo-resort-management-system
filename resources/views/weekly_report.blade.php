@extends('layouts.auth')
@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Weekly Report</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <form method="GET" action="{{ route('weekly.report') }}" class="d-print-none">
                    <div class="d-flex gap-2 align-items-center flex-row">
                        <!-- Year Selector -->
                        <div class="d-flex flex-column">
                            <label for="year" class="form-label mb-0">Select Year:</label>
                            <select name="year" id="year" class="form-control form-control-sm"
                                onchange="this.form.submit()">
                                @for ($y = date('Y'); $y >= 2024; $y--)
                                    <option value="{{ $y }}"
                                        {{ request('year', $selected_year) == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Month Selector -->
                        <div class="d-flex flex-column">
                            <label for="month" class="form-label mb-0">Select Month:</label>
                            <select name="month" id="month" class="form-control form-control-sm"
                                onchange="this.form.submit()">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}"
                                        {{ request('month', $selected_month) == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromDate($selected_year, $m, 1)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        @php
                            use Carbon\Carbon;

                            $firstDay = Carbon::createFromDate($selected_year, $selected_month, 1);
                            $lastDay = $firstDay->copy()->endOfMonth();

                            // Force start on Sunday (go back to last Sunday before 1st, if not Sunday)
                            $startOfCalendar = $firstDay->copy()->startOfWeek(Carbon::SUNDAY);
                            // Force end on Saturday (go forward to Saturday after last day, if not Saturday)
                            $endOfCalendar = $lastDay->copy()->endOfWeek(Carbon::SATURDAY);

                            // Total weeks spanned
                            $totalWeeks = $startOfCalendar->diffInWeeks($endOfCalendar) + 1;
                        @endphp

                        <div class="d-flex flex-column">
                            <label for="week" class="form-label mb-0">Select Week:</label>
                            <select name="week" id="week" class="form-control form-control-sm"
                                onchange="this.form.submit()">
                                @foreach (range(1, $totalWeeks) as $week)
                                    <option value="{{ $week }}"
                                        {{ request('week', $selected_week) == $week ? 'selected' : '' }}>
                                        Week {{ $week }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>

                <div class="print-buttons">
                    <button onclick="printReport()" class="btn btn-sm btn-primary d-print-none">
                        <i class="fas fa-print"></i> Print Report
                    </button>

                    <button onclick="exportExcel()" class="btn btn-sm btn-success d-print-none">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
            </div>

            <div id="print-section">
                <table class="report-header m-auto" width="100%" cellspacing="0" cellpadding="0"
                    style="border-collapse: collapse;">
                    <tr>
                        <td style="vertical-align: middle;" class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                <div class="company-logo">
                                    <img src="{{ asset('public/img/logo.png') }}" alt="Company Logo"
                                        style="height: 100px; display: block;" />
                                </div>
                                <div class="company-text">
                                    <h4 class="mb-0">La Escapo Resort</h4>
                                    <p class="mb-0">Tuno, Tibiao, Antique</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <h2 class="mb-0">Week {{ $selected_week }} Report for {{ $month_name }}
                                {{ $selected_year }}</h2>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="date mb-1 text-start">
                                <p class="m-0">Year:
                                    {{ \Carbon\Carbon::createFromDate($selected_year, $selected_month, 1)->format('Y') }}
                                </p>
                                <p class="m-0">Month:
                                    {{ \Carbon\Carbon::createFromDate($selected_year, $selected_month, 1)->format('F') }}
                                </p>
                                <p class="m-0">Week:
                                    {{ $selected_week }}
                                </p>
                            </div>
                        </td>
                    </tr>
                </table>

                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>No. of Visitors</th>
                                <th>Entrance Fee</th>
                                <th>Kawa Hot Bath</th>
                                <th>Water Tubing</th>
                                <th>Picnic Table</th>
                                <th>Massage</th>
                                <th>Accommodation</th>
                                <th>Meals</th>
                                <th>Beverages</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($report->isEmpty())
                                <tr>
                                    <td colspan="11" class="text-center">No data available for this week.</td>
                                </tr>
                            @else
                                @foreach ($report as $dayData)
                                    <tr>
                                        <td>{{ $dayData['day'] }}</td>
                                        <td>{{ $dayData['visitors'] ?? 0 }}</td>
                                        <td>₱{{ number_format($dayData['entrance_fee'] ?? 0, 2) }}</td>
                                        <td>₱{{ number_format($dayData['kawabath'] ?? 0, 2) }}</td>
                                        <td>₱{{ number_format($dayData['watertubing'] ?? 0, 2) }}</td>
                                        <td>₱{{ number_format($dayData['picnictable'] ?? 0, 2) }}</td>
                                        <td>₱{{ number_format($dayData['massage'] ?? 0, 2) }}</td>
                                        <td>₱{{ number_format($dayData['accommodation'] ?? 0, 2) }}</td>
                                        <td>₱{{ number_format($dayData['meal'] ?? 0, 2) }}</td>
                                        <td>₱{{ number_format($dayData['beverage'] ?? 0, 2) }}</td>
                                        <td>₱{{ number_format($dayData['total'] ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="bg-light">
                                    <td colspan="1" class="text-start h6">Grand Total</td>
                                    <td class="h6">{{ $grandTotal['visitors'] }}</td>
                                    <td class="h6">₱{{ number_format($grandTotal['entrance_fee'], 2) }}</td>
                                    <td class="h6">₱{{ number_format($grandTotal['kawabath'], 2) }}</td>
                                    <td class="h6">₱{{ number_format($grandTotal['watertubing'], 2) }}</td>
                                    <td class="h6">₱{{ number_format($grandTotal['picnictable'], 2) }}</td>
                                    <td class="h6">₱{{ number_format($grandTotal['massage'], 2) }}</td>
                                    <td class="h6">₱{{ number_format($grandTotal['accommodation'], 2) }}</td>
                                    <td class="h6">₱{{ number_format($grandTotal['meal'], 2) }}</td>
                                    <td class="h6">₱{{ number_format($grandTotal['beverage'], 2) }}</td>
                                    <td class="h6">₱{{ number_format($grandTotal['total'], 2) }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="col-12 d-flex justify-content-end print-footer">
                    <div class="d-flex flex-column justify-content-end align-items-center">
                        <strong>MENDITO A. AMAR JR.</strong>
                        <span>La Escapo Resort Owner</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function printReport() {
            printJS({
                printable: 'print-section',
                type: 'html',
                css: [
                    '{{ asset('public/css/styles.css') }}',
                    '{{ asset('public/css/bootstrap.min.css') }}'
                ],
            });
        }
    </script>

    <script>
        function exportExcel() {
            // Headers from the table
            let headers = [
                "Day",
                "No. of Visitors",
                "Entrance Fee",
                "Kawa Hot Bath",
                "Water Tubing",
                "Picnic Table",
                "Massage",
                "Accommodation",
                "Meals",
                "Beverages",
                "Total"
            ];

            // Rows for each day
            let data = [
                @foreach ($report as $dayData)
                    [
                        "{{ $dayData['day'] }}",
                        "{{ $dayData['visitors'] ?? 0 }}",
                        "{{ $dayData['entrance_fee'] ?? 0 }}",
                        "{{ $dayData['kawabath'] ?? 0 }}",
                        "{{ $dayData['watertubing'] ?? 0 }}",
                        "{{ $dayData['picnictable'] ?? 0 }}",
                        "{{ $dayData['massage'] ?? 0 }}",
                        "{{ $dayData['accommodation'] ?? 0 }}",
                        "{{ $dayData['meal'] ?? 0 }}",
                        "{{ $dayData['beverage'] ?? 0 }}",
                        "{{ $dayData['total'] ?? 0 }}"
                    ],
                @endforeach

                // Grand total row
                [
                    "Grand Total",
                    "{{ $grandTotal['visitors'] }}",
                    "{{ $grandTotal['entrance_fee'] }}",
                    "{{ $grandTotal['kawabath'] }}",
                    "{{ $grandTotal['watertubing'] }}",
                    "{{ $grandTotal['picnictable'] }}",
                    "{{ $grandTotal['massage'] }}",
                    "{{ $grandTotal['accommodation'] }}",
                    "{{ $grandTotal['meal'] }}",
                    "{{ $grandTotal['beverage'] }}",
                    "{{ $grandTotal['total'] }}"
                ]
            ];

            // Convert to Excel sheet
            let worksheet = XLSX.utils.aoa_to_sheet([headers, ...data]);
            let workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Weekly Report");

            // File name includes week, month, year
            XLSX.writeFile(workbook,
                "weekly_report_week{{ $selected_week }}_{{ $month_name }}_{{ $selected_year }}.xlsx");
        }
    </script>
@endsection
