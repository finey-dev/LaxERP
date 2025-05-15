<?php

namespace Workdo\Performance\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Performance\Entities\Indicator;
use Workdo\Performance\Entities\Performance_Type;
use Workdo\Hrm\Entities\Branch;
use Workdo\Hrm\Entities\Department;
use Workdo\Hrm\Entities\Designation;
use Workdo\Hrm\Entities\Employee;
use Illuminate\Support\Facades\Auth;
use Workdo\Performance\DataTables\IndicatorDataTable;
use Workdo\Performance\Entities\Competencies;
use Workdo\Performance\Events\CreateIndicator;
use Workdo\Performance\Events\Destroyindicator;
use Workdo\Performance\Events\UpdateIndicator;

class IndicatorController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(IndicatorDataTable $dataTable)
    {
        if (\Auth::user()->isAbleTo('indicator manage')) {

            return $dataTable->render('performance::indicator.index');
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
        if (\Auth::user()->isAbleTo('indicator create')) {
            $performance_types = Performance_Type::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->get();
            $competencies = Competencies::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->get();
            $brances     = Branch::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $departments = Department::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $departments->prepend('Select Department', '');
            $degisnation = Designation::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');

            return view('performance::indicator.create', compact('performance_types', 'competencies', 'brances', 'departments', 'degisnation'));
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
        if (\Auth::user()->isAbleTo('indicator create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'branch_id' => 'required',
                    'department_id' => 'required',
                    'designation_id' => 'required',
                    'rating' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }


            $indicator              = new Indicator();
            $indicator->branch      = $request->branch_id;
            $indicator->department  = $request->department_id;
            $indicator->designation = $request->designation_id;
            $indicator->rating      = json_encode($request->rating, true);
            $indicator->workspace   = getActiveWorkSpace();
            $indicator->created_by = creatorId();
            $indicator->save();
            event(new  CreateIndicator($request, $indicator));

            return redirect()->route('indicator.index')->with('success', __('The indicator has been created successfully.'));

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Indicator $indicator)
    {
        if (\Auth::user()->isAbleTo('indicator show')) {
            $ratings = json_decode($indicator->rating, true);
            $performance_types = Performance_Type::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->get();
        return view('performance::indicator.show', compact('indicator', 'ratings', 'performance_types'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Indicator $indicator)
    {
        if (\Auth::user()->isAbleTo('indicator edit')) {
            $performance_types = Performance_Type::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->get();
            $competencies = Competencies::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->get();
            $brances     = Branch::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->get();
            $departments = Department::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->get();
            $degisnation = Designation::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->get();
            $ratings = json_decode($indicator->rating, true);
            return view('performance::indicator.edit', compact('performance_types', 'competencies', 'brances', 'departments', 'indicator', 'ratings', 'degisnation'));
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
    public function update(Request $request, Indicator $indicator)
    {
        if (\Auth::user()->isAbleTo('indicator edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'branch_id' => 'required',
                    'department_id' => 'required',
                    'designation_id' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $indicator->branch      = $request->branch_id;
            $indicator->department  = $request->department_id;
            $indicator->designation = $request->designation_id;
            $indicator->rating = json_encode($request->rating, true);
            $indicator->save();
            event(new  UpdateIndicator($request, $indicator));

            return redirect()->route('indicator.index')->with('success', __('The indicator details are updated successfully.'));
        }else{
            return redirect()->back()->with('error', __('Permission denied.'));

        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Indicator $indicator)
    {
        if (\Auth::user()->isAbleTo('indicator delete')) {
            if ($indicator->created_by == creatorId() &&  $indicator->workspace  == getActiveWorkSpace())  {
            event(new  Destroyindicator($indicator));

                $indicator->delete();

                return redirect()->route('indicator.index')->with('success', __('The indicator has been deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
