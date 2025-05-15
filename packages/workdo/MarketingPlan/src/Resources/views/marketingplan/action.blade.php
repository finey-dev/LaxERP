<td class="Action">
    <div class="action-btn me-2">
        <a href="#"
        data-url="{{ route('marketing-plan.receipt', $MarketingPlan->id) }}"
        data-size="lg" data-ajax-popup="true" class="bg-dark btn btn-sm align-items-center"
        data-bs-toggle="tooltip"
        title="{{ __('Print') }}" data-title="{{__('Print Marketing Plan')}}"><i class="text-white ti ti-printer"></i></a>
    </div>

    @permission('marketing plan show')
        <div class="action-btn me-2">
            <a href="{{ route('marketing-plan.show', $MarketingPlan->id) }}"
                class="bg-warning btn btn-sm align-items-center" data-bs-toggle="tooltip"
                title="" data-bs-original-title="{{ __('View') }}">
                <i class="text-white ti ti-eye"></i>
            </a>
        </div>
    @endpermission
    @permission('marketing plan edit')
        <div class="action-btn me-2">
            <a href="{{ route('marketing-plan.edit', $MarketingPlan->id) }}"
                class="bg-info btn btn-sm align-items-center" data-bs-toggle="tooltip"
                title="" data-bs-original-title="{{ __('Edit') }}">
                <i class="text-white ti ti-pencil"></i>
            </a>
        </div>
    @endpermission
    @permission('marketing plan delete')
        <div class="action-btn">
            {!! Form::open([
                'method' => 'DELETE',
                'route' => ['marketing-plan.destroy', $MarketingPlan->id],
                'id' => 'delete-form-' . $MarketingPlan->id,
            ]) !!}
            <a class="bg-danger btn btn-sm align-items-center bs-pass-para show_confirm"
                data-bs-toggle="tooltip" title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}"
                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"><i
                    class="text-white ti ti-trash"></i></a>
            {!! Form::close() !!}
        </div>
    @endpermission
</td>
