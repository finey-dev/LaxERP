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
use Workdo\Sales\Entities\SalesAccount;
use Workdo\Sales\Entities\SalesOrder;
use Workdo\Sales\Entities\SalesOrderItem;
use Workdo\Sales\Entities\ShippingProvider;
use Workdo\Sales\Entities\Stream;
use Workdo\Sales\Events\CreateSalesOrder;
use Workdo\Sales\Events\DestroySalesOrder;
use Workdo\Sales\Events\UpdateSalesOrder;

class SalesOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
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

            $salesorders = SalesOrder::with(['assign_user','accounts'])->where('created_by', creatorId())->where('workspace',$request->workspace_id);
            //if(Auth::user()->type != 'company'){
            //    $salesorders = $salesorders->where('user_id',Auth::user()->id);
            //}
            $salesorders = $salesorders->paginate(10)
                            ->through(function($salesOrder) use ($request){
                                $shippingContact = Contact::where('workspace',$request->workspace_id)->where('id',$salesOrder->shipping_contact)->first();
                                $shippingProvider = ShippingProvider::where('workspace',$request->workspace_id)->where('id',$salesOrder->shipping_provider)->first();
                                $tax = null;
                                if(module_is_active('ProductService')){
                                    $tax = \Workdo\ProductService\Entities\Tax::select('id','name')->where('id',$salesOrder->tax)->where('created_by',creatorId())->where('workspace_id',$request->workspace_id)->first();
                                }
                                return [
                                    'id'                    => $salesOrder->id,
                                    'order_number'          => SalesOrder::salesorderNumberFormat($salesOrder->salesorder_id),
                                    'name'                  => $salesOrder->name,
                                    'quote'                 => $salesOrder->quotes->name ?? '-',
                                    'quote_id'              => $salesOrder->quote,
                                    'opportunity'           => $salesOrder->opportunitys->name ?? '-',
                                    'status'                => SalesOrder::$status[$salesOrder->status],
                                    'account'               => $salesOrder->accounts->name ?? '-',
                                    'amount'                => currency_format_with_sym($salesOrder->amount),
                                    'date_quoted'           => $salesOrder->date_quoted,
                                    'quote_number'          => $salesOrder->quote_number,
                                    'billing_address'       => $salesOrder->billing_address,
                                    'billing_city'          => $salesOrder->billing_city,
                                    'billing_state'         => $salesOrder->billing_state,
                                    'billing_country'       => $salesOrder->billing_country,
                                    'billing_postalcode'    => $salesOrder->billing_postalcode,
                                    'shipping_address'      => $salesOrder->shipping_address,
                                    'shipping_city'         => $salesOrder->shipping_city,
                                    'shipping_state'        => $salesOrder->shipping_state,
                                    'shipping_country'      => $salesOrder->shipping_country,
                                    'shipping_postalcode'   => $salesOrder->shipping_postalcode,
                                    'billing_contact'       => $salesOrder->contacts->name ?? '-',
                                    'shipping_contact'      => $shippingContact->name ?? '-',
                                    'shipping_provider'     => $shippingProvider->name ?? '-',
                                    'tax'                   => $tax,
                                    'assigned_user'         => ucfirst(!empty($salesOrder->assign_user) ? $salesOrder->assign_user->name : '-'),
                                    'description'           => $salesOrder->description,
                                    'assign_user_id'        => $salesOrder->user_id,
                                    'billing_contact_id'    => $salesOrder->billing_contact,
                                    'shipping_contact_id'   => $salesOrder->shipping_contact,
                                    'opportunity_id'        => $salesOrder->Opportunity,
                                    'shipping_provider_id'  => (int)$salesOrder->shipping_provider,
                                ];
                            });
            return response()->json(['status'=>1,'data'=> $salesorders]);
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
        try{

            $validator = Validator::make(
                $request->all(), [
                    'workspace_id'  => 'required|exists:work_spaces,id',
                    'name'  => 'required|max:255',
                    'quote_id' => ['required','numeric',function($attribute, $value,$fail) use ($request){
                        $quote = Quote::where('id',$value)->where('workspace', $request->workspace_id)->first();
                        if (!$quote) {
                            $fail('The selected Quote is invalid for the provided workspace.');
                        }
                    }],
                    'opportunity_id'   => ['required','numeric',function($attribute, $value,$fail) use ($request){
                        $opportunity = Opportunities::where('id',$value)->where('workspace', $request->workspace_id)->first();
                        if (!$opportunity) {
                            $fail('The selected Opportunity is invalid for the provided workspace.');
                        }
                    }],
                    'status' => ['required','in:'.implode(',',SalesOrder::$status)],
                    'account_id' => ['required','numeric',function($attribute, $value,$fail) use ($request){
                        $salesAccount = SalesAccount::where('id',$value)->where('workspace',$request->workspace_id)->first();
                        if (!$salesAccount) {
                            $fail('The selected Sales Account is invalid for the provided workspace.');
                        }
                    }],
                    'date_quoted' => 'required|date_format:Y-m-d',
                    'quote_number'=>'required|numeric|gt:0',
                    'billing_contact_id'=>['required','numeric',function($attribute, $value,$fail) use ($request){
                        $billingContact = Contact::where('id',$value)->where('workspace', $request->workspace_id)->first();
                        if (!$billingContact) {
                            $fail('The selected Billing Contact is invalid for the provided workspace.');
                        }
                    }],
                    'shipping_contact_id' => ['required','numeric',function($attribute, $value,$fail) use ($request){
                        $shippingContact = Contact::where('id',$value)->where('workspace', $request->workspace_id)->first();
                        if (!$shippingContact) {
                            $fail('The selected Shipping Contact is invalid for the provided workspace.');
                        }
                    }],
                    'shipping_provider_id' => ['required','numeric',function($attribute, $value,$fail) use ($request){
                        $shippingContact = ShippingProvider::where('id',$value)->where('workspace', $request->workspace_id)->first();
                        if (!$shippingContact) {
                            $fail('The selected Shipping Provider is invalid for the provided workspace.');
                        }
                    }],
                    'assign_user_id' => 'required|exists:users,id',
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return response()->json(['status' => 0, 'message' => $messages->first()] , 403);
            }

            $salesorder                        = new SalesOrder();
            $salesorder['user_id']             = $request->assign_user_id;
            $salesorder['salesorder_id']       = $this->salesorderNumber($request->workspace_id);
            $salesorder['name']                = $request->name;
            $salesorder['quote']               = $request->quote_id;
            $salesorder['opportunity']         = $request->opportunity_id;
            $salesorder['status']              = array_flip(SalesOrder::$status)[$request->status];
            $salesorder['account']             = $request->account_id;
            // $salesorder['tax']                 = $request->tax;
            $salesorder['date_quoted']         = $request->date_quoted;
            $salesorder['quote_number']        = $request->quote_number;
            $salesorder['billing_address']     = $request->billing_address;
            $salesorder['billing_city']        = $request->billing_city;
            $salesorder['billing_state']       = $request->billing_state;
            $salesorder['billing_country']     = $request->billing_country;
            $salesorder['billing_postalcode']  = $request->billing_postalcode;
            $salesorder['shipping_address']    = $request->shipping_address;
            $salesorder['shipping_city']       = $request->shipping_city;
            $salesorder['shipping_state']      = $request->shipping_state;
            $salesorder['shipping_country']    = $request->shipping_country;
            $salesorder['shipping_postalcode'] = $request->shipping_postalcode;
            $salesorder['billing_contact']     = $request->billing_contact_id;
            $salesorder['shipping_contact']    = $request->shipping_contact_id;
            $salesorder['shipping_provider']   = $request->shipping_provider_id;
            $salesorder['description']         = $request->description;
            $salesorder['workspace']           = $request->workspace_id;
            $salesorder['created_by']          = creatorId();
            $salesorder->save();
            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->name,
                            'title' => 'salesorder',
                            'stream_comment' => '',
                            'user_name' => $salesorder->name,
                        ]
                    ),
                ]
            );
            if(!empty(company_setting('New Sales Order')) && company_setting('New Sales Order')  == true)
            {
                $Assign_user_phone = User::where('id',$request->assign_user_id)->first();

                $uArr = [
                    'quote_number' => $request->quote_number,
                    'billing_address' => $request->billing_address,
                    'shipping_address' => $request->shipping_address,
                    'description' => $request->description,
                    'date_quoted' => $request->date_quoted,
                    'salesorder_assign_user' => $Assign_user_phone->name,
                ];
                $resp = EmailTemplate::sendEmailTemplate('New Sales Order', [$salesorder->id => $Assign_user_phone->email], $uArr);
            }

            // event(new CreateSalesOrder($request,$salesorder));

            return response()->json(['status'=>1, 'message'=> 'The sales order has been created successfully!']);

        } catch (\Exception $e) {
            return response()->json(['status'=>0,'message'=>'something went wrong!!!']);
        }
    }

    public static function salesorderNumber($workspaceId)
    {
        $latest = SalesOrder::where('created_by', '=', creatorId())->where('workspace',$workspaceId)->latest()->first();

        if(!$latest)
        {
            return 1;
        }

        return $latest->salesorder_id + 1;
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
            $validator = Validator::make(
                $request->all(), [
                    'workspace_id'  => 'required|exists:work_spaces,id',
                    'name'  => 'required|max:255',
                    'quote_id' => ['required','numeric',function($attribute, $value,$fail) use ($request){
                        $quote = Quote::where('id',$value)->where('workspace', $request->workspace_id)->first();
                        if (!$quote) {
                            $fail('The selected Quote is invalid for the provided workspace.');
                        }
                    }],
                    'opportunity_id'   => ['required','numeric',function($attribute, $value,$fail) use ($request){
                        $opportunity = Opportunities::where('id',$value)->where('workspace', $request->workspace_id)->first();
                        if (!$opportunity) {
                            $fail('The selected Opportunity is invalid for the provided workspace.');
                        }
                    }],
                    'status' => ['required','in:'.implode(',',SalesOrder::$status)],
                    'account_id' => ['required','numeric',function($attribute, $value,$fail) use ($request){
                        $salesAccount = SalesAccount::where('id',$value)->where('workspace',$request->workspace_id)->first();
                        if (!$salesAccount) {
                            $fail('The selected Sales Account is invalid for the provided workspace.');
                        }
                    }],
                    'date_quoted' => 'required|date_format:Y-m-d',
                    'quote_number'=>'required|numeric|gt:0',
                    'billing_contact_id'=>['required','numeric',function($attribute, $value,$fail) use ($request){
                        $billingContact = Contact::where('id',$value)->where('workspace', $request->workspace_id)->first();
                        if (!$billingContact) {
                            $fail('The selected Billing Contact is invalid for the provided workspace.');
                        }
                    }],
                    'shipping_contact_id' => ['required','numeric',function($attribute, $value,$fail) use ($request){
                        $shippingContact = Contact::where('id',$value)->where('workspace', $request->workspace_id)->first();
                        if (!$shippingContact) {
                            $fail('The selected Shipping Contact is invalid for the provided workspace.');
                        }
                    }],
                    'shipping_provider_id' => ['required','numeric',function($attribute, $value,$fail) use ($request){
                        $shippingContact = ShippingProvider::where('id',$value)->where('workspace', $request->workspace_id)->first();
                        if (!$shippingContact) {
                            $fail('The selected Shipping Provider is invalid for the provided workspace.');
                        }
                    }],
                    'assign_user_id' => 'required|exists:users,id',
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return response()->json(['status' => 0, 'message' => $messages->first()] , 403);
            }

            $salesOrder = SalesOrder::where('id',$id)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
            if(!$salesOrder){
                return response()->json(['status' => 0, 'message' => 'Sales Order Not Found!']);
            }

            $salesOrder['user_id']             = $request->assign_user_id;
            $salesOrder['salesorder_id']       = $this->salesorderNumber($request->workspace_id);
            $salesOrder['name']                = $request->name;
            $salesOrder['quote']               = $request->quote_id;
            $salesOrder['opportunity']         = $request->opportunity_id;
            $salesOrder['status']              = array_flip(SalesOrder::$status)[$request->status];
            $salesOrder['account']             = $request->account_id;
            $salesorder['tax']                 = $request->tax;
            $salesOrder['date_quoted']         = $request->date_quoted;
            $salesOrder['quote_number']        = $request->quote_number;
            $salesOrder['billing_address']     = $request->billing_address;
            $salesOrder['billing_city']        = $request->billing_city;
            $salesOrder['billing_state']       = $request->billing_state;
            $salesOrder['billing_country']     = $request->billing_country;
            $salesOrder['billing_postalcode']  = $request->billing_postalcode;
            $salesOrder['shipping_address']    = $request->shipping_address;
            $salesOrder['shipping_city']       = $request->shipping_city;
            $salesOrder['shipping_state']      = $request->shipping_state;
            $salesOrder['shipping_country']    = $request->shipping_country;
            $salesOrder['shipping_postalcode'] = $request->shipping_postalcode;
            $salesOrder['billing_contact']     = $request->billing_contact_id;
            $salesOrder['shipping_contact']    = $request->shipping_contact_id;
            $salesOrder['shipping_provider']   = $request->shipping_provider_id;
            $salesOrder['description']         = $request->description;
            $salesOrder->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->name,
                            'title' => 'salesOrder',
                            'stream_comment' => '',
                            'user_name' => $salesOrder->name,
                        ]
                    ),
                ]
            );

            event(new UpdateSalesOrder($request,$salesOrder));

            return response()->json(['status' => 1, 'message' => 'The sales order details are updated successfully!']);

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
            $salesOrder = SalesOrder::where('id',$id)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
            if(!$salesOrder){
                return response()->json(['status' => 0, 'message' => 'Sales Order Not Found!']);
            }

            SalesOrderItem::where('salesorder_id',$id)->where('created_by',creatorId())->delete();

            event(new DestroySalesOrder($salesOrder));
            $salesOrder->delete();

            return response()->json(['status'=>1, 'message' => 'The sales order has been deleted!']);
        } catch (\Exception $e) {
            return response()->json(['status'=>0,'message'=>'something went wrong!!!']);
        }
    }
}
