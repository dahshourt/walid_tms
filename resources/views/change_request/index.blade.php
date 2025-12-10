@extends('layouts.app')

@section('content')
    @php
        $user_group = session()->has('current_group') ? session('current_group') : auth()->user()->defualt_group->id;
        $user_group =\App\Models\Group::find($user_group);
    @endphp

        <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
            <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <!--begin::Heading-->
                    <div class="d-flex flex-column">
                        <!--begin::Title-->
                        <h2 class="text-white font-weight-bold my-2 mr-5">{{ $title }}</h2>
                        <!--end::Title-->

                    </div>
                    <!--end::Heading-->
                </div>
                <!--end::Info-->
            </div>
        </div>
        <!--end::Subheader-->
        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container">

                <!--begin::Card-->
                <div class="card">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">{{ $title }}
                        </div>
                        <div class="card-toolbar">

                            @can('Create ChangeRequest')

                                <!--begin::Button-->
                                <a href='{{ url("$route/workflow/type") }}' class="btn btn-primary font-weight-bolder">
											<span class="svg-icon svg-icon-md">
												<!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg-->
												<svg xmlns="http://www.w3.org/2000/svg"
                                                     xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                     height="24px" viewBox="0 0 24 24" version="1.1">
													<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
														<rect x="0" y="0" width="24" height="24"/>
														<circle fill="#000000" cx="9" cy="15" r="6"/>
														<path
                                                            d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z"
                                                            fill="#000000" opacity="0.3"/>
													</g>
												</svg>
                                                <!--end::Svg Icon-->
											</span>New Record</a>
                                <!--end::Button-->
                            @endcan
                        </div>
                    </div>
                    @php
                        $roles_name = auth()->user()->roles->pluck('name');
                    @endphp
                    <div class="card-body">
                        @php
                            // Filter workflows to only show those with CRs
                            $workflows_with_crs = $active_work_flows->whereIn('id', array_keys($crs_by_work_flow_types));
                        @endphp

                        @if($workflows_with_crs->count() > 0)
                            <!--begin: Tabs Navigation-->
                            <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-3x nav-tabs-line-primary mb-5"
                                role="tablist">
                                @foreach($workflows_with_crs as $index => $workflow)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                           data-toggle="tab"
                                           href="#workflow_tab_{{ $workflow->id }}"
                                           role="tab"
                                           aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                            <span class="nav-text font-weight-bold">{{ $workflow->name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <!--end: Tabs Navigation-->

                            <!--begin: Tab Content-->
                            <div class="tab-content">
                                @foreach($workflows_with_crs as $index => $workflow)
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                         id="workflow_tab_{{ $workflow->id }}"
                                         role="tabpanel">

                                        @php
                                            $collection = $crs_by_work_flow_types[$workflow->id];
                                        @endphp

                                            <!--begin: Datatable-->
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>ID#</th>
                                                    <th>Title</th>
                                                    {{--<th>Status</th>--}}
                                                    @if(!empty($roles_name) && isset($roles_name[0]) && $roles_name[0] != "Viewer")
                                                        <th>Design Duration</th>
                                                        <th>Start Design Time</th>
                                                        <th>End Design Time</th>
                                                        <th>Development Duration</th>
                                                        <th>Start Development Time</th>
                                                        <th>End Development Time</th>
                                                        <th>Test Duration</th>
                                                        <th>Start Test Time</th>
                                                        <th>End Test Time</th>
                                                        <th>CR Duration</th>
                                                        <th>Start CR Time</th>
                                                        <th>End CR Time</th>
                                                    @endif
                                                    @canany(['Edit ChangeRequest' , 'Show ChangeRequest'])
                                                        {{--<th>Actions</th>--}}
                                                    @endcanany
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @include("$view.loop")
                                                </tbody>
                                            </table>
                                            <!--end: Datatable-->
                                        </div>

                                        <!--begin: Pagination-->
                                        <div class="d-flex justify-content-center mt-5">
                                            {{ $collection->links() }}
                                        </div>
                                        <!--end: Pagination-->
                                    </div>
                                @endforeach
                            </div>
                            <!--end: Tab Content-->
                        @else
                            <!--begin: No Data State-->
                            <div class="alert alert-light text-center" role="alert">
                                <i class="la la-inbox text-muted" style="font-size: 3rem;"></i>
                                <h4 class="text-muted mt-3">No Change Requests Found</h4>
                                <p class="text-muted mb-0">There are currently no change requests available.</p>
                            </div>
                            <!--end: No Data State-->
                        @endif
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::Entry-->
        </div>
        <!--end::Content-->
    </div>

@endsection

@push('css')
    <style>
        /* Enhanced Tab Styling */
        .nav-tabs-line-3x .nav-link {
            font-size: 1rem;
            padding: 1rem 1.5rem;
            color: #7e8299;
            transition: all 0.3s ease;
        }

        .nav-tabs-line-3x .nav-link:hover {
            color: #3699ff;
        }

        .nav-tabs-line-3x .nav-link.active {
            color: #3699ff;
            font-weight: 600;
        }

        .nav-tabs-line-3x .nav-link .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.42rem;
        }

        /* Tab content styling */
        .tab-content {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Empty state styling */
        .alert-light {
            background-color: #f8f9fa;
            border: 1px dashed #dee2e6;
            padding: 2rem;
        }
    </style>
@endpush

@push('script')

    <script>
        $(function () {
            $("#example1").DataTable({
                'responsive': false,
                'lengthChange': false,
                'autoWidth': true,
                'ordering': false,
                'buttons': ['excel']
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                'paging': false,
                'lengthChange': false,
                'searching': false,
                'ordering': true,
                'info': false,
                'autoWidth': true,
                'responsive': false,
                'scrollX': true,
                order: [[0, 'desc']]

            });
        });

    </script>

    <script>
        $(document).on('click', '.js-toggle-cr-details', function (e) {
            e.preventDefault();
            var $btn = $(this);
            var id = $btn.data('cr-id');
            var $row = $btn.closest('tr');
            console.log("clicked", id, $row);
            var $details = $('tr.cr-details-row[data-cr-id="' + id + '"]');
            var expanded = $btn.attr('aria-expanded') === 'true';

            if (expanded) {
                $btn.attr('aria-expanded', 'false');
                $btn.find('i.la').removeClass('la-angle-up').addClass('la-angle-down');
                $details.hide();
            } else {
                $btn.attr('aria-expanded', 'true');
                $btn.find('i.la').removeClass('la-angle-down').addClass('la-angle-up');
                if ($details.prev()[0] !== $row[0]) {
                    $details.insertAfter($row);
                }
                $details.show();
            }
        });

        $(document).on('click', 'tr.cr-row', function (e) {
            if ($(e.target).closest('a, button, .js-toggle-cr-details, .dropdown-menu, .select2-container').length) {
                return;
            }
            $(this).find('.js-toggle-cr-details').trigger('click');
        });
        $(function () {
            $('tr.cr-row:first').find('.js-toggle-cr-details').trigger('click');
        });
    </script>

    <script>
        // Tab persistence and pagination handling
        $(document).ready(function () {
            // Check for any type_X parameter in URL to determine active tab
            const urlParams = new URLSearchParams(window.location.search);
            let activeTabId = null;

            // Check for type_X parameters to determine active tab
            for (let [key, value] of urlParams.entries()) {
                if (key.startsWith('type_')) {
                    activeTabId = key.replace('type_', '');
                    break;
                }
            }

            if (activeTabId) {
                // Activate the tab from URL parameter
                const tabLink = $('a[href="#workflow_tab_' + activeTabId + '"]');
                if (tabLink.length) {
                    $('.nav-tabs a').removeClass('active');
                    $('.tab-pane').removeClass('show active');
                    tabLink.addClass('active').attr('aria-selected', 'true');
                    $('#workflow_tab_' + activeTabId).addClass('show active');
                }
            }

            // When clicking on tabs, update URL without reloading
            const target_element = $('.nav-tabs a[data-toggle="tab"]');

            target_element.on('shown.bs.tab', function (e) {
                const tabId = $(e.target).attr('href').replace('#workflow_tab_', '');

                // Build new URL preserving all existing parameters except old type_X ones
                const currentParams = new URLSearchParams(window.location.search);
                const newParams = new URLSearchParams();

                // Keep all parameters except type_X ones (to reset pagination when switching tabs)
                for (let [key, value] of currentParams.entries()) {
                    if (!key.startsWith('type_')) {
                        newParams.append(key, value);
                    }
                }

                // Add the type_X parameter with page 1 (default)
                newParams.set('type_' + tabId, '1');

                const newUrl = window.location.pathname + '?' + newParams.toString();
                window.history.pushState({path: newUrl}, '', newUrl);
            });

            // No need to update pagination links - they already have type_X parameter from Laravel
        });
    </script>
@endpush

