@php
$company_settings = getCompanyAllSetting();
@endphp
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
                        <th>{{ __('Price') }} ({{(isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '')}})</th>
                        <th>{{ __('Sub Total') }} ({{(isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '')}})</th>
                        <th>{{ __('Total') }} ({{(isset($company_settings['defult_currancy_symbol']) ? $company_settings['defult_currancy_symbol'] : '')}})</th>
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
                        <td>{{ $bill_material_item->rawMaterial->productService ? $bill_material_item->rawMaterial->productService->name . ' (' . $bill_material_item->rawMaterial->productService->type . ')' : '' }}</td>
                        <td><input type="hidden" name="raw_material[]" value="{{$bill_material_item->raw_material_id}}">{{$bill_material_item->quantity ?? ''}}</td>
                        <td><input type="hidden" name="raw_quantity[]" value="{{$bill_material_item->quantity}}">{{$bill_material_item->unit ?? ''}}</td>
                        <td><input type="hidden" name="raw_unit[]" value="{{$bill_material_item->unit}}">{{$bill_material_item->price ?? ''}}</td>
                        <td>{{ $subtotal }}</td>
                        <td colspan="5"></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">&nbsp;</td>
                        <td><strong></strong></td>
                        <td><input type="hidden" name="total" value="{{ $totalAmount }}"><strong>{{ $totalAmount ? (!empty($company_settings['default_currency_symbol']) ? $company_settings['default_currency_symbol'] : '$') . number_format($totalAmount, 2) : '' }}</strong></td>

                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>