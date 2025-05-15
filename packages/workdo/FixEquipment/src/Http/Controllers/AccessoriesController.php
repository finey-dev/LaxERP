<?php

namespace Workdo\FixEquipment\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Workdo\Account\Entities\ChartOfAccount;
use Workdo\FixEquipment\Entities\Accessories;
use Workdo\FixEquipment\Entities\EquipmentCategory;
use Workdo\FixEquipment\Entities\FixAsset;
use Workdo\FixEquipment\Entities\Manufacturer;
use Workdo\FixEquipment\Events\CreateAccessories;
use Workdo\FixEquipment\Events\DestroyAccessories;
use Workdo\FixEquipment\Events\UpdateAccessories;
use Workdo\FixEquipment\DataTables\AccessoriesDataTable;

class AccessoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function index(AccessoriesDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('accessories manage')) {
            return $dataTable->render('fix-equipment::accessories.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->isAbleTo('accessories create')) {

            $categories     = EquipmentCategory::where('workspace', getActiveWorkSpace())->where('category_type', 'Accessories')->where('created_by', creatorId())->get();
            $manufaturers   = Manufacturer::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $assets         = FixAsset::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $suppliers      = User::where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->where('type', '=', 'vendor')->get();

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
                $chartAccounts = [];
                $subAccounts = [];
            }

            return view('fix-equipment::accessories.create', compact('categories', 'manufaturers', 'assets', 'suppliers', 'chartAccounts', 'subAccounts'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('accessories create')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'title'         => 'required',
                    'category'      => 'required',
                    'asset'         => 'required',
                    'manufacturer'  => 'required',
                    'supplier'      => 'required',
                    'price'         => 'required|numeric|min:1',
                    'quantity'      => 'required|numeric|min:1'
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $accessories                = new Accessories();
            $accessories->title         = $request->title;
            $accessories->category      = $request->category;
            $accessories->asset         = $request->asset;
            $accessories->manufacturer  = $request->manufacturer;
            $accessories->supplier      = $request->supplier;
            $accessories->price         = $request->price;
            $accessories->quantity      = $request->quantity;
            $accessories->account       = isset($request->account) ? $request->account : '';
            $accessories->created_by    = creatorId();
            $accessories->workspace     = getActiveWorkSpace();
            $accessories->save();

            event(new CreateAccessories($request, $accessories));

            return redirect()->back()->with('success', __('The accessory has been created successfully'));
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
        if (Auth::user()->isAbleTo('accessories edit')) {

            $accessories    = Accessories::find($id);
            $categories     = EquipmentCategory::where('workspace', getActiveWorkSpace())->where('category_type', 'Accessories')->where('created_by', creatorId())->get();
            $suppliers      = User::where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->where('type', '=', 'vendor')->get();
            $manufaturers   = Manufacturer::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $assets         = FixAsset::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();

            if (module_is_active('DoubleEntry')) {
                $account    = ChartOfAccount::find($accessories->account);
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
                $chartAccounts  = [];
                $subAccounts    = [];
                $account        = '';
            }

            return view('fix-equipment::accessories.edit', compact('accessories', 'categories', 'manufaturers', 'assets', 'suppliers', 'account', 'chartAccounts', 'subAccounts'));
        } else {
            return redirect()->back()->with( __('Permission denied.'));
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
        if (Auth::user()->isAbleTo('accessories edit')) {

            $validator = Validator::make(
                $request->all(),
                [
                    'title'         => 'required',
                    'category'      => 'required',
                    'asset'         => 'required',
                    'manufacturer'  => 'required',
                    'supplier'      => 'required',
                    'price'         => 'required|numeric|min:1',
                    'quantity'      => 'required|numeric|min:1'
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $accessories                = Accessories::find($id);
            $accessories->title         = $request->title;
            $accessories->category      = $request->category;
            $accessories->asset         = $request->asset;
            $accessories->manufacturer  = $request->manufacturer;
            $accessories->price         = $request->price;
            $accessories->price         = $request->price;
            $accessories->quantity      = $request->quantity;
            $accessories->account       = isset($request->account) ? $request->account : '';
            $accessories->save();

            event(new UpdateAccessories($request, $accessories));

            return redirect()->back()->with('success', __('The accessory details are updated successfully'));
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
        if (Auth::user()->isAbleTo('accessories delete')) {

            $accessories = Accessories::find($id);

            event(new DestroyAccessories($accessories));

            $accessories->delete();

            return redirect()->back()->with('success', __('The accessory has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
