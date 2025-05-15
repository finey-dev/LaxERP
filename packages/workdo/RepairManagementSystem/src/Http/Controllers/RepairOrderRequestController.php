<?php

namespace Workdo\RepairManagementSystem\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\RepairManagementSystem\DataTables\RepairOrderRequestDataTable;
use Workdo\RepairManagementSystem\Entities\RepairInvoice;
use Workdo\RepairManagementSystem\Entities\RepairInvoicePayment;
use Workdo\RepairManagementSystem\Entities\RepairTechnician;
use Workdo\RepairManagementSystem\Entities\RepairMovementHistory;
use Workdo\RepairManagementSystem\Entities\RepairOrderRequest;
use Workdo\RepairManagementSystem\Entities\RepairPart;
use Workdo\RepairManagementSystem\Events\CreateRepairOrderRequest;
use Workdo\RepairManagementSystem\Events\DestroyRepairInvoice;
use Workdo\RepairManagementSystem\Events\DestroyRepairOrderRequest;
use Workdo\RepairManagementSystem\Events\UpdateRepairOrderRequest;

class RepairOrderRequestController extends Controller
{

    public function index(RepairOrderRequestDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('repair order request manage')) {
            return $dataTable->render('repair-management-system::repair-order-request.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->isAbleTo('repair order request create')) {
            $repair_technicians = RepairTechnician::where('workspace', getActiveWorkSpace())
            ->where('created_by', creatorId())
            ->get()
            ->pluck('name', 'id');
            return view('repair-management-system::repair-order-request.create',compact('repair_technicians'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->isAbleTo('repair order request create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'product_name' => 'required',
                    'product_quantity' => 'required',
                    'customer_name' => 'required',
                    'customer_email' => 'required',
                    'customer_mobile_no' => 'required',
                    'date' => 'required',
                    'expiry_date' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $repair_order_request                     = new RepairOrderRequest();
            $repair_order_request->product_name       = $request->product_name;
            $repair_order_request->product_quantity   = $request->product_quantity;
            $repair_order_request->customer_name      = $request->customer_name;
            $repair_order_request->customer_email     = $request->customer_email;
            $repair_order_request->customer_mobile_no = $request->customer_mobile_no;
            $repair_order_request->date               = $request->date;
            $repair_order_request->expiry_date        = $request->expiry_date;
            $repair_order_request->repair_technician  = $request->repair_technician;
            $repair_order_request->location           = 'Main Location';
            $repair_order_request->workspace          = getActiveWorkSpace();
            $repair_order_request->created_by         = creatorId();
            $repair_order_request->save();

            event(new CreateRepairOrderRequest($request, $repair_order_request));

            return redirect()->route('repair.request.index')->with('success', __('The repair order request created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id)
    {
        return view('repair-management-system::show');
    }

    public function edit($id)
    {
        if (Auth::user()->isAbleTo('repair order request edit')) {
            $repair_order_request = RepairOrderRequest::find($id);
            $repair_technicians = RepairTechnician::where('workspace', getActiveWorkSpace())
            ->where('created_by', creatorId())
            ->get()
            ->pluck('name', 'id');
            if ($repair_order_request->created_by == creatorId() && $repair_order_request->workspace == getActiveWorkSpace()) {
                return view('repair-management-system::repair-order-request.edit', compact('repair_order_request','repair_technicians'));
            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->isAbleTo('repair order request edit')) {
            $repair_order_request  = RepairOrderRequest::find($id);
            if ($repair_order_request->created_by == creatorId()  && $repair_order_request->workspace == getActiveWorkSpace()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'product_name' => 'required',
                        'product_quantity' => 'required',
                        'customer_name' => 'required',
                        'customer_email' => 'required',
                        'customer_mobile_no' => 'required',
                        'date' => 'required',
                        'expiry_date' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }


                $repair_order_request->product_name             = $request->product_name;
                $repair_order_request->product_quantity         = $request->product_quantity;
                $repair_order_request->customer_name            = $request->customer_name;
                $repair_order_request->customer_email           = $request->customer_email;
                $repair_order_request->customer_mobile_no       = $request->customer_mobile_no;
                $repair_order_request->date                     = $request->date;
                $repair_order_request->expiry_date              = $request->expiry_date;
                $repair_order_request->repair_technician        = $request->repair_technician;
                $repair_order_request->workspace                = getActiveWorkSpace();
                $repair_order_request->created_by               = creatorId();
                $repair_order_request->save();

                event(new UpdateRepairOrderRequest($request, $repair_order_request));

                return redirect()->route('repair.request.index')->with('success', __('The repair order request details are updated successfully.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (\Auth::user()->isAbleTo('repair order request edit')) {
            $repair_order_request  = RepairOrderRequest::find($id);
            $repair_parts = RepairPart::where('repair_id', $id)->get();
            $repair_invoice = RepairInvoice::where('repair_id', $id)->first();
            if ($repair_invoice) {
                $repair_invoice_payments = RepairInvoicePayment::where('invoice_id', $repair_invoice->id)->get();
            }
            if (($repair_order_request->created_by == creatorId()  && $repair_order_request->workspace == getActiveWorkSpace()) || ($repair_invoice->created_by == creatorId() && $repair_invoice->workspace == getActiveWorkSpace())) {
                // Delete related records using relationships
                if ($repair_order_request) {
                    foreach ($repair_parts as $repair_part) {
                        $repair_part->delete();
                    }
                }
                if ($repair_invoice) {
                    event(new DestroyRepairInvoice($repair_invoice));
                    $repair_invoice->delete();
                    foreach ($repair_invoice_payments as $repair_invoice_payment) {
                        $repair_invoice_payment->delete();
                    }
                }
                event(new DestroyRepairOrderRequest($repair_order_request));
                $repair_order_request->delete();
                return redirect()->route('repair.request.index')->with('success', __('The repair order request has been deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function repairOrderStepsChange($id, $response)
    {
        try {
            $repair_order_request    = RepairOrderRequest::findOrFail($id);
            $repair_order_request->status = $response;
            if ($response == 1) {
                RepairMovementHistory::movementHistoryStore($id, 'Main Location', 'Workshop Location', 'Repair');
                $repair_order_request->location = 'Workshop Location';
                $msg = 'Repair Started!';
            } elseif ($response == 2) {
                RepairMovementHistory::movementHistoryStore($id, 'Workshop Location', 'Waiting For Testing Location', 'Repair');
                $repair_order_request->location = 'Waiting For Testing Location';
                $msg = 'Repair Ended!';
            } elseif ($response == 3) {
                RepairMovementHistory::movementHistoryStore($id, 'Waiting For Testing Location', 'Testing Location', 'Testing');
                $repair_order_request->location = 'Testing Location';
                $msg = 'Testing Started!';
            } elseif ($response == 4) {
                RepairMovementHistory::movementHistoryStore($id, 'Testing Location', 'Finish Location', 'Testing');
                $repair_order_request->location = 'Finish Location';
                $msg = 'Testing Ended!';
            } elseif ($response == 5) {
                RepairMovementHistory::movementHistoryStore($id, $repair_order_request->location, 'Irrepairable Location', 'Fail');
                $repair_order_request->location = 'Irrepairable Location';
                $msg = 'Product Irrepairabled';
            } elseif ($response == 6) {
                RepairMovementHistory::movementHistoryStore($id, $repair_order_request->location, 'Cancel Location', 'Cancel');
                $repair_order_request->location = 'Cancel Location';
                $msg = 'Product Cancelled';
            }
            $repair_order_request->save();
            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }
}
