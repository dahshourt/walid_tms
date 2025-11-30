@extends('layouts.app')

@section('content')
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
                        <h2 class="text-white font-weight-bold my-2 mr-5">Group -</h2>
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
                <div class="row">
                    <div class="col-md-12">
                        <!--begin::Card-->
                        <div class="card card-custom gutter-b example example-compact">
                            <div class="card-header">
                                <h3 class="card-title">Group ({{ $group->name }}) Statuses</h3>
                            </div>
                            <!--begin::Form-->
                            <form class="form" action='{{url("$OtherRoute")}}/store/statuses/{{ $group->id }}' method="post" enctype="multipart/form-data">
                                @csrf
                                @php
                                    $view_statuses_ids = $group->group_statuses->where('type', 2)->pluck('status_id')->toArray();
                                    $set_statuses_ids = $group->group_statuses->where('type', 1)->pluck('status_id')->toArray();
                                @endphp 

                                <div class="card-body">
                                    @if ($errors->any())
                                        <div class="m-alert m-alert--icon alert alert-danger" role="alert" id="m_form_1_msg">
                                            <div class="m-alert__icon">
                                                <i class="la la-warning"></i>
                                            </div>
                                            <div class="m-alert__text">
                                                There are some errors
                                            </div>
                                            <div class="m-alert__close">
                                                <button type="button" class="close" data-close="alert" aria-label="Close">
                                                </button>
                                            </div>
                                        </div>
                                    @endif


                                    
                                    <div class="form-group">
                                        <label>Group Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg"
                                            placeholder="Group Name" 
                                            value="{{ isset($group) ? $group->name : old('name') }}" disabled />
                                        {!! $errors->first('name', '<span class="form-control-feedback">:message</span>') !!}
                                    </div>




                                    <div class="form-group">
                                        <label for="view_group_id">View Statuses:</label>
                                        <select class="form-control form-control-lg" id="view_group_id"
                                            name="view_statuses_id[]" multiple="multiple">
                                            <option value=""> Select</option>
                                            @foreach ($statuses as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ in_array($item->id, $view_statuses_ids ?? []) ? 'selected' : '' }}>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('view_statuses_id', '<span class="form-control-feedback">:message</span>') !!}
                                    </div>

                                    <div class="form-group">
                                        <label for="set_group_id">Set Statuses <span class="text-danger">*</span></label>
                                        <select class="form-control form-control-lg" id="set_group_id"
                                            name="set_statuses_id[]" multiple="multiple">
                                            <option value=""> Select</option>
                                            @foreach ($statuses as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ in_array($item->id, $set_statuses_ids ?? []) ? 'selected' : '' }}>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('set_    statuses_id', '<span class="form-control-feedback">:message</span>') !!}
                                    </div>


                                <div class="card-footer">
                                    <button type="submit" class="btn btn-success mr-2">Submit</button>
                                      <a href='{{ url("$route") }}'class="btn btn-secondary">Cancel</a>
                                </div>
                              
                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Card-->
                    </div>
                </div>
            </div>
            <!--end::Container-->
        </div>
        <!--end::Entry-->
    </div>
    <!--end::Content-->
@endsection
