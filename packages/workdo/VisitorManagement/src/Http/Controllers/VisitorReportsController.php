<?php

namespace Workdo\VisitorManagement\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\VisitorManagement\Entities\Visitlog;
use Workdo\VisitorManagement\Entities\VisitReason;

class VisitorReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if(\Auth::user()->isAbleTo('visitor reports manage')){

            $chartData      = $this->getChartData(15);
            $dataForChart   = $chartData['dataForChart'];
            $visitReasons   = $chartData['visitReasons'];

            return view('visitor-management::reports.index',compact('dataForChart','visitReasons'));
        }
        else{
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

    }
    public function getVisitorByDate(Request $request){
        $startDate     = Carbon::parse($request->start_date);
        $endDate       = Carbon::parse($request->end_date);
        $days          = $startDate->diffInDays($endDate);
        $dataForChart  = $this->getDynamicChartData($startDate,$endDate,$days);
        return $dataForChart;
    }

    private function getChartData($days){
        $dates = [];
        for ($i = $days; $i >= 0; $i--) {
            $dates[] = Carbon::today()->subDays($i)->toDateString();
        }
        $last15DaysVisitors = Visitlog::selectRaw('DATE(check_in) as date, COUNT(*) as total_visitors')
                            ->where('created_at', '>=', now()->subDays($days)->startOfDay())
                            ->groupBy('date')
                            ->orderBy('date')
                            ->where('workspace',getActiveWorkSpace())
                            ->where('created_by',creatorId())
                            ->get()
                            ->keyBy('date');


        $visitorCounts = [];
        foreach ($dates as $date) {
            $visitorCounts[] = $last15DaysVisitors[$date]->total_visitors ?? 0;
        }

        $dataForChart = [
            'dates' => $dates,
            'visitorCounts' => $visitorCounts,
        ];

        $visitReasons = VisitReason::where('workspace',getActiveWorkSpace())
                                    ->where('created_by',creatorId())
                                    ->withCount(['visitLogs' => function ($query) use($days) {
                                        $query->where('visit_logs.created_at', '>=', now()->subDays($days)->startOfDay());
                                    }])
                                    ->get()
                                    ->pluck('visit_logs_count', 'reason')
                                    ->toArray();
        $data = [];
        $data['visitReasons'] = $visitReasons;
        $data['dataForChart'] = $dataForChart;
        return $data;
    }
    private function getDynamicChartData($startDate, $endDate,$days){
        $dates = [];
        for ($i = 0; $i <= $days; $i++) {
            $date = Carbon::parse($startDate)->addDays($i);

            if ($date->lte($endDate)) {
                $dates[] = $date->toDateString();
            } else {
                break;
            }
        }
        $totalVisitors = VisitLog::selectRaw('DATE(check_in) as date, COUNT(*) as total_visitors')
                        ->whereDate('check_in', '>=', $startDate)
                        ->whereDate('check_in', '<=', $endDate)
                        ->where('workspace',getActiveWorkSpace())
                        ->where('created_by',creatorId())
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get()
                        ->keyBy('date');

        $visitorCounts = [];
        foreach ($dates as $date) {
            $visitorCounts[] = $totalVisitors[$date]->total_visitors ?? 0;
        }
        $dataForChart = [
            'dates' => $dates,
            'visitorCounts' => $visitorCounts,
        ];

        $visitReasons = VisitReason::where('workspace',getActiveWorkSpace())
                                    ->where('created_by',creatorId())
                                    ->withCount(['visitLogs' => function ($query) use($startDate,$endDate) {
                                        $query->whereDate('visit_logs.check_in', '>=', $startDate)->whereDate('visit_logs.check_in','<=',$endDate);
                                    }])
                                    ->get()
                                    ->pluck('visit_logs_count', 'reason')
                                    ->toArray();
        $data = [];
        $data['visitReasons'] = $visitReasons;
        $data['dataForChart'] = $dataForChart;
        return $data;
    }

}
