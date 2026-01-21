@php
	$role_permissions = $role_permissions ?? []; // to check if it's create or edit form
@endphp

@php
	$isView = request()->routeIs('*.show');
	$inputClass = $isView ? 'form-control-plaintext bg-light pl-2' : 'form-control'; // Added pl-2 for plaintext padding
	$isDisabled = $isView ? 'disabled' : '';
@endphp

<div class="card-body">
    @if($errors->any())
		<div class="alert alert-custom alert-light-danger fade show mb-10" role="alert">
			<div class="alert-icon"><i class="flaticon-warning"></i></div>
			<div class="alert-text">
				There are some errors in your submission. Please correct them.
			</div>
			<div class="alert-close">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true"><i class="ki ki-close"></i></span>
				</button>
			</div>
		</div>
	@endif

    {{-- Section: Requester Information --}}
    <h5 class="text-dark font-weight-bold mb-6">Requester Information</h5>
    
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Requester:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="la la-user"></i></span>
                    </div>
                    @if(isset($row))
						<input type="text" class="{{ $inputClass }}" value="{{ $row->creator->name }}" disabled />
						<input type="hidden" name="created_by" value="{{ $row->created_by}}" />
					@else
						<input type="text" class="{{ $inputClass }}" value="{{ Auth::user()->name }}" disabled />
						<input type="hidden" name="created_by" value="{{ Auth::id() }}" />
					@endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label>Department:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="la la-building"></i></span>
                    </div>
                    @if(isset($row))
						<input type="text" class="{{ $inputClass }}" value="{{ $row->requester_department }}" disabled />
						<input type="hidden" name="requester_department" value="{{ $row->requester_department }}" />
					@else
						<input type="text" class="{{ $inputClass }}" name="requester_department" value="{{ old('requester_department') }}" required />
					@endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group">
                <label>Mobile:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="la la-mobile-phone"></i></span>
                    </div>
                    @if(isset($row))
						<input type="text" class="{{ $inputClass }}" value="{{ $row->requester_mobile }}" disabled />
						<input type="hidden" name="requester_mobile" value="{{ $row->requester_mobile }}" />
					@else
						<input type="text" class="{{ $inputClass }}" name="requester_mobile" value="{{ old('requester_mobile') }}" required />
					@endif
                </div>
            </div>
        </div>
    </div>

    <div class="separator separator-dashed my-8"></div>

    {{-- Section: Request Details --}}
    <h5 class="text-dark font-weight-bold mb-6">Request Details</h5>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Subject:</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="la la-header"></i></span>
                    </div>
                    @if(isset($row))
						<input type="text" class="{{ $inputClass }}" value="{{ $row->subject }}" disabled />
						<input type="hidden" name="subject" value="{{ $row->subject }}" />
					@else
						<input type="text" class="{{ $inputClass }}" name="subject" value="{{ old('subject') }}" required />
					@endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label>Promo:</label>
                <div class="input-group">
                   @if(!isset($row))
					<div class="input-group-prepend">
						<span class="input-group-text"><i class="la la-tag"></i></span>
					</div>
				   @endif
                    @if(isset($row))
						<input type="text" class="{{ $inputClass }} form-control" value="{{ $row->promo->cr_no ?? 'N/A' }}" disabled />
						<input type="hidden" name="promo_id" value="{{ $row->promo_id }}" />
					@else
						<select class="form-control kt-select2" id="promo_id" name="promo_id" required>
							<option value="">Select Promo</option>
							@foreach($changeRequests as $cr)
								<option value="{{ $cr->id }}" {{ (isset($row) && $row->promo_id == $cr->id) ? 'selected' : '' }}>
									{{ $cr->cr_no }} - {{ $cr->title ?? 'N/A' }}
								</option>
							@endforeach
						</select>
					@endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group">
                <label>Group:</label>
                <div class="input-group">
                    @if(!isset($row))
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="la la-users"></i></span>
						</div>
					@endif
                    @if(isset($row))
						<input type="text" class="{{ $inputClass }} form-control" value="{{ $row->group->name ?? 'N/A' }}" disabled />
						<input type="hidden" name="group_id" value="{{ $row->group_id }}" />
					@else
						<select class="form-control kt-select2" name="group_id" required>
							<option value="">Select Group</option>
							@foreach($groups as $group)
								<option value="{{ $group->id }}" {{ (isset($row) && $row->group_id == $group->id) ? 'selected' : '' }}>
									{{ $group->name }}
								</option>
							@endforeach
						</select>
					@endif
                </div>
            </div>
        </div>
    </div>

    <div class="separator separator-dashed my-8"></div>

    {{-- Section: Additional Information --}}
    <h5 class="text-dark font-weight-bold mb-6">Additional Information</h5>

    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label>Comments:</label>
                <textarea class="{{ $inputClass }}" name="comments" rows="3" placeholder="Enter any additional comments here...">{{ old('comments') }}</textarea>
                {!! $errors->first('comments', '<span class="form-control-feedback text-danger">:message</span>') !!}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                <label>Attachments:</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input {{ $inputClass }}" name="attachments" id="customFile">
                    <label class="custom-file-label" for="customFile">Choose file</label>
                </div>
                {!! $errors->first('attachments', '<span class="form-control-feedback text-danger">:message</span>') !!}
            </div>
        </div>
    </div>

    {{-- Status Row (only in edit mode) --}}
    @if(isset($row))
		<div class="separator separator-dashed my-8"></div>
		<div class="form-group">
			<label>Status:</label>
			<select class="form-control" name="status_id">
				@foreach($statuses as $status)
					<option value="{{ $status->id }}" {{ $row->status_id == $status->id ? 'selected' : '' }}>
						{{ $status->status_name }}
					</option>
				@endforeach
			</select>
		</div>
	@else
		<input type="hidden" name="status_id" value="{{ $defaultStatusId }}" />
	@endif


    {{-- Previous Comments Section --}}
    @if(isset($comments) && $comments->count() > 0)
		<div class="separator separator-dashed my-10"></div>
		<h5 class="text-dark font-weight-bold mb-6">Comments History</h5>

		<div class="timeline timeline-3">
			<div class="timeline-items">
				@foreach($comments as $comment)
					<div class="timeline-item">
						<div class="timeline-media">
							<span class="symbol symbol-35 symbol-light-primary">
								<span class="symbol-label font-weight-bold font-size-h6">{{ substr($comment->user->name ?? 'U', 0, 1) }}</span>
							</span>
						</div>
						<div class="timeline-content">
							<div class="d-flex align-items-center justify-content-between mb-3">
								<div class="mr-2">
									<span class="text-dark-75 text-hover-primary font-weight-bold">
										{{ $comment->user->name ?? 'Unknown User' }}
									</span>
									<span class="text-muted ml-2">{{ $comment->created_at->format('d M Y H:i') }}</span>
								</div>
							</div>
							<p class="p-0">
								{{ $comment->comment }}
							</p>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	@endif

    {{-- Attachments Section --}}
    @if(isset($attachments) && $attachments->count() > 0)
		<div class="separator separator-dashed my-10"></div>
		<h5 class="text-dark font-weight-bold mb-6">Existing Attachments</h5>

		<div class="row">
			@foreach($attachments as $attachment)
				<div class="col-md-4 mb-4">
					<div class="card card-custom card-stretch border">
						<div class="card-body d-flex align-items-center p-4">
							<div class="mr-3">
								<span class="symbol symbol-lg-50 symbol-light-success">
									<span class="symbol-label font-size-h4">
										<i class="la la-file-text text-success"></i>
									</span>
								</span>
							</div>
							<div class="d-flex flex-column">
								<a href="{{ route('prerequisites.download', $attachment->id) }}" class="text-dark-75 text-hover-primary font-weight-bold mb-1">
									{{ basename($attachment->file) }}
								</a>
								<span class="text-muted font-size-sm">
									By {{ $attachment->user->name ?? 'Unknown' }} on {{ $attachment->created_at->format('d M Y') }}
								</span>
							</div>
						</div>
					</div>
				</div>
			@endforeach
		</div>
	@endif
</div>

@push('styles')
	<link href="{{ asset('assets/plugins/custom/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
	<style>
		.timeline.timeline-3 .timeline-item .timeline-media {
			min-width: 40px;
		}
		/* Enhance Select2 height to match inputs */
		.select2-container .select2-selection--single {
			height: calc(1.5em + 1.3rem + 2px) !important;
			padding: 0.65rem 1rem;
			display: flex;
			align-items: center;
		}
		.select2-container--default .select2-selection--single .select2-selection__arrow {
			top: 0.5rem;
		}
	</style>
@endpush

@push('scripts')
	<script>
		        jQuery(document).ready(function() {
            // Initialize Select2
            $('.kt-select2').select2({
                placeholder: "Select options",
                allowClear: true,
                width: '100%'
            });
            
            // Reinitialize Select2 after AJAX loads
            $(document).ajaxComplete(function() {
                $('.kt-select2').select2({
                    placeholder: "Select options",
                    allowClear: true,
                    width: '100%'
                });
            });

            // Custom file input label update
            $('.custom-file-input').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });
        });
	</script>
@endpush