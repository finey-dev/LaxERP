<?php

namespace Workdo\ApiDocsGenerator\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\Hrm\Entities\Transfer;
use Workdo\Hrm\Entities\Allowance;
use Workdo\Hrm\Entities\Attendance;
use Workdo\Hrm\Entities\Award;
use Workdo\Hrm\Entities\Branch;
use Workdo\Hrm\Entities\Commission;
use Workdo\Hrm\Entities\CompanyPolicy;
use Workdo\Hrm\Entities\Department;
use Workdo\Hrm\Entities\Designation;
use Workdo\Hrm\Entities\Document;
use Workdo\Hrm\Entities\Employee;
use Workdo\Hrm\Entities\Event;
use Workdo\Hrm\Entities\Holiday;
use Workdo\Hrm\Entities\Leave;
use Workdo\Hrm\Entities\Loan;
use Workdo\Hrm\Entities\OtherPayment;
use Workdo\Hrm\Entities\Overtime;
use Workdo\Hrm\Entities\Resignation;
use Workdo\Hrm\Entities\SaturationDeduction;

class HrmApiController extends Controller
{
    public function employees(Request $request){
        if (!module_is_active('Hrm')) {
            return response()->json(['status'=>'error','message'=>'Hrm Module Not Active!']);
        }
        if (!in_array(Auth::user()->type, Auth::user()->not_emp_type)) {
            $employees = User::where('workspace_id', $request->workspace_id)
                ->leftJoin('employees', 'users.id', '=', 'employees.user_id')
                ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id')
                ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
                ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
                ->where('users.id', Auth::user()->id)
                ->select('users.*', 'users.id as ID', 'employees.*', 'users.name as name', 'users.email as email', 'users.id as id', 'branches.name as branches_name', 'departments.name as departments_name', 'designations.name as designations_name')
                ->get();
        }
        else
        {
            $employees = User::where('workspace_id', $request->workspace_id)
                ->leftJoin('employees', 'users.id', '=', 'employees.user_id')
                ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id')
                ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
                ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
                ->where('users.created_by', creatorId())->emp()
                ->select('users.*', 'users.id as ID', 'employees.*', 'users.name as name', 'users.email as email', 'users.id as id', 'branches.name as branches_name', 'departments.name as departments_name', 'designations.name as designations_name')
                ->get();

        }


        $employees = $employees->map(function($employee){
            return [
                'id'                    => $employee->id,
                'name'                  => $employee->name,
                'email'                 => $employee->email,
                'mobile_no'             => $employee->mobile_no,
                'avatar'                => get_file($employee->avatar),
                'dob'                   => $employee->dob,
                'gender'                => $employee->gender,
                'phone'                 => $employee->phone,
                'address'               => $employee->address,
                'branches_name'         => $employee->branches_name,
                'departments_name'      => $employee->departments_name,
                'designations_name'     => $employee->designations_name,
                'account_holder_name'   => $employee->account_holder_name,
                'account_number'        => $employee->account_number,
                'bank_name'             => $employee->bank_name,
                'bank_identifier_code'  => $employee->bank_identifier_code,
                'branch_location'       => $employee->branch_location,
                'salary_type'           => $employee->salary_type,
                'salary'                => $employee->salary,

            ];
        });

        return response()->json(['status'=>'success','data'=>$employees]);
    }

    public function employeeDetail(Request $request,$id){
        if (!module_is_active('Hrm')) {
            return response()->json(['status'=>'error','message'=>'Hrm Module Not Active!']);
        }

        $employee     = Employee::where('user_id', $id)->where('workspace', $request->workspace_id)->where('created_by', creatorId())->first();
        if(!$employee){
            return response()->json(['status'=>'error','message'=>'Employee Not Found!']);
        }

        $data = [
            'name'                  => $employee->name,
            'email'                 => $employee->email,
            'mobile_no'             => $employee->mobile_no,
            'avatar'                => get_file($employee->user->avatar),
            'dob'                   => $employee->dob,
            'gender'                => $employee->gender,
            'phone'                 => $employee->phone,
            'address'               => $employee->address,
            'branches_name'         => $employee->branches_name,
            'departments_name'      => $employee->departments_name,
            'designations_name'     => $employee->designations_name,
            'account_holder_name'   => $employee->account_holder_name,
            'account_number'        => $employee->account_number,
            'bank_name'             => $employee->bank_name,
            'bank_identifier_code'  => $employee->bank_identifier_code,
            'branch_location'       => $employee->branch_location,
            'salary_type'           => $employee->salary_type,
            'salary'                => $employee->salary,

        ];

        return response()->json(['status'=>'success','data' => $data]);

    }

