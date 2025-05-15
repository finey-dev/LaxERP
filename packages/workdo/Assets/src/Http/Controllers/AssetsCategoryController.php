<?php

namespace Workdo\Assets\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Assets\Entities\AssetsCategory;
use Workdo\Assets\Events\CreateAssetsCategory;
use Workdo\Assets\Events\UpdateAssetsCategory;
use Workdo\Assets\Events\DestroyAssetsCategory;

class AssetsCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (\Auth::user()->isAbleTo('assets category manage')) {
            $categoryies = AssetsCategory::where('created_by',creatorId())->where('workspace', getActiveWorkSpace())->get();

            return view('assets::category.index',compact('categoryies'));
        }
        else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if (\Auth::user()->isAbleTo('assets category create')) {
            return view('assets::category.create');
        }
        else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (\Auth::user()->isAbleTo('assets category create')) {
            $validator = \Validator::make(
                $request->all(), [
                    'name' => 'required',
                    ]
                );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('assets-category.index')->with('error', $messages->first());
            }

            $category             = new AssetsCategory();
            $category->name       = $request->name;
            $category->workspace  = getActiveWorkSpace();
            $category->created_by = creatorId();
            $category->save();

            event(new CreateAssetsCategory($request,$category));

            return redirect()->route('assets-category.index')->with('success', __('The asset category has been created successfully.'));
        }
        else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('assets::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (\Auth::user()->isAbleTo('assets category edit')) {
            $category = AssetsCategory::find($id);

            return view('assets::category.edit',compact('category'));
        }
        else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if(\Auth::user()->isAbleTo('assets category edit'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $category = AssetsCategory::find($id);

            $category->name       = $request->name;
            $category->workspace  = getActiveWorkSpace();
            $category->created_by = creatorId();
            $category->save();

            event(new UpdateAssetsCategory($request,$category));

            return redirect()->route('assets-category.index')->with('success', __('The asset category details are updated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if(\Auth::user()->isAbleTo('assets category delete'))
        {
            $category = AssetsCategory::find($id);

            event(new DestroyAssetsCategory($category));

            $category->delete();

            return redirect()->route('assets-category.index')->with('success', 'The asset category has been deleted.');
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }

    }
}
