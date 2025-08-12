@extends('layouts.auth')
@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Weekly Income Report</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <form method="GET" action="{{ route('weekly.income.report') }}" class="d-print-none">
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
                    </div>
                </form>

                <div class="print-buttons">
                    <button onclick="printReport()" class="btn btn-sm btn-primary d-print-none">
                        <i class="fas fa-print"></i> Print Report
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
                            <h2 class="mb-0">Weekly Income Report for {{ $month_name }}
                                {{ $selected_year }}</h2>
                        </td>
                    </tr>
                    {{-- <tr>
                        <td>
                            <div class="date mb-1 text-start">
                                <p class="m-0">Year:
                                    {{ \Carbon\Carbon::createFromDate($selected_year, $selected_month, 1)->format('Y') }}
                                </p>
                                <p class="m-0">Month:
                                    {{ \Carbon\Carbon::createFromDate($selected_year, $selected_month, 1)->format('F') }}
                                </p>
                            </div>
                        </td>
                    </tr> --}}
                </table>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Week</th>
                                <th>No. of Visitors</th>
                                <th>Total Bill Income</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($weeklyTotal->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center">No data available for this month.</td>
                                </tr>
                            @else
                                @foreach ($weeklyTotal as $weekNumber => $weekData)
                                    <tr>
                                        @if ($loop->first)
                                            <td class="align-middle" rowspan="{{ $weeklyTotal->count() }}">
                                                {{ $month_name }}</td>
                                        @endif
                                        <td>Week {{ $weekNumber }}</td>
                                        <td>{{ $weekData['visitors'] }}</td>
                                        <td>₱{{ number_format($weekData['total'], 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="bg-light">
                                    <td colspan="2" class="h6 text-start">Grand Total:</td>
                                    <td class="h6">{{ $grandTotal['visitors'] }}</td>
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
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css'
                ],
            });
        }
    </script>
@endsection
