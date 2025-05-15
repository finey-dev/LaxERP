<?php

namespace Workdo\CourierManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\CourierManagement\Entities\Servicetype;
use Workdo\CourierManagement\Events\Courierservicetypecreate;
use Workdo\CourierManagement\Events\Courierservicetypeupdate;
use Workdo\CourierManagement\Events\Courierservicetypedelete;


class ServicetypeController extends Controller
{
    public function index()
    {
        if(Auth::user()->isAbleto('servicetype manage')){
            $servicetypeData = Servicetype::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            return view('courier-management::service_type.index', compact('servicetypeData'));
        }else{
            return redirect()->back()->with('error',__('Permission Denied !!!'));
        }

    }

    public function create()
    {
        if (Auth::user()->isAbleTo('servicetype create')) {
            return view('courier-management::service_type.create');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('servicetype create')) {
            $validator = Validator::make($request->all(), [
                'service_type' => 'required',
            ]);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $serviceType = new Servicetype();
            $serviceType->service_type  = $request->service_type;
            $serviceType->workspace  = getActiveWorkSpace();
            $serviceType->created_by  = creatorId();
            $serviceType->save();

            event(new Courierservicetypecreate($serviceType, $request));

            return redirect()->back()->with('success', __('The service type has been created successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function edit(Request $request, $servicetypeId)
    {
        if (Auth::user()->isAbleTo('servicetype edit')) {
            $servicetypeData = Servicetype::where('id', $servicetypeId)->first();
            if ($servicetypeData) {
                return view('courier-management::service_type.edit', compact('servicetypeData'));
            } else {
                return redirect()->back()->with('error', __('Service Type Not Found !!!'));
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request, $servicetypeId)
    {
        if (Auth::user()->isAbleTo('servicetype edit')) {
            $validator = Validator::make($request->all(), [
                'service_type' => 'required',
            ]);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $servicetypeData = Servicetype::where('id', $servicetypeId)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->first();
            if ($servicetypeData) {
                $servicetypeData->service_type = $request->service_type;
                $servicetypeData->save();

                event(new Courierservicetypeupdate($servicetypeData, $request));

                return redirect()->back()->with('success', __('The service type details are updated successfully'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Service Type Not Found !!!'));
        }
    }


    public function destroy(Request $request, $servicetypeId)
    {
        if (Auth::user()->isAbleTo('servicetype delete')) {
            $servicetypeData = Servicetype::where('id', $servicetypeId)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->first();;
            if ($servicetypeData) {
                event(new Courierservicetypedelete($servicetypeData, $request));
                $servicetypeData->delete();
                return redirect()->back()->with('success', __('The service type has been deleted'));
            } else {
                return redirect()->back()->with('error', __('Service Type Not Found !!!'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
