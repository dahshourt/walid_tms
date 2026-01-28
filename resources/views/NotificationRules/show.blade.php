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
                        <div class="card-header">
                            <h3 class="card-title">Notification Rule: {{ $row->name }}</h3>
                            <div class="card-toolbar">
                                <span class="label label-{{ $row->is_active ? 'success' : 'danger' }} label-lg font-weight-bold mr-3">
                                    {{ $row->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @can('Edit Notification Rules')
                                <a href="{{ route('notification_rules.edit', $row->id) }}" class="btn btn-primary btn-sm font-weight-bold">
                                    <i class="la la-edit"></i> Edit
                                </a>
                                @endcan
                            </div>
                        </div>
                        
                        <div class="card-body">
                            {{-- Basic Information Section --}}
                            <h5 class="font-weight-bold mb-4">Basic Information</h5>
                            <div class="row mb-5">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="font-weight-bold text-muted">Rule Name</label>
                                        <div class="text-dark-75 font-size-lg">{{ $row->name }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="font-weight-bold text-muted">Event Class</label>
                                        <div>
                                            <span >
                                                {{ $eventClasses[$row->event_class] ?? $row->event_class }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-5">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="font-weight-bold text-muted">Email Template</label>
                                        <div class="text-dark-75 font-size-lg">
                                            {{ $row->template->name ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-4">
                                        <label class="font-weight-bold text-muted">Priority</label>
                                        <div class="text-dark-75 font-size-lg">{{ $row->priority ?? 0 }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-4">
                                        <label class="font-weight-bold text-muted">Created</label>
                                        <div class="text-dark-75">{{ $row->created_at->format('M d, Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="separator separator-dashed my-5"></div>
                            
                            {{-- Condition Section --}}
                            <h5 class="font-weight-bold mb-4">Condition</h5>
                            <div class="mb-5">
                                @if($conditionDisplay && $conditionDisplay !== 'No conditions (always execute)')
                                    <span class="">
                                        {{ $conditionDisplay }}
                                    </span>
                                @else
                                    <div class="text-muted">
                                        This rule has no conditions and will always execute when the event is triggered.
                                    </div>
                                @endif
                            </div>
                            
                            <div class="separator separator-dashed my-5"></div>
                            
                            {{-- Recipients Section --}}
                            <h5 class="font-weight-bold mb-4">Recipients ({{ $row->recipients->count() }})</h5>
                            <div class="mb-5">
                                @if($row->recipients && count($row->recipients) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-head-custom table-hover">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>Channel</th>
                                                    <th>Type</th>
                                                    <th>Identifier</th>
                                                    <th>Resolved To</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($row->recipients as $recipient)
                                                    @php
                                                        $recipientConfig = collect(config('notification_recipient_types'))->firstWhere('value', $recipient->recipient_type);
                                                        $typeLabel = $recipientConfig['label'] ?? $recipient->recipient_type;
                                                        
                                                        // Try to resolve identifier to a name
                                                        $identifierDisplay = '-';
                                                        if ($recipient->recipient_identifier) {
                                                            if ($recipientConfig && isset($recipientConfig['identifier_type'])) {
                                                                if ($recipientConfig['identifier_type'] === 'user_id') {
                                                                    $user = \App\Models\User::find($recipient->recipient_identifier);
                                                                    $identifierDisplay = $user ? $user->user_name : "User #{$recipient->recipient_identifier}";
                                                                } elseif ($recipientConfig['identifier_type'] === 'group_id') {
                                                                    $group = \App\Models\Group::find($recipient->recipient_identifier);
                                                                    $identifierDisplay = $group ? $group->title : "Group #{$recipient->recipient_identifier}";
                                                                } else {
                                                                    $identifierDisplay = $recipient->recipient_identifier;
                                                                }
                                                            } else {
                                                                $identifierDisplay = $recipient->recipient_identifier;
                                                            }
                                                        }
                                                        
                                                        $channelColors = ['to' => 'primary', 'cc' => 'info', 'bcc' => 'dark'];
                                                    @endphp
                                                    <tr class="text-center">
                                                        <td>
                                                            <span class="label label-{{ $channelColors[$recipient->channel] ?? 'secondary' }} label-inline font-weight-bold">
                                                                {{ strtoupper($recipient->channel) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="font-weight-bold">{{ $typeLabel }}</span>
                                                            @if($recipientConfig && isset($recipientConfig['description']))
                                                                <br><small class="text-muted">{{ $recipientConfig['description'] }}</small>
                                                            @endif
                                                        </td>
                                                        <td>{{ $identifierDisplay }}</td>
                                                        <td>
                                                            <small class="text-muted">
                                                                @if($recipientConfig && !$recipientConfig['needs_identifier'])
                                                                    <i class="la la-magic text-primary"></i> Dynamic
                                                                @else
                                                                    <i class="la la-user text-success"></i> Static
                                                                @endif
                                                            </small>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center text-muted py-4">
                                        <p class="mb-0">No recipients configured for this rule.</p>
                                    </div>
                                @endif
                            </div>
                            
                            {{-- Template Preview Section --}}
                            @if($row->template)
                            <div class="separator separator-dashed my-5"></div>
                            
                            <h5 class="font-weight-bold mb-4">Email Template Preview</h5>
                            <div class="mb-4">
                                <label class="font-weight-bold text-muted">Subject</label>
                                <div class="text-dark-75 font-size-lg bg-light-primary rounded p-3">
                                    {{ $row->template->subject }}
                                </div>
                            </div>
                            <div>
                                <label class="font-weight-bold text-muted">Body</label>
                                <div class="border rounded p-4 bg-white" style="max-height: 400px; overflow-y: auto;">
                                    {!! $row->template->body !!}
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="card-footer">
                            <a href="{{ route('notification_rules.index') }}" class="btn btn-secondary">
                                <i class="la la-arrow-left"></i> Back to List
                            </a>
                            @can('Edit Notification Rules')
                            <a href="{{ route('notification_rules.edit', $row->id) }}" class="btn btn-primary">
                                <i class="la la-edit"></i> Edit Rule
                            </a>
                            @endcan
                        </div>
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
