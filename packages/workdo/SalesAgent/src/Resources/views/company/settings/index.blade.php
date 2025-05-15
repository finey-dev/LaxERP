@permission('salesagent manage')
<div class="card" id="salesagent-sidenav">
    {{ Form::open(array('route' => 'salesagents.setting.save','method' => 'post')) }}
    <div class="card-header p-3">
        <h5 class="">{{ __('Sales Agent Settings') }}</h5>
    </div>
    <div class="card-body p-3 pb-0">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    {{Form::label('salesagent_prefix',__('Sales Agent Prefix'),array('class'=>'form-label')) }}
                    {{Form::text('salesagent_prefix',!empty(company_setting('salesagent_prefix')) ? company_setting('salesagent_prefix') :'#AGENT',array('class'=>'form-control', 'placeholder' => 'Enter Customer Prefix'))}}
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer text-end p-3">
        <input class="btn btn-print-invoice  btn-primary" type="submit" value="{{ __('Save Changes') }}">
    </div>
    {{Form::close()}}
</div>
@endpermission
<script>
     $(document).on("change", "select[name='bill_template'], input[name='bill_color']", function ()
     {
            var template = $("select[name='bill_template']").val();
            var color = $("input[name='bill_color']:checked").val();
            $('#bill_frame').attr('src', '{{url('/bill/preview')}}/' + template + '/' + color);
        });
</script>
