<?php

namespace Workdo\ApiDocsGenerator\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use App\Models\BankTransferPayment;
use App\Models\InvoicePayment;
use App\Models\WorkSpace;

class InvoiceApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $status = Invoice::$statues;

        $query = Invoice::where('workspace', '=', $request->workspace_id)->where('created_by', creatorId());
        if(!empty($request->customer))
        {
            $query->where('user_id', '=', $request->customer);
        }
        if(!empty($request->issue_date))
        {
            $date_range = explode('to', $request->issue_date);
            if(count($date_range) == 2)
            {
                $query->whereBetween('issue_date',$date_range);
            }
            else
            {
                $query->where('issue_date',$date_range[0]);
            }
        }
        if(!empty($request->status))
        {
            $query->where('status', $request->status);
        }

        $invoices = $query->get();
        $all_invoices = $invoices->map(function($invoice) use($status){
            for($i = 0; $i<=count($status);$i++){
                if($invoice->status == $i){
                    $invoice_status = $status[$i];
                }
            }
            return [
                'id'                => $invoice->id,
                'issue_date'        => $invoice->issue_date,
                'due_date'          => $invoice->due_date,
                'send_date'         => $invoice->send_date,
                'status'            => $invoice_status,
                'invoice_module'    => $invoice->invoice_module,
                'invoice_number'    => Invoice::invoiceNumberFormat($invoice->invoice_id),

            ];
        });
        return response()->json(['status'=>'success','data'=>$all_invoices],200);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($type)
    {
        $invoice_number = Invoice::invoiceNumberFormat($this->invoiceNumber());
        $customers = User::where('workspace_id','=',getActiveWorkSpace())->where('created_by', creatorId())->where('type','Client')->get()->pluck('name', 'id');
        $category = [];
        $projects = [];
        $taxs = [];
        $customerId = 0;
        if(module_is_active('Account'))
        {
            if ($customerId > 0) {
                $temp_cm = \Workdo\Account\Entities\Customer::where('customer_id',$customerId)->first();
                if($temp_cm)
                {
                    $customerId = $temp_cm->user_id;
                }
                else
                {
                    return response()->json(['status'=>'error','message'=>__('Something went wrong please try again!')],403);
                }
            }
            $category = \Workdo\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->where('type', 1)->get()->pluck('name', 'id');
        }
        if(module_is_active('Taskly'))
        {
            if(module_is_active('ProductService'))
            {
                $taxs = \Workdo\ProductService\Entities\Tax::where('workspace_id', getActiveWorkSpace())->where('created_by', creatorId())->get()->pluck('name', 'id');
            }
            $projects = \Workdo\Taskly\Entities\Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', Auth::user()->id)->where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get()->pluck('name', 'id');
        }
        if(module_is_active('CustomField')){
            $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id',getActiveWorkSpace())->where('created_by', creatorId())->where('module', '=', 'Base')->where('sub_module','Invoice')->get();
        }else{
            $customFields = null;
        }


        $data = [];
        $data['customers']          = $customers;
        $data['invoice_number']     = $invoice_number;
        if($type == 'product'){
            $data['category']           = $category;
        }
        elseif($type == 'project'){
            $data['projects']           = $projects;
            $data['taxs']               = $taxs;
        }
        $data['customerId']         = $customerId;
        $data['customFields']       = $customFields;
        return response()->json(['status'=>'success','data'=>$data],200);
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
        $invoice = Invoice::find($id);

        if($invoice)
        {
            $bank_transfer_payments = BankTransferPayment::where('created_by',creatorId())->where('workspace',$request->workspace_id)->where('type','invoice')->where('request',$invoice->id)->get();
            $status = Invoice::$statues;
            $invoice['invoice_number'] = $invoice->invoiceNumberFormat($invoice->invoice_id);
            for($i = 0; $i<=count($status);$i++){
                if($invoice->status == $i){
                    $invoice['status'] = $status[$i];
                }
            }
            if($invoice->workspace == $request->workspace_id)
            {
                $invoicePayments = InvoicePayment::select('date','amount','payment_type','account_id','reference','description')->where('invoice_id', $invoice->id)->get();
                if(module_is_active('Account'))
                {
                    $customer = \Workdo\Account\Entities\Customer::where('user_id',$invoice->user_id)->where('workspace',$request->workspace_id)->first();
                    if (!$customer) {
                        $customer = $invoice->customer;
                    }
                }
                else
                {
                    $customer = $invoice->customer;
                }

                $customerDetail = [
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
                if(module_is_active('CustomField')){
                    $invoice->customField = \Workdo\CustomField\Entities\CustomField::getData($invoice, 'Base','Invoice');
                    $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', $request->workspace_id)->where('module', '=', 'Base')->where('sub_module','Invoice')->get();
                    $customFields_detail = $customFields->map(function($customField) use($invoice){
                        return [
                            'name'                  => $customField->name,
                            'field'                  => !empty($invoice->customField[$customField->id])?$invoice->customField[$customField->id]:'-',
                        ];
                    });
                }else{
                    $customFields_detail = [];
                }
                $items   = $invoice->items;
                $totalTaxPrice = 0;
                $items = $items->map(function($item) use ($totalTaxPrice,$invoice){
                    $product_name       = $item->product()->name;
                    if(!empty($item->tax)){
                        $taxes = Invoice::tax($item->tax);
                        foreach ($taxes as $tax){
                            $taxPrice = Invoice::taxRate($tax->rate, $item->price, $item->quantity, $item->discount);
                            $totalTaxPrice += $taxPrice;
                        }
                    }
                    if($invoice->invoice_module == 'account'){
                        return [
                            'product_name'          => !empty($item->product()) ? $item->product()->name : '',
                            'price'                 => currency_format_with_sym($item->price),
                            'tax'                   => currency_format_with_sym($totalTaxPrice),
                            'quantity'              => $item->quantity,
                            'discount'              => currency_format_with_sym($item->discount),
                            'description'           => $item->description,
                        ];
                    }
                    elseif($invoice->invoice_module == 'taskly'){
                        return [
                            'project_name'          => !empty($item->product()) ? $item->product()->title : '',
                            'price'                 => currency_format_with_sym($item->price),
                            'tax'                   => currency_format_with_sym($totalTaxPrice),
                            'quantity'              => $item->quantity,
                            'discount'              => currency_format_with_sym($item->discount),
                            'description'           => $item->description,
                        ];
                    }
                });
                $invoicePayments = $invoicePayments->map(function($invoicePayment){
                    return [
                        'date'              =>$invoicePayment->date,
                        'amount'            =>currency_format_with_sym($invoicePayment->amount),
                        'payment_type'      =>$invoicePayment->payment_type,
                        'reference'         =>$invoicePayment->reference,
                        'description'       =>$invoicePayment->description,
                        'account'           =>$invoicePayment->bankAccount->holder_name ?? '--',
                    ];
                });
                $invoiceDetail = [
                    'issue_date'                => $invoice->issue_date,
                    'due_date'                  => $invoice->due_date,
                    'send_date'                 => $invoice->send_date,
                    'status'                    => $invoice->status,
                    'invoice_module'            => $invoice->invoice_module,
                    'invoice_number'            => $invoice->invoice_number,
                ];
                $data = [];
                $data['invoice']                = $invoiceDetail;
                $data['customer']               = $customerDetail;
                $data['items']                  = $items;
                $data['invoicePayments']        = $invoicePayments;
                if(module_is_active('CustomField')){
                    $data['customFields']           = $customFields_detail;
                }
                $data['bank_transfer_payments'] = $bank_transfer_payments;
                $data['sub_total']              = currency_format_with_sym($invoice->getSubTotal());
                $data['discount']               = currency_format_with_sym($invoice->getTotalDiscount());
                $data['total']                  = currency_format_with_sym($invoice->getTotal());
                $data['paid']                   = currency_format_with_sym($invoice->getTotal() - $invoice->getDue() - $invoice->invoiceTotalCreditNote());
                $data['credit_note']            = currency_format_with_sym($invoice->invoiceTotalCreditNote());
                $data['due']                    = currency_format_with_sym($invoice->getDue());
                return response()->json(['status'=>'success','data'=>$data],200);
            }
            else
            {
                return response()->json(['status'=>'error','message'=>__('This invoice is deleted.')],404);
            }
        }
        else
        {
            return response()->json(['status'=>'error','message'=>__('No Data Found!')],404);
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

    function invoiceNumber()
    {
        $latest = company_setting('invoice_starting_number');
        if($latest == null)
        {
            return 1;
        }
        else
        {
            return $latest;
        }
    }
}
