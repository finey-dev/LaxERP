@extends('layouts.main')
@section('page-title')
    {{ __('Bill Of Material Detail') }}
@endsection
@push('script-page')
@endpush
@section('page-breadcrumb')
    {{ __('Bill Of Material Detail') }}
@endsection
@php
    $company_settings = getCompanyAllSetting();
@endphp
@section('page-action')
<div>
    <a href="{{ route('bill-of-material.index') }}" data-bs-toggle="tooltip" title="{{__('Back')}}" class="btn btn-sm btn-primary">
        <i class="ti ti-arrow-back-up"></i>
    </a>
</div>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label"><b>{{ __('Product Name:') }}</b></label>
                            <p>{{ $bill_of_material->productService->name }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><b>{{ __('Collection Center:') }}</b></label>
                            <p>{{ $bill_of_material->collectionCenter->location_name }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><b>{{ __('Quantity:') }}</b></label>
                            <p>{{ $bill_of_material->quantity }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><b>{{ __('Status:') }}</b></label>
                            <p>
                                @if ($bill_of_material->status == 1)
                                    <span class="badge bg-primary p-2 px-3">{{ __('Manufactured') }}</span>
                                @elseif($bill_of_material->status == 0)
                                    <span class="badge bg-danger p-2 px-3">{{ __('Pending') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body table-border-style">
                    <h5 class="h4 font-weight-400 mb-0">{{ __('Raw Materials') }}</h5>
                    <div class="table-responsive">
                        <table class="table mt-3 table-custom-style">
                            <thead>
                                <tr>
                                    <th>{{ __('Item') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Unit') }} </th>
                                    <th>{{ __('Price') }}
                                        ({{ isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '' }})
                                    </th>
                                    <th>{{ __('Tax') }} (%)</th>
                                    <th>{{ __('Sub Total') }}
                                        ({{ isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '' }})
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bill_material_items as $bill_material_item)
                                    <tr>
                                        <td>{{ $bill_material_item->rawMaterial && $bill_material_item->rawMaterial->productService ? $bill_material_item->rawMaterial->productService->name . ' (' . $bill_material_item->rawMaterial->productService->type . ')' : '' }}
                                        </td>
                                        <td>{{ $bill_material_item->quantity ?? '' }}</td>
                                        <td>{{ $bill_material_item->unit ?? '' }}</td>
                                        <td>{{ $bill_material_item->price ?? '' }}</td>
                                        <td>
                                            @php
                                                $taxArr = explode(',', $bill_material_item->tax);
                                                $taxes = '';
                                                foreach ($taxArr as $taxId) {
                                                    $tax = \Workdo\ProductService\Entities\Tax::find($taxId);
                                                    if ($tax) {
                                                        $taxes .=
                                                            '<span class="badge bg-primary p-2 px-3  mt-1 me-1">' .
                                                            $tax->name .
                                                            ' (' .
                                                            $tax->rate .
                                                            '%)</span>';
                                                    }
                                                }
                                                echo $taxes;
                                            @endphp
                                        </td>
                                        <td>{{ $bill_material_item->sub_total ?? '' }}</td>
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