    public function salary(Request $request){
        if (!module_is_active('Hrm')) {
            return response()->json(['status'=>'error','message'=>'Hrm Module Not Active!']);
        }
        $employees = Employee::where('workspace', $request->workspace_id)->where('created_by', creatorId())->with('salaryType')->get()
                ->map(function($employee){
                    return [
                        'id'                     => $employee->id,
                        'name'                   => $employee->name,
                        'dob'                    => $employee->dob,
                        'gender'                 => $employee->gender,
                        'phone'                  => $employee->phone,
                        'address'                => $employee->address,
                        'email'                  => $employee->email,
                        'company_doj'            => $employee->company_doj,
                        'account_holder_name'    => $employee->account_holder_name,
                        'account_number'         => $employee->account_number,
                        'bank_name'              => $employee->bank_name,
                        'bank_identifier_code'   => $employee->bank_identifier_code,
                        'branch_location'        => $employee->branch_location,
                        'tax_payer_id'           => $employee->tax_payer_id,
                        'salary_type'            => $employee->salary_type,
                        'salary'                 => currency_format_with_sym($employee->salary),
                    ];
                });
        return response()->json(['status'=>'success','data'=>$employees]);
    }

    public function salaryDetail(Request $request,$id){
        if (!module_is_active('Hrm')) {
            return response()->json(['status'=>'error','message'=>'Hrm Module Not Active!']);
        }

        if (!in_array(Auth::user()->type, Auth::user()->not_emp_type)) {

            $employee             = Employee::where('user_id', '=', \Auth::user()->id)->where('workspace', $request->workspace_id)->first();
            $currentEmployee      = Employee::where('user_id', '=', \Auth::user()->id)->where('workspace', $request->workspace_id)->first();
            $allowances           = Allowance::where('employee_id', $currentEmployee->id)->where('workspace', $request->workspace_id)->get();
            $commissions          = Commission::where('employee_id', $currentEmployee->id)->where('workspace', $request->workspace_id)->get();
            $loans                = Loan::where('employee_id', $currentEmployee->id)->where('workspace', $request->workspace_id)->get();
            $saturationdeductions = SaturationDeduction::where('employee_id', $currentEmployee->id)->where('workspace', $request->workspace_id)->get();
            $otherpayments        = OtherPayment::where('employee_id', $currentEmployee->id)->where('workspace', $request->workspace_id)->get();
            $overtimes            = Overtime::where('employee_id', $currentEmployee->id)->where('workspace', $request->workspace_id)->get();

        }
        else{

            $employee             = Employee::where('workspace', $request->workspace_id)->where('created_by', creatorId())->where('id',$id)->first();
            $allowances           = Allowance::where('employee_id', $id)->where('workspace', $request->workspace_id)->where('created_by', creatorId())->get();
            $commissions          = Commission::where('employee_id', $id)->where('workspace', $request->workspace_id)->where('created_by', creatorId())->get();
            $loans                = Loan::where('employee_id', $id)->where('workspace', $request->workspace_id)->where('created_by', creatorId())->get();
            $saturationdeductions = SaturationDeduction::where('employee_id', $id)->where('workspace', $request->workspace_id)->where('created_by', creatorId())->get();
            $otherpayments        = OtherPayment::where('employee_id', $id)->where('workspace', $request->workspace_id)->where('created_by', creatorId())->get();
            $overtimes            = Overtime::where('employee_id', $id)->where('workspace', $request->workspace_id)->where('created_by', creatorId())->get();

        }
        if(!$employee){
            return response()->json(['status'=>'error','message'=>'Salary Not Found!']);
        }

        $data = [
            'id'                            => $employee->id,
            'name'                          => $employee->name,
            'dob'                           => $employee->dob,
            'gender'                        => $employee->gender,
            'phone'                         => $employee->phone,
            'address'                       => $employee->address,
            'email'                         => $employee->email,
            'company_doj'                   => $employee->company_doj,
            'account_holder_name'           => $employee->account_holder_name,
            'account_number'                => $employee->account_number,
            'bank_name'                     => $employee->bank_name,
            'bank_identifier_code'          => $employee->bank_identifier_code,
            'branch_location'               => $employee->branch_location,
            'tax_payer_id'                  => $employee->tax_payer_id,
            'salary'                        => currency_format_with_sym($employee->salary),
            'salary_type'                   => !empty($employee->salary_type()) ?  $employee->salary_type() : '',
            'allowances'                    => $allowances->map(function($allowance){
                return [
                    'id'            => $allowance->id,
                    'title'         => $allowance->title,
                    'type'          => $allowance->type,
                    'amount'        => currency_format_with_sym($allowance->amount),
                ];
            }),
            'commissions'                   => $commissions->map(function($commission){
                return [
                    'id'       => $commission->id,
                    'title'    => $commission->title,
                    'type'     => $commission->type,
                    'amount'   => currency_format_with_sym($commission->amount),
                ];
            }),
            'loans'                         => $loans->map(function($loan){
                return [
                    'id'            => $loan->id,
                    'title'         => $loan->title,
                    'loan_option'   => !empty($loan->loan_option) ? $loan->loanoption->name : '',
                    'type'          => $loan->type,
                    'amount'        => currency_format_with_sym($loan->amount),
                    'start_date'    => $loan->start_date,
                    'end_date'      => $loan->end_date,
                    'reason'        => $loan->reason,
                ];
            }),
            'saturationdeductions'          => $saturationdeductions->map(function($saturationdeduction){
                return [
                    'id'                  => $saturationdeduction->id,
                    'title'               => $saturationdeduction->title,
                    'type'                => $saturationdeduction->type,
                    'amount'              => currency_format_with_sym($saturationdeduction->amount),
                    'deduction_option'    => !empty($saturationdeduction->deduction_option) ? $saturationdeduction->deductionoption->name : ''
                ];
            }),
            'otherpayments'                 => $otherpayments->map(function($otherpayment){
                return [
                    'id'        => $otherpayment->id,
                    'title'     => $otherpayment->title,
                    'type'      => $otherpayment->type,
                    'amount'    => currency_format_with_sym($otherpayment->amount),
                ];
            }),
            'overtimes'                     => $overtimes->map(function($overtime){
                return [
                    'id'                => $overtime->id,
                    'title'             => $overtime->title,
                    'type'              => $overtime->type,
                    'number_of_days'    => $overtime->number_of_days,
                    'hours'             => $overtime->hours,
                    'rate'              => $overtime->rate,
                ];
            })
        ];

        return response()->json(['status'=>'success','data'=>$data]);

    }

