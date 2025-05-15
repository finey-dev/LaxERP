<?php

namespace Workdo\VisitorManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\VisitorManagement\Entities\ComplianceType;
use Workdo\VisitorManagement\Events\CreateComplianceType;
use Workdo\VisitorManagement\Events\DestroyComplianceType;
use Workdo\VisitorManagement\Events\UpdateComplianceType;

class ComplianceTypeController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAbleTo('visitor compliance type manage')) {
            $compliance_types = ComplianceType::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();

            return view('visitor-management::compliance-type.index', compact('compliance_types'));
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
        if (Auth::user()->isAbleTo('visitor compliance type create')) {

            return view('visitor-management::compliance-type.create');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('visitor compliance type create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:255',

                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            // Store the validated data into the database
            $compliance_type = new ComplianceType();
            $compliance_type->name          =  $request->name;
            $compliance_type->workspace          = getActiveWorkSpace();
            $compliance_type->created_by         = creatorId();
            $compliance_type->save();
            event(new CreateComplianceType($request, $compliance_type));

            return redirect()->back()->with('success', __('The Compliance Type has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id)
    {
        return view('visitor-management::show');
    }

    public function edit($id)
    {
        if (Auth::user()->isAbleTo('visitor compliance type edit')) {
            $compliance_type = ComplianceType::find($id);
            return view('visitor-management::compliance-type.edit', compact('compliance_type'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {

        if (Auth::user()->isAbleTo('visitor compliance type edit')) {
            $compliance_type = ComplianceType::find($id);
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:255',

                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            // Store the validated data into the database
            $compliance_type->name          =  $request->name;
            $compliance_type->workspace          = getActiveWorkSpace();
            $compliance_type->created_by         = creatorId();
            $compliance_type->save();
            event(new UpdateComplianceType($request, $compliance_type));

            return redirect()->back()->with('success', __('The Compliance Type has been updated successfully.'));
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
        if (Auth::user()->isAbleTo('visitor compliance type delete')) {

            $compliance_type = ComplianceType::find($id);
            event(new DestroyComplianceType($compliance_type));
            $compliance_type->delete();
            return redirect()->route('visitors-compliance-type.index')->with('success', __('The Compliance Type has been deleted successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
