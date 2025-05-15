<?php

namespace Workdo\CourierManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Workdo\CourierManagement\Entities\Trackingstatus;
use Workdo\CourierManagement\Events\Couriertrackingstatuscreate;
use Workdo\CourierManagement\Events\Couriertrackingstatusupdate;
use Workdo\CourierManagement\Events\Couriertrackingstatusdelete;


class TrackingstatusController extends Controller
{

    public function index()
    {
        if (Auth::user()->isAbleTo('tracking manage')) {
            $trackingStatusData = Trackingstatus::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->orderby('order', 'asc')->get();
            return view('courier-management::tracking_status.index', compact('trackingStatusData'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied !!!'));
        }
    }


    public function create()
    {
        if (Auth::user()->isAbleTo('tracking create')) {
            return view('courier-management::tracking_status.create');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('tracking create')) {
            $validator = Validator::make($request->all(), [
                'icon' => 'required',
                'status_name' => 'required|unique:trackingstatuses,status_name',
                'status_color' => 'required'
            ]);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $trackingStatus = new Trackingstatus();
            $trackingStatus->icon_name = $request->icon;
            $trackingStatus->status_color = $request->status_color;
            $trackingStatus->status_name  = $request->status_name;
            $maxOrder = DB::table('trackingstatuses')->where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->max('order');
            $trackingStatus->order = $maxOrder + 1;
            $trackingStatus->workspace  = getActiveWorkSpace();
            $trackingStatus->created_by  = creatorId();
            $trackingStatus->save();

            event(new Couriertrackingstatuscreate($trackingStatus, $request));

            return redirect()->back()->with('success', __('The tracking status has been created successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }



    public function edit(Request $request, $trackingStatusId)
    {
        if (Auth::user()->isAbleTo('tracking edit')) {
            $trackingStatusData = Trackingstatus::where('id', $trackingStatusId)->first();
            if ($trackingStatusData) {
                return view('courier-management::tracking_status.edit', compact('trackingStatusData'));
            } else {
                return redirect()->back()->with('error', __('Data Not Found !!!'));
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function update(Request $request, $trackingStatusId)
    {
        if (Auth::user()->isAbleTo('tracking edit')) {
            $validator = Validator::make($request->all(), [
                'icon' => 'required',
                'status_name' => 'required|unique:trackingstatuses,status_name',
                'status_color' => 'required'
            ]);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
            $trackingStatusData = Trackingstatus::where('id', $trackingStatusId)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->first();
            if ($trackingStatusData) {
                $trackingStatusData->icon_name = $request->icon;
                $trackingStatusData->status_color = $request->status_color;
                $trackingStatusData->status_name = $request->status_name;
                $trackingStatusData->save();
                event(new Couriertrackingstatusupdate($trackingStatusData, $request));

                return redirect()->back()->with('success', __('The tracking status details are updated successfully'));
            } else {
                return redirect()->back()->with('error', __('Data Not Found !!!'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Request $request, $trackingStatusId)
    {
        if (Auth::user()->isAbleTo('tracking delete')) {
            $trackingStatusData = Trackingstatus::where('id', $trackingStatusId)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->first();
            if ($trackingStatusData) {
                event(new Couriertrackingstatusdelete($trackingStatusData, $request));

                $trackingStatusData->delete();
                return redirect()->back()->with('success', __('The tracking status has been deleted'));
            } else {
                return redirect()->back()->with('error', __('Tracking Status Not Found !!!'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function orderUpdate(Request $request)
    {
        $post = $request->all();
        foreach ($post['order'] as $key => $item) {
            $stage        = Trackingstatus::where('id', '=', $item)->first();
            $stage->order = $key;
            $stage->save();
        }
        return response()->json(['success' => true, 'message' => 'Tracking Status Order Updated Successfully!']);

    }
}
