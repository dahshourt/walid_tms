@extends('layouts.app')

@section('content')

<div class="container" id="results">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Report:Current Status </h3>
                    <form action="{{ route('report.current-status.export') }}" method="POST">
                        @csrf
                             @if(request('cr_type'))
                                <input type="hidden" name="cr_type" value="{{ request('cr_type') }}">
                            @endif
                            @if(request('status_ids'))
                                @foreach(request('status_ids') as $status_id)
                                    <input type="hidden" name="status_ids[]" value="{{ $status_id }}">
                                @endforeach
                            @endif
                            @if(request('cr_nos'))
                                <input type="hidden" name="cr_nos" value="{{ request('cr_nos') }}">
                            @endif
                        <button type="submit" class="btn btn-success">
                            Export Table
                        </button>
                    </form>
                </div>
                 <div  class="card-header d-flex justify-content-between align-items-center">
                     <!-- 🔎 Filter Form -->
                    <form action="{{ route('report.current-status') }}" method="POST" class="mb-4">
                         @csrf
                        <div class="row g-3">
                            <div class="form-group col-md-4">
                                <label for="cr_type">Workflow</label>
                                <select class="form-control form-control-lg" id="cr_type" name="cr_type">
                                    <option value="" selected>Select ...</option>
                                    @if(isset($workflow_type))
                                        @foreach($workflow_type as $item)
                                            <option value="{{ $item->id }}" 
                                                {{ request('cr_type') == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                {!! $errors->first('cr_type', '<span class="form-control-feedback">:message</span>') !!}
                            </div>

                            <div class="form-group col-md-4">
                                <label for="status_ids">Status IDs</label>
                                <select class="form-control form-control-lg select2" id="status_ids" name="status_ids[]" multiple>
                                    @if(isset($status))
                                        @foreach($status as $item)
                                            <option value="{{ $item->id }}"
                                                {{ collect(request('status_ids'))->contains($item->id) ? 'selected' : '' }}>
                                                {{ $item->status_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                {!! $errors->first('status_ids', '<span class="form-control-feedback">:message</span>') !!}
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">CR Numbers</label>
                                <input type="text" name="cr_nos" class="form-control"
                                        value="{{ request('cr_nos') }}"
                                        placeholder="CR001,CR005">
                                <small class="text-muted">Comma separated</small>
                            </div>

                            <div class="form-group">
                                <button   class="btn btn-primary w-100">
                                    🔍  Search
                                </button>
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
