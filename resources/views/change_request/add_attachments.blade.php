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
                    <h2 class="text-white font-weight-bold my-2 mr-5">Add Attachments and Feedback</h2>
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
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    

                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-header">
                            <h3 class="card-title">Add Attachments and Feedback</h3>
                        </div>
                        <!--begin::Form-->
                        <form class="form" action="{{ route('store_attachments') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label>CR Number <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="cr_number" 
                                           class="form-control form-control-solid @error('cr_number') is-invalid @enderror" 
                                           placeholder="Enter CR Number" 
                                           value="{{ old('cr_number') }}"
                                           required
                                           autofocus>
                                    @error('cr_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Business Feedback <span class="text-muted">(Optional)</span></label>
                                    <textarea name="business_feedback" 
                                              class="form-control form-control-solid @error('business_feedback') is-invalid @enderror" 
                                              rows="3" 
                                              placeholder="Enter business feedback">{{ old('business_feedback') }}</textarea>
                                    @error('business_feedback')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Technical Feedback <span class="text-muted">(Optional)</span></label>
                                    <textarea name="technical_feedback" 
                                              class="form-control form-control-solid @error('technical_feedback') is-invalid @enderror" 
                                              rows="3" 
                                              placeholder="Enter technical feedback">{{ old('technical_feedback') }}</textarea>
                                    @error('technical_feedback')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Business Attachments <span class="text-muted">(Optional)</span></label>
                                    <div class="custom-file">
                                        <input type="file" 
                                               class="custom-file-input @error('business_attachments.*') is-invalid @enderror" 
                                               name="business_attachments[]" 
                                               id="businessAttachments" 
                                               multiple>
                                        <label class="custom-file-label" for="businessAttachments">Choose files</label>
                                        @error('business_attachments.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <span class="form-text text-muted">Upload business-related documents (multiple files allowed)</span>
                                </div>

                                <div class="form-group">
                                    <label>Technical Attachments <span class="text-muted">(Optional)</span></label>
                                    <div class="custom-file">
                                        <input type="file" 
                                               class="custom-file-input @error('technical_attachments.*') is-invalid @enderror" 
                                               name="technical_attachments[]" 
                                               id="technicalAttachments" 
                                               multiple>
                                        <label class="custom-file-label" for="technicalAttachments">Choose files</label>
                                        @error('technical_attachments.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <span class="form-text text-muted">Upload technical documents (multiple files allowed)</span>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary mr-2">Update Status</button>
                                <a href="{{ url('/') }}" class="btn btn-secondary">Cancel</a>
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