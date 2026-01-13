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
                <div class="row">
                    <div class="col-md-12">
                        <!--begin::Card-->
                        <div class="card card-custom gutter-b example example-compact">

                            <!--begin::Form-->

                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title m-0">{{ $form_title.' #  '.$cr->cr_no.' | '.$cr?->workflowType?->name?:' ' }}</h3>
                                <div class="d-flex">

                                    @can('Show CR Logs')
                                        <button type="button" id="openModal" class="btn btn-primary">View History Logs
                                        </button>
                                    @endcan

                                </div>
                            </div>
                            <div class="card-body">
                                @foreach($CustomFields as $item)
                                    @php
                                        $custom_field_value = $cr->change_request_custom_fields->where('custom_field_name', $item->CustomField->name)->sortByDesc('id')->first();
                                        $custom_field_value = $custom_field_value  ? $custom_field_value->custom_field_value  : $cr->{$item->CustomField->name}
                                    @endphp
                                    @if($item->CustomField->type == "input")
                                        <div class="form-group col-md-6" style="float:left">
                                            <label for="user_type">
                                                {{ $item->CustomField->label }}</label>
                                            <input type="text" name="{{ $item->CustomField->name }}"
                                                   class="form-control form-control-lg"
                                                   value="{{ $custom_field_value }}" disabled/>
                                        </div>
                                    @elseif($item->CustomField->type == "select")
                                        <div class="form-group col-md-6" style="float:left">
                                            <label for="user_type">{{ $item->CustomField->label }} </label>
                                            <select name="{{ $item->CustomField->name }}"
                                                    class="form-control form-control-lg" disabled>
                                                <option value="">Select</option>
                                                @if($item->CustomField->name == "new_status_id")
                                                    <option value="{{ $status_name }}" selected>

                                                        @php
                                                            $display_status = $status_name;
                                                            if($cr->isDependencyHold()) {
                                                                $blockingCrs = $cr->getBlockingCrNumbers();
                                                                $crList = !empty($blockingCrs) ? ' (CR#' . implode(', CR#', $blockingCrs) . ')' : '';
                                                                $display_status = 'Design Estimation - Pending Dependency' . $crList;
                                                            }
                                                        @endphp
                                                        {{ $display_status }}
                                                    </option>

                                                @else
                                                    @foreach($item->CustomField->getCustomFieldValue() as $value)
                                                        <option value="{{ $value->id }}"
                                                            {{ $custom_field_value == $value->id ? 'selected' : '' }}>
                                                            {{ $value->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    @elseif($item->CustomField->type == "textArea")
                                        <div class="form-group col-md-6" style="float:left">
                                            <label for="user_type">
                                                {{ $item->CustomField->label }}</label>
                                            <textarea name="{{ $item->CustomField->name }}" disabled
                                                      class="form-control form-control-lg">{{ $custom_field_value }}</textarea>
                                        </div>
                                    @elseif($item->CustomField->name == "relevant")
                                        @php
                                            // Decode the stored JSON ["6366", "6350"]
                                            $selectedValues = json_decode($custom_field_value, true);

                                            // Fallback if value is comma-separated (e.g., "6366,6350")
                                            if (!is_array($selectedValues)) {
                                                $selectedValues = explode(',', $custom_field_value);
                                            }

                                            // Normalize to strings
                                            $selectedValues = array_map('strval', $selectedValues);

                                            // Fetch all selected CRs by cr_no
                                            $selectedCRs = \App\Models\Change_request::whereIn('id', $selectedValues)
                                                                ->select('cr_no', 'title')
                                                                ->get();
                                        @endphp

                                        <div class="form-group col-md-6" style="float:left">
                                            <label>{{ $item->CustomField->label }}</label>

                                            <select class="form-control form-control-lg select2-field" multiple
                                                    disabled>

                                                @foreach($selectedCRs as $crItem)
                                                    @php
                                                        $label = $crItem->cr_no . ' - ' . $crItem->title;
                                                    @endphp

                                                    <option value="{{ $crItem->cr_no }}" selected>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </div>

                                    @endif

                                @endforeach
                            </div>


                                <?php
                                $technical_feedback = $cr->change_request_custom_fields->where('custom_field_name', 'technical_feedback')->sortByDesc('updated_at');
                                $business_feedback = $cr->change_request_custom_fields->where('custom_field_name', 'business_feedback')->sortByDesc('updated_at');
                                ?>
                            <div class="form-group col-md-12" style="float:left">
                                @can('View Technical Feedback')
                                    @if($technical_feedback->count() > 0  )
                                        <h5>Technichal Feedback</h5>
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr class="text-center">
                                                <th>Feedback</th>
                                                <th>Updated By</th>
                                                <th>Updated At</th>
                                            </tr>
                                            </thead>
                                            <tbody class="text-center">
                                            @foreach ($technical_feedback as $index => $feedback)
                                                <tr>
                                                    <td>{{ $feedback->custom_field_value }}</td>
                                                    <td>{{ optional($feedback->user)->user_name ?? 'N/A' }}</td>
                                                    <td>{{ $feedback->updated_at }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                @endcan
                                @can('View Business Feedback')
                                    @if($business_feedback->count() > 0  )
                                        <h5>Business Feedback</h5>
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr class="text-center">
                                                <th>Feedback</th>
                                                <th>Updated By</th>
                                                <th>Updated At</th>
                                            </tr>
                                            </thead>
                                            <tbody class="text-center">
                                            @foreach ($business_feedback as $index => $feedback)
                                                <tr>
                                                    <td>{{ $feedback->custom_field_value }}</td>
                                                    <td>{{ optional($feedback->user)->user_name ?? 'N/A' }}</td>
                                                    <td>{{ $feedback->updated_at }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                @endcan
                                <!-- end feedback table -->
                                @if(count($cr['attachments'])  > 0  )
                                    <div class="form-group col-md-12" style="float:left">
                                        @can('View Technichal Attachments')
                                            <h5>Technichal Attachments</h5>
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr class="text-center">
                                                    <th>#</th>
                                                    <th>File Name</th>
                                                    <th>User Name</th>
                                                    <th>Uploaded At</th>
                                                    <th>File Size (MB)</th>
                                                    <th>Download</th>
                                                </tr>
                                                </thead>
                                                <tbody class="text-center">
                                                @foreach ($cr['attachments'] as $key => $file)
                                                    @if ($file->flag == 1)
                                                        <tr>
                                                            <td>{{ ++$key }}</td>
                                                            <td>{{ $file->file }}</td>
                                                            <td>{{ $file->user->user_name }}
                                                                ({{ $file->user->defualt_group->title }})
                                                            </td>
                                                            <td>{{ $file->created_at }}</td>
                                                            <td>
                                                                @if (isset($file->size))
                                                                    <!-- Ensure the file size is available -->
                                                                    {{ round($file->size / 1024) }} KB
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                <a href="{{ route('files.download', $file->id) }}"
                                                                   class="btn btn-light btn-sm">
                                                                    Download
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @endcan
                                        @can('View Business Attachments')
                                            <h5>Business Attachments</h5>
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr class="text-center">
                                                    <th>#</th>
                                                    <th>File Name</th>
                                                    <th>User Name</th>
                                                    <th>Uploaded At</th>
                                                    <th>File Size (MB)</th>
                                                    <th>Download</th>
                                                </tr>
                                                </thead>
                                                <tbody class="text-center">
                                                @foreach ($cr['attachments'] as $key => $file)
                                                    @if ($file->flag == 2)
                                                        <tr>
                                                            <td>{{ ++$key }}</td>
                                                            <td>{{ $file->file }}</td>
                                                            <td>{{ $file->user->user_name }}
                                                                ({{ $file->user->defualt_group->title }})
                                                            </td>
                                                            <td>{{ $file->created_at }}</td>
                                                            <td>
                                                                @if (isset($file->size))
                                                                    <!-- Ensure the file size is available -->
                                                                    {{ round($file->size / 1024) }} KB
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                <a href="{{ route('files.download', $file->id) }}"
                                                                   class="btn btn-light btn-sm">
                                                                    Download
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @endcan

                                    </div>
                                @endif

                            </div>



                            <!--end::Form-->
                        </div>
                        <!-- Button to trigger the modal -->

                        <!-- Attachments form for dev teams -->
                        @can('Upload CR Attachments')
                            <div class="card card-custom gutter-b example example-compact">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title m-0">
                                        <i class="fas fa-cloud-upload-alt mr-2"></i> Upload Attachments
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <form class="form" id="devAttachmentsForm" enctype="multipart/form-data" data-cr-id="{{ $cr->id }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group mb-4 mb-lg-5">
                                                    <label for="technical_attachments" class="font-weight-bold text-dark">
                                                        <i class="la la-paperclip text-secondary mr-1"></i>
                                                        Attachments
                                                    </label>
                                                    <div class="dropzone-wrapper" id="dropzoneWrapper">
                                                        <div class="dropzone-desc">
                                                            <i class="la la-cloud-upload la-3x text-primary mb-2"></i>
                                                            <p class="mb-1">Click to browse or drag and drop files here</p>
                                                        </div>
                                                        <input type="file" name="technical_attachments[]" id="technicalAttachments" class="dropzone-input" multiple>
                                                    </div>
                                                    <div id="filePreview" class="mt-3"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-right mt-4">
                                            <button type="button" class="btn btn-secondary px-5 mr-2" id="clearBtn" disabled>
                                                <i class="fas fa-times mr-2"></i> Clear All
                                            </button>
                                            <button type="submit" class="btn btn-success px-5" id="uploadBtn" disabled>
                                                <i class="fas fa-check-circle mr-2"></i> Submit
                                            </button>
                                        </div>

                                        <div id="uploadProgress" class="mt-3" style="display: none;">
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                                            </div>
                                            <p class="text-center mt-2" id="progressText">Uploading...</p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endcan

                        @include("$view.cr_logs")







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

@push('css')
    <style>
        html, body {
            overflow-x: hidden;
        }

        .select2-container {
            max-width: 100%;
        }

        .select2-dropdown {
            max-width: 100vw;
            overflow-x: hidden;
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
        #filePreview {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        @media (max-width: 768px) {
            #filePreview {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            #filePreview {
                grid-template-columns: 1fr;
            }
        }

        .file-preview-item {
            display: flex;
            flex-direction: column;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 0.25rem;
            border: 1px solid #dee2e6;
            position: relative;
            min-height: 100px;
        }

        .file-preview-item i {
            font-size: 2rem;
            margin-bottom: 8px;
            align-self: center;
        }

        .file-preview-info {
            flex: 1;
            text-align: center;
        }

        .file-preview-name {
            font-weight: 600;
            color: #212529;
            margin-bottom: 4px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            font-size: 0.875rem;
            line-height: 1.3;
        }

        .file-preview-size {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .file-preview-remove {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            min-width: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s ease;
            padding: 0;
            line-height: 1;
            position: absolute;
            top: 8px;
            right: 8px;
        }

        .file-preview-remove:hover {
            background: #c82333;
        }

        .file-preview-remove i {
            font-size: 12px;
            line-height: 1;
            margin: 0;
        }

        .progress {
            height: 25px;
        }

        .alert {
            margin-top: 15px;
        }
    </style>
@endpush



@push('script')
    <script>
        $(function () {
            if ($.fn.select2) {
                // Initialize all select2 fields
                $('.select2-field').each(function () {
                    var $el = $(this);

                    // Find the closest form as parent
                    var $form = $el.closest('form');
                    var dropdownParent = $form.length ? $form : $(document.body);

                    $el.select2({
                        placeholder: $el.data('placeholder') || 'Select',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: dropdownParent
                    });
                });
            }
        });
        // Modern Bootstrap Modal Handler
        var btn = document.getElementById("openModal");
        var closeBtn = document.getElementById("close_logs");

        // Open modal with Bootstrap
        if (btn) {
            btn.onclick = function () {
                $('#modal').modal('show');
            }
        }

        // Close modal with Bootstrap
        if (closeBtn) {
            closeBtn.onclick = function () {
                $('#modal').modal('hide');
            }
        }

        // Drag and Drop File Upload Functionality
        $(document).ready(function() {
            let selectedFiles = [];
            const maxFileSize = 51200 * 1024; // 50MB in bytes
            const allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar', 'jpg', 'jpeg', 'png', 'gif', 'msg'];

            const dropzoneWrapper = $('#dropzoneWrapper');
            const fileInput = $('#technicalAttachments');
            const filePreview = $('#filePreview');
            const uploadBtn = $('#uploadBtn');
            const clearBtn = $('#clearBtn');
            const uploadProgress = $('#uploadProgress');
            const progressBar = $('.progress-bar');
            const progressText = $('#progressText');

            // Prevent default drag behaviors
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzoneWrapper[0].addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            // Highlight drop zone when item is dragged over it
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzoneWrapper[0].addEventListener(eventName, () => {
                    dropzoneWrapper.addClass('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzoneWrapper[0].addEventListener(eventName, () => {
                    dropzoneWrapper.removeClass('dragover');
                }, false);
            });

            // Handle dropped files
            dropzoneWrapper[0].addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles(files);
            }, false);

            // Handle file input change
            fileInput.on('change', function() {
                handleFiles(this.files);
                // Reset the input value so the same file can be added again after removal
                this.value = '';
            });

            // Click on dropzone to trigger file input
            dropzoneWrapper.on('click', function(e) {
                if (e.target === this || $(e.target).closest('.dropzone-desc').length) {
                    fileInput.click();
                }
            });

            function handleFiles(files) {
                const filesArray = Array.from(files);

                filesArray.forEach(file => {
                    // Validate file
                    const validation = validateFile(file);
                    if (!validation.valid) {
                        showAlert(validation.message, 'danger');
                        return;
                    }

                    // Check if file already exists
                    const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size);
                    if (exists) {
                        showAlert(`File "${file.name}" is already added.`, 'warning');
                        return;
                    }

                    selectedFiles.push(file);
                    addFilePreview(file);
                });

                updateButtons();
            }

            function validateFile(file) {
                // Check file size
                if (file.size > maxFileSize) {
                    return {
                        valid: false,
                        message: `File "${file.name}" exceeds the maximum size of 50MB.`
                    };
                }

                // Check file extension
                const extension = file.name.split('.').pop().toLowerCase();
                if (!allowedExtensions.includes(extension)) {
                    return {
                        valid: false,
                        message: `File "${file.name}" has an invalid extension. Allowed: ${allowedExtensions.join(', ')}`
                    };
                }

                return { valid: true };
            }

            function addFilePreview(file) {
                const fileSize = formatFileSize(file.size);
                const fileIcon = getFileIcon(file.name);
                const fileId = Date.now() + Math.random();

                const previewHtml = `
                    <div class="file-preview-item" data-file-id="${fileId}">
                        <i class="${fileIcon}"></i>
                        <div class="file-preview-info">
                            <div class="file-preview-name">${file.name}</div>
                            <div class="file-preview-size">${fileSize}</div>
                        </div>
                        <button type="button" class="file-preview-remove" data-file-id="${fileId}">
                            <i class="la la-times" style="color: white !important;"></i>
                        </button>
                    </div>
                `;

                filePreview.append(previewHtml);
                file.previewId = fileId;
            }

            // Remove file handler
            filePreview.on('click', '.file-preview-remove', function() {
                const fileId = $(this).data('file-id');
                selectedFiles = selectedFiles.filter(f => f.previewId !== fileId);
                $(`.file-preview-item[data-file-id="${fileId}"]`).remove();
                updateButtons();
            });

            function updateButtons() {
                if (selectedFiles.length > 0) {
                    uploadBtn.prop('disabled', false);
                    clearBtn.prop('disabled', false);
                } else {
                    uploadBtn.prop('disabled', true);
                    clearBtn.prop('disabled', true);
                }
            }

            clearBtn.on('click', function() {
                selectedFiles = [];
                filePreview.empty();
                fileInput.val('');
                updateButtons();
                showAlert('All files cleared.', 'info');
            });

            // Handle form submission
            $('#devAttachmentsForm').on('submit', function(e) {
                e.preventDefault();

                if (selectedFiles.length === 0) {
                    showAlert('Please select at least one file to upload.', 'warning');
                    return;
                }

                const formData = new FormData();
                const crId = $('#devAttachmentsForm').data('cr-id');
                formData.append('_token', $('input[name="_token"]').val());

                selectedFiles.forEach((file, index) => {
                    formData.append('technical_attachments[]', file);
                });

                // Show progress bar
                uploadProgress.show();
                progressBar.css('width', '0%');
                progressText.text('Uploading files...');
                uploadBtn.prop('disabled', true);
                clearBtn.prop('disabled', true);

                $.ajax({
                    url: '{{ route("change_request.upload_dev_attachments", ["change_request" => $cr->id]) }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percentComplete = (e.loaded / e.total) * 100;
                                progressBar.css('width', percentComplete + '%');
                                progressText.text(`Uploading... ${Math.round(percentComplete)}%`);
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(response) {
                        progressBar.css('width', '100%');
                        progressText.text('Upload complete!');

                        setTimeout(function() {
                            uploadProgress.hide();
                            showAlert(response.message || 'Files uploaded successfully!', 'success');

                            // Clear the form
                            selectedFiles = [];
                            filePreview.empty();
                            fileInput.val('');
                            updateButtons();

                            // Reload page after 2 seconds to show new attachments
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }, 1000);
                    },
                    error: function(xhr) {
                        uploadProgress.hide();
                        uploadBtn.prop('disabled', false);
                        clearBtn.prop('disabled', false);

                        let errorMessage = 'Failed to upload files. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showAlert(errorMessage, 'danger');
                    }
                });
            });

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
                    'msg': 'la la-envelope text-secondary'
                };
                return iconMap[ext] || 'la la-file text-secondary';
            }

            function showAlert(message, type) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show shadow-sm d-flex align-items-center" role="alert">
                        ${message}
                    </div>
                `;

                // Remove existing alerts
                $('.alert').remove();

                // Add new alert before the form
                $('#devAttachmentsForm').parent().prepend(alertHtml);

                // Auto-dismiss after 5 seconds
                setTimeout(function() {
                    $('.alert').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 3000);
            }
        });

    </script>

@endpush
