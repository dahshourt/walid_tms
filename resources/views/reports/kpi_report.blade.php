@extends('layouts.app')

@section('content')

<div class="container" id="results">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Report: KPI Report</h3>
                </div>
                 <div  class="card-header d-flex justify-content-between align-items-center">
                     <!-- 🔎 Filter Form -->
                    <form action="{{ route('reports.kpi_report') }}" method="GET" class="mb-4 w-100">
                        <div class="row g-3">
                            <div class="form-group col-md-3">
                                <label for="from_date">From Date</label>
                                <input type="date" class="form-control" id="from_date" name="from_date" value="{{ request('from_date') }}">
                            </div>

                            <div class="form-group col-md-3">
                                <label for="to_date">To Date</label>
                                <input type="date" class="form-control" id="to_date" name="to_date" value="{{ request('to_date') }}">
                            </div>

                            <div class="form-group col-md-3">
                                <label for="unit_id">Unit</label>
                                <select class="form-control" id="unit_id" name="unit_id">
                                    <option value="">Select Unit...</option>
                                    @if(isset($units))
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="status_name">Status</label>
                                <select class="form-control" id="status_name" name="status_name">
                                    <option value="">Select Status...</option>
                                    @if(isset($statuses))
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->status_name }}" {{ request('status_name') == $status->status_name ? 'selected' : '' }}>
                                                {{ $status->status_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="department_id">Department</label>
                                <select class="form-control" id="department_id" name="department_id">
                                    <option value="">Select Department...</option>
                                    @if(isset($departments))
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group col-md-3 align-self-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    🔍  Search
                                </button>
                            </div>
                        </div>
                    </form>
                 </div>
                <div class="card-body">
                    @if($results->count())
                        <!-- Responsive table wrapper -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        @foreach(array_keys((array)$results->first()) as $column)
                                            <th>{{ ucwords(str_replace('_', ' ', $column)) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $row)
                                        <tr>
                                            @foreach((array)$row as $value)
                                                <td>{{ $value }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $results->links() }}
                        </div>

                        <!-- KPI Summary Table -->
                        @if(isset($kpiStats) && count($kpiStats) > 0)
                            <div class="mt-5">
                                <h4>KPI Summary</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Metric</th>
                                                <th>Total (Applicable)</th>
                                                <th>Met</th>
                                                <th>Achieved %</th>
                                                <th>Result</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($kpiStats as $stat)
                                                <tr>
                                                    <td>{{ $stat['name'] }}</td>
                                                    <td>{{ $stat['total'] }}</td>
                                                    <td>{{ $stat['meet'] }}</td>
                                                    <td>{{ $stat['percentage'] }}%</td>
                                                    <td>
                                                        @if($stat['percentage'] >= 95)
                                                            <span class="badge badge-success">Excellent</span>
                                                        @elseif($stat['percentage'] >= 75)
                                                            <span class="badge badge-info">Good</span>
                                                        @elseif($stat['percentage'] >= 50)
                                                            <span class="badge badge-warning">Fair</span>
                                                        @else
                                                            <span class="badge badge-danger">Poor</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Modern Chart Section -->
                            <div class="mt-5 mb-5">
                                <h4>KPI Performance Chart</h4>
                                <div class="card">
                                    <div class="card-body">
                                        <canvas id="kpiChart" style="max-height: 500px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <p>No results found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('css')
<style>
    /* Prevent table cells from wrapping and allow horizontal scroll */
    .table-responsive {
        overflow-x: auto;
    }
    .table th, .table td {
        white-space: nowrap;
    }
</style>
@endpush

@push('script')
<script src="{{ asset('public/new_theme/assets/js/chart.min.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if(isset($kpiStats) && count($kpiStats) > 0)
            const kpiData = @json($kpiStats);
            
            const labels = kpiData.map(item => item.name);
            const percentages = kpiData.map(item => item.percentage);
            
            // Dynamic colors based on percentage
            const backgroundColors = percentages.map(value => {
                if (value >= 95) return 'rgba(40, 167, 69, 0.7)'; // Success Green
                if (value >= 75) return 'rgba(23, 162, 184, 0.7)'; // Info Blue
                if (value >= 50) return 'rgba(255, 193, 7, 0.7)'; // Warning Yellow
                return 'rgba(220, 53, 69, 0.7)'; // Danger Red
            });

            const borderColors = percentages.map(value => {
                if (value >= 95) return 'rgba(40, 167, 69, 1)';
                if (value >= 75) return 'rgba(23, 162, 184, 1)';
                if (value >= 50) return 'rgba(255, 193, 7, 1)';
                return 'rgba(220, 53, 69, 1)';
            });

            const ctx = document.getElementById('kpiChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Achieved Percentage (%)',
                        data: percentages,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Percentage (%)'
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false 
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.raw + '%';
                                }
                            }
                        }
                    }
                }
            });
        @endif
    });
</script>
@endpush
