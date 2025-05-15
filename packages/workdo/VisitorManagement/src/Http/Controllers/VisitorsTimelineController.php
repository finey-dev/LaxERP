<?php

namespace Workdo\VisitorManagement\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\VisitorManagement\Entities\Visitlog;

class VisitorsTimelineController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if(\Auth::user()->isAbleTo('visitor timeline manage')){
            $currentDate = Carbon::now()->toDateString();

            $visitorLogs = Visitlog::where('workspace',getActiveWorkSpace())
                                    ->where('created_by',creatorId())
                                    ->where(function($query) use($currentDate){
                                        $query->whereDate('check_in','=',$currentDate)->orWhereDate('check_out','=',$currentDate);
                                    })
                                    ->orderBy('check_in','DESC')
                                    ->with('visitor')
                                    ->get();
            return view('visitor-management::timeline.index',compact('visitorLogs'));
        }
        else{
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

}
