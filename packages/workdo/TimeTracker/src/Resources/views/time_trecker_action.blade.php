@permission('timetracker show')
    <img alt="Image placeholder" src="{{ asset('assets/images/gallery.png') }}"
        class="avatar view-images rounded-circle avatar-sm me-2" data-toggle="tooltip" title="{{ __('View Screenshot Images') }}"
        style="height: 25px;width:24px;margin-right:10px;cursor: pointer;" data-id="{{ $trecker->id }}"
        id="track-images-{{ $trecker->id }}">
@endpermission
@permission('delete timetracker')
    {!! Form::open([
        'method' => 'DELETE',
        'route' => ['tracker.destroy', $trecker->id],
        'id' => 'delete-form-' . $trecker->id,
        'class' => 'd-inline',
    ]) !!}
    <a href="#" class="action-btn btn-danger btn btn-sm d-inline-flex align-items-center bs-pass-para show_confirm"
        data-toggle="tooltip" title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}"
        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
        data-confirm-yes="delete-form-{{ $trecker->id }}">
        <i class="ti ti-trash"></i>
    </a>
    {!! Form::close() !!}
@endpermission
