@extends('layouts.main')
@section('page-title')
{{__('Manufacturing Detail')}}
@endsection
@push('script-page')
@endpush
@section('page-breadcrumb')
{{__('Manufacturing Detail')}}
@endsection
@php
$company_settings = getCompanyAllSetting();
@endphp
@section('page-action')
<div>
    <a href="{{ route('manufacturing.index') }}" data-bs-toggle="tooltip" title="{{__('Back')}}" class="btn btn-sm btn-primary">
        <i class="ti ti-arrow-back-up"></i>
    </a>
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        {{-- <div class="text-end mb-2">
            <input type="button" value="{{ __('Back') }}" onclick="location.href = '{{ route('manufacturing.index') }}';" class="btn btn-primary mb-2">
        </div> --}}
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <label class="form-label"><b>{{ __('Bill OF Material Name:') }}</b></label>
                        <p>
                            @if($manufacturing->billOfMaterial && $manufacturing->billOfMaterial->productService)
                            {{ $manufacturing->billOfMaterial->productService->name }} ({{ $manufacturing->billOfMaterial->productService->type }})
                            @else
                            {{ '' }}
                            @endif
                        </p>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label"><b>{{ __('Collection Center:') }}</b></label>
                        <p>{{$manufacturing->collectionCenter->location_name}}</p>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label"><b>{{ __('Product Name:') }}</b></label>
                        <p>{{$manufacturing->productService->name ?? ''}}</p>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label"><b>{{ __('Quantity:') }}</b></label>
                        <p>{{$manufacturing->quantity ?? ''}}</p>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label"><b>{{ __('Status:') }}</b></label>
                        <p>
                            @if ($manufacturing->status == 1)
                            <span class="badge fix_badge bg-primary p-2 px-3">{{__('Completed')}}</span>
                            @elseif($manufacturing->status == 0)
                            <span class="badge fix_badge bg-warning p-2 px-3">{{__(('Pending'))}}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>


            <div class="card-body table-border-style mt-2">
                <h5 class="h4 d-inline-block font-weight-400 mb-4">{{ __('Raw Materials') }}</h5>
                <div class="table-responsive">
                    <table class="table mt-3 table-custom-style" data-repeater-list="items" id="sortable-table">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                <th>{{ __('Unit') }} </th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Sub Total') }} </th>
                            </tr>
                        </thead>

                        <tbody class="ui-sortable">
                            @php
                            $totalAmount = 0;
                            @endphp

                            @foreach($bill_material_items as $bill_material_item)
                            @php
                            $subtotal = $bill_material_item->sub_total;
                            $totalAmount += $subtotal;
                            @endphp
                            <tr>
                                <td>{{ $bill_material_item->rawMaterial && $bill_material_item->rawMaterial->productService ? $bill_material_item->rawMaterial->productService->name . ' (' . $bill_material_item->rawMaterial->productService->type . ')' : '' }}</td>

                                <td>{{$bill_material_item->quantity ?? ''}}</td>
                                <td>{{$bill_material_item->unit ?? ''}}</td>
                                @if($company_settings['site_currency_symbol_position'] == 'pre')
                                <td>{{ $company_settings['defult_currancy_symbol'] . $bill_material_item->price ?? ''}}</td>
                                <td>{{ $company_settings['defult_currancy_symbol'] . $subtotal  ?? ''}}</td>
                                @else
                                <td>{{$bill_material_item->price . $company_settings['defult_currancy_symbol'] ?? ''}}</td>
                                <td>{{ $subtotal . $company_settings['defult_currancy_symbol'] ?? ''}}</td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3"></td>
                                <td><strong>{{__('Total')}}</strong></td>
                                @if($company_settings['site_currency_symbol_position'] == 'pre')
                                <td><input type="hidden" name="total" value="{{$totalAmount}}"><strong>{{ $totalAmount ? $company_settings['defult_currancy_symbol'] . $totalAmount :'' }}</strong></td>
                                @else
                                <td><input type="hidden" name="total" value="{{$totalAmount}}"><strong>{{ $totalAmount ?  $totalAmount . $company_settings['defult_currancy_symbol'] : '' }}</strong></td>
                                @endif
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
