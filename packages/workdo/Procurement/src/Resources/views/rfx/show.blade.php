@extends('layouts.main')
@section('page-title')
    {{ __('RFx Details') }}
@endsection
@section('page-action')
    <div>
        @permission('rfx edit')
            <a href="{{ route('rfx.edit', $rfx->id) }}" data-size="md" data-title="{{ __('Edit RFx') }}" data-bs-toggle="tooltip"
                title="" class="btn btn-sm btn-info" data-bs-original-title="{{ __('Edit') }}">
                <i class="ti ti-pencil text-white"></i>
            </a>
        @endpermission
    </div>
@endsection

@section('page-breadcrumb')
    {{ __('Manage RFx') }},
    {{ __('RFx Details') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card invoice">
                <div class="card-body invoice-summary p-3">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="bg-primary text-white text-start">{{ __('RFx Details') }}</th>
                                    <th class="bg-primary text-white text-start" colspan="2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-start">{{ __('RFx Title') }}</td>
                                    <td class="text-start">{{ $rfx->title }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">{{ __('RFx Category') }}</td>
                                    <td class="text-start">{{ !empty($rfx->category) ? $rfx->categories->name : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">{{ __('RFx Type') }}</td>
                                    <td class="text-start">{{ !empty($rfx->rfx_type) ? $rfx->rfx_type : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">{{ __('Positions') }}</td>
                                    <td class="text-start">{{ $rfx->position }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">{{ __('Status') }}</td>
                                    <td class="text-start">
                                        @if ($rfx->status == 'active')
                                            <span
                                                class="p-2 px-3 badge bg-success">{{ \Workdo\Procurement\Entities\Rfx::$status[$rfx->status] }}</span>
                                        @else
                                            <span
                                                class="p-2 px-3 badge bg-danger">{{ \Workdo\Procurement\Entities\Rfx::$status[$rfx->status] }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start">{{ __('Budget From') }}</td>
                                    <td class="text-start">{{ currency_format_with_sym($rfx->budget_from) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">{{ __('Budget To') }}</td>
                                    <td class="text-start">{{ currency_format_with_sym($rfx->budget_to) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">{{ __('Created Date') }}</td>
                                    <td class="text-start">{{ company_date_formate($rfx->created_at) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">{{ __('Start Date') }}</td>
                                    <td class="text-start">{{ company_date_formate($rfx->start_date) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">{{ __('End Date') }}</td>
                                    <td class="text-start">{{ company_date_formate($rfx->end_date) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">{{ __('Skill') }}</td>
                                    <td class="text-start">
                                        @foreach ($rfx->skill as $skill)
                                            <span class="p-2 px-3 badge bg-primary">{{ $skill }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start">{{ __('Location') }}</td>
                                    <td class="text-start">{{ !empty($rfx->location) ? $rfx->location : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-start">{{ __('Billing Type') }}</td>
                                    <td class="text-start">{{ !empty($rfx->billing_type) ? ucFirst($rfx->billing_type) : '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            @if ($rfx->billing_type == 'items')
                                <h2 class="mt-4 mb-3 h3">{{ __('Items') }}</h2>
                                <thead>
                                    <tr>
                                        <th class="bg-primary text-white text-start">{{ __('Item Type') }}</th>
                                        <th class="bg-primary text-white text-start">{{ __('Item Name') }}</th>
                                        <th class="bg-primary text-white text-start">{{ __('Description') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rfxItemData as $rfxItem)
                                        <tr>
                                            <td class="text-start">{{ ucfirst($rfxItem->product_type) }}</td>
                                            <td class="text-start">{{ !empty($rfxItem->product_id) ? $rfxItem->product()->name : '' }}</td>
                                            <td class="text-start">{{ $rfxItem->product_description }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            @elseif($rfx->billing_type == 'rfx')
                                <h2 class="my-3 h3">{{ __('RFx`s Task') }}</h2>
                                <thead>
                                    <tr>
                                        <th class="bg-primary text-white text-start">{{ __('Task') }}</th>
                                        <th class="bg-primary text-white text-start" colspan="2">{{ __('Description') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rfxItemData as $rfxItem)
                                        <tr>
                                            <td class="text-start">{{ $rfxItem->rfx_task }}</td>
                                            <td class="text-start">{{ $rfxItem->rfx_description }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card card-fluid">
                <div class="card-body pb-0 p-3">
                    <div class="col-12">
                        <div class="row">
                            @if ($rfx->applicant)
                                <div class="col-lg-4 col-sm-6">
                                    <h6>{{ __('Need to ask ?') }}</h6>
                                    <ul>
                                        @foreach ($rfx->applicant as $applicant)
                                            <li>{{ ucfirst($applicant) }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (!empty($rfx->visibility))
                                <div class="col-lg-4 col-sm-6">
                                    <h6>{{ __('Need to show option ?') }}</h6>
                                    <ul>
                                        @foreach ($rfx->visibility as $visibility)
                                            <li>{{ ucfirst($visibility) }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (count($rfx->questions()) > 0)
                                <div class="col-lg-4">
                                    <h6>{{ __('Custom Question') }}</h6>
                                    <ul>
                                        @foreach ($rfx->questions() as $question)
                                            <li>{{ $question->question }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h6>{{ __('RFx Description') }}</h6>
                            {!! $rfx->description !!}
                        </div>
                        <div>
                            <h6>{{ __('RFx Requirement') }}</h6>
                            {!! $rfx->requirement !!}
                        </div>
                        @if (!empty($rfx->terms_and_conditions))
                            <div>
                                <h6>{{ __('Terms And Conditions') }}</h6>
                                {!! $rfx->terms_and_conditions !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
