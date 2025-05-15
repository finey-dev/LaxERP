<?php

namespace Workdo\ApiDocsGenerator\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\User;
use App\Models\WorkSpace;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Workdo\Lead\Emails\SendLeadEmail;
use Workdo\Lead\Entities\Lead;
use Workdo\Lead\Entities\LeadActivityLog;
use Workdo\Lead\Entities\LeadCall;
use Workdo\Lead\Entities\LeadDiscussion;
use Workdo\Lead\Entities\LeadEmail;
use Workdo\Lead\Entities\LeadFile;
use Workdo\Lead\Events\LeadAddCall;
use Workdo\Lead\Entities\LeadStage;
use Workdo\Lead\Entities\LeadTask;
use Workdo\Lead\Entities\Pipeline;
use Workdo\Lead\Entities\Source;
use Workdo\Lead\Entities\UserLead;
use Workdo\Lead\Events\CreateLead;
use Workdo\Lead\Events\CreateLeadTask;
use Workdo\Lead\Events\DestroyLead;
use Workdo\Lead\Events\DestroyLeadProduct;
use Workdo\Lead\Events\DestroyLeadTask;
use Workdo\Lead\Events\DestroyLeadUser;
use Workdo\Lead\Events\DestroyLeadCall;
use Workdo\Lead\Events\DestroyLeadFile;
use Workdo\Lead\Events\LeadAddDiscussion;
use Workdo\Lead\Events\LeadAddEmail;
use Workdo\Lead\Events\LeadAddNote;
use Workdo\Lead\Events\LeadAddProduct;
use Workdo\Lead\Events\LeadAddUser;
use Workdo\Lead\Events\LeadSourceUpdate;
use Workdo\Lead\Events\LeadUploadFile;
use Workdo\Lead\Events\UpdateLead;
use Workdo\Lead\Events\UpdateLeadTask;
use Workdo\Lead\Events\LeadUpdateCall;
use Workdo\ProductService\Entities\ProductService;

class LeadApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if (module_is_active('Lead')) {

            if (Auth::user()->default_pipeline) {
                $pipeline = Pipeline::where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->where('id', '=', Auth::user()->default_pipeline)->first();
                if (!$pipeline) {
                    $pipeline = Pipeline::where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->first();
                }
            } else {
                $pipeline = Pipeline::where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->first();
            }

