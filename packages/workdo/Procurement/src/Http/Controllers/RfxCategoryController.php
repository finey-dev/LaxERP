<?php

namespace Workdo\Procurement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Procurement\Entities\Rfx;
use Workdo\Procurement\Entities\RfxCategory;
use Illuminate\Support\Facades\Auth;
use Workdo\Procurement\Events\CreateRFxCategory;
use Workdo\Procurement\Events\DestroyRFxCategory;
use Workdo\Procurement\Events\UpdateRFxCategory;

class RfxCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (Auth::user()->isAbleTo('rfxcategory manage')) {
            $categories = RfxCategory::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();

            return view('procurement::rfxCategory.index', compact('categories'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if (Auth::user()->isAbleTo('rfxcategory create')) {
            return view('procurement::rfxCategory.create');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('rfxcategory create')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:300',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $rfxCategory = new RfxCategory();
            $rfxCategory->name = $request->name;
            $rfxCategory->workspace = getActiveWorkSpace();
            $rfxCategory->created_by = creatorId();
            $rfxCategory->save();

            event(new CreateRFxCategory($request, $rfxCategory));

            return redirect()->back()->with('success', __('The rfx category has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('rfxcategory edit')) {
            $rfxCategory = RfxCategory::findOrFail($id);
            if ($rfxCategory) {
                return view('procurement::rfxCategory.edit', compact('rfxCategory'));
            } else {
                return response()->json(['error' => __('The rfx category is not found.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
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

        if (Auth::user()->isAbleTo('rfxcategory edit')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:300',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $rfxCategory = RfxCategory::findOrFail($id);
            if ($rfxCategory) {
                $rfxCategory->name = $request->name;
                $rfxCategory->save();

                event(new UpdateRFxCategory($request, $rfxCategory));

                return redirect()->back()->with('success', __('The rfx category are updated successfully.'));
            } else {
                return redirect()->back()->with('error', __('The rfx category is not found.'));
            }
        } else {
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

        if (Auth::user()->isAbleTo('rfxcategory delete')) {
            $rfxCategory = RfxCategory::findOrFail($id);
            if ($rfxCategory) {
                if ($rfxCategory->created_by == creatorId() && $rfxCategory->workspace == getActiveWorkSpace()) {
                    $rfxs = Rfx::where('category', $rfxCategory->id)->where('workspace', getActiveWorkSpace())->get();
                    if (count($rfxs) == 0) {
                        event(new DestroyRFxCategory($rfxCategory));

                        $rfxCategory->delete();
                    } else {
                        return redirect()->back()->with('error', __('This RFx category has RFx. Please remove the RFx from this RFx category.'));
                    }
                    return redirect()->back()->with('success', __('The rfx category has been deleted.'));
                } else {
                    return redirect()->back()->with('error', __('Permission denied.'));
                }
            } else {
                return redirect()->back()->with('error', __('The rfx category is not found.'));
            }

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
