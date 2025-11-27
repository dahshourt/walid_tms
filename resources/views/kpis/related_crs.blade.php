@php
    $isView = request()->routeIs('*.show');
@endphp
    <!-- Section: Linked Change Requests -->
    @if(isset($row))
    <div class="card card-custom card-stretch gutter-b">
        <div class="card-header border-0 pt-5">
            <h3 class="card-title font-weight-bolder">Related Change Requests</h3>
        </div>
        <div class="card-body">
            @if(!$isView)
            <div class="alert alert-custom alert-light-primary fade show mb-5" role="alert">
                <div class="alert-icon"><i class="flaticon2-search-1"></i></div>
                <div class="alert-text">
                    <div class="input-group">
                        <input type="text" id="kpi_cr_no" class="form-control" placeholder="Enter CR number to link...">
                        <div class="input-group-append">
                            <button type="button" id="kpi_cr_search_btn" class="btn btn-primary font-weight-bold">Search & Link</button>
                        </div>
                    </div>
                    <small id="kpi_cr_search_message" class="form-text text-danger mt-2 font-weight-bold"></small>
                </div>
            </div>

            <div id="kpi_cr_search_result" class="card card-custom bg-light-success gutter-b" style="display:none;">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <span class="font-weight-bolder mr-2">Found:</span>
                        <span id="kpi_cr_result_no" class="font-weight-bold mr-3"></span>
                        <span id="kpi_cr_result_title" class="mr-3"></span>
                        <span class="label label-inline label-white mr-3" id="kpi_cr_result_status"></span>
                    </div>
                    <div>
                        <a href="#" target="_blank" id="kpi_cr_result_link" class="btn btn-sm btn-light-primary font-weight-bold mr-2">View CR</a>
                        <button type="button" id="kpi_cr_attach_btn" class="btn btn-sm btn-success font-weight-bold">Link to KPI</button>
                    </div>
                </div>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-head-custom table-vertical-center" id="kt_advance_table_widget_1">
                    <thead>
                        <tr class="text-left">
                            <th class="pl-0" style="width: 100px">CR #</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Workflow</th>
                            @if(!$isView)
                                <th class="text-right pr-0">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="kpi_cr_table_body">
                        @php
                            $linkedCrs = isset($changeRequests) ? $changeRequests : ($row->changeRequests ?? collect());
                        @endphp
                        @forelse($linkedCrs as $cr)
                            <tr data-cr-id="{{ $cr->id }}">
                                <td class="pl-0 font-weight-bolder">{{ $cr->cr_no }}</td>
                                <td>
                                    <a href="{{ route('show.cr', $cr->id) }}" target="_blank" class="text-dark-75 text-hover-primary font-weight-bold">{{ $cr->title }}</a>
                                </td>
                                <td>
                                    <span class="label label-lg label-light-info label-inline font-weight-bold">{{ optional(optional($cr->CurrentRequestStatuses)->status)->status_name ?? '-' }}</span>
                                </td>
                                <td>{{ $cr->workflowType->name ?? '-' }}</td>
                                @if(!$isView)
                                <td class="text-right pr-0">
                                    <button type="button" class="btn btn-icon btn-light-danger btn-sm js-detach-cr" data-cr-id="{{ $cr->id }}" title="Remove">
                                        <i class="flaticon2-trash"></i>
                                    </button>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr class="no-records">
                                <td colspan="{{ $isView ? 4 : 5 }}" class="text-center text-muted font-weight-bold py-5">No Change Requests linked to this KPI.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
