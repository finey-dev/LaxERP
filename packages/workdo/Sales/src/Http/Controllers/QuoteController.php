<?php

namespace Workdo\Sales\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Crypt;
use Workdo\Sales\Entities\Quote;
use Workdo\Sales\Entities\SalesUtility;
use Workdo\Sales\Entities\SalesAccount;
use Workdo\Sales\Entities\Opportunities;
use Workdo\Sales\Entities\Contact;
use Workdo\Sales\Entities\ShippingProvider;
use Workdo\Sales\Entities\Stream;
use Workdo\Sales\Entities\QuoteItem;
use Workdo\Sales\Entities\SalesOrder;
use Workdo\Sales\Entities\SalesOrderItem;
use Workdo\Sales\Entities\SalesInvoice;
use Illuminate\Support\Facades\Cookie;
use Workdo\Sales\Events\CreateQuote;
use Workdo\Sales\Events\DestroyQuote;
use Workdo\Sales\Events\UpdateQuote;
use Illuminate\Support\Facades\Auth;
use Workdo\Sales\Events\CreateSalesOrderConvert;
use Workdo\Sales\Events\QuoteDuplicate;
use App\Models\Setting;
use Workdo\Sales\DataTables\SalesQuoteDataTable;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(SalesQuoteDataTable $dataTable)
    {
        if(\Auth::user()->isAbleTo('quote manage'))
        {
            return $dataTable->render('sales::quote.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if(\Auth::user()->isAbleTo('quote create'))
        {
            $user = User::where('workspace_id',getActiveWorkSpace())->emp()->get()->pluck('name', 'id');
            if(module_is_active('ProductService')){
                $tax = \Workdo\ProductService\Entities\Tax::where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('name', 'id');
                $tax_count =$tax->count();
                $tax->prepend('No Tax', 0);

            }else{
                $tax=[0 => 'No Tax'];
                $tax_count =$tax;
            }
            $account = SalesAccount::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');

            $opportunities = Opportunities::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $status  = Quote::$status;
            $contact = Contact::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $contact->prepend('', 0);
            $shipping_provider = ShippingProvider::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            if(module_is_active('CustomField')){
                $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id',getActiveWorkSpace())->where('module', '=', 'sales')->where('sub_module','quotes')->get();
            }else{
                $customFields = null;
            }

            return view('sales::quote.create', compact('user', 'tax', 'account', 'opportunities', 'status', 'contact', 'shipping_provider','customFields','tax_count'));
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
        if(\Auth::user()->isAbleTo('quote create'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                    'name'                  => 'required|string|max:120',
                                    'opportunity'           =>'required',
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
                                    'tax'                   => 'required',
                                    'user'                  => 'required',
                                    'date_quoted'           => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            if(count($request->tax) > 1 && in_array(0, $request->tax))
            {
                return redirect()->back()->with('error', __('Please select valid tax.'));
            }

            $quote                        = new Quote();
            $quote['user_id']             = $request->user;
            $quote['quote_id']            = $this->quoteNumber();
            $quote['name']                = $request->name;
            $quote['opportunity']         = $request->opportunity;
            $quote['status']              = $request->status;
            $quote['account']             = $request->account_id;
            $quote['date_quoted']         = $request->date_quoted;
            $quote['quote_number']        = $request->quote_number;
            $quote['billing_address']     = $request->billing_address;
            $quote['billing_city']        = $request->billing_city;
            $quote['billing_state']       = $request->billing_state;
            $quote['billing_country']     = $request->billing_country;
            $quote['billing_postalcode']  = $request->billing_postalcode;
            $quote['shipping_address']    = $request->shipping_address;
            $quote['shipping_city']       = $request->shipping_city;
            $quote['shipping_state']      = $request->shipping_state;
            $quote['shipping_country']    = $request->shipping_country;
            $quote['shipping_postalcode'] = $request->shipping_postalcode;
            $quote['billing_contact']     = $request->billing_contact;
            $quote['shipping_contact']    = $request->shipping_contact;
            // $quote['tax']                 = implode(',', $request->tax);
            $quote['shipping_provider']   = $request->shipping_provider;
            $quote['description']         = $request->description;
            $quote['workspace']         = getActiveWorkSpace();
            $quote['created_by']          = creatorId();
            $quote->save();

            Stream::create(
                [
                    'user_id' => Auth::user()->id,
                    'created_by' =>creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => Auth::user()->username,
                            'title' => 'quote',
                            'stream_comment' => '',
                            'user_name' => $quote->name,
                        ]
                    ),
                ]
            );

            if(module_is_active('CustomField'))
            {
                \Workdo\CustomField\Entities\CustomField::saveData($quote, $request->customField);
            }
            $company_settings = getCompanyAllSetting();
            if(!empty($company_settings['New Quotation']) && $company_settings['New Quotation']  == true)
            {
                $Assign_user_phone = User::where('id',$request->user)->where('workspace_id', '=',  getActiveWorkSpace())->first();

                $uArr = [
                    'quote_number' => $request->quote_number,
                    'billing_address' => $request->billing_address,
                    'shipping_address' => $request->shipping_address,
                    'description' => $request->description,
                    'date_quoted' => $request->date_quoted,
                    'quote_assign_user' => $Assign_user_phone->name,
                ];
                $resp =EmailTemplate::sendEmailTemplate('New Quotation', [$quote->id => $Assign_user_phone->email], $uArr);
            }
            event(new CreateQuote($request,$quote));

            return redirect()->back()->with('success', __('The quote has been created successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Quote $quote)
    {
        if(\Auth::user()->isAbleTo('quote show'))
        {
            $items         = [];
            $totalTaxPrice = 0;
            $totalQuantity = 0;
            $totalRate     = 0;
            $totalDiscount = 0;
            $taxesData     = [];
            foreach($quote->itemsdata as $item)
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
        $company_settings = getCompanyAllSetting();

            $quote->items         = $items;
            $quote->totalTaxPrice = $totalTaxPrice;
            $quote->totalQuantity = $totalQuantity;
            $quote->totalRate     = $totalRate;
            $quote->totalDiscount = $totalDiscount;
            $quote->taxesData     = $taxesData;
            $company_setting['company_name']           = isset($company_settings['company_name']) ? $company_settings['company_name'] : '';
            $company_setting['company_address']        = isset($company_settings['company_address']) ? $company_settings['company_address'] : '';
            $company_setting['company_city']           = isset($company_settings['company_city']) ? $company_settings['company_city'] : '';
            $company_setting['company_state']          = isset($company_settings['company_state']) ? $company_settings['company_state'] : '';
            $company_setting['company_zipcode']        = isset($company_settings['company_zipcode']) ? $company_settings['company_zipcode'] : '';
            $company_setting['company_country']        = isset($company_settings['company_country']) ? $company_settings['company_country'] : '';
            $company_setting['registration_number']    = isset($company_settings['registration_number']) ? $company_settings['registration_number'] : '';
            $company_setting['tax_type']               = isset($company_settings['tax_type']) ? $company_settings['tax_type'] : '';
            $company_setting['vat_number']             = isset($company_settings['vat_number']) ? $company_settings['vat_number'] : '';
            $company_setting['quote_footer_title']     = isset($company_settings['quote_footer_title']) ? $company_settings['quote_footer_title'] : '';
            $company_setting['quote_footer_notes']     = isset($company_settings['quote_footer_notes']) ? $company_settings['quote_footer_notes'] : '';
            $company_setting['quote_shipping_display'] = isset($company_settings['quote_shipping_display']) ? $company_settings['quote_shipping_display'] : '';
            $company_setting['quote_qr_display']       = isset($company_settings['quote_qr_display']) ? $company_settings['quote_qr_display'] : '';

            if(module_is_active('CustomField')){
                $quote->customField = \Workdo\CustomField\Entities\CustomField::getData($quote, 'Sales','Quotes');
                $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'Sales')->where('sub_module','Quotes')->get();
            }else{
                $customFields = null;
            }

            return view('sales::quote.view', compact('quote','company_setting','customFields'));
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
    public function edit(Quote $quote)
    {
        if(\Auth::user()->isAbleTo('quote edit'))
        {
            $opportunity     = Opportunities::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $account         = SalesAccount::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $billing_contact = Contact::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $billing_contact->prepend('--', '');
            if(module_is_active('ProductService')){
                $tax = \Workdo\ProductService\Entities\Tax::where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('name', 'id');
                $tax->prepend('No Tax', 0);
            }else{
                $tax=[0 => 'No Tax'];
            }
            $shipping_provider = ShippingProvider::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $user              = User::where('workspace_id',getActiveWorkSpace())->emp()->get()->pluck('name', 'id');
            $salesorders       = SalesOrder::where('quote', $quote->id)->get();
            $invoices          = SalesInvoice::where('quote', $quote->id)->get();
            $status            = Quote::$status;
            // get previous user id
            $previous = Quote::where('id', '<', $quote->id)->max('id');
            // get next user id
            $next = Quote::where('id', '>', $quote->id)->min('id');

            if(module_is_active('CustomField')){
                $quote->customField = \Workdo\CustomField\Entities\CustomField::getData($quote, 'HospitalManagement','Doctor');
                $customFields             = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'HospitalManagement')->where('sub_module','Doctor')->get();
            }else{
                $customFields = null;
            }
            return view('sales::quote.edit', compact('quote', 'salesorders' ,'opportunity', 'status', 'account', 'billing_contact', 'tax', 'shipping_provider', 'user','invoices','previous', 'next','customFields'));
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
    public function update(Request $request, Quote $quote)
    {
        if(\Auth::user()->isAbleTo('quote edit'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                    'name'                  => 'required|string|max:120',
                                    'opportunity'           =>'required',
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
                                    'tax'                   => 'required',
                                    'user'                  => 'required',
                                    'date_quoted'           => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            if(count($request->tax) > 1 && in_array(0, $request->tax))
            {
                return redirect()->back()->with('error', __('Please select valid tax.'));
            }

            $quote['user_id']             = $request->user;
            // $quote['quote_id']            = $this->quoteNumber();
            $quote['name']                = $request->name;
            $quote['opportunity']         = $request->opportunity;
            $quote['status']              = $request->status;
            $quote['account']             = $request->account;
            $quote['date_quoted']         = $request->date_quoted;
            $quote['quote_number']        = $request->quote_number;
            $quote['billing_address']     = $request->billing_address;
            $quote['billing_city']        = $request->billing_city;
            $quote['billing_state']       = $request->billing_state;
            $quote['billing_country']     = $request->billing_country;
            $quote['billing_postalcode']  = $request->billing_postalcode;
            $quote['shipping_address']    = $request->shipping_address;
            $quote['shipping_city']       = $request->shipping_city;
            $quote['shipping_state']      = $request->shipping_state;
            $quote['shipping_country']    = $request->shipping_country;
            $quote['shipping_postalcode'] = $request->shipping_postalcode;
            $quote['billing_contact']     = $request->billing_contact;
            $quote['shipping_contact']    = $request->shipping_contact;
            // $quote['tax']                 = implode(',', $request->tax);
            $quote['shipping_provider']   = $request->shipping_provider;
            $quote['description']         = $request->description;
            $quote['workspace']         = getActiveWorkSpace();
            $quote['created_by']          = creatorId();
            $quote->update();

            Stream::create(
                [
                    'user_id' => Auth::user()->id,
                    'created_by' => creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => Auth::user()->username,
                            'title' => 'quote',
                            'stream_comment' => '',
                            'user_name' => $quote->name,
                        ]
                    ),
                ]
            );


            if(module_is_active('CustomField'))
            {
                \Workdo\CustomField\Entities\CustomField::saveData($quote, $request->customField);
            }
            event(new UpdateQuote($request,$quote));

            return redirect()->back()->with('success', __('The quote details are updated successfully.'));
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
    public function destroy(Quote $quote)
    {
        if(\Auth::user()->isAbleTo('quote delete'))
        {
            if(module_is_active('CustomField'))
            {
                $customFields = \Workdo\CustomField\Entities\CustomField::where('module','HospitalManagement')->where('sub_module','Doctor')->get();
                foreach($customFields as $customField)
                {
                    $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $quote->id)->where('field_id',$customField->id)->first();
                    if(!empty($value)){
                        $value->delete();
                    }
                }
            }
            event(new DestroyQuote($quote));
            QuoteItem::where('quote_id',$quote->id)->where('created_by',$quote->created_by)->delete();
            $quote->delete();

            return redirect()->back()->with('success', __('The quote has been deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
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




    public function getaccount(Request $request)
    {
        if($request->opportunities_id)
        {
            $opportunitie = Opportunities::where('id', $request->opportunities_id)->first()->toArray();
            $account = SalesAccount::find($opportunitie['account'])->toArray();

            return response()->json(
                [
                    'opportunitie' => $opportunitie,
                    'account' => $account,
                ]
            );
        }
    }


    public function quoteitem($id)
    {
        $quote = Quote::find($id);

        $items = \Workdo\ProductService\Entities\ProductService::where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('name', 'id');
        $items->prepend('select Item', '');
        $tax_rate = \Workdo\ProductService\Entities\Tax::where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('rate', 'id');
        return view('sales::quote.quoteitem', compact('items', 'quote', 'tax_rate'));
    }

    public function itemsDestroy($id)
    {
        $item = QuoteItem::find($id);
        $item->delete();

        return redirect()->back()->with('success', __('The item has been deleted.'));
    }

    public function items(Request $request)
    {
        $items        = \Workdo\ProductService\Entities\ProductService::where('id', $request->item_id)->first();
        if($items != null)
        {
            $items->taxes = $items->tax($items->tax_id);
            return json_encode($items);
        }
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
        $quoteitem                = new QuoteItem();
        $quoteitem['quote_id']    = $request->id;
        $quoteitem['item']        = $request->item;
        $quoteitem['quantity']    = $request->quantity;
        $quoteitem['price']       = $request->price;
        $quoteitem['discount']    = $request->discount;
        $quoteitem['tax']         = $request->tax;
        $quoteitem['description'] = $request->description;
        $quoteitem['workspace'] = getActiveWorkSpace();
        $quoteitem['created_by']  = creatorId();
        $quoteitem->save();

        return redirect()->back()->with('success', __('The quote item has been created successfully.'));

    }

    public function quoteitemEdit($id)
    {
        $quoteItem = QuoteItem::find($id);

        $items = \Workdo\ProductService\Entities\ProductService::where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('name', 'id');
        $items->prepend('select Item', '');

        $tax_rate = \Workdo\ProductService\Entities\Tax::where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('rate', 'id');

        return view('sales::quote.quoteitemEdit', compact('items', 'quoteItem', 'tax_rate'));
    }

    public function quoteitemUpdate(Request $request, $id)
    {

        $quoteitem                = QuoteItem::find($id);
        $quoteitem['item']        = $request->item;
        $quoteitem['quantity']    = $request->quantity;
        $quoteitem['price']       = $request->price;
        $quoteitem['discount']    = $request->discount;
        $quoteitem['tax']         = $request->tax;
        $quoteitem['description'] = $request->description;
        $quoteitem->save();

        return redirect()->back()->with('success', __('The quote item are updated successfully.'));

    }


    public function previewQuote($template, $color)
    {
        $objUser  = Auth::user();
        $quote    = new Quote();

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

        $quote->quote_id   = 1;
        $quote->issue_date = date('Y-m-d H:i:s');
        $quote->due_date   = date('Y-m-d H:i:s');
        $quote->items      = $items;

        $quote->totalTaxPrice = 60;
        $quote->totalQuantity = 3;
        $quote->totalRate     = 300;
        $quote->totalDiscount = 10;
        $quote->taxesData     = $taxesData;

        $company_settings = getCompanyAllSetting();

        $preview    = 1;
        $color      = '#' . $color;
        $font_color = SalesUtility::getFontColor($color);

        $company_logo = get_file(sidebar_logo());
        $quote_logo = isset($company_settings['quote_logo']) ? $company_settings['quote_logo'] : '';
        if(isset($quote_logo) && !empty($quote_logo))
        {
            $img = get_file($quote_logo);
        }
        else
        {
            $img = $company_logo;
        }

        $settings['site_rtl']               = isset($company_settings['site_rtl']) ? $company_settings['site_rtl'] : '';
        $settings['company_name']           = isset($company_settings['company_name']) ? $company_settings['company_name'] : '';
        $settings['company_email']          = isset($company_settings['company_email']) ? $company_settings['company_email'] : '';
        $settings['company_telephone']      = isset($company_settings['company_telephone']) ? $company_settings['company_telephone'] : '';
        $settings['company_address']        = isset($company_settings['company_address']) ? $company_settings['company_address'] : '';
        $settings['company_city']           = isset($company_settings['company_city']) ? $company_settings['company_city'] : '';
        $settings['company_state']          = isset($company_settings['company_state']) ? $company_settings['company_state'] : '';
        $settings['company_zipcode']        = isset($company_settings['company_zipcode']) ? $company_settings['company_zipcode'] : '';
        $settings['company_country']        = isset($company_settings['company_country']) ? $company_settings['company_country'] : '';
        $settings['registration_number']    = isset($company_settings['registration_number']) ? $company_settings['registration_number'] : '';
        $settings['tax_type']               = isset($company_settings['tax_type']) ? $company_settings['tax_type'] : '';
        $settings['vat_number']             = isset($company_settings['vat_number']) ? $company_settings['vat_number'] : '';
        $settings['quote_footer_title']     = isset($company_settings['quote_footer_title']) ? $company_settings['quote_footer_title'] : '';
        $settings['quote_footer_notes']     = isset($company_settings['quote_footer_notes']) ? $company_settings['quote_footer_notes'] : '';
        $settings['quote_shipping_display'] = isset($company_settings['quote_shipping_display']) ? $company_settings['quote_shipping_display'] : '';
        $settings['quote_template']         = isset($company_settings['quote_template']) ? $company_settings['quote_template'] : '';
        $settings['quote_color']            = isset($company_settings['quote_color']) ? $company_settings['quote_color'] : '';
        $settings['quote_qr_display']       = isset($company_settings['quote_qr_display']) ? $company_settings['quote_qr_display'] : '';

        return view('sales::quote.templates.' . $template, compact('quote', 'preview', 'color', 'img', 'settings', 'user', 'font_color'));
    }


    public function duplicate($id)
    {
        if(\Auth::user()->isAbleTo('quote create'))
        {
            $quote                            = Quote::find($id);

            $duplicate                        = new Quote();
            $duplicate['user_id']             = $quote->user_id;
            $duplicate['quote_id']            = $this->quoteNumber();
            $duplicate['name']                = $quote->name;
            $duplicate['opportunity']         = $quote->opportunity;
            $duplicate['status']              = $quote->status;
            $duplicate['account']             = $quote->account;
            $duplicate['amount']              = $quote->amount;
            $duplicate['date_quoted']         = $quote->date_quoted;
            $duplicate['quote_number']        = $quote->quote_number;
            $duplicate['billing_address']     = $quote->billing_address;
            $duplicate['billing_city']        = $quote->billing_city;
            $duplicate['billing_state']       = $quote->billing_state;
            $duplicate['billing_country']     = $quote->billing_country;
            $duplicate['billing_postalcode']  = $quote->billing_postalcode;
            $duplicate['shipping_address']    = $quote->shipping_address;
            $duplicate['shipping_city']       = $quote->shipping_city;
            $duplicate['shipping_state']      = $quote->shipping_state;
            $duplicate['shipping_country']    = $quote->shipping_country;
            $duplicate['shipping_postalcode'] = $quote->shipping_postalcode;
            $duplicate['billing_contact']     = $quote->billing_contact;
            $duplicate['shipping_contact']    = $quote->shipping_contact;
            // $duplicate['tax']                 = $quote->tax;
            $duplicate['shipping_provider']   = $quote->shipping_provider;
            $duplicate['description']         = $quote->description;
            $duplicate['workspace']           = getActiveWorkSpace();
            $duplicate['created_by']          = creatorId();
            $duplicate->save();

            Stream::create(
                [
                    'user_id' => Auth::user()->id,
                    'created_by' => creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => Auth::user()->username,
                            'title' => 'quote',
                            'stream_comment' => '',
                            'user_name' => $quote->name,
                        ]
                    ),
                ]
            );

            if($duplicate)
            {
                $quoteItem = QuoteItem::where('quote_id', $quote->id)->get();

                foreach($quoteItem as $item)
                {

                    $quoteitem                = new QuoteItem();
                    $quoteitem['quote_id']    = $duplicate->id;
                    $quoteitem['item']        = $item->item;
                    $quoteitem['quantity']    = $item->quantity;
                    $quoteitem['price']       = $item->price;
                    $quoteitem['discount']    = $item->discount;
                    $quoteitem['tax']         = $item->tax;
                    $quoteitem['description'] = $item->description;
                    $quoteitem['created_by']  = creatorId();
                    $quoteitem->save();
                }
            }
            event(new QuoteDuplicate($duplicate,$quoteItem));

            return redirect()->back()->with('success', __('The quote has been duplicated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function convert($id)
    {
        $quote = Quote::find($id);

        $salesorder                        = new SalesOrder();
        $salesorder['user_id']             = $quote->user_id;
        $salesorder['salesorder_id']       = $this->salesorderNumber();
        $salesorder['name']                = $quote->name;
        $salesorder['quote']               = $id;
        $salesorder['opportunity']         = $quote->opportunity;
        $salesorder['status']              = $quote->status;
        $salesorder['account']             = $quote->account;
        $salesorder['amount']              = $quote->amount;
        $salesorder['date_quoted']         = $quote->date_quoted;
        $salesorder['quote_number']        = $quote->quote_number;
        $salesorder['billing_address']     = $quote->billing_address;
        $salesorder['billing_city']        = $quote->billing_city;
        $salesorder['billing_state']       = $quote->billing_state;
        $salesorder['billing_country']     = $quote->billing_country;
        $salesorder['billing_postalcode']  = $quote->billing_postalcode;
        $salesorder['shipping_address']    = $quote->shipping_address;
        $salesorder['shipping_city']       = $quote->shipping_city;
        $salesorder['shipping_state']      = $quote->shipping_state;
        $salesorder['shipping_country']    = $quote->shipping_country;
        $salesorder['shipping_postalcode'] = $quote->shipping_postalcode;
        $salesorder['billing_contact']     = $quote->billing_contact;
        $salesorder['shipping_contact']    = $quote->shipping_contact;
        $salesorder['tax']                 = $quote->tax;
        $salesorder['shipping_provider']   = $quote->shipping_provider;
        $salesorder['description']         = $quote->description;
        $salesorder['workspace']           = getActiveWorkSpace();
        $salesorder['created_by']          = $quote->created_by;
        $salesorder->save();

        if(!empty($salesorder))
        {
            $quote->converted_salesorder_id = $salesorder->id;
            $quote->save();
        }
        Stream::create(
            [
                'user_id' => Auth::user()->id,
                'created_by' => creatorId(),
                'log_type' => 'created',
                'remark' => json_encode(
                    [
                        'owner_name' => Auth::user()->username,
                        'title' => 'salesorder',
                        'stream_comment' => '',
                        'user_name' => $salesorder->name,
                    ]
                ),
            ]
        );

        if($salesorder)
        {
            $quotesProduct = QuoteItem::where('quote_id', $quote->id)->get();

            foreach($quotesProduct as $product)
            {
                $salesorderProduct                = new SalesOrderItem();
                $salesorderProduct->salesorder_id = $salesorder->id;
                $salesorderProduct->item          = $product->item;
                $salesorderProduct->quantity      = $product->quantity;
                $salesorderProduct->price         = $product->price;
                $salesorderProduct->discount      = $product->discount;
                $salesorderProduct->tax           = $product->tax;
                $salesorderProduct->description   = $product->description;
                $salesorderProduct->created_by    = $product->created_by;
                $salesorderProduct->save();
            }
        }
        event(new CreateSalesOrderConvert($salesorder,$quotesProduct));

        return redirect()->back()->with('success', __('The quote to sales order has been converted successfully.'));
    }


    function salesorderNumber()
    {
        $latest = SalesOrder::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->latest()->first();

        if(!$latest)
        {
            return 1;
        }

        return $latest->salesorder_id + 1;
    }

    public function saveQuoteTemplateSettings(Request $request)
    {
        $user = Auth::user();
        if($request->quote_logo)
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'quote_logo' => 'image|mimes:png',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            if($request->hasFile('quote_logo'))
            {
                $quote_logo         = $user->id.'_quote_logo'.time().'.png';
                $company_settings = getCompanyAllSetting();

                $uplaod = upload_file($request,'quote_logo',$quote_logo,'quote_logo');
                if($uplaod['flag'] == 1)
                {
                    $url = $uplaod['url'];
                    $old_quote_logo = isset($company_settings['quote_logo']) ? $company_settings['quote_logo'] : '';
                    if(!empty($old_quote_logo) && check_file($old_quote_logo))
                    {
                        delete_file($old_quote_logo);
                    }
                }
                else
                {
                    return redirect()->back()->with('error',$uplaod['msg']);
                }
            }
        }
        $post = $request->all();
        unset($post['_token']);
        if(isset($post['quote_template']) && (!isset($post['quote_color']) || empty($post['quote_color'])))
        {
            $post['quote_color'] = "ffffff";
        }
        if(isset($post['quote_logo']))
        {
            $post['quote_logo'] = $url;
        }
        if (!isset($post['quote_qr_display'])) {
            $post['quote_qr_display'] = 'off';
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

        return redirect()->back()->with('success', __('The quote setting are updated successfully.'));
    }

    public function payquote($quote_id){

        if(!empty($quote_id))
        {
            try {
                $id = \Illuminate\Support\Facades\Crypt::decrypt($quote_id);
            } catch (\Throwable $th) {
                return redirect('login');
            }

            $quote = Quote::where('id',$id)->first();

            if(!is_null($quote)){
                $items         = [];
                $totalTaxPrice = 0;
                $totalQuantity = 0;
                $totalRate     = 0;
                $totalDiscount = 0;
                $taxesData     = [];

                foreach($quote->itemsdata as $item)
                {
                    $totalQuantity += $item->quantity;
                    $totalRate     += $item->price;
                    $totalDiscount += $item->discount;
                    $taxes         = SalesUtility::tax($item->tax);

                    $itemTaxes = [];
                    if (!empty($item->tax))
                    {
                        foreach($taxes as $tax)
                        {
                                if(!empty($tax))
                                {
                                    $taxPrice            = SalesUtility::taxRate($tax->rate, $item->price, $item->quantity);
                                    $totalTaxPrice       += $taxPrice;
                                    $itemTax['tax_name'] = $tax->tax_name;
                                    $itemTax['tax']      = $tax->rate . '%';
                                    $itemTax['price']    = company_date_formate($taxPrice,$quote->created_by,$quote->workspace);
                                    $itemTaxes[]         = $itemTax;

                                    if(array_key_exists($tax->name, $taxesData))
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
                                    $itemTax['price']    = company_date_formate($taxPrice,$quote->created_by,$quote->workspace);
                                    $itemTaxes[]         = $itemTax;

                                    if(array_key_exists('No Tax', $taxesData))
                                    {
                                        $taxesData[$tax->tax_name] = $taxesData['No Tax'] + $taxPrice;
                                    }
                                    else
                                    {
                                        $taxesData['No Tax'] = $taxPrice;
                                    }

                                }
                        }
                    }
                    else
                    {
                        $item->itemTax = [];
                    }
                    $item->itemTax = $itemTaxes;
                    $items[]       = $item;
                }
                $quote->items         = $items;
                $quote->totalTaxPrice = $totalTaxPrice;
                $quote->totalQuantity = $totalQuantity;
                $quote->totalRate     = $totalRate;
                $quote->totalDiscount = $totalDiscount;
                $quote->taxesData     = $taxesData;
                $ownerId = SalesUtility::ownerIdforQuote($quote->created_by);
                $company_settings = getCompanyAllSetting($quote->created_by,$quote->workspace);

                $company_setting['company_name']           = isset($company_settings['company_name']) ? $company_settings['company_name'] : '';
                $company_setting['company_email']          = isset($company_settings['company_email']) ? $company_settings['company_email'] : '';
                $company_setting['company_telephone']      = isset($company_settings['company_telephone']) ? $company_settings['company_telephone'] : '';
                $company_setting['company_address']        = isset($company_settings['company_address']) ? $company_settings['company_address'] : '';
                $company_setting['company_city']           = isset($company_settings['company_city']) ? $company_settings['company_city'] : '';
                $company_setting['company_state']          = isset($company_settings['company_state']) ? $company_settings['company_state'] : '';
                $company_setting['company_zipcode']        = isset($company_settings['company_zipcode']) ? $company_settings['company_zipcode'] : '';
                $company_setting['company_country']        = isset($company_settings['company_country']) ? $company_settings['company_country'] : '';
                $company_setting['registration_number']    = isset($company_settings['registration_number']) ? $company_settings['registration_number'] : '';
                $company_setting['tax_type']               = isset($company_settings['tax_type']) ? $company_settings['tax_type'] : '';
                $company_setting['vat_number']             = isset($company_settings['vat_number']) ? $company_settings['vat_number'] : '';
                $company_setting['quote_footer_title']     = isset($company_settings['quote_footer_title']) ? $company_settings['quote_footer_title'] : '';
                $company_setting['quote_footer_notes']     = isset($company_settings['quote_footer_notes']) ? $company_settings['quote_footer_notes'] : '';
                $company_setting['quote_shipping_display'] = isset($company_settings['quote_shipping_display']) ? $company_settings['quote_shipping_display'] : '';
                $company_setting['quote_qr_display']       = isset($company_settings['quote_qr_display']) ? $company_settings['quote_qr_display'] : '';

                $users = User::where('id',$quote->created_by)->first();

                if(!is_null($users)){
                    \App::setLocale($users->lang);
                }else{
                    $users = User::where('type','owner')->first();
                    \App::setLocale($users->lang);
                }

                if(module_is_active('CustomField')){
                    $quote->customField = \Workdo\CustomField\Entities\CustomField::getData($quote, 'Sales','Quotes');
                    $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace($quote->created_by))->where('module', '=', 'Sales')->where('sub_module','Quotes')->get();
                }else{
                    $customFields = null;
                }
                $company_id=$quote->created_by;
                $workspace = $quote->workspace;
                return view('sales::quote.quotepay',compact('quote', 'company_setting','users','company_id','workspace','customFields'));
            }else{
                return abort('404', __('The link you followed has expired.'));
            }
        }else{
            return abort('404', __('The link you followed has expired.'));
        }
    }

    public function pdf($id)
    {
        $quoteId = Crypt::decrypt($id);
        $quote   = Quote::where('id', $quoteId)->first();
        $data  = \DB::table('settings');
        $data  = $data->where('id', '=', $quote->created_by);
        $data1 = $data->get();

        $user         = new User();
        $user->name   = $quote->name;
        $user->email  = $quote->contacts->email ?? '';
        $user->mobile = $quote->contacts->phone ?? '';

        $user->bill_address = $quote->billing_address;
        $user->bill_zip     = $quote->billing_postalcode;
        $user->bill_city    = $quote->billing_city;
        $user->bill_country = $quote->billing_country;
        $user->bill_state   = $quote->billing_state;

        $user->address = $quote->shipping_address;
        $user->zip     = $quote->shipping_postalcode;
        $user->city    = $quote->shipping_city;
        $user->country = $quote->shipping_country;
        $user->state   = $quote->shipping_state;

        $items         = [];
        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];

        foreach($quote->itemsdata as $product)
        {
            $product_item = \Workdo\ProductService\Entities\ProductService::where('id',$product->item)->first();

            $item           = new \stdClass();
            $item->name     = $product_item->name;
            $item->quantity = $product->quantity;
            $item->tax      = !empty($product->tax) ? $product->tax: '';
            $item->discount = $product->discount;
            $item->price    = $product->price;
            $item->description = $product->description;

            $totalQuantity  += $item->quantity;
            $totalRate      += $item->price;
            $totalDiscount  += $item->discount;

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
                    $itemTax['price'] = currency_format_with_sym($taxPrice,$quote->created_by,$quote->workspace);
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
            }
            else
            {
                $item->itemTax = [];
            }
            $item->itemTax = $itemTaxes;
            $items[]       = $item;
        }
        $quote->issue_date=$quote->date_quoted;
        $quote->items         = $items;
        $quote->totalTaxPrice = $totalTaxPrice;
        $quote->totalQuantity = $totalQuantity;
        $quote->totalRate     = $totalRate;
        $quote->totalDiscount = $totalDiscount;
        $quote->taxesData     = $taxesData;

        //Set your logo
        $company_settings = getCompanyAllSetting($quote->created_by,$quote->workspace);

        $quote_logo = isset($company_settings['quote_logo']) ? $company_settings['quote_logo'] : '';
        $company_logo = get_file(sidebar_logo());
        if(isset($quote_logo) && !empty($quote_logo))
        {
            $img = get_file($quote_logo);
        }
        else
        {
            $img = $company_logo;
        }

        $settings['site_rtl']               = isset($company_settings['site_rtl']) ? $company_settings['site_rtl'] : '';
        $settings['company_name']           = isset($company_settings['company_name']) ? $company_settings['company_name'] : '';
        $settings['company_email']          = isset($company_settings['company_email']) ? $company_settings['company_email'] : '';
        $settings['company_telephone']      = isset($company_settings['company_telephone']) ? $company_settings['company_telephone'] : '';
        $settings['company_address']        = isset($company_settings['company_address']) ? $company_settings['company_address'] : '';
        $settings['company_city']           = isset($company_settings['company_city']) ? $company_settings['company_city'] : '';
        $settings['company_state']          = isset($company_settings['company_state']) ? $company_settings['company_state'] : '' ;
        $settings['company_zipcode']        = isset($company_settings['company_zipcode']) ? $company_settings['company_zipcode'] : '';
        $settings['company_country']        = isset($company_settings['company_country']) ? $company_settings['company_country'] : '';
        $settings['registration_number']    = isset($company_settings['registration_number']) ? $company_settings['registration_number'] : '';
        $settings['tax_type']               = isset($company_settings['tax_type']) ? $company_settings['tax_type'] : '';
        $settings['vat_number']             = isset($company_settings['vat_number']) ? $company_settings['vat_number'] : '';
        $settings['quote_footer_title']     = isset($company_settings['quote_footer_title']) ? $company_settings['quote_footer_title'] : '';
        $settings['quote_footer_notes']     = isset($company_settings['quote_footer_notes']) ? $company_settings['quote_footer_notes'] : '';
        $settings['quote_shipping_display'] = isset($company_settings['quote_shipping_display']) ? $company_settings['quote_shipping_display'] : '';
        $settings['quote_template']         = isset($company_settings['quote_template']) ? $company_settings['quote_template'] : '';
        $settings['quote_color']            = isset($company_settings['quote_color']) ? $company_settings['quote_color'] : '';
        $settings['quote_qr_display']       = isset($company_settings['quote_qr_display']) ? $company_settings['quote_qr_display'] : '';

        if(module_is_active('CustomField')){
            $quote->customField = \Workdo\CustomField\Entities\CustomField::getData($quote, 'Sales','Quotes');
            $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace($quote->created_by))->where('module', '=', 'Sales')->where('sub_module','Quotes')->get();
        }else{
            $customFields = null;
        }

        if($quote)
        {
            $color      = '#' . $settings['quote_color'];
            $font_color = SalesUtility::getFontColor($color);

            return view('sales::quote.templates.' . company_setting('quote_template') , compact('quote', 'user', 'color', 'settings', 'img', 'font_color','customFields'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function fileExport()
    {
        $name = 'quote_' . date('Y-m-d i:h:s');
        $data = Excel::download(new QuoteExport(), $name . '.xlsx');  ob_end_clean();


        return $data;
    }

    public function grid()
    {
        if(\Auth::user()->isAbleTo('quote manage'))
        {
            $quotes = Quote::where('created_by', creatorId())->where('workspace',getActiveWorkSpace());
            $quotes = $quotes->paginate(11);

            return view('sales::quote.grid', compact('quotes'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
