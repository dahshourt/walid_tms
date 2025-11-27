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
        <!-- Any additional form group content can go here -->
    </div>

    <div class="form-group">
        <label>Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" 
               placeholder="Department Name" name="name"
               value="{{ isset($row) ? $row->name : old('name') }}" required autofocus/>
        @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    
    <div class="form-group">
        <label>Status</label>
        <div class="checkbox-inline">
            <label class="checkbox">
                <input type="checkbox" name="active" value="1" 
                    {{ (isset($row) && $row->active == 1) || old('active', 1) == 1 ? 'checked' : '' }}>
                <span></span>Active
            </label>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Any additional JavaScript can go here
</script>
@endpush
