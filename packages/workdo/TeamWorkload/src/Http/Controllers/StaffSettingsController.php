<?php

namespace Workdo\TeamWorkload\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\TeamWorkload\Entities\GeneralSetting;
use Workdo\TeamWorkload\Entities\Holiday;
use Workdo\TeamWorkload\Entities\WorkloadStaffSetting;

class StaffSettingsController extends Controller
{

    public function index()
    {
        // $staffs = User::where('workspace_id', getActiveWorkSpace())->where('created_by', '=', creatorId())->emp()->get();
        $staffs = WorkloadStaffSetting::where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->get();
        $week_days = WorkloadStaffSetting::$week_days;
        return view('team-workload::staff-setting.index', compact('staffs', 'week_days'));
    }

    public function create()
    {

        $staffs = User::where('workspace_id', getActiveWorkSpace())->where('created_by', '=', creatorId())->emp()->get()->pluck('name', 'id');

        $week_days = WorkloadStaffSetting::$week_days;
        return view('team-workload::staff-setting.create', compact('staffs', 'week_days'));
    }



    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'user' => 'required',
                'working_hours' => 'required',
                'working_days' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $staff = $request->user;
        $workingHours = $request->working_hours;
        $workingDays = $request->working_days;
        $holidays = $request->enable_holiday;

        $staffWorkload = [];
        $totalHours = 0;

        foreach ($workingDays as $day) {
            $dayWorkload = [
                'working_days' => $day,
                'working_hours' => $workingHours,
            ];
            $staffWorkload[] = $dayWorkload;
            $totalHours += $workingHours;
        }


        WorkloadStaffSetting::updateOrcreate(
            ['user_id' => $staff,'workspace_id' => getActiveWorkSpace()],
            [
                'user_id' => $staff,
                'working' => json_encode($staffWorkload),
                'total_hours' => $totalHours,
                'enable_holiday' => $request->enable_holiday,
                'workspace_id' => getActiveWorkSpace(),
                'created_by' => creatorId(),
            ]
        );

        return redirect()->route('staff-setting.index')->with('success', __('Staff Setting Saved Successfully'));
    }


    public function workloadstore(Request $request)
    {

        $users = $request->user;
        $workingHours = $request->working_hours;

        foreach ($users as $user) {
            $staffWorkload = [];
            $totalHours = 0;

            foreach ($workingHours[$user] as $day => $hours) {
                if ($hours !== null && $hours !== '') {
                    $staffWorkload[] = [
                        'working_days' => $day,
                        'working_hours' => $hours,
                    ];
                    $totalHours += $hours;
                }
            }
            $user_id = $user;
            $workspace_id = getActiveWorkSpace();
            $created_by = creatorId();

            WorkloadStaffSetting::updateOrInsert(
                ['user_id' => $user_id, 'workspace_id' => $workspace_id],
                [
                    'working' => json_encode($staffWorkload),
                    'total_hours' => $totalHours,
                    'created_by' => $created_by,
                ]
            );
        }
        return redirect()->route('staff-setting.index')->with('success', __('Staff Setting Saved Successfully'));
    }


}
