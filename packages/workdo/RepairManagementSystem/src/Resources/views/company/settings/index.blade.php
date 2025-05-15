<div class="card" id="RepairManagementSystem-sidenav">
    {{ Form::open(array('route' => 'repair.setting.store','method' => 'post')) }}
    <div class="card-header p-3">
        <h5 class="">{{ __('Repair Management System Settings') }}</h5>
    </div>
    <div class="card-body p-3 pb-0">
        <div class="row">
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    {{ Form::label('repair_invoice_prefix', __('Repair Invoice Prefix'), ['class' => 'form-label']) }}
                    {{ Form::text('repair_invoice_prefix', isset($settings['repair_invoice_prefix']) ? $settings['repair_invoice_prefix'] : '#INV', ['class' => 'form-control', 'placeholder' => 'Enter Prefix']) }}
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer text-end p-3">
        <input class="btn btn-print-invoice  btn-primary" type="submit" value="{{ __('Save Changes') }}">
    </div>
    {{Form::close()}}
</div>
