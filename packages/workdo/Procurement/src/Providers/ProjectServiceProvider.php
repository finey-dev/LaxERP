<?php

namespace Workdo\Procurement\Providers;

use Illuminate\Support\ServiceProvider;
use Workdo\Procurement\Entities\ProcurementInterviewSchedule;
use Workdo\Procurement\Entities\Rfx;
use App\Models\User;
use Workdo\Procurement\Entities\RfxApplicant;
use Workdo\Procurement\Entities\RfxApplication;
use Workdo\Procurement\Entities\RfxStage;
use Workdo\Procurement\Entities\VendorOnBoard;
use Workdo\Taskly\Entities\Project;

class ProjectServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        view()->composer(['taskly::projects.*'], function ($view) {
            try {
                $project_id =  \Request::segment(2);
                if (!is_numeric($project_id)) {
                    $projectId = \Request::segment(4);
                    $project_id = \Illuminate\Support\Facades\Crypt::decrypt($projectId);
                }
              
                $project = Project::where('id', $project_id)->first();
                
               $user = User::where('id', $project->created_by)->first();
                $rfxs = Rfx::where('created_by', $user->id)->where('workspace', $user->workspace_id)->get();
                $stages = RfxStage::where('created_by', '=', $user->id)->where('workspace', $user->workspace_id)->orderBy('order', 'asc')->get();
                $filter['rfx'] = '';
                $rfxApplicants = RfxApplicant::where('created_by', $user->id)->where('workspace', $user->workspace_id)->get();
                $vendorOnBoards = VendorOnBoard::where('created_by', $user->id)->where('workspace', $user->workspace_id)->with(['applications'])->get();
                $schedules   = ProcurementInterviewSchedule::where('created_by', $user->id)->where('workspace', $user->workspace_id)->get();
                
                $arrSchedule = [];
                $today_date = date('m');
                $current_month_event = ProcurementInterviewSchedule::select('id', 'applicant', 'date', 'employee', 'time', 'comment')->where('workspace', $user->workspace_id)->whereNotNull(['date'])->whereMonth('date', $today_date)->with('applications')->get();
                foreach ($schedules as $schedule) {
                    $arr['id']     = $schedule['id'];
                    $arr['title']  = !empty($schedule->applications) ? (!empty($schedule->applications->rfxs) ? $schedule->applications->rfxs->title : '') : '';
                    $arr['start']  = $schedule['date'];
                    $arr['url']    = route('rfx.interview.schedule.detail', $schedule['id']);
                    $arr['className'] = ' event-danger';
                    $arrSchedule[] = $arr;
                }
                $arrSchedule = json_encode($arrSchedule);
               $view->getFactory()->startPush('ProcurementShareSection', view('procurement::rfx.project_share_section', compact('rfxs','stages','filter','project_id','rfxApplicants','vendorOnBoards','arrSchedule','schedules')));
            } catch (\Throwable $th) {
            }
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
