@if ($business && isset($module) && $module == 'Project')
    <div class="col-md-auto col-sm-4 pb-3">
        <a href="{{ route('business-process-mapping.index', ['project_id' => $module_data->id, 'related' => $related->id, 'module' => 'Project']) }}"
            class="btn btn-sm btn-primary me-2" data-toggle="tooltip" data-bs-toggle="tooltip"
            data-bs-original-title="{{ __('Business Mapping') }}">
            <span class="btn-inner--text text-white">
                <i class="ti ti-sitemap"></i>
            </span>
        </a>
    </div>
@elseif ($business && isset($module) && $module == 'Task')
    <a href="{{ route('business-process-mapping.index', ['project_id' => $module_data->id, 'related' => $related->id, 'module' => 'Task']) }}"
        class="btn btn-sm btn-primary me-2" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Business Mapping') }}">
        <i class="ti ti-sitemap"></i>
    </a>
@elseif ($business && isset($module) && $module == 'Lead')
    <a href="{{ route('business-process-mapping.index', ['lead_id' => $module_data->id, 'related' => $related->id, 'module' => 'Lead']) }}"
        class="btn btn-sm btn-primary me-2" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Business Mapping') }}">
        <i class="ti ti-sitemap"></i>
    </a>
@elseif ($business && isset($module) && $module == 'Deal')
    <a href="{{ route('business-process-mapping.index', ['deal_id' => $module_data->id, 'related' => $related->id, 'module' => 'Deal']) }}"
        class="btn btn-sm btn-primary me-2" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Business Mapping') }}">
        <i class="ti ti-sitemap"></i>
    </a>
@elseif ($business && isset($module) && $module == 'Property')
    <a href="{{ route('business-process-mapping.index', ['property_id' => $module_data->id, 'related' => $related->id, 'module' => 'Property']) }}"
        class="btn btn-sm btn-primary me-2" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Business Mapping') }}">
        <i class="ti ti-sitemap"></i>
    </a>
@elseif ($business && isset($module) && $module == 'Contract')
    <a href="{{ route('business-process-mapping.index', ['contract_id' => $module_data->id, 'related' => $related->id, 'module' => 'Contract']) }}"
        class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Business Mapping') }}">
        <i class="ti ti-sitemap"></i>
    </a>
@endif
