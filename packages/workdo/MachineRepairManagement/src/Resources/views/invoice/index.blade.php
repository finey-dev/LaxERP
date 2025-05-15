@extends('layouts.main')
@section('page-title')
    {{ __('Diagnosis') }}  {{--Machine Repair Invoices--}}
@endsection
@section('page-breadcrumb')
    {{ __('Diagnosis') }}
@endsection
@push('css')
@endpush
@section('page-action')
    <div>
        {{-- @stack('addButtonHook') --}}
        @if (module_is_active('ProductService'))
        @permission('category create')
            <a href="{{ route('category.index') }}"data-size="md" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"data-title="{{ __('Setup') }}" title="{{ __('Setup') }}"><i class="ti ti-settings"></i></a>
        @endpermission
        @endif
        @if ((module_is_active('ProductService')))
            {{-- @permission('invoice manage')
                <a href="{{ route('machine-repair-invoice.grid.view') }}"  data-bs-toggle="tooltip" data-bs-original-title="{{__('Grid View')}}" class="btn btn-sm btn-primary btn-icon">
                    <i class="ti ti-layout-grid"></i>
                </a>
            @endpermission --}}

            @permission('machine diagnosis create')
                <a href="{{ route('machine.invoice.create', \Crypt::encrypt(0)) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                    data-bs-original-title="{{ __('Create') }}">
                    <i class="ti ti-plus"></i>
                </a>
            @endpermission
        @endif
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="mt-2" id="multiCollapseExample1">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => ['machine-repair-invoice.index'], 'method' => 'GET', 'id' => 'machine_submit']) }}
                    <div class="row d-flex align-items-center justify-content-end">
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                            <div class="btn-box">
                                {{ Form::label('issue_date', __('Issue Date'), ['class' => 'form-label']) }}
                                {{ Form::text('issue_date', isset($_GET['issue_date']) ? $_GET['issue_date'] : null, ['class' => 'form-control flatpickr-to-input','placeholder' => 'Select Date']) }}

                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                            <div class="btn-box">
                                {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
                                {{ Form::select('status', ['' => 'Select Status'] + $status, isset($_GET['status']) ? $_GET['status'] : '', ['class' => 'form-control select']) }}
                            </div>
                        </div>
                        <div class="col-auto float-end ms-2 mt-4">

                            <a href="#" class="btn btn-sm btn-primary"
                                onclick="document.getElementById('machine_submit').submit(); return false;"
                                data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                data-original-title="{{ __('apply') }}">
                                <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                            </a>
                            <a href="{{ route('machine-repair-invoice.index') }}" class="btn btn-sm btn-danger" data-toggle="tooltip"
                                data-original-title="{{ __('Reset') }}" title="{{ __('Reset') }}">
                                <span class="btn-inner--icon"><i class="ti ti-trash-off text-white-off"></i></span>
                            </a>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple" id="assets">
                            <thead>
                                <tr>
                                    <th>{{ __('Invoice') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Issue Date') }}</th>
                                    <th>{{ __('Due Date') }}</th>
                                    <th>{{ __('Due Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $invoice)
                                    <tr>
                                        <td class="Id">
                                            @if (Laratrust::hasPermission('machine diagnosis show'))
                                                <a href="{{ route('machine-repair-invoice.show', \Crypt::encrypt($invoice->id)) }}"
                                                    class="btn btn-outline-primary">{{ \Workdo\MachineRepairManagement\Entities\MachineInvoice::machineInvoiceNumberFormat($invoice->invoice_id) }}</a>
                                            @else
                                                <a href="#"
                                                    class="btn btn-outline-primary">{{ \Workdo\MachineRepairManagement\Entities\MachineInvoice::machineInvoiceNumberFormat($invoice->invoice_id) }}</a>
                                            @endif
                                        </td>
                                        <td>{{ !empty($invoice->customer_name) ? $invoice->customer_name : '' }}</td>
                                        <td>{{ company_date_formate($invoice->issue_date) }}</td>
                                        <td>
                                            @if ($invoice->due_date < date('Y-m-d'))
                                                <p class="text-danger mb-0">
                                                    {{ company_date_formate($invoice->due_date) }}</p>
                                            @else
                                                {{ company_date_formate($invoice->due_date) }}
                                            @endif
                                        </td>
                                        <td>{{ currency_format_with_sym($invoice->getDue()) }}</td>
                                        <td>
                                            @if ($invoice->status == 0)
                                                <span
                                                    class="badge fix_badges bg-primary p-2 px-3">{{ __(\Workdo\MachineRepairManagement\Entities\MachineInvoice::$statues[$invoice->status]) }}</span>
                                        
                                            @elseif($invoice->status == 1)
                                                <span
                                                    class="badge fix_badges bg-secondary p-2 px-3">{{ __(\Workdo\MachineRepairManagement\Entities\MachineInvoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 2)
                                                <span
                                                    class="badge fix_badges bg-warning p-2 px-3">{{ __(\Workdo\MachineRepairManagement\Entities\MachineInvoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 3)
                                                <span
                                                    class="badge fix_badges bg-danger p-2 px-3">{{ __(\Workdo\MachineRepairManagement\Entities\MachineInvoice::$statues[$invoice->status]) }}</span>
                                            @endif
                                        </td>

                                            <td class="Action">
                                                <span>
                                                    @permission('machine invoice payment create')
                                                        <div class="action-btn me-2">
                                                            <a href="#" data-url="{{ route('machine.invoice.payment', $invoice->id) }}"
                                                                data-ajax-popup="true" data-title="{{ __('Add Payment') }}" class="btn btn-sm bg-primary align-items-center"
                                                                data-original-title="{{ __('Add Payment') }}"data-bs-toggle="tooltip"  title="{{ __('Add Payment') }}"><i
                                                                    class="ti ti-report-money text-white"></i></a>
                                                        </div>
                                                    @endpermission
                                                    @permission('machine diagnosis show')
                                                        <div class="action-btn me-2">
                                                            <a href="{{ route('machine-repair-invoice.show', \Crypt::encrypt($invoice->id)) }}"
                                                                class="btn btn-sm  bg-warning align-items-center"
                                                                data-bs-toggle="tooltip" title="{{ __('View') }}">
                                                                <i class="ti ti-eye text-white"></i>
                                                            </a>
                                                        </div>
                                                    @endpermission
                                                    @if (module_is_active('ProductService'))
                                                        @permission('machine diagnosis edit')
                                                            <div class="action-btn me-2">
                                                                <a href="{{ route('machine-repair-invoice.edit', \Crypt::encrypt($invoice->id)) }}"
                                                                    class="btn btn-sm  bg-info align-items-center"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-original-title="{{ __('Edit') }}">
                                                                    <i class="ti ti-pencil text-white"></i>
                                                                </a>
                                                            </div>
                                                        @endpermission
                                                    @endif
                                                    @permission('machine diagnosis delete')
                                                        <div class="action-btn">
                                                            {{ Form::open(['route' => ['machine-repair-invoice.destroy', $invoice->id], 'class' => 'm-0']) }}
                                                            @method('DELETE')
                                                            <a href="#"
                                                                class="btn btn-sm  bg-danger align-items-center bs-pass-para show_confirm"
                                                                data-bs-toggle="tooltip" title=""
                                                                data-bs-original-title="Delete" aria-label="Delete"
                                                                data-confirm="{{ __('Are You Sure?') }}"
                                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="delete-form-{{ $invoice->id }}">
                                                                <i class="ti ti-trash text-white text-white"></i>
                                                            </a>
                                                            {{ Form::close() }}
                                                        </div>
                                                    @endpermission
                                                </span>
                                            </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).on("click",".cp_link",function() {
            var value = $(this).attr('data-link');
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(value).select();
                document.execCommand("copy");
                $temp.remove();
                toastrs('success', '{{__('Link Copy on Clipboard')}}', 'success')
        });
    </script>
@endpush






