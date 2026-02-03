@extends('layouts.app')

@section('content')

<div class="container" id="results">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Report: SLA Report</h3>
                    <a href="{{ request()->fullUrlWithQuery(['export' => 1]) }}" class="btn btn-success">
                        Export Excel
                    </a>
                </div>
                 <div  class="card-header d-flex justify-content-between align-items-center">
                     <!-- 🔎 Filter Form -->
                    <form action="{{ route('reports.sla_report') }}" method="GET" class="mb-4 w-100">
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

                        </div>
                         <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="relevant_with" id="relevant_with" value="3" {{ request('relevant_with') == 3 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="relevant_with">Relevant with</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="depend_on" id="depend_on" value="2" {{ request('depend_on') == 2 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="depend_on">Depend On</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="top_management" id="top_management" value="1" {{ request('top_management') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="top_management">Top Management</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="on_hold" id="on_hold" value="1" {{ request('on_hold') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="on_hold">ON-Hold</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="on_behalf" id="on_behalf" value="1" {{ request('on_behalf') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="on_behalf">On Behalf</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                             <div class="form-group col-md-3">
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
