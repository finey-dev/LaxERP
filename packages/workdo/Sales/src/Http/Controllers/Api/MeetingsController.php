<?php

namespace Workdo\Sales\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Sales\Entities\Meeting;
use Workdo\Sales\Entities\Stream;
use Workdo\Sales\Events\CreateMeeting;
use Workdo\Sales\Events\DestroyMeeting;
use Workdo\Sales\Events\UpdateMeeting;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\EmailTemplate;
use FedaPay\Account;
use Workdo\ChildcareManagement\Entities\Parents;
use Workdo\Lead\Entities\Lead;
use Workdo\Sales\Entities\UserDefualtView;
use Workdo\Sales\Entities\SalesUtility;
use Workdo\Sales\Entities\SalesOrder;
use Workdo\Sales\Entities\Contact;
use Workdo\Sales\Entities\SalesAccount;
use Workdo\Sales\Entities\Opportunities;
use Workdo\Sales\Entities\CommonCase;

class MeetingsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                'workspace_id'  => 'required|exists:work_spaces,id',
                ]
            );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status' => 0, 'message' => $messages->first()] , 403);
        }

        $meetings = Meeting::with('assign_user')
        // ->where('created_by', Auth::id())
        ->where('created_by', creatorId())
        ->where('workspace', $request->workspace_id)
        ->paginate(10)
        ->through(function($meeting){
            return [
                'id' => $meeting->id,
                'name'=> $meeting->name ?? '',
                'parent' => $meeting->parent,
                'description' => $meeting->description,
                'status' => Meeting::$status[$meeting->status],
                'account' => !empty($meeting->accountName) ? $meeting->accountName->name : '',
                'start_date' => $meeting->start_date,
                'end_date' => $meeting->end_date,
                'attendees_user' => !empty($meeting->attendees_users) ? $meeting->attendees_users->name : '',
                'attendees_contact' => !empty($meeting->attendees_contacts) ? $meeting->attendees_contacts->name : '',
                'attendees_lead' => !empty($meeting->leadName) ? $meeting->leadName->name : '',
                'assigned_user' => !empty($meeting->assign_user)?$meeting->assign_user->name:'',
                'assign_user_id' => $meeting->user_id,
                'attendees_user_id'=> $meeting->attendees_user,
                'attendeescontact_id'=> $meeting->attendees_contact,
                'attendees_lead_id'=> $meeting->attendees_lead,
                'account_id'=> $meeting->account
            ];
        });

        return response()->json(['status' => 1, 'data' => $meetings], 200);
    }

    public function create(Request $request)
    {
        try {
            $validator = \Validator::make(
                $request->all(), [
                    'workspace_id'  => 'required|exists:work_spaces,id',
                    ]
                );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return response()->json(['status' => 0, 'message' => $messages->first()] , 403);
            }

            $data = [];
            $data['parent']            = array_keys(Meeting::$parent);
            $data['account'] = SalesAccount::select('id','name')->where('created_by', creatorId())->where('workspace', $request->workspace_id)->get();
            if (module_is_active('Lead')) {
                $data['lead'] = \Workdo\Lead\Entities\Lead::select('id','name')->where('created_by', creatorId())->where('workspace_id', $request->workspace_id)->get();
            }
            else{
                $data['lead'] = [];
            }
            $data['contact'] = Contact::select('id','name')->where('created_by', creatorId())->where('workspace', $request->workspace_id)->get();
            $data['opportunities'] = Opportunities::select('id','name')->where('created_by', creatorId())->where('workspace', $request->workspace_id)->get();
            $data['case'] = CommonCase::where('created_by', creatorId())->where('workspace', $request->workspace_id)->get()->map(function($case){
                return [
                    'id'=>$case->id,
                    'name'=>$case->name,
                ];
            });
            $data['users'] = User::select('id','name')->where('workspace_id', $request->workspace_id)->emp()->get();
            return response()->json(['status' => 1, 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Something went wrong!'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:120',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
            'workspace_id' => 'required|exists:work_spaces,id',
            'parent' => 'required|string|max:100',
            'description' => 'required',
            'status' => ['required', 'in:'.implode(',', Meeting::$status)],
            'account' => ['required','numeric',function($attribute, $value,$fail) use ($request){
                $account = SalesAccount::where('id',$value)->where('workspace',$request->workspace_id)->first();
                if (!$account) {
                    $fail('The selected Sales Account is invalid for the provided workspace.');
                }
            }],
            'attendees_user' => ['required','numeric',function($attribute, $value,$fail) use ($request){
                $attendeesUser = User::where('id',$value)->where('workspace_id', $request->workspace_id)->first();
                if (!$attendeesUser) {
                    $fail('The selected Attendees User is invalid for the provided workspace.');
                }
            }],
            'attendees_contact' => ['required', 'numeric', function ($attribute, $value, $fail) use ($request) {
                $attendeesContact = Contact::where('id',$value)->where('workspace', $request->workspace_id)->first();
                if (!$attendeesContact) {
                    $fail('The selected Attendees Contact is invalid for the provided workspace.');
                }
            }],
            'attendees_lead' => ['required', 'numeric', function ($attribute, $value, $fail) use ($request) {
                $attendeesLead = Lead::where('id',$value)->where('workspace_id', $request->workspace_id)->first();
                if (!$attendeesLead) {
                    $fail('The selected Attendees Lead is invalid for the provided workspace.');
                }
            }],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 403);
        }

        try {
            $meeting                      = new Meeting();
            $meeting['user_id']           = $request->assign_user;
            $meeting['name']              = $request->name;
            $meeting['status']            = array_flip(Meeting::$status)[$request->status];
            $meeting['start_date']        = $request->start_date;
            $meeting['end_date']          = $request->end_date;
            $meeting['parent']            = $request->parent;
            $meeting['parent_id']         = $request->parent_id;
            $meeting['description']       = $request->description;
            $meeting['account']           = $request->account;
            $meeting['attendees_user']    = $request->attendees_user;
            $meeting['attendees_contact'] = $request->attendees_contact;
            $meeting['attendees_lead']    = $request->attendees_lead;
            $meeting['workspace']          = $request->workspace_id;
            $meeting['created_by']        = creatorId();
            $meeting->save();

            Stream::create([
                'user_id' => Auth::id(),
                'created_by' => Auth::id(),
                'log_type' => 'created',
                'remark' => json_encode([
                    'owner_name' => Auth::user()->name,
                    'title' => 'meeting',
                    'stream_comment' => '',
                    'user_name' => $meeting->name,
                ]),
            ]);

            event(new CreateMeeting($request, $meeting));

            return response()->json(['status' => 1, 'message' => 'The meeting has been created successfully.'], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Something went wrong!'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('sales::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */

    public function update(Request $request, $id)
    {
        try {

            $validator = \Validator::make($request->all(), [
                'name' => 'required|max:120',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d',
                'workspace_id' => 'required|exists:work_spaces,id',
                'parent' => 'required|string|max:100',
                'description' => 'required',
                'status' => 'required|string|max:100',
                'account' => ['required','numeric',function($attribute, $value,$fail) use ($request){
                    $account = SalesAccount::where('id',$value)->where('workspace',$request->workspace_id)->first();
                    if (!$account) {
                        $fail('The selected Sales Account is invalid for the provided workspace.');
                    }
                }],
                'attendees_user' => ['required','numeric',function($attribute, $value,$fail) use ($request){
                    $attendeesUser = User::where('id',$value)->where('workspace_id', $request->workspace_id)->first();
                    if (!$attendeesUser) {
                        $fail('The selected Attendees User is invalid for the provided workspace.');
                    }
                }],
                'attendees_contact' => ['required', 'numeric', function ($attribute, $value, $fail) use ($request) {
                    $attendeesContact = Contact::where('id',$value)->where('workspace', $request->workspace_id)->first();
                    if (!$attendeesContact) {
                        $fail('The selected Attendees Contact is invalid for the provided workspace.');
                    }
                }],
                'attendees_lead' => ['required', 'numeric', function ($attribute, $value, $fail) use ($request) {
                    $attendeesLead = Lead::where('id',$value)->where('workspace_id', $request->workspace_id)->first();
                    if (!$attendeesLead) {
                        $fail('The selected Attendees Lead is invalid for the provided workspace.');
                    }
                }],
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 403);
            }

            $meeting = Meeting::where('id', $id)
                ->where('workspace', $request->workspace_id)
                ->where('created_by', creatorId())
                ->first();

            if (!$meeting) {
                return response()->json(['status' => 0, 'message' => 'Meeting not found'], 404);
            }

            $meeting['user_id']           = $request->assign_user;
            $meeting['name']              = $request->name;
            $meeting['start_date']        = $request->start_date;
            $meeting['end_date']          = $request->end_date;
            $meeting['parent']            = $request->parent;
            $meeting['parent_id']         = $request->parent_id;
            $meeting['description']       = $request->description;
            $meeting['account']           = $request->account;
            $meeting['attendees_user']    = $request->attendees_user;
            $meeting['attendees_contact'] = $request->attendees_contact;
            $meeting['attendees_lead']    = $request->attendees_lead;
            $meeting['status']            = array_flip(Meeting::$status)[$request->status];
            $meeting->save();

            Stream::create([
                'user_id' => Auth::id(),
                'created_by' => Auth::id(),
                'log_type' => 'updated',
                'remark' => json_encode([
                    'owner_name' => Auth::user()->name,
                    'title' => 'meeting',
                    'stream_comment' => '',
                    'user_name' => $meeting->name,
                ]),
            ]);

            event(new UpdateMeeting($request, $meeting));

            return response()->json(['status' => 1, 'message' => 'The meeting details are updated successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Something went wrong!'], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request, $id)
    {
        try {
            $validator = \Validator::make(
                $request->all(), [
                    'workspace_id'  => 'required|exists:work_spaces,id',
                    ]
                );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return response()->json(['status' => 0, 'message' => $messages->first()] , 403);
            }

            $meeting = Meeting::where('id', $id)
            ->where('workspace', $request->workspace_id)
            ->where('created_by', creatorId())
            ->first();

            if (!$meeting) {
                return response()->json(['status' => 0, 'message' => 'Meeting not found'], 404);
            }

            event(new DestroyMeeting($meeting));
            $meeting->delete();

            return response()->json(['status' => 1, 'message' => 'The meeting has been deleted.'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Something went wrong!'], 500);
        }

    }
}
