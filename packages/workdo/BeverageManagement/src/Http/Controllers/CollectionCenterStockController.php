<?php

namespace Workdo\BeverageManagement\Http\Controllers;

use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\BeverageManagement\Entities\CollectionCenter;
use Workdo\BeverageManagement\Entities\CollectionCenterStock;
use Workdo\BeverageManagement\Entities\Manufacturing;
use Workdo\BeverageManagement\Entities\Packaging;
use Workdo\BeverageManagement\Entities\RawMaterial;
use Illuminate\Support\Facades\Auth;

class CollectionCenterStockController extends Controller
{

    public function index()
    {
        return view('beverage-management::index');
    }

    public function create($id)
    {
        if (Auth::user()->isAbleTo('add stock')) {
            $raw_material = RawMaterial::find($id);
            if ($raw_material) {
                $warehouseItems = WarehouseProduct::where('workspace', getActiveWorkSpace())
                    ->where('product_id', $raw_material->item_id)
                    ->pluck('warehouse_id')
                    ->toArray();

                if (!empty($warehouseItems)) {
                    $warehouses = Warehouse::where('created_by', creatorId())
                        ->where('workspace', getActiveWorkSpace())
                        ->whereIn('id', $warehouseItems)
                        ->pluck('name', 'id')
                        ->prepend('Select Warehouse', '');

                    return view('beverage-management::add-stock.create', compact('warehouses'));
                } else {
                    return response()->json(['error' => __('Warehouse Not Found.')], 401);
                }
            } else {
                return response()->json(['error' => __('Raw Material not found.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }


    public function warehouseItem(Request $request)
    {
        if (module_is_active('ProductService')) {
            $items = WarehouseProduct::where('workspace', getActiveWorkSpace())->where('warehouse_id', $request->warehouse)->pluck('product_id')->toArray();
            $product = \Workdo\ProductService\Entities\ProductService::where('workspace_id', getActiveWorkSpace())->whereIn('id', $items)->get();
            return response()->json($product);
        } else {
            return redirect()->route('beveragemanagement::raw-material.index')->with('error', __('Please Enable Product & Service Module'));
        }
    }

    public function itemCollectionCenter(Request $request)
    {
        if ($request->item_id) {
            $items = RawMaterial::where('workspace', getActiveWorkSpace())->where('item_id', $request->item_id)->pluck('collection_center_id')->toArray();
            $collection_centers = CollectionCenter::where('workspace', getActiveWorkSpace())->whereIn('id', $items)->get();
            return response()->json($collection_centers);
        } else {
            return response()->json(['error' => __('Something Went Wrong.')], 401);
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('add stock')) {
            $warehouse = WarehouseProduct::where('created_by', creatorId())
                ->where('workspace', getActiveWorkSpace())
                ->where('warehouse_id', $request->warehouse)
                ->where('product_id', $request->item_id)
                ->first();

            if (!$warehouse) {
                return redirect()->back()->with('error', __('Warehouse Product not found.'));
            }

            if ($warehouse->quantity < $request->quantity) {
                return redirect()->back()->with('error', __('Not enough quantity in the warehouse.'));
            }

            $warehouse->quantity -= $request->quantity;
            $warehouse->save();

            $raw_material = RawMaterial::where('collection_center_id', $request->to_collection_center)
                ->where('item_id', $request->item_id)
                ->first();

            if (!$raw_material) {
                return redirect()->back()->with('error', __('Item not found in the selected collection center.'));
            }

            $raw_material->quantity += $request->quantity;
            $raw_material->save();

            $stock = new CollectionCenterStock();
            $stock->warehouse_id = $request->warehouse;
            $stock->to_collection_center = $request->to_collection_center;
            $stock->item_id = $request->item_id;
            $stock->quantity = $request->quantity;
            $stock->type = 'add stock';
            $stock->save();

            return redirect()->back()->with('success', __('Stock added successfully.'));
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }


    public function showForm($id)
    {
        if (Auth::user()->isAbleTo('add stock')) {
            $packaging = Packaging::find($id);
            if ($packaging) {
                $collection_centers = CollectionCenter::where('id', $packaging->collection_center_id)->get()->pluck('location_name', 'id');
                $manufacturing = Manufacturing::where('id', $packaging->manufacturing_id)->first();

                $warehouseItems = WarehouseProduct::where('workspace', getActiveWorkSpace())
                    ->where('product_id', $manufacturing->item_id)
                    ->pluck('warehouse_id')
                    ->toArray();

                if (!empty($warehouseItems)) {
                    $warehouses = Warehouse::where('created_by', creatorId())
                        ->where('workspace', getActiveWorkSpace())
                        ->whereIn('id', $warehouseItems)
                        ->pluck('name', 'id')
                        ->prepend('Select Warehouse', '');
                    return view('beverage-management::add-stock.packaging', compact('warehouses', 'collection_centers', 'packaging'));
                } else {
                    return redirect()->back()->with('error', __('Warehouse Not Found.'));
                }
            }else{
                return redirect()->back()->with('error', __('Packaging not found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));

        }
    }

    public function addStockPackaging(Request $request)
    {
        $packaging = Packaging::where('id', $request->packaging_id)->where('collection_center_id', $request->to_collection_center)->where('status',1)->first();

        $manufacturing = Manufacturing::where('id', $packaging->manufacturing_id)->where('item_id', $request->item_id)->first();

        if (!$manufacturing) {
            return redirect()->back()->with('error', __('Item not found in the selected collection center.'));
        }

        if ($manufacturing->quantity < $request->quantity) {
            return redirect()->back()->with('error', __('Not enough quantity in the collection center.'));
        }

        $manufacturing->quantity += $request->quantity;
        $manufacturing->save();

        $warehouse = WarehouseProduct::where('created_by', creatorId())
            ->where('workspace', getActiveWorkSpace())
            ->where('warehouse_id', $request->warehouse)
            ->where('product_id', $request->item_id)
            ->first();

        if (!$warehouse) {
            return redirect()->back()->with('error', __('Warehouse Product not found.'));
        }

        $warehouse->quantity -= $request->quantity;
        $warehouse->save();

        $stock = new CollectionCenterStock();
        $stock->warehouse_id = $request->warehouse;
        $stock->to_collection_center = $request->to_collection_center;
        $stock->item_id = $request->item_id;
        $stock->quantity = $request->quantity;
        $stock->save();

        return redirect()->back()->with('success', __('Stock added successfully.'));
    }
}
