<div class="card-body">
    <!-- Error Message Section -->
    @if($errors->any())
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="la la-warning la-2x mr-3"></i>
            <div>
                <strong>Validation Error!</strong> Please correct the errors below.
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Row 1: CR Number and Resuming Date -->
    <div class="row">
        <!-- CR Number Field -->
        <div class="col-md-6 col-lg-6">
            <div class="form-group mb-4 mb-lg-5">
                <label for="change_request_id" class="font-weight-bold text-dark">
                    <i class="la la-file-text text-primary mr-1"></i>
                    CR Number for Promo
                    <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light">
                            <i class="la la-hashtag"></i>
                        </span>
                    </div>
                    <input
                        type="number"
                        class="form-control @error('change_request_id') is-invalid @enderror"
                        id="change_request_id"
                        name="change_request_id"
                        placeholder="Enter CR Number"
                        value="{{ old('change_request_id', isset($row) ? $row->change_request_id : '') }}"
                        required
                    />
                </div>
                @error('change_request_id')
                    <span class="text-danger small d-block mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Resuming Date Field -->
        <div class="col-md-6 col-lg-6">
            <div class="form-group mb-4 mb-lg-5">
                <label for="resuming_date" class="font-weight-bold text-dark">
                    <i class="la la-calendar text-success mr-1"></i>
                    Resuming Date
                    <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light">
                            <i class="la la-calendar-check"></i>
                        </span>
                    </div>
                    <input
                        type="datetime-local"
                        class="form-control @error('resuming_date') is-invalid @enderror"
                        id="resuming_date"
                        name="resuming_date"
                        value="{{ old('resuming_date', isset($row) ? $row->resuming_date : '') }}"
                        min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                        required
                    />
                </div>
                @error('resuming_date')
                    <span class="text-danger small d-block mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <!-- Row 2: On-hold Reason and On-Hold Justification -->
    <div class="row">
        <!-- On-hold Reason -->
        <div class="col-md-6 col-lg-6">
            <div class="form-group mb-4 mb-lg-5">
                <label for="hold_reason_id" class="font-weight-bold text-dark">
                    <i class="la la-pause-circle text-warning mr-1"></i>
                    On-hold Reason
                    <span class="text-danger">*</span>
                </label>
                <select
                    class="form-control select2 @error('hold_reason_id') is-invalid @enderror"
                    id="hold_reason_id"
                    name="hold_reason_id"
                    required
                >
                    <option value="">-- Select Hold Reason --</option>
                    @foreach($holdReasons as $reason)
                        <option value="{{ $reason->id }}" {{ old('hold_reason_id') == $reason->id ? 'selected' : '' }}>
                            {{ $reason->name }}
                        </option>
                    @endforeach
                </select>
                @error('hold_reason_id')
                    <span class="text-danger small d-block mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- On-Hold Justification -->
        <div class="col-md-6 col-lg-6">
            <div class="form-group mb-4 mb-lg-5">
                <label for="justification" class="font-weight-bold text-dark">
                    <i class="la la-comment-dots text-info mr-1"></i>
                    On-Hold Justification
                </label>
                <textarea
                    class="form-control @error('justification') is-invalid @enderror"
                    id="justification"
                    name="justification"
                    rows="3"
                    placeholder="Provide detailed justification for putting this CR on hold..."
                >{{ old('justification', isset($row) ? $row->justification : '') }}</textarea>
                @error('justification')
                    <span class="text-danger small d-block mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <!-- Row 3: Attachments -->
    <div class="row">
        <div class="col-md-12">
            <div class="form-group mb-4 mb-lg-5">
                <label for="attachments" class="font-weight-bold text-dark">
                    <i class="la la-paperclip text-secondary mr-1"></i>
                    Attachments
                </label>
                <div class="dropzone-wrapper" id="dropzone-area">
                    <div class="dropzone-desc">
                        <i class="la la-cloud-upload la-3x text-primary mb-2"></i>
                        <p class="mb-1">Click to browse or drag and drop files here</p>
                        <p class="text-muted small mb-0">PDF, DOC, DOCX, XLS, XLSX, ZIP, RAR, Images</p>
                    </div>
                    <input type="file" name="attachments[]" id="attachments" class="dropzone-input" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.rar,.jpg,.jpeg,.png,.gif">
                </div>
                <div id="file-preview" class="mt-3"></div>
                @error('attachments')
                    <span class="text-danger small d-block mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

</div>

@push('css')
<style>
    html,body{overflow-x:hidden}
    .select2-container{max-width:100%}
    .select2-dropdown{max-width:100vw;overflow-x:hidden}
