<?php

namespace Workdo\BeverageManagement\Http\Controllers;

use App\Models\WarehouseProduct;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Workdo\BeverageManagement\Entities\BillOfMaterial;
use Workdo\BeverageManagement\Entities\CollectionCenter;
use Workdo\BeverageManagement\Entities\CollectionCenterStock;
use Workdo\BeverageManagement\Entities\Manufacturing;
use Workdo\BeverageManagement\Entities\Packaging;
use Workdo\BeverageManagement\Entities\RawMaterial;
use Workdo\BeverageManagement\Events\CreateRawMaterial;
use Workdo\BeverageManagement\Events\DestroyRawMaterial;
use Workdo\BeverageManagement\Events\UpdateRawMaterial;
use Workdo\ProductService\Entities\ProductService;
use Illuminate\Support\Facades\Auth;
use Workdo\BeverageManagement\DataTables\RawMaterialDataTable;

class RawMaterialController extends Controller
{

    public function index(RawMaterialDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('raw material manage')) {
            return $dataTable->render('beverage-management::raw-material.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->isAbleTo('raw material create')) {
            $collection_centers = CollectionCenter::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('status', 1)->pluck('location_name', 'id');
            if (module_is_active('CustomField')) {
                $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id', getActiveWorkSpace())->where('module', '=', 'BeverageManagement')->where('sub_module', 'Raw Material')->get();
            } else {
                $customFields = null;
            }
            if (module_is_active('ProductService')) {
                $products = \Workdo\ProductService\Entities\ProductService::where('workspace_id', getActiveWorkSpace())->whereIn('type', ['product','parts'])->get();
                $product_count = $products->count();
            } else {
                return response()->json(['error' => __('Please Enable Product & Service Module.')], 401);
            }
            return view('beverage-management::raw-material.create', compact('collection_centers', 'customFields', 'products', 'product_count'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function getProductItem(Request $request)
    {

        if (module_is_active('ProductService')) {
            $data['product']     = $product = \Workdo\ProductService\Entities\ProductService::find($request->item_id);
            $data['unit']        = !empty($product) ? ((!empty($product->unit())) ? $product->unit()->name : '') : '';
            $data['taxRate']     = $taxRate = !empty($product) ? (!empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0) : 0;
            $data['taxes']       =  !empty($product) ? (!empty($product->tax_id) ? $product->tax($product->tax_id) : 0) : 0;
            $data['imagePath']   = $product->image != null ? get_file($product->image) : asset("packages/workdo/ProductService/src/Resources/assets/image/img01.jpg");
            return json_encode($data);
        } else {
            return redirect()->route('beveragemanagement::raw-material.index')->with('error', __('Please Enable Product & Service Module'));
        }
    }

    public function collectionCenterQtyTransfer($id, $type)
    {
        if (Auth::user()->isAbleTo('move stock')) {

            if (module_is_active('ProductService')) {
                $common_data = null;
                $collection_center = [];

                switch ($type) {
                    case 'Raw Material':
                        $common_data = RawMaterial::find($id);
                        break;
                    case 'Bill of Material':
                        $common_data = BillOfMaterial::find($id);
                        break;
                    case 'Manufactured':
                        $common_data = Manufacturing::find($id);
                        break;
                    case 'Packaging':
                        $common_data = Packaging::find($id);
                        break;
                }
                $to_collection_centers = CollectionCenter::where('created_by', creatorId())
                    ->where('workspace', getActiveWorkSpace())
                    ->where('id', '!=', $common_data->collection_center_id)
                    ->get()
                    ->pluck('location_name', 'id');

                return view('beverage-management::raw-material.quantityTransfer', compact('to_collection_centers', 'type', 'common_data'));
            } else {
                return response()->json(['error' => __('Please Enable Product & Service Module.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function collectionCenterQtyStore(Request $request)
    {
        if (Auth::user()->isAbleTo('move stock')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'to_collection_center' => 'required',
                    'quantity' => 'required',
                ],
                [
                    'to_collection_center.required' => 'To Collection Center field is required.',
                    'quantity.required' => 'The Quantity field is required.'
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            if ($request->type == 'Raw Material') {
                $from_raw_material = RawMaterial::where('created_by', creatorId())
                    ->where('workspace', getActiveWorkSpace())
                    ->findOrFail($request->id);

                if ($from_raw_material->quantity < $request->quantity) {
                    return redirect()->back()->with('error', __('Not enough quantity in ' . $from_raw_material->collectionCenter->location_name . ' center'));
                }
                // Subtract the requested quantity from $from_raw_material->quantity
                $from_raw_material->quantity -= $request->quantity;
                $from_raw_material->save();

                $to_raw_material = RawMaterial::updateOrCreate(
                    [
                        'collection_center_id' => $request->to_collection_center,
                        'item_id' => $from_raw_material->item_id
                    ],
                    [
                        'created_by' => creatorId(),
                        'workspace' => getActiveWorkSpace(),
                        'price' => $from_raw_material->price,
                        'status' => $from_raw_material->status,
                    ]
                );

                if ($to_raw_material) {
                    $to_raw_material->quantity += $request->quantity;
                    $to_raw_material->save();
                }
                $item_id = $from_raw_material->item_id;
            } elseif ($request->type == 'Bill of Material') {
                $bill_of_material = BillOfMaterial::where('created_by', creatorId())
                    ->where('workspace', getActiveWorkSpace())
                    ->findOrFail($request->id);
                if ($bill_of_material->quantity < $request->quantity) {
                    return redirect()->back()->with('error', __('Not enough quantity in ' . $bill_of_material->collectionCenter->location_name . ' center'));
                }

                $to_manufacturing = BillOfMaterial::updateOrCreate(
                    [
                        'collection_center_id' => $request->to_collection_center,
                        'item_id' => $bill_of_material->item_id
                    ],
                    [
                        'created_by' => creatorId(),
                        'workspace' => getActiveWorkSpace(),
                        'status' => $bill_of_material->status,
                    ]
                );
                $bill_of_material->quantity -= $request->quantity;
                $bill_of_material->save();

                if ($to_manufacturing) {
                    $to_manufacturing->quantity += $request->quantity;
                    $to_manufacturing->save();
                }

                $item_id = $bill_of_material->item_id;
            } elseif ($request->type == 'Manufactured') {

                $manufacturing = Manufacturing::where('created_by', creatorId())
                    ->where('workspace', getActiveWorkSpace())
                    ->findOrFail($request->id);

                if ($manufacturing->quantity < $request->quantity) {
                    return redirect()->back()->with('error', __('Not enough quantity in ' . $manufacturing->collectionCenter->location_name . ' center'));
                }

                $to_manufacturing = Manufacturing::updateOrCreate(
                    [
                        'collection_center_id' => $request->to_collection_center,
                        'item_id' => $manufacturing->item_id,
                        'bill_of_material_id' => $manufacturing->bill_of_material_id,
                    ],
                    [
                        'created_by' => creatorId(),
                        'workspace' => getActiveWorkSpace(),
                        'schedule_date' => $manufacturing->schedule_date,
                        'total' => $manufacturing->total,
                        'status' => $manufacturing->status
                    ]
                );

                $manufacturing->quantity -= $request->quantity;
                $manufacturing->save();

                if ($to_manufacturing) {
                    $to_manufacturing->quantity += $request->quantity;
                    $to_manufacturing->save();
                }

                $item_id = $manufacturing->item_id;
            } elseif ($request->type == 'Packaging') {

                $packaging = Packaging::where('created_by', creatorId())
                    ->where('workspace', getActiveWorkSpace())
                    ->findOrFail($request->id);

                $manufacturing = Manufacturing::find($packaging->manufacturing_id);

                if ($manufacturing->quantity < $request->quantity) {
                    return redirect()->back()->with('error', __('Not enough quantity in ' . $packaging->collectionCenter->location_name . ' center'));
                }
                $to_packaging = Packaging::updateOrCreate(
                    [
                        'collection_center_id' => $request->to_collection_center,
                        'manufacturing_id' => $packaging->manufacturing_id,
                    ],
                    [
                        'total' => $packaging->total,
                        'status' => $packaging->status,
                        'created_by' => creatorId(),
                        'workspace' => getActiveWorkSpace(),
                    ]
                );
                if ($to_packaging) {

                    $to_manufacturing = Manufacturing::where('id', $to_packaging->manufacturing_id)->where('collection_center_id', $to_packaging->collection_center_id)->first();
                    if ($to_manufacturing) {
                        $manufacturing->quantity -= $request->quantity;
                        $manufacturing->save();

                        $to_manufacturing->quantity += $request->quantity;
                        $to_manufacturing->save();
                    } else {
                        return redirect()->back()->with('error', __('Quantity not transferred in collection center.'));
                    }
                }
                $item_id = $manufacturing->item_id;
            } else {
                return redirect()->back()->with('error', __('Invalid type specified.'));
            }
            if ($request->from_collection_center) {
                $collection_center_stock = CollectionCenterStock::where('to_collection_center', $request->from_collection_center)->where('item_id', $item_id)->first();
                if (!empty($collection_center_stock)) {
                    $collection_center_stock->quantity -= $request->quantity;
                    $collection_center_stock->save();
                }
            }
            $stock = new CollectionCenterStock();
            $stock->to_collection_center = $request->to_collection_center;
            $stock->item_id = $item_id;
            $stock->quantity = $request->quantity;
            $stock->type = $request->type;
            $stock->save();

            return redirect()->back()->with('success', __('Quantity Successfully Transferred.'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function collectionCenterMove(Request $request)
    {
        if ($request->from_collection_center) {
            $to_collection_center  = CollectionCenter::where('id', '!=', $request->from_collection_center)->where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();
            return response()->json($to_collection_center);
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('raw material create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'collection_center_id' => [
                        'required',
                        Rule::unique('raw_materials')->where(function ($query) use ($request) {
                            return $query->where('item_id', $request->item_id);
                        })
                    ],
                    'item_id' => 'required',
                    'status' => 'required',
                ],
                [
                    'collection_center_id.required' => 'The Collection Center field is required.',
                    'collection_center_id.unique' => 'The Collection Center has already been taken with this name',
                    'item_id.required' => 'The Name field is required.'
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $raw_material                       = new RawMaterial();
            $raw_material->collection_center_id = $request->collection_center_id;
            $raw_material->item_id              = $request->item_id;
            $raw_material->price                = $request->price + $request->itemTaxPrice;
            $raw_material->status               = $request->status;
            $raw_material->workspace            = getActiveWorkSpace();
            $raw_material->created_by           = creatorId();
            $raw_material->save();
            if (module_is_active('CustomField')) {
                \Workdo\CustomField\Entities\CustomField::saveData($raw_material, $request->customField);
            }
            event(new CreateRawMaterial($request, $raw_material));

            return redirect()->route('raw-material.index')->with('success', __('Raw Material successfully created!'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function show($id)
    {
        if (Auth::user()->isAbleTo('raw material show')) {
            $raw_material = RawMaterial::find($id);
            if($raw_material){
                if (module_is_active('ProductService')) {
                    $data['product']     = $product = \Workdo\ProductService\Entities\ProductService::find($raw_material->item_id);
                    $data['unit']        = !empty($product) ? ((!empty($product->unit())) ? $product->unit()->name : '') : '';
                    $data['taxRate']     = $taxRate = !empty($product) ? (!empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0) : 0;
                    $data['taxes']       = !empty($product) ? (!empty($product->tax_id) ? $product->tax($product->tax_id) : 0) : 0;
                } else {
                    return redirect()->route('beveragemanagement::raw-material.index')->with('error', __('Please Enable Product & Service Module'));
                }
                return view('beverage-management::raw-material.show', compact('raw_material', 'data'));
            }else{
                return redirect()->back()->with('error', __('Raw Material not found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if (Auth::user()->isAbleTo('raw material edit')) {
            $raw_material = RawMaterial::find($id);
            if ($raw_material) {
                if ($raw_material->created_by == creatorId() && $raw_material->workspace == getActiveWorkSpace()) {
                    $collection_centers = CollectionCenter::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('status', 1)->pluck('location_name', 'id');

                    if (module_is_active('CustomField')) {
                        $raw_material->customField = \Workdo\CustomField\Entities\CustomField::getData($raw_material, 'BeverageManagement', 'Raw Material');
                        $customFields             = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'BeverageManagement')->where('sub_module', 'Raw Material')->get();
                    } else {
                        $customFields = null;
                    }
                    if (module_is_active('ProductService')) {
                        $products = \Workdo\ProductService\Entities\ProductService::where('workspace_id', getActiveWorkSpace())->whereIn('type', ['product','parts'])->get();
                        $product_count = $products->count();

                        $data['product']     = $product = \Workdo\ProductService\Entities\ProductService::find($raw_material->item_id);
                        $data['unit']        = !empty($product) ? ((!empty($product->unit())) ? $product->unit()->name : '') : '';
                        $data['taxRate']     = $taxRate = !empty($product) ? (!empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0) : 0;
                        $data['taxes']       = !empty($product) ? (!empty($product->tax_id) ? $product->tax($product->tax_id) : 0) : 0;
                        $salePrice           = !empty($product) ?  $product->sale_price : 0;
                        $quantity            = 1;
                        $taxPrice            = !empty($product) ? (($taxRate / 100) * ($salePrice * $quantity)) : 0;
                        $data['totalAmount'] = !empty($product) ?  ($salePrice * $quantity) : 0;
                        $data['imagePath']   = $product->image != null ? get_file($product->image) : asset("Workdo/ProductService/Resources/assets/image/img01.jpg");
                    } else {
                        return redirect()->route('beveragemanagement::raw-material.index')->with('error', __('Please Enable Product & Service Module'));
                    }
                    return view('beverage-management::raw-material.edit', compact('collection_centers', 'raw_material', 'customFields', 'products', 'product_count', 'data'));
                } else {
                    return response()->json(['error' => __('Permission denied.')]);
                }
            } else {
                return response()->json(['error' => __('Raw Material not found.')]);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')]);
        }
    }

    public function update(Request $request, $id)
    {
       
        if (Auth::user()->isAbleTo('raw material edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'collection_center_id' => 'required',
                    'item_id' => 'required',
                    'status' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $raw_material                       = RawMaterial::find($id);
            $raw_material->collection_center_id = $request->collection_center_id;
            $raw_material->item_id              = $request->item_id;
            $raw_material->price                = $request->price + $request->itemTaxPrice;
            $raw_material->status               = $request->status;
            $raw_material->workspace            = getActiveWorkSpace();
            $raw_material->created_by           = creatorId();
            $raw_material->update();
            if (module_is_active('CustomField')) {
                \Workdo\CustomField\Entities\CustomField::saveData($raw_material, $request->customField);
            }
            event(new UpdateRawMaterial($request, $raw_material));


            return redirect()->route('raw-material.index')->with('success', __('Raw Material Successfully Updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (!Auth::user()->isAbleTo('raw material delete')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $raw_material = RawMaterial::find($id);
        if (!$raw_material) {
            return redirect()->route('raw-material.index')->with('error', 'Raw Material not found.');
        }

        $product_service = ProductService::find($raw_material->item_id);
        if (!$product_service) {
            return redirect()->route('raw-material.index')->with('error', 'Associated product/service not found.');
        }
        try {
            $warehouse = WarehouseProduct::where('created_by', creatorId())
                ->where('workspace', getActiveWorkSpace())
                ->where('warehouse_id', $product_service->warehouse_id)
                ->where('product_id', $raw_material->item_id)
                ->first();

            $collection_center_stock = CollectionCenterStock::where('warehouse_id', $product_service->warehouse_id)
                ->where('to_collection_center', $raw_material->collection_center_id)
                ->where('type', 'add stock')
                ->first();

            if ($warehouse && $collection_center_stock) {
                $warehouse->quantity += $collection_center_stock->quantity;
                $warehouse->save();

                $collection_center_stock->delete();

                if (module_is_active('CustomField')) {
                    $customFields = \Workdo\CustomField\Entities\CustomField::where('module', 'BeverageManagement')
                        ->where('sub_module', 'Raw Material')
                        ->get();

                    foreach ($customFields as $customField) {
                        $customFieldValue = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', $raw_material->id)
                            ->where('field_id', $customField->id)
                            ->first();

                        if ($customFieldValue) {
                            $customFieldValue->delete();
                        }
                    }
                }

                if (!empty($raw_material->image)) {
                    delete_file($raw_material->image);
                }
            }
            event(new DestroyRawMaterial($raw_material));
            $raw_material->delete();
            return redirect()->route('raw-material.index')->with('success', 'Raw Material successfully deleted.');
        } catch (\Exception $e) {
            return redirect()->route('raw-material.index')->with('error', 'An error occurred while deleting the raw material.');
        }
    }
}
