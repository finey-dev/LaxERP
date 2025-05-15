<?php

namespace Workdo\FixEquipment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Workdo\FixEquipment\Entities\AssetComponents;
use Workdo\FixEquipment\Entities\FixAsset;
use Workdo\FixEquipment\Entities\PreDefinedKit;
use Workdo\FixEquipment\Events\CreatePreDefinedKit;
use Workdo\FixEquipment\Events\DestroyPreDefinedKit;
use Workdo\FixEquipment\Events\UpdatePreDefinedKit;
use Workdo\FixEquipment\DataTables\PreDefinedKitDataTable;

class PreDefinedKitController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function index(PreDefinedKitDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('predefined kit manage')) {
            return $dataTable->render('fix-equipment::predefinedkit.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(Auth::user()->isAbleTo('predefined kit create')){

            $components = AssetComponents::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $assets = FixAsset::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();

            return view('fix-equipment::predefinedkit.create', compact('components', 'assets'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->isAbleTo('predefined kit create')){

            $validator = Validator::make(
                $request->all(),
                [
                    'kit_title'  => 'required',
                    'asset'      => 'required',
                    'component'  => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $kit             = new PreDefinedKit();
            $kit->title      = $request->kit_title;
            $kit->asset      = $request->asset;
            $kit->component  = $request->component;
            $kit->created_by = creatorId();
            $kit->workspace  = getActiveWorkSpace();
            $kit->save();

            event(new CreatePreDefinedKit($request, $kit));

            return redirect()->back()->with('success', __('The pre defined kit has been created successfully'));
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
        if(Auth::user()->isAbleTo('predefined kit edit')){

            $kit        = PreDefinedKit::find($id);
            $components = AssetComponents::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $assets     = FixAsset::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();

            return view('fix-equipment::predefinedkit.edit', compact('kit', 'components', 'assets'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if(Auth::user()->isAbleTo('predefined kit edit')){

            $validator = Validator::make(
                $request->all(),
                [
                    'kit_title'  => 'required',
                    'asset'      => 'required',
                    'component'  => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $kit = PreDefinedKit::find($id);

            $kit->title     = $request->kit_title;
            $kit->asset     = $request->asset;
            $kit->component = $request->component;
            $kit->save();

            event(new UpdatePreDefinedKit($request, $kit));

            return redirect()->back()->with('success', __('The pre defined kit details are updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if(Auth::user()->isAbleTo('predefined kit delete')){

            $kit = PreDefinedKit::find($id);

            event(new DestroyPreDefinedKit($kit));

            $kit->delete();

            return redirect()->back()->with('success', __('The pre defined kit has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
