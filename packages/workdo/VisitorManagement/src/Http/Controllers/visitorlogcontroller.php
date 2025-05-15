<?php

namespace Workdo\VisitorManagement\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\VisitorManagement\Entities\Visitlog;
use Workdo\VisitorManagement\Entities\Visitors;
use Workdo\VisitorManagement\Entities\VisitReason;
use Workdo\VisitorManagement\Events\CreateVisitor;
use Workdo\VisitorManagement\Events\CreateVisitorLog;
use Workdo\VisitorManagement\Events\DeleteVisitorLog;
use Workdo\VisitorManagement\Events\UpdateVisitorLog;
use Workdo\VisitorManagement\DataTables\VisitorLogDatatable;
class visitorlogcontroller extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(VisitorLogDatatable $dataTable)
    {
        if (\Auth::user()->isAbleTo('visitor log manage')) {

            return $dataTable->render('visitor-management::visitor-log.index');
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->isAbleTo('visitor log create')) {
            $visitors = Visitors::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->pluck('first_name', 'id');
            $visitors->prepend("Select Visitor", "");

            $visitReason = VisitReason::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->pluck('reason', 'id');
            $visitReason->prepend("Select Visit Purpose", "");
            return view('visitor-management::visitor-log.create', compact('visitors', 'visitReason'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->isAbleTo('visitor log create')) {
            if (!empty($request->visitor_id)) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'visitor_id'    => 'required',
                        'visit_reason'  => 'required',
                        'check_in'      => 'required|date'
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $visitor = Visitors::find($request->visitor_id);
            } else {

                $validator = \Validator::make(
                    $request->all(),
                    [
                        'first_name'    => 'required',
                        'last_name'     => 'required',
                        'email'         => 'required|unique:visitors',
                        'visit_reason'  => 'required',
                        'phone'         => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
                        'check_in'      => 'required|date'
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $post = $request->all();
                $post['workspace']  = getActiveWorkSpace();
                $post['created_by'] = creatorId();
                $visitor = Visitors::create($post);
                event(new CreateVisitor($request, $visitor));
            }

            $visitorLog                 = [];
            $visitorLog['visitor_id']   = $visitor->id;
            $visitorLog['check_in']     = date('Y-m-d H:i:s', strtotime($request->check_in));
            $visitorLog['workspace']    = getActiveWorkSpace();
            $visitorLog['created_by']   = creatorId();
            $visitorLog                 = VisitLog::create($visitorLog);
            event(new CreateVisitorLog($request, $visitorLog));
            $visitorLog->assignVisitLogReason($request->visit_reason, $visitor->id);

            return redirect()->back()->with('success', __('Check In Successfully!'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function show($id)
    {
        if (\Auth::user()->isAbleTo('visitor log edit')) {
            $visitorLog = VisitLog::find($id);
            return view('visitor-management::visitor-log.show',compact('visitorLog'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->isAbleTo('visitor log edit')) {
            $visitorLog     = VisitLog::find($id);
            if (!$visitorLog) {
                return redirect()->back()->with('error', __('Visitor Log Not Found'));
            }
            $visitors       = Visitors::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->pluck('first_name', 'id');
            $visitors->prepend("Select Visitor", "");

            $visitReason    = VisitReason::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->pluck('reason', 'id');
            $visitReason->prepend("Select Visit Purpose", "");

            return view('visitor-management::visitor-log.edit', compact('visitorLog', 'visitReason', 'visitors'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->isAbleTo('visitor log edit')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'visitor_id'    => 'required',
                    'visit_reason'  => 'required',
                    'check_in'      => 'required|date',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $visitor    = Visitors::find($request->visitor_id);
            $visitorLog = VisitLog::find($id);
            if (!$visitorLog) {
                return redirect()->back()->with('error', __('Visitor Log Not Found'));
            }
            $checkOut           = date('Y-m-d H:i:s', strtotime($request->check_out));
            $check_out          = Carbon::createFromFormat('Y-m-d H:i:s', $checkOut);
            $check_in           = Carbon::createFromFormat('Y-m-d H:i:s', $visitorLog->check_in);
            $durationOfVisit    = $check_in->diff($check_out)->format('%d days, %h hours, %i minutes');

            $visitorLog->visitor_id         = $request->visitor_id;
            $visitorLog->check_out          = $check_out;
            $visitorLog->duration_of_visit  = $durationOfVisit;
            $visitorLog->save();
            event(new UpdateVisitorLog($request, $visitorLog));
            $visitorLog->assignVisitLogReason($request->visit_reason, $visitor->id);

            return redirect()->back()->with('success', __('Check Out Successfully!'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function destroy($id)
    {
        $visitor = VisitLog::find($id);
        if (!$visitor) {
            return redirect()->back()->with('error', __('Visitor Log Not Found'));
        }
        event(new DeleteVisitorLog($visitor));
        $visitor->delete();
        return redirect()->back()->with('success', __('The Visit Log has been deleted'));
    }

    public function getVisitorDetail(Request $request)
    {
        $visitor = Visitors::find($request->id);
        if ($visitor) {
            return response()->json(['status' => 'success', 'data' => $visitor]);
        } else {
            return response()->json(['stutus' => 'error']);
        }
    }

    public function departure_time(Request $request ,$id) {
        if (\Auth::user()->isAbleTo('visitor log edit')) {
            $visitorLog = VisitLog::find($id);
            $checkOut           = date('Y-m-d H:i:s', strtotime($request->check_out));
            $check_out          = Carbon::createFromFormat('Y-m-d H:i:s', $checkOut);
            $visitorLog->check_out          = $check_out;
            $visitorLog->save();
            return redirect()->back()->with('success', __('Visitor Departure Time has been save successfully!'));

        } else {
            return response()->json(['stutus' => 'error']);
        }
    }
}
