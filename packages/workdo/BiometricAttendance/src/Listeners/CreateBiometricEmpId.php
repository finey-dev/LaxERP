<?php

namespace Workdo\BiometricAttendance\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Hrm\Entities\Employee;

class CreateBiometricEmpId
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $biometric_emp_id = $event->request;
        $employee = Employee::find($event->employee->id);
        if (module_is_active('BiometricAttendance')) {
            if (!empty($biometric_emp_id->biometric_emp_id) && !empty($employee)) {
                $employee->biometric_emp_id = $biometric_emp_id->biometric_emp_id;
                $employee->save();
            }
        }
    }
}
