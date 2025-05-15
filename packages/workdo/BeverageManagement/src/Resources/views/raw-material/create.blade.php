@extends('layouts.main')
@section('page-title')
    {{ __('Create Raw Material') }}
@endsection
@push('script-page')
@endpush
@section('page-breadcrumb')
    {{ __('Raw Material') }}
@endsection
@section('content')
    <div class="row">
        {{ Form::open(['route' => 'raw-material.store', 'class' => 'w-100', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group" id="customer-box">
                                {{ Form::label('collection_center_id', __('Collection Center'), ['class' => 'form-label']) }}<x-required></x-required>
                                {{ Form::select('collection_center_id', $collection_centers, '', ['class' => 'form-control select', 'required' => 'required', 'placeholder' => __('Select Collection Center')]) }}
                                @if (count($collection_centers) <= 0)
                                    <div class="text-muted text-xs mt-1">{{ __('Please create new collection center') }} <a
                                            href="{{ route('collection-center.index') }}">{{ __('here') }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" id="customer-box">
                                {{ Form::label('item_id', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                                <select name="item_id" class="form-control select" id="item_id" required>
                                    <option value="">{{ __('Select item') }}</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} - ({{ $product->type }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="text-muted text-xs mt-1">{{ __('Please add Items Here') }} <a
                                        href="{{ route('product-service.index') }}">{{ __('Add Product') }}</a></div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
                                {{ Form::select('status', ['' => __('Select Status'), 1 => __('Active'), 0 => __('Inactive')], null, ['class' => 'form-control select', 'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                            {{ Form::label('taxes', __('Tax'), ['class' => 'form-label']) }}
                                <div class="input-group colorpickerinput">
                                    <div class="taxes"></div>
                                    {{ Form::hidden('tax', '', ['class' => 'form-control tax text-dark']) }}
                                    {{ Form::hidden('itemTaxPrice', '', ['class' => 'form-control itemTaxPrice']) }}
                                    {{ Form::hidden('itemTaxRate', '', ['class' => 'form-control itemTaxRate']) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {{ Form::label('price', __('Price'), ['class' => 'form-label']) }}
                                <div class="form-icon-user">
                                    {{ Form::number('price', null, ['class' => 'form-control price', 'disabled' => 'disabled']) }}
                                    {{ Form::hidden('price', null, ['class' => 'form-control price']) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {{ Form::label('unit', __('Unit'), ['class' => 'form-label']) }}
                                <div class="form-icon-user">
                                    {{ Form::text('unit', null, ['class' => 'form-control unit', 'disabled' => 'disabled']) }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                                {{ Form::textarea('description', null, ['class' => 'form-control select description', 'disabled' => 'disabled', 'rows' => 2]) }}
                            </div>
                        </div>
                        <div class="col-md-3">

                            <div class="form-group">
                                {{ Form::label('image', __('Image'), ['class' => 'form-label']) }}
                                <div class="choose-file">
                                    <label for="Image">
                                        <img id="image_show"
                                            src='{{ asset('packages/workdo/ProductService/src/Resources/assets/image/img01.jpg') }}'
                                            width="30%" class="mb-2">
                                    </label>
                                </div>
                            </div>
                        </div>

                        @if (module_is_active('CustomField') && !$customFields->isEmpty())
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                                        @include('custom-field::formBuilder')
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <input type="button" value="{{ __('Cancel') }}"
                            onclick="location.href = '{{ route('raw-material.index') }}';" class="btn btn-light me-2">
                        <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
                    </div>
                </div>

            </div>
        </div>

        {{ Form::close() }}

    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#item_id').on('change', function() {
                var item_id = $(this).val();
                $.ajax({
                    url: "{{ route('raw_material.item') }}",
                    type: "POST",
                    data: {
                        item_id: item_id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {

                        var item = result;
                        if (item.product != null) {
                            $('.price').val(item.product.sale_price);
                            $('.description').val(item.product.description);

                        } else {
                            $('.price').val(0);
                            $('.description').val('');
                        }

                        var taxes = '';
                        var tax = [];
                        var totalItemTaxRate = 0;

                        if (item.taxes == 0) {
                            taxes += '-';
                        } else {
                            for (var i = 0; i < item.taxes.length; i++) {
                                taxes +=
                                    '<span class="badge bg-primary p-2 px-3  mt-1 me-1">' +
                                    item.taxes[i].name + ' ' + '(' + item.taxes[i].rate + '%)' +
                                    '</span>';
                                tax.push(item.taxes[i].id);
                                totalItemTaxRate += parseFloat(item.taxes[i].rate);
                            }
                        }

                        var itemTaxPrice = 0;
                        if (item.product != null) {
                            var itemTaxPrice = parseFloat((totalItemTaxRate / 100) * (item
                                .product.sale_price * 1));
                        }
                        $('.itemTaxPrice').val(itemTaxPrice.toFixed(2));
                        $('.itemTaxRate').val(totalItemTaxRate.toFixed(2));
                        $('.taxes').html(taxes);
                        $('.tax').val(tax);
                        $('.unit').val(item.unit);
                        $('#image_show').attr('src', item.imagePath);
                    }
                });
            });
        });
    </script>
@endpush
