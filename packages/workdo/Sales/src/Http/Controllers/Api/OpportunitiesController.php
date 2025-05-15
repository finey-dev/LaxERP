<?php

namespace Workdo\Sales\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Workdo\Sales\Entities\Contact;
use Workdo\Sales\Entities\Opportunities;
use Workdo\Sales\Entities\OpportunitiesStage;
use Workdo\Sales\Entities\Quote;
use Workdo\Sales\Entities\SalesAccount;
use Workdo\Sales\Entities\ShippingProvider;
use Workdo\Sales\Entities\Stream;
use Workdo\Sales\Events\CreateOpportunities;
use Workdo\Sales\Events\DestroyOpportunities;
use Workdo\Sales\Events\UpdateOpportunities;

class OpportunitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(), [
                    'workspace_id'  => 'required|exists:work_spaces,id',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return response()->json(['status' => 0, 'message' => $messages->first()] , 403);
            }
            $opportunities = Opportunities::with('stages', 'accounts', 'assign_user')->where('workspace', $request->workspace_id);
            // if(Auth::user()->type != 'company'){
            //     $opportunities = $opportunities->where('user_id',Auth::user()->id);
            // }
            $opportunities = $opportunities->paginate(10)
                            ->through(function($opportunity){
                                return [
                                    'id'                    => $opportunity->id,
                                    'name'                  => $opportunity->name ?? null,
                                    'account'               => !empty($opportunity->accounts) ? $opportunity->accounts->name : null,
                                    'stage'                 => !empty($opportunity->stages) ? $opportunity->stages->name : null,
                                    'amount'                => currency_format_with_sym( $opportunity->amount),
                                    'probability'           => $opportunity->probability,
                                    'close_date'            => company_date_formate($opportunity->close_date),
                                    'contacts'              => !empty($opportunity->contacts) ? $opportunity->contacts : null,
                                    'lead_source'           => !empty($opportunity->leadsource) ? $opportunity->leadsource : null,
                                    'description'           => $opportunity->description,
                                    'assign_user'           => !empty($opportunity->assign_user) ? $opportunity->assign_user->name : null,
                                    'account_id'            => $opportunity->account,
                                    'contact_id'            => $opportunity->contact,
                                    'opportunity_stage_id'  => $opportunity->stage,
                                    'assign_user_Id'        => $opportunity->user_id,
                                ];
                            });

            return response()->json(['status'=>1,'data'=>$opportunities]);
        } catch (\Exception $e) {
            return response()->json(['status'=>0,'message'=>'something went wrong!!!']);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(), [
                    'workspace_id'  => 'required|exists:work_spaces,id',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return response()->json(['status' => 0, 'message' => $messages->first()] , 403);
            }

            $accounts        = SalesAccount::select('id','name')->where('workspace', $request->workspace_id)->get();
            $contacts        = Contact::select('id','name')->where('created_by', creatorId())->where('workspace', $request->workspace_id)->get();
            $leadsources = [];
            if (module_is_active('Lead')) {
                $leadsources          = \Workdo\Lead\Entities\Source::select('id','name')->where('created_by', creatorId())->where('workspace_id', $request->workspace_id)->get();
            }
            $users                = User::select('id','name')->where('workspace_id', $request->workspace_id)->emp()->get();
            $opportunitiesStages = OpportunitiesStage::select('id','name')->where('created_by', creatorId())->where('workspace', $request->workspace_id)->get();
            $opportunities = Opportunities::where('created_by', creatorId())->where('workspace', $request->workspace_id)->get()->map(function($opportunity){
                return [
                    'id'    => $opportunity->id,
                    'name'    => $opportunity->name,
                    'account_id'    => $opportunity->account,
                    'account_name'    => !empty($opportunity->accounts) ? $opportunity->accounts->name : null,
                ];
            });
            $shippingProvider = ShippingProvider::select('id','name')->where('created_by', creatorId())->where('workspace',$request->workspace_id)->get();
            $quotes = Quote::select('id','name')->where('created_by',creatorId())->where('workspace',$request->workspace_id)->get();
            $tax = [];
            if(module_is_active('ProductService')){
                $tax = \Workdo\ProductService\Entities\Tax::select('id','name')->where('created_by', creatorId())->where('workspace_id',$request->workspace_id)->get();
            }

            $data = [];
            $data['accounts'] = $accounts;
            $data['contacts'] = $contacts;
            $data['lead_sources'] = $leadsources;
            $data['users'] = $users;
            $data['opportunities_stages'] = $opportunitiesStages;
            $data['opportunities'] = $opportunities;
            $data['shipping_provider'] = $shippingProvider;
            $data['quotes'] = $quotes;
            $data['tax'] = $tax;

            return response()->json(['status'=>1,'data'=>$data]);
        } catch (\Exception $e) {
            return response()->json(['status'=>0,'message'=>'something went wrong!!!']);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'workspace_id'  => 'required|exists:work_spaces,id',
                'name' => 'required|max:120',
                'amount' => 'required|numeric|gt:0',
                'probability' => 'required|numeric',
                'opportunity_stage_id' => 'required|exists:opportunities_stages,id',
                'close_date' => 'required|date_format:Y-m-d',
                'sales_account_id' => 'required|exists:sales_accounts,id',
                'contact_id'=>'required|exists:contacts,id',
                'assign_user_id'=>'required|exists:users,id',
            ];
            if(module_is_active('Lead')){
                $rules['lead_source_id'] = 'required|exists:sources,id';
            }
            $validator = Validator::make(
                $request->all(), $rules
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return response()->json(['status' => 0, 'message' => $messages->first()] , 403);
            }

            $opportunity                    = new Opportunities();
            $opportunity->user_id           = $request->assign_user_id;
            $opportunity->name              = $request->name;
            $opportunity->account           = $request->sales_account_id;
            $opportunity->stage             = $request->opportunity_stage_id;
            $opportunity->amount            = $request->amount;
            $opportunity->probability       = $request->probability;
            $opportunity->close_date        = $request->close_date;
            $opportunity->contact           = $request->contact_id;
            $opportunity->lead_source       = $request->lead_source_id;
            $opportunity->description       = $request->description;
            $opportunity->workspace         = $request->workspace_id;
            $opportunity->created_by        = creatorId();
            $opportunity->save();

            Stream::create(
                [
                    'user_id' => Auth::user()->id, 'created_by' => creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => Auth::user()->name,
                            'title' => 'opportunities',
                            'stream_comment' => '',
                            'user_name' => $opportunity->name,
                        ]
                    ),
                ]
            );

            event(new CreateOpportunities($request, $opportunity));

            return response()->json(['status'=>1,'message'=>'The sales opportunities has been created successfully!']);

        } catch (\Exception $e) {
            return response()->json(['status'=>0,'message'=>'something went wrong!!!']);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('sales::show');
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
        try{
            $rules = [
                'workspace_id'  => 'required|exists:work_spaces,id',
                'name' => 'required|max:120',
                'amount' => 'required|numeric|gt:0',
                'probability' => 'required|numeric',
                'opportunity_stage_id' => 'required|exists:opportunities_stages,id',
                'close_date' => 'required|date_format:Y-m-d',
                'sales_account_id' => 'required|exists:sales_accounts,id',
                'contact_id'=>'required|exists:contacts,id',
                'assign_user_id'=>'required|exists:users,id',
            ];
            if(module_is_active('Lead')){
                $rules['lead_source_id'] = 'required|exists:sources,id';
            }
            $validator = Validator::make(
                $request->all(), $rules
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return response()->json(['status' => 0, 'message' => $messages->first()] , 403);
            }

            $opportunity = Opportunities::where('id',$id)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
            if(!$opportunity){
                return response()->json(['status'=>0,'message'=>'Opportunity Not Found!']);
            }

            $opportunity->user_id           = $request->assign_user_id;
            $opportunity->name              = $request->name;
            $opportunity->account           = $request->sales_account_id;
            $opportunity->stage             = $request->opportunity_stage_id;
            $opportunity->amount            = $request->amount;
            $opportunity->probability       = $request->probability;
            $opportunity->close_date        = $request->close_date;
            $opportunity->contact           = $request->contact_id;
            $opportunity->lead_source       = $request->lead_source_id;
            $opportunity->description       = $request->description;
            $opportunity->save();

            Stream::create(
                [
                    'user_id' => Auth::user()->id, 'created_by' => creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => Auth::user()->name,
                            'title' => 'opportunities',
                            'stream_comment' => '',
                            'user_name' => $opportunity->name,
                        ]
                    ),
                ]
            );
            event(new UpdateOpportunities($request, $opportunity));

            return response()->json(['status'=>1,'message'=>'The sales opportunities details are updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status'=>0,'message'=>'something went wrong!!!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request,$id)
    {
        try{
            $validator = Validator::make(
                $request->all(), [
                    'workspace_id'  => 'required|exists:work_spaces,id',
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return response()->json(['status' => 0, 'message' => $messages->first()] , 403);
            }

            $opportunity = Opportunities::where('id',$id)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
            if(!$opportunity){
                return response()->json(['status'=>0,'message'=>'Opportunity Not Found!']);
            }

            event(new DestroyOpportunities($opportunity));

            $opportunity->delete();

            return response()->json(['status'=>1,'message'=>'The sales opportunities has been deleted!']);
        } catch (\Exception $e) {
            return response()->json(['status'=>0,'message'=>'something went wrong!!!']);
        }
    }
}
