@permission('vendoronboard convert')
@if ($vendorOnBoard->status == 'confirm' && $vendorOnBoard->convert_to_vendor == 0 && module_is_active('Account'))
    <div class="action-btn">
        <a data-url="{{ route('vendor.on.board.converts', $vendorOnBoard->id) }}"
            class="mx-3 btn bg-dark btn-sm  align-items-center" data-ajax-popup="true"
            data-bs-toggle="tooltip" data-bs-placement="top" data-size="lg"
            title="{{ __('Convert to Vendor') }}" data-title="{{ __('Convert to Vendor') }}">
            <i class="ti ti-arrows-right-left text-white"></i>
        </a>
    </div>
@endif
@endpermission
@permission('vendoronboard edit')
<div class="action-btn  me-2">
    <a  class="mx-3 btn bg-info btn-sm  align-items-center"
        data-url="{{ route('vendor.on.board.edit', $vendorOnBoard->id) }}"
        data-ajax-popup="true"
        data-title="{{ __('Edit Vendor On-Boarding') }}"
        data-bs-toggle="tooltip" data-bs-placement="top"
        title="{{ __('Edit') }}">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endpermission
@permission('vendoronboard delete')
<div class="action-btn me-2">
    {!! Form::open([
        'method' => 'DELETE',
        'route' => ['vendor.on.board.delete', $vendorOnBoard->id],
        'id' => 'delete-form-' . $vendorOnBoard->id,
    ]) !!}
    <a href="#!"
        class="mx-3 btn bg-danger btn-sm  align-items-center bs-pass-para show_confirm"
        data-bs-toggle="tooltip" data-bs-placement="top"
        title="{{ __('Delete') }}">
        <i class="ti ti-trash text-white"></i></a>
    {!! Form::close() !!}
</div>
@endpermission

