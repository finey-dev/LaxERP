<?php

namespace Workdo\FixEquipment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Workdo\Account\Entities\ChartOfAccount;
use Workdo\FixEquipment\Entities\AssetLicense;
use Workdo\FixEquipment\Entities\EquipmentCategory;
use Workdo\FixEquipment\Entities\FixAsset;
use Workdo\FixEquipment\Events\CreateLicence;
use Workdo\FixEquipment\Events\DestroyLicence;
use Workdo\FixEquipment\Events\UpdateLicence;
use Workdo\FixEquipment\DataTables\LicensesDataTable;

class LicenceController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function index(LicensesDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('asset licenses manage')) {
            return $dataTable->render('fix-equipment::licenses.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->isAbleTo('asset licenses create')) {

            $assets     = FixAsset::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $categories = EquipmentCategory::where('workspace', getActiveWorkSpace())->where('category_type', 'Licence')->where('created_by', creatorId())->get();

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

            return view('fix-equipment::licenses.create', compact('assets', 'categories', 'chartAccounts', 'subAccounts'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('asset licenses create')) {

            $validator = Validator::make(
                $request->all(),
                [
                    'title'            => 'required',
                    'category'         => 'required',
                    'asset'            => 'required',
                    'license_number'   => 'required',
                    'purchase_date'    => 'required',
                    'purchase_price'   => 'required|numeric|min:1',
                    'expire_date'      => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $license                    = new AssetLicense();

            $license->title             = $request->title;
            $license->category          = $request->category;
            $license->asset             = $request->asset;
            $license->license_number    = $request->license_number;
            $license->purchase_date     = $request->purchase_date;
            $license->purchase_price    = $request->purchase_price;
            $license->expire_date       = $request->expire_date;
            $license->account           = isset($request->account) ? $request->account : '';
            $license->created_by        = creatorId();
            $license->workspace         = getActiveWorkSpace();
            $license->save();

            event(new CreateLicence($request, $license));

            return redirect()->back()->with('success', __('The license has been created successfully'));
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
        if (Auth::user()->isAbleTo('asset licenses edit')) {

            $license    = AssetLicense::find($id);
            $assets     = FixAsset::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $categories = EquipmentCategory::where('workspace', getActiveWorkSpace())->where('category_type', 'Licence')->where('created_by', creatorId())->get();

            if (module_is_active('DoubleEntry')) {
                $account = ChartOfAccount::find($license->account);
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

            return view('fix-equipment::licenses.edit', compact('license', 'assets', 'categories', 'account', 'chartAccounts', 'subAccounts'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('asset licenses edit')) {

            $validator = Validator::make(
                $request->all(),
                [
                    'title'             => 'required',
                    'category'          => 'required',
                    'asset'             => 'required',
                    'license_number'    => 'required',
                    'purchase_date'     => 'required',
                    'purchase_price'    => 'required|numeric|min:1',
                    'expire_date'       => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $license                    = AssetLicense::find($id);
            $license->title             = $request->title;
            $license->category          = $request->category;
            $license->asset             = $request->asset;
            $license->license_number    = $request->license_number;
            $license->purchase_date     = $request->purchase_date;
            $license->purchase_price    = $request->purchase_price;
            $license->expire_date       = $request->expire_date;
            $license->account           = isset($request->account) ? $request->account : '';
            $license->save();

            event(new UpdateLicence($license, $request));

            return redirect()->back()->with('success', __('The license details are updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('asset licenses delete')) {

            $license = AssetLicense::find($id);

            event(new DestroyLicence($license));

            $license->delete();

            return redirect()->back()->with('success', __('The license has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
