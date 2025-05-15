<?php

namespace Workdo\ApiDocsGenerator\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Workdo\Sales\Entities\AccountIndustry;
use Workdo\Sales\Entities\CaseType;
use Workdo\Sales\Entities\CommonCase;
use Workdo\Sales\Entities\Contact;
use Workdo\Sales\Entities\Opportunities;
use Workdo\Sales\Entities\OpportunitiesStage;
use Workdo\Sales\Entities\Quote;
use Workdo\Sales\Entities\QuoteItem;
use Workdo\Sales\Entities\SalesAccount;
use Workdo\Sales\Entities\SalesAccountType;
use Workdo\Sales\Entities\SalesDocument;
use Workdo\Sales\Entities\SalesDocumentFolder;
use Workdo\Sales\Entities\SalesDocumentType;
use Workdo\Sales\Entities\ShippingProvider;
use Workdo\Sales\Entities\Stream;
use Workdo\Sales\Events\CreateCaseType;
use Workdo\Sales\Events\CreateContact;
use Workdo\Sales\Events\CreateSalesAccount;
use Workdo\Sales\Events\CreateSalesAccountIndustry;
use Workdo\Sales\Events\CreateSalesAccountType;
use Workdo\Sales\Events\CreateShippingProvider;
use Workdo\Sales\Events\CreateOpportunitiesstage;
use Workdo\Sales\Events\CreateSalesDocumentType;
use Workdo\Sales\Events\CreateDocumentFolder;
use Workdo\Sales\Events\CreateOpportunities;
use Workdo\Sales\Events\CreateQuote;
use Workdo\Sales\Events\CreateCommonCase;
use Workdo\Sales\Events\CreateSalesDocument;
use Workdo\Sales\Events\DestroyCaseType;
use Workdo\Sales\Events\DestroyContact;
use Workdo\Sales\Events\DestroyOpportunities;
use Workdo\Sales\Events\DestroyOpportunitiesstage;
use Workdo\Sales\Events\DestroySalesAccount;
use Workdo\Sales\Events\DestroySalesAccountIndustry;
use Workdo\Sales\Events\DestroySalesAccountType;
use Workdo\Sales\Events\DestroyShippingProvider;
use Workdo\Sales\Events\DestroySalesDocumentType;
use Workdo\Sales\Events\DestroySalesDocument;
use Workdo\Sales\Events\DestroyCommonCase;
use Workdo\Sales\Events\DestroyDocumentFolder;
use Workdo\Sales\Events\DestroyQuote;
use Workdo\Sales\Events\UpdateContact;
use Workdo\Sales\Events\UpdateSalesAccount;
use Workdo\Sales\Events\UpdateSalesAccountIndustry;
use Workdo\Sales\Events\UpdateOpportunitiesstage;
use Workdo\Sales\Events\UpdateOpportunities;
use Workdo\Sales\Events\UpdateShippingProvider;
use Workdo\Sales\Events\UpdateCaseType;
use Workdo\Sales\Events\UpdateSalesDocumentType;
use Workdo\Sales\Events\UpdateSalesDocument;
use Workdo\Sales\Events\UpdateDocumentFolder;
use Workdo\Sales\Events\UpdateQuote;
use Workdo\Sales\Events\UpdateCommonCase;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function salesAccounts(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $accounts = SalesAccount::with('assign_user')
                    ->where('created_by', creatorId())
                    ->where('workspace',$request->workspace_id)
                    ->get()
                    ->map(function($account) use($request){
                        $industry    = AccountIndustry::where('created_by', creatorId())->where('workspace',$request->workspace_id)->where('id',$account->industry)->first();
                        $accountype  = SalesAccountType::where('created_by', creatorId())->where('workspace',$request->workspace_id)->where('id',$account->type)->first();
                        $document   = SalesDocument::where('created_by', creatorId())->where('workspace',$request->workspace_id)->where('id',$account->document_id)->first();

                        return [
                            'id'                    => $account->id,
                            'name'                  => $account->name,
                            'email'                 => $account->email,
                            'phone'                 => $account->phone,
                            'website'               => $account->website,
                            'billing_address'       => $account->billing_address,
                            'billing_city'          => $account->billing_city,
                            'billing_state'         => $account->billing_state,
                            'billing_country'       => $account->billing_country,
                            'billing_postalcode'    => $account->billing_postalcode,
                            'billing_country'       => $account->billing_country,
                            'shipping_address'      => $account->shipping_address,
                            'shipping_city'         => $account->shipping_city,
                            'shipping_state'        => $account->shipping_state,
                            'shipping_country'      => $account->shipping_country,
                            'shipping_postalcode'   => $account->shipping_postalcode,
                            'description'           => $account->description,
                            'assign_user_name'      => $account->assign_user->name ?? '-',
                            'assign_user_email'     => $account->assign_user->email ?? '-',
                            'industry'              => !empty($industry) ? $industry->name : '',
                            'type'                  => !empty($accountype) ? $accountype->name : '',
                            'document'              => !empty($document) ? $document->name : ''
                        ];
                    });

        return response()->json(['status' => 'success','data' => $accounts]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function salesAccountStore(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $salesAccountType = SalesAccountType::where('created_by', creatorId())->where('workspace',$request->workspace_id)->where('id',$request->type)->first();
        if(!$salesAccountType){
            return response()->json(['status'=>'error','message'=>'Sales Account Type Not Found!']);
        }

        $industry    = AccountIndustry::where('created_by', creatorId())->where('workspace',$request->workspace_id)->where('id',$request->industry)->first();
        if(!$industry){
            return response()->json(['status'=>'error','message'=>'Industry Not Found!']);
        }

        if($request->document_id){
            $document   = SalesDocument::where('created_by', creatorId())->where('workspace',$request->workspace_id)->where('id',$request->document_id)->first();
            if(!$document){
                return response()->json(['status'=>'error','message'=>'Document Not Found!']);
            }
        }

        if($request->user){
            $user = User::where('created_by', creatorId())->where('workspace_id',$request->workspace_id)->where('id',$request->user)->first();
            if(!$user){
                return response()->json(['status'=>'error','message'=>'User Not Found!']);
            }
        }

        $validator = Validator::make(
            $request->all(), [
                'name' => 'required|max:120',
                'email' => 'required|email|unique:sales_accounts',
                'type' => 'required|numeric',
                'industry' => 'required|numeric',
                'shipping_postalcode' => 'required',
                'billing_postalcode' => 'required',
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $salesaccount                      = new SalesAccount();
        $salesaccount->user_id             = $request->user;
        $salesaccount->document_id         = $request->document_id;
        $salesaccount->name                = $request->name;
        $salesaccount->email               = $request->email;
        $salesaccount->phone               = $request->phone;
        $salesaccount->website             = $request->website;
        $salesaccount->billing_address     = $request->billing_address;
        $salesaccount->billing_city        = $request->billing_city;
        $salesaccount->billing_state       = $request->billing_state;
        $salesaccount->billing_country     = $request->billing_country;
        $salesaccount->billing_postalcode  = $request->billing_postalcode;
        $salesaccount->shipping_address    = $request->shipping_address;
        $salesaccount->shipping_city       = $request->shipping_city;
        $salesaccount->shipping_state      = $request->shipping_state;
        $salesaccount->shipping_country    = $request->shipping_country;
        $salesaccount->shipping_postalcode = $request->shipping_postalcode;
        $salesaccount->type                = $request->type;
        $salesaccount->industry            = $request->industry;
        $salesaccount->description         = $request->description;
        $salesaccount->workspace           = $request->workspace_id;
        $salesaccount->created_by          = creatorId();
        $salesaccount->save();

        Stream::create(
            [
                'user_id' => Auth::user()->id,'created_by' => creatorId(),
                'log_type' => 'created',
                'remark' => json_encode(
                    [
                        'owner_name' => Auth::user()->name,
                        'title' => 'account',
                        'stream_comment' => '',
                        'user_name' => $salesaccount->name,
                    ]
                ),
            ]
        );

        event(new CreateSalesAccount($request,$salesaccount));

        return response()->json(['status'=>'success','message'=> 'Sales Account Successfully Created!']);

    }

    public function salesAccountUpdate(Request $request,$id){
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $salesAccountType = SalesAccountType::where('created_by', creatorId())->where('workspace',$request->workspace_id)->where('id',$request->type)->first();
        if(!$salesAccountType){
            return response()->json(['status'=>'error','message'=>'Sales Account Type Not Found!']);
        }

        $industry    = AccountIndustry::where('created_by', creatorId())->where('workspace',$request->workspace_id)->where('id',$request->industry)->first();
        if(!$industry){
            return response()->json(['status'=>'error','message'=>'Industry Not Found!']);
        }

        if($request->document_id){
            $document   = SalesDocument::where('created_by', creatorId())->where('workspace',$request->workspace_id)->where('id',$request->document_id)->first();
            if(!$document){
                return response()->json(['status'=>'error','message'=>'Document Not Found!']);
            }
        }

        if($request->user){
            $user = User::where('created_by', creatorId())->where('workspace_id',$request->workspace_id)->where('id',$request->user)->first();
            if(!$user){
                return response()->json(['status'=>'error','message'=>'User Not Found!']);
            }
        }

        $validator = Validator::make(
            $request->all(), [
                'name' => 'required|max:120',
                'email' => 'required|email|unique:sales_accounts,email,'.$id,
                'type' => 'required|numeric',
                'industry' => 'required|numeric',
                'shipping_postalcode' => 'required',
                'billing_postalcode' => 'required',
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $salesaccount                      = SalesAccount::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$salesaccount){
            return response()->json(['status'=>'error','message'=>'Sales Account Not Found!']);
        }

        $salesaccount->user_id             = $request->user;
        $salesaccount->document_id         = $request->document_id;
        $salesaccount->name                = $request->name;
        $salesaccount->email               = $request->email;
        $salesaccount->phone               = $request->phone;
        $salesaccount->website             = $request->website;
        $salesaccount->billing_address     = $request->billing_address;
        $salesaccount->billing_city        = $request->billing_city;
        $salesaccount->billing_state       = $request->billing_state;
        $salesaccount->billing_country     = $request->billing_country;
        $salesaccount->billing_postalcode  = $request->billing_postalcode;
        $salesaccount->shipping_address    = $request->shipping_address;
        $salesaccount->shipping_city       = $request->shipping_city;
        $salesaccount->shipping_state      = $request->shipping_state;
        $salesaccount->shipping_country    = $request->shipping_country;
        $salesaccount->shipping_postalcode = $request->shipping_postalcode;
        $salesaccount->type                = $request->type;
        $salesaccount->industry            = $request->industry;
        $salesaccount->description         = $request->description;
        $salesaccount->save();

        Stream::create(
            [
                'user_id' => Auth::user()->id,
                'created_by' => creatorId(),
                'log_type' => 'updated',
                'remark' => json_encode(
                    [
                        'owner_name' => Auth::user()->name,
                        'title' => 'account',
                        'stream_comment' => '',
                        'user_name' => $salesaccount->name,
                    ]
                ),
            ]
        );

        event(new UpdateSalesAccount($request,$salesaccount));

        return response()->json(['status'=>'success', 'message'=>'Sales Account Successfully Updated!']);
    }

    public function salesAccountDelete(Request $request,$id){
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $salesaccount                      = SalesAccount::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$salesaccount){
            return response()->json(['status'=>'error','message'=>'Sales Account Not Found!']);
        }
        event(new DestroySalesAccount($salesaccount));

        $salesaccount->delete();

        return response()->json(['status'=>'success','message'=>'Sales Account Successfully Deleted!']);
    }

    public function salesContacts(Request $request){
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $contacts = Contact::with('assign_user')
                ->where('created_by', creatorId())
                ->where('workspace',$request->workspace_id)
                ->get()
                ->map(function($contact){
                    return [
                        'id'                 => $contact->id,
                        'name'               => $contact->name,
                        'email'              => $contact->email,
                        'phone'              => $contact->phone,
                        'contact_address'    => $contact->contact_address,
                        'contact_city'       => $contact->contact_city,
                        'contact_state'      => $contact->contact_state,
                        'contact_country'    => $contact->contact_country,
                        'contact_postalcode' => $contact->contact_postalcode,
                        'description'        => $contact->description,
                        'account_name'       => $contact->account_name,
                        'assign_user_name'   => $contact->assign_user->name,
                        'assign_user_email'  => $contact->assign_user->email
                    ];
                });

        return response()->json(['status'=>'success','data'=>$contacts]);
    }

    public function salesContactCreate(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }
        $account = SalesAccount::where('created_by', creatorId())->where('workspace',$request->workspace_id)->where('id',$request->account)->first();
        if(!$account){
            return response()->json(['status'=>'error','message'=>'Sales Account Not Found!']);
        }

        $user = User::where('workspace_id',$request->workspace_id)->where('id',$request->user_id)->emp()->first();
        if(!$user){
            return response()->json(['status'=>'error','message'=>'Assign User Not Found!']);
        }

        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required|max:120',
                'email' => 'required|email|unique:contacts',
                'contact_postalcode' => 'required',
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
            ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $contact                        = new Contact();
        $contact->user_id               = $request->user_id;
        $contact->name                  = $request->name;
        $contact->account               = $request->account;
        $contact->email                 = $request->email;
        $contact->phone                 = $request->phone;
        $contact->contact_address       = $request->contact_address;
        $contact->contact_city          = $request->contact_city;
        $contact->contact_state         = $request->contact_state;
        $contact->contact_country       = $request->contact_country;
        $contact->contact_postalcode    = $request->contact_postalcode;
        $contact->description           = $request->description;
        $contact->workspace             = $request->workspace_id;
        $contact->created_by            = creatorId();
        $contact->save();

        Stream::create(
            [
                'user_id' => Auth::user()->id,
                'created_by' => creatorId(),
                'log_type' => 'created',
                'remark' => json_encode(
                    [
                        'owner_name' => Auth::user()->name,
                        'title' => 'contact',
                        'stream_comment' => '',
                        'user_name' => $contact->name,
                    ]
                ),
            ]
        );
        event(new CreateContact($request,$contact));

        return response()->json(['status'=>'success', 'message'=> 'Sales Contact Successfully Created!']);
    }

    public function salesContactUpdate(Request $request, $id){
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $account = SalesAccount::where('created_by', creatorId())->where('workspace',$request->workspace_id)->where('id',$request->account)->first();
        if(!$account){
            return response()->json(['status'=>'error','message'=>'Sales Account Not Found!']);
        }

        $user = User::where('workspace_id',$request->workspace_id)->where('id',$request->user_id)->emp()->first();
        if(!$user){
            return response()->json(['status'=>'error','message'=>'Assign User Not Found!']);
        }

        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required|max:120',
                'email' => 'required|email',
                'contact_postalcode' => 'required',
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
            ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $contact = Contact::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$contact){
            return response()->json(['status'=>'error' ,'message' => 'Sales Contact Not Found!']);
        }

        $contact->user_id            = $request->user_id;
        $contact->name               = $request->name;
        $contact->account            = $request->account;
        $contact->email              = $request->email;
        $contact->phone              = $request->phone;
        $contact->contact_address    = $request->contact_address;
        $contact->contact_city       = $request->contact_city;
        $contact->contact_state      = $request->contact_state;
        $contact->contact_country    = $request->contact_country;
        $contact->contact_postalcode = $request->contact_postalcode;
        $contact->description        = $request->description;
        $contact->workspace          = $request->workspace_id;
        $contact->created_by         = creatorId();
        $contact->save();

        Stream::create(
            [
                'user_id' => Auth::user()->id,
                'created_by' => creatorId(),
                'log_type' => 'updated',
                'remark' => json_encode(
                    [
                        'owner_name' => Auth::user()->name,
                        'title' => 'contact',
                        'stream_comment' => '',
                        'user_name' => $contact->name,
                    ]
                ),
            ]
        );

        event(new UpdateContact($request,$contact));

        return response()->json(['status'=>'success', 'message' => 'Sales Contact Successfully Updated!']);
    }

    public function salesContactDelete(Request $request , $id){
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }
        $contact = Contact::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$contact){
            return response()->json(['status'=>'error' ,'message' => 'Sales Contact Not Found!']);
        }

        event(new DestroyContact($contact));

        $contact->delete();

        return response()->json(['status'=>'success', 'message'=>'Sales Contact Successfully Deleted!']);
    }

    public function salesAccountType(Request $request){
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        return SalesAccountType::where('created_by', creatorId())
                ->where('workspace',$request->workspace_id)
                ->get()
                ->map(function($accountType){
                    return [
                        'id'    => $accountType->id,
                        'name'    => $accountType->name,
                    ];
                });
    }

    public function salesAccountTypeCreate(Request $request){
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $validator = Validator::make(
            $request->all(), ['name' => 'required|max:40',]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $salesaccounttype               = new SalesAccountType();
        $salesaccounttype->name         = $request->name;
        $salesaccounttype->workspace    = $request->workspace_id;
        $salesaccounttype->created_by   = creatorId();
        $salesaccounttype->save();

        event(new CreateSalesAccountType($request,$salesaccounttype));

        return response()->json(['status'=>'success', 'message'=> 'Sales Account Type Successfully Created!']);

    }

    public function salesAccountTypeUpdate(Request $request,$id){
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $validator = Validator::make(
            $request->all(), ['name' => 'required|max:40',]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $salesAccountType = SalesAccountType::where('created_by', creatorId())
                            ->where('workspace',$request->workspace_id)
                            ->where('id',$id)
                            ->first();

        if(!$salesAccountType){
            return response()->json(['status'=>'error','message'=>'Sales Account Type Not Found!']);
        }

        $salesAccountType->name     = $request->name;
        $salesAccountType->save();

        return response()->json(['status'=>'success','message'=>'Sales Account Type successfully updated!']);
    }

    public function salesAccountTypeDelete(Request $request,$id)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $salesAccountType = SalesAccountType::where('created_by', creatorId())
                            ->where('workspace',$request->workspace_id)
                            ->where('id',$id)
                            ->first();

        if(!$salesAccountType){
            return response()->json(['status'=>'error','message'=>'Sales Account Type Not Found!']);
        }

        $salesAccountType->delete();
        event(new DestroySalesAccountType($salesAccountType));

        return response()->json(['status'=>'success','message'=>'Sales Account Type successfully deleted!']);

    }

    public function salesAccountIndustry(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $industrys = AccountIndustry::where('created_by', creatorId())
                    ->where('workspace',$request->workspace_id)
                    ->get()
                    ->map(function($industry){
                        return [
                            'id'    => $industry->id,
                            'name'  => $industry->name
                        ];
                    });

        return response()->json(['status'=>'success','data'=>$industrys]);
    }

    public function salesAccountIndustryCreate(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $validator = Validator::make(
            $request->all(), ['name' => 'required|max:40',]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $accountIndustry               = new accountIndustry();
        $accountIndustry->name         = $request->name;
        $accountIndustry->workspace    = $request->workspace_id;
        $accountIndustry->created_by   = creatorId();
        $accountIndustry->save();

        event(new CreateSalesAccountIndustry($request,$accountIndustry));

        return response()->json(['status'=>'success', 'message' => 'Sales Account Industry successfully created!']);
    }

    public function salesAccountIndustryUpdate(Request $request ,$id)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $validator = Validator::make(
            $request->all(), ['name' => 'required|max:40',]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $accountIndustry = accountIndustry::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$accountIndustry){
            return response()->json(['status'=>'error','message'=>'Sales Account Industry Not Found!']);
        }
        $accountIndustry->name = $request->name;
        $accountIndustry->save();

        event(new UpdateSalesAccountIndustry($request,$accountIndustry));

        return response()->json(['status'=>'success', 'message' => 'Sales Account Industry successfully updated!']);
    }

    public function salesAccountIndustryDelete(Request $request,$id)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $accountIndustry = accountIndustry::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$accountIndustry){
            return response()->json(['status'=>'error','message'=>'Sales Account Industry Not Found!']);
        }
        event(new DestroySalesAccountIndustry($accountIndustry));
        $accountIndustry->delete();

        return response()->json(['status'=>'success', 'message' => 'Sales Account Industry successfully deleted!']);
    }

    public function salesOpportunity(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $opportunityStages = OpportunitiesStage::where('created_by', creatorId())
                                ->where('workspace',$request->workspace_id)
                                ->get()
                                ->map(function($opportunityStage){
                                    return [
                                        'id'    => $opportunityStage->id,
                                        'name'  => $opportunityStage->name
                                    ];
                                });
        return response()->json(['status'=>'success','data'=>$opportunityStages]);
    }

    public function salesOpportunityCreate(Request $request){
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }
        $validator = \Validator::make(
            $request->all(), [
                               'name' => 'required|max:120',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $opportunitiesstage               = new OpportunitiesStage();
        $opportunitiesstage->name         = $request->name;
        $opportunitiesstage->workspace    = $request->workspace_id;
        $opportunitiesstage->created_by   = creatorId();
        $opportunitiesstage->save();

        event(new CreateOpportunitiesstage($request,$opportunitiesstage));

        return response()->json(['status'=>'success','message'=>'Sales Opportunities Stage successfully created!']);
    }

    public function salesOpportunityUpdate(Request $request,$id){
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $validator = Validator::make(
            $request->all(), [
                               'name' => 'required|max:120',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $opportunitiesStage = OpportunitiesStage::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$opportunitiesStage){
            return response()->json(['status'=>'error', 'message' => 'Sales Opportunities Stage Not Found!']);
        }

        $opportunitiesStage->name         = $request->name;
        $opportunitiesStage->save();

        event(new UpdateOpportunitiesstage($request,$opportunitiesStage));

        return response()->json(['status'=>'success', 'message' => 'Sales Opportunities Stage successfully updated!']);
    }

    public function salesOpportunityDelete(Request $request,$id)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $opportunitiesStage = OpportunitiesStage::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$opportunitiesStage){
            return response()->json(['status'=>'error', 'message' => 'Sales Opportunities Stage Not Found!']);
        }

        event(new DestroyOpportunitiesstage($opportunitiesStage));

        $opportunitiesStage->delete();

        return response()->json(['status'=>'success','message'=>'Sales Opportunities Stage successfully deleted!']);

    }

    public function salesCaseType(Request $request){
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $salesTypes = CaseType::where('created_by', creatorId())
                        ->where('workspace',$request->workspace_id)
                        ->get()
                        ->map(function($salesType){
                            return [
                                'id'    => $salesType->id,
                                'name'  => $salesType->name
                            ];
                        });
        return response()->json(['status' => 'success','data' => $salesTypes]);
    }

    public function salesCaseTypeCreate(Request $request){
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $validator = Validator::make(
            $request->all(), [
                               'name' => 'required|max:120',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=> $messages->first()]);
        }

        $casetype               = new CaseType();
        $casetype->name         = $request->name;
        $casetype->workspace    = $request->workspace_id;
        $casetype->created_by   = creatorId();
        $casetype->save();

        event(new CreateCaseType($request,$casetype));

        return response()->json(['status'=>'success','message'=>'Sales Case Type successfully created!']);
    }

    public function salesCaseTypeUpdate(Request $request, $id)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $validator = Validator::make(
            $request->all(), [
                               'name' => 'required|max:120',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=> $messages->first()]);
        }

        $caseType = CaseType::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$caseType){
            return response()->json(['status'=>'error','message'=>'Sales Case Type Not Found!']);
        }

        $caseType->name = $request->name;
        $caseType->save();

        event(new UpdateCaseType($request,$caseType));

        return response()->json(['status'=>'error','message'=>'Sales Case Type successfully updated!']);
    }

    public function salesCaseTypeDelete(Request $request,$id){
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $caseType = CaseType::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$caseType){
            return response()->json(['status'=>'error','message'=>'Sales Case Type Not Found!']);
        }

        event(new DestroyCaseType($caseType));

        $caseType->delete();

        return response()->json(['status'=>'success','message'=>'Sales Case Type successfully deleted!']);
    }

    public function salesShippingProvider(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $shippingProviders = ShippingProvider::where('created_by', creatorId())
                                                ->where('workspace', $request->workspace_id)
                                                ->get()
                                                ->map(function($shippingProvider){
                                                    return [
                                                        'id'        => $shippingProvider->id,
                                                        'name'      => $shippingProvider->name,
                                                        'website'   => $shippingProvider->website
                                                    ];
                                                });

        return response()->json(['status' => 'success','data' => $shippingProviders]);
    }

    public function salesShippingProviderCreate(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:120',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=> $messages->first()]);
        }

        $shippingprovider               = new ShippingProvider();
        $shippingprovider->name         = $request->name;
        $shippingprovider->website      = $request->website;
        $shippingprovider->workspace    = $request->workspace_id;
        $shippingprovider->created_by   = creatorId();
        $shippingprovider->save();

        event(new CreateShippingProvider($request, $shippingprovider));

        return response()->json(['status'=>'success', 'message'=>'Shipping Provider successfully created!']);
    }

    public function salesShippingProviderUpdate(Request $request, $id)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:120',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=> $messages->first()]);
        }

        $shippingprovider = ShippingProvider::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$shippingprovider){
            return response()->json(['status'=>'error','message'=>'Shipping Provider Not Found!']);
        }

        $shippingprovider->name     = $request->name;
        $shippingprovider->website  = $request->website;
        $shippingprovider->save();

        event(new UpdateShippingProvider($request, $shippingprovider));

        return response()->json(['status'=>'success','message'=>'Shipping Provider Successfully updated!']);
    }

    public function salesShippingProviderDelete(Request $request , $id){
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $shippingProvider = ShippingProvider::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$shippingProvider){
            return response()->json(['status'=>'error','message'=>'Shipping Provider Not Found!']);
        }

        event(new DestroyShippingProvider($shippingProvider));
        $shippingProvider->delete();

        return response()->json(['status'=>'success','message'=>'Shipping Provider Successfully deleted!']);
    }

    public function salesDocumentType(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $documentTypes = SalesDocumentType::where('created_by', creatorId())
                                            ->where('workspace', $request->workspace_id)
                                            ->get()
                                            ->map(function($documentType){
                                                return [
                                                    'id'    => $documentType->id,
                                                    'name'  => $documentType->name
                                                ];
                                            });
        return response()->json(['status'=>'success','data'=>$documentTypes]);
    }

    public function salesDocumentTypeCreate(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $validator = Validator::make(
            $request->all(),
            ['name' => 'required|max:40']
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error','message'=> $messages->first()]);
        }

        $salesdocumenttype                      = new SalesDocumentType();
        $salesdocumenttype->name                = $request->name;
        $salesdocumenttype->workspace           = $request->workspace_id;
        $salesdocumenttype->created_by          = creatorId();
        $salesdocumenttype->save();

        event(new CreateSalesDocumentType($request, $salesdocumenttype));

        return response()->json(['status'=>'success', 'message'=> 'Document Type successfully created!']);
    }

    public function salesDocumentTypeUpdate(Request $request,$id)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $validator = Validator::make(
            $request->all(),
            ['name' => 'required|max:40']
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error','message'=> $messages->first()]);
        }

        $salesdocumenttype = SalesDocumentType::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$salesdocumenttype){
            return response()->json(['status'=>'error','message'=>'Sales Document Type Not Found!']);
        }
        $salesdocumenttype->name = $request->name;
        $salesdocumenttype->save();

        event(new UpdateSalesDocumentType($request, $salesdocumenttype));

        return response()->json(['status'=>'success','message'=>'Document Type successfully updated!']);
    }

    public function salesDocumentTypeDelete(Request $request,$id)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }
        $salesdocumenttype = SalesDocumentType::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$salesdocumenttype){
            return response()->json(['status'=>'error','message'=>'Sales Document Type Not Found!']);
        }

        event(new DestroySalesDocumentType($salesdocumenttype));

        $salesdocumenttype->delete();

        return response()->json(['status'=>'success','message'=>'Document successfully deleted!']);
    }

    public function salesDocumentFolder(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }
        $documentFolders = SalesDocumentFolder::where('created_by', creatorId())
                                        ->where('workspace', $request->workspace_id)
                                        ->get()
                                        ->map(function($folder){
                                            return [
                                                'id'    => $folder->id,
                                                'name'    => $folder->name,
                                                'parent'    => !empty($folder->parents->name)?$folder->parents->name: '-',
                                                'description' => $folder->description
                                            ];
                                        });

        return response()->json(['status'=>'success','data'=>$documentFolders]);
    }

    public function salesDocumentFolderCreate(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:120',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['statuss'=>'error', 'message'=> $messages->first()]);
        }

        $documentfolder               = new SalesDocumentFolder();
        $documentfolder->name         = $request->name;
        $documentfolder->parent       = $request->parent;
        $documentfolder->description  = $request->description;
        $documentfolder->workspace    = $request->workspace_id;
        $documentfolder->created_by   = creatorId();
        $documentfolder->save();

        event(new CreateDocumentFolder($request, $documentfolder));

        return response()->json(['status'=>'success', 'message' => 'Document Folders successfully created!']);
    }

    public function salesDocumentFolderUpdate(Request $request, $id)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:120',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['statuss'=>'error', 'message'=> $messages->first()]);
        }

        $salesDocumentFolder = SalesDocumentFolder::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$salesDocumentFolder){
            return response()->json(['status'=>'error','message'=>'Sales Document Folder Not Found!']);
        }

        $salesDocumentFolder->name = $request->name;
        $salesDocumentFolder->parent = $request->parent;
        $salesDocumentFolder->description = $request->description;
        $salesDocumentFolder->save();

        event(new UpdateDocumentFolder($request, $salesDocumentFolder));

        return response()->json(['status'=>'success','message'=>'Document Folders successfully updated!']);

    }

    public function salesDocumentFolderDelete(Request $request, $id)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $salesDocumentFolder = SalesDocumentFolder::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$salesDocumentFolder){
            return response()->json(['status'=>'error','message'=>'Sales Document Folder Not Found!']);
        }

        event(new DestroyDocumentFolder($salesDocumentFolder));

        $salesDocumentFolder->delete();

        return response()->json(['status'=>'success','message'=>'Document Folders successfully deleted!']);
    }

    public function salesOpportunities(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }
        $opportunities = Opportunities::with('stages', 'accounts', 'assign_user')
                        ->where('workspace', $request->workspace_id)
                        ->get()
                        ->map(function($opportunity){
                            return [
                                'id'            => $opportunity->id,
                                'name'          => $opportunity->name,
                                'account'       => !empty($opportunity->accounts) ? $opportunity->accounts->name : '-',
                                'stage'         => !empty($opportunity->stages) ? $opportunity->stages->name : '-',
                                'amount'        => currency_format_with_sym( $opportunity->amount),
                                'probability'   => $opportunity->probability,
                                'close_date'    => company_date_formate($opportunity->close_date),
                                'contacts'      => !empty($opportunity->contacts) ? $opportunity->contacts : '-',
                                'lead_source'   => !empty($opportunity->leadsource) ? $opportunity->leadsource : '-',
                                'description'   => $opportunity->description,
                                'assign_user'   => !empty($opportunity->assign_user) ? $opportunity->assign_user->name : '-'
                            ];
                        });

        return response()->json(['status'=>'success','data'=>$opportunities]);
    }

    public function salesOpportunitiesCreate(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }
        $salesAccount = SalesAccount::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$request->account)->first();
        if(!$salesAccount){
            return response()->json(['status'=>'error','message'=>'Sales Account Not Found!']);
        }

        $opportunityStage = OpportunitiesStage::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$request->stage)->first();
        if(!$opportunityStage){
            return response()->json(['status'=>'error','message'=>'Opportunity Stage Not Found!']);
        }
        if(!empty($request->contact)){
            $contact = Contact::where('id',$request->contact)->where('workspace', $request->workspace_id)->where('created_by', creatorId())->first();
            if(!$contact){
                return response()->json(['status'=>'error','message'=>'Contact Not Found!']);
            }
        }

        if(module_is_active('Lead')){
            if(!empty($request->lead_source)){
                $leadsource = \Workdo\Lead\Entities\Source::where('id',$request->lead_source)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
                if(!$leadsource){
                    return response()->json(['status'=>'error','message'=>'Lead Not Found!']);
                }
            }
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:120',
                'amount' => 'required|numeric',
                'probability' => 'required|numeric',
                'stage' => 'required',
                'close_date' => 'required|date_format:Y-m-d',
                'account' => 'required'
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['statuss'=>'error', 'message'=> $messages->first()]);
        }

        $opportunity                    = new Opportunities();
        $opportunity->user_id           = $request->user;
        $opportunity->name              = $request->name;
        $opportunity->account           = $request->account;
        $opportunity->stage             = $request->stage;
        $opportunity->amount            = $request->amount;
        $opportunity->probability       = $request->probability;
        $opportunity->close_date        = $request->close_date;
        $opportunity->contact           = $request->contact;
        $opportunity->lead_source       = $request->lead_source;
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

        return response()->json(['statuss'=>'success', 'message'=> 'Sales Opportunities Successfully Created!']);
    }

    public function salesOpportunitiesUpdate(Request $request, $id)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $salesAccount = SalesAccount::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$request->account)->first();
        if(!$salesAccount){
            return response()->json(['status'=>'error','message'=>'Sales Account Not Found!']);
        }

        $opportunityStage = OpportunitiesStage::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$request->stage)->first();
        if(!$opportunityStage){
            return response()->json(['status'=>'error','message'=>'Opportunity Stage Not Found!']);
        }
        if(!empty($request->contact)){
            $contact = Contact::where('id',$request->contact)->where('workspace', $request->workspace_id)->where('created_by', creatorId())->first();
            if(!$contact){
                return response()->json(['status'=>'error','message'=>'Contact Not Found!']);
            }
        }

        if(module_is_active('Lead')){
            if(!empty($request->lead_source)){
                $leadsource = \Workdo\Lead\Entities\Source::where('id',$request->lead_source)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
                if(!$leadsource){
                    return response()->json(['status'=>'error','message'=>'Lead Not Found!']);
                }
            }
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:120',
                'amount' => 'required|numeric',
                'probability' => 'required|numeric',
                'stage' => 'required',
                'close_date' => 'required|date_format:Y-m-d',
                'account' => 'required'
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['statuss'=>'error', 'message'=> $messages->first()]);
        }

        $opportunity = Opportunities::where('id',$id)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$opportunity){
            return response()->json(['status'=>'error','message'=>'Opportunity Not Found!']);
        }

        $opportunity->user_id     = $request->user;
        $opportunity->name        = $request->name;
        $opportunity->account     = $request->account;
        $opportunity->contact     = $request->contact;
        $opportunity->stage       = $request->stage;
        $opportunity->amount      = $request->amount;
        $opportunity->probability = $request->probability;
        $opportunity->close_date  = $request->close_date;
        $opportunity->lead_source = $request->lead_source;
        $opportunity->description = $request->description;
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

        return response()->json(['status'=>'success','message'=>'Sales Opportunities Successfully Updated!']);
    }

    public function salesOpportunitiesDelete(Request $request, $id)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $opportunity = Opportunities::where('id',$id)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$opportunity){
            return response()->json(['status'=>'error','message'=>'Opportunity Not Found!']);
        }

        event(new DestroyOpportunities($opportunity));

        $opportunity->delete();

        return response()->json(['status'=>'success','message'=>'Sales Opportunities Successfully Deleted!']);
    }

    public function salesQuotes(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $quotes = Quote::with(['accounts','assign_user'])
                        ->where('created_by', creatorId())
                        ->where('workspace',$request->workspace_id)
                        ->get()
                        ->map(function($quote){
                            return [
                                'id'                    => $quote->id,
                                'quote_id'              => Quote::quoteNumberFormat($quote->quote_id),
                                'name'                  => $quote->name,
                                'amount'                => currency_format_with_sym($quote->getTotal()),
                                'date_quoted'           => company_date_formate($quote->date_quoted),
                                'user'                  => $quote->assign_user->name,
                                'quote_number'          => $quote->quote_number,
                                'status'                => Quote::$status[$quote->status],
                                'account'               => $quote->accounts->name,
                                'opportunity'           => $quote->opportunitys->name,
                                'billing_address'       => $quote->billing_address,
                                'billing_city'          => $quote->billing_city,
                                'billing_state'         => $quote->billing_state,
                                'billing_country'       => $quote->billing_country,
                                'billing_postalcode'    => $quote->billing_postalcode,
                                'shipping_address'      => $quote->shipping_address,
                                'shipping_city'         => $quote->shipping_city,
                                'shipping_state'        => $quote->shipping_state,
                                'shipping_country'      => $quote->shipping_country,
                                'shipping_postalcode'   => $quote->shipping_postalcode,
                                'billing_contact'       => $quote->contacts->name
                            ];
                        });

        return response()->json(['status'=>'success','data'=>$quotes]);
    }

    public function salesQuotesCreate(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $salesAccount = SalesAccount::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$request->account)->first();
        if(!$salesAccount){
            return response()->json(['status'=>'error','message'=>'Sales Account Not Found!']);
        }

        $opportunity = Opportunities::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$request->opportunity)->first();
        if(!$opportunity){
            return response()->json(['status'=>'error','message'=>'Opportunity Not Found!']);
        }

        if(!empty($request->billing_contact)){
            $contact = Contact::where('id',$request->billing_contact)->where('workspace', $request->workspace_id)->where('created_by', creatorId())->first();
            if(!$contact){
                return response()->json(['status'=>'error','message'=>'Billing Contact Not Found!']);
            }
        }
        if(!empty($request->shipping_contact)){
            $contact = Contact::where('id',$request->shipping_contact)->where('workspace', $request->workspace_id)->where('created_by', creatorId())->first();
            if(!$contact){
                return response()->json(['status'=>'error','message'=>'Shipping Contact Not Found!']);
            }
        }

        $shippingProvider = ShippingProvider::where('created_by', creatorId())->where('workspace',$request->workspace_id)->where('id',$request->shipping_provider)->first();
        if(!$shippingProvider){
            return response()->json(['status'=>'error','message'=>'Shipping Provider Not Found!']);
        }

        $user = User::where('created_by', creatorId())->where('workspace_id',$request->workspace_id)->where('id',$request->user)->first();
        if(!$user){
            return response()->json(['status'=>'error','message'=>'User Not Found!']);
        }

        if(module_is_active('ProductService')){
            $tax =  \Workdo\ProductService\Entities\Tax::where('created_by', creatorId())->where('workspace_id',$request->workspace_id)->pluck('id')->toArray();

            if(empty(array_intersect(json_decode($request->tax),$tax))){
                return response()->json(['status'=>'error','message'=>'Tax Not Found!']);
            }
        }

        $validator = Validator::make(
            $request->all(), [
                               'name' => 'required|max:120',
                               'shipping_postalcode' => 'required',
                               'billing_postalcode' => 'required',
                               'user' => 'required',
                               'date_quoted' => 'required|date_format:Y-m-d',
                               'status'=>'required|in:'.implode(',',Quote::$status)
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error','message'=> $messages->first()]);
        }

        $quote                          = new Quote();
        $quote->user_id                 = $request->user;
        $quote->quote_id                = $this->quoteNumber();
        $quote->name                    = $request->name;
        $quote->opportunity             = $request->opportunity;
        $quote->status                  = array_flip(Quote::$status)[$request->status];
        $quote->account                 = $request->account;
        $quote->date_quoted             = $request->date_quoted;
        $quote->quote_number            = $request->quote_number;
        $quote->billing_address         = $request->billing_address;
        $quote->billing_city            = $request->billing_city;
        $quote->billing_state           = $request->billing_state;
        $quote->billing_country         = $request->billing_country;
        $quote->billing_postalcode      = $request->billing_postalcode;
        $quote->shipping_address        = $request->shipping_address;
        $quote->shipping_city           = $request->shipping_city;
        $quote->shipping_state          = $request->shipping_state;
        $quote->shipping_country        = $request->shipping_country;
        $quote->shipping_postalcode     = $request->shipping_postalcode;
        $quote->billing_contact         = $request->billing_contact;
        $quote->shipping_contact        = $request->shipping_contact;
        $quote->tax                     = implode(',', json_decode($request->tax));
        $quote->shipping_provider       = $request->shipping_provider;
        $quote->description             = $request->description;
        $quote->workspace               = $request->workspace_id;
        $quote->created_by              = creatorId();
        $quote->save();

        Stream::create(
            [
                'user_id' => Auth::user()->id,
                'created_by' =>creatorId(),
                'log_type' => 'created',
                'remark' => json_encode(
                    [
                        'owner_name' => Auth::user()->name,
                        'title' => 'quote',
                        'stream_comment' => '',
                        'user_name' => $quote->name,
                    ]
                ),
            ]
        );

        $company_settings = getCompanyAllSetting();

        if(!empty($company_settings['New Quotation']) && $company_settings['New Quotation']  == true)
        {
            $Assign_user_phone = User::where('id',$request->user)->where('workspace_id', '=',  $request->workspace_id)->first();

            $uArr = [
                'quote_number' => $request->quote_number,
                'billing_address' => $request->billing_address,
                'shipping_address' => $request->shipping_address,
                'description' => $request->description,
                'date_quoted' => $request->date_quoted,
                'quote_assign_user' => $Assign_user_phone->name,
            ];

            $resp = EmailTemplate::sendEmailTemplate('New Quotation', [$quote->id => $Assign_user_phone->email], $uArr);
        }
        event(new CreateQuote($request,$quote));

        return response()->json(['status'=>'success', 'message' => 'Quote Successfully Created!']);

    }

    public function salesQuotesUpdate(Request $request , $id){
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $salesAccount = SalesAccount::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$request->account)->first();
        if(!$salesAccount){
            return response()->json(['status'=>'error','message'=>'Sales Account Not Found!']);
        }

        $opportunity = Opportunities::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$request->opportunity)->first();
        if(!$opportunity){
            return response()->json(['status'=>'error','message'=>'Opportunity Not Found!']);
        }

        if(!empty($request->billing_contact)){
            $contact = Contact::where('id',$request->billing_contact)->where('workspace', $request->workspace_id)->where('created_by', creatorId())->first();
            if(!$contact){
                return response()->json(['status'=>'error','message'=>'Billing Contact Not Found!']);
            }
        }
        if(!empty($request->shipping_contact)){
            $contact = Contact::where('id',$request->shipping_contact)->where('workspace', $request->workspace_id)->where('created_by', creatorId())->first();
            if(!$contact){
                return response()->json(['status'=>'error','message'=>'Shipping Contact Not Found!']);
            }
        }

        $shippingProvider = ShippingProvider::where('created_by', creatorId())->where('workspace',$request->workspace_id)->where('id',$request->shipping_provider)->first();
        if(!$shippingProvider){
            return response()->json(['status'=>'error','message'=>'Shipping Provider Not Found!']);
        }

        $user = User::where('created_by', creatorId())->where('workspace_id',$request->workspace_id)->where('id',$request->user)->first();
        if(!$user){
            return response()->json(['status'=>'error','message'=>'User Not Found!']);
        }

        if(module_is_active('ProductService')){
            $tax =  \Workdo\ProductService\Entities\Tax::where('created_by', creatorId())->where('workspace_id',$request->workspace_id)->pluck('id')->toArray();
            if(!empty(array_diff(json_decode($request->tax), $tax))){
                return response()->json(['status'=>'error','message'=>'Tax Not Found!']);
            }
        }

        $validator = Validator::make(
            $request->all(), [
                               'name' => 'required|max:120',
                               'shipping_postalcode' => 'required',
                               'billing_postalcode' => 'required',
                               'user' => 'required',
                               'date_quoted' => 'required|date_format:Y-m-d',
                               'status'=>'required|in:'.implode(',',Quote::$status)
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error','message'=> $messages->first()]);
        }

        $quote = Quote::where('id',$id)->where('created_by', creatorId())->where('workspace',$request->workspace_id)->first();
        if(!$quote){
            return response()->json(['status'=>'error','message'=> 'Quote Not Found!']);
        }

        $quote->user_id             = $request->user;
        $quote->name                = $request->name;
        $quote->opportunity         = $request->opportunity;
        $quote->status              = $request->status;
        $quote->account             = $request->account;
        $quote->date_quoted         = $request->date_quoted;
        $quote->quote_number        = $request->quote_number;
        $quote->billing_address     = $request->billing_address;
        $quote->billing_city        = $request->billing_city;
        $quote->billing_state       = $request->billing_state;
        $quote->billing_country     = $request->billing_country;
        $quote->billing_postalcode  = $request->billing_postalcode;
        $quote->shipping_address    = $request->shipping_address;
        $quote->shipping_city       = $request->shipping_city;
        $quote->shipping_state      = $request->shipping_state;
        $quote->shipping_country    = $request->shipping_country;
        $quote->shipping_postalcode = $request->shipping_postalcode;
        $quote->billing_contact     = $request->billing_contact;
        $quote->shipping_contact    = $request->shipping_contact;
        $quote->tax                 = implode(',', json_decode($request->tax));
        $quote->shipping_provider   = $request->shipping_provider;
        $quote->description         = $request->description;
        $quote->save();

        Stream::create(
            [
                'user_id' => Auth::user()->id,
                'created_by' => creatorId(),
                'log_type' => 'updated',
                'remark' => json_encode(
                    [
                        'owner_name' => Auth::user()->name,
                        'title' => 'quote',
                        'stream_comment' => '',
                        'user_name' => $quote->name,
                    ]
                ),
            ]
        );

        event(new UpdateQuote($request,$quote));

        return response()->json(['status'=>'success', 'message'=> 'Quote Successfully Updated!']);

    }

    public function salesQuoteDelete(Request $request , $id){
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $quote = Quote::where('id',$id)->where('created_by', creatorId())->where('workspace',$request->workspace_id)->first();
        if(!$quote){
            return response()->json(['status'=>'error','message'=> 'Quote Not Found!']);
        }

        event(new DestroyQuote($quote));

        QuoteItem::where('quote_id',$quote->id)->where('created_by',$quote->created_by)->delete();
        $quote->delete();

        return response()->json(['status'=>'success','message'=>'Quote Successfully Deleted!']);
    }

    public static function quoteNumber()
    {
        $latest = Quote::where('created_by', '=', creatorId())->latest()->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->quote_id + 1;
    }

    public function salesCases(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $cases = CommonCase::with('accounts','assign_user')
                ->where('created_by', creatorId())
                ->where('workspace',$request->workspace_id)
                ->get()
                ->map(function($case){
                    return [
                        'id'            => $case->id,
                        'user'          => $case->assign_user->name,
                        'name'          => $case->name,
                        'number'        => $case->number,
                        'status'        => CommonCase::$status[$case->status],
                        'account'       => $case->accounts->name,
                        'priority'      => CommonCase::$priority[$case->priority],
                        'contact'       => $case->contacts->name,
                        'type'          => $case->types->name,
                        'description'   => $case->description
                    ];
                });

        return response()->json(['status'=>'success','data'=>$cases]);
    }

    public function salesCaseCreate(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $salesAccount = SalesAccount::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$request->account)->first();
        if(!$salesAccount){
            return response()->json(['status'=>'error','message'=>'Sales Account Not Found!']);
        }

        $contact = Contact::where('id',$request->contact)->where('workspace', $request->workspace_id)->where('created_by', creatorId())->first();
        if(!$contact){
            return response()->json(['status'=>'error','message'=>'Sales Contact Not Found!']);
        }

        $caseType = CaseType::where('id',$request->type)->where('workspace', $request->workspace_id)->where('created_by', creatorId())->first();
        if(!$caseType){
            return response()->json(['status'=>'error','message'=>'Case Type Not Found!']);
        }

        $user = User::where('created_by', creatorId())->where('workspace_id',$request->workspace_id)->where('id',$request->user)->first();
        if(!$user){
            return response()->json(['status'=>'error','message'=>'User Not Found!']);
        }

        $validator = Validator::make(
            $request->all(), [
                'name' => 'required|max:120',
                'priority'=>'required|in:'.implode(',',CommonCase::$priority),
                'status'  => 'required|in:'.implode(',',CommonCase::$status)
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=>$messages->first()]);
        }

        $commoncase                 = new CommonCase();
        $commoncase->user_id        = $request->user;
        $commoncase->name           = $request->name;
        $commoncase->number         = $this->caseNumber($request->workspace_id);
        $commoncase->status         = array_flip(CommonCase::$status)[$request->status];
        $commoncase->account        = $request->account;
        $commoncase->priority       = array_flip(CommonCase::$priority)[$request->priority];
        $commoncase->contact        = $request->contact;
        $commoncase->type           = $request->type;
        $commoncase->description    = $request->description;
        $commoncase->attachments    = '';
        $commoncase->workspace      = $request->workspace_id;
        $commoncase->created_by     = creatorId();
        $commoncase->save();

        Stream::create(
            [
                'user_id' => Auth::user()->id,'created_by' => creatorId(),
                'log_type' => 'created',
                'remark' => json_encode(
                    [
                        'owner_name' => Auth::user()->name,
                        'title' => 'commoncase',
                        'stream_comment' => '',
                        'user_name' => $commoncase->name,
                    ]
                ),
            ]
        );

        event(new CreateCommonCase($request,$commoncase));

        return response()->json(['status'=>'success','message'=> 'Common Case Successfully Created!']);
    }

    public function salesCasesUpdate(Request $request , $id)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $salesAccount = SalesAccount::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$request->account)->first();
        if(!$salesAccount){
            return response()->json(['status'=>'error','message'=>'Sales Account Not Found!']);
        }

        $contact = Contact::where('id',$request->contact)->where('workspace', $request->workspace_id)->where('created_by', creatorId())->first();
        if(!$contact){
            return response()->json(['status'=>'error','message'=>'Sales Contact Not Found!']);
        }

        $caseType = CaseType::where('id',$request->type)->where('workspace', $request->workspace_id)->where('created_by', creatorId())->first();
        if(!$caseType){
            return response()->json(['status'=>'error','message'=>'Case Type Not Found!']);
        }

        $user = User::where('created_by', creatorId())->where('workspace_id',$request->workspace_id)->where('id',$request->user)->first();
        if(!$user){
            return response()->json(['status'=>'error','message'=>'User Not Found!']);
        }

        $validator = Validator::make(
            $request->all(), [
                'name' => 'required|max:120',
                'priority'=>'required|in:'.implode(',',CommonCase::$priority),
                'status'  => 'required|in:'.implode(',',CommonCase::$status)
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=>$messages->first()]);
        }

        $commoncase = CommonCase::where('id',$id)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$commoncase){
            return response()->json(['status'=>'error','message'=>'Common Case Not Found!']);
        }

        $commoncase->user_id     = $request->user;
        $commoncase->name        = $request->name;
        $commoncase->status      = $request->status;
        $commoncase->account     = $request->account;
        $commoncase->priority    = $request->priority;
        $commoncase->contact     = $request->contact;
        $commoncase->type        = $request->type;
        $commoncase->description = $request->description;
        $commoncase->save();

        Stream::create(
            [
                'user_id' => Auth::user()->id,'created_by' => creatorId(),
                'log_type' => 'updated',
                'remark' => json_encode(
                    [
                        'owner_name' => Auth::user()->name,
                        'title' => 'commonCase',
                        'stream_comment' => '',
                        'user_name' => $commoncase->name,
                    ]
                ),
            ]
        );

        event(new UpdateCommonCase($request,$commoncase));

        return response()->json(['status'=>'success','message'=> 'Common Cases Successfully updated!']);

    }

    public function salesCaseDelete(Request $request , $id)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $commoncase = CommonCase::where('id',$id)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$commoncase){
            return response()->json(['status'=>'error','message'=>'Common Case Not Found!']);
        }

        if(!empty($commoncase->attachments))
        {
            delete_file($commoncase->attachments);
        }

        event(new DestroyCommonCase($commoncase));

        $commoncase->delete();

        return response()->json(['status'=>'success','message'=>'Common Case Successfully deleted!']);

    }

    function caseNumber($workspace_id)
    {
        $latest = CommonCase::where('workspace',$workspace_id)->latest()->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->number + 1;
    }

    public function salesDocuments(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $documents = SalesDocument::with('assign_user')
                                    ->where('created_by', creatorId())
                                    ->where('workspace',$request->workspace_id)
                                    ->get()
                                    ->map(function($document){
                                        return [
                                            'id'                => $document->id,
                                            'name'              => $document->name,
                                            'assign_user_name'  => $document->assign_user->name,
                                            'account'           => $document->accounts->name,
                                            'type'              => $document->types->name,
                                            'opportunities'     => $document->opportunitys->name ?? '-',
                                            'publish_date'      => company_date_formate($document->publish_date),
                                            'expiration_date'   => company_date_formate($document->expiration_date),
                                            'status'            => SalesDocument::$status[$document->status],
                                            'attachment'        => !empty($document->attachment) ? get_file($document->attachment) : '',
                                            'description'       => $document->description
                                        ];
                                    });

        return response()->json(['status'=>'success','data'=>$documents]);
    }

    public function salesDocumentsCreate(Request $request)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $user = User::where('id',$request->user_id)->where('workspace_id',$request->workspace_id)->where('created_by', creatorId())->emp()->first();
        if(!$user){
            return response()->json(['status'=>'error','message'=>'Assign User not Found!']);
        }

        $salesAccount = SalesAccount::where('id',$request->account)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$salesAccount){
            return response()->json(['status'=>'error','message'=>'Sales Account Not Found!']);
        }

        $folder = SalesDocumentFolder::where('id',$request->folder)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$folder){
            return response()->json(['status'=>'error','message'=>'Document Folder Not Found!']);
        }

        $salesDocumentFolderType = SalesDocumentType::where('id',$request->type)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$salesDocumentFolderType){
            return response()->json(['status'=>'error','message'=>'Sales Document Folder Type Not Found!']);
        }

        $opportunity = Opportunities::where('id',$request->opportunities)->where('workspace',$request->workspace_id)->where('created_by', creatorId())->first();
        if(!$opportunity)
        {
            return response()->json(['status'=>'error','message'=>'Opportunity Not Found!']);
        }

        $validator = Validator::make(
            $request->all(), [
                               'name' => 'required|max:120',
                               'folder' => 'required',
                               'type' => 'required',
                               'publish_date' => 'required|date_format:Y-m-d',
                               'expiration_date' => 'required|date_format:Y-m-d',
                               'status'=>'in:'.implode(',',SalesDocument::$status)
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error','message' => $messages->first()]);
        }

        if(!empty($request->attachment))
        {
            $filenameWithExt = $request->file('attachment')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('attachment')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $id = $folder->parent;
            if($folder->parent)
            {
                $parent = SalesDocumentFolder::find($id);
                $uplaod = upload_file($request,'attachment',$fileNameToStore,$parent->name.'/'.$folder->name);
            }
            else
            {
                $uplaod = upload_file($request,'attachment',$fileNameToStore,$folder->name);
            }
            if($uplaod['flag'] == 1)
            {
                $url = $uplaod['url'];
            }
            else
            {
                return response()->json(['status'=>'error','message' => $uplaod['msg']]);
            }
        }

        $salesdocument                  = new SalesDocument();
        $salesdocument->user_id         = $request->user_id;
        $salesdocument->name            = $request->name;
        $salesdocument->account         = $request->account;
        $salesdocument->folder          = $request->folder;
        $salesdocument->opportunities   = $request->opportunities;
        $salesdocument->type            = $request->type;
        $salesdocument->status          = array_flip(SalesDocument::$status)[$request->status];
        $salesdocument->publish_date    = $request->publish_date;
        $salesdocument->expiration_date = $request->expiration_date;
        $salesdocument->description     = $request->description;
        $salesdocument->attachment      = !empty($request->attachment) ? $url : '';
        $salesdocument->workspace       = $request->workspace_id;
        $salesdocument->created_by      = creatorId();
        $salesdocument->save();

        Stream::create(
            [
                'user_id' => Auth::user()->id,'created_by' => creatorId(),
                'log_type' => 'created',
                'remark' => json_encode(
                    [
                        'owner_name' => Auth::user()->name,
                        'title' => 'document',
                        'stream_comment' => '',
                        'user_name' => $salesdocument->name,
                    ]
                ),
            ]
        );

        event(new CreateSalesDocument($request,$salesdocument));

        return response()->json(['status'=>'success', 'message' => 'Document Successfully Created!']);
    }

    public function salesDocumentsUpdate(Request $request,$id)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $user = User::where('id',$request->user_id)->where('workspace_id',$request->workspace_id)->where('created_by', creatorId())->emp()->first();
        if(!$user){
            return response()->json(['status'=>'error','message'=>'Assign User not Found!']);
        }

        $salesAccount = SalesAccount::where('id',$request->account)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$salesAccount){
            return response()->json(['status'=>'error','message'=>'Sales Account Not Found!']);
        }

        $folder = SalesDocumentFolder::where('id',$request->folder)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$folder){
            return response()->json(['status'=>'error','message'=>'Document Folder Not Found!']);
        }

        $salesDocumentFolderType = SalesDocumentType::where('id',$request->type)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$salesDocumentFolderType){
            return response()->json(['status'=>'error','message'=>'Sales Document Folder Type Not Found!']);
        }

        $opportunity = Opportunities::where('id',$request->opportunities)->where('workspace',$request->workspace_id)->where('created_by', creatorId())->first();
        if(!$opportunity)
        {
            return response()->json(['status'=>'error','message'=>'Opportunity Not Found!']);
        }

        $validator = Validator::make(
            $request->all(), [
                               'name' => 'required|max:120',
                               'folder' => 'required',
                               'type' => 'required',
                               'publish_date' => 'required|date_format:Y-m-d',
                               'expiration_date' => 'required|date_format:Y-m-d',
                               'status'=>'in:'.implode(',',SalesDocument::$status)
                           ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error','message' => $messages->first()]);
        }

        $salesDocument = SalesDocument::where('id',$id)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$salesDocument){
            return response()->json(['status'=>'error','message'=>'Sales Document Not Found!']);
        }

        if(!empty($request->attachment))
        {
            $filenameWithExt = $request->file('attachment')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('attachment')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $folder = SalesDocumentFolder::find($request->folder);
            $id = $folder->parent;
            if(!empty($salesDocument->attachment))
            {
                delete_file($salesDocument->attachment);
            }
            if($folder->parent)
            {
                $parent = SalesDocumentFolder::find($id);
                $uplaod = upload_file($request,'attachment',$fileNameToStore,$parent->name.'/'.$folder->name);
            }
            else
            {
                $uplaod = upload_file($request,'attachment',$fileNameToStore,$folder->name);
            }
            if($uplaod['flag'] == 1)
            {
                $url = $uplaod['url'];
            }
            else
            {
                return response()->json(['status'=>'error','message'=>$uplaod['msg']]);
            }
        }

        $salesDocument->user_id         = $request->user_id;
        $salesDocument->name            = $request->name;
        $salesDocument->account         = $request->account;
        $salesDocument->folder          = $request->folder;
        $salesDocument->type            = $request->type;
        $salesDocument->opportunities   = $request->opportunities;
        $salesDocument->status          = array_flip(SalesDocument::$status)[$request->status];
        $salesDocument->publish_date    = $request->publish_date;
        $salesDocument->expiration_date = $request->expiration_date;
        $salesDocument->description     = $request->description;
        if(!empty($request->attachment))
        {
            $salesDocument->attachment = $url;
        }
        $salesDocument->save();

        Stream::create(
            [
                'user_id' => Auth::user()->id,'created_by' => creatorId(),
                'log_type' => 'updated',
                'remark' => json_encode(
                    [
                        'owner_name' => Auth::user()->name,
                        'title' => 'document',
                        'stream_comment' => '',
                        'user_name' => $salesDocument->name,
                    ]
                ),
            ]
        );

        event(new UpdateSalesDocument($request,$salesDocument));

        return response()->json(['status'=>'success','message' => 'Document Successfully Updated!']);

    }

    public function salesDocumentsDelete(Request $request , $id)
    {
        if (!module_is_active('Sales')) {
            return response()->json(['status'=>'error','message'=>'Sales Module Not Active!']);
        }

        $salesDocument = SalesDocument::where('id',$id)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$salesDocument){
            return response()->json(['status'=>'error','message'=>'Sales Document Not Found!']);
        }

        if(!empty($salesDocument->attachment))
        {
            delete_file($salesDocument->attachment);
        }

        event(new DestroySalesDocument($salesDocument));

        $salesDocument->delete();

        return response()->json(['status'=>'success','message' => 'Document Successfully Deleted!']);
    }
}
