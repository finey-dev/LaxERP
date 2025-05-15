<?php

namespace Workdo\TeamWorkload\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Hrm\Entities\Department;
use Workdo\Hrm\Entities\Employee;
use Workdo\TeamWorkload\Entities\WorkloadTimesheet;
use Workdo\Timesheet\Entities\Timesheet;

class TeamWorkloadController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if (\Auth::user()->isAbleTo('workload overview manage')) {

            if (module_is_active('Hrm')) {
                $department = Department::where('created_by', creatorId())
                    ->where('workspace', getActiveWorkSpace())
                    ->get()
                    ->pluck('name', 'id');
                $department->prepend('All', '');
            } else {
                $department = [];
            }
                $emp = User::where('workspace_id', getActiveWorkSpace())
                    ->where('created_by', creatorId())
                    ->emp()
                    ->get()
                    ->pluck('name', 'id');
                $emp->prepend('Select Employee', '');
          
            $staff = \DB::table('workload_staff_settings')
                ->where('workload_staff_settings.workspace_id', getActiveWorkSpace())
                ->where('workload_staff_settings.created_by', creatorId())
                ->join('users', 'users.id', '=', 'workload_staff_settings.user_id')
                ->select('users.name', 'users.type', 'workload_staff_settings.total_hours', 'workload_staff_settings.user_id');

            if (!empty($request->staff)) {
                $staff->where('user_id', $request->staff);
            }

            if (!empty($request->department)) {
                $employees = Employee::where('department_id', $request->department)
                    ->get();
                $userIds = $employees->pluck('user_id')->all();
                $staff->whereIn('user_id', $userIds);
            }

            $datesInCurrentWeek = [];
            $userIds = [];

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start_date = date('Y-m-d', strtotime($request->start_date));
                $end_date = date('Y-m-d', strtotime($request->end_date));

                $current_date = $start_date;
                while ($current_date <= $end_date) {

                    if (module_is_active('Timesheet')) {
                        $timesheets = Timesheet::where('date', $current_date)
                            ->get();
                    } else {
                        $timesheets = WorkloadTimesheet::where('date', $current_date)
                            ->get();
                    }
                    $userIds = array_merge($userIds, $timesheets->pluck('user_id')->all());

                    $datesInCurrentWeek[] = [
                        'date' => $current_date,
                        'day' => date('l', strtotime($current_date)),
                    ];

                    $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
                }
            } else {
                $currentDate = date('Y-m-d');
                $startDate = date('Y-m-d', strtotime('last Sunday', strtotime($currentDate)));
                $endDate = date('Y-m-d', strtotime('next Saturday', strtotime($currentDate)));

                $currentDate = $startDate;

                while (strtotime($currentDate) <= strtotime($endDate)) {
                    $datesInCurrentWeek[] = [
                        'date' => $currentDate,
                        'day' => date('l', strtotime($currentDate)),
                    ];
                    $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
                }
            }
            if (!empty($userIds)) {
                $staff->whereIn('user_id', $userIds);
            }

            $staffs = $staff->get();

            $labels = [];
            $total_hours = [];

            foreach ($staffs as $staff) {
                $labels[] = $staff->name;
                $total_hours[] = $staff->total_hours;
            }

            return view('team-workload::workload.index', compact('department', 'staffs', 'emp', 'datesInCurrentWeek', 'labels', 'total_hours'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

}
