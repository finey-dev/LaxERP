@permission('jobapplication show')
    <div class="action-btn me-2">
        <a class="mx-3 btn btn-sm  align-items-center bg-warning" data-bs-toggle="tooltip" title="{{ __('View') }}"
            data-title="{{ __('Details') }}" href="{{ route('job-application.show', \Crypt::encrypt($jobApplication->id)) }}">
            <i class="ti ti-eye text-white"></i></a>
    </div>
@endpermission
