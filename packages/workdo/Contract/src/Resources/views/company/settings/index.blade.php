<div class="card" id="contract-sidenav">
    {{ Form::open(array('route' => 'contract.setting.store','method' => 'post')) }}
    <div class="card-header p-3">
        <h5 class="">{{ __('Contract Settings') }}</h5>
    </div>
    <div class="card-body p-3 pb-0">
        <div class="row">
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    {{Form::label('contract_prefix',__('Contract Prefix'),array('class'=>'form-label')) }}
                    {{Form::text('contract_prefix',!empty($settings['contract_prefix']) ? $settings['contract_prefix'] :'#CON',array('class'=>'form-control', 'placeholder' => __('Enter Contract Prefix')))}}
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer text-end p-3">
        <input class="btn btn-print-invoice  btn-primary" type="submit" value="{{ __('Save Changes') }}">
    </div>
    {{Form::close()}}
</div>
