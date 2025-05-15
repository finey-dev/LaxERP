<?php

namespace Workdo\CourierManagement\Http\Controllers;

use Google\Service\Classroom\Course;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\CourierManagement\Entities\CourierBranch;
use Workdo\CourierManagement\Events\Courierbranchcreate;
use Workdo\CourierManagement\Events\Courierbranchupdate;
use Workdo\CourierManagement\Events\Courierbranchdelete;



class BranchController extends Controller
{
    public function index()
    {
        if(Auth::user()->isAbleTo('courier branch manage')){
            $branchData = CourierBranch::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            return view('courier-management::branch.index', compact('branchData'));
        }else{
            return redirect()->back()->with('error','Permission Denied !!!');
        }

    }


    public function create()
    {
        if (Auth::user()->isAbleTo('courier branch create')) {
            return view('courier-management::branch.create');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('courier branch create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'branch_name' => ['required','string','not_regex:/[*\-\/]/'],
                    'branch_location' => ['required','string','not_regex:/[*\-\/]/'],
                    'city' => ['required','string','not_regex:/[*\-\/]/'],
                    'state' => ['required','string','not_regex:/[*\-\/]/'],
                    'country' => ['required','string','not_regex:/[*\-\/]/'],
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $branchData = new CourierBranch();
            $branchData->branch_name = $request->branch_name;
            $branchData->branch_location = $request->branch_location;
            $branchData->city = $request->city;
            $branchData->state = $request->state;
            $branchData->country = $request->country;
            $branchData->workspace = getActiveWorkSpace();
            $branchData->created_by = creatorId();
            $branchData->save();

            event(new Courierbranchcreate($branchData, $request));

            return redirect()->back()->with('success', __('The branch has been created successfully'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function show(Request $request, $branchId)
    {
        if (Auth::user()->isAbleTo('courier branch show')) {
            $brachData = CourierBranch::where('id', $branchId)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->first();
            if ($brachData) {
                return view('courier-management::branch.show', compact('brachData'));
            } else {
                return redirect()->back()->with('error', __('Branch Not Found !!! '));
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function edit(Request $request, $branchId)
    {
        if (Auth::user()->isAbleTo('courier branch edit')) {
            $branchData = CourierBranch::find($branchId);
            if ($branchData) {
                return view('courier-management::branch.edit', compact('branchData'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $branchId)
    {
        if (Auth::user()->isAbleTo('courier branch edit')) {
            $branchData = CourierBranch::where('id', $branchId)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->first();
            if ($branchData) {
                $branchData->branch_name = $request->branch_name;
                $branchData->branch_location = $request->branch_location;
                $branchData->city = $request->city;
                $branchData->state = $request->state;
                $branchData->country = $request->country;
                $branchData->save();

                event(new Courierbranchupdate($branchData, $request));
                return redirect()->back()->with('success', __('The branch details are updated successfully'));
            } else {
                return redirect()->back()->with('error', __('Branch Not Found !!!'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Request $request, $branchId)
    {
        if (Auth::user()->isAbleTo('courier branch delete')) {
            $branchData = CourierBranch::where('id', $branchId)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->first();
            if ($branchData) {
                event(new Courierbranchdelete($branchData, $request));
                $branchData->delete();

                return redirect()->back()->with('success', __('The branch has been deleted'));
            } else {
                return redirect()->back()->with('error', __('Branch Not Found !!!'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
