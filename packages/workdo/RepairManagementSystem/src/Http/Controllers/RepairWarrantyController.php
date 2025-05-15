<?php

namespace Workdo\RepairManagementSystem\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\RepairManagementSystem\DataTables\RepairWarrantyDataTable;
use Illuminate\Support\Facades\Auth;
use Workdo\RepairManagementSystem\Entities\RepairWarranty;
use Workdo\RepairManagementSystem\Entities\RepairOrderRequest;
use Workdo\ProductService\Entities\ProductService;
use Workdo\RepairManagementSystem\Entities\RepairPart;
use Workdo\RepairManagementSystem\Events\CreateRepairWarranty;
use Workdo\RepairManagementSystem\Events\UpdateRepairWarranty;
use Workdo\RepairManagementSystem\Events\DestroyRepairWarranty;
class RepairWarrantyController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(RepairWarrantyDataTable $datatable)
    {
        if (Auth::user()->isAbleTo('warranty manage')) {
            return $datatable->render('repair-management-system::repair-warranty.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if (Auth::user()->isAbleTo('warranty create')) {
            $repair_orders = RepairOrderRequest::where('workspace', getActiveWorkSpace())
                ->where('created_by', creatorId())
                ->get()
                ->pluck('product_name', 'id');

            $repair_parts = collect();

            return view('repair-management-system::repair-warranty.create', compact('repair_orders', 'repair_parts'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function getRepairParts(Request $request)
    {
        $repair_order_id = $request->repair_order_id;

        $repair_parts_ids = RepairPart::where('repair_id', $repair_order_id)
            ->pluck('product_id');

        if ($repair_parts_ids->isNotEmpty()) {
            $repair_parts = ProductService::whereIn('id', $repair_parts_ids->toArray())
                ->pluck('name', 'id');
        } else {
            $repair_parts = collect();
        }

        return response()->json($repair_parts);
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('warranty create')) {
            $validator = \Validator::make(
                $request->all(),
                [

                    'repair_order_id' => 'required',
                    'part_id' => 'required',
                    'warranty_number' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required|date|after_or_equal:start_date',
                    'warranty_terms' => 'required',
                    'claim_status' => 'required',

                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $repair_warranty                    = new RepairWarranty();
            $repair_warranty->repair_order_id   = $request->repair_order_id;
            $repair_warranty->part_id           = $request->part_id;
            $repair_warranty->warranty_number   = $request->warranty_number;
            $repair_warranty->start_date        = $request->start_date;
            $repair_warranty->end_date          = $request->end_date;
            $repair_warranty->warranty_terms    = $request->warranty_terms;
            $repair_warranty->claim_status      = $request->claim_status;
            $repair_warranty->workspace         = getActiveWorkSpace();
            $repair_warranty->created_by        = creatorId();
            $repair_warranty->save();

            event(new CreateRepairWarranty($request, $repair_warranty));

            return redirect()->route('repair-warranty.index')->with('success', __('The repair warranty has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if (Auth::user()->isAbleTo('warranty edit')) {
            $repair_warranty = RepairWarranty::find($id);
            $repair_orders = RepairOrderRequest::where('workspace', getActiveWorkSpace())
            ->where('created_by', creatorId())
            ->get()
            ->pluck('product_name', 'id');
            $repair_order_id = $repair_orders->keys();

            $repair_parts_ids = RepairPart::whereIn('repair_id', $repair_order_id->toArray()) // Use whereIn with multiple IDs
            ->pluck('product_id');

            if ($repair_parts_ids->isNotEmpty()) {
                $repair_parts = ProductService::whereIn('id', $repair_parts_ids->toArray())
                    ->pluck('name', 'id');
            } else {
                $repair_parts = collect();
            }
            return view('repair-management-system::repair-warranty.edit', compact('repair_warranty','repair_orders','repair_parts'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('warranty edit')) {
            $repair_warranty  = RepairWarranty::find($id);

                $validator = \Validator::make(
                    $request->all(),
                    [
                        'repair_order_id' => 'required',
                        'part_id' => 'required',
                        'warranty_number' => 'required',
                        'start_date' => 'required',
                        'end_date' => 'required',
                        'warranty_terms' => 'required',
                        'claim_status' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }


                $repair_warranty->repair_order_id   = $request->repair_order_id;
                $repair_warranty->part_id           = $request->part_id;
                $repair_warranty->warranty_number   = $request->warranty_number;
                $repair_warranty->start_date        = $request->start_date;
                $repair_warranty->end_date          = $request->end_date;
                $repair_warranty->warranty_terms    = $request->warranty_terms;
                $repair_warranty->claim_status      = $request->claim_status;
                $repair_warranty->workspace         = getActiveWorkSpace();
                $repair_warranty->created_by        = creatorId();
                $repair_warranty->save();

                event(new UpdateRepairWarranty($request, $repair_warranty));

                return redirect()->route('repair-warranty.index')->with('success', __('The repair warranty details are updated successfully.'));

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('warranty delete')) {

            $repair_warranty = RepairWarranty::find($id);

            event(new DestroyRepairWarranty($repair_warranty));

            $repair_warranty->delete();

            return redirect()->back()->with('success', __('The repair warranty has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function viewcontent($id)
    {
        $repair_warranty = RepairWarranty::find($id);
        return view('repair-management-system::repair-warranty.warranty_terms', compact('repair_warranty'));
    }
}