            $leadStages = $pipeline->leadStages->map(function($leadStage){
                return [
                    'id'                => $leadStage->id,
                    'name'              => $leadStage->name,
                    'order'             => $leadStage->order,
                    'leads'             => $leadStage->lead()->map(function($lead){
                        return [
                            'id'                  => $lead->id,
                            'name'                => $lead->name,
                            'email'               => $lead->email,
                            'subject'             => $lead->subject,
                            'phone'               => $lead->phone,
                            'products_count'            => count($lead->products()),
                            'sources_count'             => count($lead->sources()),
                            'users'               => $lead->users->map(function($user){
                                return [
                                    'name'      => $user->name,
                                    'avatar'    => get_file($user->avatar),
                                ];
                            }),

                        ];
                    }),
                ];
            });
            $pipeline =  [
                'id'        => $pipeline->id,
                'name'      => $pipeline->name
            ];
            $data = [];
            $data['pipeline']       = $pipeline;
            $data['lead_stages']    = $leadStages;
            return response()->json(['status'=>'success','data'=>$data],200);
        }
        else{
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'subject' => 'required',
                'name' => 'required',
                'email' => 'required',
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        if (Auth::user()->default_pipeline){
            $pipeline = Pipeline::where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->where('id', '=', Auth::user()->default_pipeline)->first();
            if (!$pipeline) {
                $pipeline = Pipeline::where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->first();
            }
        } else {
            $pipeline = Pipeline::where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->first();
        }

        if(!$pipeline){
            return response()->json(['status'=>'error', 'message' => 'Please Create Pipeline!']);
        }
        $leadStage = LeadStage::where('pipeline_id', '=', $pipeline->id)->where('workspace_id', $request->workspace_id)->first();
        if(!$leadStage){
            return response()->json(['status'=>'error','message'=>'Please Create Stage for This Pipeline!']);
        }

        $lead                 = new Lead();
        $lead->name           = $request->name;
        $lead->email          = $request->email;
        $lead->subject        = $request->subject;
        $lead->user_id        = $request->user_id;
        $lead->pipeline_id    = $pipeline->id;
        $lead->stage_id       = $leadStage->id;
        $lead->phone          = $request->phone;
        $lead->created_by     = creatorId();
        $lead->workspace_id   = $request->workspace_id;
        $lead->date           = date('Y-m-d');
        $lead->follow_up_date = $request->follow_up_date;
        $lead->save();

        if (Auth::user()->hasRole('company')) {
            $usrLeads = [
                Auth::user()->id,
                $request->user_id,
            ];
        } else {
            $usrLeads = [
                creatorId(),
                $request->user_id,
            ];
        }

        foreach ($usrLeads as $usrLead) {
            UserLead::create(
                [
                    'user_id' => $usrLead,
                    'lead_id' => $lead->id,
                ]
            );
        }

        $leadArr = [
            'lead_id' => $lead->id,
            'name' => $lead->name,
            'updated_by' => Auth::user()->id,
        ];

        if (!empty(company_setting('Lead Assigned')) && company_setting('Lead Assigned')  == true) {
            $lArr    = [
                'lead_name' => $lead->name,
                'lead_email' => $lead->email,
                'lead_pipeline' => $pipeline->name,
                'lead_stage' => $leadStage->name,
            ];
            $usrEmail = User::find($request->user_id);

            // Send Email
            $resp = EmailTemplate::sendEmailTemplate('Lead Assigned', [$usrEmail->id => $usrEmail->email], $lArr);
        }

        event(new CreateLead($request,$lead));

        $resp = null;
        $resp['is_success'] = true;

        return response()->json(['status'=>'success', 'message' => 'Lead Successfully Created!']);

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Request $request, $id)
    {
        if (module_is_active('Lead')) {
            // $lead = Lead::find($id);
            $lead = Lead::where('workspace_id',$request->workspace_id)->where('created_by', creatorId())->where('id', $id)->first();
            if($lead){
                if ($lead->is_active) {

                    $stageCnt      = LeadStage::where('pipeline_id', '=', $lead->pipeline_id)->where('created_by', '=', $lead->created_by)->get();
                    $i             = 0;
                    foreach ($stageCnt as $stage) {
                        $i++;
                        if ($stage->id == $lead->stage_id) {
                            break;
                        }
                    }
                    $precentage = number_format(($i * 100) / count($stageCnt));
                    if(count($lead->products())){
                        $products = $lead->products()->map(function($product){
                            return [
                                'id'      => $product->id,
                                'name'    => $product->name,
                                'image'   => !empty($product->image) ? get_file($product->image) : get_file('packages/workdo/ProductService/src/Resources/assets/image/img01.jpg'),
                            ];
                        });
                    }
                    else{
                        $products = [];
                    }
                    if(count($lead->sources())){
                        $sources = $lead->sources()->map(function($source){
                            return [
                                'id'    => $source->id,
                                'name'  => $source->name
                            ];
                        });
                    }
                    else{
                        $sources = [];
                    }
                    $lead_detail = [
                        'id'               => $lead->id,
                        'name'             => $lead->name,
                        'email'            => $lead->email,
                        'subject'          => $lead->subject,
                        'order'            => $lead->order,
                        'phone'            => $lead->phone,
                        'is_active'        => $lead->is_active,
                        'date'             => $lead->date,
                        'products'         => $products,
                        'sources'          => $sources,
                        'tasks'            => $lead->tasks->map(function($task){
                            return [
                                'id'            => $task->id,
                                'name'          => $task->name,
                                'date'          => $task->date,
                                'time'          => $task->time,
                                'priority'      => LeadTask::$priorities[$task->priority],
                                'status'        => LeadTask::$status[$task->status]
                            ];
                        }),
                        'calls'            => $lead->calls->map(function($call){
                            return [
                                'id'                => $call->id,
                                'subject'           => $call->subject,
                                'call_type'         => $call->call_type,
                                'duration'          => $call->duration,
                                'lead_call_user'    => $call->getLeadCallUser->name,
                            ];
                        }),
                        'emails'           => $lead->emails->map(function($email){
                            return [
                                'id'        => $email->id,
                                'subject'   => $email->subject,
                                'to'        => $email->to,
                                'diff_time' => $email->created_at->diffForHumans()
                            ];
                        }),
                        'pipeline'         => $lead->pipeline->name,
                        'stage'            => $lead->stage->name,
                        'users'            => $lead->users->map(function($user){
                            return [
                                'id'        => $user->id,
                                'name'      => $user->name,
                                'avatar'    => get_file($user->avatar)
                            ];
                        }),
                        'discussion'        => $lead->discussions->map(function($discussion){
                            return [
                                'id'                 => $discussion->id,
                                'comment'            => $discussion->comment,
                                'user_name'          => $discussion->user->name,
                                'user_type'          => $discussion->user->type,
                                'user_avatar'        => get_file($discussion->user->avatar),

                            ];
                        }),
                        'files'              => $lead->files->map(function($file){
                            return [
                                'id'        => $file->id,
                                'file_path' => get_file($file->file_path)
                            ];
                        })
                    ];
                    $data = [];
                    $data['lead']  = $lead_detail;
                    $data['precentage'] = $precentage;
                    return response()->json(['status'=>'success','data'=>$data],200);
                } else {
                    return response()->json(['status'=>'error','message'=>__('This Lead Is Not Active!')],404);
                }
            }
            else{
                return response()->json(['status'=>'error','message'=>__('Lead Not Found!')],404);
            }
        }
        else{
            return response()->json(['status'=>'error','message'=>__('CRM Module Not Found!')],404);
        }

    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $objUser          = Auth::user();
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }

        $pipeline = Pipeline::where('id',$request->pipeline_id)->where('workspace_id', $request->workspace_id)->where('created_by', '=', creatorId())->first();
        if(!$pipeline){
            return response()->json(['status'=>'error','message'=>'Pipeline Not Found!']);
        }

        // Decode the sources to ensure it's an array
        $sources = json_decode($request->sources, true);
        if (!is_array($sources)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid Sources Format!']);
        }
        $source = Source::whereIn('id', $sources)->where('workspace_id', $request->workspace_id)->where('created_by', '=', creatorId())->first();
        if(!$source){
            return response()->json(['status'=>'error','message'=>'Source Not Found!']);
        }

        $user = User::where('id',$request->user_id)->where('type', '!=', 'client')->where('workspace_id', $request->workspace_id)->where('created_by', '=', creatorId())->first();
        if(!$user){
            return response()->json(['status'=>'error','message'=>'User Not Found!']);
        }

        if (module_is_active('ProductService')) {
            // Decode the sources to ensure it's an array
            $products = json_decode($request->products, true);
            if (!is_array($products)) {
                return response()->json(['status' => 'error', 'message' => 'Invalid Products Format!']);
            }
            $product = ProductService::whereIn('id',$products)->where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->where('workspace_id', $request->workspace_id)->first();
            if(!$product){
                return response()->json(['status'=>'error','message'=>'Product Not Found!']);
            }
        }
        else{
            return response()->json(['status'=>'error','message'=>'Product Not Found!']);
        }

        $leadStage = LeadStage::where('id',$request->stage_id)->where('pipeline_id',$pipeline->id)->where('workspace_id', $request->workspace_id)->first();
        if(!$leadStage){
            return response()->json(['status'=>'error','message'=>'Lead Stage Not Found!']);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'subject' => 'required',
                'name' => 'required',
                'email' => 'required',
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
                'follow_up_date'=>'date_format:Y-m-d'
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $lead = Lead::where('id',$id)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!']);
        }

        $lead->name           = $request->name;
        $lead->email          = $request->email;
        $lead->subject        = $request->subject;
        $lead->user_id        = $request->user_id;
        $lead->pipeline_id    = $request->pipeline_id;
        $lead->stage_id       = $request->stage_id;
        $lead->sources        = implode(",", array_filter(json_decode($request->sources)));
        $lead->products       = implode(",", array_filter(json_decode($request->products)));
        $lead->notes          = $request->notes;
        $lead->phone          = $request->phone;
        $lead->follow_up_date = $request->follow_up_date;
        $lead->save();

        if (Auth::user()->hasRole('company')) {
            $usrLeads = [
                $objUser->id,
                $request->user_id,
            ];

        } else {
            $usrLeads = [
                creatorId(),
                $request->user_id,
            ];
        }

	    $user_leads = UserLead::where('lead_id',$lead->id)->delete();

        foreach ($usrLeads as $usrLead) {
            UserLead::updateOrCreate(
                [
                    'user_id' => $usrLead,
                    'lead_id' => $lead->id,
                ]
            );
        }


        event(new UpdateLead($request,$lead));

        return response()->json(['status'=>'success', 'message' => 'Lead Successfully Updated!']);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request,$id)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }

        $lead = Lead::where('id',$id)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!']);
        }

        LeadDiscussion::where('lead_id', '=', $lead->id)->delete();
        UserLead::where('lead_id', '=', $lead->id)->delete();

        $leadfiles = LeadFile::where('lead_id', '=', $lead->id)->get();
        foreach ($leadfiles as $leadfile) {

            delete_file($leadfile->file_path);
            $leadfile->delete();
        }
        LeadActivityLog::where('lead_id', '=', $lead->id)->delete();

        if (module_is_active('CustomField')) {
            $customFields = \Workdo\CustomField\Entities\CustomField::where('module', 'lead')->where('sub_module', 'lead')->get();
            foreach ($customFields as $customField) {
                $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $lead->id)->where('field_id', $customField->id)->first();
                if (!empty($value)) {
                    $value->delete();
                }
            }
        }

        event(new DestroyLead($lead));

        $lead->delete();

        return response()->json(['status'=>'success', 'message' => 'Lead Successfully Deleted!']);
    }

    public function leadTaskStore(Request $request, $lead_id)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }

        $creatorId          = creatorId();
        $getActiveWorkSpace = $request->workspace_id;
        $lead       = Lead::where('id',$lead_id)->where('workspace_id',$getActiveWorkSpace)->where('created_by',$creatorId)->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!'],404);
        }
        $lead_users = $lead->users->pluck('id')->toArray();
        $usrs       = User::whereIN('id', $lead_users)->get()->pluck('email', 'id')->toArray();

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:255',
                'date' => 'required|date_format:Y-m-d',
                'time' => 'required|date_format:H:i',
                'priority' => 'required|in:'.implode(',',array_values(LeadTask::$priorities)),
                'status' => 'required|in:'.implode(',',array_values(LeadTask::$status)),
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $leadTask = LeadTask::create(
            [
                'lead_id' => $lead->id,
                'name' => $request->name,
                'date' => $request->date,
                'time' => date('H:i:s', strtotime($request->date . ' ' . $request->time)),
                'priority' => array_flip(LeadTask::$priorities)[$request->priority],
                'status' => array_flip(LeadTask::$status)[$request->status],
                'workspace' => $getActiveWorkSpace,
            ]
        );

        LeadActivityLog::create(
            [
                'user_id' => Auth::user()->id,
                'lead_id' => $lead->id,
                'log_type' => 'Create Task',
                'remark' => json_encode(['title' => $leadTask->name]),
            ]
        );

        if (!empty(company_setting('New Task')) && company_setting('New Task')  == true) {
            $tArr = [
                'lead_name' => $lead->name,
                'lead_pipeline' => $lead->pipeline->name,
                'lead_stage' => $lead->stage->name,
                'lead_status' => $lead->status,
                'lead_price' => currency_format_with_sym($lead->price),
                'task_name' => $leadTask->name,
                'task_priority' => LeadTask::$priorities[$leadTask->priority],
                'task_status' => LeadTask::$status[$leadTask->status],
            ];

            // Send Email
            $resp = EmailTemplate::sendEmailTemplate('New Task', $usrs, $tArr);
        }

        event(new CreateLeadTask($request, $leadTask, $lead));

        return response()->json(['status'=>'success', 'message'=> __('Lead Task successfully created!') . ((!empty($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : '')]);

    }

    public function leadTaskUpdate(Request $request, $leadId, $leadTaskId)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:255',
                'date' => 'required|date_format:Y-m-d',
                'time' => 'required|date_format:H:i',
                'priority' => 'required|in:'.implode(',',array_values(LeadTask::$priorities)),
                'status' => 'required|in:'.implode(',',array_values(LeadTask::$status)),
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $lead = Lead::where('id',$leadId)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!'],404);
        }

        $leadTask = LeadTask::where('id',$leadTaskId)->where('workspace',$request->workspace_id)->first();
        if(!$leadTask){
            return response()->json(['status'=>'error','message'=>'Lead Task Not Found!']);
        }

        $leadTask->name       = $request->name;
        $leadTask->date       = $request->date;
        $leadTask->time       = $request->time;
        $leadTask->priority   = array_flip(LeadTask::$priorities)[$request->priority];
        $leadTask->status     = array_flip(LeadTask::$status)[$request->status];
        $leadTask->save();

        event(new UpdateLeadTask($request, $lead, $leadTask));

        return response()->json(['status'=>'success', 'message'=> 'Lead Task successfully Updated!']);

    }

    public function leadTaskDelete(Request $request, $leadId, $leadTaskId)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }

        $lead = Lead::where('id',$leadId)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!'],404);
        }

        $leadTask = LeadTask::where('id',$leadTaskId)->where('workspace',$request->workspace_id)->first();
        if(!$leadTask){
            return response()->json(['status' => 'error','message' => 'Lead Task Not Found!']);
        }

        $leadTask->delete();

        event(new DestroyLeadTask($lead));

        return response()->json(['status'=>'success', 'message'=> 'Lead Task successfully Deleted!']);
    }

    public function leadUserStore(Request $request, $leadId)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'users' => 'required'
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $lead = Lead::where('id',$leadId)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!'],404);
        }

        // Decode the sources to ensure it's an array
        $userExists = json_decode($request->users, true);
        if (!is_array($userExists)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid Users Format!']);
        }
        $userExist = User::whereIn('id',$userExists)->get();
        if(!$userExist){
            return response()->json(['status'=>'error','message'=>'Please Insert Valid User!'],404);
        }

	    $user_leads = UserLead::where('lead_id',$lead->id)->delete();

        foreach ($userExist as $user) {
            UserLead::updateOrCreate(
                [
                    'lead_id' => $lead->id,
                    'user_id' => $user->id,
                ]
            );
        }

        event(new LeadAddUser($request,$lead));

        return response()->json(['status'=>'success', 'message' => 'Lead Users successfully Created!']);

    }

    public function leadUserDelete(Request $request, $leadId,$leadUserId)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }

        $lead = Lead::where('id',$leadId)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!'],404);
        }

        $userLead = UserLead::where('lead_id', '=', $lead->id)->where('user_id', '=', $leadUserId)->first();
        if(!$userLead){
            return response()->json(['status'=>'error','message'=>'Lead User Not Found!']);
        }

        $userLead->delete();

        event(new DestroyLeadUser($lead));

        return response()->json(['status'=>'success', 'message'=> 'Lead User Successfully Deleted!']);

    }

    public function leadProductCreate(Request $request,$leadId)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }

        $lead = Lead::where('id',$leadId)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!'],404);
        }

        $creatorId          = creatorId();
        $getActiveWorkSpace = $request->workspace_id;

        $products = [];
        if (module_is_active('ProductService')) {
            $products = \Workdo\ProductService\Entities\ProductService::where('created_by', '=', $creatorId)->where('workspace_id', $getActiveWorkSpace)->whereNOTIn('id', explode(',', $lead->products))->where('workspace_id', $getActiveWorkSpace)->get()->pluck('name', 'id');
        }

        return response()->json(['status'=>'success','data'=>$products]);
    }

    public function leadProductStore(Request $request,$leadId)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'products' => 'required'
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $lead = Lead::where('id',$leadId)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!'],404);
        }

        $user        = Auth::user();
        // Decode the products to ensure it's an array
        $leadProducts = json_decode($request->products, true);
        if (!is_array($leadProducts)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid Products Format!']);
        }
        $leadProduct = ProductService::whereIn('id',$leadProducts)->get();
        if(!$leadProduct){
            return response()->json(['status'=>'error','message'=>'Please Insert Valid Product!'],404);
        }

        $products       = array_filter(($leadProducts));
        $old_products   = explode(',', $lead->products);
        $lead->products = implode(',', array_unique(array_merge($old_products, $products)));
        $lead->save();

        $objProduct = ProductService::whereIn('id', $products)->get()->pluck('name', 'id')->toArray();

        LeadActivityLog::create(
            [
                'user_id' => $user->id,
                'lead_id' => $lead->id,
                'log_type' => 'Add Product',
                'remark' => json_encode(['title' => implode(",", $objProduct)]),
            ]
        );

        event(new LeadAddProduct($request,$lead));

        return response()->json(['status'=>'success', 'message' => 'Lead Products successfully Created!']);

    }

    public function leadProductDelete(Request $request,$leadId,$leadProductId)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }

        $lead = Lead::where('id',$leadId)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!'],404);
        }

        $products = explode(',', $lead->products);
        foreach ($products as $key => $product) {
            if ($leadProductId == $product) {
                unset($products[$key]);
            }
        }
        $lead->products = implode(',', $products);
        $lead->save();

        event(new DestroyLeadProduct($lead));

        return response()->json(['status'=>'success','message' => 'Lead Products Successfully Deleted!']);
    }

    public function leadSourceCreate(Request $request,$leadId)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }
        $user        = Auth::user();
        $lead = Lead::where('id',$leadId)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!'],404);
        }

        // Decode the sources to ensure it's an array
        $leadSources = json_decode($request->sources, true);
        if (!is_array($leadSources)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid Sources Format!']);
        }
        $leadSource = Source::whereIn('id',$leadSources)->get();
        if(!$leadSource){
            return response()->json(['status'=>'error','message'=>'Please Insert Valid Source!'],404);
        }

        if (!empty($leadSource) && count($leadSource) > 0) {
            $lead->sources = implode(',', $leadSources);
        } else {
            $lead->sources = "";
        }

        $lead->save();

        LeadActivityLog::create(
            [
                'user_id' => $user->id,
                'lead_id' => $lead->id,
                'log_type' => 'Update Sources',
                'remark' => json_encode(['title' => 'Update Sources']),
            ]
        );

        event(new LeadSourceUpdate($request,$lead));

        return response()->json(['status'=>'success', 'message'=> 'Lead Sources successfully Created!']);

    }

    public function leadSourceDelete(Request $request,$leadId,$leadSourceId)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }

        $lead = Lead::where('id',$leadId)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!'],404);
        }

        $sources = explode(',', $lead->sources);
        foreach ($sources as $key => $source) {
            if ($leadSourceId == $source) {
                unset($sources[$key]);
            }
        }
        $lead->sources = implode(',', $sources);
        $lead->save();

        event(new DestroyLeadProduct($lead));

        return response()->json(['status'=>'success','message' => 'Lead Sources Successfully Deleted!']);
    }

    public function leadEmailCreate(Request $request,$leadId)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'to' => 'required|email',
                'subject' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $lead = Lead::where('id',$leadId)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!'],404);
        }

        $leadEmail = LeadEmail::create(
            [
                'lead_id' => $lead->id,
                'to' => $request->to,
                'subject' => $request->subject,
                'description' => $request->description,
            ]
        );

        if (!empty(company_setting('Lead Emails')) && company_setting('Lead Emails')  == true) {
            try {
                $setconfing =  SetConfigEmail();
                if ($setconfing ==  true) {
                    try {
                        Mail::to($request->to)->send(new SendLeadEmail($leadEmail));
                    } catch (\Exception $e) {
                        $smtp_error['status'] = false;
                        $smtp_error['msg'] = $e->getMessage();
                    }
                } else {
                    $smtp_error['status'] = false;
                    $smtp_error['msg'] = __('Something went wrong please try again ');
                }
            } catch (\Exception $e) {
                $smtp_error['status'] = false;
                $smtp_error['msg'] = $e->getMessage();
            }
        }

        LeadActivityLog::create(
            [
                'user_id' => Auth::user()->id,
                'lead_id' => $lead->id,
                'log_type' => 'Create Lead Email',
                'remark' => json_encode(['title' => 'Create new Lead Email']),
            ]
        );

        event(new LeadAddEmail($request,$lead));

        return response()->json(['status'=>'success', 'message' => 'Lead Email Successfully Created!']);
    }

    public function leadDiscussionCreate(Request $request,$leadId)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'comment' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $user        = Auth::user();

        $lead = Lead::where('id',$leadId)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!'],404);
        }

        $discussion             = new LeadDiscussion();
        $discussion->comment    = $request->comment;
        $discussion->lead_id    = $lead->id;
        $discussion->created_by = $user->id;
        $discussion->save();

        event(new LeadAddDiscussion($request,$lead));

        return response()->json(['status'=>'success', 'message'=> 'Lead Discussion Successfully Created!']);

    }

    public function leadNoteCreate(Request $request,$leadId)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'notes' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $lead = Lead::where('id',$leadId)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!'],404);
        }

        $lead->notes = $request->notes;
        $lead->save();

        event(new LeadAddNote($request,$lead));

        return response()->json(
            [
                'status' =>'success',
                'message' => 'Lead Note successfully saved!',
            ],
            200
        );
    }

    public function leadFileCreate(Request $request,$leadId)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }
        if(gettype($request->file) == 'array'){
            return response()->json(['status'=>'error','message'=>'Please Upload Single File!']);
        }
        $validator = Validator::make(
            $request->all(),
            [
                'file' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $lead = Lead::where('id',$leadId)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!'],404);
        }

        $file_name = time().'_'.$request->file->getClientOriginalName();

        $url = upload_file($request, 'file', $file_name, 'leads', []);

        if (isset($url['flag']) && $url['flag'] == 1) {
            $file                 = LeadFile::create(
                [
                    'lead_id' => $request->lead_id,
                    'file_name' => $file_name,
                    'file_path' => $url['url'],
                ]
            );

            LeadActivityLog::create(
                [
                    'user_id' => Auth::user()->id,
                    'lead_id' => $lead->id,
                    'log_type' => 'Upload File',
                    'remark' => json_encode(['file_name' => $file_name]),
                ]
            );

            event(new LeadUploadFile($request,$lead));

            return response()->json(['status'=>'success','message'=>'Lead File Successfully Created!']);
        } else {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $url['msg'],
                ],
                401
            );
        }
    }

    public function leadFileDelete(Request $request,$leadId,$leadFileId)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }

        $lead = Lead::where('id',$leadId)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!'],404);
        }

        $leadFile = LeadFile::where('id',$leadFileId)->where('lead_id',$leadId)->first();
        if(!$leadFile){
            return response()->json(['status'=>'error','message'=>"Lead File Not Found!"]);
        }

        delete_file($leadFile->file_path);
        $leadFile->delete();

        event(new DestroyLeadFile($lead));

        return response()->json(['status'=>'success','message'=>"Lead File Successfully Deleted!"]);

    }

    public function leadCallCreate(Request $request,$leadId)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'subject' => 'required|max:255',
                'call_type' => 'required|in:outbound,inbound',
                'user_id' => 'required|exists:users,id',
                'duration' => 'required|date_format:H:i:s',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $user = Auth::user();
        $lead = Lead::where('id',$leadId)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!'],404);
        }

        $leadCall = LeadCall::create(
            [
                'lead_id' => $lead->id,
                'subject' => $request->subject,
                'call_type' => $request->call_type,
                'duration' => $request->duration,
                'user_id' => $request->user_id,
                'description' => $request->description,
                'call_result' => $request->call_result,
            ]
        );

        LeadActivityLog::create(
            [
                'user_id' => $user->id,
                'lead_id' => $lead->id,
                'log_type' => 'Create Lead Call',
                'remark' => json_encode(['title' => 'Create new Lead Call'])
            ]
        );

        event(new LeadAddCall($request,$lead));

        return response()->json(['status'=>'success', 'message' => 'Lead Call Successfully Created!']);

    }

    public function leadCallEdit(Request $request,$leadId, $leadCallId)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'subject' => 'required|max:255',
                'call_type' => 'required|in:outbound,inbound',
                'user_id' => 'required|exists:users,id',
                'duration' => 'required|date_format:H:i:s',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $lead = Lead::where('id',$leadId)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!'],404);
        }

        $leadCall = LeadCall::where('id',$leadCallId)->where('lead_id',$leadId)->first();
        if(!$leadCall){
            return response()->json(['status'=>'error','message'=>'Lead Call Not Found!'],404);
        }

        $leadCall->subject      = $request->subject;
        $leadCall->call_type    = $request->call_type;
        $leadCall->duration     = $request->duration;
        $leadCall->user_id      = $request->user_id;
        $leadCall->description  = $request->description;
        $leadCall->call_result  = $request->call_result;
        $leadCall->save();

        event(new LeadUpdateCall($request,$lead));

        return response()->json(['status'=>'success', 'message'=> 'Lead Call Successfully Updated!']);
    }

    public function leadCallDelete(Request $request,$leadId, $leadCallId)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'CRM Module Not Found!'],404);
        }

        $lead = Lead::where('id',$leadId)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead){
            return response()->json(['status'=>'error','message'=>'Lead Not Found!'],404);
        }

        $leadCall = LeadCall::where('id',$leadCallId)->where('lead_id',$leadId)->first();
        if(!$leadCall){
            return response()->json(['status'=>'error','message'=>'Lead Call Not Found!'],404);
        }

        $leadCall->delete();

        event(new DestroyLeadCall($lead));

        return response()->json(['status'=>'success', 'message'=> 'Lead Call Successfully Deleted!']);
    }

}
