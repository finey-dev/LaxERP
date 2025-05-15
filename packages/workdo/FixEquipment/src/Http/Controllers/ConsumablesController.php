<?php

namespace Workdo\FixEquipment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Workdo\Account\Entities\ChartOfAccount;
use Workdo\FixEquipment\Entities\Consumables;
use Workdo\FixEquipment\Entities\EquipmentCategory;
use Workdo\FixEquipment\Entities\FixAsset;
use Workdo\FixEquipment\Entities\Manufacturer;
use Workdo\FixEquipment\Events\CreateConsumables;
use Workdo\FixEquipment\Events\DestroyConsumables;
use Workdo\FixEquipment\Events\UpdateConsumables;
use Workdo\FixEquipment\DataTables\ConsumablesDataTable;

class ConsumablesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(ConsumablesDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('consumables manage')) {
            return $dataTable->render('fix-equipment::consumables.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->isAbleTo('consumables create')) {

            $assets         = FixAsset::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $categories     = EquipmentCategory::where('workspace', getActiveWorkSpace())->where('category_type', 'Consumables')->where('created_by', creatorId())->get();
            $manufacturers  = Manufacturer::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();

            if (module_is_active('DoubleEntry')) {
                $chartAccounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_accounts.parent')
                    ->where('parent', '=', 0)
                    ->where('created_by', creatorId())
                    ->where('workspace', getActiveWorkSpace())
                    ->get()->toarray();


                $subAccounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_account_parents.account');
                $subAccounts->leftjoin('chart_of_account_parents', 'chart_of_accounts.parent', 'chart_of_account_parents.id');
                $subAccounts->where('chart_of_accounts.parent', '!=', 0);
                $subAccounts->where('chart_of_accounts.created_by', creatorId());
                $subAccounts->where('chart_of_accounts.workspace', getActiveWorkSpace());
                $subAccounts = $subAccounts->get()->toArray();
            } else {
                $chartAccounts  = [];
                $subAccounts    = [];
            }

            return view('fix-equipment::consumables.create', compact('assets', 'categories', 'manufacturers', 'subAccounts', 'chartAccounts'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('consumables create')) {

            $validator = Validator::make(
                $request->all(),
                [
                    'title'        => 'required',
                    'category'     => 'required',
                    'asset'        => 'required',
                    'manufacturer' => 'required',
                    'date'         => 'required',
                    'price'        => 'required',
                    'quantity'     => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $consumables                = new Consumables();
            $consumables->title         = $request->title;
            $consumables->category      = $request->category;
            $consumables->asset         = $request->asset;
            $consumables->manufacturer  = $request->manufacturer;
            $consumables->date          = $request->date;
            $consumables->price         = $request->price;
            $consumables->quantity      = $request->quantity;
            $consumables->account       = isset($request->account) ? $request->account : '';
            $consumables->created_by    = creatorId();
            $consumables->workspace     = getActiveWorkSpace();
            $consumables->save();

            event(new CreateConsumables($request, $consumables));

            return redirect()->back()->with('success', __('The consumable has been created successfully'));
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
        if (Auth::user()->isAbleTo('consumables edit')) {

            $consumables    = Consumables::find($id);

            $assets         = FixAsset::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $categories     = EquipmentCategory::where('workspace', getActiveWorkSpace())->where('category_type', 'Consumables')->where('created_by', creatorId())->get();
            $manufacturers  = Manufacturer::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();

            if (module_is_active('DoubleEntry')) {
                $account = ChartOfAccount::find($consumables->account);
                $chartAccounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_accounts.parent')
                    ->where('parent', '=', 0)
                    ->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()
                    ->toarray();

                $subAccounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_account_parents.account');
                $subAccounts->leftjoin('chart_of_account_parents', 'chart_of_accounts.parent', 'chart_of_account_parents.id');
                $subAccounts->where('chart_of_accounts.parent', '!=', 0);
                $subAccounts->where('chart_of_accounts.created_by', creatorId());
                $subAccounts->where('chart_of_accounts.workspace', getActiveWorkSpace());
                $subAccounts = $subAccounts->get()->toArray();
            } else {
                $chartAccounts = [];
                $subAccounts = [];
                $account = '';
            }

            return view('fix-equipment::consumables.edit', compact('consumables', 'assets', 'categories', 'manufacturers', 'account', 'chartAccounts', 'subAccounts'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('consumables edit')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'title'         => 'required',
                    'category'      => 'required',
                    'asset'         => 'required',
                    'manufacturer'  => 'required',
                    'date'          => 'required',
                    'price'         => 'required|numeric|min:1',
                    'quantity'      => 'required|numeric|min:1',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $consumables                = Consumables::find($id);
            $consumables->title         = $request->title;
            $consumables->category      = $request->category;
            $consumables->asset         = $request->asset;
            $consumables->manufacturer  = $request->manufacturer;
            $consumables->date          = $request->date;
            $consumables->price         = $request->price;
            $consumables->quantity      = $request->quantity;
            $consumables->account       = isset($request->account) ? $request->account : '';
            $consumables->save();

            event(new UpdateConsumables($request, $consumables));

            return redirect()->back()->with('success', __('The consumable details are updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('consumables delete')) {

            $consumables = Consumables::find($id);

            event(new DestroyConsumables($consumables));

            $consumables->delete();

            return redirect()->back()->with('success', __('The consumable has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
