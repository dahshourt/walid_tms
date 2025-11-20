<div class="card-body">


    @if($errors->any())
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

    <div class="form-group form-group-last">

    </div>


    <div class="form-group">
        <label>CR Number <span class="text-danger">*</span>
            <small class="text-muted">(must match an existing CR ID)</small>
        </label>
        <input type="text" class="form-control form-control-lg" placeholder="CR NO" name="change_request_id"
               value="{{ old('change_request_id') }}"/>
        {!! $errors->first('change_request_id', '<span class="form-control-feedback">:message</span>') !!}
    </div>


</div>

@push('script')



@endpush
