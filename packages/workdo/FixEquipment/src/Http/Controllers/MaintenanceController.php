<?php

namespace Workdo\FixEquipment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Workdo\Account\Entities\ChartOfAccount;
use Workdo\FixEquipment\Entities\FixAsset;
use Workdo\FixEquipment\Entities\Maintenance;
use Workdo\FixEquipment\Events\CreateMaintenance;
use Workdo\FixEquipment\Events\DestroyMaintenance;
use Workdo\FixEquipment\Events\UpdateMaintenance;
use Workdo\FixEquipment\DataTables\MaintenanceDataTable;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function index(MaintenanceDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('equipment maintenance manage')) {
            return $dataTable->render('fix-equipment::maintenance.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->isAbleTo('equipment maintenance create')) {

            $assets = FixAsset::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();

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

            return view('fix-equipment::maintenance.create', compact('assets', 'chartAccounts', 'subAccounts'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('equipment maintenance create')) {

            $validator = Validator::make(
                $request->all(),
                [
                    'maintenance_type'  => 'required',
                    'asset'             => 'required',
                    'price'             => 'required|numeric|min:1',
                    'maintenance_date'  => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $maintenance                    = new Maintenance();
            $maintenance->maintenance_type  = $request->maintenance_type;
            $maintenance->asset             = $request->asset;
            $maintenance->price             = $request->price;
            $maintenance->maintenance_date  = $request->maintenance_date;
            $maintenance->description       = $request->description;
            $maintenance->account           = isset($request->account) ? $request->account : '';
            $maintenance->created_by        = creatorId();
            $maintenance->workspace         = getActiveWorkSpace();
            $maintenance->save();

            event(new CreateMaintenance($request, $maintenance));

            return redirect()->back()->with('success', __('The maintenance has been created successfully'));
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
        if (Auth::user()->isAbleTo('equipment maintenance edit')) {

            $maintenance = Maintenance::find($id);

            $assets = FixAsset::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();

            if (module_is_active('DoubleEntry')) {
                $account = ChartOfAccount::find($maintenance->account);
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

            return view('fix-equipment::maintenance.edit', compact('maintenance', 'assets', 'account', 'chartAccounts', 'subAccounts'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('equipment maintenance edit')) {

            $validator = Validator::make(
                $request->all(),
                [
                    'maintenance_type'  => 'required',
                    'asset'             => 'required',
                    'price'             => 'required|numeric|min:1',
                    'maintenance_date'  => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $maintenance                    = Maintenance::find($id);
            $maintenance->maintenance_type  = $request->maintenance_type;
            $maintenance->asset             = $request->asset;
            $maintenance->price             = $request->price;
            $maintenance->maintenance_date  = $request->maintenance_date;
            $maintenance->description       = $request->description;
            $maintenance->account           = isset($request->account) ? $request->account : '';
            $maintenance->save();

            event(new UpdateMaintenance($request, $maintenance));

            return redirect()->back()->with('success', __('The maintenance details are updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('equipment maintenance delete')) {

            $maintenance = Maintenance::find($id);

            event(new DestroyMaintenance($maintenance));

            $maintenance->delete();

            return redirect()->back()->with('success', __('The maintenance has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
