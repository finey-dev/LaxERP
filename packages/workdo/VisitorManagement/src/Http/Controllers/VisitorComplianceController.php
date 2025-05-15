<?php

namespace Workdo\VisitorManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\VisitorManagement\DataTables\VisitorComplianceDataTable;
use Workdo\VisitorManagement\Entities\ComplianceType;
use Workdo\VisitorManagement\Entities\VisitorCompliance;
use Workdo\VisitorManagement\Entities\Visitors;
use Workdo\VisitorManagement\Events\CreateVisitorCompliance;
use Workdo\VisitorManagement\Events\DeleteVisitorCompliance;
use Workdo\VisitorManagement\Events\UpdateVisitorCompliance;

class VisitorComplianceController extends Controller
{
    public function index(VisitorComplianceDataTable $dataTable)
    {
        if (\Auth::user()->isAbleTo('visitor compliance manage')) {

            return $dataTable->render('visitor-management::visitor-compliance.index');
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->isAbleTo('visitor compliance create')) {

            $visitors = Visitors::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->pluck('first_name', 'id');
            $visitors->prepend("Select Visitor", "");
            $compliance = ComplianceType::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->pluck('name', 'id');
            $compliance->prepend("Select Compliance Type", "");
            return view('visitor-management::visitor-compliance.create', compact('visitors','compliance'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('visitor compliance create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'visitor_id' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $visitor_compliance                = new VisitorCompliance();
            $visitor_compliance->visitor_id = $request->visitor_id;
            $visitor_compliance->compliance_type = $request->compliance_type;
            $visitor_compliance->date        =  date('Y-m-d H:i:s', strtotime($request->date));
            $visitor_compliance->status        =  $request->status;
            $visitor_compliance->workspace     = getActiveWorkSpace();
            $visitor_compliance->created_by    = creatorId();
            $visitor_compliance->save();
            event(new CreateVisitorCompliance($request, $visitor_compliance));

            return redirect()->route('visitors-compliance.index')->with('success', __('The Visitor Compliance has been created successfully.'));
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
        if (\Auth::user()->isAbleTo('visitor compliance edit')) {
            $visitor_compliance     = VisitorCompliance::find($id);
            if (!$visitor_compliance) {
                return redirect()->back()->with('error', __('Visitor visitor-compliance Not Found'));
            }
            $visitors       = Visitors::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->pluck('first_name', 'id');
            $visitors->prepend("Select Visitor", "");
            $compliance = ComplianceType::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->pluck('name', 'id');
            $compliance->prepend("Select Compliance Type", "");
            return view('visitor-management::visitor-compliance.edit', compact('visitor_compliance', 'visitors','compliance'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('visitor compliance edit')) {

            $visitor_compliance = VisitorCompliance::find($id);
            $validator = \Validator::make(
                $request->all(),
                [
                    'visitor_id' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $visitor_compliance->visitor_id = $request->visitor_id;
            $visitor_compliance->compliance_type = $request->compliance_type;
            $visitor_compliance->date        =  date('Y-m-d H:i:s', strtotime($request->date));
            $visitor_compliance->status        =  $request->status;
            $visitor_compliance->save();
            event(new UpdateVisitorCompliance($request, $visitor_compliance));

            return redirect()->route('visitors-compliance.index')->with('success', __('The Visitor Compliance has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('visitor compliance delete')) {
            $visitor_compliance = VisitorCompliance::find($id);
            if (!$visitor_compliance) {
                return redirect()->back()->with('error', __('Visitor Compliance Not Found'));
            }
            event(new DeleteVisitorCompliance($visitor_compliance));
            $visitor_compliance->delete();
            return redirect()->back()->with('success', __('The Visitor Compliance has been deleted successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