    public function attendanceList(Request $request){
        if (!module_is_active('Hrm')) {
            return response()->json(['status'=>'error','message'=>'Hrm Module Not Active!']);
        }

        if (!in_array(Auth::user()->type, Auth::user()->not_emp_type)) {
            $attendances = Attendance::where('employee_id', Auth::user()->id)->where('workspace', $request->workspace_id)->with('employees')->get();
        }
        else{
            $employee = User::where('workspace_id', getActiveWorkSpace())
                ->leftjoin('employees', 'users.id', '=', 'employees.user_id')
                ->where('users.created_by', creatorId())->emp()
                ->select('users.id')
                ->get()->pluck('id');

            $attendances = Attendance::whereIn('employee_id', $employee)->where('workspace', $request->workspace_id)->with('employees')->get();
        }

        $data = $attendances->map(function($attendance){
            return [
                'id'                    => $attendance->id,
                'date'                  => $attendance->date,
                'status'                => $attendance->status,
                'clock_in'              => $attendance->clock_in,
                'clock_out'             => $attendance->clock_out,
                'late'                  => $attendance->late,
                'early_leaving'         => $attendance->early_leaving,
                'overtime'              => $attendance->overtime,
                'total_rest'            => $attendance->total_rest,
                'employees'             => !empty($attendance->employees) ? [
                    'name'    => $attendance->employees->name,
                    'email'    => $attendance->employees->email,
                ] : [],
            ];
        });

        return response()->json(['status'=>'success','data'=>$data]);
    }

