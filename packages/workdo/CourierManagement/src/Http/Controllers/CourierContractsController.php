<?php

namespace Workdo\CourierManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\CourierManagement\DataTables\CourierContractDataTable;
use Workdo\CourierManagement\Entities\CourierContracts;
use Workdo\CourierManagement\Entities\Servicetype;
use Workdo\CourierManagement\Events\CourierContractscreate;
use Workdo\CourierManagement\Events\CourierContractsdelete;
use Workdo\CourierManagement\Events\CourierContractsupdate;

class CourierContractsController extends Controller
{
    public function index(CourierContractDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('courier contracts manage')) {
            return $dataTable->render('courier-management::courier-contracts.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function create()
    {
        if (Auth::user()->isAbleTo('courier contracts create')) {
            $serviceType = Servicetype::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            return view('courier-management::courier-contracts.create', compact('serviceType'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('courier contracts create')) {
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
            $courier_contracts                        = new CourierContracts();
            $courier_contracts->customer_name         = $request->customer_name;
            $courier_contracts->service_type              = $request->service_type;
            $courier_contracts->start_date     = $request->start_date;
            $courier_contracts->end_date     = $request->end_date;
            $courier_contracts->contract_details     = $request->contract_details;
            $courier_contracts->status            = $request->status;
            $courier_contracts->workspace             = getActiveWorkSpace();
            $courier_contracts->created_by            = creatorId();
            $courier_contracts->save();
            event(new CourierContractscreate($courier_contracts, $request));

            return redirect()->route('courier-contracts.index')->with('success', __('The Courier Contracts has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function show($id)
    {
        if (Auth::user()->isAbleTo('courier contracts show')) {
            $courier_contract = CourierContracts::with('servicetype')->find($id);
            return view('courier-management::courier-contracts.show', compact('courier_contract'));
        } else {
            return redirect()->back()->with(['error' => __('Permission denied.')], 401);
        }
    }
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('courier contracts edit')) {
            $courier_contracts = CourierContracts::find($id);
            if ($courier_contracts->created_by == creatorId() && $courier_contracts->workspace == getActiveWorkSpace()) {
                $serviceType = Servicetype::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
                return view('courier-management::courier-contracts.edit', compact('courier_contracts', 'serviceType'));
            } else {
                return response()->json(['error' => __('Permission denied.')]);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')]);
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('courier contracts edit')) {
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
            $courier_contracts                        = CourierContracts::find($id);
            $courier_contracts->customer_name         = $request->customer_name;
            $courier_contracts->service_type              = $request->service_type;
            $courier_contracts->start_date     = $request->start_date;
            $courier_contracts->end_date     = $request->end_date;
            $courier_contracts->contract_details     = $request->contract_details;
            $courier_contracts->status            = $request->status;
            $courier_contracts->workspace             = getActiveWorkSpace();
            $courier_contracts->created_by            = creatorId();
            $courier_contracts->update();
            event(new CourierContractsupdate($courier_contracts, $request));

            return redirect()->route('courier-contracts.index')->with('success', __('The Courier Contracts has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function destroy(Request $request,$id)
    {
        if (Auth::user()->isAbleTo('courier contracts delete')) {
            $courier_contracts = CourierContracts::find($id);
            if ($courier_contracts->created_by == creatorId()  && $courier_contracts->workspace == getActiveWorkSpace()) {
                event(new CourierContractsdelete($courier_contracts, $request));

                $courier_contracts->delete();
                return redirect()->route('courier-contracts.index')->with('success', __('The Courier Contracts has been deleted successfully.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
