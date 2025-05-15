<?php

namespace Workdo\CourierManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\CourierManagement\DataTables\ServiceAgreementDatatable;
use Workdo\CourierManagement\Entities\ServiceAgreements;
use Workdo\CourierManagement\Events\ServiceAgreementscreate;
use Workdo\CourierManagement\Events\ServiceAgreementsdelete;
use Workdo\CourierManagement\Events\ServiceAgreementsupdate;

class ServiceAgreementsController extends Controller
{
    public function index(ServiceAgreementDatatable $dataTable)
    {
        if (Auth::user()->isAbleTo('service agreements manage')) {
            return $dataTable->render('courier-management::service-agreements.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function create()
    {
        if (Auth::user()->isAbleTo('service agreements create')) {
            return view('courier-management::service-agreements.create');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('service agreements create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'customer_name' => 'required',

                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $service_agreements                        = new ServiceAgreements();
            $service_agreements->customer_name         = $request->customer_name;
            $service_agreements->start_date            = $request->start_date;
            $service_agreements->end_date              = $request->end_date;
            $service_agreements->agreement_details     = $request->agreement_details;
            $service_agreements->workspace             = getActiveWorkSpace();
            $service_agreements->created_by            = creatorId();
            $service_agreements->save();
            event(new ServiceAgreementscreate($service_agreements, $request));

            return redirect()->route('service-agreements.index')->with('success', __('The Service Agreements created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function show($id)
    {
        if (Auth::user()->isAbleTo('service agreements show')) {
            $service_agreements = ServiceAgreements::find($id);
            return view('courier-management::service-agreements.show', compact('service_agreements'));
        } else {
            return redirect()->back()->with(['error' => __('Permission denied.')], 401);
        }
    }
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('service agreements edit')) {
            $service_agreements = ServiceAgreements::find($id);
            if ($service_agreements->created_by == creatorId() && $service_agreements->workspace == getActiveWorkSpace()) {
                return view('courier-management::service-agreements.edit', compact('service_agreements'));
            } else {
                return response()->json(['error' => __('Permission denied.')]);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')]);
        }
    }
    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('service agreements edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'customer_name' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $service_agreements                        = ServiceAgreements::find($id);
            $service_agreements->customer_name         = $request->customer_name;
            $service_agreements->start_date            = $request->start_date;
            $service_agreements->end_date              = $request->end_date;
            $service_agreements->agreement_details     = $request->agreement_details;
            $service_agreements->workspace             = getActiveWorkSpace();
            $service_agreements->created_by            = creatorId();
            $service_agreements->update();
            event(new ServiceAgreementsupdate($service_agreements, $request));

            return redirect()->route('service-agreements.index')->with('success', __('The Service Agreements updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function destroy(Request $request,$id)
    {
        if (Auth::user()->isAbleTo('service agreements delete')) {
            $service_agreements = ServiceAgreements::find($id);
            if ($service_agreements->created_by == creatorId()  && $service_agreements->workspace == getActiveWorkSpace()) {
                event(new ServiceAgreementsdelete($service_agreements, $request));

                $service_agreements->delete();
                return redirect()->route('service-agreements.index')->with('success', __('The Service Agreements deleted successfully.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
