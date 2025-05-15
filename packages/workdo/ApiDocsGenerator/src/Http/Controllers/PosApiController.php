<?php

namespace Workdo\ApiDocsGenerator\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Pos\Entities\Pos;

class PosApiController extends Controller
{
    public function posOrderList(Request $request){

        if (!module_is_active('Pos')) {
            return response()->json(['status'=>'error','message'=>'Pos Module Not Active!']);
        }

        $posPayments = Pos::where('created_by', creatorId())
                    ->where('workspace',$request->workspace_id)
                    ->with('customer','warehouse','posPayment')
                    ->get()
                    ->map(function($posPayment){
                        return [
                            'id'                => $posPayment->id,
                            'pos_date'          => company_date_formate($posPayment->pos_date),
                            'warehouse'         => [
                                'id'        => $posPayment->warehouse->id,
                                'name'      => $posPayment->warehouse->name,
                                'address'   => $posPayment->warehouse->address,
                                'city'      => $posPayment->warehouse->city,
                                'city_zip'  => $posPayment->warehouse->city_zip
                            ],
                            'pos_payment'        => !empty($posPayment->posPayment) ? [
                                'id'        => $posPayment->posPayment->id,
                                'amount'    => currency_format_with_sym($posPayment->posPayment->amount),
                                'discount'  => currency_format_with_sym($posPayment->posPayment->discount),
                                'date'      => !empty($posPayment->posPayment->date) ? company_date_formate($posPayment->posPayment->date) : null,
                            ] : [],
                            'customer'              => !empty($posPayment->customer) ? [
                                'id'                  => $posPayment->customer->id,
                                'name'                => $posPayment->customer->name,
                                'email'               => $posPayment->customer->email,
                                'contact'             => $posPayment->customer->contact,
                                'tax_number'          => $posPayment->customer->tax_number,
                                'billing_name'        => $posPayment->customer->billing_name,
                                'billing_country'     => $posPayment->customer->billing_country,
                                'billing_state'       => $posPayment->customer->billing_state,
                                'billing_city'        => $posPayment->customer->billing_city,
                                'billing_phone'       => $posPayment->customer->billing_phone,
                                'billing_zip'         => $posPayment->customer->billing_zip,
                                'billing_address'     => $posPayment->customer->billing_address,
                                'shipping_name'       => $posPayment->customer->shipping_name,
                                'shipping_country'    => $posPayment->customer->shipping_country,
                                'shipping_state'      => $posPayment->customer->shipping_state,
                                'shipping_city'       => $posPayment->customer->shipping_city,
                                'shipping_phone'      => $posPayment->customer->shipping_phone,
                                'shipping_zip'        => $posPayment->customer->shipping_zip,
                                'shipping_address'    => $posPayment->customer->shipping_address,
                                'balance'             => currency_format_with_sym($posPayment->customer->balance),
                                'credit_note_balance' => currency_format_with_sym($posPayment->customer->credit_note_balance),
                            ] : []
                        ];
                    });

        return response()->json(['status'=>'success','data'=>$posPayments]);
    }

    public function posOrderDetail($id){
        if (!module_is_active('Pos')) {
            return response()->json(['status'=>'error','message'=>'Pos Module Not Active!']);
        }



    }
}
