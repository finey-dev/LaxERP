<?php

namespace Workdo\BeverageManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\BeverageManagement\DataTables\WasteRecordDataTable;
use Workdo\BeverageManagement\Entities\Manufacturing;
use Workdo\BeverageManagement\Entities\WasteRecord;
use Workdo\BeverageManagement\Events\CreateWasteRecord;
use Workdo\BeverageManagement\Events\DestroyWasteRecord;
use Workdo\BeverageManagement\Events\UpdateWasteRecord;

class WasteRecordController extends Controller
{
    public function index(WasteRecordDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('waste-records manage')) {
            return $dataTable->render('beverage-management::waste-records.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->isAbleTo('waste-records create')) {
            $products = Manufacturing::where('status', 1)->get();
            return view('beverage-management::waste-records.create', compact('products'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('waste-records create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'item_id' => 'required',
                    'waste_categories' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $waste_records                        = new WasteRecord();
            $waste_records->item_id               = $request->item_id;
            $waste_records->waste_date             = $request->waste_date   ;
            $waste_records->waste_categories            = $request->waste_categories;
            $waste_records->quantity                = $request->quantity;
            $waste_records->reason                = $request->reason;
            $waste_records->comments              = $request->comments;
            $waste_records->workspace             = getActiveWorkSpace();
            $waste_records->created_by            = creatorId();
            $waste_records->save();
            event(new CreateWasteRecord($request, $waste_records));

            return redirect()->route('waste-records.index')->with('success', __('The Waste Records has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function show($id)
    {
        if (Auth::user()->isAbleTo('waste-records show')) {
            $waste_records = WasteRecord::find($id);
            if ($waste_records) {
                if (module_is_active('ProductService')) {
                    $data['product']     = \Workdo\ProductService\Entities\ProductService::find($waste_records->item_id);
                    $data['waste_date']       = !empty($waste_records['waste_date']) ?  $waste_records['waste_date'] : '';
                    $data['waste_categories']     = !empty($waste_records['waste_categories']) ?  $waste_records['waste_categories'] : '';
                    $data['quantity']     = !empty($waste_records['quantity']) ?  $waste_records['quantity'] : '';
                    $data['reason']     = !empty($waste_records['reason']) ?  $waste_records['reason'] : '';
                    $data['comments']     = !empty($waste_records['comments']) ?  $waste_records['comments'] : '';
                } else {
                    return redirect()->route('beveragemanagement::waste-records.index')->with('error', __('Please Enable Product & Service Module'));
                }
                return view('beverage-management::waste-records.show', compact('waste_records', 'data'));
            } else {
                return redirect()->back()->with('error', __('Quality Standards not found.'));
            }
        } else {
            return redirect()->back()->with(['error' => __('Permission denied.')], 401);
        }
    }
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('waste-records edit')) {
            $waste_records = WasteRecord::find($id);
            if ($waste_records) {
                if ($waste_records->created_by == creatorId() && $waste_records->workspace == getActiveWorkSpace()) {
                    $waste_record = Manufacturing::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('status', 1)->get();
                    return view('beverage-management::waste-records.edit', compact('waste_records', 'waste_record'));
                } else {
                    return response()->json(['error' => __('Permission denied.')]);
                }
            } else {
                return response()->json(['error' => __('Waste Records not found.')]);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')]);
        }
    }
    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('waste-records edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'item_id' => 'required',
                    'waste_categories' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $waste_records                        = WasteRecord::find($id);
            $waste_records->item_id               = $request->item_id;
            $waste_records->waste_date            = $request->waste_date;
            $waste_records->waste_categories      = $request->waste_categories;
            $waste_records->quantity                = $request->quantity;
            $waste_records->reason                = $request->reason;
            $waste_records->comments              = $request->comments;
            $waste_records->workspace             = getActiveWorkSpace();
            $waste_records->created_by            = creatorId();
            $waste_records->update();
            event(new UpdateWasteRecord($request, $waste_records));

            return redirect()->route('waste-records.index')->with('success', __('The Waste Records has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('waste-records delete')) {
            $waste_records = WasteRecord::find($id);
            if ($waste_records->created_by == creatorId()  && $waste_records->workspace == getActiveWorkSpace()) {
                event(new DestroyWasteRecord($waste_records));
                
                $waste_records->delete();
                return redirect()->route('waste-records.index')->with('success', __('The Waste Records has been deleted successfully.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
