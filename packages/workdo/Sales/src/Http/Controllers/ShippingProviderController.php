<?php

namespace Workdo\Sales\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Sales\Entities\Quote;
use Workdo\Sales\Entities\SalesInvoice;
use Workdo\Sales\Entities\SalesOrder;
use Workdo\Sales\Entities\ShippingProvider;
use Workdo\Sales\Events\CreateShippingProvider;
use Workdo\Sales\Events\DestroyShippingProvider;
use Workdo\Sales\Events\UpdateShippingProvider;

class ShippingProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (\Auth::user()->isAbleTo('shippingprovider manage')) {
            $shipping_providers = ShippingProvider::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->orderBy('id','desc')->get();

            return view('sales::shipping_provider.index', compact('shipping_providers'));
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
        if (\Auth::user()->isAbleTo('shippingprovider create')) {
            return view('sales::shipping_provider.create');
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
        if (\Auth::user()->isAbleTo('shippingprovider create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|string|max:120',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $shippingprovider               = new ShippingProvider();
            $shippingprovider->name         = $request['name'];
            $shippingprovider->website      = $request['website'];
            $shippingprovider['workspace']  = getActiveWorkSpace();
            $shippingprovider['created_by'] = creatorId();
            $shippingprovider->save();
            event(new CreateShippingProvider($request, $shippingprovider));

            return redirect()->route('shipping_provider.index')->with('success', __('The shipping provider has been created successfully.'));
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
        return view('sales::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(ShippingProvider $shippingProvider)
    {
        if (\Auth::user()->isAbleTo('shippingprovider edit')) {
            return view('sales::shipping_provider.edit', compact('shippingProvider'));
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
    public function update(Request $request, ShippingProvider $shippingProvider)
    {
        if (\Auth::user()->isAbleTo('shippingprovider edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|string|max:120',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $name                           = $request['name'];
            $shippingProvider->name         = $name;
            $shippingProvider->website      = $request['website'];
            $shippingProvider['workspace']  = getActiveWorkSpace();
            $shippingProvider['created_by'] = creatorId();
            $shippingProvider->update();
            event(new UpdateShippingProvider($request, $shippingProvider));

            return redirect()->route('shipping_provider.index')->with('success', __('The shipping provider details are updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(ShippingProvider $shippingProvider)
    {
        if (\Auth::user()->isAbleTo('shippingprovider delete')) {
            $quote = Quote::where('shipping_provider', '=', $shippingProvider->id)->count();
            $salesorder = SalesOrder::where('shipping_provider', '=', $shippingProvider->id)->count();
            $salesinvoice = SalesInvoice::where('shipping_provider', '=', $shippingProvider->id)->count();
            if ($quote == 0 && $salesorder == 0 && $salesinvoice == 0) {
                event(new DestroyShippingProvider($shippingProvider));

                $shippingProvider->delete();

                return redirect()->route('shipping_provider.index')->with(
                    'success',
                    'The shipping provider has been deleted.'
                );
            } else {
                return redirect()->back()->with('error', __('This shipping provides is used on quote , sales order and sales invoice.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
