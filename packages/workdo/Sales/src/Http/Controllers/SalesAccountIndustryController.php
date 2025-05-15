<?php

namespace Workdo\Sales\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Sales\Entities\AccountIndustry;
use Workdo\Sales\Entities\SalesAccount;
use Workdo\Sales\Events\CreateSalesAccountIndustry;
use Workdo\Sales\Events\DestroySalesAccountIndustry;
use Workdo\Sales\Events\UpdateSalesAccountIndustry;

class SalesAccountIndustryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if(\Auth::user()->isAbleTo('accountindustry manage'))
        {
        $industrys = AccountIndustry::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->orderBy('id','desc')->get();

        return view('sales::account_industry.index', compact('industrys'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if(\Auth::user()->isAbleTo('accountindustry create'))
        {
        return view('sales::account_industry.create');
        }
        else
        {
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
        if(\Auth::user()->isAbleTo('accountindustry create'))
        {
            $validator = \Validator::make(
                $request->all(), ['name' => 'required|string|max:40',]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
        $name                          = $request['name'];
        $accountIndustry               = new accountIndustry();
        $accountIndustry->name         = $name;
        $accountIndustry['workspace'] = getActiveWorkSpace();
        $accountIndustry['created_by'] = creatorId();
        $accountIndustry->save();
        event(new CreateSalesAccountIndustry($request,$accountIndustry));

        return redirect()->route('account_industry.index')->with('success', 'The account industry has been created successfully.');
        }
        else
        {
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
        return view('sales::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(accountIndustry $accountIndustry)
    {
        if(\Auth::user()->isAbleTo('accountindustry edit'))
        {
        return view('sales::account_industry.edit', compact('accountIndustry'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request,  accountIndustry $accountIndustry)
    {
        if(\Auth::user()->isAbleTo('accountindustry edit'))
        {
            $validator = \Validator::make(
                $request->all(), ['name' => 'required|string|max:40',]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $accountIndustry['name'] = $request->name;
            $accountIndustry['workspace'] = getActiveWorkSpace();
            $accountIndustry['created_by'] = creatorId();
            $accountIndustry->update();
            event(new UpdateSalesAccountIndustry($request,$accountIndustry));

            return redirect()->route('account_industry.index')->with(
                'success', __('The account industry details are updated successfully.')
            );
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
    public function destroy(accountIndustry $accountIndustry)
    {
        if(\Auth::user()->isAbleTo('accountindustry delete'))
        {

            $salesaccount = SalesAccount::where('industry', '=', $accountIndustry->id)->count();
            if($salesaccount == 0){
                event(new DestroySalesAccountIndustry($accountIndustry));

                $accountIndustry->delete();

                return redirect()->route('account_industry.index')->with('success', __('The account industry has been deleted.'));
            }
            else
            {

                return redirect()->back()->with('error', __('This account industry is used on sales account.'));
            }

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
