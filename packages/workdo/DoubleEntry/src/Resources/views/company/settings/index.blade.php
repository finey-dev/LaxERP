<div class="card" id="journal-sidenav">
    {{ Form::open(array('route' => 'journal-entry.setting.store','method' => 'post')) }}
    <div class="card-header p-3">
        <h5 class="">{{ __('Journal Settings') }}</h5>
    </div>
    <div class="card-body p-3 pb-0">
        <div class="row">
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    {{Form::label('journal_prefix',__('Journal Prefix'),array('class'=>'form-label')) }}
                    {{Form::text('journal_prefix',!empty($settings['journal_prefix']) ? $settings['journal_prefix'] :'#JUR00000',array('class'=>'form-control', 'placeholder' => 'Enter Journal Prefix'))}}
                </div>
            </div>

        </div>
    </div>
    <div class="card-footer text-end p-3">
        <input class="btn btn-print-invoice btn-primary" type="submit" value="{{ __('Save Changes') }}">
    </div>
    {{Form::close()}}
</div>
