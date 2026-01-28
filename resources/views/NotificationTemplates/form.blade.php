<div class="card-body">

@if($errors->any())
    <div class="m-alert m-alert--icon alert alert-danger" role="alert" id="m_form_1_msg">
        <div class="m-alert__icon">
            <i class="la la-warning"></i>
        </div>
        <div class="m-alert__text">
            There are some errors:
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <div class="m-alert__close">
            <button type="button" class="close" data-close="alert" aria-label="Close">
            </button>
        </div>
    </div>
@endif

@if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('status') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

    <div class="form-group">
        <label for="template_name">Template Name: <span class="text-danger">*</span></label>
        <input class="form-control form-control-lg" id="template_name" name="template_name" 
               value="{{ isset($row) ? $row->name : old('template_name') }}" 
               placeholder="Enter template name" required>
    </div>
    
    <div class="form-group">
        <label for="template_subject">Template Subject: <span class="text-danger">*</span></label>
        <input class="form-control form-control-lg" id="template_subject" name="template_subject" 
               value="{{ isset($row) ? $row->subject : old('template_subject') }}"
               placeholder="Enter email subject line" required>
    </div>
    
    <div class="form-group">
        <label for="template_body">Template Body: <span class="text-danger">*</span></label>
        <textarea class="form-control form-control-lg" id="summernote" name="template_body" rows="10" cols="50">{{ isset($row) ? $row->body : old('template_body') }}</textarea>
        <small class="form-text text-muted">Use placeholders like cr_number, requester_name, etc.</small>
    </div>
    
    <div class="form-group">
        <label for="available_placeholder">Available Placeholders:</label>
        @php
            $placeholders = '';
            if (isset($row) && $row->available_placeholders) {
                $placeholders = is_array($row->available_placeholders) 
                    ? implode(', ', $row->available_placeholders) 
                    : $row->available_placeholders;
            } else {
                $placeholders = old('available_placeholder', '');
            }
        @endphp
        <input class="form-control form-control-lg" id="available_placeholder" name="available_placeholder" 
               value="{{ $placeholders }}"
               placeholder="e.g. cr_number, requester_name, status">
        <small class="form-text text-muted">Comma-separated list of placeholders that can be used in this template</small>
    </div>
    
    <div class="form-group">
        <div class="checkbox-inline">
            <label class="checkbox checkbox-success">
                <input type="checkbox" name="is_active" value="1" 
                       {{ (isset($row) && $row->is_active) || (!isset($row) && old('is_active', true)) ? 'checked' : '' }}>
                <span></span>
                Active
            </label>
        </div>
        <small class="form-text text-muted">Only active templates can be used in notification rules</small>
    </div>
    
</div>

@push('script')
<script>
    $('#summernote').summernote({
        tabsize: 2,
        height: 200,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
</script>
@endpush