<?php

namespace Workdo\Recruitment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\Hrm\Entities\Employee;
use Workdo\Hrm\Events\CreatePayslipType;
use Workdo\Hrm\Events\DestroyPayslipType;
use Workdo\Hrm\Events\UpdatePayslipType;
use Workdo\Recruitment\Entities\PayslipType;

class PayslipTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (Auth::user()->isAbleTo('paysliptype manage')) {
            $paysliptypes = PayslipType::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();
            return view('recruitment::paysliptype.index', compact('paysliptypes'));
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
        if (Auth::user()->isAbleTo('paysliptype create')) {
            return view('recruitment::paysliptype.create');
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
        if (Auth::user()->isAbleTo('paysliptype create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:30',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $paysliptype             = new PayslipType();
            $paysliptype->name       = $request->name;
            $paysliptype->workspace  = getActiveWorkSpace();
            $paysliptype->created_by = creatorId();
            $paysliptype->save();

            event(new CreatePayslipType($request, $paysliptype));

            return redirect()->route('paysliptype.index')->with('success', __('The payslip type has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return redirect()->back();
        return view('recruitment::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(PayslipType $paysliptype)
    {
        if (Auth::user()->isAbleTo('paysliptype edit')) {
            if ($paysliptype->created_by == creatorId() && $paysliptype->workspace == getActiveWorkSpace()) {
                return view('recruitment::paysliptype.edit', compact('paysliptype'));
            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
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
    public function update(Request $request, PayslipType $paysliptype)
    {
        if (Auth::user()->isAbleTo('paysliptype edit')) {
            if ($paysliptype->created_by == creatorId() && $paysliptype->workspace == getActiveWorkSpace()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required|max:20',

                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $paysliptype->name = $request->name;
                $paysliptype->save();

                event(new UpdatePayslipType($request, $paysliptype));

                return redirect()->route('paysliptype.index')->with('success', __('The payslip type details are updated successfully.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
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
    public function destroy(PayslipType $paysliptype)
    {
        if (Auth::user()->isAbleTo('paysliptype delete')) {
            if ($paysliptype->created_by == creatorId() && $paysliptype->workspace == getActiveWorkSpace()) {
                $employee     = Employee::where('salary_type', $paysliptype->id)->where('workspace', getActiveWorkSpace())->get();
                if (count($employee) == 0) {

                    event(new DestroyPayslipType($paysliptype));

                    $paysliptype->delete();
                } else {
                    return redirect()->route('paysliptype.index')->with('error', __('This Payslip Type has Set Salary. Please remove the Set Salary from this Payslip Type.'));
                }
                return redirect()->route('paysliptype.index')->with('success', __('The payslip type has been deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
