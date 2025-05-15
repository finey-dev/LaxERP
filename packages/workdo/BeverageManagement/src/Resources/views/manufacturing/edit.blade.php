@extends('layouts.main')
@section('page-title')
{{ __('Edit Manufacturing') }}
@endsection
@push('script-page')
@endpush
@section('page-breadcrumb')
{{ __('Manufacturing') }}
@endsection
@php
$company_settings = getCompanyAllSetting();
@endphp
@section('content')
<div class="row">
    {{ Form::model($manufacturing, ['route' => ['manufacturing.update', $manufacturing->id], 'method' => 'PUT', 'class' => 'w-100', 'id' => 'bill_material_form','enctype' => 'multipart/form-data', 'class'=>'needs-validation','novalidate']) }}
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {{ Form::label('bill_of_material_id', __('Bill Of Material'), ['class' => 'form-label']) }}<x-required></x-required>
                            <select class="form-control raw_material" name="bill_of_material_id" id="bill_material" placeholder="Select Raw Material" required>
                                <option value="">{{__('Select Raw Material')}}</option>
                                @foreach($bill_of_materials as $bil_of_material)
                                <option value="{{ $bil_of_material->id }}" @if($bil_of_material->id == $manufacturing->bill_of_material_id) selected @endif>{{ $bil_of_material->productService->name }} - ({{ $bil_of_material->productService->type }})</option>
                                @endforeach
                            </select>
                            @if (count($bill_of_materials) <= 0) <div class="text-muted text-xs">{{ __('Please create new bill of material') }} <a href="{{ route('bill-of-material.index') }}">{{ __('here') }}</a>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group" id="customer-box">
                        {{ Form::label('collection_center_id', __('Collection Center'), ['class' => 'form-label']) }}<x-required></x-required>
                        {{ Form::select('collection_center_id', $collection_centers, null, ['class' => 'form-control select', 'required' => 'required', 'placeholder' => __('Select Collection Center')]) }}
                        @if (count($collection_centers) <= 0) <div class="text-muted text-xs mt-1">{{ __('Please create new collection center') }} <a href="{{ route('collection-center.index') }}">{{ __('here') }}</a>
                            @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group" id="customer-box">
                        {{ Form::label('item_id', __('Product Name'), ['class' => 'form-label']) }}<x-required></x-required>
                        {{ Form::select('item_id', $product, null, ['class' => 'form-control select', 'required' => 'required', 'id' => 'item_id', 'placeholder' => __('Select item')]) }}
                        @if (empty($product_count))
                        <div class="text-muted text-xs mt-1">{{ __('Please create Product first.') }} <a href="{{ route('product-service.index') }}">{{ __('Add Product') }}</a></div>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {{ Form::label('schedule_date', __('Schedule Date'), ['class' => 'form-label']) }}<x-required></x-required>
                        <div class="form-icon-user">
                            {{ Form::date('schedule_date', null, ['class' => 'form-control ', 'id' => 'schedule_date', 'required' => 'required', 'placeholder' => 'Select Due Date']) }}

                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}
                        <div class="form-icon-user">
                            {{ Form::number('quantity', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'quantity', 'required' => 'required', 'min' => 0,'placeholder'=>'Enter Quantity']) }}
                        </div>
                    </div>
                </div>
                @if (module_is_active('CustomField') && !$customFields->isEmpty())
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                            @include('custom-field::formBuilder', [
                            'fildedata' => $manufacturing->customField,
                            ])
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 section_div">
        <div class="edit_div">
            <h5 class="h4 d-inline-block font-weight-400 mb-4">{{ __('Raw Materials') }}</h5>
            <div class="card repeater">
                <div class="card-body table-border-style mt-2">
                    <div class="table-responsive">
                        <table class="table  mb-0 table-custom-style" data-repeater-list="items" id="sortable-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Raw Material') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Unit') }} </th>
                                    <th>{{ __('Price') }}
                                        ({{ isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '' }})
                                    </th>
                                    <th>{{ __('Sub Total') }}
                                        ({{ isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '' }})
                                    </th>
                                    <th>{{ __('Total') }}
                                        ({{ isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '' }})
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="ui-sortable">
                                @php
                                $totalAmount = 0;
                                @endphp

                                @foreach ($bill_material_items as $bill_material_item)
                                @php
                                $subtotal =
                                $bill_material_item->rawMaterial->price *
                                $bill_material_item->quantity;
                                $totalAmount += $subtotal;
                                @endphp
                                <tr>
                                    <td>{{ $bill_material_item->rawMaterial->productService ? $bill_material_item->rawMaterial->productService->name . ' (' . $bill_material_item->rawMaterial->productService->type . ')' : '' }}</td>
                                    <td><input type="hidden" name="raw_quantity[]" value="{{ $bill_material_item->quantity }}">{{ $bill_material_item->unit }}
                                    </td>
                                    <td><input type="hidden" name="raw_material[]" value="{{ $bill_material_item->raw_material_id }}">{{ $bill_material_item->quantity }}
                                    </td>
                                    <td><input type="hidden" name="raw_unit[]" value="{{ $bill_material_item->unit }}">{{ $bill_material_item->rawMaterial->price }}
                                    </td>
                                    <td>{{ $subtotal }}</td>
                                    <td colspan="5"></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4">&nbsp;</td>
                                    <td><strong></strong></td>
                                    <td><input type="hidden" name="total" value="{{ $totalAmount }}"><strong>{{ $totalAmount ? (!empty(company_setting('defult_currancy_symbol')) ? company_setting('defult_currancy_symbol') : '$') . $totalAmount : '' }}</strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" onclick="location.href = '{{ route('manufacturing.index') }}';" class="btn btn-light me-2">
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}
</div>
@endsection
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#bill_material').on('change', function() {
            $('.edit_div').remove();
            var bill_material_id = $(this).val();
            $.ajax({
                url: "{{ route('bill.material') }}",
                type: "POST",
                data: {
                    bill_material_id: bill_material_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    if (result) {
                        $('.section_div').html(result.html);
                        $('#quantity').val(result.quantity);
                    }
                }
            });
        });
    });
</script>
