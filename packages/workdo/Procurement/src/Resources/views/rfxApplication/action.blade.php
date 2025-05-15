

@if ($application->is_archive == 0)
<div class="action-btn me-2">
{!! Form::open(['method' => 'DELETE', 'route' => ['rfx.application.archive', $application->id]]) !!}
    <a class="mx-3 btn bg-primary btn-sm  align-items-center bs-pass-para show_confirm"
        data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Archive') }}"><i
            class="ti ti-archive text-white"></i></a>
    {!! Form::close() !!}
</div>
@endif

@permission('rfxapplication show')
<div class="action-btn me-2">
    <a href="{{ route('rfx-application.show', \Crypt::encrypt($application->id)) }}"
        class="mx-3 btn bg-warning btn-sm  align-items-center"
        data-bs-toggle="tooltip" data-bs-placement="top"
        title="{{ __('View') }}">
        <i class="ti ti-eye text-white"></i>
    </a>
</div>
@endpermission

@permission('rfxapplication delete')
<div class="action-btn">
    {!! Form::open([
        'method' => 'DELETE',
        'route' => ['rfx-application.destroy', $application->id],
        'id' => 'delete-form-' . $application->id,
    ]) !!}
    <a href="#!"
        class="mx-3 btn bg-danger btn-sm  align-items-center bs-pass-para show_confirm"
        data-bs-toggle="tooltip" data-bs-placement="top"
        title="{{ __('Delete') }}">
        <i class="ti ti-trash text-white"></i></a>
    {!! Form::close() !!}
</div>
@endpermission
