<?php

namespace Workdo\Planning\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use Workdo\Planning\Entities\PlanningCategories;
use Workdo\Planning\Entities\PlanningChallenge;
use Workdo\Planning\Events\CreatePlanningCategories;
use Workdo\Planning\Events\DestroyPlanningCategories;
use Workdo\Planning\Events\UpdatePlanningCategories;

class PlanningCetegoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (Auth::user()->isAbleTo('planning categories manage')) {

        $PlanningCategories = PlanningCategories::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();

        return view('planning::planningcategories.index', compact('PlanningCategories'));
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
        if (Auth::user()->isAbleTo('planning categories create')) {
            return view('planning::planningcategories.create');
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
        if (Auth::user()->isAbleTo('planning categories create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $PlanningCategories             = new PlanningCategories();

            $PlanningCategories->title       = $request->name;
            $PlanningCategories->workspace  = getActiveWorkSpace();
            $PlanningCategories->created_by = creatorId();
            $PlanningCategories->save();
            event(new CreatePlanningCategories($request, $PlanningCategories));
            return redirect()->route('planning-categories.index')->with('success', __('The Category has been created successfully'));
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
        return view('planning::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $PlanningCategories = PlanningCategories::find($id);

        if (Auth::user()->isAbleTo('planning categories edit')) {
            if ($PlanningCategories->created_by == creatorId() &&  $PlanningCategories->workspace  == getActiveWorkSpace()) {
                return view('planning::planningcategories.edit', compact('PlanningCategories'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
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

        $PlanningCategories = PlanningCategories::find($id);

        if (Auth::user()->isAbleTo('planning categories edit')) {
            if ($PlanningCategories->created_by == creatorId() &&  $PlanningCategories->workspace  == getActiveWorkSpace()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $PlanningCategories->title       = $request->name;
                $PlanningCategories->workspace  = getActiveWorkSpace();
                $PlanningCategories->created_by = creatorId();

                $PlanningCategories->save();

                event(new UpdatePlanningCategories($request, $PlanningCategories));
                return redirect()->route('planning-categories.index')->with('success', __('The Category details are updated successfully'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
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

        $PlanningCategories = PlanningCategories::find($id);

        if (Auth::user()->isAbleTo('planning categories delete')) {

            if ($PlanningCategories->created_by == creatorId() &&  $PlanningCategories->workspace  == getActiveWorkSpace()) {

                $Challenge     = PlanningChallenge::where('category',$PlanningCategories->id)->where('workspace',getActiveWorkSpace())->get();
                if(count($Challenge) == 0)
                {
                    event(new DestroyPlanningCategories($PlanningCategories));
                    $PlanningCategories->delete();
                }
                else
                {
                    return redirect()->route('planning-categories.index')->with('error', __('This category has challenge. Please remove the challenge from this category.'));
                }
                return redirect()->route('planning-categories.index')->with('success', __('The Category has been deleted'));
            } else {

                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {

            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
