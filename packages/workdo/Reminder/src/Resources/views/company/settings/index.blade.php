<div class="card" id="sidenav-reminder">
    {{ Form::open(['route' => 'reminder.setting.store', 'method' => 'post']) }}
<div class="card-header p-3">
        <div class="row align-items-center">
            <div class="col-sm-10 col-9">
                <h5 class="">{{ __('Reminder Settings') }}</h5>
            </div>
        </div>
    </div>
    <div class="row card-body p-3 mt-3  mb-3">

            <div class="col-md-8">
                <span>{{__('To use the Reminder notifications functionality, you need to inform the Super Admin.
                    ')}}</span><br>
                    <span>
                        {{__('They will configure and set up the required cron job.')}}
                    </span><br>
                    <span>
                    {{__('This setup is essential to activate the module.')}}
                    </span>
                    <div class="mt-2">
                        <p>{{__('Note: Enable the reminder button to receive timely notifications about your work through your selected sources')}}</p>
                    </div>
            </div>
            <div class="col-md-4 ">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="form-group col mb-0">
                            <div class=" form-switch p-0">
                                <label for="ip_restrict" class=" form-label mb-0">{{ __('Reminder Enable') }}</label>
                                <div class=" float-end">
                                    <input type="checkbox" name="reminder_notification_is"
                                    class="form-check-input input-primary pointer" value="on" id="reminder_notification_is"
                                    {{ isset($settings['reminder_notification_is']) && $settings['reminder_notification_is'] == 'on' ? ' checked ' : '' }}>
                                <label class="form-check-label" for="reminder_notification_is"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

         </div>

    <div class="card-footer p-3">
        <div class="text-end">
            <input class="btn btn-print-invoice  btn-primary" type="submit" value="{{ __('Save Changes') }}">
        </div>

    </div>
    {{ Form::close() }}

</div>
