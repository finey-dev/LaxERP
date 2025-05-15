<?php

namespace Workdo\BiometricAttendance\Http\Controllers;

use App\Models\Setting;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Workdo\BiometricAttendance\DataTables\AttendanceDataTable;
use Workdo\Hrm\Entities\Attendance;
use Workdo\Hrm\Entities\Employee;

class BiometricAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if (Auth::user()->isAbleTo('biometricattendance manage')) {

            $company_setting = getCompanyAllSetting();
            $api_urls = !empty($company_setting['zkteco_api_url']) ? $company_setting['zkteco_api_url'] : '';
            $token = !empty($company_setting['auth_token']) ? $company_setting['auth_token'] : '';

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start_date = date('Y-m-d H:i:s', strtotime($request->start_date));
                $end_date = date('Y-m-d H:i:s', strtotime($request->end_date) + 86400 - 1);
            } else {
                $start_date = date('Y-m-d', strtotime('-7 days'));
                $end_date = date('Y-m-d');
            }
            $api_url = rtrim($api_urls, '/');

            // Dynamic Api URL Call
            $url = $api_url . '/iclock/api/transactions/?' . http_build_query([
                'start_time' => $start_date,
                'end_time' => $end_date,
                'page_size' => 10000,
            ]);

            $curl = curl_init();
            if (!empty($token)) {
                try {
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json',
                            'Authorization: Token ' . $token
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);

                    $json_attendance = json_decode($response, true);
                    $attendances = $json_attendance['data'];

                    $Attend = Attendance::where('attendances.workspace', getActiveWorkSpace())
                    ->where('attendances.created_by', creatorId())
                    ->leftJoin('employees', 'attendances.employee_id', '=', 'employees.user_id')
                    ->select('attendances.*', 'employees.biometric_emp_id as biometric_id')
                    ->get();

                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', __('Something went wrong please try again.'));
                }
            } else {
                $Attend = [];
                $attendances = [];
            }

            return view('biometric-attendance::attendance.index', compact('attendances', 'token', 'Attend'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return redirect()->back();
        return view('biometricattendance::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        return redirect()->back();
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Request $request, $id)
    {
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return redirect()->back();
        return view('biometricattendance::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('biometricattendance manage')) {

            $company_setting = getCompanyAllSetting();

            if (empty($company_setting['auth_token'])) {
                return redirect()->back()->with('error', __('Please first create auth token') . ' <a href="' . route('biometric-settings.index') . '"><b>' . __('here.') . '</b></a>');
            }

            $employee = Employee::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('biometric_emp_id', $request->biometric_emp_id)->first();
            if (empty($employee)) {
                return redirect()->back()->with('error', __('Please first create employee or edit employee code.'));
            } else {
                $biometric_code = $employee->biometric_emp_id;
                $employeeId      = Auth::user()->id;

                $startTime  = !empty($company_setting['company_start_time']) ? $company_setting['company_start_time'] : '09:00';
                $endTime  = !empty($company_setting['company_end_time']) ? $company_setting['company_end_time'] : '18:00';

                $date = date("Y-m-d", strtotime($request->punch_time));
                $time = date("H:i", strtotime($request->punch_time));

                $attendance = Attendance::where('attendances.workspace', getActiveWorkSpace())
                    ->where('attendances.created_by', creatorId())
                    ->where('employees.biometric_emp_id', $biometric_code)
                    ->where('clock_out', '=', '00:00:00')
                    ->where('date', '=', $date)
                    ->orderBy('id', 'desc')
                    ->leftJoin('employees', 'attendances.employee_id', '=', 'employees.user_id')
                    ->select('attendances.*', 'employees.biometric_emp_id as biometric_id')
                    ->first();


                if ($attendance != null) {
                    if ($attendance->date == $date && date("H:i", strtotime($attendance->clock_in)) == $time) {
                        return redirect()->back()->with('error', __('This employee is already sync.'));
                    }
                    $endTimestamp = strtotime($date . $endTime);
                    $currentTimestamp = strtotime($date . $time);
                    if ($currentTimestamp > $endTimestamp) {
                        $endTimestamp = strtotime($date . ' +1 day ' . $endTime);
                    }
                    $totalEarlyLeavingSeconds = $endTimestamp - $currentTimestamp;
                    if ($totalEarlyLeavingSeconds < 0) {
                        $earlyLeaving = '0:00:00';
                    } else {
                        $hours                    = floor($totalEarlyLeavingSeconds / 3600);
                        $mins                     = floor($totalEarlyLeavingSeconds / 60 % 60);
                        $secs                     = floor($totalEarlyLeavingSeconds % 60);
                        $earlyLeaving             = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                    }

                    $endTimeTimestamp = strtotime($date . $endTime);
                    $timeTimestamp = strtotime($date . $time);
                    if ($timeTimestamp > $endTimeTimestamp) {
                        //Overtime
                        $totalOvertimeSeconds = $timeTimestamp - $endTimeTimestamp;
                        $hours                = floor($totalOvertimeSeconds / 3600);
                        $mins                 = floor(($totalOvertimeSeconds % 3600) / 60);
                        $secs                 = floor($totalOvertimeSeconds % 60);
                        $overtime             = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                    } else {
                        $overtime = '00:00:00';
                    }

                    $attendance                = Attendance::find($attendance->id);
                    $attendance->clock_out     = $time;
                    $attendance->early_leaving = $earlyLeaving;
                    $attendance->overtime      = $overtime;
                    $attendance->save();
                }

                // Find the last clocked out entry for the employee
                $lastClockOutEntry = Attendance::where('attendances.workspace', getActiveWorkSpace())
                    ->where('attendances.created_by', creatorId())
                    ->where('employees.biometric_emp_id', $biometric_code)
                    ->where('attendances.employee_id', '=', $employee->user_id)
                    ->where('clock_out', '!=', '00:00:00')
                    ->where('date', '=', $date)
                    ->orderBy('id', 'desc')
                    ->leftJoin('employees', 'attendances.employee_id', '=', 'employees.user_id')
                    ->select('attendances.*', 'employees.biometric_emp_id as biometric_id')
                    ->first();

                if (!empty($company_settings['defult_timezone'])) {
                    date_default_timezone_set($company_setting['defult_timezone']);
                }

                if ($lastClockOutEntry != null) {
                    $lastClockOutTime = $lastClockOutEntry->clock_out;
                    $actualClockInTime = $date . ' ' . $time;

                    $totalLateSeconds = strtotime($actualClockInTime) - strtotime($date . ' ' . $lastClockOutTime);

                    $totalLateSeconds = max($totalLateSeconds, 0);

                    $hours = floor($totalLateSeconds / 3600);
                    $mins  = floor($totalLateSeconds / 60 % 60);
                    $secs  = floor($totalLateSeconds % 60);
                    $late  = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                } else {
                    $expectedStartTime = $date . ' ' . $startTime;
                    $actualClockInTime = $date . ' ' . $time;

                    $totalLateSeconds = strtotime($actualClockInTime) - strtotime($expectedStartTime);

                    $totalLateSeconds = max($totalLateSeconds, 0);

                    $hours = floor($totalLateSeconds / 3600);
                    $mins  = floor($totalLateSeconds / 60 % 60);
                    $secs  = floor($totalLateSeconds % 60);
                    $late  = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                }

                $checkDb = Attendance::where('attendances.workspace', getActiveWorkSpace())
                    ->where('attendances.created_by', creatorId())
                    ->where('employees.biometric_emp_id', $biometric_code)
                    ->where('attendances.employee_id', '=', $employee->user_id)
                    ->where('attendances.date', '=', $date)
                    ->leftJoin('employees', 'attendances.employee_id', '=', 'employees.user_id')
                    ->select('attendances.*', 'employees.biometric_emp_id as biometric_id')
                    ->get()
                    ->toArray();


                if (empty($checkDb)) {
                    $employeeAttendance              = new Attendance();
                    $employeeAttendance->employee_id = $employee->user_id;
                    $employeeAttendance->biometric_id  = $request->biometric_id;
                    $employeeAttendance->date          = $date;
                    $employeeAttendance->status        = 'Present';
                    $employeeAttendance->clock_in      = $time;
                    $employeeAttendance->clock_out     = '00:00:00';
                    $employeeAttendance->late          = $late;
                    $employeeAttendance->early_leaving = '00:00:00';
                    $employeeAttendance->overtime      = '00:00:00';
                    $employeeAttendance->total_rest    = '00:00:00';
                    $employeeAttendance->created_by  = creatorId();
                    $employeeAttendance->workspace   = getActiveWorkSpace();
                    $employeeAttendance->save();
                    return redirect()->back()->with('success', __('Employee successfully Sync.'));
                }

                $attendancess = Attendance::where('attendances.workspace', getActiveWorkSpace())
                    ->where('attendances.created_by', creatorId())
                    ->where('employees.biometric_emp_id', $biometric_code)
                    ->where('clock_in', '!=', '00:00:00')
                    ->where('clock_out', '!=', $time)
                    ->orderBy('id', 'desc')
                    ->leftJoin('employees', 'attendances.employee_id', '=', 'employees.user_id')
                    ->select('attendances.*', 'employees.biometric_emp_id as biometric_id')
                    ->first();

                if (empty($attendance)) {
                    $employeeAttendance              = new Attendance();
                    $employeeAttendance->employee_id = $employee->user_id;
                    $employeeAttendance->biometric_id  = $request->id;
                    $employeeAttendance->date          = $date;
                    $employeeAttendance->status        = 'Present';
                    $employeeAttendance->clock_in      = $time;
                    $employeeAttendance->clock_out     = '00:00:00';
                    $employeeAttendance->late          = $late;
                    $employeeAttendance->early_leaving = '00:00:00';
                    $employeeAttendance->overtime      = '00:00:00';
                    $employeeAttendance->total_rest    = '00:00:00';
                    $employeeAttendance->created_by  = creatorId();
                    $employeeAttendance->workspace   = getActiveWorkSpace();
                    $employeeAttendance->save();
                }

                return redirect()->back()->with('success', __('Employee successfully Sync.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        return redirect()->back();
    }

    public function SettingCreate(Request $request)
    {
        if (Auth::user()->isAbleTo('biometricsetting manage')) {

            return view('biometric-attendance::settings.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function SettingStore(Request $request)
    {
        if (Auth::user()->isAbleTo('biometricsetting create')) {

            $request->validate(
                [
                    'zkteco_api_url' => 'required',
                    'username' => 'required',
                    'user_password' => 'required',
                ]
            );
            try {
                $url = "$request->zkteco_api_url" . '/api-token-auth/';
                $headers = array(
                    "Content-Type: application/json"
                );
                $data = array(
                    "username" => $request->username,
                    "password" => $request->user_password
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec($ch);
                curl_close($ch);
                $auth_token = json_decode($response, true);
                if (isset($auth_token['token'])) {
                    $biometric_settings = [];

                    $biometric_settings['zkteco_api_url'] = $request->zkteco_api_url;
                    $biometric_settings['username'] = $request->username;
                    $biometric_settings['user_password'] = $request->user_password;
                    $biometric_settings['auth_token'] = $auth_token['token'];

                    foreach ($biometric_settings as $key => $value) {
                        // Define the data to be updated or inserted
                        $data = [
                            'key' => $key,
                            'workspace' => getActiveWorkSpace(),
                            'created_by' => creatorId(),
                        ];

                        // Check if the record exists, and update or insert accordingly
                        Setting::updateOrInsert($data, ['value' => $value]);
                    }
                } else {
                    return redirect()->back()->with('error', isset($auth_token['non_field_errors']) ? $auth_token['non_field_errors'][0] : __("something went wrong please try again"));
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
            // Settings Cache forget
            AdminSettingCacheForget();
            comapnySettingCacheForget();

            return redirect()->back()->with('success', __('The biometric setting details are updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function AllSync(Request $request)
    {
        if (Auth::user()->isAbleTo('biometricattendance manage')) {

            $company_setting = getCompanyAllSetting();
            $api_urls = !empty($company_setting['zkteco_api_url']) ? $company_setting['zkteco_api_url'] : '';
            $token = !empty($company_setting['auth_token']) ? $company_setting['auth_token'] : '';

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start_date = date('Y-m-d:00:00:00', strtotime($request->start_date));
                $end_date = date('Y-m-d:23:59:59', strtotime($request->end_date) + 86400 - 1);
            } else {
                $start_date = date('Y-m-d', strtotime('-7 days'));
                $end_date = date('Y-m-d');
            }
            $api_url = rtrim($api_urls, '/');
            // Dynamic Api URL Call
            $url = $api_url . '/iclock/api/transactions/?' . http_build_query([
                'start_time' => $start_date,
                'end_time' => $end_date,
                'page_size' => 10000,
            ]);

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Token ' . $token
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $json_attendance = json_decode($response, true);
            $attendances = $json_attendance['data'];

            $idToRemove = Attendance::where('workspace', getActiveWorkSpace())
                ->where('created_by', creatorId())
                ->pluck('biometric_id')
                ->toArray();

            $filteredAttendances = array_filter($attendances, function($attendance) use ($idToRemove) {
                return !in_array($attendance['id'], $idToRemove);
            });

            $filteredAttendances = array_values($filteredAttendances);

            if (empty($company_setting['auth_token'])) {
                return redirect()->back()->with('error', __('Please first create auth token') . ' <a href="' . route('biometric-settings.index') . '"><b>' . __('here.') . '</b></a>');
            }

            $employeeAttendance = [];
            foreach ($filteredAttendances as $bio_attendance) {

                $employees = Employee::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('biometric_emp_id', $bio_attendance['emp_code'])->get();

                if ($employees->isEmpty()) {
                    continue;
                }

                foreach ($employees as $employee) {
                    $biometric_code = $employee->biometric_emp_id;

                    $startTime  = !empty($company_setting['company_start_time']) ? $company_setting['company_start_time'] : '09:00';
                    $endTime  = !empty($company_setting['company_end_time']) ? $company_setting['company_end_time'] : '18:00';

                    $date = date("Y-m-d", strtotime($bio_attendance['punch_time']));
                    $time = date("H:i", strtotime($bio_attendance['punch_time']));

                    $todayAttendance = Attendance::where('attendances.workspace', getActiveWorkSpace())
                        ->where('attendances.created_by', creatorId())
                        ->where('employees.biometric_emp_id', $biometric_code)
                        ->where('clock_in', '=', date("H:i:s", strtotime($time)))
                        ->where('date', '=', $date)
                        ->leftJoin('employees', 'attendances.employee_id', '=', 'employees.user_id')
                        ->select('attendances.*', 'employees.biometric_emp_id as biometric_id')
                        ->first();

                        if (!empty($todayAttendance)) {
                            continue;
                        }

                    $lastClockOutEntry = Attendance::where('attendances.workspace', getActiveWorkSpace())
                        ->where('attendances.created_by', creatorId())
                        ->where('employees.biometric_emp_id', $biometric_code)
                        ->where('attendances.employee_id', '=', $employee->user_id)
                        ->where('clock_out', '!=', '00:00:00')
                        ->where('date', '=', date('Y-m-d'))
                        ->orderBy('id', 'desc')
                        ->leftJoin('employees', 'attendances.employee_id', '=', 'employees.user_id')
                        ->select('attendances.*', 'employees.biometric_emp_id as biometric_id')
                        ->first();

                    if (!empty($company_settings['defult_timezone'])) {
                        date_default_timezone_set($company_setting['defult_timezone']);
                    }

                    if ($lastClockOutEntry != null) {
                        $lastClockOutTime = $lastClockOutEntry->clock_out;
                        $actualClockInTime = $date . ' ' . $time;

                        $totalLateSeconds = strtotime($actualClockInTime) - strtotime($date . ' ' . $lastClockOutTime);

                        $totalLateSeconds = max($totalLateSeconds, 0);

                        $hours = floor($totalLateSeconds / 3600);
                        $mins  = floor($totalLateSeconds / 60 % 60);
                        $secs  = floor($totalLateSeconds % 60);
                        $late  = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                    } else {
                        $expectedStartTime = $date . ' ' . $startTime;
                        $actualClockInTime = $date . ' ' . $time;

                        $totalLateSeconds = strtotime($actualClockInTime) - strtotime($expectedStartTime);

                        $totalLateSeconds = max($totalLateSeconds, 0);

                        $hours = floor($totalLateSeconds / 3600);
                        $mins  = floor($totalLateSeconds / 60 % 60);
                        $secs  = floor($totalLateSeconds % 60);
                        $late  = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                    }

                    $checkDb = Attendance::where('attendances.workspace', getActiveWorkSpace())
                        ->where('attendances.created_by', creatorId())
                        ->where('employees.biometric_emp_id', $biometric_code)
                        ->where('attendances.employee_id', '=', $employee->user_id)
                        ->where('attendances.date', '=', $date)
                        ->leftJoin('employees', 'attendances.employee_id', '=', 'employees.user_id')
                        ->select('attendances.*', 'employees.biometric_emp_id as biometric_id')
                        ->get()
                        ->toArray();

                    $employeeAttendance                = new Attendance();
                    $employeeAttendance->employee_id   = $employee->user_id;
                    $employeeAttendance->biometric_id  = $bio_attendance['id'];
                    $employeeAttendance->date          = $date;
                    $employeeAttendance->status        = 'Present';
                    $employeeAttendance->clock_in      = $time;
                    $employeeAttendance->clock_out     = '00:00:00';
                    $employeeAttendance->late          = $late;
                    $employeeAttendance->early_leaving = '00:00:00';
                    $employeeAttendance->overtime      = '00:00:00';
                    $employeeAttendance->total_rest    = '00:00:00';
                    $employeeAttendance->created_by    = creatorId();
                    $employeeAttendance->workspace     = getActiveWorkSpace();
                    $employeeAttendance->save();
                }
            }
            return Response::json([
                'url' => route('biometric-attendance.allsync'),
                'data' => $employeeAttendance,
                'message' => 'Employee successfully Sync.'
            ]);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
