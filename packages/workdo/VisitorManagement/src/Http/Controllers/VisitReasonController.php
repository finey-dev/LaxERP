<?php

namespace Workdo\VisitorManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\VisitorManagement\Entities\VisitReason;
use Workdo\VisitorManagement\Events\CreateVisitReason;
use Workdo\VisitorManagement\Events\DeleteVisitReason;
use Workdo\VisitorManagement\Events\UpdateVisitReason;

class VisitReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (\Auth::user()->isAbleTo('reason manage')) {
            $visit_reason = VisitReason::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            return view('visitor-management::reason.index', compact('visit_reason'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->isAbleTo('reason create')){

            return view('visitor-management::reason.create');
        }
        else{
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->isAbleTo('reason create')){
            $validator = \Validator::make(
                $request->all(), [
                    'reason' => 'required',
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $post               = $request->all();
            $post['workspace']  = getActiveWorkSpace();
            $post['created_by'] = creatorId();
            $visitReason        = VisitReason::create($post);
            event(new CreateVisitReason($request,$visitReason));
            return redirect()->back()->with('success',__('The Visit Reason has been created successfully'));
        }
        else{
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function edit($id)
    {
        if(\Auth::user()->isAbleTo('reason edit')){
            $visitReason = VisitReason::find($id);
            if(!$visitReason){
                return redirect()->back()->with('error',__('Visit Reason Not Found!'));
            }
            return view('visitor-management::reason.edit',compact('visitReason'));
        }
        else{
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if(\Auth::user()->isAbleTo('reason edit')){
            $validator = \Validator::make(
                $request->all(), [
                    'reason' => 'required',
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $visitReason = VisitReason::find($id);
            if(!$visitReason){
                return redirect()->back()->with('error',__('Visit Reason Not Found!'));
            }
            $visitReason->update($request->all());
            event(new UpdateVisitReason($request,$visitReason));
            return redirect()->back()->with('success',__('The Visit Reason details are updated successfully!'));
        }
        else{
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function destroy($id)
    {
        if(\Auth::user()->isAbleTo('reason delete')){
            $visitReason = VisitReason::find($id);
            if(!$visitReason){
                return redirect()->back()->with('error',__('Visit Reason Not Found!'));
            }

            event(new DeleteVisitReason($visitReason));
            $visitReason->delete();
            return redirect()->back()->with('success',__('The Visit Purpose has been deleted!'));
        }
        else{
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
}
