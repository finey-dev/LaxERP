<?php

namespace Workdo\VisitorManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\VisitorManagement\DataTables\PreRegistrationDataTable;
use Workdo\VisitorManagement\Entities\PreRegistration;
use Workdo\VisitorManagement\Entities\Visitors;
use Workdo\VisitorManagement\Events\CreatePreRegistration;
use Workdo\VisitorManagement\Events\DeletePreRegistration;
use Workdo\VisitorManagement\Events\UpdatePreRegistration;
use Illuminate\Support\Facades\Auth;

class PreRegistrationController extends Controller
{
    public function index(PreRegistrationDataTable $dataTable)
    {
        if (\Auth::user()->isAbleTo('visitor pre registration manage')) {

            return $dataTable->render('visitor-management::pre-registration.index');
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->isAbleTo('visitor pre registration create')) {
            $visitors = Visitors::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->pluck('first_name', 'id');
            $visitors->prepend("Select Visitor", "");

            return view('visitor-management::pre-registration.create', compact('visitors'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('visitor pre registration create')) {
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

            $pre_registration                = new PreRegistration();
            $pre_registration->visitor_id = $request->visitor_id;
            $pre_registration->appointment_date        =  date('Y-m-d H:i:s', strtotime($request->appointment_date));
            $pre_registration->status        =  $request->status;
            $pre_registration->workspace     = getActiveWorkSpace();
            $pre_registration->created_by    = creatorId();
            $pre_registration->save();
            event(new CreatePreRegistration($request, $pre_registration));

            return redirect()->route('visitors-pre-registration.index')->with('success', __('The Pre Registration has been created successfully.'));
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
        if (\Auth::user()->isAbleTo('visitor pre registration edit')) {
            $pre_registration     = PreRegistration::find($id);
            if (!$pre_registration) {
                return redirect()->back()->with('error', __('Visitor pre-registration Not Found'));
            }
            $visitors       = Visitors::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->pluck('first_name', 'id');
            $visitors->prepend("Select Visitor", "");

            return view('visitor-management::pre-registration.edit', compact('pre_registration', 'visitors'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('visitor pre registration edit')) {

            $pre_registration = PreRegistration::find($id);
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
            $pre_registration->visitor_id          = $request->visitor_id;
            $pre_registration->appointment_date        =  date('Y-m-d H:i:s', strtotime($request->appointment_date));
            $pre_registration->status        =  $request->status;
            $pre_registration->save();
            event(new UpdatePreRegistration($request, $pre_registration));

            return redirect()->route('visitors-pre-registration.index')->with('success', __('The Pre Registration has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('visitor pre registration delete')) {
            $pre_registration = PreRegistration::find($id);
            if (!$pre_registration) {
                return redirect()->back()->with('error', __('Visitor pre-registration Not Found'));
            }
            event(new DeletePreRegistration($pre_registration));
            $pre_registration->delete();
            return redirect()->back()->with('success', __('The Pre Registration has been deleted successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
