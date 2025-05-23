<?php

namespace Workdo\Sales\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use App\Models\EmailTemplate;
use Workdo\Sales\Entities\SalesOrder;
use Workdo\Sales\Entities\SalesUtility;
use Workdo\Sales\Entities\SalesAccount;
use Workdo\Sales\Entities\Opportunities;
use Workdo\Sales\Entities\Quote;
use Workdo\Sales\Entities\Contact;
use Workdo\Sales\Entities\ShippingProvider;
use Workdo\Sales\Entities\Stream;
use Workdo\Sales\Entities\SalesOrderItem;
use Workdo\Sales\Entities\SalesInvoice;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cookie;
use Workdo\Sales\Events\CreateSalesOrder;
use Workdo\Sales\Events\DestroySalesOrder;
use Workdo\Sales\Events\SalesOrderDuplicate;
use Workdo\Sales\Events\UpdateSalesOrder;
use App\Models\Setting;
use Workdo\Sales\DataTables\SalesOrderDataTable;

class SalesOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(SalesOrderDataTable $dataTable)
    {
        if(\Auth::user()->isAbleTo('salesorder manage'))
        {
            return $dataTable->render('sales::salesorder.index');
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
        if(\Auth::user()->isAbleTo('salesorder create'))
        {
            $user = User::where('workspace_id',getActiveWorkSpace())->emp()->get()->pluck('name', 'id');
            if(module_is_active('ProductService')){
                $tax = \Workdo\ProductService\Entities\Tax::where('created_by',creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('name', 'id');
                $tax_count =$tax->count();
                $tax->prepend('No Tax', 0);
            }else{
                $tax=[0 => 'No Tax'];
                $tax_count =$tax;
            }

            $account = SalesAccount::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $account->prepend('--', '');
            $opportunities = Opportunities::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $quote = Quote::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $quote->prepend('--', 0);
            $status  = SalesOrder::$status;
            $contact = Contact::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $contact->prepend('--', '0');
            $shipping_provider = ShippingProvider::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');

            if(module_is_active('CustomField')){
                $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id',getActiveWorkSpace())->where('module', '=', 'sales')->where('sub_module','sales order')->get();
            }else{
                $customFields = null;
            }

            return view('sales::salesorder.create', compact('user', 'tax', 'account', 'opportunities', 'status', 'contact', 'shipping_provider', 'quote', 'type', 'id','customFields','tax_count'));
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
        if(\Auth::user()->isAbleTo('salesorder create'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name'                   =>  'required|string|max:120',
                                   'opportunity'            =>  'required',
                                   'quote_number'           =>  'required',
                                   'billing_address'        =>  'required',
                                   'shipping_address'       =>  'required',
                                   'billing_city'           =>  'required',
                                   'billing_state'          =>  'required',
                                   'shipping_city'          =>  'required',
                                   'shipping_state'         =>  'required',
                                   'billing_country'        =>  'required',
                                   'shipping_country'       =>  'required',
                                   'shipping_postalcode'    =>  'required',
                                   'billing_postalcode'     =>  'required',
                                   'tax'                    =>  'required',
                                   'date_quoted'            =>  'required',
                                   'account_id'             =>  'required',

                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            if(count($request->tax) > 1 && in_array(0, $request->tax))
            {
                return redirect()->back()->with('error', 'Please select valid tax');
            }
            $salesorder                        = new SalesOrder();
            $salesorder['user_id']             = $request->user;
            $salesorder['salesorder_id']       = $this->salesorderNumber();
            $salesorder['name']                = $request->name;
            $salesorder['quote']               = $request->quote;
            $salesorder['opportunity']         = $request->opportunity;
            $salesorder['status']              = $request->status;
            $salesorder['account']             = $request->account_id;
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
            $salesorder['billing_contact']     = $request->billing_contact;
            $salesorder['shipping_contact']    = $request->shipping_contact;
            // $salesorder['tax']                 = implode(',', $request->tax);
            $salesorder['shipping_provider']   = $request->shipping_provider;
            $salesorder['description']         = $request->description;
            $salesorder['workspace']           = getActiveWorkSpace();
            $salesorder['created_by']          = creatorId();
            $salesorder->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'salesorder',
                            'stream_comment' => '',
                            'user_name' => $salesorder->name,
                        ]
                    ),
                ]
            );

            if(module_is_active('CustomField'))
            {
                \Workdo\CustomField\Entities\CustomField::saveData($salesorder, $request->customField);
            }

            if(!empty(company_setting('New Sales Order')) && company_setting('New Sales Order')  == true)
            {
                $Assign_user_phone = User::where('id',$request->user)->first();

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

            event(new CreateSalesOrder($request,$salesorder));

            return redirect()->back()->with('success', __('The sales order has been created successfully.'));
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
    public function show(SalesOrder $salesOrder, $id)
    {
        if(\Auth::user()->isAbleTo('salesorder show'))
        {

            $salesOrder = SalesOrder::find($id);
            $items         = [];
            $totalTaxPrice = 0;
            $totalQuantity = 0;
            $totalRate     = 0;
            $totalDiscount = 0;
            $taxesData     = [];
            foreach($salesOrder->itemsdata as $item)
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


            $salesOrder->items         = $items;
            $salesOrder->totalTaxPrice = $totalTaxPrice;
            $salesOrder->totalQuantity = $totalQuantity;
            $salesOrder->totalRate     = $totalRate;
            $salesOrder->totalDiscount = $totalDiscount;
            $salesOrder->taxesData     = $taxesData;
            $company_settings = getCompanyAllSetting();

            $company_setting['company_name']                = isset($company_settings['company_name']) ? $company_settings['company_name'] : '';
            $company_setting['company_email']               = isset($company_settings['company_email']) ? $company_settings['company_email'] : '';
            $company_setting['company_telephone']           = isset($company_settings['company_telephone']) ? $company_settings['company_telephone'] : '';
            $company_setting['company_address']             = isset($company_settings['company_address']) ? $company_settings['company_address'] : '';
            $company_setting['company_city']                = isset($company_settings['company_city']) ? $company_settings['company_city'] : '';
            $company_setting['company_state']               = isset($company_settings['company_state']) ? $company_settings['company_state'] : '';
            $company_setting['company_zipcode']             = isset($company_settings['company_zipcode']) ? $company_settings['company_zipcode'] : '';
            $company_setting['company_country']             = isset($company_settings['company_country']) ? $company_settings['company_country'] : '';
            $company_setting['registration_number']         = isset($company_settings['registration_number']) ? $company_settings['registration_number'] : '';
            $company_setting['tax_type']                    = isset($company_settings['tax_type']) ? $company_settings['tax_type'] : '';
            $company_setting['vat_number']                  = isset($company_settings['vat_number']) ? $company_settings['vat_number'] : '';
            $company_setting['salesorder_footer_title']     = isset($company_settings['salesorder_footer_title']) ? $company_settings['salesorder_footer_title'] : '';
            $company_setting['salesorder_footer_notes']     = isset($company_settings['salesorder_footer_notes']) ? $company_settings['salesorder_footer_notes'] : '';
            $company_setting['salesorder_shipping_display'] = isset($company_settings['salesorder_shipping_display']) ? $company_settings['salesorder_shipping_display'] : '';
            $company_setting['salesorder_qr_display']       = isset($company_settings['salesorder_qr_display']) ? $company_settings['salesorder_qr_display'] : '';
            if(module_is_active('CustomField')){
                $salesOrder->customField = \Workdo\CustomField\Entities\CustomField::getData($salesOrder, 'Sales','Sales Order');
                $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'Sales')->where('sub_module','Sales Order')->get();
            }else{
                $customFields = null;
            }
            return view('sales::salesorder.view', compact('salesOrder', 'company_setting','customFields'));
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
    public function edit(SalesOrder $salesOrder,$id)
    {
        if(\Auth::user()->isAbleTo('salesorder edit'))
        {
            $salesOrder  = SalesOrder::find($id);
            if($salesOrder){

                $opportunity = Opportunities::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
                $opportunity->prepend('--', '');
                $quote = Quote::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
                $quote->prepend('--', '');
                $account = SalesAccount::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
                $account->prepend('--', '');
                $billing_contact = Contact::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
                $billing_contact->prepend('--', '');
                if(module_is_active('ProductService')){
                    $tax = \Workdo\ProductService\Entities\Tax::where('created_by',creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('name', 'id');
                    $tax->prepend('No Tax', 0);
                }
                else{
                    $tax=[0 => 'No Tax'];
                }
                $shipping_provider = ShippingProvider::where('created_by',creatorId())->get()->pluck('name', 'id');
                $user              = User::where('workspace_id',getActiveWorkSpace())->emp()->get()->pluck('name', 'id');
                $user->prepend('--', 0);
                $invoices = SalesInvoice::where('salesorder', $salesOrder->id)->where('workspace',getActiveWorkSpace())->get();
                $status   = SalesOrder::$status;



                if(module_is_active('CustomField')){
                    $salesOrder->customField = \Workdo\CustomField\Entities\CustomField::getData($salesOrder, 'sales','sales order');
                    $customFields             = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'sales')->where('sub_module','sales order')->get();
                }else{
                    $customFields = null;
                }

                return view('sales::salesorder.edit', compact('salesOrder', 'quote', 'opportunity', 'status', 'account', 'billing_contact', 'tax', 'shipping_provider', 'user', 'invoices','customFields'));
            }
            else
            {
                return redirect()->back()->with('error', __('The sales order not found.'));
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
    public function update(Request $request, $id)
    {
        if(\Auth::user()->isAbleTo('salesorder edit'))
        {
            $salesOrder = SalesOrder::find($id);

            $validator = \Validator::make(
                $request->all(), [
                                    'name'                   =>  'required|string|max:120',
                                    'opportunity'            =>  'required',
                                    'quote_number'           =>  'required',
                                    'billing_address'        =>  'required',
                                    'shipping_address'       =>  'required',
                                    'billing_city'           =>  'required',
                                    'billing_state'          =>  'required',
                                    'shipping_city'          =>  'required',
                                    'shipping_state'         =>  'required',
                                    'billing_country'        =>  'required',
                                    'shipping_country'       =>  'required',
                                    'shipping_postalcode'    =>  'required',
                                    'billing_postalcode'     =>  'required',
                                    'tax'                    =>  'required',
                                    'date_quoted'            =>  'required',
                                    'account'                =>  'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            if(count($request->tax) > 1 && in_array(0, $request->tax))
            {
                return redirect()->back()->with('error', 'Please select valid tax');
            }


            $salesOrder['user_id']             = $request->user;
            $salesOrder['salesorder_id']       = $this->salesorderNumber();
            $salesOrder['name']                = $request->name;
            $salesOrder['quote']               = $request->quote;
            $salesOrder['opportunity']         = $request->opportunity;
            $salesOrder['status']              = $request->status;
            $salesOrder['account']             = $request->account;
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
            $salesOrder['billing_contact']     = $request->billing_contact;
            $salesOrder['shipping_contact']    = $request->shipping_contact;
            // $salesOrder['tax']                 = implode(',', $request->tax);
            $salesOrder['shipping_provider']   = $request->shipping_provider;
            $salesOrder['description']         = $request->description;
            $salesorder['workspace']           = getActiveWorkSpace();
            $salesOrder['created_by']          = creatorId();
            $salesOrder->save();

            Stream::create(
                [
                    'user_id' => \Auth::user()->id,
                    'created_by' => creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => \Auth::user()->username,
                            'title' => 'salesOrder',
                            'stream_comment' => '',
                            'user_name' => $salesOrder->name,
                        ]
                    ),
                ]
            );


            if(module_is_active('CustomField'))
            {
                \Workdo\CustomField\Entities\CustomField::saveData($salesOrder, $request->customField);
            }
            event(new UpdateSalesOrder($request,$salesOrder));

            return redirect()->back()->with('success', __('The sales order details are updated successfully.'));
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
        if(\Auth::user()->isAbleTo('salesorder delete'))
        {

            $salesOrder = SalesOrder::find($id);
            if(module_is_active('CustomField'))
            {
                $customFields = \Workdo\CustomField\Entities\CustomField::where('module','sales')->where('sub_module','sales order')->get();
                foreach($customFields as $customField)
                {
                    $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $salesOrder->id)->where('field_id',$customField->id)->first();
                    if(!empty($value)){
                        $value->delete();
                    }
                }
            }

            SalesOrderItem::where('salesorder_id',$id)->where('created_by',$salesOrder->created_by)->delete();

            event(new DestroySalesOrder($salesOrder));
            $salesOrder->delete();

            return redirect()->back()->with('success', __('The sales order has been deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
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

    public function duplicate($id)
    {
        if(\Auth::user()->isAbleTo('salesorder create'))
        {
            $salesorder = SalesOrder::find($id);

            $duplicate                        = new SalesOrder();
            $duplicate['user_id']             = $salesorder->user_id;
            $duplicate['salesorder_id']       = $this->salesorderNumber();
            $duplicate['name']                = $salesorder->name;
            $duplicate['quote']               = $salesorder->quote;
            $duplicate['opportunity']         = $salesorder->opportunity;
            $duplicate['status']              = $salesorder->status;
            $duplicate['account']             = $salesorder->account;
            $duplicate['amount']              = $salesorder->amount;
            $duplicate['date_quoted']         = $salesorder->date_quoted;
            $duplicate['quote_number']        = $salesorder->quote_number;
            $duplicate['billing_address']     = $salesorder->billing_address;
            $duplicate['billing_city']        = $salesorder->billing_city;
            $duplicate['billing_state']       = $salesorder->billing_state;
            $duplicate['billing_country']     = $salesorder->billing_country;
            $duplicate['billing_postalcode']  = $salesorder->billing_postalcode;
            $duplicate['shipping_address']    = $salesorder->shipping_address;
            $duplicate['shipping_city']       = $salesorder->shipping_city;
            $duplicate['shipping_state']      = $salesorder->shipping_state;
            $duplicate['shipping_country']    = $salesorder->shipping_country;
            $duplicate['shipping_postalcode'] = $salesorder->shipping_postalcode;
            $duplicate['billing_contact']     = $salesorder->billing_contact;
            $duplicate['shipping_contact']    = $salesorder->shipping_contact;
            // $duplicate['tax']                 = $salesorder->tax;
            $duplicate['shipping_provider']   = $salesorder->shipping_provider;
            $duplicate['description']         = $salesorder->description;
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
                            'title' => 'salesorder',
                            'stream_comment' => '',
                            'user_name' => $salesorder->name,
                        ]
                    ),
                ]
            );

            if($duplicate)
            {
                $salesorderItem = SalesOrderItem::where('salesorder_id', $salesorder->id)->get();

                foreach($salesorderItem as $item)
                {

                    $salesorderitem                  = new SalesOrderItem();
                    $salesorderitem['salesorder_id'] = $duplicate->id;
                    $salesorderitem['item']          = $item->item;
                    $salesorderitem['quantity']      = $item->quantity;
                    $salesorderitem['price']         = $item->price;
                    $salesorderitem['discount']      = $item->discount;
                    $salesorderitem['tax']           = $item->tax;
                    $salesorderitem['description']   = $item->description;
                    $salesorderitem['created_by']    = creatorId();
                    $salesorderitem->save();
                }
            }
            event(new SalesOrderDuplicate($duplicate,$salesorderItem));

            return redirect()->back()->with('success', __('The sales order has been duplicated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function grid()
    {
        if(\Auth::user()->isAbleTo('salesorder manage'))
        {
            $salesorders = SalesOrder::where('created_by', creatorId())->where('workspace',getActiveWorkSpace());
            $salesorders = $salesorders->paginate(11);
            return view('sales::salesorder.grid', compact('salesorders'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public static function salesorderNumber()
    {
        $latest = SalesOrder::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->latest()->first();

        if(!$latest)
        {
            return 1;
        }

        return $latest->salesorder_id + 1;
    }

    public function saveSalesorderTemplateSettings(Request $request)
    {
        $user = \Auth::user();
        $post = $request->all();
        unset($post['_token']);
        if(isset($post['salesorder_template']) && (!isset($post['salesorder_color']) || empty($post['salesorder_color'])))
        {
            $post['salesorder_color'] = "ffffff";
        }
        if (!isset($post['salesorder_qr_display'])) {
            $post['salesorder_qr_display'] = 'off';
        }
        if($request->salesorder_logo)
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'salesorder_logo' => 'image|mimes:png',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            if($request->hasFile('salesorder_logo'))
            {
                $salesorder_logo         = $user->id.'_salesorder_logo'.time().'.png';

                $uplaod = upload_file($request,'salesorder_logo',$salesorder_logo,'salesorder_logo');
                if($uplaod['flag'] == 1)
                {
                    $url = $uplaod['url'];
                    $company_settings = getCompanyAllSetting();

                    $old_salesorder_logo = isset($company_settings['salesorder_logo']) ? $company_settings['salesorder_logo'] : '';
                    if(!empty($old_salesorder_logo) && check_file($old_salesorder_logo))
                    {
                        delete_file($old_salesorder_logo);
                    }
                }
                else
                {
                    return redirect()->back()->with('error',$uplaod['msg']);
                }
            }
        }
        if(isset($post['salesorder_logo']))
        {
            $post['salesorder_logo'] = $url;
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

        return redirect()->back()->with('success', __('The sales order setting are updated successfully.'));
    }

    public function previewSalesorder($template, $color)
    {
        $objUser    = \Auth::user();
        $salesorder = new SalesOrder();

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

        $salesorder->invoice_id = 1;
        $salesorder->issue_date = date('Y-m-d H:i:s');
        $salesorder->due_date   = date('Y-m-d H:i:s');
        $salesorder->items      = $items;

        $salesorder->totalTaxPrice = 60;
        $salesorder->totalQuantity = 3;
        $salesorder->totalRate     = 300;
        $salesorder->totalDiscount = 10;
        $salesorder->taxesData     = $taxesData;

        $preview    = 1;
        $color      = '#' . $color;
        $font_color = SalesUtility::getFontColor($color);

        $dark_logo = get_file(sidebar_logo());
        $company_settings = getCompanyAllSetting();

        $salesorder_logo = isset($company_settings['salesorder_logo']) ? $company_settings['salesorder_logo'] : '';
        if(isset($salesorder_logo) && !empty($salesorder_logo))
        {
            $img = get_file($salesorder_logo);
        }
        else
        {
            $img = $dark_logo;
        }
        $settings['site_rtl']                    = isset($company_settings['site_rtl']) ? $company_settings['site_rtl'] : '';
        $settings['company_name']                = isset($company_settings['company_name']) ? $company_settings['company_name'] : '';
        $settings['company_email']               = isset($company_settings['company_email']) ? $company_settings['company_email'] : '';
        $settings['company_telephone']           = isset($company_settings['company_telephone']) ? $company_settings['company_telephone'] : '';
        $settings['company_address']             = isset($company_settings['company_address']) ? $company_settings['company_address'] : '';
        $settings['company_city']                = isset($company_settings['company_city']) ? $company_settings['company_city'] : '';
        $settings['company_state']               = isset($company_settings['company_state']) ? $company_settings['company_state'] : '';
        $settings['company_zipcode']             = isset($company_settings['company_zipcode']) ? $company_settings['company_zipcode'] : '';
        $settings['company_country']             = isset($company_settings['company_country']) ? $company_settings['company_country'] : '';
        $settings['registration_number']         = isset($company_settings['registration_number']) ? $company_settings['registration_number'] : '';
        $settings['tax_type']                    = isset($company_settings['tax_type']) ? $company_settings['tax_type'] : '';
        $settings['vat_number']                  = isset($company_settings['vat_number']) ? $company_settings['vat_number'] : '';
        $settings['salesorder_footer_title']     = isset($company_settings['salesorder_footer_title']) ? $company_settings['salesorder_footer_title'] : '';
        $settings['salesorder_footer_notes']     = isset($company_settings['salesorder_footer_notes']) ? $company_settings['salesorder_footer_notes'] : '';
        $settings['salesorder_shipping_display'] = isset($company_settings['salesorder_shipping_display']) ? $company_settings['salesorder_shipping_display'] : '';
        $settings['salesorder_template']         = isset($company_settings['salesorder_template']) ? $company_settings['salesorder_template'] : '';
        $settings['salesorder_color']            = isset($company_settings['salesorder_color']) ? $company_settings['salesorder_color'] : '';
        $settings['salesorder_qr_display']       = isset($company_settings['salesorder_qr_display']) ? $company_settings['salesorder_qr_display'] : '';

        return view('sales::salesorder.templates.' . $template, compact('salesorder', 'preview', 'color', 'img', 'settings', 'user', 'font_color'));
    }

    public function paysalesorder($salesorder_id){

        if(!empty($salesorder_id)){
            try {
                $id = \Illuminate\Support\Facades\Crypt::decrypt($salesorder_id);
            } catch (\Throwable $th) {
                return redirect('login');
            }

            $salesorder = SalesOrder::where('id',$id)->first();

            if(!is_null($salesorder)){

                // $settings = Utility::settings();

                $items         = [];
                $totalTaxPrice = 0;
                $totalQuantity = 0;
                $totalRate     = 0;
                $totalDiscount = 0;
                $taxesData     = [];

                foreach($salesorder->itemsdata as $item)
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
                            $itemTax['tax']      = $tax->name . '%';
                            $itemTax['price']    = company_date_formate($taxPrice,$salesorder->created_by,$salesorder->workspace);
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
                            $itemTax['price']    = company_date_formate($taxPrice,$salesorder->created_by,$salesorder->workspace);
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
                    $item->itemTax = $itemTaxes;
                    $items[]       = $item;

                }

                $salesorder->items         = $items;
                $salesorder->totalTaxPrice = $totalTaxPrice;
                $salesorder->totalQuantity = $totalQuantity;
                $salesorder->totalRate     = $totalRate;
                $salesorder->totalDiscount = $totalDiscount;
                $salesorder->taxesData     = $taxesData;
                $company_settings = getCompanyAllSetting($salesorder->created_by,$salesorder->workspace);

                $ownerId = SalesUtility::ownerIdforSalesorder($salesorder->created_by,$salesorder->created_by);

                $company_setting['company_name']                = isset($company_settings['company_name']) ? $company_settings['company_name'] : '';
                $company_setting['company_email']               = isset($company_settings['company_email']) ? $company_settings['company_email'] : '';
                $company_setting['company_telephone']           = isset($company_settings['company_telephone']) ? $company_settings['company_telephone'] : '';
                $company_setting['company_address']             = isset($company_settings['company_address']) ? $company_settings['company_address'] : '';
                $company_setting['company_city']                = isset($company_settings['company_city']) ? $company_settings['company_city'] : '';
                $company_setting['company_state']               = isset($company_settings['company_state']) ? $company_settings['company_state'] : '';
                $company_setting['company_zipcode']             = isset($company_settings['company_zipcode']) ? $company_settings['company_zipcode'] : '';
                $company_setting['company_country']             = isset($company_settings['company_country']) ? $company_settings['company_country'] : '';
                $company_setting['registration_number']         = isset($company_settings['registration_number']) ? $company_settings['registration_number'] : '';
                $company_setting['tax_type']                    = isset($company_settings['tax_type']) ? $company_settings['tax_type'] : '';
                $company_setting['vat_number']                  = isset($company_settings['vat_number']) ? $company_settings['vat_number'] : '';
                $company_setting['salesorder_footer_title']     = isset($company_settings['salesorder_footer_title']) ? $company_settings['salesorder_footer_title'] : '';
                $company_setting['salesorder_footer_notes']     = isset($company_settings['salesorder_footer_notes']) ? $company_settings['salesorder_footer_notes'] : '';
                $company_setting['salesorder_shipping_display'] = isset($company_settings['salesorder_shipping_display']) ? $company_settings['salesorder_shipping_display'] : '';
                $company_setting['salesorder_qr_display']       = isset($company_settings['salesorder_qr_display']) ? $company_settings['salesorder_qr_display'] : '';

                $users = User::where('id',$salesorder->created_by)->first();

                if(!is_null($users)){
                    \App::setLocale($users->lang);
                }else{
                    $users = User::where('type','owner')->first();
                    \App::setLocale($users->lang);
                }
                $company_id=$salesorder->created_by;
                $workspace = $salesorder->workspace;
                if(module_is_active('CustomField')){
                    $salesorder->customField = \Workdo\CustomField\Entities\CustomField::getData($salesorder, 'Sales','Sales Order');
                    $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace($salesorder->created_by))->where('module', '=', 'Sales')->where('sub_module','Sales Order')->get();
                }else{
                    $customFields = null;
                }
                return view('sales::salesorder.salesorderpay',compact('salesorder', 'company_setting','users','company_id','workspace','customFields'));
            }else{
                return abort('404', __('The link you followed has expired.'));
            }
        }else{
            return abort('404', __('The link you followed has expired.'));
        }
    }

    public function pdf($id)
    {
        $salesorderId = Crypt::decrypt($id);
        $salesorder   = SalesOrder::where('id', $salesorderId)->first();

        $data  = \DB::table('settings');
        $data  = $data->where('id', '=', $salesorder->created_by);
        $data1 = $data->get();

        $user         = new User();
        $user->name   = $salesorder->name;
        $user->email  = $salesorder->contacts->email ?? '';
        $user->mobile = $salesorder->contacts->phone ?? '';

        $user->bill_address = $salesorder->billing_address;
        $user->bill_zip     = $salesorder->billing_postalcode;
        $user->bill_city    = $salesorder->billing_city;
        $user->bill_country = $salesorder->billing_country;
        $user->bill_state   = $salesorder->billing_state;

        $user->address = $salesorder->shipping_address;
        $user->zip     = $salesorder->shipping_postalcode;
        $user->city    = $salesorder->shipping_city;
        $user->country = $salesorder->shipping_country;
        $user->state   = $salesorder->shipping_state;


        $items         = [];
        $totalTaxPrice = 0;
        $totalQuantity = 0;
        $totalRate     = 0;
        $totalDiscount = 0;
        $taxesData     = [];
        foreach($salesorder->itemsdata as $product)
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

            $taxes = SalesUtility::tax($product->tax);
            $itemTaxes = [];
            if (!empty($item->tax))
            {
                foreach($taxes as $tax)
                {
                    $taxPrice      = SalesUtility::taxRate($tax->rate, $item->price, $item->quantity,$item->discount);
                    $totalTaxPrice += $taxPrice;

                    $itemTax['name']  = $tax->name;
                    $itemTax['rate']  = $tax->rate . '%';
                    $itemTax['price'] = currency_format_with_sym($taxPrice,$salesorder->created_by,$salesorder->workspace);
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
        $salesorder->issue_date    =$salesorder->date_quoted;
        $salesorder->items         = $items;
        $salesorder->totalTaxPrice = $totalTaxPrice;
        $salesorder->totalQuantity = $totalQuantity;
        $salesorder->totalRate     = $totalRate;
        $salesorder->totalDiscount = $totalDiscount;
        $salesorder->taxesData     = $taxesData;

        //Set your logo
        $dark_logo    = get_file('company_logo_dark',$salesorder->created_by,$salesorder->workspace);
        $salesorder_logo = get_file(sidebar_logo());
        if(isset($salesorder_logo) && !empty($salesorder_logo))
        {
            $img = get_file($salesorder_logo);
        }
        else
        {
            $img = $dark_logo;
        }
        $company_settings = getCompanyAllSetting($salesorder->created_by,$salesorder->workspace);

        $settings['site_rtl']                    = isset($company_settings['site_rtl']) ? $company_settings['site_rtl'] : '';
        $settings['company_name']                = isset($company_settings['company_name']) ? $company_settings['company_name'] : '';
        $settings['company_email']               = isset($company_settings['company_email']) ? $company_settings['company_email'] : '';
        $settings['company_telephone']           = isset($company_settings['company_telephone']) ? $company_settings['company_telephone'] : '';
        $settings['company_address']             = isset($company_settings['company_address']) ? $company_settings['company_address'] : '';
        $settings['company_city']                = isset($company_settings['company_city']) ? $company_settings['company_city'] : '';
        $settings['company_state']               = isset($company_settings['company_state']) ? $company_settings['company_state'] : '';
        $settings['company_zipcode']             = isset($company_settings['company_zipcode']) ? $company_settings['company_zipcode'] : '';
        $settings['company_country']             = isset($company_settings['company_country']) ? $company_settings['company_country'] : '';
        $settings['registration_number']         = isset($company_settings['registration_number']) ? $company_settings['registration_number'] : '';
        $settings['tax_type']                    = isset($company_settings['tax_type']) ? $company_settings['tax_type'] : '';
        $settings['vat_number']                  = isset($company_settings['vat_number']) ? $company_settings['vat_number'] : '';
        $settings['salesorder_footer_title']     = isset($company_settings['salesorder_footer_title']) ? $company_settings['salesorder_footer_title'] : '';
        $settings['salesorder_footer_notes']     = isset($company_settings['salesorder_footer_notes']) ? $company_settings['salesorder_footer_notes'] : '';
        $settings['salesorder_shipping_display'] = isset($company_settings['salesorder_shipping_display']) ? $company_settings['salesorder_shipping_display'] : '';
        $settings['quote_template']              = isset($company_settings['quote_template']) ? $company_settings['quote_template'] : '';
        $settings['quote_color']                 = isset($company_settings['quote_color']) ? $company_settings['quote_color'] : '';
        $settings['salesorder_qr_display']       = isset($company_settings['salesorder_qr_display']) ? $company_settings['salesorder_qr_display'] : '';

        if(module_is_active('CustomField')){
            $salesorder->customField = \Workdo\CustomField\Entities\CustomField::getData($salesorder, 'Sales','Sales Order');
            $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace($salesorder->created_by))->where('module', '=', 'Sales')->where('sub_module','Sales Order')->get();
        }else{
            $customFields = null;
        }
        if($salesorder)
        {

            $color = isset($company_settings['salesorder_color']) ? $company_settings['salesorder_color'] : '';
            if($color){
                $color=$color;
            }else{
                $color='ffffff';
            }
            $color      = '#' .$color ;
            $font_color   = SalesUtility::getFontColor($color);

            return view('sales::salesorder.templates.' . company_setting('salesorder_template') , compact('salesorder', 'user', 'color', 'settings', 'img', 'font_color','customFields'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function salesorderitem($id)
    {
        $salesorder = SalesOrder::find($id);

        $items = \Workdo\ProductService\Entities\ProductService::where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('name', 'id');
        $items->prepend('select', '');
        $tax_rate = \Workdo\ProductService\Entities\Tax::where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('rate', 'id');
        return view('sales::salesorder.salesorderitem', compact('items', 'salesorder', 'tax_rate'));
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
        $salesorderitem                  = new SalesOrderItem();
        $salesorderitem['salesorder_id'] = $request->id;
        $salesorderitem['item']          = $request->item;
        $salesorderitem['quantity']      = $request->quantity;
        $salesorderitem['price']         = $request->price;
        $salesorderitem['discount']      = $request->discount;
        $salesorderitem['tax']           = $request->tax;
        $salesorderitem['description']   = $request->description;
        $salesorderitem['workspace']     = getActiveWorkSpace();
        $salesorderitem['created_by']    = creatorId();
        $salesorderitem->save();
        return redirect()->back()->with('success', __('The sales order item has been created successfully.'));

    }

    public function items(Request $request)
    {

        $items = \Workdo\ProductService\Entities\ProductService::where('id', $request->item_id)->first();

        $items->taxes = $items->tax($items->tax_id);
        return json_encode($items);
    }

    public function salesorderItemEdit($id)
    {
        $salesorderItem = SalesOrderItem::find($id);

        $items = \Workdo\ProductService\Entities\ProductService::where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('name', 'id');
        $items->prepend('select', '');
        $tax_rate = \Workdo\ProductService\Entities\Tax::where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('rate', 'id');

        return view('sales::salesorder.salesorderitemEdit', compact('items', 'salesorderItem', 'tax_rate'));
    }
    public function salesorderItemUpdate(Request $request, $id)
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
        $salesorderitem                = SalesOrderItem::find($id);
        $salesorderitem['item']        = $request->item;
        $salesorderitem['quantity']    = $request->quantity;
        $salesorderitem['price']       = $request->price;
        $salesorderitem['discount']    = $request->discount;
        $salesorderitem['tax']         = $request->tax;
        $salesorderitem['description'] = $request->description;
        $salesorderitem->save();

        return redirect()->back()->with('success', __('The sales order item are updated successfully.'));

    }

    public function itemsDestroy($id)
    {
        $item = SalesOrderItem::find($id);
        $item->delete();

        return redirect()->back()->with('success', __('The sales order item has been deleted.'));
    }
}
