<?php

namespace Workdo\BeverageManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\BeverageManagement\Entities\CollectionCenter;
use Workdo\BeverageManagement\Entities\Manufacturing;
use Workdo\BeverageManagement\Entities\Packaging;
use Workdo\BeverageManagement\Entities\PackagingItems;
use Workdo\BeverageManagement\Entities\RawMaterial;
use Workdo\BeverageManagement\Events\CreatePackaging;
use Workdo\BeverageManagement\Events\CreatePackagingItem;
use Workdo\BeverageManagement\Events\DestroyPackaging;
use Workdo\BeverageManagement\Events\DestroyPackagingItem;
use Workdo\BeverageManagement\Events\UpdatePackaging;
use Workdo\BeverageManagement\Events\UpdatePackagingItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Workdo\BeverageManagement\DataTables\PackagingDataTable;
use Workdo\BeverageManagement\Entities\CollectionCenterStock;
use Workdo\ProductService\Entities\ProductService;

class PackagingController extends Controller
{

    public function index(PackagingDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('packaging manage')) {
            return $dataTable->render('beverage-management::packaging.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->isAbleTo('packaging create')) {
            $manufacturings = Manufacturing::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('status', 1)->get();
            $raw_materials = RawMaterial::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('status', 1)->get();
            $collection_centers = CollectionCenter::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('status', 1)->pluck('location_name', 'id');
            return view('beverage-management::packaging.create', compact('raw_materials', 'manufacturings', 'collection_centers'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function centerWiseRawMaterial(Request $request)
    {
        try {
            $action = $request->action;
            $packaging_id = $request->packaging_id;
            $raw_material = RawMaterial::where('workspace', getActiveWorkSpace())->where('collection_center_id', $request->collection_center_id)->get('item_id')->toArray();
            $parts =  \Workdo\ProductService\Entities\ProductService::whereIn('id', $raw_material)->where('type', 'parts')->get('id')->toArray();
            $raw_materials = RawMaterial::with('productService')->whereIn('item_id', $parts)->get();
            $packaging_item_summary = [];
            if ($action == 'edit') {
                $packaging_item_summary = PackagingItems::where('packaging_id', $packaging_id)->get();
            }
            $returnHTML = view('beverage-management::packaging.section', compact('raw_materials', 'action', 'packaging_item_summary', 'packaging_id'))->render();
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
            $data['product'] = $product = \Workdo\ProductService\Entities\ProductService::find($raw_material->item_id);
            $data['unit']        = !empty($product) ? ((!empty($product->unit())) ? $product->unit()->name : '') : '';
            $data['taxRate']     = $taxRate = !empty($product) ? (!empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0) : 0;
            $data['taxes']       =  !empty($product) ? (!empty($product->tax_id) ? $product->tax($product->tax_id) : 0) : 0;
            $salePrice           = !empty($product) ?  $product->sale_price : 0;
            $quantity            = 1;
            $taxPrice            = !empty($product) ? (($taxRate / 100) * ($salePrice * $quantity)) : 0;
            $data['totalAmount'] = !empty($product) ?  ($salePrice * $quantity) : 0;
            return response()->json($data);
        } catch (\Exception $e) {
            \Log::info(['getRawMaterial', $e]);
        }
    }

    public function items(Request $request)
    {
        $items = PackagingItems::where('packaging_id', $request->packaging_id)->where('raw_material_id', $request->raw_material_id)->first();

        return json_encode($items);
    }


    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('packaging create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'manufacturing_id' => 'required|unique:packagings,manufacturing_id',
                    'collection_center_id' => 'required',
                ],
                [
                    'manufacturing_id.required' => 'Manufacturing is required.',
                    'manufacturing_id.unique' => 'This Manufacturing package already exists.',
                    'collection_center_id.required' => 'Collection Center is required.',
                ]
            );


            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $packaging                = new Packaging();
            $packaging->manufacturing_id  = $request->manufacturing_id;
            $packaging->collection_center_id  = $request->collection_center_id;
            $packaging->total         = $request->final_amount;
            $packaging->status        = 0;
            $packaging->workspace     = getActiveWorkSpace();
            $packaging->created_by    = creatorId();
            $packaging->save();
            event(new CreatePackaging($request, $packaging));

            if ($request->items) {
                foreach ($request->items as $value) {
                    if ($value['quantity'] == 0) {
                        return redirect()->back()->with(['error' => __('Bill of material not created with raw material quantity 0.')], 401);
                    }
                    $raw_material = RawMaterial::find($value['raw_material_id']);
                    if ($raw_material->quantity >= $value['quantity']) {
                        // Create a new instance of the PackagingItems model
                        $packaging_item = new PackagingItems();
                        $packaging_item->packaging_id = $packaging->id;
                        $packaging_item->raw_material_id = $value['raw_material_id'];
                        $packaging_item->quantity = $value['quantity'];
                        $packaging_item->unit = $value['unit'];
                        $packaging_item->price = $value['price'];
                        $packaging_item->sub_total = $value['total_amount'];
                        $packaging_item->workspace = getActiveWorkSpace();
                        $packaging_item->created_by = creatorId();
                        $packaging_item->save();
                        event(new CreatePackagingItem($request, $packaging_item));
                    } else {
                        return redirect()->back()->with((['error' => 'Quantity not available in collection center.']));
                    }
                }
            }
            return redirect()->route('packaging.index')->with('success', __('Packaging successfully created!'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function show($id)
    {
        if (Auth::user()->isAbleTo('packaging show')) {
            $packaging = Packaging::with('manufacture')->find($id);
            if ($packaging) {
                $manufacture = Manufacturing::with('billOfMaterial')->find($packaging->manufacturing_id);
                $packaging_items = PackagingItems::with('rawMaterial')->where('packaging_id', $packaging->id)->get();
                $company_settings = getCompanyAllSetting($packaging->created_by, $packaging->workspace);
                return view('beverage-management::packaging.show', compact('packaging', 'packaging_items', 'manufacture', 'company_settings'));
            } else {
                return redirect()->back()->with('error', __('Packaging not found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function edit($id)
    {
        if (Auth::user()->isAbleTo('packaging edit')) {
            $packaging = Packaging::find($id);
            if ($packaging->created_by == creatorId() && $packaging->workspace == getActiveWorkSpace()) {
                $packaging_items = PackagingItems::with('packaging')->where('packaging_id', $id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
                $manufacturings = Manufacturing::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('status', 1)->get();
                $raw_materials = RawMaterial::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('status', 1)->get();
                $collection_centers = CollectionCenter::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('status', 1)->pluck('location_name', 'id');
                return view('beverage-management::packaging.edit', compact('packaging_items', 'packaging', 'raw_materials', 'manufacturings', 'collection_centers'));
            } else {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('packaging edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'manufacturing_id' => 'required|unique:packagings,manufacturing_id,' . $id,
                    'collection_center_id' => 'required',
                ],
                [
                    'manufacturing_id.required' => 'Manufacturing is required.',
                    'manufacturing_id.unique' => 'This Manufacturing package already exists.',
                    'collection_center_id.required' => 'Collection Center is required.',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }


            if (empty($request['items'])) {
                return redirect()->back()->with('error', 'Packaging not create. Please sleect atleast one raw material.');
            }

            $packaging                = Packaging::find($id);
            $packaging->manufacturing_id  = $request->manufacturing_id;
            $packaging->collection_center_id  = $request->collection_center_id;
            $packaging->total         = $request->final_amount;
            $packaging->status        = 0;
            $packaging->workspace     = getActiveWorkSpace();
            $packaging->created_by    = creatorId();
            $packaging->save();
            event(new UpdatePackaging($request, $packaging));

            foreach ($request['items'] as $key => $value) {
                if ($value['packaging_item_id']) {

                    $packaging_item = PackagingItems::where('packaging_id', $id)
                        ->where('id', $value['packaging_item_id'])
                        ->first();

                    if ($value['quantity'] == 0) {
                        return redirect()->back()->with(['error' => __('Bill of material not created with raw material quantity 0.')], 401);
                    }
                    $raw_material = RawMaterial::find($value['raw_material_id']);

                    if ($raw_material->quantity >= $value['quantity']) {

                        if ($packaging_item) {
                            // Update the existing item
                            $packaging_item->quantity = $value['quantity'];
                            $packaging_item->unit = $value['unit'];
                            $packaging_item->price = $value['price'];
                            $packaging_item->sub_total = $value['total_amount'];
                        } else {
                            // Create a new item if it doesn't exist
                            $packaging_item = new PackagingItems();
                            $packaging_item->packaging_id = $id;
                            $packaging_item->raw_material_id = $value['raw_material_id'];
                            $packaging_item->quantity = $value['quantity'];
                            $packaging_item->unit = $value['unit'];
                            $packaging_item->price = $value['price'];
                            $packaging_item->sub_total = $value['total_amount'];
                        }

                        $packaging_item->workspace = getActiveWorkSpace();
                        $packaging_item->created_by = creatorId();
                        $packaging_item->save();

                        event(new UpdatePackagingItem($request, $packaging_item));
                    } else {

                        return redirect()->back()->with(['error' => __('Insufficient quantity of raw material available.')]);
                    }
                } else {
                    return redirect()->back()->with((['error' => 'Quantity not available in collection center.']));
                }
            }
            return redirect()->route('packaging.index')->with('success', __('Packaging successfully updated!'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('packaging delete')) {
            $packaging  = Packaging::find($id);
            if($packaging){
                $Packaging_item = PackagingItems::where('packaging_id', $id)->get();
    
                if ($packaging->created_by == creatorId() && $packaging->workspace == getActiveWorkSpace()) {
                    event(new DestroyPackaging($packaging));
                    $packaging->delete();
                    if ($Packaging_item) {
                        foreach ($Packaging_item as $packaging) {
                            event(new DestroyPackagingItem($packaging));
                            $packaging->delete();
                        }
                    }
                    return redirect()->route('packaging.index')->with('success', __("Packaging Successfully Deleted."));
                } else {
                    return response()->json(['error' => __('Permission Denied.')], 401);
                }
            }else{
                return response()->json(['error' => __('Packaging not found.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function statusCompleted($id)
    {
        if (Auth::user()->isAbleTo('packaging status')) {
            $packaging = Packaging::find($id);

            if (!$packaging) {
                return redirect()->route('packaging.index')->with('error', 'Packaging not found.');
            }

            $packaging_items = PackagingItems::where('packaging_id', $id)->get();

            $manufacturing = Manufacturing::find($packaging->manufacturing_id);

            if (!$manufacturing) {
                return redirect()->route('packaging.index')->with('error', 'Manufacturing not found.');
            }

            $product = ProductService::find($manufacturing->item_id);

            if (!$product) {
                return redirect()->route('packaging.index')->with('error', 'Product not found.');
            }

            // Increase product quantity by manufacturing quantity
            $product->quantity += $manufacturing->quantity;
            $product->save();

            // Reduce raw material quantity based on packaging items
            foreach ($packaging_items as $packaging_item) {
                $raw_material = RawMaterial::find($packaging_item->raw_material_id);

                if (!$raw_material) {
                    continue; // Skip if raw material not found
                }

                $collection_center = CollectionCenterStock::where('to_collection_center', $raw_material->collection_center_id)
                    ->where('item_id', $raw_material->item_id)
                    ->first();

                if ($collection_center) {
                    $collection_center->quantity -= $packaging_item->quantity;
                    $collection_center->save();
                }

                $raw_material->quantity -= $packaging_item->quantity;
                $raw_material->save();
            }

            // Update packaging status
            $packaging->status = 1;
            $packaging->save();

            return redirect()->route('packaging.index')->with('success', 'Packaging status successfully changed.');
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }


    public function deleteRawMaterial(Request $request)
    {
        try {
            $packaging_item_id = PackagingItems::find($request->packaging_item_id);
            if($packaging_item_id){
                $packaging_item_id->delete();
                return response()->json(['success' => __('Raw material deleted successfully.')]);
            }else{
                return response()->json(['error' => __('Packaging not found.')]);
            }
        } catch (\Exception $e) {
            \Log::info('deleteRawMaterial', $e);
        }
    }
}
