@extends('layouts.auth')
@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Yearly Report</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <form method="GET" action="{{ route('yearly.report') }}" class="d-print-none col-md-3">
                    <div class="row g-2 align-items-center">
                        <div class="d-flex flex-column col-md-6">
                            <label for="year" class="form-label mb-0">Select Year:</label>
                            <select name="year" id="year" class="form-control form-control-sm"
                                onchange="this.form.submit()">
                                @for ($y = date('Y'); $y >= 2024; $y--)
                                    <option value="{{ $y }}"
                                        {{ request('year', $selected_year) == $y ? 'selected' : '' }}>{{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </form>

                <div class="print-buttons">
                    <button onclick="printReport()" class="btn btn-sm btn-primary d-print-none">
                        <i class="fas fa-print"></i> Print Report
                    </button>

                    <button onclick="exportYearlyExcel()" class="btn btn-sm btn-success d-print-none">
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
                            <h2 class="mb-0">Year {{ $year_name }} Report</h2>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="date mb-1 text-start">
                                <p class="m-0">Year: {{ $selected_year }}</p>
                            </div>
                        </td>
                    </tr>
                </table>

                <div class="table-responsive">
                    <table id="summary_report" class="table table-bordered" width="100%" cellspacing="0">
                        <thead class="">
                            <tr class="text-uppercase">
                                <th>Month</th>
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
                            @if ($monthlyBreakdown->isEmpty())
                                <tr>
                                    <td colspan="11" class="text-center">No data available for this year.</td>
                                </tr>
                            @else
                                @foreach ($monthlyBreakdown as $monthNumber => $monthData)
                                    <tr>
                                        <td>{{ $monthData['month_name'] }}</td>
                                        <td>{{ $monthData['visitors'] }}</td>
                                        <td>₱{{ number_format($monthData['entrance_fee'], 2) }}</td>
                                        <td>₱{{ number_format($monthData['kawabath'], 2) }}</td>
                                        <td>₱{{ number_format($monthData['watertubing'], 2) }}</td>
                                        <td>₱{{ number_format($monthData['picnictable'], 2) }}</td>
                                        <td>₱{{ number_format($monthData['massage'], 2) }}</td>
                                        <td>₱{{ number_format($monthData['accommodation'], 2) }}</td>
                                        <td>₱{{ number_format($monthData['meal'], 2) }}</td>
                                        <td>₱{{ number_format($monthData['beverage'], 2) }}</td>
                                        <td>₱{{ number_format($monthData['total'], 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="">
                                    <td class="h6 text-uppercase">Grand Total:</td>
                                    <td class="h6">{{ $monthlyBreakdown->sum('visitors') }}</td>
                                    <td class="h6">₱{{ number_format($monthlyBreakdown->sum('entrance_fee'), 2) }}</td>
                                    <td class="h6">₱{{ number_format($monthlyBreakdown->sum('kawabath'), 2) }}</td>
                                    <td class="h6">₱{{ number_format($monthlyBreakdown->sum('watertubing'), 2) }}</td>
                                    <td class="h6">₱{{ number_format($monthlyBreakdown->sum('picnictable'), 2) }}</td>
                                    <td class="h6">₱{{ number_format($monthlyBreakdown->sum('massage'), 2) }}</td>
                                    <td class="h6">₱{{ number_format($monthlyBreakdown->sum('accommodation'), 2) }}
                                    </td>
                                    <td class="h6">₱{{ number_format($monthlyBreakdown->sum('meal'), 2) }}</td>
                                    <td class="h6">₱{{ number_format($monthlyBreakdown->sum('beverage'), 2) }}</td>
                                    <td class="h6">₱{{ number_format($monthlyBreakdown->sum('total'), 2) }}</td>
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
        function exportYearlyExcel() {
            // Headers for Excel
            const headers = [
                "Month",
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

            // Data rows from Blade
            const data = [
                @foreach ($monthlyBreakdown as $monthData)
                    [
                        "{{ $monthData['month_name'] }}",
                        "{{ $monthData['visitors'] ?? 0 }}",
                        "{{ $monthData['entrance_fee'] ?? 0 }}",
                        "{{ $monthData['kawabath'] ?? 0 }}",
                        "{{ $monthData['watertubing'] ?? 0 }}",
                        "{{ $monthData['picnictable'] ?? 0 }}",
                        "{{ $monthData['massage'] ?? 0 }}",
                        "{{ $monthData['accommodation'] ?? 0 }}",
                        "{{ $monthData['meal'] ?? 0 }}",
                        "{{ $monthData['beverage'] ?? 0 }}",
                        "{{ $monthData['total'] ?? 0 }}"
                    ],
                @endforeach

                // Add Grand Total row
                [
                    "Grand Total",
                    "{{ $monthlyBreakdown->sum('visitors') }}",
                    "{{ $monthlyBreakdown->sum('entrance_fee') }}",
                    "{{ $monthlyBreakdown->sum('kawabath') }}",
                    "{{ $monthlyBreakdown->sum('watertubing') }}",
                    "{{ $monthlyBreakdown->sum('picnictable') }}",
                    "{{ $monthlyBreakdown->sum('massage') }}",
                    "{{ $monthlyBreakdown->sum('accommodation') }}",
                    "{{ $monthlyBreakdown->sum('meal') }}",
                    "{{ $monthlyBreakdown->sum('beverage') }}",
                    "{{ $monthlyBreakdown->sum('total') }}"
                ]
            ];

            // Convert to Excel sheet
            const worksheet = XLSX.utils.aoa_to_sheet([headers, ...data]);

            // Auto-width columns
            worksheet["!cols"] = headers.map(h => ({
                wch: Math.max(h.length, 12)
            }));

            // Create workbook and append sheet
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Yearly Report");

            // File name example: yearly_report_2025.xlsx
            const filename = `yearly_report_{{ $selected_year }}.xlsx`;

            XLSX.writeFile(workbook, filename);
        }
    </script>
@endsection
