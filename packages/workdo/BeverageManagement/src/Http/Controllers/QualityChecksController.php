<?php

namespace Workdo\BeverageManagement\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\BeverageManagement\DataTables\QualityCheckDataTable;
use Workdo\BeverageManagement\Entities\QualityCheck;
use Workdo\BeverageManagement\Entities\RawMaterial;
use Workdo\BeverageManagement\Events\CreateQualityCheck;
use Workdo\BeverageManagement\Events\DestroyQualityCheck;
use Workdo\BeverageManagement\Events\UpdateQualityCheck;

class QualityChecksController extends Controller
{

    public function index(QualityCheckDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('quality-checks manage')) {
            return $dataTable->render('beverage-management::quality-checks.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->isAbleTo('quality-checks create')) {
            $products = RawMaterial::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('status', 1)->get();
            return view('beverage-management::quality-checks.create', compact('products'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('quality-checks create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'item_id' => 'required',
                    'check_type' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $quality_checks                        = new QualityCheck();
            $quality_checks->item_id               = $request->item_id;
            $quality_checks->check_date            = $request->check_date;
            $quality_checks->check_type            = $request->check_type;
            $quality_checks->result                = $request->result;
            $quality_checks->comments              = $request->comments;
            $quality_checks->workspace             = getActiveWorkSpace();
            $quality_checks->created_by            = creatorId();
            $quality_checks->save();
            event(new CreateQualityCheck($request, $quality_checks));

            return redirect()->route('quality-checks.index')->with('success', __('The Quality Checks has been created successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function show($id)
    {
        if (Auth::user()->isAbleTo('quality-checks show')) {
            $quality_checks = QualityCheck::find($id);
            if ($quality_checks) {
                if (module_is_active('ProductService')) {
                    $data['product']     = \Workdo\ProductService\Entities\ProductService::find($quality_checks->item_id);
                    $data['check_date']       = !empty($quality_checks['check_date']) ?  $quality_checks['check_date'] : '';
                    $data['check_type']     = !empty($quality_checks['check_type']) ?  $quality_checks['check_type'] : '';
                    $data['result']     = !empty($quality_checks['result']) ?  $quality_checks['result'] : '';
                    $data['comments']     = !empty($quality_checks['comments']) ?  $quality_checks['comments'] : '';
                } else {
                    return redirect()->route('beveragemanagement::quality-checks.index')->with('error', __('Please Enable Product & Service Module'));
                }
                return view('beverage-management::quality-checks.show', compact('quality_checks', 'data'));
            } else {
                return redirect()->back()->with('error', __('Quality Standards not found.'));
            }
        } else {
            return redirect()->back()->with(['error' => __('Permission denied.')], 401);
        }
    }
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('quality-checks edit')) {
            $quality_checks = QualityCheck::find($id);
            if ($quality_checks) {
                if ($quality_checks->created_by == creatorId() && $quality_checks->workspace == getActiveWorkSpace()) {
                    $quality_check = RawMaterial::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('status', 1)->get();
                    return view('beverage-management::quality-checks.edit', compact('quality_checks', 'quality_check'));
                } else {
                    return response()->json(['error' => __('Permission denied.')]);
                }
            } else {
                return response()->json(['error' => __('Quality Checks not found.')]);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')]);
        }
    }
    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('quality-checks edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'item_id' => 'required',
                    'check_type' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $quality_checks                        = QualityCheck::find($id);
            $quality_checks->item_id               = $request->item_id;
            $quality_checks->check_date            = $request->check_date;
            $quality_checks->check_type            = $request->check_type;
            $quality_checks->result                = $request->result;
            $quality_checks->comments              = $request->comments;
            $quality_checks->workspace             = getActiveWorkSpace();
            $quality_checks->created_by            = creatorId();
            $quality_checks->update();
            event(new UpdateQualityCheck($request, $quality_checks));

            return redirect()->route('quality-checks.index')->with('success', __('The Quality Checks has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('quality-checks delete')) {
            $quality_checks = QualityCheck::find($id);
            if ($quality_checks->created_by == creatorId()  && $quality_checks->workspace == getActiveWorkSpace()) {
                event(new DestroyQualityCheck($quality_checks));

                $quality_checks->delete();
                return redirect()->route('quality-checks.index')->with('success', __('The Quality Checks has been deleted successfully.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
