@php
	$role_permissions = $role_permissions ?? []; // to check if it's create or edit form
@endphp

@php
    $isView = request()->routeIs('*.show');
    $inputClass = $isView ? 'form-control-plaintext bg-light' : 'form-control';
    $isDisabled = $isView ? 'disabled' : '';
@endphp

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


	<!-- First Row -->
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<label>Requester:</label>
				@if(isset($row))
				<input type="text" class="{{ $inputClass }}" value="{{ $row->creator->name }}" disabled />
				<input type="hidden" name="created_by" value="{{ $row->created_by}}" />
				@else
				<input type="text" class="{{ $inputClass }}" value="{{ Auth::user()->name }}" disabled />
				<input type="hidden" name="created_by" value="{{ Auth::id() }}" />
				@endif
			</div>
		</div>

		<div class="col-md-4">
			<div class="form-group">
				<label>Requester Department:</label>
				@if(isset($row))
					<input type="text" class="{{ $inputClass }}" value="{{ $row->requester_department }}" disabled />
					<input type="hidden" name="requester_department" value="{{ $row->requester_department }}" />
				@else
					<input type="text" class="{{ $inputClass }}" name="requester_department" value="{{ old('requester_department') }}" required />
				@endif
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="form-group">
				<label>Requester Mobile:</label>
				@if(isset($row))
					<input type="text" class="{{ $inputClass }}" value="{{ $row->requester_mobile }}" disabled />
					<input type="hidden" name="requester_mobile" value="{{ $row->requester_mobile }}" />
				@else
					<input type="text" class="{{ $inputClass }}" name="requester_mobile" value="{{ old('requester_mobile') }}" required />
				@endif
			</div>
		</div>
	</div>

	<!-- Second Row -->
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<label>Subject:</label>
				@if(isset($row))
					<input type="text" class="{{ $inputClass }}" value="{{ $row->subject }}" disabled />
					<input type="hidden" name="subject" value="{{ $row->subject }}" />
				@else
					<input type="text" class="{{ $inputClass }}" name="subject" value="{{ old('subject') }}" required />
				@endif
			</div>
		</div>

		<div class="col-4">
			<div class="form-group">
				<label>Promo:</label>
				@if(isset($row))
					<input type="text" class="{{ $inputClass }}" value="{{ $row->promo->cr_no ?? 'N/A' }}" disabled />
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
		
		<div class="col-md-4">
			<div class="form-group">
				<label>Group:</label>
				@if(isset($row))
					<input type="text" class="{{ $inputClass }}" value="{{ $row->group->name ?? 'N/A' }}" disabled />
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

	<!-- Third Row - Full Width -->
	<div class="row">
		<div class="col-6">
			<div class="form-group">
				<label>Comments:</label>
				<textarea class="{{ $inputClass }}" name="comments" rows="3">{{ old('comments') }}</textarea>
				{!! $errors->first('comments', '<span class="form-control-feedback">:message</span>') !!}
			</div>
		</div>
		<div class="col-6">
			<div class="form-group">
				<label>Attachments:</label>
				<input type="file" class="{{ $inputClass }}" name="attachments" />
				{!! $errors->first('attachments', '<span class="form-control-feedback">:message</span>') !!}
			</div>
		</div>
	</div>

	<!-- Status Row (only in edit mode) -->
	@if(isset($row))
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


	<!-- Comments Section -->
	@if(isset($comments) && $comments->count() > 0)
	<hr>
	<h4 class="mt-4">Comments</h4>

	<div class="comments-list mt-3">
		@foreach($comments as $comment)
			<div class="border rounded p-3 mb-3 bg-light">
				<div class="d-flex justify-content-between align-items-center">
					<strong>{{ $comment->user->name ?? 'Unknown User' }}</strong>
					<small class="text-muted">{{ $comment->created_at->format('Y-m-d H:i') }}</small>
				</div>
				<p class="mb-0 mt-2">{{ $comment->comment }}</p>
			</div>
		@endforeach
	</div>
	@endif

	<!-- Attachments Section -->
	@if(isset($attachments) && $attachments->count() > 0)
	<hr>
	<h4 class="mt-4">Attachments</h4>

	<div class="attachments-list mt-3">
		@foreach($attachments as $attachment)
			<div class="border rounded p-3 mb-3 bg-light">
				<div class="d-flex justify-content-between align-items-center">
					<strong>{{ $attachment->user->name ?? 'Unknown User' }}</strong>
					<small class="text-muted">{{ $attachment->created_at->format('Y-m-d H:i') }}</small>
				</div>
				<p class="mb-0 mt-2">
					📎 
					<a href="{{ route('prerequisites.download', $attachment->id) }}" 
					class="text-primary" 
					title="Download Attachment">
						{{ basename($attachment->file) }}
					</a>
				</p>
			</div>
		@endforeach
	</div>
	@endif


@push('styles')
    <link href="{{ asset('assets/plugins/custom/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        /* Add some spacing between form groups */
        .form-group {
            margin-bottom: 1.5rem;
        }
        /* Make sure select2 respects the container width */
        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush

@push('scripts')
    <script>

		jQuery(document).ready(function() {
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
		});
        
    </script>
@endpush