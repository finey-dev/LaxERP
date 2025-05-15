@extends('layouts.main')
@section('page-title')
    {{ __('Create Manufacturing') }}
@endsection
@push('script-page')
@endpush
@section('page-breadcrumb')
    {{ __('Manufacturing') }}
@endsection
@section('content')
    <div class="row">
        {{ Form::open(['route' => 'manufacturing.store', 'class' => 'w-100', 'id' => 'bill_material_form', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('bill_of_material_id', __('Bill Of Material'), ['class' => 'form-label']) }}<x-required></x-required>
                                <select class="form-control raw_material" name="bill_of_material_id" id="bill_material"
                                    placeholder="Select Raw Material" required>
                                    <option value="">{{ __('Select Raw Material') }}</option>
                                    @foreach ($bil_of_materials as $bil_of_material)
                                        <option value="{{ $bil_of_material->id }}">
                                            {{ $bil_of_material->productService->name }} -
                                            ({{ $bil_of_material->productService->type }})
                                        </option>
                                    @endforeach
                                </select>
                                @if (count($bil_of_materials) <= 0)
                                    <div class="text-muted text-xs mt-1">{{ __('Please create new bill of material') }} <a
                                            href="{{ route('bill-of-material.index') }}">{{ __('here') }}</a></div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group" id="customer-box">
                                {{ Form::label('collection_center_id', __('Collection Center'), ['class' => 'form-label']) }}<x-required></x-required>
                                {{ Form::select('collection_center_id', $collection_centers, '', ['class' => 'form-control select', 'required' => 'required', 'placeholder' => __('Select Collection Center')]) }}
                                @if (count($collection_centers) <= 0)
                                    <div class="text-muted text-xs mt-1">{{ __('Please create new collection center') }} <a
                                            href="{{ route('collection-center.index') }}">{{ __('here') }}</a></div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group" id="customer-box">
                                {{ Form::label('item_id', __('Product Name'), ['class' => 'form-label']) }}<x-required></x-required>
                                {{ Form::select('item_id', $product, '', ['class' => 'form-control select', 'required' => 'required', 'id' => 'item_id', 'placeholder' => __('Select item')]) }}
                                @if (empty($product_count))
                                    <div class="text-muted text-xs mt-1">{{ __('Please create Product first.') }} <a
                                            href="{{ route('product-service.index') }}">{{ __('Add Product') }}</a></div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('schedule_date', __('Schedule Date'), ['class' => 'form-label']) }}<x-required></x-required>
                                <div class="form-icon-user">
                                    {{ Form::date('schedule_date', '', ['class' => 'form-control ', 'id' => 'schedule_date', 'required' => 'required', 'placeholder' => 'Select Due Date']) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}<x-required></x-required>
                                <div class="form-icon-user">
                                    {{ Form::number('quantity', null, ['class' => 'form-control', 'id' => 'quantity', 'required' => 'required', 'min' => '1', 'placeholder' => 'Enter Quantity']) }}
                                </div>
                            </div>
                        </div>
                        @if (module_is_active('CustomField') && !$customFields->isEmpty())
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                                        @include('custom-field::formBuilder')
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 section_div"></div>
        </div>

        <div class="modal-footer">
            <input type="button" value="{{ __('Cancel') }}"
                onclick="location.href = '{{ route('manufacturing.index') }}';" class="btn btn-light me-2">
            <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
        </div>
        {{ Form::close() }}
    </div>
@endsection
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#bill_material').on('change', function() {
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
                    }
                }
            });
        });
    });
    $(document).ready(function() {
        $('#quantity').on('change', function() {
            var quantity = $(this).val();
            if (quantity === '-1') {
                $(this).val('');
                alert('Please enter a valid quantity.');
            }
        });
    });
</script>
