<?php

namespace Workdo\FixEquipment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Workdo\FixEquipment\Entities\Depreciation;
use Workdo\FixEquipment\Events\CreateDepreciation;
use Workdo\FixEquipment\Events\DestroyDepreciation;
use Workdo\FixEquipment\Events\UpdateDepreciation;

class DepreciationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if(Auth::user()->isAbleTo('depreciation manage')){

            $depteciations = Depreciation::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();

            return view('fix-equipment::depreciation.index', compact('depteciations'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(Auth::user()->isAbleTo('depreciation create')){

            return view('fix-equipment::depreciation.create');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->isAbleTo('depreciation create')){

            $validator = Validator::make(
                $request->all(),
                [
                    'depreciation_title'    => 'required',
                    'depteciation_rate'     => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $depreciation = new Depreciation();

            $depreciation->title      = $request->depreciation_title;
            $depreciation->rate       = $request->depteciation_rate;
            $depreciation->created_by = creatorId();
            $depreciation->workspace  = getActiveWorkSpace();
            $depreciation->save();

            event(new CreateDepreciation($request, $depreciation));

            return redirect()->back()->with('success', __('The depreciation has been created successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id)
    {
        return view('fix-equipment::show');
    }

    public function edit($id)
    {
        if(Auth::user()->isAbleTo('depreciation edit')){

            $depreciation = Depreciation::find($id);

            return view('fix-equipment::depreciation.edit' , compact('depreciation'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if(Auth::user()->isAbleTo('depreciation edit')){

            $validator = Validator::make(
                $request->all(),
                [
                    'depreciation_title'   => 'required',
                    'depteciation_rate'    => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $depreciation = Depreciation::find($id);

            $depreciation->title = $request->depreciation_title;
            $depreciation->rate  = $request->depteciation_rate;
            $depreciation->save();

            event(new UpdateDepreciation($request, $depreciation));

            return redirect()->back()->with('success', __('The depreciation details are updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if(Auth::user()->isAbleTo('depreciation delete')){

            $depreciation = Depreciation::find($id);

            event(new DestroyDepreciation($depreciation));

            $depreciation->delete();

            return redirect()->back()->with('success', __('The depreciation has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
