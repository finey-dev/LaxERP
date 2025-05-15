@permission('journalentry edit')
<div class="action-btn me-2">
    <a data-title="{{__('Edit Journal')}}"
       href="{{ route('journal-entry.edit',[$journalEntry->id]) }}"
       class="bg-info btn btn-sm align-items-center" data-bs-toggle="tooltip"
       title="{{__('Edit')}}" data-original-title="{{__('Edit')}}">
        <i class="ti ti-pencil text-white"></i>
    </a>
</div>
@endpermission

@permission('journalentry delete')
<div class="action-btn">
    {!! Form::open(['method' => 'DELETE', 'route' => ['journal-entry.destroy', $journalEntry->id, 'enctype' => 'multipart/form-data']]) !!}
    <a href ="#!" class="bg-danger btn btn-sm align-items-center text-white show_confirm"
       data-bs-toggle="tooltip" title='Delete'>
        <i class="ti ti-trash"></i>
    </a>
    {!! Form::close() !!}
</div>
@endpermission
