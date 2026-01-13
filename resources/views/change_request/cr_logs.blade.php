<!-- Modern History Logs Modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="historyLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header border-0 bg-primary">
                <div class="w-100 d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="modal-title text-white font-weight-bolder mb-1">
                            <i class="flaticon2-list-2 mr-2"></i>
                            Change Request History
                        </h3>
                        <p class="text-white-75 mb-0 font-size-sm">Activity timeline and audit trail</p>
                    </div>
                    <button type="button" id="close_logs" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body bg-light p-8">
                @if($logs_ers->count() > 0)
                    <div class="timeline-wrapper">
                        @foreach($logs_ers as $key => $log)
                            @php
                                $colorClass = $key % 3 == 0 ? 'primary' : ($key % 3 == 1 ? 'success' : 'info');
                            @endphp

                            <div class="timeline-item-wrapper">
                                <div class="timeline-item">
                                    <!-- Timeline Badge -->
                                    <div class="timeline-badge bg-{{ $colorClass }}">
                                        @if($key % 3 == 0)
                                            <i class="flaticon2-user text-white"></i>
                                        @elseif($key % 3 == 1)
                                            <i class="flaticon2-edit text-white"></i>
                                        @else
                                            <i class="flaticon2-layers-1 text-white"></i>
                                        @endif
                                    </div>

                                    <!-- Timeline Content Card -->
                                    <div class="timeline-content-card">
                                        <div class="card card-custom shadow-sm border-0 mb-0">
                                            <div class="card-body p-6">
                                                <!-- User Header -->
                                                <div class="d-flex align-items-center justify-content-between mb-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="symbol symbol-40 symbol-light-{{ $colorClass }} mr-4">
                                                            <span class="symbol-label font-size-h5 font-weight-bold text-{{ $colorClass }}">
                                                                {{ strtoupper(substr($log->user->user_name, 0, 2)) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <div class="text-dark font-weight-bold font-size-h6 mb-1">
                                                                {{ $log->user->user_name }}
                                                            </div>
                                                            <span class="label label-inline label-light-{{ $colorClass }} font-weight-bold">
                                                                {{ $log->user->defualt_group->title }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="text-muted font-size-sm font-weight-bold">
                                                            <i class="flaticon2-calendar-9 text-{{ $colorClass }} mr-1"></i>
                                                            {{ $log->created_at->format('M d, Y') }}
                                                        </div>
                                                        <div class="text-muted font-size-xs mt-1">
                                                            <i class="flaticon2-time text-{{ $colorClass }} mr-1"></i>
                                                            {{ $log->created_at->format('g:i A') }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Log Message -->
                                                <div class="bg-light-{{ $colorClass }} rounded p-4">
                                                    <p class="text-dark-75 font-size-lg mb-0" style="line-height: 1.6;">
                                                        {{ $log->log_text }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-20">
                        <div class="mb-10">
                            <i class="flaticon2-file-1 text-muted" style="font-size: 6rem; opacity: 0.3;"></i>
                        </div>
                        <h3 class="font-weight-boldest text-dark-75 font-size-h3 mb-3">
                            No History Logs Available
                        </h3>
                        <p class="text-muted font-size-lg">
                            There are no activity logs recorded for this change request yet.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-0 bg-light pt-0 pb-6 px-8" style="display: none;">
            </div>
        </div>
    </div>
</div>

@push('css')
    <style>
        /* Modal Enhancements */
        #modal .modal-dialog {
            max-width: 1200px;
        }

        #modal .modal-content {
            border-radius: 12px;
            overflow: hidden;
            border: none;
        }

        #modal .modal-header {
            padding: 1.75rem 2rem;
        }

        #modal .modal-header .modal-title i {
            color: #ffffff !important;
        }

        #modal .modal-header .close {
            padding: 0 !important;
            margin: 0 !important;
            margin-left: 3rem !important;
            font-size: 2.5rem !important;
            line-height: 1 !important;
            font-weight: 200 !important;
            opacity: 1 !important;
            color: #ffffff !important;
            text-shadow: none !important;
            outline: none !important;
            transition: all 0.3s ease !important;
            z-index: 10 !important;
            background: rgba(255, 255, 255, 0.2) !important;
            border: none !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            visibility: visible !important;
            position: relative !important;
            float: none !important;
            width: 40px !important;
            height: 40px !important;
            border-radius: 8px !important;
        }

        #modal .modal-header .close span {
            color: #ffffff !important;
            opacity: 1 !important;
            display: inline-block !important;
            visibility: visible !important;
        }

        #modal .modal-header .close:focus {
            outline: none !important;
            box-shadow: none !important;
        }

        #modal .modal-header .close:not(:disabled):not(.disabled) {
            opacity: 1 !important;
        }

        #modal .modal-body {
            max-height: 600px;
            overflow-y: auto;
        }

        /* Timeline Styles */
        .timeline-wrapper {
            position: relative;
            padding-left: 30px;
        }

        .timeline-wrapper::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 20px;
            bottom: 20px;
            width: 3px;
            background: linear-gradient(to bottom, #3699FF, #1BC5BD, #8950FC);
            border-radius: 10px;
        }

        .timeline-item-wrapper {
            position: relative;
            margin-bottom: 2rem;
        }

        .timeline-item-wrapper:last-child {
            margin-bottom: 0;
        }

        .timeline-item {
            position: relative;
            display: flex;
            align-items: flex-start;
        }

        /* Timeline Badge */
        .timeline-badge {
            position: absolute;
            left: -30px;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            box-shadow: 0 0 0 6px #f3f6f9;
            transition: all 0.3s ease;
        }

        .timeline-item:hover .timeline-badge {
            transform: scale(1.1);
            box-shadow: 0 0 0 8px #f3f6f9, 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .timeline-badge i {
            font-size: 1.3rem;
        }

        /* Timeline Content Card */
        .timeline-content-card {
            flex: 1;
            margin-left: 30px;
        }

        .timeline-content-card .card {
            transition: all 0.3s ease;
        }

        .timeline-content-card .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
        }

        /* Symbol Styles */
        .symbol.symbol-40 {
            width: 40px;
            height: 40px;
        }

        .symbol .symbol-label {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        /* Label Styles */
        .label.label-inline {
            padding: 0.45rem 0.85rem;
            border-radius: 6px;
            font-size: 0.85rem;
        }

        /* Scrollbar Styling */
        #modal .modal-body::-webkit-scrollbar {
            width: 10px;
        }

        #modal .modal-body::-webkit-scrollbar-track {
            background: #f1f3f6;
            border-radius: 10px;
        }

        #modal .modal-body::-webkit-scrollbar-thumb {
            background: #b5b5c3;
            border-radius: 10px;
        }

        #modal .modal-body::-webkit-scrollbar-thumb:hover {
            background: #7e8299;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            #modal .modal-dialog {
                max-width: 95%;
                margin: 1rem auto;
            }

            .timeline-wrapper {
                padding-left: 20px;
            }

            .timeline-wrapper::before {
                left: 12px;
            }

            .timeline-badge {
                left: -22px;
                width: 36px;
                height: 36px;
            }

            .timeline-badge i {
                font-size: 1.1rem;
            }

            .timeline-content-card {
                margin-left: 20px;
            }

            .timeline-content-card .card-body {
                padding: 1.25rem !important;
            }

            .timeline-item-wrapper {
                margin-bottom: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            #modal .modal-header {
                padding: 1.25rem 1.5rem;
            }

            #modal .modal-body {
                padding: 1.5rem !important;
                max-height: 500px;
            }

            #modal .modal-footer {
                padding: 1rem 1.5rem 1.5rem !important;
            }

            .timeline-content-card .d-flex {
                flex-direction: column !important;
            }

            .timeline-content-card .text-right {
                text-align: left !important;
                margin-top: 0.75rem;
            }

            .symbol.symbol-40 {
                width: 35px;
                height: 35px;
            }

            .symbol .symbol-label {
                font-size: 0.9rem !important;
            }
        }

        /* Animation */
        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out;
        }

        .modal.show .modal-dialog {
            transform: none;
        }

        /* Card Background Colors */
        .bg-light-primary {
            background-color: #E1F0FF !important;
        }

        .bg-light-success {
            background-color: #C9F7F5 !important;
        }

        .bg-light-info {
            background-color: #EEE5FF !important;
        }

        /* Text Colors */
        .text-white-75 {
            color: rgba(255, 255, 255, 0.75) !important;
        }

        .text-dark-75 {
            color: #3F4254 !important;
        }
    </style>
@endpush
