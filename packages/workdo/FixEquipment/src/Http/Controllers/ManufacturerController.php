<?php

namespace Workdo\FixEquipment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Workdo\FixEquipment\Entities\Manufacturer;
use Workdo\FixEquipment\Events\CreateManufacturer;
use Workdo\FixEquipment\Events\DestroyManufacturer;
use Workdo\FixEquipment\Events\UpdateManufacturer;

class ManufacturerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if(Auth::user()->isAbleTo('asset manufacturers manage')){

            $manufacturers = Manufacturer::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();

            return view('fix-equipment::manufacturers.index', compact('manufacturers'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(Auth::user()->isAbleTo('asset manufacturers create')){
            return view('fix-equipment::manufacturers.create');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->isAbleTo('asset manufacturers create')){

            $validator = Validator::make(
                $request->all(),
                [
                    'manufacturer_title'  => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $manufacturer = new Manufacturer();

            $manufacturer->title       = $request->manufacturer_title;
            $manufacturer->created_by  = creatorId();
            $manufacturer->workspace   = getActiveWorkSpace();
            $manufacturer->save();

            event(new CreateManufacturer($request, $manufacturer));

            return redirect()->back()->with('success', __('The manufacturer has been created successfully'));
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
        if(Auth::user()->isAbleTo('asset manufacturers edit')){

            $manufacturer = Manufacturer::find($id);

            return view('fix-equipment::manufacturers.edit', compact('manufacturer'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if(Auth::user()->isAbleTo('asset manufacturers edit')){

            $validator = Validator::make(
                $request->all(),
                [
                    'manufacturer_title'  => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $manufacturer = Manufacturer::find($id);

            $manufacturer->title = $request->manufacturer_title;
            $manufacturer->save();

            event(new UpdateManufacturer($request, $manufacturer));

            return redirect()->back()->with('success', __('The manufacturer details are updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if(Auth::user()->isAbleTo('asset manufacturers delete')){

            $manufacturer = Manufacturer::find($id);

            event(new DestroyManufacturer($manufacturer));

            $manufacturer->delete();

            return redirect()->back()->with('success', __('The manufacturer has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
