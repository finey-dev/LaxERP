<?php

namespace Workdo\FixEquipment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Workdo\FixEquipment\Entities\AssetComponents;
use Workdo\FixEquipment\Entities\EquipmentCategory;
use Workdo\FixEquipment\Entities\FixAsset;
use Workdo\FixEquipment\Events\CreateComponent;
use Workdo\FixEquipment\Events\DestroyComponent;
use Workdo\FixEquipment\Events\UpdateComponent;
use Workdo\FixEquipment\DataTables\ComponentsDataTable;

class ComponentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(ComponentsDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('equipment components manage')) {
            return $dataTable->render('fix-equipment::component.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(Auth::user()->isAbleTo('equipment components create')){

            $categories = EquipmentCategory::where('workspace', getActiveWorkSpace())->where('category_type','Components')->where('created_by', creatorId())->get();
            $assets = FixAsset::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();

            return view('fix-equipment::component.create', compact('categories', 'assets'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->isAbleTo('equipment components create')){

            $validator = Validator::make(
                $request->all(),
                [
                    'title'      => 'required',
                    'category'   => 'required',
                    'asset'      => 'required',
                    'price'      => 'required|numeric|min:1',
                    'quantity'   => 'required|numeric|min:1',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $component = new AssetComponents();

            $component->title       = $request->title;
            $component->category    = $request->category;
            $component->asset       = $request->asset;
            $component->price       = $request->price;
            $component->quantity    = $request->quantity;
            $component->created_by  = creatorId();
            $component->workspace   = getActiveWorkSpace();
            $component->save();

            event(new CreateComponent($request, $component));

            return redirect()->back()->with('success', __('The component has been created successfully'));
        }
    }

    public function show($id)
    {
        return view('fix-equipment::show');
    }

    public function edit($id)
    {
        if(Auth::user()->isAbleTo('equipment components edit')){

            $component = AssetComponents::find($id);

            $categories = EquipmentCategory::where('workspace', getActiveWorkSpace())->where('category_type','Components')->where('created_by', creatorId())->get();
            $assets = FixAsset::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();

            return view('fix-equipment::component.edit', compact('component', 'categories', 'assets'));
        } else {
            return redirect()->back()->with( __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if(Auth::user()->isAbleTo('equipment components edit')){

            $validator = Validator::make(
                $request->all(),
                [
                    'title'      => 'required',
                    'category'   => 'required',
                    'asset'      => 'required',
                    'price'      => 'required|numeric|min:1',
                    'quantity'   => 'required|numeric|min:1',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $component = AssetComponents::find($id);

            $component->title       = $request->title;
            $component->category    = $request->category;
            $component->asset       = $request->asset;
            $component->price       = $request->price;
            $component->quantity    = $request->quantity;
            $component->save();

            event(new UpdateComponent($request, $component));


            return redirect()->back()->with('success', __('The component details are updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if(Auth::user()->isAbleTo('equipment components delete')){

            $component = AssetComponents::find($id);

            event(new DestroyComponent($component));

            $component->delete();

            return redirect()->back()->with('success', __('The component has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
