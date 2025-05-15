<?php

namespace Workdo\FixEquipment\Http\Controllers;

use App\Models\WorkSpace;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Workdo\FixEquipment\Entities\Accessories;
use Workdo\FixEquipment\Entities\AssetComponents;
use Workdo\FixEquipment\Entities\Consumables;
use Workdo\FixEquipment\Entities\FixAsset;

class FixEquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function __construct()
    {
        if (module_is_active('GoogleAuthentication')) {
            $this->middleware('2fa');
        }
    }
    public function index()
    {
        if (Auth::user()->isAbleTo('fix equipment dashboard manage')) {

            $assets             = FixAsset::take(5)->with('equipmentCategory')->where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            $total_asset        = FixAsset::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->count();
            $total_accessories  = Accessories::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->count();
            $total_component    = AssetComponents::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->count();
            $total_consumables  = Consumables::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->count();

            $data = DB::table('fix_assets')
                ->join('equipment_statuses', 'fix_assets.status', '=', 'equipment_statuses.id')
                ->select('equipment_statuses.title as status_name', 'equipment_statuses.color as status_color')
                ->where('fix_assets.workspace', getActiveWorkSpace())->where('fix_assets.created_by', creatorId())
                ->get();
            $statusCounts   = $data->groupBy('status_name')->map->count();

            $statusNames    = $statusCounts->keys()->toArray();
            $assetCounts    = $statusCounts->values()->toArray();
            $statusColors   = $data->pluck('status_color')->toArray();

            $Activeworkspace = WorkSpace::where('id', getActiveWorkSpace())->where('created_by', creatorId())->pluck('name')->first();
            return view('fix-equipment::dashboard.dashboard', compact('total_asset', 'total_accessories', 'total_component', 'total_consumables', 'statusNames', 'assetCounts', 'statusColors', 'assets','Activeworkspace'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        return view('fix-equipment::create');
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        return view('fix-equipment::show');
    }

    public function edit($id)
    {
        return view('fix-equipment::edit');
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
