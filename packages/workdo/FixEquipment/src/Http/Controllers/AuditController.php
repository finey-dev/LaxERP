<?php

namespace Workdo\FixEquipment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Workdo\FixEquipment\Entities\Audit;
use Workdo\FixEquipment\Entities\FixAsset;
use Workdo\FixEquipment\Events\CreateAudit;
use Workdo\FixEquipment\Events\DestroyAudit;
use Workdo\FixEquipment\Events\UpdateAudit;
use Workdo\FixEquipment\DataTables\AuditDataTable;

class AuditController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function index(AuditDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('equipment audit manage')) {
            return $dataTable->render('fix-equipment::audit.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->isAbleTo('equipment audit create')) {

            $assets = FixAsset::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();

            return view('fix-equipment::audit.create', compact('assets'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('equipment audit create')) {

            $validator = Validator::make(
                $request->all(),
                [
                    'asset'       => 'required',
                    'audit_title' => 'required',
                    'audit_date'  => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $asset_id = $request->asset;

            $accessoriesData = DB::table('fix_assets')
                ->select(
                    'fix_assets.id',
                    'accessories.title as title',
                    'accessories.quantity as quantity'
                )
                ->leftJoin('accessories', 'accessories.asset', '=', 'fix_assets.id')
                ->whereIn('fix_assets.id', $asset_id);

            $componentData = DB::table('fix_assets')
                ->select(
                    'fix_assets.id',
                    'asset_components.title as title',
                    'asset_components.quantity as quantity'
                )
                ->leftJoin('asset_components', 'asset_components.asset', '=', 'fix_assets.id')
                ->whereIn('fix_assets.id', $asset_id);

            $consumablesData = DB::table('fix_assets')
                ->select(
                    'fix_assets.id',
                    'consumables.title as title',
                    'consumables.quantity as quantity'
                )
                ->leftJoin('consumables', 'consumables.asset', '=', 'fix_assets.id')
                ->whereIn('fix_assets.id', $asset_id);

            $data   = $accessoriesData->union($componentData)->union($consumablesData)->get();

            $audit                  = new Audit();
            $audit->audit_title     = $request->audit_title;
            $audit->audit_date      = $request->audit_date;
            $audit->audit_data      = $data;
            $audit->audit_status    = 'Pending';
            $audit->asset           = implode(",", $request->asset);
            $audit->created_by      = creatorId();
            $audit->workspace       = getActiveWorkSpace();
            $audit->save();

            event(new CreateAudit($request, $audit));

            return redirect()->route('fix.equipment.audit.index')->with('success', __('The audit has been created successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id)
    {
        if (Auth::user()->isAbleTo('equipment audit manage')) {

            $audit_id   = Crypt::decrypt($id);
            $audit      = Audit::find($audit_id);
            $asset_id   = explode(',', $audit->asset);
            $assets     = FixAsset::whereIn('id', $asset_id)->get();

            $assetTitles = [];
            foreach ($assets as $asset) {
                $assetTitles[] = $asset->title;
            }
            $combinedTitles = implode(', ', $assetTitles);

            return view('fix-equipment::audit.show', compact('assets', 'audit', 'combinedTitles'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if(Auth::user()->isAbleTo('equipment audit edit')){

            $audit_id   = Crypt::decrypt($id);
            $assets     = FixAsset::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $audit      = Audit::find($audit_id);

            return view('fix-equipment::audit.edit', compact('assets', 'audit'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('equipment audit edit')) {

            $validator = Validator::make(
                $request->all(),
                [
                    'asset'       => 'required',
                    'audit_title' => 'required',
                    'audit_date'  => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $asset_id = $request->asset;

            $accessoriesData = DB::table('fix_assets')
                ->select(
                    'fix_assets.id',
                    'accessories.title as title',
                    'accessories.quantity as quantity'
                )
                ->leftJoin('accessories', 'accessories.asset', '=', 'fix_assets.id')
                ->whereIn('fix_assets.id', $asset_id);

            $componentData = DB::table('fix_assets')
                ->select(
                    'fix_assets.id',
                    'asset_components.title as title',
                    'asset_components.quantity as quantity'
                )
                ->leftJoin('asset_components', 'asset_components.asset', '=', 'fix_assets.id')
                ->whereIn('fix_assets.id', $asset_id);

            $consumablesData = DB::table('fix_assets')
                ->select(
                    'fix_assets.id',
                    'consumables.title as title',
                    'consumables.quantity as quantity'
                )
                ->leftJoin('consumables', 'consumables.asset', '=', 'fix_assets.id')
                ->whereIn('fix_assets.id', $asset_id);

            $data = $accessoriesData->union($componentData)->union($consumablesData)->get();

            $dataJson = json_encode($data);

            $assets = is_array($request->asset) ? implode(",", $request->asset) : $request->asset;

            $audit              = Audit::find($id);
            $audit->audit_date  = $request->audit_date;
            $audit->audit_data  = $dataJson;
            $audit->audit_title = $request->audit_title;
            $audit->asset       = $assets;
            $audit->created_by  = creatorId();
            $audit->workspace   = getActiveWorkSpace();

            $audit->save();
            event(new UpdateAudit($request, $audit));

            return redirect()->route('fix.equipment.audit.index')->with('success' ,__('The audit details are updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('equipment audit delete')) {

            $audit = Audit::find($id);

            event(new DestroyAudit($audit));

            $audit->delete();

            return redirect()->back()->with('success', __('The audit has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getData(Request $request)
    {
        $selectedValue = $request->input('selectedValue');

        $accessoriesData = DB::table('fix_assets')
            ->select(
                'fix_assets.id',
                'accessories.title as title',
                'accessories.quantity as quantity'
            )
            ->leftJoin('accessories', 'accessories.asset', '=', 'fix_assets.id')
            ->whereIn('fix_assets.id', $selectedValue);

        $componentData = DB::table('fix_assets')
            ->select(
                'fix_assets.id',
                'asset_components.title as title',
                'asset_components.quantity as quantity'
            )
            ->leftJoin('asset_components', 'asset_components.asset', '=', 'fix_assets.id')
            ->whereIn('fix_assets.id', $selectedValue);

        $consumablesData = DB::table('fix_assets')
            ->select(
                'fix_assets.id',
                'consumables.title as title',
                'consumables.quantity as quantity'
            )
            ->leftJoin('consumables', 'consumables.asset', '=', 'fix_assets.id')
            ->whereIn('fix_assets.id', $selectedValue);

        $data = $accessoriesData->union($componentData)->union($consumablesData)->get();

        return response()->json(['data' => $data]);
    }

    public function status(Request $request, $id){
        $audit = Audit::find($id);

        return view('fix-equipment::audit.status', compact('audit'));
    }

    public function updateStatus(Request $request, $id){
        $audit = Audit::find($id);

        if($request->status == 'Approved'){

            $audit->audit_status = $request->status;
            $audit->save();
        } else {
            $audit->audit_status = $request->status;
            $audit->save();
        }

        return redirect()->back()->with('success', __('The status has been changed successfully'));
    }
}