    public function leavesList(Request $request){
        if (!module_is_active('Hrm')) {
            return response()->json(['status'=>'error','message'=>'Hrm Module Not Active!']);
        }

        if (!in_array(Auth::user()->type, Auth::user()->not_emp_type)) {

            $leaves   = Leave::where('user_id', '=', Auth::user()->id)->where('workspace', $request->workspace_id)->orderBy('id', 'desc')->get();

        } else {

            $leaves = Leave::where('leaves.workspace', $request->workspace_id)->with('leaveType')
                ->leftJoin('employees', 'employees.user_id', '=', 'leaves.user_id')
                ->leftJoin('users', 'users.id', '=', 'leaves.user_id')
                ->where('leaves.created_by', creatorId())
                ->select('leaves.*', 'leaves.id as ID', 'employees.*', 'users.*', 'leaves.user_id as user_name', 'leaves.applied_on as applied_on', 'leaves.start_date as start_date', 'leaves.end_date as end_date', 'leaves.id as id')
                ->get();

        }

        $data = $leaves->map(function($leave){
            return [
                'applied_on'        => $leave->applied_on,
                'start_date'        => $leave->start_date,
                'end_date'          => $leave->end_date,
                'total_leave_days'  => $leave->total_leave_days,
                'leave_reason'      => $leave->leave_reason,
                'remark'            => $leave->remark,
                'status'            => $leave->status,
                'employee_name'     => $leave->name,
                'employee_dob'      => $leave->dob,
                'leave_type'        => !empty($leave->leaveType) ? [
                    'title' => $leave->leaveType->title,
                    'days' => $leave->leaveType->days,
                ] : []

            ];
        });

        return response()->json(['status'=>'success','data'=>$data]);

    }

    public function awardsList(Request $request){
        if (!module_is_active('Hrm')) {
            return response()->json(['status'=>'error','message'=>'Hrm Module Not Active!']);
        }

        if (!in_array(Auth::user()->type, Auth::user()->not_emp_type)) {
            $awards     = Award::where('user_id', Auth::user()->id)->where('workspace', $request->workspace_id)->get();
        } else {
            $awards     = Award::where('created_by', creatorId())->where('workspace', $request->workspace_id)->with(['users', 'awardType'])->get();
        }

        $data = $awards->map(function($award){
            return [
                'date'        => $award->date,
                'gift'        => $award->gift,
                'description'        => $award->description,
                'award_type'        => !empty($award->awardType) ? [
                    'name'  => $award->awardType->name
                ] : [],
                'users'     => !empty($award->users) ? [
                    'name'      => $award->users->name,
                    'email'     => $award->users->email,
                ] : []
            ];
        });

        return response()->json(['status'=>'success','data' => $data]);

    }

    public function transferList(Request $request){
        if (!module_is_active('Hrm')) {
            return response()->json(['status'=>'error','message'=>'Hrm Module Not Active!']);
        }

        if (!in_array(Auth::user()->type, Auth::user()->not_emp_type)) {
            $transfers     = Transfer::where('user_id', Auth::user()->id)->where('workspace', $request->workspace_id)->with(['branch', 'department'])->get();
        } else {
            $transfers     = Transfer::where('created_by', creatorId())->where('workspace', $request->workspace_id)->with(['branch', 'department'])->get();
        }

        $data = $transfers->map(function($transfer){
            return [
                'id'                => $transfer->id,
                'transfer_date'     => $transfer->transfer_date,
                'description'       => $transfer->description,
                'branch'            => [
                        'name'      =>$transfer->branch->name,
                    ],
                'department'        => [
                        'name'  => $transfer->department->name,
                    ]
            ];
        });

        return response()->json(['status'=>'success','data'=>$data]);
    }

    public function resignationsList(Request $request){
        if (!module_is_active('Hrm')) {
            return response()->json(['status'=>'error','message'=>'Hrm Module Not Active!']);
        }

        if (!in_array(Auth::user()->type, Auth::user()->not_emp_type)) {
            $resignations     = Resignation::where('user_id', Auth::user()->id)->where('workspace', $request->workspace_id)->get();
        } else {
            $resignations     = Resignation::where('created_by', creatorId())->where('workspace', $request->workspace_id)->get();
        }

        $data = $resignations->map(function($resignation){
            return [
                'id'                    => $resignation->id,
                'resignation_date'      => $resignation->resignation_date,
                'last_working_date'     => $resignation->last_working_date,
                'description'           => $resignation->description,
                'last_working_date'     => $resignation->last_working_date,
            ];
        });

        return response()->json(['status'=>'success','data'=>$data]);
    }

