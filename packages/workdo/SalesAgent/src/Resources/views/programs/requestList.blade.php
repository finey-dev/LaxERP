@extends('layouts.main')

@section('page-title')
    {{__('Sales Agent')}}
@endsection

@section('page-breadcrumb')
{{ __('Program')}} , {{ __('Join requests')}}
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple" id="assets">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Contact') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    @if (Laratrust::hasPermission('salesagent edit') || Laratrust::hasPermission('salesagent delete') || Laratrust::hasPermission('salesagent show'))
                                        <th width="10%"> {{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($salesagents as $k => $Agent)
                                    <tr class="font-style">
                                        @if (!empty($Agent['customer_id']))
                                            <td class="">
                                                @permission('salesagent show')
                                                    <a href="{{ route('salesagents.show', \Crypt::encrypt($Agent['id'])) }}"
                                                        class="btn btn-outline-primary">
                                                        {{ Workdo\SalesAgent\Entities\SalesAgent::salesagentNumberFormat($Agent['customer_id']) }}
                                                    </a>
                                                @else
                                                    <a  class="btn btn-outline-primary">
                                                        {{ \Workdo\SalesAgent\Entities\SalesAgent::salesagentNumberFormat($Agent['customer_id']) }}
                                                    </a>
                                                @endpermission
                                            </td>
                                        @else
                                            <td>--</td>
                                        @endif

                                        <td>{{ $Agent['name'] }}</td>
                                        <td>{{ $Agent['contact'] }}</td>
                                        <td>{{ $Agent['email'] }}</td>
                                        @if (Laratrust::hasPermission('salesagent edit') || Laratrust::hasPermission('salesagent delete') || Laratrust::hasPermission('salesagent show'))
                                            <td class="Action">
                                                @if($Agent['is_disable'] == 1)
                                                    <span>
                                                        @permission('salesagent edit')
                                                            <div class="action-btn me-2">
                                                                <a  class="bg-primary btn btn-sm  align-items-center"
                                                                    href="{{ route('salesagent.program.accept.request', [ $program->id ,$Agent['id']]) }}"
                                                                    data-ajax-popup="" data-size="lg" data-bs-toggle="tooltip"
                                                                    title="" data-title="{{ __('Edit Sales Agent') }}"
                                                                    data-bs-original-title="{{ __('Accept') }}">
                                                                    <i class="ti ti-check text-white"></i>
                                                                </a>
                                                            </div>
                                                        @endpermission
                                                        @if (!empty($Agent['customer_id']))
                                                            @permission('salesagent delete')
                                                                <div class="action-btn">
                                                                    {{ Form::open(['route' => ['salesagent.program.reject.request', [ $program->id ,$Agent['id']]], 'class' => 'm-0']) }}
                                                                    @method('DELETE')
                                                                    <a
                                                                        class="bg-danger btn btn-sm  align-items-center bs-pass-para show_confirm"
                                                                        data-bs-toggle="tooltip" title=""
                                                                        data-bs-original-title="Reject" aria-label="Delete"
                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                        data-confirm-yes="delete-form-{{ $Agent['id'] }}"><i
                                                                            class="ti ti-trash text-white text-white"></i></a>
                                                                    {{ Form::close() }}
                                                                </div>
                                                            @endpermission
                                                        @endif
                                                    </span>
                                                @else
                                                    <div class="text-center">
                                                        <i class="ti ti-lock"></i>
                                                    </div>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

<script>
    $(document).on('click', '#billing_data', function() {
        $("[name='shipping_name']").val($("[name='billing_name']").val());
        $("[name='shipping_country']").val($("[name='billing_country']").val());
        $("[name='shipping_state']").val($("[name='billing_state']").val());
        $("[name='shipping_city']").val($("[name='billing_city']").val());
        $("[name='shipping_phone']").val($("[name='billing_phone']").val());
        $("[name='shipping_zip']").val($("[name='billing_zip']").val());
        $("[name='shipping_address']").val($("[name='billing_address']").val());
    })
</script>

<script>
    $(document).on('change','#is_agent_active',function(){
        var val = $(this).prop("checked");
        if(val == true){
                var is_agent_active = 1;
        }
        else{
            var is_agent_active = 0;
        }

        var sales_agent_id = $(this).val();

        console.log(sales_agent_id);
        $.ajax({
                type:'POST',
                url: "{{route('activeSalesAgent')}}",
                datType: 'json',
                data:{
                    "_token": "{{ csrf_token() }}",
                    "is_agent_active":is_agent_active,
                    "sales_agent_id":sales_agent_id,
                },
                success : function(data){
                    toastrs('Success',data.message, 'success')
                    // setTimeout(function() {
                    //     location.reload(true);
                    // }, 1500);
                }
        });
    });

</script>

@endpush
