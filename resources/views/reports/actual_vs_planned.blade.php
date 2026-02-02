@extends('layouts.app')

@section('content')

<div class="container" id="results">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Report: Actual vs Planned</h3>

                <a href="{{ request()->fullUrlWithQuery(['export' => 1]) }}" class="btn btn-success">
                    Export Table
                </a>
            </div>
                <div class="card-header">
                     <form action="{{ route('reports.actual_vs_planned') }}" method="GET">
                        <div class="row align-items-center">
                            <div class="col-md-10">
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
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Search</button>
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
