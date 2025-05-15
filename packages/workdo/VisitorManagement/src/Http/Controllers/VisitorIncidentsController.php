<?php

namespace Workdo\VisitorManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\VisitorManagement\DataTables\VisitorIncidentDataTable;
use Workdo\VisitorManagement\Entities\VisitorIncident;
use Workdo\VisitorManagement\Entities\Visitors;
use Workdo\VisitorManagement\Events\CreateVisitorIncident;
use Workdo\VisitorManagement\Events\DeleteVisitorIncident;
use Workdo\VisitorManagement\Events\UpdateVisitorIncident;
use Illuminate\Support\Facades\Auth;


class VisitorIncidentsController extends Controller

{
    public function index(VisitorIncidentDataTable $dataTable)
    {
        if (\Auth::user()->isAbleTo('visitor incidents manage')) {

            return $dataTable->render('visitor-management::visitor-incident.index');
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->isAbleTo('visitor incidents create')) {
            $visitors = Visitors::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->pluck('first_name', 'id');
            $visitors->prepend("Select Visitor", "");

            return view('visitor-management::visitor-incident.create', compact('visitors'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('visitor incidents create')) {
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

            $visitor_incident                = new VisitorIncident();
            $visitor_incident->visitor_id = $request->visitor_id;
            $visitor_incident->incident_date        =  date('Y-m-d H:i:s', strtotime($request->incident_date));
            $visitor_incident->incident_description        =  $request->incident_description;
            $visitor_incident->action_taken        =  $request->action_taken;
            $visitor_incident->workspace     = getActiveWorkSpace();
            $visitor_incident->created_by    = creatorId();
            $visitor_incident->save();
            event(new CreateVisitorIncident($request, $visitor_incident));

            return redirect()->route('visitors-incidents.index')->with('success', __('The Visitor Incidents has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id)
    {
        if (\Auth::user()->isAbleTo('visitor incidents manage')) {
            $visitor_incident  = VisitorIncident::find($id);
            return view('visitor-management::visitor-incident.show' ,compact('visitor_incident'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function edit($id)
    {
        if (\Auth::user()->isAbleTo('visitor incidents edit')) {
            $visitor_incident     = VisitorIncident::find($id);
            if (!$visitor_incident) {
                return redirect()->back()->with('error', __('Visitor incidents Not Found'));
            }
            $visitors       = Visitors::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->pluck('first_name', 'id');
            $visitors->prepend("Select Visitor", "");

            return view('visitor-management::visitor-incident.edit', compact('visitor_incident', 'visitors'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('visitor incidents edit')) {

            $visitor_incident = VisitorIncident::find($id);
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
            $visitor_incident->visitor_id          = $request->visitor_id;
            $visitor_incident->incident_date        =  date('Y-m-d H:i:s', strtotime($request->incident_date));
            $visitor_incident->incident_description        =  $request->incident_description;
            $visitor_incident->action_taken        =  $request->action_taken;
            $visitor_incident->save();
            event(new UpdateVisitorIncident($request, $visitor_incident));

            return redirect()->route('visitors-incidents.index')->with('success', __('The Visitor Incidents has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('visitor incidents delete')) {
            $visitor_incident = VisitorIncident::find($id);
            if (!$visitor_incident) {
                return redirect()->back()->with('error', __('Visitor incidents Not Found'));
            }
            event(new DeleteVisitorIncident($visitor_incident));
            $visitor_incident->delete();
            return redirect()->back()->with('success', __('The Visitor Incidents has been deleted successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
