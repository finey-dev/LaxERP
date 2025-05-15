<?php

namespace Workdo\BeverageManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\BeverageManagement\DataTables\ManufacturingDataTable;
use Workdo\BeverageManagement\Entities\Packaging;
use Workdo\ProductService\Entities\ProductService;
use Workdo\BeverageManagement\Entities\RawMaterial;
use Workdo\BeverageManagement\Entities\Manufacturing;
use Workdo\BeverageManagement\Entities\BillOfMaterial;
use Workdo\BeverageManagement\Entities\PackagingItems;
use Workdo\BeverageManagement\Events\DestroyPackaging;
use Workdo\BeverageManagement\Entities\BillMaterialItem;
use Workdo\BeverageManagement\Entities\CollectionCenter;
use Workdo\BeverageManagement\Events\CreateManufacturing;
use Workdo\BeverageManagement\Events\UpdateManufacturing;
use Workdo\BeverageManagement\Events\DestroyManufacturing;
use Workdo\BeverageManagement\Events\DestroyPackagingItem;
use Workdo\BeverageManagement\Entities\CollectionCenterStock;

class ManufacturingController extends Controller
{

    public function index(ManufacturingDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('manufacturing manage')) {
            return $dataTable->render('beverage-management::manufacturing.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function billMaterial(Request $request)
    {
        $bill_of_material = BillOfMaterial::find($request->bill_material_id);
        if ($bill_of_material) {
            $bill_material_items = BillMaterialItem::with('rawMaterial')->where('bill_of_material_id', $request->bill_material_id)->get();
            $html = '';
            $html = view("beverage-management::manufacturing.bill-material", compact('bill_material_items'))->render();
            $return['html'] = $html;
            $return['quantity'] = $bill_of_material->quantity;
            return response()->json($return);
        } else {
            return redirect()->back()->with('error', __('Bill of Material not found.'), 404);
        }
    }

    public function create()
    {
        if (Auth::user()->isAbleTo('manufacturing create')) {
            $collection_centers = CollectionCenter::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('status', 1)->pluck('location_name', 'id');
            $bil_of_materials = BillOfMaterial::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('status', 0)->get();
            if (module_is_active('CustomField')) {
                $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id', getActiveWorkSpace())->where('module', '=', 'BeverageManagement')->where('sub_module', 'Manufacturing')->get();
            } else {
                $customFields = null;
            }
            if (module_is_active('ProductService')) {
                $product = \Workdo\ProductService\Entities\ProductService::where('workspace_id', getActiveWorkSpace())->where('type','product')->get()->pluck('name', 'id');
                $product_count = $product->count();
            } else {
                return redirect()->route('beverage-management::raw-material.index')->with('error', __('Please Enable Product & Service Module'));
            }
            return view('beverage-management::manufacturing.create', compact('bil_of_materials', 'customFields', 'collection_centers', 'product', 'product_count'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('manufacturing create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'bill_of_material_id' => 'required|unique:manufacturings,bill_of_material_id',
                    'collection_center_id' => 'required',
                    'item_id' => 'required',
                    'schedule_date' => 'required',
                ],
                [
                    'bill_of_material_id.required' => 'The bill of material is required.',
                    'bill_of_material_id.unique' => 'This bill of material manufacturing has already exists.',
                    'collection_center_id.required' => 'The collection center is required.',
                    'item_id.required' => 'The item is required.',
                    'schedule_date.required' => 'The schedule date is required.',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $raw_material_ids = $request->raw_material;
            if($raw_material_ids){
                $bill_of_materials = RawMaterial::whereIn('id', $raw_material_ids)
                    ->orderByRaw("FIELD(id, " . implode(',', $raw_material_ids) . ")")
                    ->get();

                foreach ($bill_of_materials as $index => $bill_of_material) {
                    $quantity = $request->raw_quantity[$index];

                    if ($bill_of_material->quantity >= $quantity) {
                        $success = true;
                    } else {
                        $success = false;
                        break;
                    }
                }
                if ($success) {
                    $manufacturing = new Manufacturing();
                    $manufacturing->bill_of_material_id = $request->bill_of_material_id;
                    $manufacturing->collection_center_id = $request->collection_center_id;
                    $manufacturing->item_id = $request->item_id;
                    $manufacturing->schedule_date = $request->schedule_date;
                    $manufacturing->quantity = $request->quantity;
                    $manufacturing->total = $request->total;
                    $manufacturing->workspace     = getActiveWorkSpace();
                    $manufacturing->created_by    = creatorId();
                    $manufacturing->save();
                } else {
                    return redirect()->back()->with('error', 'Raw material quantity not available');
                }
                if (module_is_active('CustomField')) {
                    \Workdo\CustomField\Entities\CustomField::saveData($manufacturing, $request->customField);
                }
                event(new CreateManufacturing($request, $manufacturing));
                return redirect()->route('manufacturing.index')->with('success', __('Manufacturing successfully created!'));
            } else {
                return redirect()->back()->with('error', __('Raw material not available.'));
            }
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
    }

    public function show($id)
    {
        if (Auth::user()->isAbleTo('manufacturing show')) {
            $manufacturing = Manufacturing::with('billOfMaterial')->find($id);
            $company_settings = getCompanyAllSetting($manufacturing->created_by, $manufacturing->workspace);
            if ($manufacturing) {
                $bill_material_items = BillMaterialItem::with('rawMaterial')->where('bill_of_material_id', $manufacturing->bill_of_material_id)->get();
                if (module_is_active('CustomField')) {
                    $manufacturing->customField = \Workdo\CustomField\Entities\CustomField::getData($manufacturing, 'BeverageManagement', 'Manufacturing');
                    $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'BeverageManagement')->where('sub_module', 'Manufacturing')->get();
                } else {
                    $customFields = null;
                }
                return view('beverage-management::manufacturing.show', compact('manufacturing', 'bill_material_items', 'customFields','company_settings'));
            } else {
                return response()->json(['error' => __('Manufacturing not found.')], 404);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function edit($id)
    {
        if (Auth::user()->isAbleTo('manufacturing edit')) {
            $manufacturing = Manufacturing::find($id);
            if ($manufacturing) {
                $bill_material = BillOfMaterial::find($manufacturing->bill_of_material_id);
                if ($manufacturing->created_by == creatorId() && $manufacturing->workspace == getActiveWorkSpace()) {
                    $bill_material_items = BillMaterialItem::with('rawMaterial')->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('bill_of_material_id', $manufacturing->bill_of_material_id)->get();
                    $bill_of_materials = BillOfMaterial::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('status', 0)->get();
                    if (module_is_active('CustomField')) {
                        $manufacturing->customField = \Workdo\CustomField\Entities\CustomField::getData($manufacturing, 'BeverageManagement', 'Manufacturing');
                        $customFields             = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'BeverageManagement')->where('sub_module', 'Manufacturing')->get();
                    } else {
                        $customFields = null;
                    }
                    $collection_centers = CollectionCenter::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('status', 1)->pluck('location_name', 'id');
                    if (module_is_active('ProductService')) {
                        $product = \Workdo\ProductService\Entities\ProductService::where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
                        $product_count = $product->count();
                    } else {
                        return redirect()->route('beveragemanagement::raw-material.index')->with('error', __('Please Enable Product & Service Module'));
                    }
                    return view('beverage-management::manufacturing.edit', compact('bill_material_items', 'manufacturing', 'bill_of_materials', 'bill_material', 'customFields', 'collection_centers', 'product', 'product_count'));
                } else {
                    return response()->json(['error' => __('Permission denied.')], 401);
                }
            } else {
                return response()->json(['error' => __('Manufacturing not found.')], 404);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('manufacturing edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'bill_of_material_id' => 'required|unique:manufacturings,bill_of_material_id,' . $id,
                    'collection_center_id' => 'required',
                    'item_id' => 'required',
                    'schedule_date' => 'required',
                ],
                [
                    'bill_of_material_id.required' => 'The bill of material is required.',
                    'bill_of_material_id.unique' => 'This bill of material manufacturing has already exists.',
                    'collection_center_id.required' => 'The collection center is required.',
                    'item_id.required' => 'The item is required.',
                    'schedule_date.required' => 'The schedule date is required.',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $bil_of_materials = RawMaterial::whereIn('id', $request->raw_material)->get();
            foreach ($bil_of_materials as $index => $bil_of_material) {
                $quantity = $request->raw_quantity[$index];

                if ($bil_of_material->quantity >= $quantity) {
                    $success = true;
                } else {
                    return redirect()->back()->with('error', __('Raw Material Quantity Not Match'));
                }
            }
            if ($success == true) {
                $manufacturing                      = Manufacturing::find($id);
                if (!$manufacturing) {
                    return redirect()->back()->with('error', __('Manufacturing not found.'));
                }
                $manufacturing->bill_of_material_id = $request->bill_of_material_id;
                $manufacturing->collection_center_id = $request->collection_center_id;
                $manufacturing->item_id             = $request->item_id;
                $manufacturing->schedule_date       = $request->schedule_date;
                $manufacturing->quantity = $request->quantity;
                $manufacturing->total               = $request->total;
                $manufacturing->workspace           = getActiveWorkSpace();
                $manufacturing->created_by          = creatorId();
                $manufacturing->save();
                if (module_is_active('CustomField')) {
                    \Workdo\CustomField\Entities\CustomField::saveData($manufacturing, $request->customField);
                }
                event(new UpdateManufacturing($request, $manufacturing));
            }
            return redirect()->route('manufacturing.index')->with('success', __('Manufacturing successfully updated!'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('manufacturing delete')) {
            $manufacturing  = Manufacturing::find($id);
            if ($manufacturing) {

                $packaging = Packaging::where('manufacturing_id', $manufacturing->id)->first();
                if ($packaging) {
                    $packaging_items = PackagingItems::where('packaging_id', $packaging->id)->get();
                }
                if (module_is_active('CustomField')) {
                    $customFields = \Workdo\CustomField\Entities\CustomField::where('module', 'BeverageManagement')->where('sub_module', 'Manufacturing')->get();
                    foreach ($customFields as $customField) {
                        $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $manufacturing->id)->where('field_id', $customField->id)->first();
                        if (!empty($value)) {
                            $value->delete();
                        }
                    }
                }

                if (($manufacturing->created_by == creatorId() && $manufacturing->workspace == getActiveWorkSpace()) || ($packaging->created_by == creatorId() && $packaging->workspace == getActiveWorkSpace())) {

                    event(new DestroyManufacturing($manufacturing));

                    $manufacturing->delete();

                    if ($packaging) {
                        event(new DestroyPackaging($packaging));
                        $packaging->delete();

                        foreach ($packaging_items as $packaging_item) {
                            event(new DestroyPackagingItem($packaging_item));
                            $packaging_item->delete();
                        }
                    }

                    return redirect()->route('manufacturing.index')->with('success', 'Manufacturing successfully deleted.');
                } else {
                    return response()->json(['error' => __('Permission Denied.')], 401);
                }
            } else {
                return response()->json(['error' => __('Manufacturing not found.')], 401);
            }
        } else {
            return response()->json(['error' => __('Manufacturing not found.')], 401);
        }
    }

    public function statusCompleted($id)
    {
        if (Auth::user()->isAbleTo('manufacturing status')) {
            $manufacturing = Manufacturing::find($id);

            if (!$manufacturing) {
                return redirect()->route('manufacturing.index')->with('error', 'Manufacturing not found.');
            }

            $bill_material = BillOfMaterial::find($manufacturing->bill_of_material_id);

            if (!$bill_material) {
                return redirect()->route('manufacturing.index')->with('error', 'Bill of Material not found.');
            }

            $product = ProductService::find($bill_material->item_id);

            if (!$product) {
                return redirect()->route('manufacturing.index')->with('error', 'Product not found.');
            }

            $product->quantity += $bill_material->quantity;
            $product->save();

            $bill_material->status = 1;
            $bill_material->save();

            $bill_material_items = BillMaterialItem::where('bill_of_material_id', $bill_material->id)->get();

            foreach ($bill_material_items as $bill_material_item) {
                $raw_material = RawMaterial::find($bill_material_item->raw_material_id);
                $collection_center = CollectionCenterStock::where('to_collection_center', $raw_material->collection_center_id)
                    ->where('item_id', $raw_material->item_id)
                    ->first();

                if ($collection_center) {
                    $collection_center->quantity -= $bill_material_item->quantity;
                    $collection_center->save();
                }

                if ($raw_material) {
                    $raw_material->quantity -= $bill_material_item->quantity;
                    $raw_material->save();
                }
            }

            $manufacturing->status = 1;
            $manufacturing->save();

            return redirect()->route('manufacturing.index')->with('success', 'Manufacturing status has been changes successfully.');
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
