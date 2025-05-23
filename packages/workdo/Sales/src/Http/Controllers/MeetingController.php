<?php

namespace Workdo\Sales\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use App\Models\EmailTemplate;
use Workdo\Sales\Entities\Meeting;
use Workdo\Sales\Entities\UserDefualtView;
use Workdo\Sales\Entities\SalesUtility;
use Workdo\Sales\Entities\Contact;
use Workdo\Sales\Entities\SalesAccount;
use Workdo\Sales\Entities\Opportunities;
use Workdo\Sales\Entities\Stream;
use Workdo\Sales\Entities\CommonCase;
use Workdo\Sales\Events\CreateMeeting;
use Workdo\Sales\Events\DestroyMeeting;
use Workdo\Sales\Events\UpdateMeeting;
use Illuminate\Support\Facades\Auth;
use Workdo\Sales\DataTables\SalesMeetingDataTable;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(SalesMeetingDataTable $dataTable)
    {
        if (\Auth::user()->isAbleTo('meeting manage')) {

            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'meeting';
            $defualtView->view   = 'list';
            SalesUtility::userDefualtView($defualtView);

            return $dataTable->render('sales::meeting.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($type, $id)
    {
        if (\Auth::user()->isAbleTo('meeting create')) {
            $status            = Meeting::$status;
            $parent            = Meeting::$parent;
            $account_name      = SalesAccount::where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $user              = User::where('workspace_id', getActiveWorkSpace())->emp()->get()->pluck('name', 'id');
            $attendees_contact = Contact::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $attendees_contact->prepend('--', 0);
            $attendees_lead = ['--'];
            if (module_is_active('Lead')) {
                $attendees_lead    = \Workdo\Lead\Entities\Lead::where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
                $attendees_lead->prepend('--', 0);
            }
            return view('sales::meeting.create', compact('status', 'account_name', 'parent', 'type', 'user', 'attendees_contact', 'attendees_lead','id'));
        } else {
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
        if (\Auth::user()->isAbleTo('meeting create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name'          =>  'required|string|max:120',
                    'status'        =>  'required',
                    'parent'        =>  'required',
                    'account'       =>  'required',
                    'user'          =>  'required',
                    'attendees_user'=>  'required',
                    'start_date'    =>  'required',
                    'end_date'      =>  'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $meeting                      = new Meeting();
            $meeting['user_id']           = $request->user;
            $meeting['name']              = $request->name;
            $meeting['status']            = $request->status;
            $meeting['start_date']        = $request->start_date;
            $meeting['end_date']          = $request->end_date;
            $meeting['parent']            = $request->parent;
            $meeting['parent_id']         = $request->parent_id;
            $meeting['account']           = $request->account;
            $meeting['description']       = $request->description;
            $meeting['attendees_user']    = $request->attendees_user;
            $meeting['attendees_contact'] = $request->attendees_contact;
            $meeting['attendees_lead']    = $request->attendees_lead;
            $meeting['workspace']          = getActiveWorkSpace();
            $meeting['created_by']        = creatorId();
            $meeting->save();

            Stream::create(
                [
                    'user_id' => Auth::user()->id, 'created_by' => creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => Auth::user()->username,
                            'title' => 'meeting',
                            'stream_comment' => '',
                            'user_name' => $meeting->name,
                        ]
                    ),
                ]
            );


            if (!empty(company_setting('Meeting assigned')) && company_setting('Meeting assigned')  == true) {
                $Assign_user_phone = User::where('id', $request->user)->where('workspace_id', '=',  getActiveWorkSpace())->first();

                $uArr = [
                    'meeting_assign_user' => $Assign_user_phone->name,
                    'meeting_name' => $request->name,
                    'meeting_start_date' => $request->start_date,
                    'meeting_due_date' => $request->end_date,
                    'meeting_description' => $request->description,
                    'attendees_user' => $request->attendees_user,
                    'attendees_contact' => $request->attendees_contact,

                ];
                $resp = EmailTemplate::sendEmailTemplate('Meeting assigned', [$meeting->id => $Assign_user_phone->email], $uArr);
            }
            $meeting->parent_name = $meeting->getparent($meeting->parent,$meeting->parent_id);
            event(new CreateMeeting($request, $meeting));

            return redirect()->back()->with('success', __('The meeting has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Meeting $meeting)
    {
        if (\Auth::user()->isAbleTo('meeting show')) {
            $status = Meeting::$status;

            return view('sales::meeting.view', compact('meeting', 'status'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Meeting $meeting)
    {
        if (\Auth::user()->isAbleTo('meeting edit')) {
            $status            = Meeting::$status;
            $attendees_contact = Contact::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $attendees_contact->prepend('--', '');
            $attendees_lead = ['--'];
            $account_name  = SalesAccount::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            if (module_is_active('Lead')) {
                $attendees_lead    = \Workdo\Lead\Entities\Lead::where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
                $attendees_lead->prepend('--', 0);
            }
            $user              = User::where('workspace_id', getActiveWorkSpace())->emp()->get()->pluck('name', 'id');
            $user->prepend('--', '');

            // get previous user id
            $previous = Meeting::where('id', '<', $meeting->id)->max('id');
            // get next user id
            $next = Meeting::where('id', '>', $meeting->id)->min('id');

            return view('sales::meeting.edit', compact('meeting', 'account_name','attendees_contact', 'status', 'user', 'previous', 'next', 'attendees_lead'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, Meeting $meeting)
    {
        if (\Auth::user()->isAbleTo('meeting edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name'          =>  'required|string|max:120',
                    'status'        =>  'required',
                    'account'       =>  'required',
                    'user_id'       =>  'required',
                    'attendees_user'=>  'required',
                    'start_date'    =>  'required',
                    'end_date'      =>  'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $meeting['user_id']           = $request->user_id;
            $meeting['name']              = $request->name;
            $meeting['status']            = $request->status;
            $meeting['start_date']        = $request->start_date;
            $meeting['end_date']          = $request->end_date;
            $meeting['description']       = $request->description;
            $meeting['account']           = $request->account;
            $meeting['attendees_user']    = $request->attendees_user;
            $meeting['attendees_contact'] = $request->attendees_contact;
            $meeting['attendees_lead']    = $request->attendees_lead;
            $meeting['workspace']         = getActiveWorkSpace();
            $meeting['created_by']        = creatorId();
            $meeting->update();

            Stream::create(
                [
                    'user_id' => Auth::user()->id, 'created_by' => creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => Auth::user()->username,
                            'title' => 'meeting',
                            'stream_comment' => '',
                            'user_name' => $meeting->name,
                        ]
                    ),
                ]
            );

            event(new UpdateMeeting($request, $meeting));

            return redirect()->back()->with('success', __('The meeting details are updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Meeting $meeting)
    {
        if (\Auth::user()->isAbleTo('meeting delete')) {
            event(new DestroyMeeting($meeting));
            $meeting->delete();

            return redirect()->back()->with('success', __('The ') . $meeting->name . __(' meeting has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function grid()
    {
        if (\Auth::user()->isAbleTo('meeting manage')) {
            $meetings = Meeting::where('created_by', creatorId())->where('workspace', getActiveWorkSpace());

            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'meeting';
            $defualtView->view   = 'grid';
            SalesUtility::userDefualtView($defualtView);
            $meetings = $meetings->paginate(11);
            return view('sales::meeting.grid', compact('meetings'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getparent(Request $request)
    {
        if ($request->parent == 'account') {
            $parent = SalesAccount::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->toArray();
        } elseif ($request->parent == 'lead') {
            $parent = \Workdo\Lead\Entities\Lead::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->toArray();
        } elseif ($request->parent == 'contact') {
            $parent = Contact::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->toArray();
        } elseif ($request->parent == 'opportunities') {
            $parent = Opportunities::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->toArray();
        } elseif ($request->parent == 'case') {
            $parent = CommonCase::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->toArray();
        } else {
            $parent = '';
        }

        return response()->json($parent);
    }
}
