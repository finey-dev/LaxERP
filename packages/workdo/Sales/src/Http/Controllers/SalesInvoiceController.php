<?php

namespace Workdo\Sales\Http\Controllers;

use App\Models\BankTransferPayment;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Crypt;
use Workdo\Sales\Entities\SalesInvoice;
use Workdo\Sales\Entities\SalesUtility;
use Workdo\Sales\Entities\SalesAccount;
use Workdo\Sales\Entities\Opportunities;
use Workdo\Sales\Entities\SalesOrder;
use Workdo\Sales\Entities\Quote;
use Workdo\Sales\Entities\Contact;
use Workdo\Sales\Entities\ShippingProvider;
use Workdo\Sales\Entities\Stream;
use Workdo\Sales\Entities\SalesInvoiceItem;
use Illuminate\Support\Facades\Cookie;
use Workdo\Sales\Entities\SalesInvoicePayment;
use Workdo\Sales\Events\CreateSalesInvoice;
use Workdo\Sales\Events\CreateSalesInvoiceItem;
use Workdo\Sales\Events\DestroySalesInvoice;
use Workdo\Sales\Events\SalesInvoiceItemDuplicate;
use Workdo\Sales\Events\SalesPayInvoice;
use Workdo\Sales\Events\UpdateSalesInvoice;
use Workdo\Sales\Events\UpdateSalesInvoiceItem;
use App\Models\Setting;
use Workdo\Sales\DataTables\SalesInvoiceDataTable;
use Exception;

class SalesInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(SalesInvoiceDataTable $dataTable)
    {
        if(\Auth::user()->isAbleTo('salesinvoice manage'))
        {
            return $dataTable->render('sales::salesinvoice.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($type,$id)
    {
        if(\Auth::user()->isAbleTo('salesinvoice create'))
        {
            $user = User::where('workspace_id',getActiveWorkSpace())->emp()->get()->pluck('name', 'id');
            if(module_is_active('ProductService')){
                $tax = \Workdo\ProductService\Entities\Tax::where('created_by',creatorId())->get()->where('workspace_id',getActiveWorkSpace())->pluck('name', 'id');
                $tax_count =$tax->count();
                $tax->prepend('No Tax', 0);
            }
            else
            {
                $tax=[0 => 'No Tax'];
                $tax_count =$tax;
            }
            $account = SalesAccount::where('created_by',creatorId())->get()->where('workspace',getActiveWorkSpace())->pluck('name', 'id');
            $account->prepend('--', '');
            $opportunities = Opportunities::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $salesorder = SalesOrder::where('created_by',creatorId())->get()->where('workspace',getActiveWorkSpace())->pluck('name', 'id');
            $salesorder->prepend('--', 0);
            $quote = Quote::where('created_by',creatorId())->get()->where('workspace',getActiveWorkSpace())->pluck('name', 'id');
            $quote->prepend('Select Quote', 0);
            $status  = SalesInvoice::$status;
            $contact = Contact::where('created_by',creatorId())->get()->where('workspace',getActiveWorkSpace())->pluck('name', 'id');
            $contact->prepend('--', '');
            $shipping_provider = ShippingProvider::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');

            if(module_is_active('CustomField')){
                $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id',getActiveWorkSpace())->where('module', '=', 'sales')->where('sub_module','sales invoice')->get();
            }else{
                $customFields = null;
            }
            return view('sales::salesinvoice.create', compact('user', 'salesorder', 'quote', 'tax', 'account', 'opportunities', 'status', 'contact', 'shipping_provider', 'type', 'id','customFields','tax_count'));
        }
        else
        {
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
        if(\Auth::user()->isAbleTo('salesinvoice create'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                    'name'                  => 'required|string|max:120',
                                    'quote_number'          =>'required',
                                    'billing_address'       =>'required',
                                    'shipping_address'      =>'required',
                                    'billing_city'          =>'required',
                                    'billing_state'         =>'required',
                                    'shipping_city'         =>'required',
                                    'shipping_state'        =>'required',
                                    'billing_country'       =>'required',
                                    'shipping_country'      =>'required',
                                    'shipping_postalcode'   => 'required',
                                    'billing_postalcode'    => 'required',
                                    'date_quoted'           => 'required',
                                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $sale_order = SalesOrder::where('id', $request->salesorder)->where('workspace',getActiveWorkSpace())->where('created_by',creatorId())->first();

            $invoice                        = new SalesInvoice();
            $invoice['invoice_id']          = $this->invoiceNumber();
            $invoice['name']                = $request->name;
            $invoice['salesorder']          = $request->salesorder;
            $invoice['quote']               = $sale_order->quote;
            $invoice['opportunity']         = $sale_order->opportunity;
            $invoice['status']              = 0;
            $invoice['account']             = $request->account;
            $invoice['date_quoted']         = $request->date_quoted;
            $invoice['quote_number']        = $request->quote_number;
            $invoice['billing_address']     = $request->billing_address;
            $invoice['billing_city']        = $request->billing_city;
            $invoice['billing_state']       = $request->billing_state;
            $invoice['billing_country']     = $request->billing_country;
            $invoice['billing_postalcode']  = $request->billing_postalcode;
            $invoice['shipping_address']    = $request->shipping_address;
            $invoice['shipping_city']       = $request->shipping_city;
            $invoice['shipping_state']      = $request->shipping_state;
            $invoice['shipping_country']    = $request->shipping_country;
            $invoice['shipping_postalcode'] = $request->shipping_postalcode;
            $invoice['billing_contact']     = $request->billing_contact;
            $invoice['shipping_contact']    = $request->shipping_contact;
            $invoice['shipping_provider']   = $request->shipping_provider;
            $invoice['description']         = $request->description;
            $invoice['workspace']           = getActiveWorkSpace();
            $invoice['created_by']          = creatorId();
            $invoice->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'invoice',
                            'stream_comment' => '',
                            'user_name' => $invoice->name,
                        ]
                    ),
                ]
            );


            if(module_is_active('CustomField'))
            {
                \Workdo\CustomField\Entities\CustomField::saveData($invoice, $request->customField);
            }
            $company_settings = getCompanyAllSetting();

            if(!empty($company_settings['New Sales Invoice']) && $company_settings['New Sales Invoice']  == true)
            {
                $Assign_user_phone = User::where('id',$request->user)->where('workspace_id', '=',  getActiveWorkSpace())->first();

                $uArr = [
                    'invoice_id' => $this->invoiceNumber(),
                    'invoice_client' => $Assign_user_phone->name,
                    'date_quoted' => $request->date_quoted,
                    'invoice_status' => 0,
                    'invoice_sub_total' =>  currency_format_with_sym($invoice->getTotal()) ,
                    'created_at' => $request->created_at,

                ];
                $resp = EmailTemplate::sendEmailTemplate('New Sales Invoice', [$invoice->id => $Assign_user_phone->email], $uArr);
            }

            event(new CreateSalesInvoice($request,$invoice));

            return redirect()->back()->with('success', __('The invoice has been created successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public static function invoiceNumber()
    {
        $latest = SalesInvoice::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->latest()->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->invoice_id + 1;
    }

    public function getaccount(Request $request)
    {
        $opportunitie = Opportunities::where('id', $request->opportunities_id)->first();
        $data = [];
        if($opportunitie != null)
        {
            $opportunitie = $opportunitie->toArray();
            $account      = SalesAccount::find($opportunitie['account'])->toArray();

            $data = [
                'response'     => 'success',
                'opportunitie' => $opportunitie,
                'account'      => $account,
            ];
        }
        return response()->json($data);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        if(\Auth::user()->isAbleTo('salesinvoice show'))
        {
            $invoice = SalesInvoice::find($id);
            $bank_transfer_payments = BankTransferPayment::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->where('type','salesinvoice')->where('request',$invoice->id)->get();
            $items         = [];
            $totalTaxPrice = 0;
            $totalQuantity = 0;
            $totalRate     = 0;
            $totalDiscount = 0;
            $taxesData     = [];
            foreach($invoice->itemsdata as $item)
            {
                $totalQuantity += $item->quantity;
                $totalRate     += $item->price;
                $totalDiscount += $item->discount;
                $taxes         = SalesUtility::tax($item->tax);

                $itemTaxes = [];
                foreach($taxes as $tax)
                {
                    if(!empty($tax))
                    {
                        $taxPrice            = SalesUtility::taxRate($tax->rate, $item->price, $item->quantity,$item->discount);
                        $totalTaxPrice       += $taxPrice;
                        $itemTax['tax_name'] = $tax->tax_name;
                        $itemTax['tax']      = $tax->name . '%';
                        $itemTax['price']    = currency_format_with_sym($taxPrice);
                        $itemTaxes[]         = $itemTax;
                        if(array_key_exists($tax->name, $taxesData))
                        {
                            $taxesData[$tax->tax_name] = $taxesData[$tax->tax_name] + $taxPrice;
                        }
                        else
                        {
                            $taxesData[$tax->tax_name] = $taxPrice;
                        }
                    }
                    else
                    {
                        $taxPrice            = SalesUtility::taxRate(0, $item->price, $item->quantity,$item->discount);
                        $totalTaxPrice       += $taxPrice;
                        $itemTax['tax_name'] = 'No Tax';
                        $itemTax['tax']      = '';
                        $itemTax['price']    = currency_format_with_sym($taxPrice);
                        $itemTaxes[]         = $itemTax;

                        if(array_key_exists('No Tax', $taxesData))
                        {
                            $taxesData[$itemTax['tax_name']] = $taxesData['No Tax'] + $taxPrice;
                        }
                        else
                        {
                            $taxesData['No Tax'] = $taxPrice;
                        }

                    }

                }

                $item->itemTax = $itemTaxes;
                $items[]       = $item;

            }
            $invoice->items         = $items;
            $invoice->totalTaxPrice = $totalTaxPrice;
            $invoice->totalQuantity = $totalQuantity;
            $invoice->totalRate     = $totalRate;
            $invoice->totalDiscount = $totalDiscount;
            $invoice->taxesData     = $taxesData;
            $company_settings = getCompanyAllSetting();

            $company_setting['company_name']                  = isset($company_settings['company_name']) ? $company_settings['company_name'] : '';
            $company_setting['company_email']                 = isset($company_settings['company_email']) ? $company_settings['company_email'] : '';
            $company_setting['company_telephone']             = isset($company_settings['company_telephone']) ? $company_settings['company_telephone'] : '';
            $company_setting['company_address']               = isset($company_settings['company_address']) ? $company_settings['company_address'] : '';
            $company_setting['company_city']                  = isset($company_settings['company_city']) ? $company_settings['company_city'] : '';
            $company_setting['company_state']                 = isset($company_settings['company_state']) ? $company_settings['company_state'] : '';
            $company_setting['company_zipcode']               = isset($company_settings['company_zipcode']) ? $company_settings['company_zipcode'] : '';
            $company_setting['company_country']               = isset($company_settings['company_country']) ? $company_settings['company_country'] : '';
            $company_setting['registration_number']           = isset($company_settings['registration_number']) ? $company_settings['registration_number'] : '';
            $company_setting['tax_type']                      = isset($company_settings['tax_type']) ? $company_settings['tax_type'] : '';
            $company_setting['vat_number']                    = isset($company_settings['vat_number']) ? $company_settings['vat_number'] : '';
            $company_setting['salesinvoice_footer_title']     = isset($company_settings['salesinvoice_footer_title']) ? $company_settings['salesinvoice_footer_title'] : '';
            $company_setting['salesinvoice_footer_notes']     = isset($company_settings['salesinvoice_footer_notes']) ? $company_settings['salesinvoice_footer_notes'] : '';
            $company_setting['salesinvoice_shipping_display'] = isset($company_settings['salesinvoice_shipping_display']) ? $company_settings['salesinvoice_shipping_display'] : '';
            $company_setting['sales_invoice_qr_display']      = isset($company_settings['sales_invoice_qr_display']) ? $company_settings['sales_invoice_qr_display'] : '';

            if(module_is_active('CustomField')){
                $invoice->customField = \Workdo\CustomField\Entities\CustomField::getData($invoice, 'sales','sales invoice');
                $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'sales')->where('sub_module','sales invoice')->get();
            }else{
                $customFields = null;
            }

            return view('sales::salesinvoice.view', compact('invoice', 'company_setting','customFields','bank_transfer_payments'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if(\Auth::user()->isAbleTo('salesinvoice edit'))
        {
            $invoice = SalesInvoice::find($id);
            if($invoice){

                $opportunity = Opportunities::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
                $salesorder = SalesOrder::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
                $salesorder->prepend('--', 0);
                $quote = Quote::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
                $quote->prepend('--', 0);
                $account = SalesAccount::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
                $account->prepend('--', '');
                $status          = SalesInvoice::$status;
                $billing_contact = Contact::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
                $billing_contact->prepend('--', '');
                if(module_is_active('ProductService')){
                    $tax = \Workdo\ProductService\Entities\Tax::where('created_by',creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('name', 'id');
                    $tax->prepend('No Tax', 0);
                }
                else
                {
                    $tax=[0 => 'No Tax'];
                }
                $shipping_provider = ShippingProvider::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
                $user              = User::where('workspace_id',getActiveWorkSpace())->emp()->get()->pluck('name', 'id');
                $user->prepend('--', 0);

                if(module_is_active('CustomField')){
                    $invoice->customField = \Workdo\CustomField\Entities\CustomField::getData($invoice, 'sales','sales invoice');
                    $customFields             = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'sales')->where('sub_module','sales invoice')->get();
                }else{
                    $customFields = null;
                }

                return view('sales::salesinvoice.edit', compact('invoice', 'opportunity', 'status', 'account', 'billing_contact', 'tax', 'shipping_provider', 'user', 'salesorder', 'quote','customFields'));
            }else
            {
                return redirect()->back()->with('error', 'Salesinvoice Not Found.');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request,$id)
    {
        if(\Auth::user()->isAbleTo('salesinvoice edit'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                    'name'                  => 'required|string|max:120',
                                    'quote_number'          =>'required',
                                    'billing_address'       =>'required',
                                    'shipping_address'      =>'required',
                                    'billing_city'          =>'required',
                                    'billing_state'         =>'required',
                                    'shipping_city'         =>'required',
                                    'shipping_state'        =>'required',
                                    'billing_country'       =>'required',
                                    'shipping_country'      =>'required',
                                    'shipping_postalcode'   => 'required',
                                    'billing_postalcode'    => 'required',
                                    'date_quoted'           => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            // if(count($request->tax) > 1 && in_array(0, $request->tax))
            // {
            //     return redirect()->back()->with('error', 'Please select valid tax');
            // }
            $sale_order = SalesOrder::where('id', $request->salesorder)->where('workspace',getActiveWorkSpace())->where('created_by',creatorId())->first();

            $invoice = SalesInvoice::find($id);
            // $invoice['user_id']             = $request->user;
            $invoice['name']                = $request->name;
            $invoice['salesorder']          = $request->salesorder;
            $invoice['quote']               = $sale_order->quote;
            $invoice['opportunity']         = $sale_order->opportunity;
            $invoice['account']             = $request->account;
            $invoice['date_quoted']         = $request->date_quoted;
            $invoice['quote_number']        = $request->quote_number;
            $invoice['billing_address']     = $request->billing_address;
            $invoice['billing_city']        = $request->billing_city;
            $invoice['billing_state']       = $request->billing_state;
            $invoice['billing_country']     = $request->billing_country;
            $invoice['billing_postalcode']  = $request->billing_postalcode;
            $invoice['shipping_address']    = $request->shipping_address;
            $invoice['shipping_city']       = $request->shipping_city;
            $invoice['shipping_state']      = $request->shipping_state;
            $invoice['shipping_country']    = $request->shipping_country;
            $invoice['shipping_postalcode'] = $request->shipping_postalcode;
            $invoice['billing_contact']     = $request->billing_contact;
            $invoice['shipping_contact']    = $request->shipping_contact;
            // $invoice['tax']                 = implode(',', $request->tax);
            $invoice['shipping_provider']   = $request->shipping_provider;
            $invoice['description']         = $request->description;
            $invoice['workspace']           = getActiveWorkSpace();
            $invoice['created_by']          = creatorId();
            $invoice->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'invoice',
                            'stream_comment' => '',
                            'user_name' => $invoice->name,
                        ]
                    ),
                ]
            );

            if(module_is_active('CustomField'))
            {
                \Workdo\CustomField\Entities\CustomField::saveData($invoice, $request->customField);
            }

            event(new UpdateSalesInvoice($request,$invoice));

            return redirect()->back()->with('success', __('The invoice details are updated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if(\Auth::user()->isAbleTo('salesinvoice delete'))
        {
            $invoice = SalesInvoice::find($id);
            if(module_is_active('CustomField'))
            {
                $customFields = \Workdo\CustomField\Entities\CustomField::where('module','sales')->where('sub_module','sales invoice')->get();
                foreach($customFields as $customField)
                {
                    $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $id)->where('field_id',$customField->id)->first();
                    if(!empty($value)){
                        $value->delete();
                    }
                }
            }
            event(new DestroySalesInvoice($invoice));
            SalesInvoiceItem::where('invoice_id',$id)->where('created_by',$invoice->created_by)->delete();
            $invoice->delete();

            return redirect()->back()->with('success', __('The invoice has been deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function saveInvoiceTemplateSettings(Request $request)
    {
        $user = \Auth::user();
        $post = $request->all();
        unset($post['_token']);

        if(isset($post['salesinvoice_template']) && (!isset($post['salesinvoice_color']) || empty($post['salesinvoice_color'])))
        {
            $post['salesinvoice_color'] = "ffffff";
        }
        if (!isset($post['sales_invoice_qr_display'])) {
            $post['sales_invoice_qr_display'] = 'off';
        }
        if($request->salesinvoice_logo)
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'salesinvoice_logo' => 'image|mimes:png',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $invoice_logo         = $user->id . '_salesinvoice_logo.png';

            $validation =[
                'mimes:'.'png',
                'max:'.'20480',
            ];

            if($request->hasFile('salesinvoice_logo'))
            {
                $salesinvoice_logo         = $user->id.'_salesinvoice_logo'.time().'.png';
                $company_settings = getCompanyAllSetting();

                $uplaod = upload_file($request,'salesinvoice_logo',$salesinvoice_logo,'salesinvoice_logo');
                if($uplaod['flag'] == 1)
                {
                    $url = $uplaod['url'];
                    $old_salesinvoice_logo = isset($company_settings['salesinvoice_logo']) ? $company_settings['salesinvoice_logo'] : '';
                    if(!empty($old_salesinvoice_logo) && check_file($old_salesinvoice_logo))
                    {
                        delete_file($old_salesinvoice_logo);
                    }
                }
                else
                {
                    return redirect()->back()->with('error',$uplaod['msg']);
                }
            }
        }
        if(isset($post['salesinvoice_logo']))
        {
            $post['salesinvoice_logo'] = $url;
        }
        foreach ($post as $key => $value) {
            // Define the data to be updated or inserted
            $data = [
                'key' => $key,
                'workspace' => getActiveWorkSpace(),
                'created_by' => creatorId(),
            ];
            // Check if the record exists, and update or insert accordingly
            Setting::updateOrInsert($data, ['value' => $value]);

        }
        // Settings Cache forget
        comapnySettingCacheForget();

        return redirect()->back()->with('success', __('The invoice setting are updated successfully.'));
    }

    public function previewInvoice($template, $color)
    {
        $objUser  = \Auth::user();
        $invoice  = new SalesInvoice();

        $user               = new \stdClass();
        $user->company_name = '<Company Name>';
        $user->name         = '<Name>';
        $user->email        = '<Email>';
        $user->mobile       = '<Phone>';
        $user->address      = '<Address>';
        $user->country      = '<Country>';
        $user->state        = '<State>';
        $user->city         = '<City>';
        $user->zip          = '<Zip>';
        $user->bill_address = '<Address>';
        $user->bill_country = '<Country>';
        $user->bill_state   = '<State>';
        $user->bill_city    = '<City>';
        $user->bill_zip     = '<Zip>';

        $totalTaxPrice = 0;
        $taxesData     = [];

        $items = [];
        for($i = 1; $i <= 3; $i++)
        {
            $item           = new \stdClass();
            $item->name     = 'Item ' . $i;
            $item->quantity = 1;
            $item->tax      = 5;
            $item->discount = 50;
            $item->price    = 100;
            $item->description    = 'In publishing and graphic design, Lorem ipsum is a placeholder';

            $taxes = [
                'Tax 1',
                'Tax 2',
            ];

            $itemTaxes = [];
            foreach($taxes as $k => $tax)
            {
                $taxPrice         = 10;
                $totalTaxPrice    += $taxPrice;
                $itemTax['name']  = 'Tax ' . $k;
                $itemTax['rate']  = '10 %';
                $itemTax['price'] = '$10';
                $itemTaxes[]      = $itemTax;
                if(array_key_exists('Tax ' . $k, $taxesData))
                {
                    $taxesData['Tax ' . $k] = $taxesData['Tax 1'] + $taxPrice;
                }
                else
                {
                    $taxesData['Tax ' . $k] = $taxPrice;
                }
            }
            $item->itemTax = $itemTaxes;
            $items[]       = $item;
        }

        $invoice->invoice_id = 1;
        $invoice->issue_date = date('Y-m-d H:i:s');
        $invoice->due_date   = date('Y-m-d H:i:s');
        $invoice->items      = $items;

        $invoice->totalTaxPrice = 60;
        $invoice->totalQuantity = 3;
        $invoice->totalRate     = 300;
        $invoice->totalDiscount = 10;
        $invoice->taxesData     = $taxesData;


        $preview    = 1;
        $color      = '#' . $color;
        $font_color = SalesUtility::getFontColor($color);
        $company_settings = getCompanyAllSetting();

        $dark_logo    = get_file(sidebar_logo());
        $invoice_logo = isset($company_settings['salesinvoice_logo']) ? $company_settings['salesinvoice_logo'] : '';
        if(isset($invoice_logo) && !empty($invoice_logo))
        {
            $img = get_file($invoice_logo);
        }
        else
        {
            $img = $dark_logo;
        }

        $settings['site_rtl']                      = isset($company_settings['site_rtl']) ? $company_settings['site_rtl'] : '';
        $settings['company_name']                  = isset($company_settings['company_name']) ?  $company_settings['company_name'] : '';
        $settings['company_email']                 = isset($company_settings['company_email']) ? $company_settings['company_email'] : '';
        $settings['company_telephone']             = isset($company_settings['company_telephone']) ? $company_settings['company_telephone'] : '';
        $settings['company_address']               = isset($company_settings['company_address']) ? $company_settings['company_address'] : '';
        $settings['company_city']                  = isset($company_settings['company_city']) ? $company_settings['company_city'] : '';
        $settings['company_state']                 = isset($company_settings['company_state']) ? $company_settings['company_state'] : '';
        $settings['company_zipcode']               = isset($company_settings['company_zipcode']) ? $company_settings['company_zipcode'] : '';
        $settings['company_country']               = isset($company_settings['company_country']) ? $company_settings['company_country'] : '';
        $settings['registration_number']           = isset($company_settings['registration_number']) ? $company_settings['registration_number'] : '';
        $settings['tax_type']                      = isset($company_settings['tax_type']) ? $company_settings['tax_type'] : '';
        $settings['vat_number']                    = isset($company_settings['vat_number']) ? $company_settings['vat_number'] : '';
        $settings['salesinvoice_footer_title']     = isset($company_settings['salesinvoice_footer_title']) ? $company_settings['salesinvoice_footer_title'] : '';
        $settings['salesinvoice_footer_notes']     = isset($company_settings['salesinvoice_footer_notes']) ? $company_settings['salesinvoice_footer_notes'] : '';
        $settings['salesinvoice_shipping_display'] = isset($company_settings['salesinvoice_shipping_display']) ? $company_settings['salesinvoice_shipping_display'] : '';
        $settings['salesinvoice_template']         = isset($company_settings['salesinvoice_template']) ? $company_settings['salesinvoice_template'] : '';
        $settings['salesinvoice_color']            = isset($company_settings['salesinvoice_color']) ? $company_settings['salesinvoice_color'] : '';
        $settings['sales_invoice_qr_display']      = isset($company_settings['sales_invoice_qr_display']) ? $company_settings['sales_invoice_qr_display'] : '';


        return view('sales::salesinvoice.templates.' . $template, compact('invoice', 'preview', 'color', 'img', 'settings', 'user', 'font_color'));
    }

    public function pdf($id)
    {
        $invoiceId = Crypt::decrypt($id);
        $invoice   = SalesInvoice::where('id', $invoiceId)->first();

        $data  = \DB::table('settings');
        $data  = $data->where('id', '=', $invoice->created_by);
        $data1 = $data->get();


        $user         = new User();
        $user->name   = $invoice->name;
        $user->email  = $invoice->contacts->email ?? '';
        $user->mobile = $invoice->contacts->phone ?? '';

        $user->bill_address = $invoice->billing_address;
        $user->bill_zip     = $invoice->billing_postalcode;
        $user->bill_city    = $invoice->billing_city;
        $user->bill_country = $invoice->billing_country;
        $user->bill_state   = $invoice->billing_state;

        $user->address = $invoice->shipping_address;
        $user->zip     = $invoice->shipping_postalcode;
        $user->city    = $invoice->shipping_city;
        $user->country = $invoice->shipping_country;
        $user->state   = $invoice->shipping_state;


        $items         = [];
        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];

        foreach($invoice->itemsdata as $product)
        {
            $product_item = \Workdo\ProductService\Entities\ProductService::where('id',$product->item)->first();


            $item           = new \stdClass();
            $item->name     = $product_item->name;
            $item->quantity = $product->quantity;
            $item->tax      = $product->tax;
            $item->discount = $product->discount;
            $item->price    = $product->price;
            $item->description = $product->description;

            $totalQuantity += $item->quantity;
            $totalRate     += $item->price;
            $totalDiscount += $item->discount;

            $taxes     = SalesUtility::tax($product->tax);
            $itemTaxes = [];
            if (!empty($item->tax))
            {
                foreach($taxes as $tax)
                {
                    $taxPrice      = SalesUtility::taxRate($tax->rate, $item->price, $item->quantity,$item->discount);
                    $totalTaxPrice += $taxPrice;

                    $itemTax['name']  = $tax->name;
                    $itemTax['rate']  = $tax->rate . '%';
                    $itemTax['price'] = currency_format_with_sym($taxPrice,$invoice->created_by,$invoice->workspace);
                    $itemTaxes[]      = $itemTax;


                    if(array_key_exists($tax->name, $taxesData))
                    {
                        $taxesData[$tax->name] = $taxesData[$tax->name] + $taxPrice;
                    }
                    else
                    {
                        $taxesData[$tax->name] = $taxPrice;
                    }

                }
            }else
            {
                $item->itemTax = [];
            }
            $item->itemTax = $itemTaxes;
            $items[]       = $item;
        }
        $invoice->issue_date=$invoice->date_quoted;
        $invoice->items         = $items;
        $invoice->totalTaxPrice = $totalTaxPrice;
        $invoice->totalQuantity = $totalQuantity;
        $invoice->totalRate     = $totalRate;
        $invoice->totalDiscount = $totalDiscount;
        $invoice->taxesData     = $taxesData;

        $dark_logo    = get_file(sidebar_logo());
        $company_settings = getCompanyAllSetting($invoice->created_by,$invoice->workspace);

        $invoice_logo = isset($company_settings['salesinvoice_logo']) ? $company_settings['salesinvoice_logo'] : '';
        if(isset($invoice_logo) && !empty($invoice_logo))
        {
            $img = get_file($invoice_logo);
        }
        else
        {
            $img = $dark_logo;
        }
        $settings['site_rtl']                      = isset($company_settings['site_rtl']) ? $company_settings['site_rtl'] : '';
        $settings['company_name']                  = isset($company_settings['company_name']) ? $company_settings['company_name'] : '';
        $settings['company_email']                 = isset($company_settings['company_email']) ? $company_settings['company_email'] : '';
        $settings['company_telephone']             = isset($company_settings['company_telephone']) ? $company_settings['company_telephone'] : '';
        $settings['company_address']               = isset($company_settings['company_address']) ? $company_settings['company_address'] : '';
        $settings['company_city']                  = isset($company_settings['company_city']) ? $company_settings['company_city'] : '';
        $settings['company_state']                 = isset($company_settings['company_state']) ? $company_settings['company_state'] : '';
        $settings['company_zipcode']               = isset($company_settings['company_zipcode']) ? $company_settings['company_zipcode'] : '';
        $settings['company_country']               = isset($company_settings['company_country']) ? $company_settings['company_country'] : '';
        $settings['registration_number']           = isset($company_settings['registration_number']) ? $company_settings['registration_number'] : '';
        $settings['tax_type']                      = isset($company_settings['tax_type']) ? $company_settings['tax_type'] : '';
        $settings['vat_number']                    = isset($company_settings['vat_number']) ? $company_settings['vat_number'] : '';
        $settings['salesinvoice_footer_title']     = isset($company_settings['salesinvoice_footer_title']) ? $company_settings['salesinvoice_footer_title'] : '';
        $settings['salesinvoice_footer_notes']     = isset($company_settings['salesinvoice_footer_notes']) ? $company_settings['salesinvoice_footer_notes'] : '';
        $settings['salesinvoice_shipping_display'] = isset($company_settings['salesinvoice_shipping_display']) ? $company_settings['salesinvoice_shipping_display'] : '';
        $settings['salesinvoice_template']         = isset($company_settings['salesinvoice_template']) ? $company_settings['salesinvoice_template'] : '';
        $settings['salesinvoice_color']            = isset($company_settings['salesinvoice_color']) ? $company_settings['salesinvoice_color'] : '';
        $settings['sales_invoice_qr_display']      = isset($company_settings['sales_invoice_qr_display']) ? $company_settings['sales_invoice_qr_display'] : '';

        if(module_is_active('CustomField')){
            $invoice->customField = \Workdo\CustomField\Entities\CustomField::getData($invoice, 'sales','sales invoice');
            $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace($invoice->created_by))->where('module', '=', 'sales')->where('sub_module','sales invoice')->get();
        }else{
            $customFields = null;
        }

        if($invoice)
        {
            $color      = '#' . $settings['salesinvoice_color'];
            $font_color = SalesUtility::getFontColor($color);
            return view('sales::salesinvoice.templates.' . $settings['salesinvoice_template'], compact('invoice', 'user', 'color', 'settings', 'img', 'font_color','customFields'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function payinvoice($invoice_id)
    {
        if(!empty($invoice_id))
        {
            try {
                $id = \Illuminate\Support\Facades\Crypt::decrypt($invoice_id);
            } catch (\Throwable $th) {
                return redirect('login');
            }

            $invoice = SalesInvoice::where('id',$id)->first();

            if(!is_null($invoice)){


                $items         = [];
                $totalTaxPrice = 0;
                $totalQuantity = 0;
                $totalRate     = 0;
                $totalDiscount = 0;
                $taxesData     = [];

                foreach($invoice->itemsdata as $item)
                {
                    $totalQuantity += $item->quantity;
                    $totalRate     += $item->price;
                    $totalDiscount += $item->discount;
                    $taxes         = SalesUtility::tax($item->tax);

                    $itemTaxes = [];
                    foreach($taxes as $tax)
                    {
                        if(!empty($tax))
                        {

                            $taxPrice            = SalesUtility::taxRate($tax->rate, $item->price, $item->quantity);
                            $totalTaxPrice       += $taxPrice;
                            $itemTax['tax_name'] = $tax->tax_name;
                            $itemTax['tax']      = $tax->rate . '%';
                            $itemTax['price']    = company_date_formate($taxPrice,$invoice->created_by,$invoice->workspace);
                            $itemTaxes[]         = $itemTax;

                            if(array_key_exists($tax->tax_name, $taxesData))
                            {
                                $taxesData[$itemTax['tax_name']] = $taxesData[$tax->tax_name] + $taxPrice;
                            }
                            else
                            {
                                $taxesData[$tax->tax_name] = $taxPrice;
                            }
                        }
                        else
                        {
                            $taxPrice            = SalesUtility::taxRate(0, $item->price, $item->quantity);
                            $totalTaxPrice       += $taxPrice;
                            $itemTax['tax_name'] = 'No Tax';
                            $itemTax['tax']      = '';
                            $itemTax['price']    = company_date_formate($taxPrice,$invoice->created_by,$invoice->workspace);
                            $itemTaxes[]         = $itemTax;

                            if(array_key_exists('No Tax', $taxesData))
                            {
                                $taxesData = $taxesData['No Tax'] + $taxPrice;
                            }
                            else
                            {
                                $taxesData['No Tax'] = $taxPrice;
                            }

                        }
                    }
                    $item->itemTax = $itemTaxes;
                    $items[]       = $item;
                }
                $invoice->items         = $items;
                $invoice->totalTaxPrice = $totalTaxPrice;
                $invoice->totalQuantity = $totalQuantity;
                $invoice->totalRate     = $totalRate;
                $invoice->totalDiscount = $totalDiscount;
                $invoice->taxesData     = $taxesData;

                $ownerId = SalesUtility::ownerIdforInvoice($invoice->created_by);
                $company_settings = getCompanyAllSetting($invoice->created_by,$invoice->workspace);

                $company_setting['company_name']                  = isset($company_settings['company_name']) ? $company_settings['company_name'] : '';
                $company_setting['company_email']                 = isset($company_settings['company_email']) ? $company_settings['company_email'] : '';
                $company_setting['company_telephone']             = isset($company_settings['company_telephone']) ? $company_settings['company_telephone'] : '';
                $company_setting['company_address']               = isset($company_settings['company_address']) ? $company_settings['company_address'] : '';
                $company_setting['company_city']                  = isset($company_settings['company_city']) ? $company_settings['company_city'] : '';
                $company_setting['company_state']                 = isset($company_settings['company_state']) ? $company_settings['company_state'] : '';
                $company_setting['company_zipcode']               = isset($company_settings['company_zipcode']) ? $company_settings['company_zipcode'] : '';
                $company_setting['company_country']               = isset($company_settings['company_country']) ? $company_settings['company_country'] : '';
                $company_setting['registration_number']           = isset($company_settings['registration_number']) ? $company_settings['registration_number'] : '';
                $company_setting['tax_type']                      = isset($company_settings['tax_type']) ? $company_settings['tax_type'] : '';
                $company_setting['vat_number']                    = isset($company_settings['vat_number']) ? $company_settings['vat_number'] : '';
                $company_setting['salesinvoice_footer_title']     = isset($company_settings['salesinvoice_footer_title']) ? $company_settings['salesinvoice_footer_title'] : '';
                $company_setting['salesinvoice_footer_notes']     = isset($company_settings['salesinvoice_footer_notes']) ? $company_settings['salesinvoice_footer_notes'] : '';
                $company_setting['salesinvoice_shipping_display'] = isset($company_settings['salesinvoice_shipping_display']) ? $company_settings['salesinvoice_shipping_display'] : '';
                $company_setting['sales_invoice_qr_display']      = isset($company_settings['sales_invoice_qr_display']) ? $company_settings['sales_invoice_qr_display'] : '';

                $users = User::where('id',$invoice->created_by)->first();

                if(!is_null($users)){
                    \App::setLocale($users->lang);
                }else{
                    $users = User::where('type','company')->first();
                    \App::setLocale($users->lang);
                }


                if(module_is_active('CustomField')){
                    $invoice->customField = \Workdo\CustomField\Entities\CustomField::getData($invoice, 'sales','sales invoice');
                    $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace($invoice->created_by))->where('module', '=', 'sales')->where('sub_module','sales invoice')->get();
                }else{
                    $customFields = null;
                }

                $invoicePayment= SalesInvoicePayment::where("invoice_id",$invoice->id)->first();

                $company_id=$invoice->created_by;
                $workspace = $invoice->workspace;
            event(new SalesPayInvoice($invoice,$invoicePayment));

                return view('sales::salesinvoice.invoicepay',compact('invoice', 'company_setting','users','company_id','workspace','customFields'));
            }else{
                return abort('404', __('The link you followed has expired.'));
            }
        }else{
            return abort('404', __('The link you followed has expired.'));
        }
    }

    public function invoicelink($invoice_id)
    {

        return view('sales::salesinvoice.invoicelink',compact('invoice_id'));
    }

    public function sendmail(Request $request,$id)
    {
        $validator = \Validator::make(
            $request->all(), [
                                'name' => 'required|max:120',
                                'email' => 'required|email'
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $invoice = SalesInvoice::where('id',$id)->first();
        if(!is_null($invoice)){
            $invoice->reciverName = $request->name;
            $invoice->invoice = SalesInvoice::invoiceNumberFormat($invoice->invoice_id);

            $invoiceId    = Crypt::encrypt($invoice->id);
            $invoice->url = route('pay.salesinvoice', $invoiceId);

            $invoice->invoice = SalesInvoice::invoiceNumberFormat($invoice->invoice_id);
            $invoice->url = route('pay.salesinvoice',\Illuminate\Support\Facades\Crypt::encrypt($invoice->id));
            $invoice->reciverName = $request->name;
            $company_settings = getCompanyAllSetting();

            if(!empty($company_settings['Sales Invoice Sent']) && $company_settings['Sales Invoice Sent']  == true)
            {
                $uArr = [
                    'invoice_recivername' => $invoice->reciverName,
                    'salesinvoice_number' => $invoice->invoice,
                    'salesinvoice_url' => $invoice->url,
                ];
                $resp = EmailTemplate::sendEmailTemplate('Sales Invoice Sent', [$request->email],$uArr);
                return redirect()->back()->with('success', __('The sales invoice has been sent successfully.') . ((isset($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
            }
            else{

                return redirect()->back()->with('error', __('The sales invoice sent notification is off.'));
            }
        }else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function invoiceitem($id)
    {
        $invoice = SalesInvoice::find($id);

        $items = \Workdo\ProductService\Entities\ProductService::where('created_by',creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('name', 'id');
        $items->prepend('select', '');
        $tax_rate = \Workdo\ProductService\Entities\Tax::where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('rate', 'id');
        return view('sales::salesinvoice.invoiceitem', compact('items', 'invoice', 'tax_rate'));
    }

    public function storeitem(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(), [

                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $invoice = SalesInvoice::find($id);
        if($invoice->getTotal() == 0.0)
        {
            SalesInvoice::change_status($invoice->id, 0);
        }
        $invoiceitem                = new SalesInvoiceItem();
        $invoiceitem['invoice_id']  = $request->id;
        $invoiceitem['item']        = $request->item;
        $invoiceitem['quantity']    = $request->quantity;
        $invoiceitem['price']       = $request->price;
        $invoiceitem['discount']    = $request->discount;
        $invoiceitem['tax']         = $request->tax;
        $invoiceitem['description'] = $request->description;
        $invoiceitem['workspace']   = getActiveWorkSpace();
        $invoiceitem['created_by']  = creatorId();
        $invoiceitem->save();
        $invoice = SalesInvoice::find($id);
        $invoice_getdue = number_format((float)$invoice->getDue(), 2, '.', '');
        if($invoice_getdue > 0.0 || $invoice_getdue < 0.0)
        {
            SalesInvoice::change_status($invoice->id, 1);
        }
        event(new CreateSalesInvoiceItem($invoice,$request,$invoiceitem));

        return redirect()->back()->with('success', __('The invoice item has been created successfully.'));

    }

    public function items(Request $request)
    {
        $items        = \Workdo\ProductService\Entities\ProductService::where('id', $request->item_id)->first();
        $items->taxes = $items->tax($items->tax_id);

        return json_encode($items);
    }

    public function invoiceItemEdit($id)
    {
        $invoiceItem = SalesInvoiceItem::find($id);

        $items = \Workdo\ProductService\Entities\ProductService::where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('name', 'id');
        $items->prepend('select', '');
        $tax_rate = \Workdo\ProductService\Entities\Tax::where('created_by',creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('rate', 'id');

        return view('sales::salesinvoice.invoiceitemEdit', compact('items', 'invoiceItem', 'tax_rate'));
    }

    public function invoiceItemUpdate(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(), [

                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $invoiceitem                = SalesInvoiceItem::find($id);
        $invoiceitem['item']        = $request->item;
        $invoiceitem['quantity']    = $request->quantity;
        $invoiceitem['price']       = $request->price;
        $invoiceitem['discount']    = $request->discount;
        $invoiceitem['tax']         = $request->tax;
        $invoiceitem['description'] = $request->description;
        $invoiceitem->save();
        event(new UpdateSalesInvoiceItem($invoiceitem,$request));

        return redirect()->back()->with('success', __('The invoice item are updated successfully.'));

    }

    public function itemsDestroy($id)
    {

        $item = SalesInvoiceItem::find($id);
        $invoice = SalesInvoice::find($item->invoice_id);
        $item->delete();
        $invoice_getdue = number_format((float)$invoice->getDue(), 2, '.', '');
        if($invoice_getdue <= 0.0)
        {
            SalesInvoice::change_status($invoice->id, 3);
        }

        return redirect()->back()->with('success', __('The item has been deleted.'));
    }

    public function duplicate($id)
    {
        if(\Auth::user()->isAbleTo('salesinvoice create'))
        {
            $invoice                          = SalesInvoice::find($id);
            $duplicate                        = new SalesInvoice();
            $duplicate['invoice_id']          = $this->invoiceNumber();
            $duplicate['name']                = $invoice->name;
            $duplicate['salesorder']          = $invoice->salesorder;
            $duplicate['quote']               = $invoice->quote;
            $duplicate['opportunity']         = $invoice->opportunity;
            $duplicate['status']              = 0;
            $duplicate['account']             = $invoice->account;
            $duplicate['amount']              = $invoice->amount;
            $duplicate['date_quoted']         = $invoice->date_quoted;
            $duplicate['quote_number']        = $invoice->quote_number;
            $duplicate['billing_address']     = $invoice->billing_address;
            $duplicate['billing_city']        = $invoice->billing_city;
            $duplicate['billing_state']       = $invoice->billing_state;
            $duplicate['billing_country']     = $invoice->billing_country;
            $duplicate['billing_postalcode']  = $invoice->billing_postalcode;
            $duplicate['shipping_address']    = $invoice->shipping_address;
            $duplicate['shipping_city']       = $invoice->shipping_city;
            $duplicate['shipping_state']      = $invoice->shipping_state;
            $duplicate['shipping_country']    = $invoice->shipping_country;
            $duplicate['shipping_postalcode'] = $invoice->shipping_postalcode;
            $duplicate['billing_contact']     = $invoice->billing_contact;
            $duplicate['shipping_contact']    = $invoice->shipping_contact;
            // $duplicate['tax']                 = $invoice->tax;
            $duplicate['shipping_provider']   = $invoice->shipping_provider;
            $duplicate['description']         = $invoice->description;
            $duplicate['workspace']           = getActiveWorkSpace();
            $duplicate['created_by']          = creatorId();
            $duplicate->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'invoice',
                            'stream_comment' => '',
                            'user_name' => $invoice->name,
                        ]
                    ),
                ]
            );

            if($duplicate)
            {
                $invoiceItem = SalesInvoiceItem::where('invoice_id', $invoice->id)->get();

                foreach($invoiceItem as $item)
                {

                    $invoiceitem                = new SalesInvoiceItem();
                    $invoiceitem['invoice_id']  = $duplicate->id;
                    $invoiceitem['item']        = $item->item;
                    $invoiceitem['quantity']    = $item->quantity;
                    $invoiceitem['price']       = $item->price;
                    $invoiceitem['discount']    = $item->discount;
                    $invoiceitem['tax']         = $item->tax;
                    $invoiceitem['description'] = $item->description;
                    $invoiceitem['workspace']   = getActiveWorkSpace();
                    $invoiceitem['created_by']  = creatorId();
                    $invoiceitem->save();
                }
            }
            event(new SalesInvoiceItemDuplicate($duplicate, $invoiceItem));

            return redirect()->back()->with('success', __('The invoice has been duplicated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function transactionNumber($id)
    {
        $latest = SalesInvoicePayment::select('sales_invoice_payments.*')->join('sales_invoices', 'sales_invoice_payments.invoice_id', '=', 'sales_invoices.id')->where('sales_invoices.created_by', '=', $id)->latest()->first();
        if($latest)
        {
            return $latest->transaction_id + 1;
        }

        return 1;
    }

    public function grid()
    {
        if(\Auth::user()->isAbleTo('salesinvoice manage'))
        {
            $invoices = SalesInvoice::where('created_by',creatorId())->where('workspace',getActiveWorkSpace());
            $invoices = $invoices->paginate(11);
            return view('sales::salesinvoice.grid', compact('invoices'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function salesorder_details(Request $request){
        $SalesOrder = SalesOrder::find($request->salesorder_id);
        if($SalesOrder){
            $quote = $SalesOrder->quote;
            $opportunity = $SalesOrder->opportunity;

            return response()->json(
                [
                    'quote' => $quote,
                    'opportunity' => $opportunity,
                ],
                200
            );
        }
        return response()->json(
            ['error' => 'Sales Order not found'],
            404
        );

    }

    public function salesorder_invoice(Request $request ,$id)  {
        try{
            $salesorder = SalesOrder::find($id);
            if($salesorder){
                $salesorder->convert_invoice = 1;
                $salesorder->save();

                $invoice                        = new SalesInvoice();
                $invoice['invoice_id']          = $this->invoiceNumber();
                $invoice['name']                = $salesorder->name;
                $invoice['salesorder']          = $salesorder->id;
                $invoice['quote']               = $salesorder->quote;
                $invoice['opportunity']         = $salesorder->opportunity;
                $invoice['status']              = 0;
                $invoice['account']             = $salesorder->account;
                $invoice['date_quoted']         = $salesorder->date_quoted;
                $invoice['quote_number']        = $salesorder->quote_number;
                $invoice['billing_address']     = $salesorder->billing_address;
                $invoice['billing_city']        = $salesorder->billing_city;
                $invoice['billing_state']       = $salesorder->billing_state;
                $invoice['billing_country']     = $salesorder->billing_country;
                $invoice['billing_postalcode']  = $salesorder->billing_postalcode;
                $invoice['shipping_address']    = $salesorder->shipping_address;
                $invoice['shipping_city']       = $salesorder->shipping_city;
                $invoice['shipping_state']      = $salesorder->shipping_state;
                $invoice['shipping_country']    = $salesorder->shipping_country;
                $invoice['shipping_postalcode'] = $salesorder->shipping_postalcode;
                $invoice['billing_contact']     = $salesorder->billing_contact;
                $invoice['shipping_contact']    = $salesorder->shipping_contact;
                $invoice['shipping_provider']   = $salesorder->shipping_provider;
                $invoice['description']         = $salesorder->description;
                $invoice['workspace']           = getActiveWorkSpace();
                $invoice['created_by']          = creatorId();
                $invoice->save();

                Stream::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'created_by' => creatorId(),
                        'log_type' => 'created',
                        'remark' => json_encode(
                            [
                                'owner_name' => \Auth::user()->username,
                                'title' => 'invoice',
                                'stream_comment' => '',
                                'user_name' => $invoice->name,
                            ]
                        ),
                    ]
                );


                if(module_is_active('CustomField'))
                {
                    \Workdo\CustomField\Entities\CustomField::saveData($invoice, $request->customField);
                }
                $company_settings = getCompanyAllSetting();

                if(!empty($company_settings['New Sales Invoice']) && $company_settings['New Sales Invoice']  == true)
                {
                    $Assign_user_phone = User::where('id',$request->user)->where('workspace_id', '=',  getActiveWorkSpace())->first();

                    $uArr = [
                        'invoice_id' => $this->invoiceNumber(),
                        'invoice_client' => $Assign_user_phone->name,
                        'date_quoted' => $request->date_quoted,
                        'invoice_status' => 0,
                        'invoice_sub_total' =>  currency_format_with_sym($invoice->getTotal()) ,
                        'created_at' => $request->created_at,

                    ];
                    $resp = EmailTemplate::sendEmailTemplate('New Sales Invoice', [$invoice->id => $Assign_user_phone->email], $uArr);
                }

                event(new CreateSalesInvoice($request,$invoice));
                return redirect()->back()->with('success', __('Sales Order successfully Convert to Invoice'));

            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }




    }
}
