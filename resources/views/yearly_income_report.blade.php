@extends('layouts.auth')
@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Yearly Income Report</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-end align-items-start mb-4">
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
                            <h2 class="mb-0">Yearly Income Report</h2>
                        </td>
                    </tr>
                </table>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead class="">
                            <tr>
                                <th>Year</th>
                                <th>No. of Visitors</th>
                                <th>Total Bill Income</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($yearlyBreakdown->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center">No data available.</td>
                                </tr>
                            @else
                                @foreach ($yearlyBreakdown as $data)
                                    <tr>
                                        <td>{{ $data['year'] }}</td>
                                        <td>{{ $data['visitors'] }}</td>
                                        <td>₱{{ number_format($data['total'], 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="fw-bold">
                                    <td class="h6">Grand Total:</td>
                                    <td class="h6">{{ $totalVisitors }}</td>
                                    <td class="h6">₱{{ number_format($grandTotal, 2) }}</td>
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
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css'
                ],
            });
        }
    </script>
@endsection