</style>
<style>
    /* Input Group Styles */
    .input-group-text {
        background-color: #f3f6f9;
        border-right: 0;
    }

    .input-group .form-control {
        border-left: 0;
    }

    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Select2 Styles */
    .select2-container--default .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: calc(1.5em + 0.75rem);
        padding-left: 12px;
        color: #495057;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 0.75rem);
    }

    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Dropzone Styles */
    .dropzone-wrapper {
        position: relative;
        border: 2px dashed #dee2e6;
        border-radius: 0.25rem;
        background: #f8f9fa;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .dropzone-wrapper:hover {
        border-color: #007bff;
        background: #e7f1ff;
    }

    .dropzone-wrapper.dragover {
        border-color: #007bff;
        background: #cfe2ff;
    }

    .dropzone-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .dropzone-desc {
        pointer-events: none;
    }

    /* File Preview Styles */
    .file-preview-item {
        display: flex;
        align-items: center;
        padding: 10px 12px;
        background: #f8f9fa;
        border-radius: 0.25rem;
        margin-bottom: 8px;
        border: 1px solid #dee2e6;
    }

    .file-preview-item i {
        font-size: 1.5rem;
        margin-right: 10px;
    }

    .file-preview-info {
        flex: 1;
    }

    .file-preview-name {
        font-weight: 600;
        color: #212529;
        margin-bottom: 2px;
    }

    .file-preview-size {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .file-preview-remove {
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        min-width: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s ease;
        padding: 0;
        line-height: 1;
    }

    .file-preview-remove:hover {
        background: #c82333;
    }

    .file-preview-remove i {
        font-size: 14px;
        line-height: 1;
        margin: 0;
    }

    /* Character Counter */
    #char-count {
        color: #007bff;
    }
</style>
@endpush

@push('script')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('#hold_reason_id').select2({
        placeholder: '-- Select Hold Reason --',
        allowClear: false,
        width: '100%'
    });

    // Dropzone functionality
    const dropzoneArea = $('#dropzone-area');
    const fileInput = $('#attachments');
    const filePreview = $('#file-preview');
    let selectedFiles = [];

    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzoneArea.on(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    // Highlight drop area when item is dragged over it
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzoneArea.on(eventName, function() {
            dropzoneArea.addClass('dragover');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzoneArea.on(eventName, function() {
            dropzoneArea.removeClass('dragover');
        });
    });

    // Handle dropped files
    dropzoneArea.on('drop', function(e) {
        const files = e.originalEvent.dataTransfer.files;
        handleFiles(files);
    });

    // Handle file input change
    fileInput.on('change', function(e) {
        handleFiles(this.files);
    });

    function handleFiles(files) {
        // Append new files to existing ones instead of replacing
        const newFiles = Array.from(files);
        newFiles.forEach(file => {
            // Check if file already exists (by name and size)
            const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size);
            if (!exists) {
                selectedFiles.push(file);
            }
        });
        displayFiles();
    }

    function displayFiles() {
        filePreview.empty();

        if (selectedFiles.length === 0) {
            return;
        }

        selectedFiles.forEach((file, index) => {
            const fileSize = formatFileSize(file.size);
            const fileIcon = getFileIcon(file.name);

            const fileItem = $(`
                <div class="file-preview-item" data-index="${index}">
                    <i class="${fileIcon}"></i>
                    <div class="file-preview-info">
                        <div class="file-preview-name">${file.name}</div>
                        <div class="file-preview-size">${fileSize}</div>
                    </div>
                    <button type="button" class="file-preview-remove" data-index="${index}">
                        <i class="la la-times"></i>
                    </button>
                </div>
            `);

            filePreview.append(fileItem);
        });

        // Update file input
        updateFileInput();
    }

    // Remove file handler
    filePreview.on('click', '.file-preview-remove', function() {
        const index = $(this).data('index');
        selectedFiles.splice(index, 1);
        displayFiles();
    });

    function updateFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput[0].files = dt.files;
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    function getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const iconMap = {
            'pdf': 'la la-file-pdf text-danger',
            'doc': 'la la-file-word text-primary',
            'docx': 'la la-file-word text-primary',
            'xls': 'la la-file-excel text-success',
            'xlsx': 'la la-file-excel text-success',
            'zip': 'la la-file-archive text-warning',
            'rar': 'la la-file-archive text-warning',
            'jpg': 'la la-file-image text-info',
            'jpeg': 'la la-file-image text-info',
            'png': 'la la-file-image text-info',
            'gif': 'la la-file-image text-info',
        };
        return iconMap[ext] || 'la la-file text-secondary';
    }
});
</script>
@endpush
