<?php

namespace Workdo\BeverageManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\BeverageManagement\Entities\BillMaterialItem;
use Workdo\BeverageManagement\Entities\BillOfMaterial;
use Workdo\BeverageManagement\Entities\CollectionCenter;
use Workdo\BeverageManagement\Entities\Manufacturing;
use Workdo\BeverageManagement\Entities\Packaging;
use Workdo\BeverageManagement\Entities\PackagingItems;
use Workdo\BeverageManagement\Entities\RawMaterial;
use Workdo\BeverageManagement\Events\CreateBillItemMaterial;
use Workdo\BeverageManagement\Events\CreateBillOfMaterial;
use Workdo\BeverageManagement\Events\DestroyBillItemMaterial;
use Workdo\BeverageManagement\Events\DestroyBillOfMaterial;
use Workdo\BeverageManagement\Events\DestroyManufacturing;
use Workdo\BeverageManagement\Events\DestroyPackaging;
use Workdo\BeverageManagement\Events\DestroyPackagingItem;
use Workdo\BeverageManagement\Events\UpdateBillItemMaterial;
use Workdo\BeverageManagement\Events\UpdateBillOfMaterial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Workdo\BeverageManagement\DataTables\BillOfMaterialDataTable;

class BillOfMaterialController extends Controller
{

