<?php

namespace Workdo\Sales\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Sales\Entities\SalesAccount;
use Workdo\Sales\Entities\SalesAccountType;
use Workdo\Sales\Events\CreateSalesAccountType;
use Workdo\Sales\Events\DestroySalesAccountType;
use Workdo\Sales\Events\UpdateSalesAccountType;

class SalesAccountTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if(\Auth::user()->isAbleTo('salesaccounttype manage'))
        {
            $types = SalesAccountType::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->orderBy('id','desc')->get();

            return view('sales::account_type.index', compact('types'));
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
        if(\Auth::user()->isAbleTo('salesaccounttype create'))
        {
            return view('sales::account_type.create');
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
        if(\Auth::user()->isAbleTo('salesaccounttype create'))
        {
            $validator = \Validator::make(
                $request->all(), ['name' => 'required|string|max:40',]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $name                      = $request['name'];
            $salesaccounttype               = new SalesAccountType();
            $salesaccounttype->name         = $name;
            $salesaccounttype->workspace = getActiveWorkSpace();
            $salesaccounttype['created_by'] = creatorId();

            $salesaccounttype->save();
            event(new CreateSalesAccountType($request,$salesaccounttype));

            return redirect()->route('account_type.index')->with('success', __('The account type has been created successfully.'));
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
       //
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     *@return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        if(\Auth::user()->isAbleTo('salesaccounttype edit'))
        {
            $salesaccounttype = SalesAccountType::find($id);
            return view('sales::account_type.edit', compact('salesaccounttype'));
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
    public function update(Request $request,$id)
    {
        if(\Auth::user()->isAbleTo('salesaccounttype edit'))
        {
            $salesaccounttype = SalesAccountType::find($id);
            $validator = \Validator::make(
                $request->all(), ['name' => 'required|string|max:40',]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $salesaccounttype['name']       = $request->name;
            $salesaccounttype['workspace'] = getActiveWorkSpace();
            $salesaccounttype['created_by'] = creatorId();
            $salesaccounttype->update();

            event(new UpdateSalesAccountType($request,$salesaccounttype));

            return redirect()->route('account_type.index')->with(
                'success', __('The account type details are updated successfully.')
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
    public function destroy($id)
    {
        if(\Auth::user()->isAbleTo('salesaccounttype delete'))
        {
            $salesaccount = SalesAccount::where('type', '=', $id)->count();
            if($salesaccount == 0){

                $salesaccounttype = SalesAccountType::find($id);
                $salesaccounttype->delete();
                event(new DestroySalesAccountType($salesaccounttype));

                return redirect()->route('account_type.index')->with(
                    'success', 'The account type has been deleted.'
                );
            }
            else
            {
                return redirect()->back()->with('error', __('This account type is used on sales account.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
