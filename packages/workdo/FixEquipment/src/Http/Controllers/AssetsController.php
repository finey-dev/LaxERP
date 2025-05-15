<?php

namespace Workdo\FixEquipment\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Workdo\Account\Entities\ChartOfAccount;
use Workdo\FixEquipment\Entities\Accessories;
use Workdo\FixEquipment\Entities\AssetComponents;
use Workdo\FixEquipment\Entities\AssetLicense;
use Workdo\FixEquipment\Entities\Consumables;
use Workdo\FixEquipment\Entities\Depreciation;
use Workdo\FixEquipment\Entities\EquipmentCategory;
use Workdo\FixEquipment\Entities\EquipmentLocation;
use Workdo\FixEquipment\Entities\EquipmentStatus;
use Workdo\FixEquipment\Entities\FixAsset;
use Workdo\FixEquipment\Entities\Maintenance;
use Workdo\FixEquipment\Entities\Manufacturer;
use Workdo\FixEquipment\Events\CreateAsset;
use Workdo\FixEquipment\Events\DestroyAsset;
use Workdo\FixEquipment\Events\UpdateAsset;
use Workdo\FixEquipment\DataTables\AssetsDataTable;

class AssetsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function index(AssetsDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('fix equipment assets manage')) {
            return $dataTable->render('fix-equipment::assets.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->isAbleTo('fix equipment assets create')) {

            $status         = EquipmentStatus::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $locations      = EquipmentLocation::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $suppliers      = User::where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->where('type', '=', 'vendor')->get();
            $manufaturers   = Manufacturer::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $categories     = EquipmentCategory::where('workspace', getActiveWorkSpace())->where('category_type', 'Asset')->where('created_by', creatorId())->get();
            $depreciations  = Depreciation::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();

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
                $subAccounts   = [];
            }

            return view('fix-equipment::assets.create', compact('status', 'locations', 'suppliers', 'manufaturers', 'categories', 'depreciations', 'chartAccounts', 'subAccounts'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('fix equipment assets create')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'asset_name'      => 'required',
                    'model'           => 'required',
                    'status'          => 'required',
                    'serial_number'   => 'required',
                    'purchase_date'   => 'required',
                    'purchase_price'  => 'required|numeric|min:1',
                    'location'        => 'required',
                    'supplier'        => 'required',
                    'manufacturer'    => 'required',
                    'category'        => 'required',
                    'depreciation'    => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $asset                      = new FixAsset();

            if ($request->hasFile('asset_image')) {

                $filenameWithExt        = time() . '_' . $request->file('asset_image')->getClientOriginalName();
                $filename               = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension              = $request->file('asset_image')->getClientOriginalExtension();
                $fileNameToStore        = $filename . '_' . time() . '.' . $extension;

                $path = upload_file($request, 'asset_image', $fileNameToStore, 'fix_equipment/asset_image');
                if ($path['flag'] == 1) {
                    $url = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }

            $asset->title               = $request->asset_name;
            $asset->asset_image         = !empty($url) ? $url : '';
            $asset->model_name          = $request->model;
            $asset->status              = $request->status;
            $asset->serial_number       = $request->serial_number;
            $asset->purchase_date       = $request->purchase_date;
            $asset->purchase_price      = $request->purchase_price;
            $asset->location            = $request->location;
            $asset->supplier            = $request->supplier;
            $asset->manufacturer        = $request->manufacturer;
            $asset->category            = $request->category;
            $asset->depreciation_method = $request->depreciation;
            $asset->description         = $request->description;
            $asset->account             = isset($request->account) ? $request->account : '';
            $asset->created_by          = creatorId();
            $asset->workspace           = getActiveWorkSpace();
            $asset->save();

            event(new CreateAsset($request, $asset));

            return redirect()->back()->with('success', __('The asset has been created successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id)
    {
        $asset_id       = Crypt::decrypt($id);
        $asset          = FixAsset::find($asset_id);
        $assessories    = Accessories::where('asset', $asset_id)->where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
        $components     = AssetComponents::where('asset', $asset_id)->where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
        $consumables    = Consumables::with('equipmentCategory')->where('asset', $asset->id)->where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
        $maintenances   = Maintenance::where('asset', $asset->id)->where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
        $licenses       = AssetLicense::where('asset', $asset->id)->get();
        $account        = ChartOfAccount::find($asset->account);

        return view('fix-equipment::assets.show', compact('asset', 'assessories', 'components', 'consumables', 'maintenances', 'licenses', 'account'));
    }

    public function edit($id)
    {
        if (Auth::user()->isAbleTo('fix equipment assets edit')) {

            $asset          = FixAsset::find($id);
            $status         = EquipmentStatus::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $locations      = EquipmentLocation::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $suppliers      = User::where('type', '=', 'vendor')->where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $manufaturers   = Manufacturer::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $categories     = EquipmentCategory::where('workspace', getActiveWorkSpace())->where('category_type', 'Asset')->where('created_by', creatorId())->get();
            $depreciations  = Depreciation::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();

            if (module_is_active('DoubleEntry')) {
                $account        = ChartOfAccount::find($asset->account);
                $chartAccounts  = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_accounts.parent')
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

            return view('fix-equipment::assets.edit', compact('asset', 'status', 'locations', 'suppliers', 'manufaturers', 'categories', 'depreciations', 'chartAccounts', 'subAccounts', 'account'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('fix equipment assets edit')) {

            $validator = Validator::make(
                $request->all(),
                [
                    'asset_name'      => 'required',
                    'model'           => 'required',
                    'status'          => 'required',
                    'serial_number'   => 'required',
                    'purchase_date'   => 'required',
                    'purchase_price'  => 'required|numeric|min:1',
                    'location'        => 'required',
                    'supplier'        => 'required',
                    'manufacturer'    => 'required',
                    'category'        => 'required',
                    'depreciation'    => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $asset                  = FixAsset::find($id);

            if ($request->hasFile('asset_image')) {

                $old_asset_img = $asset->asset_image;

                if (file_exists($old_asset_img)) {
                    delete_file($old_asset_img);
                }

                $filenameWithExt  = time() . '_' . $request->file('asset_image')->getClientOriginalName();
                $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension        = $request->file('asset_image')->getClientOriginalExtension();
                $fileNameToStore  = $filename . '_' . time() . '.' . $extension;

                $path = upload_file($request, 'asset_image', $fileNameToStore, 'fix_equipment/asset_image');
                if ($path['flag'] == 1) {
                    $url = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }

            $asset->title               = $request->asset_name;
            $asset->model_name          = $request->model;
            $asset->asset_image         = !empty($url) ? $url : $asset->asset_image;
            $asset->status              = $request->status;
            $asset->serial_number       = $request->serial_number;
            $asset->purchase_date       = $request->purchase_date;
            $asset->purchase_price      = $request->purchase_price;
            $asset->location            = $request->location;
            $asset->supplier            = $request->supplier;
            $asset->manufacturer        = $request->manufacturer;
            $asset->category            = $request->category;
            $asset->depreciation_method = $request->depreciation;
            $asset->description         = $request->description;
            $asset->account             = isset($request->account) ? $request->account : '';
            $asset->save();

            event(new UpdateAsset($request, $asset));

            return redirect()->back()->with('success', __('The asset details are updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('fix equipment assets delete')) {

            $asset = FixAsset::find($id);

            if (file_exists($asset->asset_image)) {
                delete_file($asset->asset_image);
            }

            event(new DestroyAsset($asset));

            $asset->delete();

            return redirect()->back()->with('success', __('The asset has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
