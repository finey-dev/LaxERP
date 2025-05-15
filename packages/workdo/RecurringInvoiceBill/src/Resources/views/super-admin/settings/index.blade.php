<div class="card" id="sidenav-recurringinvoicebill">
    {{ Form::open(['route' => 'recurring.setting.store', 'method' => 'post']) }}
    <div class="card-header p-3">
            <div class="row align-items-center">
                <div class="col-sm-10 col-9">
                    <h5>{{ __('Recurring Invoice & Bill Settings') }}</h5>
                </div>
                <div class="col-sm-2 col-3">
                    <div class="form-check form-switch  float-end">
                        <input type="checkbox" name="recurring_invoice_bill"
                            class="form-check-input input-primary pointer" value="on" id="recurring_invoice_bill"
                            {{ isset($settings['recurring_invoice_bill']) && $settings['recurring_invoice_bill'] == 'on' ? ' checked ' : '' }}>
                        <label class="form-check-label" for="recurring_invoice_bill"></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-3 pb-0">
                <h6 for="recurring_invoice_bill">{{ __('Recurring Invoice & Bill Settings') }}</h6>
                <p>{{__('Note: With the recurring invoices button enabled in settings, easily customize the duplication frequency using the custom button. Choose the desired interval for invoice duplication or set it to infinity for seamless management of recurring billing cycles.')}}</p>
                <h6>{{__('Recurring Invoice & Bill Cronjob Instruction')}}</h6>
                <p>{{__('1. If you would like to create automatically Recurring Invoice and Bill you need set a cron job for that which one run like every day.')}}</p>
                <p>{{__('0 0 * * *domain && php artisan recurring:invoice-bill >/dev/null 2>&1')}}</p>
                <p>{{__('2. Example url as  ' . '/usr/local/bin/ea-php82 /home/proje99/public_html/dash-demo.workdo.io/artisan recurring:invoice-bill')}}</p>
        </div>
        <div class="card-footer p-3">
            <div class="text-end">
                <input class="btn btn-print-invoice  btn-primary" type="submit" value="{{ __('Save Changes') }}">
            </div>
        </div>
    {{ Form::close() }}
</div>
