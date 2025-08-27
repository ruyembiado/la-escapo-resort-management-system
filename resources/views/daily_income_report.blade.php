@extends('layouts.auth')
@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Daily Income Report</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <form method="GET" action="{{ route('daily.income.report') }}" class="d-print-none">
                    <div class="d-flex gap-2 align-items-center">
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
                                @foreach (range(1, 12) as $month)
                                    <option value="{{ $month }}"
                                        {{ request('month', $selected_month) == $month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Week Selector -->
                        <div class="d-flex flex-column">
                            <label for="week" class="form-label mb-0">Select Week:</label>
                            <select name="week" id="week" class="form-control form-control-sm"
                                onchange="this.form.submit()">
                                @foreach (range(1, \Carbon\Carbon::createFromDate($selected_year, $selected_month, 1)->endOfMonth()->weekOfMonth) as $week)
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
                            <h2 class="mb-0">Daily Income Report</h2>
                        </td>
                    </tr>
                </table>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Day</th>
                                <th>No. of Visitors</th>
                                <th>Total Bill Income</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dailyReport as $day)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($day['date'])->format('F d, Y') }}</td>
                                    <td>{{ $day['day'] }}</td>
                                    <td>{{ $day['visitors'] }}</td>
                                    <td>₱{{ number_format($day['total'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No data available for this month.</td>
                                </tr>
                            @endforelse
                            @if (count($dailyReport))
                                <tr>
                                    <td colspan="2" class="h6">Grand Total:</td>
                                    <td class="h6">{{ number_format(collect($dailyReport)->sum('visitors')) }}</td>
                                    <td class="h6">₱{{ number_format(collect($dailyReport)->sum('total'), 2) }}</td>
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
@endsection
