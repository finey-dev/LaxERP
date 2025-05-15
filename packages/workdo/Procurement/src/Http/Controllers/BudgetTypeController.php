<?php

namespace Workdo\Procurement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Procurement\Entities\BudgetType;
use Workdo\Procurement\Events\CreateBudgetType;
use Workdo\Procurement\Events\DestroyBudgetType;
use Workdo\Procurement\Events\UpdateBudgetType;
use Illuminate\Support\Facades\Auth;

class BudgetTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (Auth::user()->isAbleTo('budgettype manage')) {
            $budgettypes = BudgetType::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();
            return view('procurement::budgettype.index', compact('budgettypes'));
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
        if (Auth::user()->isAbleTo('budgettype create')) {
            return view('procurement::budgettype.create');
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
        if (Auth::user()->isAbleTo('budgettype create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:30',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $budgettype = new BudgetType();
            $budgettype->name = $request->name;
            $budgettype->workspace = getActiveWorkSpace();
            $budgettype->created_by = creatorId();
            $budgettype->save();

            event(new CreateBudgetType($request, $budgettype));

            return redirect()->route('budgettype.index')->with('success', __('The budget type  has been created successfully'));
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
        return view('procurement::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('budgettype edit')) {
            $budgettype = BudgetType::find($id);
            if ($budgettype) {
                if ($budgettype->created_by == creatorId() && $budgettype->workspace == getActiveWorkSpace()) {
                    return view('procurement::budgettype.edit', compact('budgettype'));
                } else {
                    return response()->json(['error' => __('Permission denied.')], 401);
                }
            } else {
                return response()->json(['error' => __('The budget type is not found')], 401);
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
        if (Auth::user()->isAbleTo('budgettype edit')) {
            $budgettype = BudgetType::find($id);
            if ($budgettype) {
                if ($budgettype->created_by == creatorId() && $budgettype->workspace == getActiveWorkSpace()) {
                    $validator = \Validator::make(
                        $request->all(),
                        [
                            'name' => 'required|max:20',

                        ]
                    );

                    if ($validator->fails()) {
                        $messages = $validator->getMessageBag();

                        return redirect()->back()->with('error', $messages->first());
                    }

                    $budgettype->name = $request->name;
                    $budgettype->save();

                    event(new UpdateBudgetType($request, $budgettype));

                    return redirect()->route('budgettype.index')->with('success', __('The budget type are updated successfully.'));
                } else {
                    return redirect()->back()->with('error', __('Permission denied.'));
                }
            } else {
                return redirect()->back()->with('error', __('The budget type is not found.'));
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
        if (Auth::user()->isAbleTo('budgettype delete')) {
            $budgettype = BudgetType::find($id);
            if ($budgettype) {
                if ($budgettype->created_by == creatorId() && $budgettype->workspace == getActiveWorkSpace()) {

                    event(new DestroyBudgetType($budgettype));

                    $budgettype->delete();

                    return redirect()->route('budgettype.index')->with('success', __('The budget type has been deleted.'));
                } else {
                    return redirect()->back()->with('error', __('Permission denied.'));
                }
            } else {
                return redirect()->back()->with('error', __('The budget type is not found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