    public function holidaysList(Request $request){
        if (!module_is_active('Hrm')) {
            return response()->json(['status'=>'error','message'=>'Hrm Module Not Active!']);
        }

        $holidays = Holiday::where('created_by', '=', creatorId())->where('workspace', $request->workspace_id)->get()
                ->map(function($holiday){
                    return [
                        'id'            => $holiday->id,
                        'start_date'    => $holiday->start_date,
                        'end_date'      => $holiday->end_date,
                        'occasion'      => $holiday->occasion,
                    ];
                });

        return response()->json(['status'=>'success','data'=>$holidays]);

    }

    public function eventsList(Request $request)
    {
        if (!module_is_active('Hrm')) {
            return response()->json(['status'=>'error','message'=>'Hrm Module Not Active!']);
        }

        $events = Event::where('created_by', '=', creatorId())->where('workspace', $request->workspace_id)
                    ->get()
                    ->map(function($event){
                        return [
                            'id'            => $event->id,
                            'title'         => $event->title,
                            'start_date'    => $event->start_date,
                            'end_date'      => $event->end_date,
                            'description'   => $event->description,
                        ];
                    });

        return response()->json(['status'=>'success','data'=>$events]);
    }

    public function documentsList(Request $request){
        if (!module_is_active('Hrm')) {
            return response()->json(['status'=>'error','message'=>'Hrm Module Not Active!']);
        }

        $documents = Document::where('workspace', $request->workspace_id)
                ->leftJoin('roles', 'documents.role', '=', 'roles.id')
                ->where('documents.created_by', creatorId())
                ->select('documents.*', 'roles.name as role_name')
                ->get()
                ->map(function($document){
                    return [
                        'id'            => $document->id,
                        'name'          => $document->name,
                        'document'      => get_file($document->document),
                        'description'   => $document->description,
                        'role_name'     => $document->role_name,
                    ];
                });

        return response()->json(['status'=>'success','data' => $documents]);
    }

    public function companyPolicyList(Request $request){
        if (!module_is_active('Hrm')) {
            return response()->json(['status'=>'error','message'=>'Hrm Module Not Active!']);
        }

        $companyPolicies = CompanyPolicy::where('workspace', $request->workspace_id)
                        ->where('created_by', '=', creatorId())
                        ->with('branches')
                        ->get()
                        ->map(function($companyPolicy){
                            return [
                                'id'        => $companyPolicy->id,
                                'title'        => $companyPolicy->title,
                                'description'        => $companyPolicy->description,
                                'attachment'        => get_file($companyPolicy->attachment),
                                'branch_name'        => !empty($companyPolicy->branches) ? $companyPolicy->branches->name : '',
                            ];
                        });

        return response()->json(['status'=>'success','data' => $companyPolicies]);

    }

    public function branchesList(Request $request){
        if (!module_is_active('Hrm')) {
            return response()->json(['status'=>'error','message'=>'Hrm Module Not Active!']);
        }

        $branches = Branch::where('created_by', '=', creatorId())
                    ->where('workspace', $request->workspace_id)
                    ->get()
                    ->map(function($branch){
                        return [
                            'id'  => $branch->id,
                            'name'  => $branch->name
                        ];
                    });

        return response()->json(['status'=>'success','data'=>$branches]);
    }

    public function departmentsList(Request $request){
        if (!module_is_active('Hrm')) {
            return response()->json(['status'=>'error','message'=>'Hrm Module Not Active!']);
        }

        $departments = Department::where('created_by', '=', creatorId())->where('workspace', $request->workspace_id)
                    ->with('branch')
                    ->get()
                    ->map(function($department){
                        return [
                            'id'        => $department->id,
                            'department_name'        => $department->name,
                            'branch_name'        => $department->branch->name,
                        ];
                    });

        return response()->json(['status'=>'success','data'=>$departments]);
    }

    public function designationsList(Request $request){
        if (!module_is_active('Hrm')) {
            return response()->json(['status'=>'error','message'=>'Hrm Module Not Active!']);
        }

        $designations = Designation::where('created_by', '=', creatorId())
                        ->where('workspace', $request->workspace_id)
                        ->with(['branch', 'department'])
                        ->get()
                        ->map(function($designation){
                            return [
                                'id'            => $designation->id,
                                'designation_name'          => $designation->name,
                                'branch_name'          => $designation->branch->name,
                                'department_name'          => $designation->department->name,
                            ];
                        });

        return response()->json(['status'=>'success','data'=>$designations]);

    }

}
