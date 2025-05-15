<?php

namespace Workdo\Performance\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Hrm\Entities\Branch;
use Workdo\Hrm\Entities\Department;
use Workdo\Hrm\Entities\Designation;
use Workdo\Hrm\Entities\Employee;
use Workdo\Performance\Entities\Competencies;
use Workdo\Performance\Entities\Appraisal;
use Workdo\Performance\Entities\Performance_Type;
use Workdo\Performance\Entities\Indicator;
use Illuminate\Support\Facades\Auth;
use Workdo\Performance\DataTables\AppraisalDataTable;
use Workdo\Performance\Events\CreateAppraisal;
use Workdo\Performance\Events\DestroyAppraisal;
use Workdo\Performance\Events\UpdateAppraisal;

class AppraisalController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(AppraisalDataTable $dataTable)
    {
        if (\Auth::user()->isAbleTo('appraisal manage')) {

            return $dataTable->render('performance::appraisal.index');
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
        if (\Auth::user()->isAbleTo('appraisal create')) {
            $brances = Branch::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();

            $employee = Employee::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $employee->prepend('Select Employee', '');
            $performance_types = Performance_Type::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();

            return view('performance::appraisal.create', compact('employee', 'brances', 'performance_types'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (\Auth::user()->isAbleTo('appraisal create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'brances' => 'required',
                    'employee' => 'required',
                    'rating' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $appraisal                 = new Appraisal();
            $employee = Employee::where('id', '=', $request->employee)->first();
            if (!empty($employee)) {
                $appraisal->user_id = $employee->user_id;
            }
            $appraisal->branch         = $request->brances;
            $appraisal->employee       = $request->employee;
            $appraisal->appraisal_date = $request->appraisal_date;
            $appraisal->rating         = json_encode($request->rating, true);
            $appraisal->workspace      = getActiveWorkSpace();
            $appraisal->remark         = $request->remark;
            $appraisal->created_by     = creatorId();

            $appraisal->save();
            event(new CreateAppraisal($request, $appraisal));
            return redirect()->route('appraisal.index')->with('success', __('The appraisal has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Appraisal $appraisal)
    {
        if (\Auth::user()->isAbleTo('appraisal show')) {
            $rating = json_decode($appraisal->rating, true);
            $performance_types = Performance_Type::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $employee = Employee::find($appraisal->employee);
            $indicator = Indicator::where('branch', $employee->branch_id)->where('department', $employee->department_id)->where('designation', $employee->designation_id)->first();

            $ratings = json_decode($indicator->rating, true);

            return view('performance::appraisal.show', compact('appraisal', 'performance_types', 'rating', 'ratings'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Appraisal $appraisal)
    {
        if (\Auth::user()->isAbleTo('appraisal edit')) {
            $performance_types = Performance_Type::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();

            $employee   = Employee::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $employee->prepend('Select Employee', '');

            $brances = Branch::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $rating = json_decode($appraisal->rating, true);

            return view('performance::appraisal.edit', compact('brances', 'employee', 'appraisal', 'performance_types', 'rating'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, Appraisal $appraisal)
    {
        if (\Auth::user()->isAbleTo('appraisal edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'brances' => 'required',
                    'employees' => 'required',
                    'rating' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $appraisal->branch         = $request->brances;
            $appraisal->employee       = $request->employees;
            $appraisal->appraisal_date = $request->appraisal_date;
            $appraisal->rating         = json_encode($request->rating, true);
            $appraisal->remark         = $request->remark;
            $appraisal->save();
            event(new UpdateAppraisal($request, $appraisal));

            return redirect()->route('appraisal.index')->with('success', __('The appraisal details are updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Appraisal $appraisal)
    {
        if (\Auth::user()->isAbleTo('appraisal delete')) {
            if ($appraisal->created_by == creatorId() &&  $appraisal->workspace  == getActiveWorkSpace()) {
                event(new DestroyAppraisal($appraisal));

                $appraisal->delete();

                return redirect()->route('appraisal.index')->with('success', __('The appraisal has been deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function empByStar(Request $request)
    {
        $employee = Employee::find($request->employee);

        if($employee)
        {
            $indicator = Indicator::where('branch',$employee->branch_id)->where('department',$employee->department_id)->where('designation',$employee->designation_id)->first();
            if($indicator)
            {
                $ratings = json_decode($indicator->rating, true);

                $performance_types = Performance_Type::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->get();

                $viewRender = view('performance::appraisal.star', compact('ratings','performance_types'))->render();
                return response()->json(array('success' => true, 'html'=>$viewRender));
            }
        }else
        {
            return response()->json(array('error' => true, 'html'=>''));
        }

    }
    public function empByStar1(Request $request)
    {
        $employee = Employee::find($request->employee);
        if ($employee) {
            $appraisal = Appraisal::find($request->appraisal);
            $indicator = Indicator::where('branch', $employee->branch_id)->where('department', $employee->department_id)->where('designation', $employee->designation_id)->first();
            if ($indicator) {
                $ratings = json_decode($indicator->rating, true);
                $rating = json_decode($appraisal->rating, true);
                $performance_types = Performance_Type::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();
                $viewRender = view('performance::appraisal.staredit', compact('ratings', 'rating', 'performance_types'))->render();
                return response()->json(array('success' => true, 'html' => $viewRender));
            }
        } else {
            return response()->json(array('error' => true, 'html' => ''));
        }
    }
    public function getemployee(Request $request)
    {
        $data['employee'] = Employee::where('branch_id', $request->branch_id)->get();
        return response()->json($data);
    }

    public function checkBranchIndicator(Request $request)
    {
        $branchExists = Indicator::where('branch', $request->branch_id)
            ->where('created_by', creatorId())
            ->where('workspace', getActiveWorkSpace())
            ->exists();

        return response()->json(['exists' => $branchExists]);
    }
}
