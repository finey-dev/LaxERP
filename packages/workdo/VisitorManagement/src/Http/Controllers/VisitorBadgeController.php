<?php

namespace Workdo\VisitorManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\VisitorManagement\DataTables\VisitorBadgeDataTable;
use Workdo\VisitorManagement\Entities\VisitorBadge;
use Workdo\VisitorManagement\Entities\Visitors;
use Workdo\VisitorManagement\Events\CreateVisitorBadge;
use Workdo\VisitorManagement\Events\DeleteVisitorBadge;
use Workdo\VisitorManagement\Events\UpdateVisitorBadge;

class VisitorBadgeController extends Controller
{
    public function index(VisitorBadgeDataTable $dataTable)
    {
        if (\Auth::user()->isAbleTo('visitor badge manage')) {

            return $dataTable->render('visitor-management::visitor-badge.index');
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->isAbleTo('visitor badge create')) {
            $visitors = Visitors::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->pluck('first_name', 'id');
            $visitors->prepend("Select Visitor", "");

            return view('visitor-management::visitor-badge.create', compact('visitors'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('visitor badge create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'visitor_id' => 'required',
                    'badge_number' => 'required|unique:visitor_badges,badge_number'
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $visitor_badge                = new VisitorBadge();
            $visitor_badge->visitor_id = $request->visitor_id;
            $visitor_badge->badge_number        =  $request->badge_number;
            $visitor_badge->issue_date        =  date('Y-m-d H:i:s', strtotime($request->issue_date));
            $visitor_badge->return_date        = date('Y-m-d H:i:s', strtotime($request->return_date));
            $visitor_badge->workspace     = getActiveWorkSpace();
            $visitor_badge->created_by    = creatorId();
            $visitor_badge->save();
            event(new CreateVisitorBadge($request, $visitor_badge));

            return redirect()->route('visitors-badge.index')->with('success', __('The Visitor Badge has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show($id)
    {
        return view('visitor-management::show');
    }


    public function edit($id)
    {
        if (\Auth::user()->isAbleTo('visitor badge edit')) {
            $visitor_badge     = VisitorBadge::find($id);
            if (!$visitor_badge) {
                return redirect()->back()->with('error', __('Visitor Badge Not Found'));
            }
            $visitors       = Visitors::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->pluck('first_name', 'id');
            $visitors->prepend("Select Visitor", "");

            return view('visitor-management::visitor-badge.edit', compact('visitor_badge', 'visitors'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('visitor badge edit')) {

            $visitor_badge = VisitorBadge::find($id);
            $validator = \Validator::make(
                $request->all(),
                [
                    'visitor_id' => 'required',
                    'badge_number' => 'required|unique:visitor_badges,badge_number,' . $visitor_badge->id,
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $visitor_badge->visitor_id          = $request->visitor_id;
            $visitor_badge->badge_number        =  $request->badge_number;
            $visitor_badge->issue_date        =  date('Y-m-d H:i:s', strtotime($request->issue_date));
            $visitor_badge->return_date        = date('Y-m-d H:i:s', strtotime($request->return_date));

            $visitor_badge->save();
            event(new UpdateVisitorBadge($request, $visitor_badge));

            return redirect()->route('visitors-badge.index')->with('success', __('The Visitor Badge has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('visitor badge delete')) {
            $visitor_badge = VisitorBadge::find($id);
            if (!$visitor_badge) {
                return redirect()->back()->with('error', __('Visitor Badge Not Found'));
            }
            event(new DeleteVisitorBadge($visitor_badge));
            $visitor_badge->delete();
            return redirect()->back()->with('success', __('The Visitor Badge has been deleted successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
