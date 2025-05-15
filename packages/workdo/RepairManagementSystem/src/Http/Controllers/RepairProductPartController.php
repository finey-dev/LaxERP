<?php

namespace Workdo\RepairManagementSystem\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;
use Workdo\RepairManagementSystem\Entities\RepairOrderRequest;
use Workdo\RepairManagementSystem\Entities\RepairPart;
use Workdo\RepairManagementSystem\Events\CreateRepairPart;
use Workdo\RepairManagementSystem\Events\DestroyRepairPart;
use Workdo\RepairManagementSystem\Events\UpdateRepairPart;

class RepairProductPartController extends Controller
{

    public function create($id)
    {
        if (module_is_active('ProductService')) {
            if (\Auth::user()->isAbleTo('repair part create')) {
                try {
                    $repair_id       = Crypt::decrypt($id);
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', __('Repair Order Request Not Found.'));
                }
                $repair_order_request = RepairOrderRequest::find($repair_id);
                if (module_is_active('ProductService')) {
                    $product_parts = \Workdo\ProductService\Entities\ProductService::where('workspace_id', getActiveWorkSpace())->where('type', 'parts')->get()->pluck('name', 'id');
                    $product_parts_count = $product_parts->count();
                }
                return view('repair-management-system::repair-part.create', compact('product_parts', 'repair_order_request', 'product_parts_count'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->route('repair.request.index')->with('error', __('Please Enable Product & Service Module'));
        }
    }

    public function store(Request $request)
    {
        if (module_is_active('ProductService')) {
            if (\Auth::user()->isAbleTo('repair part create')) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'items' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                $products = $request->items;

                for ($i = 0; $i < count($products); $i++) {
                    $repair_order_request                 = new RepairPart();
                    $repair_order_request->repair_id      = $request->repair_id;
                    $repair_order_request->product_id     = $products[$i]['item'];
                    $repair_order_request->quantity       = $products[$i]['quantity'];
                    $repair_order_request->tax            = $products[$i]['tax'];
                    $repair_order_request->discount       = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                    $repair_order_request->price          = $products[$i]['price'];
                    $repair_order_request->description    = str_replace(array('\'', '"', '`', '{', "\n"), ' ', $products[$i]['description']);

                    $repair_order_request->save();
                }
                event(new CreateRepairPart($request, $repair_order_request));

                return redirect()->route('repair.request.index')->with('success', __('The repair parts has been created successfully.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->route('repair.request.index')->with('error', __('Please Enable Product & Service Module'));
        }
    }

    public function product(Request $request)
    {
        $data['product']     = $product = \Workdo\ProductService\Entities\ProductService::find($request->product_id);
        $data['unit']        = !empty($product) ? ((!empty($product->unit())) ? $product->unit()->name : '') : '';
        $data['taxRate']     = $taxRate = !empty($product) ? (!empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0) : 0;
        $data['taxes']       =  !empty($product) ? (!empty($product->tax_id) ? $product->tax($product->tax_id) : 0) : 0;
        $salePrice           = !empty($product) ?  $product->sale_price : 0;
        $quantity            = 1;
        $taxPrice            = !empty($product) ? (($taxRate / 100) * ($salePrice * $quantity)) : 0;
        $data['totalAmount'] = !empty($product) ?  ($salePrice * $quantity) : 0;

        return json_encode($data);
    }

    public function show($id)
    {
        return view('repair-management-system::show');
    }

    public function edit($id,$type = null)
    {
        if (module_is_active('ProductService')) {
            if (\Auth::user()->isAbleTo('repair part create')) {
                try {
                    $repair_id       = Crypt::decrypt($id);
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', __('Repair Order Request Not Found.'));
                }
                $repair_order_request = RepairOrderRequest::find($repair_id);
                if (module_is_active('ProductService')) {
                    $product_parts = \Workdo\ProductService\Entities\ProductService::where('workspace_id', getActiveWorkSpace())->where('type', 'parts')->get()->pluck('name', 'id');
                    $product_parts_count = $product_parts->count();
                }
                $type = $type;
                return view('repair-management-system::repair-part.edit', compact('product_parts', 'repair_order_request', 'product_parts_count','type'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->route('repair.request.index')->with('error', __('Please Enable Product & Service Module'));
        }
    }

    public function update(Request $request, $id)
    {
        if (module_is_active('ProductService')) {
            if (\Auth::user()->isAbleTo('repair part edit')) {
                $repair_order_request = RepairOrderRequest::find($id);
                if ($repair_order_request->workspace == getActiveWorkSpace()) {
                    $validator = \Validator::make(
                        $request->all(),
                        [
                            'items' => 'required',
                        ]
                    );
                    if ($validator->fails()) {
                        $messages = $validator->getMessageBag();

                        return redirect()->route('repair.request.index')->with('error', $messages->first());
                    }
                    $products = $request->items;
                    for ($i = 0; $i < count($products); $i++) {
                        $repair_part = RepairPart::find($products[$i]['id']);

                        if ($repair_part == null) {
                            $repair_part             = new RepairPart();
                            $repair_part->repair_id  = $id;

                            RepairPart::total_quantity('minus', $products[$i]['quantity'], $products[$i]['item']);
                        } else {
                            RepairPart::total_quantity('plus', $repair_part->quantity, $repair_part->product_id);
                        }

                        if (isset($products[$i]['item'])) {
                            $repair_part->product_id = $products[$i]['item'];
                        }
                        $repair_part->quantity       = $products[$i]['quantity'];
                        $repair_part->tax            = $products[$i]['tax'];
                        $repair_part->discount       = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                        $repair_part->price          = $products[$i]['price'];
                        $repair_part->description    = str_replace(array('\'', '"', '`', '{', "\n"), ' ', $products[$i]['description']);
                        $repair_part->save();
                    }

                    // first parameter request second parameter RepairPart
                    event(new UpdateRepairPart($request, $repair_part));
                    if($request->type){
                        return redirect()->route('repair.request.invoice.show',[\Crypt::encrypt($request->type)])->with('success', __('Repair Parts successfully Updated.'));
                    }
                    return redirect()->route('repair.request.index')->with('success', __('The repair parts details are updated successfully.'));
                } else {
                    return redirect()->back()->with('error', __('Permission denied.'));
                }
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->route('repair.request.index')->with('error', __('Please Enable Product & Service Module'));
        }
    }

    public function parts(Request $request)
    {
        $parts = RepairPart::where('repair_id', $request->repair_id)->where('product_id', $request->product_id)->first();

        return json_encode($parts);
    }

    public function partDestroy(Request $request)
    {
        if (\Auth::user()->isAbleTo('repair part delete')) {
            $repair_part = RepairPart::where('id', '=', $request->id)->first();

            // first parameter request second parameter repair part
            event(new DestroyRepairPart($request, $repair_part));

            $repair_part->delete();

            return response()->json(['success' => __('The repair parts has been deleted.')]);
        } else {
            return response()->json(['error' => __('Permission denied.')]);
        }
    }
}
