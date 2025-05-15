<?php

namespace Workdo\FixEquipment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Workdo\FixEquipment\Entities\EquipmentCategory;
use Workdo\FixEquipment\Events\CreateCategory;
use Workdo\FixEquipment\Events\DestroyCategory;
use Workdo\FixEquipment\Events\UpdateCategory;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if(Auth::user()->isAbleTo('equipment categories manage')){

            $categories = EquipmentCategory::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();

            return view('fix-equipment::category.index', compact('categories'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(Auth::user()->isAbleTo('equipment categories create')){
            $equipmentCategory = new EquipmentCategory();

            $categoryTypes = $equipmentCategory->categoryTypes;

            return view('fix-equipment::category.create', compact('categoryTypes'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(Auth::user()->isAbleTo('equipment categories create')){


            $validator = Validator::make(
                $request->all(),
                [
                    'category_title' => 'required',
                    'category_type'  => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $category                 = new EquipmentCategory();
            $category->title          = $request->category_title;
            $category->category_type  = $request->category_type;
            $category->created_by     = creatorId();
            $category->workspace      = getActiveWorkSpace();
            $category->save();

            event(new CreateCategory($request, $category));

            return redirect()->back()->with('success', __('The category has been created successfully'));
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
        if(Auth::user()->isAbleTo('equipment categories edit')){

            $category       = EquipmentCategory::find($id);
            $categoryTypes  = $category->categoryTypes;

            return view('fix-equipment::category.edit', compact('category', 'categoryTypes'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if(Auth::user()->isAbleTo('equipment categories edit')){

            $validator = Validator::make(
                $request->all(),
                [
                    'category_title' => 'required',
                    'category_type'  => 'required'
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $category                 = EquipmentCategory::find($id);
            $category->title          = $request->category_title;
            $category->category_type  = $request->category_type;
            $category->save();

            event(new UpdateCategory($request, $category));

            return redirect()->back()->with('success', __('The category details are updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if(Auth::user()->isAbleTo('equipment categories delete')){

            $category = EquipmentCategory::find($id);

            event(new DestroyCategory($category));

            $category->delete();

            return redirect()->back()->with('success', __('The category has been deleted'));
        }
        else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
