<?php

namespace Workdo\CourierManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\CourierManagement\DataTables\CourierReturnDataTable;
use Workdo\CourierManagement\Entities\CourierPackageInfo;
use Workdo\CourierManagement\Entities\CourierReceiverDetails;
use Workdo\CourierManagement\Entities\CourierReturns;
use Workdo\CourierManagement\Entities\Servicetype;
use Workdo\CourierManagement\Events\CourierReturnscreate;
use Workdo\CourierManagement\Events\CourierReturnsdelete;
use Workdo\CourierManagement\Events\CourierReturnsupdate;

class CourierReturnsController extends Controller
{
    public function index(CourierReturnDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('courier returns manage')) {
            return $dataTable->render('courier-management::courier-returns.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function create()
    {
        if (Auth::user()->isAbleTo('courier returns create')) {
            $pacakges = CourierPackageInfo::where('workspace_id', getActiveWorkSpace())
                ->where('created_by', creatorId())
                ->whereHas('getTrackingStatus', function ($query) {
                    $query->where('status_name', 'Delivered');
                })->get();
                $customers = CourierReceiverDetails::where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->where('is_courier_delivered', 1)->get();
            return view('courier-management::courier-returns.create', compact('pacakges', 'customers'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('courier returns create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'package_id' => 'required',

                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $courier_returns                        = new CourierReturns();
            $courier_returns->package_id         = $request->package_id;
            $courier_returns->customer_id            = $request->customer_id;
            $courier_returns->return_date              = $request->return_date;
            $courier_returns->status     = $request->status;
            $courier_returns->return_reason         = $request->return_reason;
            $courier_returns->workspace             = getActiveWorkSpace();
            $courier_returns->created_by            = creatorId();
            $courier_returns->save();
            event(new CourierReturnscreate($courier_returns, $request));

            return redirect()->route('courier-returns.index')->with('success', __('The Courier Returns has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function show($id)
    {
        if (Auth::user()->isAbleTo('courier returns show')) {
            $courier_return = CourierReturns::with(['package', 'customer'])->find($id);
            return view('courier-management::courier-returns.show', compact('courier_return'));
        } else {
            return redirect()->back()->with(['error' => __('Permission denied.')], 401);
        }
    }
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('courier returns edit')) {
            $courier_returns = CourierReturns::find($id);
            if ($courier_returns->created_by == creatorId() && $courier_returns->workspace == getActiveWorkSpace()) {
                $pacakges = CourierPackageInfo::where('workspace_id', getActiveWorkSpace())
                ->where('created_by', creatorId())
                ->whereHas('getTrackingStatus', function ($query) {
                    $query->where('status_name', 'Delivered');
                })->get();
                $customers = CourierReceiverDetails::where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->where('is_courier_delivered', 1)->get();
                return view('courier-management::courier-returns.edit', compact('courier_returns', 'pacakges', 'customers'));
            } else {
                return response()->json(['error' => __('Permission denied.')]);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')]);
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('courier returns edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'package_id' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $courier_returns                        = CourierReturns::find($id);
            $courier_returns->package_id         = $request->package_id;
            $courier_returns->customer_id            = $request->customer_id;
            $courier_returns->return_date              = $request->return_date;
            $courier_returns->status     = $request->status;
            $courier_returns->return_reason     = $request->return_reason;
            $courier_returns->workspace             = getActiveWorkSpace();
            $courier_returns->created_by            = creatorId();
            $courier_returns->update();
            event(new CourierReturnsupdate($courier_returns, $request));

            return redirect()->route('courier-returns.index')->with('success', __('The Courier Returns has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function destroy(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('courier returns delete')) {
            $courier_returns = CourierReturns::find($id);
            if ($courier_returns->created_by == creatorId()  && $courier_returns->workspace == getActiveWorkSpace()) {
                event(new CourierReturnsdelete($courier_returns, $request));

                $courier_returns->delete();
                return redirect()->route('courier-returns.index')->with('success', __('The Courier Returns has been deleted successfully.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
