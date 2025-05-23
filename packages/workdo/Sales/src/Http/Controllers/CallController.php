<?php

namespace Workdo\Sales\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use Workdo\Sales\Entities\Call;
use Workdo\Sales\Entities\Stream;
use Workdo\Sales\Entities\UserDefualtView;
use Workdo\Sales\Entities\SalesUtility;
use Workdo\Sales\Entities\Contact;
use Workdo\Sales\Entities\Opportunities;
use Workdo\Sales\Entities\SalesAccount;
use Workdo\Sales\Entities\CommonCase;
use Illuminate\Support\Facades\Auth;
use Workdo\Sales\DataTables\SalesCallDataTable;
use Workdo\Sales\Events\CreateCall;
use Workdo\Sales\Events\DestroyCall;
use Workdo\Sales\Events\UpdateCall;

class CallController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(SalesCallDataTable $dataTable)
    {
        if(\Auth::user()->isAbleTo('call manage'))
        {
            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'call';
            $defualtView->view   = 'list';
            SalesUtility::userDefualtView($defualtView);

            return $dataTable->render('sales::call.index');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($type,$id)
    {
        if(\Auth::user()->isAbleTo('call create'))
        {
            $account_name      = SalesAccount::where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $status            = Call::$status;
            $direction         = Call::$direction;
            $parent            = Call::$parent;
            $user              = User::where('workspace_id',getActiveWorkSpace())->emp()->get()->pluck('name', 'id');
            $attendees_contact = Contact::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $attendees_contact->prepend('--', 0);
            $attendees_lead = ['--'];
            if(module_is_active('Lead')){
                $attendees_lead    =  \Workdo\Lead\Entities\Lead::where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            return view('sales::call.create', compact('status', 'parent', 'user','account_name','type', 'attendees_contact','direction','attendees_lead','id'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if(\Auth::user()->isAbleTo('call create'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                    'name'              => 'required|string|max:120',
                                    'status'            => 'required',
                                    'parent'            => 'required',
                                    'account'           => 'required',
                                    'user'              => 'required',
                                    'description'       => 'required',
                                    'attendees_user'    => 'required',
                                    'start_date'        => 'required|max:120',
                                    'end_date'          => 'required|max:120',
                                ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                $call                      = new Call();
                $call['user_id']           = $request->user;
                $call['name']              = $request->name;
                $call['status']            = $request->status;
                $call['direction']         = $request->direction;
                $call['start_date']        = $request->start_date;
                $call['end_date']          = $request->end_date;
                $call['parent']            = $request->parent;
                $call['parent_id']         = $request->parent_id;
                $call['account']           = $request->account;
                $call['description']       = $request->description;
                $call['attendees_user']    = $request->attendees_user;
                $call['attendees_contact'] = $request->attendees_contact;
                $call['attendees_lead']    = $request->attendees_lead;
                $call['workspace']         = getActiveWorkSpace();
                $call['created_by']        = creatorId();
                $call->save();

                Stream::create(
                    [
                    'user_id' => Auth::user()->id,
                    'created_by' => creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => Auth::user()->username,
                            'title' => 'call',
                            'stream_comment' => '',
                            'user_name' => $call->name,
                        ]
                    ),
                ]
            );

            event(new CreateCall($request,$call));

            return redirect()->back()->with('success', __('The call has been created successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Call $call)
    {
        if(\Auth::user()->isAbleTo('call show'))
        {
            $status = Call::$status;

            return view('sales::call.view', compact('call', 'status'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Call $call)
    {
        if(\Auth::user()->isAbleTo('call edit'))
        {
            $status            = Call::$status;
            $direction         = Call::$direction;
            $attendees_contact = Contact::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $attendees_contact->prepend('--', '');
            $attendees_lead = ['--'];
            $account_name  = SalesAccount::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');

            if(module_is_active('Lead')){
            $attendees_lead    = \Workdo\Lead\Entities\Lead::where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('name', 'id');
            $attendees_lead->prepend('--', '');
            }
            $user              = User::where('workspace_id',getActiveWorkSpace())->emp()->get()->pluck('name', 'id');
            $user->prepend('--', '');

            // get previous user id
            $previous = Call::where('id', '<', $call->id)->max('id');
            // get next user id
            $next = Call::where('id', '>', $call->id)->min('id');

            return view('sales::call.edit', compact('call', 'attendees_contact', 'account_name','status', 'user', 'direction','previous','next','attendees_lead'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, Call $call)
    {
        if(\Auth::user()->isAbleTo('call edit'))
        {
            $validator = \Validator::make(
                $request->all(), [
                    'name'              => 'required|string|max:120',
                    'status'            => 'required',
                    'account'           => 'required',
                    'user_id'           => 'required',
                    'description'       => 'required',
                    'attendees_user'    => 'required',
                    'start_date'        => 'required|max:120',
                    'end_date'          => 'required|max:120',
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $call['user_id']           = $request->user_id;
            $call['name']              = $request->name;
            $call['status']            = $request->status;
            $call['direction']         = $request->direction;
            $call['start_date']        = $request->start_date;
            $call['end_date']          = $request->end_date;
            $call['description']       = $request->description;
            $call['account']           = $request->account;
            $call['attendees_user']    = $request->attendees_user;
            $call['attendees_contact'] = $request->attendees_contact;
            $call['attendees_lead']    = $request->attendees_lead;
            $call['workspace']         = getActiveWorkSpace();
            $call['created_by']        = creatorId();
            $call->update();

            Stream::create(
                [
                    'user_id' => Auth::user()->id,'created_by' => creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => Auth::user()->username,
                            'title' => 'call',
                            'stream_comment' => '',
                            'user_name' => $call->name,
                        ]
                    ),
                ]
            );

            event(new UpdateCall($request,$call));

            return redirect()->back()->with('success', __('The call details are updated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Call $call)
    {
        if(\Auth::user()->isAbleTo('call delete'))
        {
            event(new DestroyCall($call));

            $call->delete();

            return redirect()->back()->with('success', __('The ') . $call->name . __('call has been deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function grid()
    {
        if(\Auth::user()->isAbleTo('call manage'))
        {
            $calls = Call::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->paginate(11);

            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'call';
            $defualtView->view   = 'grid';
            SalesUtility::userDefualtView($defualtView);

            return view('sales::call.grid', compact('calls'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function getparent(Request $request)
    {
        if($request->parent == 'account')
        {
            $parent = SalesAccount::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id')->toArray();
        }
        elseif($request->parent == 'lead')
        {
            $parent = \Workdo\Lead\Entities\Lead::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id')->toArray();
        }
        elseif($request->parent == 'contact')
        {
            $parent = Contact::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id')->toArray();
        }
        elseif($request->parent == 'opportunities')
        {
            $parent = Opportunities::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id')->toArray();
        }
        elseif($request->parent == 'case')
        {
            $parent = CommonCase::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id')->toArray();
        }
        else
        {
            $parent = '';
        }

        return response()->json($parent);
    }
}
