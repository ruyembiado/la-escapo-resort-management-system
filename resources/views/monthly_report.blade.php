@extends('layouts.auth')
@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="d-flex">
            <i class="fas fa-file-invoice-dollar fa-2x text-dark me-2"></i>
            <div class="d-flex flex-column">
                <h1 class="h3 mb-0 text">MONTHLY REPORT</h1>
                <h6 class="mb-0">Report | Monthly Report</h6>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <form method="GET" action="{{ route('monthly.report') }}" class="d-print-none col-md-3">
                    <div class="row g-2 align-items-center">
                        <div class="d-flex flex-column col-md-6">
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
                        <div class="d-flex flex-column col-md-6">
                            <label for="month" class="form-label mb-0">Select Month:</label>
                            <select name="month" id="month" class="form-control form-control-sm"
                                onchange="this.form.submit()">
                                @foreach (range(1, 12) as $month)
                                    <option value="{{ $month }}"
                                        {{ request('month', $selected_month) == $month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($month)->format('F') }}
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
                            <h2 class="mb-0">{{ $month_name }} {{ $selected_year }} Report</h2>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="date mb-1 text-start">
                                <p class="m-0">Month: {{ \Carbon\Carbon::parse($start_date)->format('F Y') }}</p>
                                <p class="m-0">Year: {{ $selected_year }}</p>
                            </div>
                        </td>
                    </tr>
                </table>

                <div class="table-responsive">
                    <table id="summary_report" class="table table-bordered border-dark" width="100%" cellspacing="0">
                        <thead class="">
                            <tr class="text-uppercase">
                                <th>Week</th>
                                <th>No. of Visitors</th>
                                <th>Entrance Fee</th>
                                <th>Kawa Hot Bath</th>
                                <th>Water Tubing</th>
                                <th>Picnic Table</th>
                                <th>Massage</th>
                                <th>Accommodation</th>
                                <th>Foods</th>
                                <th>Drinks</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($weeklyBreakdown->isEmpty())
                                <tr>
                                    <td colspan="11" class="text-center">No data available for this month.</td>
                                </tr>
                            @else
                                @foreach ($weeklyBreakdown as $weekNumber => $weekData)
                                    <tr>
                                        <td>Week {{ $weekNumber }}</td>
                                        <td>{{ $weekData['visitors'] }}</td>
                                        <td>₱{{ number_format($weekData['entrance_fee'], 2) }}</td>
                                        <td>₱{{ number_format($weekData['kawabath'], 2) }}</td>
                                        <td>₱{{ number_format($weekData['watertubing'], 2) }}</td>
                                        <td>₱{{ number_format($weekData['picnictable'], 2) }}</td>
                                        <td>₱{{ number_format($weekData['massage'], 2) }}</td>
                                        <td>₱{{ number_format($weekData['accommodation'], 2) }}</td>
                                        <td>₱{{ number_format($weekData['meal'], 2) }}</td>
                                        <td>₱{{ number_format($weekData['beverage'], 2) }}</td>
                                        <td>₱{{ number_format($weekData['total'], 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="">
                                    <td class="h6 text-uppercase">Grand Total:</td>
                                    <td class="h6">{{ $weeklyBreakdown->sum('visitors') }}</td>
                                    <td class="h6">₱{{ number_format($weeklyBreakdown->sum('entrance_fee'), 2) }}</td>
                                    <td class="h6">₱{{ number_format($weeklyBreakdown->sum('kawabath'), 2) }}</td>
                                    <td class="h6">₱{{ number_format($weeklyBreakdown->sum('watertubing'), 2) }}</td>
                                    <td class="h6">₱{{ number_format($weeklyBreakdown->sum('picnictable'), 2) }}</td>
                                    <td class="h6">₱{{ number_format($weeklyBreakdown->sum('massage'), 2) }}</td>
                                    <td class="h6">₱{{ number_format($weeklyBreakdown->sum('accommodation'), 2) }}</td>
                                    <td class="h6">₱{{ number_format($weeklyBreakdown->sum('meal'), 2) }}</td>
                                    <td class="h6">₱{{ number_format($weeklyBreakdown->sum('beverage'), 2) }}</td>
                                    <td class="h6">₱{{ number_format($weeklyBreakdown->sum('total'), 2) }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="col-12 d-flex justify-content-end print-footer mt-4">
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
                    '{{ asset('/public/css/styles.css') }}',
                    '{{ asset('public/css/bootstrap.min.css') }}'
                ],
            });
        }
    </script>

    <script>
        function exportExcel() {
            // Headers
            let headers = [
                "Week",
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

            // Data from each week
            let data = [
                @foreach ($weeklyBreakdown as $weekNumber => $weekData)
                    [
                        "Week {{ $weekNumber }}",
                        "{{ $weekData['visitors'] }}",
                        "{{ $weekData['entrance_fee'] }}",
                        "{{ $weekData['kawabath'] }}",
                        "{{ $weekData['watertubing'] }}",
                        "{{ $weekData['picnictable'] }}",
                        "{{ $weekData['massage'] }}",
                        "{{ $weekData['accommodation'] }}",
                        "{{ $weekData['meal'] }}",
                        "{{ $weekData['beverage'] }}",
                        "{{ $weekData['total'] }}"
                    ],
                @endforeach

                // Grand total row
                [
                    "Grand Total",
                    "{{ $weeklyBreakdown->sum('visitors') }}",
                    "{{ $weeklyBreakdown->sum('entrance_fee') }}",
                    "{{ $weeklyBreakdown->sum('kawabath') }}",
                    "{{ $weeklyBreakdown->sum('watertubing') }}",
                    "{{ $weeklyBreakdown->sum('picnictable') }}",
                    "{{ $weeklyBreakdown->sum('massage') }}",
                    "{{ $weeklyBreakdown->sum('accommodation') }}",
                    "{{ $weeklyBreakdown->sum('meal') }}",
                    "{{ $weeklyBreakdown->sum('beverage') }}",
                    "{{ $weeklyBreakdown->sum('total') }}"
                ]
            ];

            // Create Excel sheet
            let worksheet = XLSX.utils.aoa_to_sheet([headers, ...data]);
            let workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Monthly Report");

            // File name format: monthly_report_Month_Year.xlsx
            XLSX.writeFile(workbook, "monthly_report_{{ $month_name }}_{{ $selected_year }}.xlsx");
        }
    </script>
@endsection
