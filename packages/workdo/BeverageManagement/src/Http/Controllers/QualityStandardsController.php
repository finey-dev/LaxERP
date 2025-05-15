<?php

namespace Workdo\BeverageManagement\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\BeverageManagement\Entities\CollectionCenter;

use Illuminate\Support\Facades\Auth;
use Workdo\BeverageManagement\DataTables\QualityStandardDataTable;
use Workdo\BeverageManagement\DataTables\RawMaterialDataTable;
use Workdo\BeverageManagement\Entities\QualityStandard;
use Workdo\BeverageManagement\Entities\RawMaterial;
use Workdo\BeverageManagement\Events\CreateQualityStandard;
use Workdo\BeverageManagement\Events\DestroyQualityStandard;
use Workdo\BeverageManagement\Events\UpdateQualityStandard;

class QualityStandardsController extends Controller
{

    public function index(QualityStandardDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('quality-standards manage')) {
            return $dataTable->render('beverage-management::quality-standards.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->isAbleTo('quality-standards create')) {
            $products = RawMaterial::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('status', 1)->get();
            return view('beverage-management::quality-standards.create', compact('products'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('quality-standards create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'item_id' => 'required',
                    'standard_type' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $quality_standards                        = new QualityStandard();
            $quality_standards->item_id      = $request->item_id;
            $quality_standards->standard_type         = $request->standard_type;
            $quality_standards->value                 = $request->value;
            $quality_standards->workspace             = getActiveWorkSpace();
            $quality_standards->created_by            = creatorId();
            $quality_standards->save();
            event(new CreateQualityStandard($request, $quality_standards));

            return redirect()->route('quality-standards.index')->with('success', __('The Quality Standards has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function show($id)
    {
        if (Auth::user()->isAbleTo('quality-standards show')) {
            $quality_standards = QualityStandard::find($id);
            if ($quality_standards) {
                if (module_is_active('ProductService')) {
                    $data['product']     = \Workdo\ProductService\Entities\ProductService::find($quality_standards->item_id);
                    $data['standardtype']       = !empty($quality_standards['standard_type']) ?  $quality_standards['standard_type'] : '';
                    $data['value']     = !empty($quality_standards['value']) ?  $quality_standards['value'] : '';
                } else {
                    return redirect()->route('beveragemanagement::quality-standards.index')->with('error', __('Please Enable Product & Service Module'));
                }
                return view('beverage-management::quality-standards.show', compact('quality_standards', 'data'));
            } else {
                return redirect()->back()->with('error', __('Quality Standards not found.'));
            }
        } else {
            return redirect()->back()->with(['error' => __('Permission denied.')], 401);
        }
    }
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('quality-standards edit')) {
            $quality_standards = QualityStandard::find($id);

            if ($quality_standards) {
                if ($quality_standards->created_by == creatorId() && $quality_standards->workspace == getActiveWorkSpace()) {
                    $quality_standard = RawMaterial::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('status', 1)->get();

                    return view('beverage-management::quality-standards.edit', compact('quality_standards', 'quality_standard'));
                } else {
                    return response()->json(['error' => __('Permission denied.')]);
                }
            } else {
                return response()->json(['error' => __('Quality Standards not found.')]);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')]);
        }
    }
    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('quality-standards edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'item_id' => 'required',
                    'standard_type' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $quality_standards                        = QualityStandard::find($id);
            $quality_standards->item_id               = $request->item_id;
            $quality_standards->standard_type         = $request->standard_type;
            $quality_standards->value                 = $request->value;
            $quality_standards->workspace             = getActiveWorkSpace();
            $quality_standards->created_by            = creatorId();
            $quality_standards->update();
            event(new UpdateQualityStandard($request, $quality_standards));

            return redirect()->route('quality-standards.index')->with('success', __('The Quality Standards has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('quality-standards delete')) {
            $quality_standards = QualityStandard::find($id);
            if ($quality_standards->created_by == creatorId()  && $quality_standards->workspace == getActiveWorkSpace()) {
                event(new DestroyQualityStandard($quality_standards));

                $quality_standards->delete();

                return redirect()->route('quality-standards.index')->with('success', __('The Quality Standards has been deleted successfully.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
