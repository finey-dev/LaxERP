
@permission('Requests create')
<div class="action-btn me-2">
    <a data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Click To Copy Link')}}" class="mx-3 btn btn-sm align-items-center cp_link bg-primary" data-toggle="tooltip" data-original-title="{{__('Click to copy link')}} "  data-link="{{ route('response.form.show' ,$req->code)}}"><i class="ti ti-file text-white"></i></a>
 </div>
 @endpermission
 @permission('Requests formfield manage')
 <div class="action-btn me-2">
    <a href="{{route('requests-formfield.show',$req->id)}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Edit/View Form Field Button')}}" class="btn btn-icon btn-sm bg-info " data-toggle="tooltip" data-original-title="{{__('Edit/View Form Field Button')}}"><i class="ti ti-table text-white"></i></a>
 </div>
@endpermission
@permission('Requests convert lead')
@if(Module_is_active('Lead'))
<div class="action-btn me-2">
    <a class="btn btn-icon btn-sm bg-success" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Convert To Lead')}}"  data-url="{{route('request.field.bind' ,$req->id)}}" data-ajax-popup="true" data-title="{{__('Convert To Lead')}}" class="edit-icon bg-success " data-toggle="tooltip" data-original-title="{{__('Convert To Lead')}}"><i class="ti ti-exchange text-white"></i></a>
</div>
@endif
@endpermission

 @permission('Requests response manage')
<div class="action-btn me-2">
    <a href="{{route('requests.response.show' ,$req->id)}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('View Response')}}" class="btn btn-icon btn-sm bg-warning " data-toggle="tooltip" data-original-title="{{__('View')}}"><i class="ti ti-eye text-white"></i></a>
</div>
@endpermission
 @permission('Requests edit')
 <div class="action-btn me-2">
     <a data-url="{{ route('requests.edit', $req->id) }}" class="mx-3 btn btn-sm bg-info align-items-center"
      data-title="{{ __('Edit Requests') }}"
         data-bs-toggle="tooltip" title="" data-size="lg"
         data-bs-original-title="{{ __('Edit') }} " data-ajax-popup="true">
         <i class="ti ti-pencil text-white"></i>
     </a>
 </div>
 @endpermission
 @permission('Requests delete')
 <div class="action-btn">
     {{ Form::open(['route' => ['requests.destroy', $req->id], 'class' => 'm-0']) }}
     @method('DELETE')
     <a class="mx-3 btn btn-sm bg-danger align-items-center bs-pass-para show_confirm"
         data-bs-toggle="tooltip" title="" data-bs-original-title="Delete"
         aria-label="Delete" data-confirm="{{ __('Are You Sure?') }}"
         data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
         data-confirm-yes="delete-form-{{ $req->id }}"><i
             class="ti ti-trash text-white text-white"></i></a>
     {{ Form::close() }}
 </div>
 @endpermission
