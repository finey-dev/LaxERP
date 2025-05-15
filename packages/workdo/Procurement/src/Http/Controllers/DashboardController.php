<?php

namespace Workdo\Procurement\Http\Controllers;

use App\Models\WorkSpace;
use Auth;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Procurement\Entities\ProcurementInterviewSchedule;
use Workdo\Procurement\Entities\Rfx;
use Workdo\Procurement\Entities\RfxApplication;
use Workdo\Procurement\Entities\RfxStage;

class DashboardController extends Controller
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
        if (Auth::user()->isAbleTo('procurement dashboard manage')) {
            $creatorId = creatorId();
            $getActiveWorkSpace = getActiveWorkSpace();
            $workspace = WorkSpace::where('id', $getActiveWorkSpace)->first();
            $transdate = date('Y-m-d', time());

            $arrCount = [];
            $calenderTasks = [];

            $arrCount['rfx_published'] = Rfx::where('status', '=', 'active')
                ->where('created_by', '=', $creatorId)
                ->where('workspace', '=', $getActiveWorkSpace)->count();
            $arrCount['rfx_expired'] = Rfx::where('status', '=', 'in_active')
                ->orWhere('end_date', '<', now())
                ->where('created_by', '=', $creatorId)
                ->where('workspace', '=', $getActiveWorkSpace)->count();
            $arrCount['rfx_applicant'] = RfxApplication::where('created_by', '=', $creatorId)->where('is_vendor', '!=', 1)
                ->where('workspace', '=', $getActiveWorkSpace)->count();

            $interview_schedule = [];
            $interview_schedule = ProcurementInterviewSchedule::where('created_by', $creatorId)->where('workspace', $getActiveWorkSpace)->get();
            foreach ($interview_schedule as $schedule) {
                if (!empty($schedule)) {
                    $calenderTasks[] = [
                        'title' => !empty($schedule->applications) ? (!empty($schedule->applications->rfxs) ? $schedule->applications->rfxs->title : '') : '',
                        'start' => $schedule->date,
                        'className' => 'event-danger',
                    ];
                } else {
                    $calenderTasks[] = [];
                }
            }

            $rfxs = Rfx::where('created_by', '=', $creatorId)->where('workspace', '=', $getActiveWorkSpace)->take(5)->get();

            $deal_stage = RfxStage::where('created_by', $creatorId)->where('workspace', '=', $getActiveWorkSpace)->orderBy('order', 'ASC')->get();

            $dealStageName = [];
            $dealStageData = [];
            foreach ($deal_stage as $deal_stage_data) {
                $deal_stage = RfxApplication::where('created_by', $creatorId)->where('is_archive', '!=', 1)->where('workspace', '=', $getActiveWorkSpace)->where('stage', $deal_stage_data->id)->orderBy('order', 'ASC')->count();
                $dealStageName[] = $deal_stage_data->title;
                $dealStageData[] = $deal_stage;
            }
            return view('procurement::index', compact('arrCount', 'workspace', 'calenderTasks', 'transdate', 'rfxs', 'dealStageData', 'dealStageName'));
        } else {
            return redirect()->back()->with('error', 'Permission denied');
        }

    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {

        return view('procurement::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
