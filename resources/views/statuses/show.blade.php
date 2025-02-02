@extends('admin.layouts.index_layout',['title' => __("admin.$route") ])

@section('content')

@if( session('status') )
<div class="m-alert m-alert--icon m-alert--air alert alert-success alert-dismissible fade show" role="alert">
    {{-- <div class="m-alert__icon">
        <i class="la la-warning"></i>
    </div> --}}
    <div class="m-alert__text">
        <strong>{{ session('status') }}</strong>
    </div>
    <div class="m-alert__close">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        </button>
    </div>
</div>
@endif

<!--begin::Portlet-->
<div class="m-portlet">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-name">
                <span class="m-portlet__head-icon m--hide">
                    <i class="la la-gear"></i>
                </span>
                <h3 class="m-portlet__head-text">
                   {{ __('admin.Show') }} 
                </h3>
            </div>
        </div>
    </div>

    <!--begin::Form-->
    <form class="m-form" action='{{url("admin/$route")}}/{{ $row['id'] }}' method="post" enctype="multipart/form-data">

            {{ csrf_field() }}
            {{ method_field('PATCH') }}
            <input type="hidden" name="id" value="{{$row['id']}}">
            @include("admin.$view.form")
    </form>

    <!--end::Form-->
</div>

<!--end::Portlet-->

@endsection