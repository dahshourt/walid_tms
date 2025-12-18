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
                    <h2 class="text-white font-weight-bold my-2 mr-5">Edit {{ $form_title }}</h2>
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
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">Edit {{ $form_title }}</h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ route('kpi-sub-initiatives.index') }}" class="btn btn-light-primary font-weight-bolder">
                            <i class="ki ki-long-arrow-back icon-sm"></i>
                            Back to List
                        </a>
                    </div>
                </div>
                <!--begin::Form-->
                <form method="POST" action="{{ route('kpi-sub-initiatives.update', $row->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Validation Error!</strong> Please correct the errors below.
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <!--begin::Form Group-->
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Name <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <input type="text" 
                                       name="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       placeholder="Enter KPI sub-initiative name"
                                       value="{{ old('name', $row->name) }}"
                                       required />
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!--end::Form Group-->

                        <!--begin::Form Group-->
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Initiative <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <select name="initiative_id" 
                                        class="form-control select2 @error('initiative_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Select Initiative</option>
                                    @foreach($initiatives as $initiative)
                                        <option value="{{ $initiative->id }}" {{ old('initiative_id', $row->initiative_id) == $initiative->id ? 'selected' : '' }}>
                                            {{ $initiative->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('initiative_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!--end::Form Group-->

                        <!--begin::Form Group-->
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Status <span class="text-danger">*</span></label>
                            <div class="col-lg-6">
                                <span class="switch switch-outline switch-icon switch-success">
                                    <label>
                                        <input type="checkbox" 
                                               name="status" 
                                               value="1"
                                               {{ old('status', $row->status) == '1' ? 'checked' : '' }} />
                                        <span></span>
                                    </label>
                                </span>
                                <span class="form-text text-muted">Toggle to activate/deactivate this KPI sub-initiative</span>
                                <input type="hidden" name="status" value="0" id="status-hidden">
                                @error('status')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!--end::Form Group-->
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-6">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="la la-save"></i>
                                    Update KPI Sub-Initiative
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <!--end::Form-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

@endsection

@push('script')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Select Initiative',
        allowClear: true
    });

    // Handle status checkbox
    $('input[name="status"][type="checkbox"]').on('change', function() {
        if ($(this).is(':checked')) {
            $('#status-hidden').val('1');
        } else {
            $('#status-hidden').val('0');
        }
    });

    // Initialize on page load
    if ($('input[name="status"][type="checkbox"]').is(':checked')) {
        $('#status-hidden').val('1');
    }
});
</script>
@endpush



