<div class="card" id="sidenav-reminder">
    {{ Form::open(['route' => 'reminder.setting.store', 'method' => 'post']) }}
<div class="card-header p-3">
        <div class="row align-items-center">
            <div class="col-sm-10 col-9">
                <h5 class="">{{ __('Reminder Settings') }}</h5>
            </div>
            <div class="col-sm-2 col-3">
                <div class="form-check form-switch  float-end">
                    <input type="checkbox" name="reminder_notification_is"
                        class="form-check-input input-primary pointer" value="on" id="reminder_notification_is"
                        {{ isset($settings['reminder_notification_is']) && $settings['reminder_notification_is'] == 'on' ? ' checked ' : '' }}>
                    <label class="form-check-label" for="reminder_notification_is"></label>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-3 pb-0">
            <h6 class="mb-3" for="reminder_notification_is ">{{ __('Reminder Settings') }}</h6>
            <p>{{__('Enable the reminder button to receive timely notifications about your work through your selected sources')}}</p>
            <div class="font-bold mb-3">{{__('Reminder Cronjob Instruction')}}</div>
            <p>{{__('1. If you would like to send reminder notification you need set a cron job for that which one run like every day.')}}</p>
            <p>{{__('0 0 * * *domain && php artisan reminder:notification >/dev/null 2>&1')}}</p>
            <p>{{__('2. Example url as  ' . '/usr/local/bin/ea-php82 /home/proje99/public_html/dash-demo.workdo.io/artisan reminder:notification')}}</p>

    </div>

    <div class="card-footer p-3">
        <div class="text-end">
            <input class="btn btn-print-invoice  btn-primary" type="submit" value="{{ __('Save Changes') }}">
        </div>

    </div>
    {{ Form::close() }}

</div>
