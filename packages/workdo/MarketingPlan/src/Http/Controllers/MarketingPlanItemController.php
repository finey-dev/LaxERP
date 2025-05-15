<?php

namespace Workdo\MarketingPlan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\MarketingPlan\Entities\MarketingPlan;
use Workdo\MarketingPlan\Entities\MarketingPlanItem;
use Workdo\ProductService\Entities\ProductService;
use Workdo\MarketingPlan\Events\CreateMarketingPlanItem;
use Workdo\MarketingPlan\Events\DestroyMarketingPlanItem;

class MarketingPlanItemController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('marketing-plan::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($id)
    {
        if(\Auth::user()->isAbleTo('marketingplan item create'))
        {
            $MarketingPlan = MarketingPlan::find($id);
            $item_types = MarketingPlanItem::$item_type;

            return view('marketing-plan::Item.create', compact('item_types', 'MarketingPlan'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request, $id)
    {
        $MarketingPlan = MarketingPlan::find($id);
        if(\Auth::user()->isAbleTo('marketingplan item create'))
        {
            $validator = \Validator::make(
            $request->all(), [
                        'item_type' => 'required',
                        'item' => 'required',
                    ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->route('marketing-plan-item.index')->with('error', $messages->first());
            }
            $marketingplanitem                    = new MarketingPlanItem();
            $marketingplanitem->marketing_plan_id = isset($MarketingPlan->id) ? $MarketingPlan->id :'';
            $marketingplanitem->item_type         = isset($request->item_type) ? $request->item_type :'';
            $marketingplanitem->item              = isset($request->item) ? $request->item :'';
            $marketingplanitem->workspace         = getActiveWorkSpace();
            $marketingplanitem->created_by        = creatorId();
            $marketingplanitem->save();

            event(new CreateMarketingPlanItem($request, $marketingplanitem));

            return redirect()->back()->with('success', __('The item has been created successfully.'));
        }
        else
        {
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
        return view('marketing-plan::show');
    }

    public function getItems(Request $request)
    {
        $item_type = $request->input('item_type');

        $items = ProductService::where('created_by', creatorId())
                            ->where('workspace_id', getActiveWorkSpace())
                            ->where('type', $item_type)
                            ->pluck('name', 'id');

        return response()->json($items);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('marketing-plan::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if(\Auth::user()->isAbleTo('marketingplan item delete'))
        {
            $marketingplanitem = MarketingPlanItem::find($id);
            if(!empty($marketingplanitem))
            {
                event(new DestroyMarketingPlanItem($marketingplanitem));

                $marketingplanitem->delete();

                return redirect()->back()->with('success', 'The item has been deleted.' );
            }
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
