<?php

namespace Workdo\ApiDocsGenerator\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Proposal;
use App\Models\User;

class ProposalApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $status = Proposal::$statues;
        $query = Proposal::where('workspace',$request->workspace_id)->where('created_by', creatorId());
        $proposals = $query->get()->map(function($proposal) use ($status){
            for($i = 0;$i <= count($status);$i++){
                if($proposal->status == $i){
                    $proposal['status'] = $status[$i];
                }
            }
            return [
                'id'                    => $proposal->id,
                'issue_date'            => $proposal->issue_date,
                'send_date'             => $proposal->send_date,
                'proposal_module'       => $proposal->proposal_module,
                'status'                => $proposal->status,
                'proposal_number'       => Proposal::proposalNumberFormat($proposal->proposal_id)
            ];
        });
        $data = [];
        $data['proposals'] = $proposals;
        return response()->json(['status'=>'success','data'=>$data],200);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('api-docs-generator::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Request $request,$id)
    {
        $proposal = Proposal::where('workspace', $request->workspace_id)->where('created_by', creatorId())->where('id', $id)->first();
        if($proposal)
        {
            $user = $proposal->customer;

            $customer_detail = [];
            if(module_is_active('Account') && !empty($user->id))
            {
                $customer    = \Workdo\Account\Entities\Customer::where('user_id',$user->id)->where('workspace',$request->workspace_id)->first();
                if (!$customer) {
                    $customer = $user;
                }
                $customer_detail = [
                    'id'                         => $customer->id,
                    'name'                       => $customer->name,
                    'email'                      => $customer->email,
                    'contact'                    => $customer->contact,
                    'tax_number'                 => $customer->tax_number,
                    'billing_name'               => $customer->billing_name,
                    'billing_country'            => $customer->billing_country,
                    'billing_state'              => $customer->billing_state,
                    'billing_city'               => $customer->billing_city,
                    'billing_phone'              => $customer->billing_phone,
                    'billing_zip'                => $customer->billing_zip,
                    'billing_address'            => $customer->billing_address,
                    'shipping_name'              => $customer->shipping_name,
                    'shipping_country'           => $customer->shipping_country,
                    'shipping_state'             => $customer->shipping_state,
                    'shipping_city'              => $customer->shipping_city,
                    'shipping_phone'             => $customer->shipping_phone,
                    'shipping_zip'               => $customer->shipping_zip,
                    'shipping_address'           => $customer->shipping_address,
                    'lang'                       => $customer->lang,
                    'balance'                    => currency_format_with_sym($customer->balance),
                    'electronic_address'         => $customer->electronic_address,
                    'electronic_address_scheme'  => $customer->electronic_address_scheme,
                ];
            }
            $totalTaxPrice = 0;
            $taxesData=[];
            $items   = $proposal->items->map(function($item) use (&$totalTaxPrice,&$taxesData,$proposal) {
                if(!empty($item->tax)){
                    $taxes = Proposal::tax($item->tax);
                    foreach ($taxes as $tax){
                        $taxPrice = Proposal::taxRate($tax->rate, $item->price, $item->quantity, $item->discount);
                        $totalTaxPrice += $taxPrice;
                        if (array_key_exists($tax->name,$taxesData))
                        {
                            $taxesData[$tax->name] = $taxesData[$tax->name]+$taxPrice;
                        }
                        else
                        {
                            $taxesData[$tax->name] = $taxPrice;
                        }
                    }
                }
                if($proposal->proposal_module == 'account'){
                    return [
                        'id'            => $item->id,
                        'product_name'  => !empty($item->product()) ? $item->product()->name : '',
                        'tax_price'     => currency_format_with_sym($totalTaxPrice),
                        'quantity'      => $item->quantity,
                        'price'         => currency_format_with_sym($item->price),
                        'description'   => $item->description,
                        'tax'           => $taxesData
                    ];
                }
                elseif($proposal->proposal_module == 'taskly'){
                    return [
                        'id'            => $item->id,
                        'project_name'  => !empty($item->product()) ? $item->product()->title : '',
                        'tax_price'     => currency_format_with_sym($totalTaxPrice),
                        'quantity'      => $item->quantity,
                        'price'         => currency_format_with_sym($item->price),
                        'description'   => $item->description,
                        'tax'           => $taxesData
                    ];
                }
            });
            $status   = Proposal::$statues;
            if(module_is_active('CustomField')){
                $proposal->customField = \Workdo\CustomField\Entities\CustomField::getData($proposal, 'Base','Proposal');
                $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', $request->workspace_id)->where('module', '=', 'Base')->where('sub_module','Proposal')->get();
                $customFields_detail = $customFields->map(function($customField) use($proposal){
                    return [
                        'name'                  => $customField->name,
                        'field'                  => !empty($proposal->customField[$customField->id])?$proposal->customField[$customField->id]:'-',
                    ];
                });
            }else{
                $customFields_detail = [];
            }
            for($i = 0;$i <= count($status);$i++){
                if($proposal->status == $i){
                    $proposal['status'] = $status[$i];
                }
            }
            $proposal_detail = [
                'id'                                => $proposal->id,
                'proposal_number'                   => Proposal::proposalNumberFormat($proposal->proposal_id),
                'issue_date'                        => $proposal->issue_date,
                'send_date'                         => $proposal->send_date,
                'proposal_module'                   => $proposal->proposal_module,
                'status'                            => $proposal->status,
            ];
            $data = [];
            $data['proposal']           = $proposal_detail;
            $data['items']              = $items;
            if(module_is_active('CustomField')){
                $data['customFields']       = $customFields_detail;
            }
            $data['customer']           = $customer_detail;
            $data['sub_total']          = currency_format_with_sym($proposal->getSubTotal());
            $data['discount']           = currency_format_with_sym($proposal->getTotalDiscount());
            $data['tax']                = $taxesData;
            $data['total']              = currency_format_with_sym($proposal->getTotal());
            return response()->json(['status'=>'success','data'=>$data],200);
        } else {
            return response()->json(['error'=>__('Proposal Not Found!')],404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('api-docs-generator::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
