<?php

namespace Workdo\Sales\Http\Controllers\Api;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Workdo\Sales\Entities\Contact;
use Workdo\Sales\Entities\Opportunities;
use Workdo\Sales\Entities\Quote;
use Workdo\Sales\Entities\QuoteItem;
use Workdo\Sales\Entities\ShippingProvider;
use Workdo\Sales\Entities\Stream;
use Workdo\Sales\Events\CreateQuote;
use Workdo\Sales\Events\DestroyQuote;
use Workdo\Sales\Events\UpdateQuote;

class QuotesController extends Controller
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

            $quotes = Quote::with(['accounts','assign_user'])->where('created_by', creatorId())->where('workspace',$request->workspace_id);
            // if(Auth::user()->type != 'company'){
            //     $quotes = $quotes->where('user_id',Auth::user()->id);
            // }
            $quotes = $quotes->paginate(10)
                            ->through(function($quote){
                                $shippingContact = Contact::where('id',$quote->shipping_contact)->where('workspace', $quote->workspace)->first();
                                $shippingProvider = ShippingProvider::where('id',$quote->shipping_provider)->where('workspace', $quote->workspace)->first();
                                $tax = [];
                                if(!empty($quote->tax)){
                                    $tax =  \Workdo\ProductService\Entities\Tax::select('id','name')->whereIn('id',explode(',',$quote->tax))->where('created_by', creatorId())->where('workspace_id',$quote->workspace)->get();
                                }
                                return [
                                    'id'                    => $quote->id,
                                    'quote_id'              => Quote::quoteNumberFormat($quote->quote_id),
                                    'name'                  => $quote->name ?? '-',
                                    'amount'                => currency_format_with_sym($quote->getTotal()),
                                    'date_quoted'           => company_date_formate($quote->date_quoted),
                                    'user'                  => !empty($quote->assign_user) ? $quote->assign_user->name : null,
                                    'quote_number'          => $quote->quote_number,
                                    'status'                => Quote::$status[$quote->status],
                                    'account'               => $quote->accounts->name ?? '-',
                                    'opportunity'           => $quote->opportunitys->name ?? '-',
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
                                    'billing_contact'       => $quote->contacts->name ?? '-',
                                    'shipping_contact'      => $shippingContact->name ?? '-',
                                    'shipping_provider'     => $shippingProvider->name ?? '-',
                                    'description'           => $quote->description,
                                    'assign_user_id'        => $quote->user_id,
                                    'billing_contact_id'    => $quote->billing_contact,
                                    'shipping_contact_id'   => $quote->shipping_contact,
                                    'opportunity_id'        => $quote->opportunity,
                                    'shipping_provider_id'  => (int)$quote->shipping_provider,
                                    'tax'                   => $tax
                                ];
                            });

            return response()->json(['status'=>1,'data' => $quotes]);
        } catch (\Exception $e) {
            return response()->json(['status'=>0,'message'=>'something went wrong!!!']);
        }
    }

    public function create(Request $request)
    {
        try{
            if(module_is_active('ProductService')){
                $tax =  \Workdo\ProductService\Entities\Tax::where('created_by', creatorId())->where('workspace_id',$request->workspace_id)->pluck('id')->toArray();
                if(empty(array_intersect($request->tax,$tax))){
                    return response()->json(['status'=>0,'message'=>'Tax Not Found!']);
                }
            }

            $validator = Validator::make(
                $request->all(), [
                    'workspace_id'  => 'required|exists:work_spaces,id',
                    'name' => 'required|max:120',
                    'shipping_postalcode' => 'required',
                    'billing_postalcode' => 'required',
                    'assign_user_id' => 'required|exists:users,id',
                    'date_quoted' => 'required|date_format:Y-m-d',
                    'status'=>'required|in:'.implode(',',Quote::$status),
                    'opportunity' => [
                        'required',
                        'numeric',
                        function ($attribute, $value, $fail) use ($request) {
                            $opportunity = Opportunities::where('id', $value)
                                ->where('workspace', $request->workspace_id)
                                ->first();
                            if (!$opportunity) {
                                $fail('The selected opportunity is invalid for the provided workspace.');
                            }
                        },
                    ],
                    'billing_contact'=>['required','numeric',function($attribute, $value,$fail) use ($request){
                        $billingContact = Contact::where('id',$value)->where('workspace', $request->workspace_id)->first();
                        if (!$billingContact) {
                            $fail('The selected Billing Contact is invalid for the provided workspace.');
                        }
                    }],
                    'shipping_contact' => ['required','numeric',function($attribute, $value,$fail) use ($request){
                        $shippingContact = Contact::where('id',$value)->where('workspace', $request->workspace_id)->first();
                        if (!$shippingContact) {
                            $fail('The selected Shipping Contact is invalid for the provided workspace.');
                        }
                    }],
                    'shipping_provider' => ['required','numeric',function($attribute, $value,$fail) use ($request){
                        $shippingContact = ShippingProvider::where('id',$value)->where('workspace', $request->workspace_id)->first();
                        if (!$shippingContact) {
                            $fail('The selected Shipping Provider is invalid for the provided workspace.');
                        }
                    }]
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return response()->json(['status' => 0, 'message' => $messages->first()] , 403);
            }

            $opportunity = Opportunities::where('id',$request->opportunity)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();

            $quote                          = new Quote();
            $quote->user_id                 = $request->assign_user_id;
            $quote->quote_id                = $this->quoteNumber();
            $quote->name                    = $request->name;
            $quote->opportunity             = $request->opportunity;
            $quote->status                  = array_flip(Quote::$status)[$request->status];
            $quote->account                 = $opportunity->accounts->id;
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
            $quote->tax                     = implode(',', $request->tax);
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
                $Assign_user_phone = User::where('id',$request->assign_user_id)->where('workspace_id', '=',  $request->workspace_id)->first();

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

            return response()->json(['status'=>1, 'message' => 'The quote has been created successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status'=>0,'message'=>'something went wrong!!!']);
        }
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

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        try{
            if(module_is_active('ProductService')){
                $tax =  \Workdo\ProductService\Entities\Tax::where('created_by', creatorId())->where('workspace_id',$request->workspace_id)->pluck('id')->toArray();
                if(empty(array_intersect($request->tax,$tax))){
                    return response()->json(['status'=>0,'message'=>'Tax Not Found!']);
                }
            }

            $validator = Validator::make(
                $request->all(), [
                    'workspace_id'  => 'required|exists:work_spaces,id',
                    'name' => 'required|max:120',
                    'shipping_postalcode' => 'required',
                    'billing_postalcode' => 'required',
                    'assign_user_id' => 'required|exists:users,id',
                    'date_quoted' => 'required|date_format:Y-m-d',
                    'status'=>'required|in:'.implode(',',Quote::$status),
                    'opportunity' => [
                        'required',
                        'numeric',
                        function ($attribute, $value, $fail) use ($request) {
                            $opportunity = Opportunities::where('id', $value)
                                ->where('workspace', $request->workspace_id)
                                ->first();
                            if (!$opportunity) {
                                $fail('The selected opportunity is invalid for the provided workspace.');
                            }
                        },
                    ],
                    'billing_contact'=>['required','numeric',function($attribute, $value,$fail) use ($request){
                        $billingContact = Contact::where('id',$value)->where('workspace', $request->workspace_id)->first();
                        if (!$billingContact) {
                            $fail('The selected Billing Contact is invalid for the provided workspace.');
                        }
                    }],
                    'shipping_contact' => ['required','numeric',function($attribute, $value,$fail) use ($request){
                        $shippingContact = Contact::where('id',$value)->where('workspace', $request->workspace_id)->first();
                        if (!$shippingContact) {
                            $fail('The selected Shipping Contact is invalid for the provided workspace.');
                        }
                    }],
                    'shipping_provider' => ['required','numeric',function($attribute, $value,$fail) use ($request){
                        $shippingContact = ShippingProvider::where('id',$value)->where('workspace', $request->workspace_id)->first();
                        if (!$shippingContact) {
                            $fail('The selected Shipping Provider is invalid for the provided workspace.');
                        }
                    }]
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return response()->json(['status' => 0, 'message' => $messages->first()] , 403);
            }

            $quote = Quote::where('id',$id)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
            if(!$quote){
                return response()->json(['status'=>0,'message'=>'Quote Not Found!']);
            }
            $opportunity = Opportunities::where('id',$request->opportunity)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();

            $quote->user_id                 = $request->assign_user_id;
            $quote->quote_id                = $this->quoteNumber();
            $quote->name                    = $request->name;
            $quote->opportunity             = $request->opportunity;
            $quote->status                  = array_flip(Quote::$status)[$request->status];
            $quote->account                 = $opportunity->accounts->id;
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
            $quote->tax                     = implode(',', $request->tax);
            $quote->shipping_provider       = $request->shipping_provider;
            $quote->description             = $request->description;
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

            return response()->json(['status'=>1, 'message'=> 'The quote details are updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status'=>0,'message'=>'something went wrong!!!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request, $id)
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

            $quote = Quote::where('id',$id)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
            if(!$quote){
                return response()->json(['status'=>0,'message'=>'Quote Not Found!']);
            }

            event(new DestroyQuote($quote));

            QuoteItem::where('quote_id',$quote->id)->where('created_by',creatorId())->delete();
            $quote->delete();

            return response()->json(['status'=>1, 'message'=>'The quote has been deleted!']);
        } catch (\Exception $e) {
            return response()->json(['status'=>0,'message'=>'something went wrong!!!']);
        }
    }
}
