@if ($jobOnBoards->status == 'confirm')
    <div class="action-btn me-2">
        <a href="{{ route('offerlatter.download.pdf', $jobOnBoards->id) }}" class="mx-3 btn btn-sm  align-items-center bg-primary "
            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('OfferLetter PDF') }}" target="_blanks"><i
                class="ti ti-download text-white"></i></a>
    </div>
    <div class="action-btn me-2">
        <a href="{{ route('offerlatter.download.doc', $jobOnBoards->id) }}" class="mx-3 btn btn-sm  align-items-center bg-primary"
            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('OfferLetter DOC') }}" target="_blanks"><i
                class="ti ti-download text-white"></i></a>
    </div>
@endif
@permission('jobonboard convert')
    @if ($jobOnBoards->status == 'confirm' && $jobOnBoards->convert_to_employee == 0 && module_is_active('Hrm') && $jobOnBoards->type == 'internal')
        <div class="action-btn me-2">
            <a href="{{ route('job.on.board.converts', $jobOnBoards->id) }}" class="mx-3 btn btn-sm  align-items-center bg-dark"
                data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Convert to Employee') }}">
                <i class="ti ti-arrows-right-left text-white"></i>
            </a>
        </div>
    @elseif($jobOnBoards->status == 'confirm' && $jobOnBoards->convert_to_employee != 0)
        <div class="action-btn me-2">
            <a href="{{ route('employee.show', \Crypt::encrypt($jobOnBoards->convert_to_employee)) }}"
                class="mx-3 btn btn-sm  align-items-center bg-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ __('View') }}">
                <i class="ti ti-eye text-white"></i>
            </a>
        </div>
    @endif
@endpermission
@permission('jobonboard edit')
    <div class="action-btn me-2">
        <a class="mx-3 btn btn-sm  align-items-center bg-info" data-url="{{ route('job.on.board.edit', $jobOnBoards->id) }}"
            data-ajax-popup="true" data-title="{{ __('Edit Job On-Boarding') }}" data-bs-toggle="tooltip"
            data-bs-placement="top" title="{{ __('Edit') }}">
            <i class="ti ti-pencil text-white"></i>
        </a>
    </div>
@endpermission
@permission('jobonboard delete')
    <div class="action-btn  me-2">
        {!! Form::open([
            'method' => 'DELETE',
            'route' => ['job.on.board.delete', $jobOnBoards->id],
            'id' => 'delete-form-' . $jobOnBoards->id,
        ]) !!}
        <a href="#!" class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm bg-danger" data-bs-toggle="tooltip"
            data-bs-placement="top" title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
            <i class="ti ti-trash text-white"></i></a>
        {!! Form::close() !!}
    </div>
@endpermission

