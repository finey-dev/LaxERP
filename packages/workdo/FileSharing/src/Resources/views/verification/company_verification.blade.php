@extends('layouts.main')
@section('page-title')
    {{ __('Manage Verification') }}
@endsection
@section('page-breadcrumb')
    {{ __('Verification') }}
@endsection
@section('content')
    <div class="card mt-3">
        <div class="card-body">
            <div class="row timeline-wrapper">
                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <div class="progress mb-3">
                        <div class="progress-value"></div>
                    </div>
                    <div class="d-flex align-items-start gap-2">
                        <div class="timeline-icons">
                            <i class="ti ti-plus text-info"></i>
                        </div>
                        <div>
                            <h6 class="text-info mb-2" style="margin-top: 10px">{{ __('Create/Edit Verification') }}</h6>

                            @if (!empty($lastVerification) && $lastVerification->status == 0)
                                <small>{{ __('You can create and edit verification requests from here') }}</small>
                                </br>
                                <a href="#" data-ajax-popup="true" data-size="md" class="btn btn-sm btn-light"
                                    data-bs-toggle="tooltip"
                                    data-url="{{ route('file-verification.edit', $lastVerification->id) }}"
                                    title="{{ __('Edit') }}" data-title="{{ __('Edit Verification Document') }}">
                                    <i class="ti ti-edit me-1"></i>{{ __('Edit') }}</a>
                            @elseif(empty($lastVerification) || $lastVerification->status == 2)
                                <small>{{ __('You can create and edit verification requests from here') }}</small>
                                </br>
                                <a href="#" data-ajax-popup="true" data-size="md" class="btn btn-sm btn-light mt-2"
                                    data-bs-toggle="tooltip" data-url="{{ route('file-verification.create') }}"
                                    title="{{ __('Create') }}" data-title="{{ __('Upload Verification Document') }}"><i
                                        class="ti ti-pencil me-1"></i>{{ __('Create') }}</a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <div class="progress mb-3">
                        @if (!empty($lastVerification))
                            <div class="progress-value"></div>
                        @endif
                    </div>
                    <div class="d-flex align-items-start gap-2">
                        <div class="timeline-icons">
                            <i class="ti ti-send text-warning"></i>
                        </div>
                        <div class="verification-content">
                            <h6 class="text-warning  mb-2" style="margin-top: 10px">{{ __('Send Request') }}</h6>
                            @if (!empty($lastVerification) && $lastVerification->status == 1)
                                <small>{{ __('Your verification requests successfully approved.') }}</small>
                            @endif
                            @if (!empty($lastVerification) && $lastVerification->status == 0)
                                {{ __('Pending') }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <div class="progress mb-3">
                        @if (!empty($lastVerification) && $lastVerification->status != 0)
                            <div class="progress-value"></div>
                        @endif
                    </div>
                    <div class="d-flex align-items-start gap-2">
                        <div class="timeline-icons">
                            <i class="ti ti-pencil text-info"></i>
                        </div>
                        <div class="verification-content" style="flex: 1">

                            <h6 class="text-info mb-2" style="margin-top: 10px">{{ __('Status') }}</h6>
                            <p class=" text-sm mb-3">
                                {{ __('Once the Super Admin approves or rejects the verification, the status will automatically update on your end.') }}
                                :

                                </br>
                                @if (!empty($lastVerification) && $lastVerification->status == 0)
                                    <span
                                        class="badge bg-warning p-2 px-3 text-white mt-2">{{ __(\Workdo\FileSharing\Entities\FileSharingVerification::$statues[$lastVerification->status]) }}</span>
                                @elseif (!empty($lastVerification) && $lastVerification->status == 1)
                                    <span
                                        class="badge bg-success p-2 px-3 text-white mt-2">{{ __(\Workdo\FileSharing\Entities\FileSharingVerification::$statues[$lastVerification->status]) }}</span>
                                @elseif (!empty($lastVerification) && $lastVerification->status == 2)
                                    <span
                                        class="badge bg-danger p-2 px-3 text-white mt-2">{{ __(\Workdo\FileSharing\Entities\FileSharingVerification::$statues[$lastVerification->status]) }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body table-border-style">
            <h5></h5>
            <div class="table-responsive">
                <table class="table mb-0 pc-dt-simple" id="products">
                    <thead>
                        <tr>
                            <th>{{ __('Applied Date') }}</th>
                            <th>{{ __('Action Date') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Attachment') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($verifications as $verification)
                            <tr>
                                <td>{{ isset($verification->applied_date) ? $verification->applied_date : '-' }}</td>
                                <td>{{ isset($verification->action_date) ? $verification->action_date : '-' }}</td>
                                <td>
                                    @if ($verification->status == 0)
                                        <span
                                            class="badge bg-warning p-2 px-3 text-white">{{ __(\Workdo\FileSharing\Entities\FileSharingVerification::$statues[$verification->status]) }}</span>
                                    @elseif ($verification->status == 1)
                                        <span
                                            class="badge bg-success p-2 px-3 text-white">{{ __(\Workdo\FileSharing\Entities\FileSharingVerification::$statues[$verification->status]) }}</span>
                                    @elseif ($verification->status == 2)
                                        <span
                                            class="badge bg-danger p-2 px-3 text-white">{{ __(\Workdo\FileSharing\Entities\FileSharingVerification::$statues[$verification->status]) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-btn bg-primary ms-2">
                                        <a href="{{ get_file($verification->attachment) }}"
                                            class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="{{ __('Download') }}" download>
                                            <i class="ti ti-download text-white"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
