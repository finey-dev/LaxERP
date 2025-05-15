@extends('layouts.main')
@section('page-title')
    {{ __('Manage Quote') }}
@endsection
@section('page-breadcrumb')
    {{ __('Quote') }}
@endsection
@section('page-action')
    <div class="d-flex">
        <a href="{{ route('quote.index') }}" class="btn btn-sm btn-primary btn-icon me-2" data-bs-toggle="tooltip"
            title="{{ __('List View') }}">
            <i class="ti ti-list text-white"></i>
        </a>
        @permission('quote create')
            <a data-url="{{ route('quote.create', ['quote', 0]) }}" data-size="lg" data-ajax-popup="true" data-bs-toggle="tooltip"
                data-title="{{ __('Create Quote') }}" title="{{ __('Create') }}"class="btn btn-sm btn-primary btn-icon">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/letter.avatar.js') }}"></script>
@endpush
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row mb-4 project-wrp d-flex">
                @isset($quotes)
                @foreach($quotes as $quote)
                    <div class="col-xxl-3 col-xl-4 col-md-6 col-12">
                            <div class="project-card">
                                <div class="project-card-inner">
                                    <div class="project-card-header d-flex justify-content-between">
                                        @if($quote->status == 0)
                                            <span class="badge bg-secondary p-2 px-3" style="width: 79px;">{{ __(Workdo\Sales\Entities\Quote::$status[$quote->status]) }}</span>
                                        @elseif($quote->status == 1)
                                            <span class="badge bg-info p-2 px-3" style="width: 79px;">{{ __(Workdo\Sales\Entities\Quote::$status[$quote->status]) }}</span>
                                        @endif
                                        <button type="button"
                                            class="btn btn-light dropdown-toggle d-flex align-items-center justify-content-center"
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="ti ti-dots-vertical text-black"></i>
                                        </button>

                                        <div class="dropdown-menu dropdown-menu-end pointer">
                                            @permission('quote create')
                                            {!! Form::open([
                                                'method' => 'get',
                                                'route' => ['quote.duplicate', $quote->id],
                                                'id' => 'duplicate-form-' . $quote->id,
                                            ]) !!}
                                                <a href="#" class="dropdown-item show_confirm" data-bs-toggle="tooltip" data-title="{{__('Duplicate')}}"><i class="ti ti-copy me-1"></i> {{__('Duplicate')}}</a>
                                                {!! Form::close() !!}
                                            @endpermission
                                        @if ($quote->converted_salesorder_id == 0)
                                            {!! Form::open([
                                                'method' => 'get',
                                                'route' => ['quote.convert', $quote->id],
                                                'id' => 'quotes-form-' . $quote->id,
                                            ]) !!}
                                            <a href="#" class="dropdown-item show_confirm" data-bs-toggle="tooltip" data-title="{{__('Convert to Sale Order')}}"><i class="ti ti-exchange me-1"></i> {{__('Convert to Sale Order')}}</a>
                                            {!! Form::close() !!}
                                        @else
                                        <a href="{{ route('salesorder.show', $quote->converted_salesorder_id) }}" class="dropdown-item " data-bs-toggle="tooltip" data-title="{{__('Sales Order Details')}}"><i class="fab fa-stack-exchange me-1"></i> {{__('Sales Order Details')}}</a>
                                        @endif
                                        @permission('quote show')
                                            <a href="{{ route('quote.show',$quote->id) }}" data-size="md"class="dropdown-item" data-bs-toggle="tooltip" data-title="{{__('Quote Details')}}">
                                                <i class="ti ti-eye me-1"></i> {{__('View')}}
                                            </a>
                                        @endpermission
                                        @permission('quote edit')
                                            <a href="{{ route('quote.edit',$quote->id) }}" class="dropdown-item" data-bs-toggle="tooltip" data-title="{{__('Edit Quote')}}"><i class="ti ti-pencil me-1"></i> {{__('Edit')}}</a>
                                        @endpermission
                                        @permission('quote delete')
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['quote.destroy', $quote->id]]) !!}
                                                <a href="#!" class="dropdown-item show_confirm" data-bs-toggle="tooltip" data-confirm="{{ __('Are You Sure?') }}"
                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                                                <span class="text-danger"><i class="ti ti-trash"></i> {{ __('Delete') }}</span>
                                                </a>
                                            {!! Form::close() !!}
                                        @endpermission
                                        </div>
                                    </div>
                                    <div class="project-card-content">
                                        <div class="project-content-top">
                                            <div class="user-info  d-flex align-items-center">
                                                @if (Laratrust::hasPermission('quote show'))
                                                    <a href="{{ route('quote.show',$quote->id) }}"  data-title="{{__('Quote Details')}}">
                                                        {{Workdo\Sales\Entities\Quote::quoteNumberFormat($quote->quote_id)}}
                                                    </a>
                                                @else
                                                    <a href="#" > {{Workdo\Sales\Entities\Quote::quoteNumberFormat($quote->quote_id)}}</a>
                                                @endif
                                            </div>
                                            <div class="row align-items-center mt-3">
                                                <div class="col-6">
                                                    <h6 class="mb-0 text-break">{{currency_format_with_sym($quote->getTotal())}}</h6>
                                                    <span class="text-sm text-muted">{{__('Total Amount')}}</span>
                                                </div>
                                                <div class="col-6">
                                                    <h6 class="mb-0 text-break">{{currency_format_with_sym($quote->getSubTotal())}}</h6>
                                                    <span class="text-sm text-muted">{{__('Due Amount')}}</span>
                                                </div>
                                            </div>
                                            <div class="row align-items-center mt-3">
                                                <div class="col-6">
                                                    <h6 class="mb-0 text-break">{{currency_format_with_sym($quote->getTotalTax())}}</h6>
                                                    <span class="text-sm text-muted">{{__('Total Tax')}}</span>
                                                </div>
                                                <div class="col-6">
                                                    <h6 class="mb-0 text-break">{{company_date_formate($quote->date_quoted)}}</h6>
                                                    <span class="text-sm text-muted">{{__('Issue Date')}}</span>
                                                 </div>
                                            </div>
                                        </div>
                                        <div
                                            class="project-content-bottom d-flex align-items-center justify-content-between gap-2">
                                            <div class="d-flex align-items-center gap-2 user-image">
                                                @if (\Auth::user()->type != 'Client')
                                                <div class="user-group pt-2">
                                                        <img alt="image" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ $quote->assign_user->name }}"
                                                            @if ($quote->assign_user->avatar) src="{{ get_file($quote->assign_user->avatar) }}" @else src="{{ get_file('avatar.png') }}" @endif
                                                            class="rounded-circle " width="25" height="25">
                                                </div>
                                            @endif
                                            </div>
                                            <div class="comment d-flex align-items-center gap-2">
                                                @permission('quote show')
                                                    <a class="btn btn-sm btn-warning" href="{{route('quote.show',$quote->id)}}" class="dropdown-item" data-toggle="tooltip" title="{{__('View')}}">
                                                        <i class="ti ti-eye text-white"></i>
                                                    </a>
                                                @endpermission
                                                @permission('quote create')
                                                {!! Form::open([
                                                    'method' => 'get',
                                                    'route' => ['quote.duplicate', $quote->id],
                                                    'id' => 'duplicate-form-' . $quote->id,
                                                ]) !!}
                                                    <a href="#" class="btn btn-sm btn-secondary show_confirm" data-toggle="tooltip" title="{{__('Duplicate')}}"><i class="ti ti-copy text-white"></i></a>
                                                    {!! Form::close() !!}
                                                @endpermission
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endisset
                @auth('web')
                    @permission('quote create')
                        <div class="col-xxl-3 col-xl-4 col-md-6 col-12">
                            <div class="project-card-inner">
                                <a  href="#" data-url="{{ route('quote.create',['quote',0]) }}" data-size="lg" data-ajax-popup="true" class=" btn-addnew-project border-primary"
                                    data-title="{{ __('Create Quote') }}">
                                    <div class="bg-primary proj-add-icon">
                                        <i class="ti ti-plus my-2"></i>
                                    </div>
                                    <h6 class="my-2 text-center">{{ __('Create Quote') }}</h6>
                                    <p class="text-muted text-center">{{ __('Click here to add Quote') }}</p>
                                </a>
                            </div>
                        </div>
                    @endpermission
                @endauth
            </div>
        </div>
        {!! $quotes->links('vendor.pagination.global-pagination') !!}
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '#billing_data', function() {
            $("[name='shipping_address']").val($("[name='billing_address']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
        })

        $(document).on('change', 'select[name=opportunity]', function() {

            var opportunities = $(this).val();
            getaccount(opportunities);
        });

        function getaccount(opportunities_id) {
            $.ajax({
                url: '{{ route('quote.getaccount') }}',
                type: 'POST',
                data: {
                    "opportunities_id": opportunities_id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#amount').val(data.opportunitie.amount);
                    $('#name').val(data.opportunitie.name);
                    $('#account_name').val(data.account.name);
                    $('#account_id').val(data.account.id);
                    $('#billing_address').val(data.account.billing_address);
                    $('#shipping_address').val(data.account.shipping_address);
                    $('#billing_city').val(data.account.billing_city);
                    $('#billing_state').val(data.account.billing_state);
                    $('#shipping_city').val(data.account.shipping_city);
                    $('#shipping_state').val(data.account.shipping_state);
                    $('#billing_country').val(data.account.billing_country);
                    $('#billing_postalcode').val(data.account.billing_postalcode);
                    $('#shipping_country').val(data.account.shipping_country);
                    $('#shipping_postalcode').val(data.account.shipping_postalcode);

                }
            });
        }
    </script>
@endpush
