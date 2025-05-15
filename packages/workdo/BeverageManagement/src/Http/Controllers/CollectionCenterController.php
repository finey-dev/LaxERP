<?php

namespace Workdo\BeverageManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\BeverageManagement\Entities\CollectionCenter;
use Workdo\BeverageManagement\Events\CreateCollectionCenter;
use Workdo\BeverageManagement\Events\DestroyCollectionCenter;
use Workdo\BeverageManagement\Events\UpdateCollectionCenter;
use Illuminate\Support\Facades\Auth;
use Workdo\BeverageManagement\DataTables\CollectionCenterDataTable;
use Workdo\BeverageManagement\DataTables\CollectionCenterViewDataTable;
use Workdo\BeverageManagement\Entities\CollectionCenterStock;

class CollectionCenterController extends Controller
{

    public function index(CollectionCenterDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('collection center manage')) {
            return $dataTable->render('beverage-management::collection-center.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        return view('beverage-management::collection-center.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('collection center create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'location_name' => 'required',
                    'status'        => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $collection_center                = new CollectionCenter();
            $collection_center->location_name = $request->location_name;
            $collection_center->status        = $request->status;
            $collection_center->workspace     = getActiveWorkSpace();
            $collection_center->created_by    = creatorId();
            $collection_center->save();
            event(new CreateCollectionCenter($request, $collection_center));

            return redirect()->route('collection-center.index')->with('success', __('Collection center has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(CollectionCenterViewDataTable $dataTable, Request $request, $id)
    {
        if (Auth::user()->isAbleTo('collection center show')) {
            $collection_center = CollectionCenter::find($id);
            if (!$collection_center) {
                return response()->json(['error' => __('Collection center not found.')], 404);
            }
            $collection_center_stocks = CollectionCenterStock::where('to_collection_center', $id);
            if (!empty($request->type)) {
                $collection_center_stocks = $collection_center_stocks->where('type', $request->type);
            }
            $collection_center_stocks = $collection_center_stocks->whereNotNull('type')->get();
            return $dataTable->with('id',$id)->render('beverage-management::collection-center.show', compact('collection_center_stocks', 'collection_center'));
        } else {
            return redirect()->back()->with(['error' => __('Permission denied.')], 401);
        }
    }

    public function edit($id)
    {
        if (Auth::user()->isAbleTo('collection center edit')) {
            $collection_center = CollectionCenter::find($id);
            if ($collection_center) {
                if ($collection_center->created_by == creatorId() && $collection_center->workspace == getActiveWorkSpace()) {
                    return view('beverage-management::collection-center.edit', compact('collection_center'));
                } else {
                    return redirect()->back()->with('error', __('Permission denied.'));

                }
            } else {
                return response()->json(['error' => __('Collection center not found.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('collection center edit')) {
            $collection_center = CollectionCenter::find($id);
            if ($collection_center) {
                if ($collection_center->created_by == creatorId()  && $collection_center->workspace == getActiveWorkSpace()) {
                    $validator = \Validator::make(
                        $request->all(),
                        [
                            'location_name' => 'required',
                            'status' => 'required',
                        ]
                    );
                    if ($validator->fails()) {
                        $messages = $validator->getMessageBag();

                        return redirect()->back()->with('error', $messages->first());
                    }

                    $collection_center->location_name  = $request->location_name;
                    $collection_center->status         = $request->status;
                    $collection_center->save();
                    event(new UpdateCollectionCenter($request, $collection_center));

                    return redirect()->route('collection-center.index')->with('success', __('Collection center details are updated successfully.'));
                } else {
                    return redirect()->back()->with('error', __('Permission denied.'));
                }
            } else {
                return redirect()->back()->with('error', __('Collection center not found.'));

            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('collection center delete')) {
            $collection_center = CollectionCenter::find($id);
            if ($collection_center) {
                if ($collection_center->created_by == creatorId()  && $collection_center->workspace == getActiveWorkSpace()) {
                    event(new DestroyCollectionCenter($collection_center));
                    $collection_center->delete();

                    return redirect()->route('collection-center.index')->with('success', __('Collection center has been deleted.'));
                } else {
                    return redirect()->back()->with('error', __('Permission denied.'));
                }
            } else {
                return redirect()->back()->with('error', __('Collection center not found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
