@extends('layouts.main')
@section('page-title')
{{__('Packaging Detail Show')}}
@endsection
@push('script-page')
@endpush
@section('page-breadcrumb')
{{__('Packaging Detail')}}
@endsection
@section('page-action')
<div>
    <a href="{{ route('packaging.index') }}" data-bs-toggle="tooltip" title="{{__('Back')}}" class="btn btn-sm btn-primary">
        <i class="ti ti-arrow-back-up"></i>
    </a>
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label"><b>{{ __('Manufacture:') }}</b></label>
                        <p>{{$packaging->manufacture->productService->name}}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><b>{{ __('Collection Center:') }}</b></label>
                        <p>{{$packaging->collectionCenter->location_name}}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><b>{{ __('Status:') }}</b></label>
                        <p>
                        @if ($packaging->status == 1)
                            <span class="badge fix_badge bg-primary p-2 px-3">Completed</span>
                            @elseif($packaging->status == 0)
                            <span class="badge fix_badge bg-warning p-2 px-3">Pending</span>
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
                                <th>{{ __('Item') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                <th>{{ __('Unit') }} </th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Sub Total') }}</th>
                                <th>{{ __('Total Price') }}</th>
                            </tr>
                        </thead>

                        <tbody class="ui-sortable">
                        @php
                            $totalAmount = 0;
                        @endphp

                            @foreach($packaging_items as $packaging_item)
                            @php
                                $subtotal = $packaging_item->sub_total;
                                $totalAmount += $subtotal;
                            @endphp
                            <tr>
                                <td>{{ $packaging_item->rawMaterial && $packaging_item->rawMaterial->productService ? $packaging_item->rawMaterial->productService->name . ' (' . $packaging_item->rawMaterial->productService->type . ')' : '' }}</td>
                                <td>{{$packaging_item->unit}}</td>
                                <td>{{$packaging_item->quantity}}</td>
                                @if($company_settings['site_currency_symbol_position'] == 'pre')
                                <td>{{ $company_settings['defult_currancy_symbol'] . $packaging_item->price ?? ''}}</td>
                                <td>{{ $company_settings['defult_currancy_symbol'] . $subtotal ?? ''}}</td>
                                @else
                                <td>{{ $packaging_item->price . $company_settings['defult_currancy_symbol']?? ''}}</td>
                                <td>{{ $subtotal . $company_settings['defult_currancy_symbol'] ?? ''}}</td>
                                @endif
                                <td colspan="5"></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                            <td><strong></strong></td>
                            @if($company_settings['site_currency_symbol_position'] == 'pre')
                            <td><input type="hidden" name="total" value="{{$totalAmount}}"><strong>{{ $totalAmount ? $company_settings['defult_currancy_symbol'] .$totalAmount : '' }}</strong></td>
                            @else
                            <td><input type="hidden" name="total" value="{{$totalAmount}}"><strong>{{ $totalAmount ? $totalAmount . $company_settings['defult_currancy_symbol'] : '' }}</strong></td>
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