    public function index(BillOfMaterialDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('bill of material manage')) {
            return $dataTable->render('beverage-management::bill-of-material.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->isAbleTo('bill of material create')) {
            $collection_centers = CollectionCenter::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('status', 1)->pluck('location_name', 'id');
            if (module_is_active('ProductService')) {
                $raw_material = RawMaterial::where('workspace', getActiveWorkSpace())->get('item_id')->toArray();
                $products =  \Workdo\ProductService\Entities\ProductService::whereIn('id', $raw_material)->where('type', 'product')->get();
                $product_count = $products->count();
            } else {
                return response()->json(['error' => __('Please Enable Product & Service Module.')], 401);
            }
            return view('beverage-management::bill-of-material.create', compact('collection_centers', 'products', 'product_count'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function centerWiseRawMaterial(Request $request)
    {
        try {
            $action = $request->action;
            $bill_material_id = $request->bill_material_id;
            $raw_material = RawMaterial::where('workspace', getActiveWorkSpace())->where('collection_center_id', $request->collection_center_id)->get('item_id')->toArray();
            $parts =  \Workdo\ProductService\Entities\ProductService::whereIn('id', $raw_material)->where('type', 'parts')->get('id')->toArray();
            $raw_materials = RawMaterial::with('productService')->whereIn('item_id', $parts)->get();
            $bill_material_item_summary = [];
            if ($action == 'edit') {
                $bill_material_item_summary = BillMaterialItem::where('bill_of_material_id', $request->bill_material_id)->get();
            }
            $returnHTML = view('beverage-management::bill-of-material.section', compact('raw_materials', 'action', 'bill_material_item_summary', 'bill_material_id'))->render();
            $response = [
                'is_success' => true,
                'message' => '',
                'html' => $returnHTML,
            ];
            return response()->json($response);
        } catch (\Exception $e) {
            Log::info(['centerWiseRawMaterial', $e]);
        }
    }

    public function getRawMaterial(Request $request)
    {
        try {
            $raw_material = RawMaterial::find($request->raw_material_id);
            if ($raw_material) {
                $data['product'] = $product = \Workdo\ProductService\Entities\ProductService::find($raw_material->item_id);
                $data['unit']        = !empty($product) ? ((!empty($product->unit())) ? $product->unit()->name : '') : '';
                $data['taxRate']     = $taxRate = !empty($product) ? (!empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0) : 0;
                $data['taxes']       =  !empty($product) ? (!empty($product->tax_id) ? $product->tax($product->tax_id) : 0) : 0;
                $salePrice           = !empty($product) ?  $product->sale_price : 0;
                $quantity            = 1;
                $taxPrice            = !empty($product) ? (($taxRate / 100) * ($salePrice * $quantity)) : 0;
                $data['totalAmount'] = !empty($product) ?  ($salePrice * $quantity) : 0;
                return response()->json($data);
            } else {
                return response()->json(['error' => __('Raw Material not found.')], 404);
            }
        } catch (\Exception $e) {
            \Log::info(['getRawMaterial', $e]);
        }
    }

    public function items(Request $request)
    {
        $items = BillMaterialItem::where('bill_of_material_id', $request->bill_material_id)->where('raw_material_id', $request->raw_material_id)->first();

        return json_encode($items);
    }

    public function deleteRawMaterial(Request $request)
    {
        try {
            $bill_material_item = BillMaterialItem::find($request->bill_material_item_id);
            if ($bill_material_item) {
                $bill_material_item->delete();
                return response()->json(['success' => __('Raw material deleted successfully.')]);
            } else {
                return response()->json(['error' => __('Bill of Material not found.')], 404);
            }
        } catch (\Exception $e) {
            \Log::info('deleteRawMaterial', $e);
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('bill of material create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'item_id' => 'required',
                    'collection_center_id' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $bill_of_material                        = new BillOfMaterial();
            $bill_of_material->item_id               = $request->item_id;
            $bill_of_material->collection_center_id  = $request->collection_center_id;
            $bill_of_material->quantity              = $request->quantity;
            $bill_of_material->status                = 0;
            $bill_of_material->workspace             = getActiveWorkSpace();
            $bill_of_material->created_by            = creatorId();
            $bill_of_material->save();
            event(new CreateBillOfMaterial($request, $bill_of_material));

            if ($request->items) {
                foreach ($request->items as $value) {
                    if ($value['quantity'] == 0) {
                        return redirect()->back()->with(['error' => __('Bill of material not created with raw material quantity 0.')], 401);
                    }
                    $raw_material = RawMaterial::find($value['raw_material_id']);
                    if ($raw_material) {
                        if ($raw_material->quantity >= $value['quantity']) {
                            $bill_material_item = new BillMaterialItem();
                            $bill_material_item->bill_of_material_id = $bill_of_material->id;
                            $bill_material_item->raw_material_id = $value['raw_material_id'];
                            $bill_material_item->quantity = $value['quantity'];
                            $bill_material_item->unit = $value['unit'];
                            $bill_material_item->tax = $value['tax'];
                            $bill_material_item->price = $value['price'];
                            $bill_material_item->sub_total = $value['total_amount'];
                            $bill_material_item->workspace = getActiveWorkSpace();
                            $bill_material_item->created_by = creatorId();
                            $bill_material_item->save();
                            event(new CreateBillItemMaterial($request, $bill_material_item));
                        } else {
                            return redirect()->back()->with((['error' => 'Quantity not available in collection center.']));
                        }
                    } else {
                        return response()->json(['error' => __('Raw Material not found.')], 404);
                    }
                }
            }
            return redirect()->route('bill-of-material.index')->with('success', __('Bill Of Material successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function show($id)
    {
        if (Auth::user()->isAbleTo('bill of material show')) {
            $bill_of_material = BillOfMaterial::find($id);
            if ($bill_of_material) {
                $bill_material_items = BillMaterialItem::with('rawMaterial')->where('bill_of_material_id', $id)->get();
                return view('beverage-management::bill-of-material.show', compact('bill_of_material', 'bill_material_items'));
            } else {
                return response()->json(['error' => __('Bill of Material not found.')], 404);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function edit($id)
    {
        if (Auth::user()->isAbleTo('bill of material edit')) {
            $collection_centers = CollectionCenter::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('status', 1)->pluck('location_name', 'id');
            $bill_of_material = BillOfMaterial::find($id);
            if ($bill_of_material) {
                if ($bill_of_material->created_by == creatorId() && $bill_of_material->workspace == getActiveWorkSpace()) {
                    $bill_material_item = BillMaterialItem::where('bill_of_material_id', $id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
                    if (module_is_active('ProductService')) {
                        $raw_material = RawMaterial::where('workspace', getActiveWorkSpace())->get('item_id')->toArray();
                        $raw_materials =  \Workdo\ProductService\Entities\ProductService::whereIn('id', $raw_material)->where('type', 'parts')->get();

                        $products =  \Workdo\ProductService\Entities\ProductService::whereIn('id', $raw_material)->where('type', 'product')->get();
                        $product_count = $products->count();
                    } else {
                        return redirect()->back()->with('error', __('Please Enable Product & Service Module.'));
                    }
                    return view('beverage-management::bill-of-material.edit', compact('bill_material_item', 'bill_of_material', 'raw_materials', 'collection_centers', 'products', 'product_count'));
                } else {
                    return response()->json(['error' => __('Permission denied.')], 401);
                }
            } else {
                return response()->json(['error' => __('Bill of Material not found.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('bill of material edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'item_id' => 'required',
                    'collection_center_id' => 'required',
                    'main_quantity' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            if (empty($request['items'])) {
                return redirect()->back()->with('error', 'Bill of material not create. Please sleect atleast one raw material.');
            }

            $bill_of_material                = BillOfMaterial::find($id);
            $bill_of_material->item_id  = $request->item_id;
            $bill_of_material->collection_center_id  = $request->collection_center_id;
            $bill_of_material->quantity      = $request->main_quantity;
            $bill_of_material->status        = 0;
            $bill_of_material->workspace     = getActiveWorkSpace();
            $bill_of_material->created_by    = creatorId();
            $bill_of_material->update();
            event(new UpdateBillOfMaterial($request, $bill_of_material));
            foreach ($request['items'] as $key => $value) {
                if ($value['bill_material_item_id']) {

                    $bill_material_item = BillMaterialItem::where('bill_of_material_id', $id)
                        ->where('id', $value['bill_material_item_id'])
                        ->first();

                    if ($bill_material_item) {
                        $bill_material_item->bill_of_material_id = $bill_of_material->id;
                        $bill_material_item->raw_material_id = $value['raw_material_id'];
                        $bill_material_item->quantity = $value['quantity'];
                        $bill_material_item->unit = $value['unit'];
                        $bill_material_item->tax = $value['tax'];
                        $bill_material_item->price = $value['price'];
                        $bill_material_item->sub_total = $value['total_amount'];
                        $bill_material_item->workspace = getActiveWorkSpace();
                        $bill_material_item->created_by = creatorId();
                        $bill_material_item->save();

                        event(new UpdateBillItemMaterial($request, $bill_material_item));
                    }
                } else {
                    $bill_material_item = new BillMaterialItem();
                    $bill_material_item->bill_of_material_id = $bill_of_material->id;
                    $bill_material_item->raw_material_id = $value['raw_material_id'];
                    $bill_material_item->quantity = $value['quantity'];
                    $bill_material_item->unit = $value['unit'];
                    $bill_material_item->tax = $value['tax'];
                    $bill_material_item->price = $value['price'];
                    $bill_material_item->sub_total = $value['total_amount'];
                    $bill_material_item->workspace = getActiveWorkSpace();
                    $bill_material_item->created_by = creatorId();
                    $bill_material_item->save();
                    event(new CreateBillItemMaterial($request, $bill_material_item));
                }
            }
            return redirect()->route('bill-of-material.index')->with('success', __('Bill Of Material successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('bill of material delete')) {
            $bill_of_material  = BillOfMaterial::find($id);
            if($bill_of_material){

                $bill_material_items = $bill_of_material ? BillMaterialItem::where('bill_of_material_id', $id)->get() : '';
                $manufacturing = $bill_of_material ? Manufacturing::where('bill_of_material_id', $bill_of_material->id)->first() : '';
                $packaging = $manufacturing ? Packaging::where('manufacturing_id', $manufacturing->id)->first() : '';
                $packaging_items = $packaging ? PackagingItems::where('packaging_id', $packaging->id)->get() : '';


                if (($bill_of_material->created_by == creatorId() && $bill_of_material->workspace == getActiveWorkSpace()) || ($manufacturing->created_by == creatorId() && $manufacturing->workspace == getActiveWorkSpace()) || ($packaging->created_by == creatorId() && $packaging->workspace == getActiveWorkSpace())) {

                    event(new DestroyBillOfMaterial($bill_of_material));
                    $bill_of_material->delete();
                    if ($bill_material_items) {
                        foreach ($bill_material_items as $bill_material_item) {
                            event(new DestroyBillItemMaterial($bill_material_item));
                            $bill_material_item->delete();
                        }
                    }
                    if ($manufacturing) {
                        event(new DestroyManufacturing($manufacturing));
                        $manufacturing->delete();
                    }
                    if ($packaging) {
                        event(new DestroyPackaging($packaging));
                        $packaging->delete();
                    }
                    if ($packaging_items) {
                        foreach ($packaging_items as $packaging_item) {
                            event(new DestroyPackagingItem($packaging_item));
                            $packaging_item->delete();
                        }
                    }
                    return redirect()->route('bill-of-material.index')->with('success', __("Bill Of Material and related all data Successfully Deleted."));
                } else {
                    return response()->json(['error' => __('Permission denied.')], 401);
                }
            }else{
                return response()->json(['error' => __('Bill Of Material not found.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
}
