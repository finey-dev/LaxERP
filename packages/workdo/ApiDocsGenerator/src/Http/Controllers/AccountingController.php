<?php

namespace Workdo\ApiDocsGenerator\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Workdo\Account\Entities\AccountUtility;
use Workdo\Account\Entities\BankAccount;
use Workdo\Account\Entities\Bill;
use Workdo\Account\Entities\BillAccount;
use Workdo\Account\Entities\BillPayment;
use Workdo\Account\Entities\BillProduct;
use Workdo\Account\Entities\ChartOfAccount;
use Workdo\Account\Entities\ChartOfAccountSubType;
use Workdo\Account\Entities\ChartOfAccountType;
use Workdo\Account\Entities\Customer;
use Workdo\Account\Entities\StockReport;
use Workdo\Account\Entities\CustomerCreditNotes;
use Workdo\Account\Entities\Payment;
use Workdo\Account\Entities\Revenue;
use Workdo\Account\Entities\Transaction;
use Workdo\Account\Entities\Transfer;
use Workdo\Account\Entities\Vender;
use Workdo\Account\Entities\CustomerDebitNotes;
use Workdo\Account\Events\CreateBankAccount;
use Workdo\Account\Events\CreateBankTransfer;
use Workdo\Account\Events\CreateChartAccount;
use Workdo\Account\Events\CreateCustomer;
use Workdo\Account\Events\CreateRevenue;
use Workdo\Account\Events\CreateVendor;
use Workdo\Account\Events\DestroyBankAccount;
use Workdo\Account\Events\DestroyBankTransfer;
use Workdo\Account\Events\DestroyChartAccount;
use Workdo\Account\Events\DestroyVendor;
use Workdo\Account\Events\DestroyCustomer;
use Workdo\Account\Events\UpdateCustomer;
use Workdo\Account\Events\UpdateVendor;
use Workdo\Account\Events\UpdateBankAccount;
use Workdo\Account\Events\UpdateBankTransfer;
use Workdo\Account\Events\UpdateChartAccount;
use Workdo\Account\Events\CreateBill;
use Workdo\Account\Events\CreatePayment;
use Workdo\Account\Events\DestroyBill;
use Workdo\ProductService\Entities\Tax;
use Workdo\Account\Events\UpdateBill;
use Workdo\Account\Events\DestroyRevenue;
use Workdo\Account\Events\UpdateRevenue;
use Workdo\Account\Events\UpdatePayment;
use Workdo\Account\Events\DestroyPayment;

class AccountingController extends Controller
{
    public function customers(Request $request){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }
        $customers = User::where('users.workspace_id',$request->workspace_id)->where('users.created_by', creatorId())
            ->leftjoin('customers', 'users.id', '=', 'customers.user_id')
            ->where('users.type', 'Client')
            ->select('users.*','customers.*', 'users.name as name', 'users.email as email', 'users.id as id','users.mobile_no as contact')
            ->get()
            ->map(function($customer){
                return [
                    'id'                => $customer->id,
                    'name'              => $customer->name,
                    'email'             => $customer->email,
                    'mobile_no'         => $customer->mobile_no,
                    'contact'           => $customer->contact,
                    'tax_number'        => $customer->tax_number,
                    'billing_name'      => $customer->billing_name,
                    'billing_country'   => $customer->billing_country,
                    'billing_state'     => $customer->billing_state,
                    'billing_city'      => $customer->billing_city,
                    'billing_phone'     => $customer->billing_phone,
                    'billing_zip'       => $customer->billing_zip,
                    'billing_address'   => $customer->billing_address,
                    'shipping_name'     => $customer->shipping_name,
                    'shipping_country'  => $customer->shipping_country,
                    'shipping_state'    => $customer->shipping_state,
                    'shipping_city'     => $customer->shipping_city,
                    'shipping_phone'    => $customer->shipping_phone,
                    'shipping_zip'      => $customer->shipping_zip,
                    'shipping_address'  => $customer->shipping_address,
                ];
            });

        return response()->json(['status'=>'success','data' => $customers]);
    }

    public function customerStore(Request $request){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $canUse =  PlanCheck('User', Auth::user()->id);
        if ($canUse == false) {
            return response()->json(['status'=>'error', 'message'=>'You have maxed out the total number of customer allowed on your current plan']);
        }

        $rules = [];
        $rules['name'] = 'required';
        $rules['contact'] = ['required','regex:/^([0-9\s\-\+\(\)]*)$/'];
        $rules['billing_name'] = 'required';
        $rules['billing_phone'] = 'required';
        $rules['billing_address'] = 'required';
        $rules['billing_city'] = 'required';
        $rules['billing_state'] = 'required';
        $rules['billing_country'] = 'required';
        $rules['billing_zip'] = 'required';

        if(empty($request->user_id))
        {

            $rules['email'] = ['required',
                Rule::unique('users')->where(function ($query) use ($request) {
                    return $query->where('created_by', creatorId())->where('workspace_id', $request->workspace_id);
                })
            ];
            $rules['password'] = 'required';
            $rules['contact'] = ['required','regex:/^([0-9\s\-\+\(\)]*)$/'];

        }


        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error','message'=>$messages->first()]);
        }

        $roles = Role::where('name','client')->where('created_by',creatorId())->first();
        if(empty($roles))
        {
            return response()->json(['status'=>'error','message' => __('Cilent Role Not found!')]);
        }

        if(!empty($request->user_id))
        {
            $user = User::where('workspace_id',$request->workspace_id)->where('created_by', creatorId())->where('id', $request->user_id);

            if(empty($user))
            {
                return response()->json(['status'=>'error', 'message' => __('Something went wrong please try again.')]);
            }
            if($user->name != $request->name)
            {
                $user->name = $request->name;
                $user->save();
            }
            if($user->mobile_no != $request->contact)
            {
                $user->mobile_no = $request->contact;
                $user->save();
            }
        }
        else
        {
            $user = User::create(
            [
                'name' => $request['name'],
                'email' => $request['email'],
                'mobile_no' => $request['contact'],
                'password' => Hash::make($request['password']),
                'email_verified_at' => date('Y-m-d h:i:s'),
                'type' => $roles->name,
                'lang' => 'en',
                'workspace_id' => $request->workspace_id,
                'active_workspace' =>getActiveWorkSpace(),
                'created_by' => creatorId(),
                ]
            );
            $user->save();
            $user->addRole($roles);
        }

        $customer                  = new Customer();
        $customer->user_id         = $user->id;
        $customer->customer_id     = $this->customerNumber($request->workspace_id);
        $customer->name            = !empty($request->name) ? $request->name : null;
        $customer->contact         = !empty($request->contact) ? $request->contact : null;
        $customer->email           = !empty($user->email) ? $user->email : null;
        $customer->tax_number      = !empty($request->tax_number) ? $request->tax_number : null;
        $customer->password        = null;
        $customer->billing_name    = !empty($request->billing_name) ? $request->billing_name : null;
        $customer->billing_country = !empty($request->billing_country) ? $request->billing_country : null;
        $customer->billing_state   = !empty($request->billing_state) ? $request->billing_state : null;
        $customer->billing_city    = !empty($request->billing_city) ? $request->billing_city : null;
        $customer->billing_phone   = !empty($request->billing_phone) ? $request->billing_phone : null;
        $customer->billing_zip     = !empty($request->billing_zip) ? $request->billing_zip : null;
        $customer->billing_address = !empty($request->billing_address) ? $request->billing_address : null;

        $customer->shipping_name    = !empty($request->shipping_name) ? $request->shipping_name : null;
        $customer->shipping_country = !empty($request->shipping_country) ? $request->shipping_country : null;
        $customer->shipping_state   = !empty($request->shipping_state) ? $request->shipping_state : null;
        $customer->shipping_city    = !empty($request->shipping_city) ? $request->shipping_city : null;
        $customer->shipping_phone   = !empty($request->shipping_phone) ? $request->shipping_phone : null;
        $customer->shipping_zip     = !empty($request->shipping_zip) ? $request->shipping_zip : null;
        $customer->shipping_address = !empty($request->shipping_address) ? $request->shipping_address : null;
        $customer->lang             = !empty($user->lang) ? $user->lang : '';

        $customer->workspace        = $request->workspace_id;
        $customer->created_by      = Auth::user()->id;

        $customer->save();

        event(new CreateCustomer($request,$customer));

        return response()->json(['status'=>'success', 'message' => 'Customer details successfully saved!']);

    }

    function customerNumber($workspace_id)
    {
        $latest = Customer::where('workspace',$workspace_id)->where('created_by', creatorId())->latest()->first();
        if (!$latest)
        {
            return 1;
        }

        return $latest->customer_id + 1;
    }

    public function customerDetail(Request $request,$id){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $customer = Customer::where('created_by',creatorId())->where('workspace', '=', $request->workspace_id)->where('user_id',$id)->first();
        if(!$customer){
            return response()->json(['status'=>'error','message'=>'Customer Not Found!']);
        }

        $customer = [
            'id'                => $customer->id,
            'name'              => $customer->name,
            'email'             => $customer->email,
            'contact'           => $customer->contact,
            'tax_number'        => $customer->tax_number,
            'billing_name'      => $customer->billing_name,
            'billing_country'   => $customer->billing_country,
            'billing_state'     => $customer->billing_state,
            'billing_city'      => $customer->billing_city,
            'billing_phone'     => $customer->billing_phone,
            'billing_zip'       => $customer->billing_zip,
            'billing_address'   => $customer->billing_address,
            'shipping_name'     => $customer->shipping_name,
            'shipping_country'  => $customer->shipping_country,
            'shipping_state'    => $customer->shipping_state,
            'shipping_city'     => $customer->shipping_city,
            'shipping_phone'    => $customer->shipping_phone,
            'shipping_zip'      => $customer->shipping_zip,
            'shipping_address'  => $customer->shipping_address,
        ];

        return response()->json(['status'=>'success','data'=>$customer]);
    }

    public function customerUpdate(Request $request , $id){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $rules = [
            'name' => 'required',
            'contact' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
            'billing_name' => 'required',
            'billing_phone' => 'required',
            'billing_address' => 'required',
            'billing_city' => 'required',
            'billing_state' => 'required',
            'billing_country' => 'required',
            'billing_zip' => 'required',
        ];

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=>$messages->first()]);
        }

        $customer                   = Customer::where('created_by',creatorId())->where('workspace',$request->workspace_id)->where('user_id',$id)->first();
        if(!$customer){
            return response()->json(['status'=>'error','message'=>'Customer Not Found!']);
        }

        $user = User::where('id',$customer->user_id)->first();
        if(!$user)
        {
            return response()->json(['status'=>'error', 'message'=>'User Not Found!']);
        }
        if($user->name != $request->name)
        {
            $user->name = $request->name;
            $user->save();
        }
        if($user->mobile_no != $request->contact)
        {
            $user->mobile_no = $request->contact;
            $user->save();
        }


        $customer->name             = $request->name;
        $customer->contact          = $request->contact;
        $customer->tax_number       = $request->tax_number;
        $customer->billing_name     = $request->billing_name;
        $customer->billing_country  = $request->billing_country;
        $customer->billing_state    = $request->billing_state;
        $customer->billing_city     = $request->billing_city;
        $customer->billing_phone    = $request->billing_phone;
        $customer->billing_zip      = $request->billing_zip;
        $customer->billing_address  = $request->billing_address;
        $customer->shipping_name    = $request->shipping_name;
        $customer->shipping_country = $request->shipping_country;
        $customer->shipping_state   = $request->shipping_state;
        $customer->shipping_city    = $request->shipping_city;
        $customer->shipping_phone   = $request->shipping_phone;
        $customer->shipping_zip     = $request->shipping_zip;
        $customer->shipping_address = $request->shipping_address;
        $customer->save();

        event(new UpdateCustomer($request,$customer));

        return response()->json(['status'=>'success', 'message' => 'Customer successfully updated.']);

    }

    public function customerDelete(Request $request,$id){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }
        $customer                   = Customer::where('created_by',creatorId())->where('workspace',$request->workspace_id)->where('user_id',$id)->first();
        if(!$customer){
            return response()->json(['status'=>'error','message'=>'Customer Not Found!']);
        }

        event(new DestroyCustomer($customer));
        $customer->delete();

        return response()->json(['status'=>'success', 'message' => 'Customer successfully Deleted!']);

    }

    public function vendors(Request $request){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $vendors = User::where('users.workspace_id',$request->workspace_id)
                        ->where('users.created_by', creatorId())
                        ->leftjoin('vendors', 'users.id', '=', 'vendors.user_id')
                        ->where('users.type', 'vendor')
                        ->select('users.*','vendors.*', 'users.name as name', 'users.email as email', 'users.id as id','users.mobile_no as contact')
                        ->get()
                        ->map(function($vendor){
                            return [
                                'id'                  => $vendor->id,
                                'name'                => $vendor->name,
                                'email'               => $vendor->email,
                                'mobile_no'           => $vendor->mobile_no,
                                'contact'             => $vendor->contact,
                                'tax_number'          => $vendor->tax_number,
                                'billing_name'        => $vendor->billing_name,
                                'billing_country'     => $vendor->billing_country,
                                'billing_state'       => $vendor->billing_state,
                                'billing_city'        => $vendor->billing_city,
                                'billing_phone'       => $vendor->billing_phone,
                                'billing_zip'         => $vendor->billing_zip,
                                'billing_address'     => $vendor->billing_address,
                                'shipping_name'       => $vendor->shipping_name,
                                'shipping_country'    => $vendor->shipping_country,
                                'shipping_state'      => $vendor->shipping_state,
                                'shipping_city'       => $vendor->shipping_city,
                                'shipping_phone'      => $vendor->shipping_phone,
                                'shipping_zip'        => $vendor->shipping_zip,
                                'shipping_address'    => $vendor->shipping_address,
                                'balance'             => $vendor->balance,
                                'debit_note_balance'  => $vendor->debit_note_balance,
                            ];
                        });

        return response()->json(['status'=>'success','data'=>$vendors]);
    }

    public function vendorStore(Request $request){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $rules = [];
        $rules['name'] = 'required';
        $rules['contact'] = ['required','regex:/^([0-9\s\-\+\(\)]*)$/'];
        $rules['billing_name'] = 'required';
        $rules['billing_phone'] = 'required';
        $rules['billing_address'] = 'required';
        $rules['billing_city'] = 'required';
        $rules['billing_state'] = 'required';
        $rules['billing_country'] = 'required';
        $rules['billing_zip'] = 'required';

        if(empty($request->user_id))
        {
            $rules['email']     = ['required','email','unique:users'];
            $rules['password'] = 'required';
            $rules['contact'] = ['required','regex:/^([0-9\s\-\+\(\)]*)$/'];
        }
        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error','message'=> $messages->first()]);
        }

        $roles = Role::where('name','vendor')->where('created_by',creatorId())->first();
        if(!$roles)
        {
            return response()->json(['status'=>'error','message'=> 'Vendor Role Not found !']);
        }

        $userpassword                = $request->input('password');
        $user['name']                = $request->input('name');
        $user['email']               = $request->input('email');
        $user['mobile_no']           = $request->input('contact');
        $user['password']            = \Hash::make($userpassword);
        $user['email_verified_at']   = date('Y-m-d h:i:s');
        $user['lang']                = 'en';
        $user['type']                = $roles->name;
        $user['created_by']          = \Auth::user()->id;
        $user['workspace_id']        = $request->workspace_id;
        $user['active_workspace']    = getActiveWorkSpace();
        $user = User::create($user);

        $user->addRole($roles);

        $vendor                   = new Vender();
        $vendor->vendor_id        = $this->vendorNumber($request->workspace_id);
        $vendor->user_id          = $user->id;
        $vendor->name             = $request->name;
        $vendor->contact          = $request->contact;
        $vendor->email            = $user->email;
        $vendor->tax_number       = $request->tax_number;
        $vendor->billing_name     = $request->billing_name;
        $vendor->billing_country  = $request->billing_country;
        $vendor->billing_state    = $request->billing_state;
        $vendor->billing_city     = $request->billing_city;
        $vendor->billing_phone    = $request->billing_phone;
        $vendor->billing_zip      = $request->billing_zip;
        $vendor->billing_address  = $request->billing_address;

        if(company_setting('bill_shipping_display')=='on')
        {
            $vendor->shipping_name    = $request->shipping_name;
            $vendor->shipping_country = $request->shipping_country;
            $vendor->shipping_state   = $request->shipping_state;
            $vendor->shipping_city    = $request->shipping_city;
            $vendor->shipping_phone   = $request->shipping_phone;
            $vendor->shipping_zip     = $request->shipping_zip;
            $vendor->shipping_address = $request->shipping_address;
        }
        $vendor->lang             = $user->lang;
        $vendor->created_by       = \Auth::user()->id;
        $vendor->workspace        = $request->workspace_id;
        $vendor->save();

        event(new CreateVendor($request,$vendor));

        return response()->json(['status'=>'success', 'message'=>'Vendor successfully created!']);

    }

    function vendorNumber($workspace_id)
    {
        $latest = Vender::where('workspace',$workspace_id)->where('created_by',creatorId())->latest()->first();
        if (!$latest)
        {
            return 1;
        }
        return $latest->vendor_id + 1;
    }

    public function vendorDetail(Request $request,$id){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $vendor = Vender::where('created_by',creatorId())->where('workspace',$request->workspace_id)->where('user_id',$id)->first();
        if(!$vendor){
            return response()->json(['status'=>'error','message'=>'Vendor Not Found!']);
        }

        $vendor = [
            'name'                => $vendor->name,
            'email'               => $vendor->email,
            'contact'             => $vendor->contact,
            'tax_number'          => $vendor->tax_number,
            'billing_name'        => $vendor->billing_name,
            'billing_country'     => $vendor->billing_country,
            'billing_state'       => $vendor->billing_state,
            'billing_city'        => $vendor->billing_city,
            'billing_phone'       => $vendor->billing_phone,
            'billing_zip'         => $vendor->billing_zip,
            'billing_address'     => $vendor->billing_address,
            'shipping_name'       => $vendor->shipping_name,
            'shipping_country'    => $vendor->shipping_country,
            'shipping_state'      => $vendor->shipping_state,
            'shipping_city'       => $vendor->shipping_city,
            'shipping_phone'      => $vendor->shipping_phone,
            'shipping_zip'        => $vendor->shipping_zip,
            'shipping_address'    => $vendor->shipping_address,
            'balance'             => $vendor->balance,
            'debit_note_balance'  => $vendor->debit_note_balance,
        ];

        return response()->json(['status'=>'success','data'=>$vendor]);

    }

    public function vendorUpdate(Request $request, $id){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $rules = [
            'name' => 'required',
            'contact' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
            'billing_name' => 'required',
            'billing_phone' => 'required',
            'billing_address' => 'required',
            'billing_city' => 'required',
            'billing_state' => 'required',
            'billing_country' => 'required',
            'billing_zip' => 'required',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $vendor = Vender::where('created_by',creatorId())->where('workspace',$request->workspace_id)->where('user_id',$id)->first();
        if(!$vendor){
            return response()->json(['status'=>'error','message'=>'Vendor Not Found!']);
        }

        $user = User::where('id',$vendor->user_id)->where('created_by',creatorId())->where('workspace_id',$request->workspace_id)->first();

        if(!$user)
        {
            return response()->json(['status'=>'error', 'message'=>'User Not Found!']);
        }
        if($user->name != $request->name)
        {
            $user->name = $request->name;
            $user->save();
        }

        $vendor->name             = $request->name;
        $vendor->contact          = $request->contact;
        $vendor->tax_number       = $request->tax_number;
        $vendor->billing_name     = $request->billing_name;
        $vendor->billing_country  = $request->billing_country;
        $vendor->billing_state    = $request->billing_state;
        $vendor->billing_city     = $request->billing_city;
        $vendor->billing_phone    = $request->billing_phone;
        $vendor->billing_zip      = $request->billing_zip;
        $vendor->billing_address  = $request->billing_address;
        $vendor->shipping_name    = $request->shipping_name;
        $vendor->shipping_country = $request->shipping_country;
        $vendor->shipping_state   = $request->shipping_state;
        $vendor->shipping_city    = $request->shipping_city;
        $vendor->shipping_phone   = $request->shipping_phone;
        $vendor->shipping_zip     = $request->shipping_zip;
        $vendor->shipping_address = $request->shipping_address;
        $vendor->save();

        event(new UpdateVendor($request,$vendor));

        return response()->json(['status'=>'success', 'message' => 'Vendor successfully updated.']);

    }

    public function vendorDelete(Request $request,$id){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $vendor = Vender::where('created_by',creatorId())->where('workspace',$request->workspace_id)->where('user_id',$id)->first();
        if(!$vendor){
            return response()->json(['status'=>'error','message'=>'Vendor Not Found!']);
        }

        event(new DestroyVendor($vendor));
        $vendor->delete();

        return response()->json(['status'=>'success', 'message' => 'Vendor successfully Deleted!']);

    }

    public function bankAccountList(Request $request){

        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $accounts = DB::table('bank_accounts')
                ->select('bank_accounts.*', 'chart_of_accounts.name as chart_account_name')
                ->leftJoin('chart_of_accounts', 'bank_accounts.chart_account_id', '=', 'chart_of_accounts.id')
                ->where('bank_accounts.workspace', $request->workspace_id)
                ->where('bank_accounts.created_by', creatorId())
                ->get()
                ->map(function($account){
                    return [
                        'id'            => $account->id,
                        'holder_name'   => $account->holder_name,
                        'bank_name'   => $account->bank_name,
                        'bank_type'   => $account->bank_type,
                        'wallet_type'   => $account->wallet_type,
                        'opening_balance'   => $account->opening_balance,
                        'contact_number'   => $account->contact_number,
                        'bank_address'   => $account->bank_address,
                        'bank_branch'   => $account->bank_branch,
                        'swift'   => $account->swift,
                        'chart_account_name'   => $account->chart_account_name,
                        'contact_number'   => $account->contact_number,
                    ];
                });

        return response()->json(['status'=>'success','data'=>$accounts]);

    }

    public function bankAccountStore(Request $request){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $chartIds = ChartOfAccount::where('workspace', $request->workspace_id)->where('created_by', creatorId())->pluck('id')->toArray();
        $validator = \Validator::make(
            $request->all(),
            [
                'holder_name' => 'required|string',
                'bank_type' => ['required',Rule::in(["bank","wallet"])],
                'opening_balance' => 'required|numeric',
                'bank_address' => 'required|string',
                'chart_account' => ['nullable',Rule::in($chartIds)],
                'wallet_type' => ['nullable',Rule::in(['paypal','stripe'])],
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=> $messages->first()]);
        }

        $account                    = new BankAccount();
        $account->chart_account_id  = $request->chart_account;
        $account->holder_name       = $request->holder_name;
        $account->bank_name         = $request->bank_name;
        $account->bank_type         = $request->bank_type;
        $account->wallet_type       = $request->wallet_type;
        $account->account_number    = $request->account_number;
        $account->opening_balance   = $request->opening_balance;
        $account->contact_number    = $request->contact_number;
        $account->bank_branch       = $request->bank_branch;
        $account->swift             = $request->swift;
        $account->bank_address      = $request->bank_address;
        $account->workspace         = $request->workspace_id;
        $account->created_by        = creatorId();
        $account->save();

        $data = [
            'account_id' => $account->chart_account_id,
            'transaction_type' => 'Credit',
            'transaction_amount' => $account->opening_balance,
            'reference' => 'Bank Account',
            'reference_id' => $account->id,
            'reference_sub_id' => 0,
            'date' => date('Y-m-d'),
        ];

        AccountUtility::addTransactionLines($data);

        event(new CreateBankAccount($request, $account));

        return response()->json(['status'=>'success', 'message'=>'Bank Account successfully created!']);

    }

    public function bankAccountUpdate(Request $request,$id){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $chartIds = ChartOfAccount::where('workspace', $request->workspace_id)->where('created_by', creatorId())->pluck('id')->toArray();
        $validator = \Validator::make(
            $request->all(),
            [
                'holder_name' => 'required|string',
                'bank_type' => ['required',Rule::in(["bank","wallet"])],
                'opening_balance' => 'required|numeric',
                'bank_address' => 'required|string',
                'chart_account' => ['nullable',Rule::in($chartIds)],
                'wallet_type' => ['nullable',Rule::in(['paypal','stripe'])],
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=> $messages->first()]);
        }

        $bankAccount = BankAccount::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();

        if(!$bankAccount){
            return response()->json(['status'=>'error', 'message'=> 'Bank Account Not Found!']);
        }

        $bankAccount->chart_account_id  = $request->chart_account;
        $bankAccount->holder_name       = $request->holder_name;
        $bankAccount->bank_name         = $request->bank_name;
        $bankAccount->bank_type         = $request->bank_type;
        $bankAccount->wallet_type       = $request->wallet_type;
        $bankAccount->account_number    = $request->account_number;
        $bankAccount->opening_balance   = $request->opening_balance;
        $bankAccount->contact_number    = $request->contact_number;
        $bankAccount->bank_branch       = $request->bank_branch;
        $bankAccount->swift             = $request->swift;
        $bankAccount->bank_address      = $request->bank_address;
        $bankAccount->save();

        $data = [
            'account_id' => $bankAccount->chart_account_id,
            'transaction_type' => 'Credit',
            'transaction_amount' => $bankAccount->opening_balance,
            'reference' => 'Bank Account',
            'reference_id' => $bankAccount->id,
            'reference_sub_id' => 0,
            'date' => date('Y-m-d'),
        ];

        AccountUtility::addTransactionLines($data, 'edit');

        event(new UpdateBankAccount($request, $bankAccount));

        return response()->json(['status'=>'success','message'=>'Bank Account successfully updated']);
    }

    public function bankAccountDelete(Request $request , $id){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $bankAccount = BankAccount::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$bankAccount){
            return response()->json(['status'=>'error', 'message'=> 'Bank Account Not Found!']);
        }

        $revenue        = Revenue::where('account_id', $bankAccount->id)->where('workspace',$request->workspace_id)->where('created_by', creatorId())->first();
        $invoicePayment = InvoicePayment::where('account_id', $bankAccount->id)->leftjoin('invoices', 'invoice_payments.invoice_id', '=', 'invoices.id')->where('invoices.workspace',$request->workspace_id)->where('invoices.created_by', creatorId())->first();
        $transaction    = Transaction::where('account', $bankAccount->id)->where('workspace',$request->workspace_id)->where('created_by', creatorId())->first();
        $payment        = Payment::where('account_id', $bankAccount->id)->where('workspace',$request->workspace_id)->where('created_by', creatorId())->first();
        $billPayment    = BillPayment::where('account_id', $bankAccount->id)->leftjoin('bills', 'bill_payments.bill_id', '=', 'bills.id')->where('bills.workspace',$request->workspace_id)->where('bills.created_by', creatorId())->first();

        if (!empty($revenue) || !empty($invoicePayment) || !empty($transaction) || !empty($payment) || !empty($billPayment)) {
            return response()->json(['status'=>'error','message'=>'Please delete related record of this account!']);

        } else {
            event(new DestroyBankAccount($bankAccount));
            $bankAccount->delete();

            return response()->json(['status'=>'success','message'=>' Bank Account successfully deleted!']);
        }

    }

    public function chartOfAccountSubTypeList(Request $request){

        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $types = ChartOfAccountType::where('created_by', '=', creatorId())->where('workspace', $request->workspace_id)->get();
        $account_type = [];

        foreach ($types as $type) {
            $accountTypes = ChartOfAccountSubType::where('type', $type->id)->where('created_by', '=', creatorId())->where('workspace',$request->workspace_id)->get();
            $temp = [];
            foreach ($accountTypes as $accountType) {
                $temp[$accountType->id] = $accountType->name;
            }

            $account_type[$type->name] = $temp;
        }

        return response()->json(['status'=>'success','data'=>$account_type]);

    }

    public function chartOfAccount(Request $request){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $accounts = ChartOfAccount::where('workspace', $request->workspace_id)
                ->where('created_by', creatorId())
                ->get()
                ->map(function($account){
                    return [
                        'id'                    => $account->id,
                        'name'                  => $account->name,
                        'code'                  => $account->code,
                        'description'           => $account->description,
                    ];
                });

        return response()->json(['status'=>'success','data'=>$accounts]);
    }

    public function chartOfAccountStore(Request $request){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $accountTypes = ChartOfAccountSubType::where('workspace',$request->workspace_id)->where('created_by', '=', creatorId())->pluck('id');
        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'sub_type' => ['required' , Rule::in($accountTypes)],
                'code'=>'numeric'
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $type = ChartOfAccountSubType::where('id', $request->sub_type)->where('created_by', '=', creatorId())->where('workspace', $request->workspace_id)->first();

        $account = new ChartOfAccount();
        $account->name = $request->name;
        $account->code = $request->code;
        $account->type = $type->type;
        $account->sub_type = $request->sub_type;
        $account->description = $request->description;
        $account->is_enabled = $request->is_enabled;
        $account->created_by = creatorId();
        $account->workspace = $request->workspace_id;
        $account->save();

        event(new CreateChartAccount($request, $account));

        return response()->json(['status'=>'success','message'=>'Chart Of Account successfully created!']);

    }

    public function chartOfAccountUpdate(Request $request , $id){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }
        $accountTypes = ChartOfAccountSubType::where('workspace',$request->workspace_id)->where('created_by', '=', creatorId())->pluck('id');
        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'sub_type' => ['required' , Rule::in($accountTypes)],
                'code'=>'numeric'
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $chartOfAccount = ChartOfAccount::where('created_by',creatorId())->where('workspace',$request->workspace_id)->where('id',$id)->first();
        if(!$chartOfAccount){
            return response()->json(['status'=>'error','message'=>'Chart Of Account Not found!']);
        }

        $chartOfAccount->name = $request->name;
        $chartOfAccount->code = $request->code;
        $chartOfAccount->description = $request->description;
        $chartOfAccount->is_enabled = $request->is_enabled;
        $chartOfAccount->save();

        event(new UpdateChartAccount($request, $chartOfAccount));

        return response()->json(['status'=>'success','message' => 'Chart of Account successfully updated!']);
    }

    public function chartOfAccountDelete(Request $request , $id){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $chartOfAccount = ChartOfAccount::where('created_by',creatorId())->where('workspace',$request->workspace_id)->where('id',$id)->first();
        if(!$chartOfAccount){
            return response()->json(['status'=>'error','message'=>'Chart Of Account Not found!']);
        }

        event(new DestroyChartAccount($chartOfAccount));

        $chartOfAccount->delete();

        return response()->json(['status'=>'success','message'=>'Chart Of Account successfully deleted!']);
    }

    public function bankTransfer(Request $request){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $transfers = Transfer::where('workspace', $request->workspace_id)
        ->where('created_by', creatorId())->get()
            ->map(function($transfer){
                return [
                    'id'             => $transfer->id,
                    'from_account'   => !empty($transfer->fromBankAccount) ? $transfer->fromBankAccount->bank_name . ' ' . $transfer->fromBankAccount->holder_name : '',
                    'to_account'     => !empty($transfer->toBankAccount) ? $transfer->toBankAccount->bank_name .' '.$transfer->toBankAccount->holder_name : '',
                    'from_type'      => $transfer->from_type,
                    'to_type'        => $transfer->to_type,
                    'amount'         => currency_format_with_sym($transfer->amount),
                    'date'           => company_date_formate($transfer->date),
                    'reference'      => $transfer->reference,
                    'description'    => $transfer->description,
                ];
            });

        return response()->json(['status'=>'success','data'=>$transfers]);
    }

    public function bankTransferStore(Request $request){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $bankAccounts = BankAccount::where('workspace', $request->workspace_id)->where('created_by',creatorId())->pluck('id')->toArray();

        $validator = \Validator::make(
            $request->all(), [
                    'from_account' => 'required|numeric|in:'.implode(',',$bankAccounts),
                    'to_account' => 'required|numeric|in:'.implode(',',$bankAccounts),
                    'amount' => 'required|numeric|gt:0',
                    'date' => 'required|date_format:Y-m-d',
                    'from_type' => 'required|in:bank,wallet',
                    'to_type' => 'required|in:bank,wallet',
                ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error','message'=> $messages->first()]);
        }

        $transfer                 = new Transfer();
        $transfer->from_account   = $request->from_account;
        $transfer->to_account     = $request->to_account;
        $transfer->from_type      = $request->from_type;
        $transfer->to_type        = $request->to_type;
        $transfer->amount         = $request->amount;
        $transfer->date           = $request->date;
        $transfer->payment_method = 0;
        $transfer->reference      = $request->reference;
        $transfer->description    = $request->description;
        $transfer->created_by      = creatorId();
        $transfer->workspace      = $request->workspace_id;
        $transfer->save();

        Transfer::bankAccountBalance($request->from_account, $request->amount, 'debit');

        Transfer::bankAccountBalance($request->to_account, $request->amount, 'credit');
        event(new CreateBankTransfer($request,$transfer));

        return response()->json(['status'=>'success','message'=>'Amount successfully transfer!']);
    }

    public function bankTransferUpdate(Request $request,$id){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $bankAccounts = BankAccount::where('workspace', $request->workspace_id)->where('created_by',creatorId())->pluck('id')->toArray();

        $validator = \Validator::make(
            $request->all(), [
                'from_account' => 'required|numeric|in:'.implode(',',$bankAccounts),
                'to_account' => 'required|numeric|in:'.implode(',',$bankAccounts),
                'amount' => 'required|numeric|gt:0',
                'date' => 'required|date_format:Y-m-d',
                'from_type' => 'required|in:bank,wallet',
                'to_type' => 'required|in:bank,wallet',
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error','message'=> $messages->first()]);
        }
        $transfer = Transfer::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$transfer){
            return response()->json(['status'=>'error','message'=>'Transfer not Found!']);
        }

        Transfer::bankAccountBalance($transfer->from_account, $transfer->amount, 'credit');
        Transfer::bankAccountBalance($transfer->to_account, $transfer->amount, 'debit');

        $transfer->from_account   = $request->from_account;
        $transfer->to_account     = $request->to_account;
        $transfer->from_type      = $request->from_type;
        $transfer->to_type        = $request->to_type;
        $transfer->amount         = $request->amount;
        $transfer->date           = $request->date;
        $transfer->payment_method = 0;
        $transfer->reference      = $request->reference;
        $transfer->description    = $request->description;
        $transfer->save();

        Transfer::bankAccountBalance($request->from_account, $request->amount, 'debit');
        Transfer::bankAccountBalance($request->to_account, $request->amount, 'credit');
        event(new UpdateBankTransfer($request,$transfer));

        return response()->json(['status'=>'success','message'=>'Transfer Amount successfully updated!']);

    }

    public function bankTransferDelete(Request $request,$id){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $transfer = Transfer::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$transfer){
            return response()->json(['status'=>'error','message'=>'Transfer Amount not Found!']);
        }

        event(new DestroyBankTransfer($transfer));

        $transfer->delete();

        return response()->json(['status'=>'success','message'=>'Transfer Amount successfully deleted!']);
    }

    public function revenueList(Request $request){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $revenues = Revenue::select('revenues.*', 'bank_accounts.bank_name as bank_name', 'bank_accounts.holder_name as holder_name', 'customers.name as customer_name','categories.name as category_name')
            ->where('revenues.workspace', '=', $request->workspace_id)
            ->where('revenues.created_by', creatorId())
            ->join('customers', 'revenues.customer_id', '=', 'customers.id')
            ->join('bank_accounts', 'revenues.account_id', '=', 'bank_accounts.id')
            ->join('categories', 'revenues.category_id', '=', 'categories.id')
            ->get()
            ->map(function($revenue){
                return [
                    'id'            => $revenue->id,
                    'date'          => company_date_formate($revenue->date),
                    'amount'        => currency_format_with_sym($revenue->amount),
                    'account_name'  => $revenue->bank_name,
                    'customer'      => $revenue->customer_name,
                    'category'      => module_is_active('ProductService') ? $revenue->category_name : '',
                    'reference'     => $revenue->reference,
                    'description'   => $revenue->description
                ];
            });

        return response()->json(['status'=>'success','data'=>$revenues]);
    }

    public function revenueStore(Request $request)
    {
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $customer = Customer::where('workspace', '=',$request->workspace_id)->where('created_by', creatorId())->where('id',$request->customer_id)->first();
        if(!$customer){
            return response()->json(['status'=>'error','message'=>'Customer Not Found!']);
        }
        $account   = BankAccount::where('workspace', $request->workspace_id)->where('created_by', creatorId())->where('id',$request->account_id)->first();
        if(!$account){
            return response()->json(['status'=>'error','message'=>'Account Not Found!']);
        }

        $categories = false;

        if(module_is_active('ProductService'))
        {
            $categories = \Workdo\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->where('id',$request->category_id)->exists();
        }

        if($request->category_id && !$categories){
            return response()->json(['status'=>'error','message'=>'Category Not Found!']);
        }

        $validator = \Validator::make(
            $request->all(), [
                'date' => 'required|date_format:Y-m-d',
                'amount' => 'required|numeric|gt:0',
                'account_id' => 'required',
                'category_id' => 'required',
                'reference' => 'required',
                'description' => 'required',
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error','message'=> $messages->first()]);
        }

        $revenue                 = new Revenue();
        $revenue->date           = $request->date;
        $revenue->amount         = $request->amount;
        $revenue->account_id     = $request->account_id;
        $revenue->customer_id    = $request->customer_id;
        $revenue->user_id        = $customer->user_id;
        $revenue->category_id    = $request->category_id;
        $revenue->payment_method = 0;
        $revenue->reference      = $request->reference;
        $revenue->description    = $request->description;
        if(!empty($request->add_receipt))
        {
            $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();

            $uplaod = upload_file($request,'add_receipt',$fileName,'revenue');
            if($uplaod['flag'] == 1)
            {
                $url = $uplaod['url'];
            }
            else{
                return response()->json(['status'=>'error','message'=>$uplaod['msg']]);
            }
            $revenue->add_receipt = $url;

        }
        $revenue->created_by     = \Auth::user()->id;
        $revenue->workspace        = $request->workspace_id;
        $revenue->save();
        if(module_is_active('ProductService'))
        {
            $category            = \Workdo\ProductService\Entities\Category::where('id', $request->category_id)->first();
        }
        else
        {
            $category = [];
        }
        $revenue->payment_id = $revenue->id;
        $revenue->type       = 'Revenue';
        $revenue->category   = !empty($category) ? $category->name : '';
        $revenue->user_id    = $revenue->customer_id;
        $revenue->user_type  = 'Customer';
        $revenue->account    = $request->account_id;
        Transaction::addTransaction($revenue);

        $customer         = Customer::where('id', $request->customer_id)->first();
        $payment          = new InvoicePayment();
        $payment->name    = !empty($customer) ? $customer['name'] : '';
        $payment->date    = company_date_formate($request->date);
        $payment->amount  = currency_format_with_sym($request->amount);
        $payment->invoice = '';
        if(!empty($customer))
        {
            AccountUtility::userBalance('customer', $customer->id, $revenue->amount, 'credit');
        }

        Transfer::bankAccountBalance($request->account_id, $revenue->amount, 'credit');

        event(new CreateRevenue($request,$revenue));

        if(!empty(company_setting('Revenue Payment Create')) && company_setting('Revenue Payment Create')  == true)
        {
            $uArr = [
                'payment_name' => $payment->name,
                'payment_amount' => $payment->amount,
                'revenue_type' =>$revenue->type,
                'payment_date' => $payment->date,
            ];
            try
            {
                $resp = EmailTemplate::sendEmailTemplate('Revenue Payment Create', [$customer->id => $customer->email], $uArr);
            }
            catch(\Exception $e)
            {
                $resp['error'] = $e->getMessage();
            }
            return response()->json(['status'=>'success', 'message' => 'Revenue successfully created!' . ((isset($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : '')]);
        }

        return response()->json(['status'=>'success', 'message' => 'Revenue successfully created!']);
    }

    public function revenueUpdate(Request $request, $id)
    {

        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $customer = Customer::where('workspace', '=', $request->workspace_id)->where('created_by', creatorId())->where('id', $request->customer_id)->first();
        if(!$customer){
            return response()->json(['status'=>'error','message'=>'Customer Not Found!']);
        }

        $account   = BankAccount::where('workspace', $request->workspace_id)->where('created_by', creatorId())->where('id',$request->account_id)->first();
        if(!$account){
            return response()->json(['status'=>'error','message'=>'Account Not Found!']);
        }

        $categories = false;

        if(module_is_active('ProductService'))
        {
            $categories = \Workdo\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->where('id',$request->category_id)->exists();
        }

        if($request->category_id && !$categories){
            return response()->json(['status'=>'error','message'=>'Category Not Found!']);
        }

        $validator = \Validator::make(
            $request->all(), [
                'date' => 'required|date_format:Y-m-d',
                'amount' => 'required|numeric|gt:0',
                'account_id' => 'required',
                'category_id' => 'required',
                'reference' => 'required',
                'description' => 'required',
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error','message'=> $messages->first()]);
        }

        $revenue = Revenue::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$revenue){
            return response()->json(['status'=>'error','message'=>'Revenue Not Found!']);
        }

        Transfer::bankAccountBalance($revenue->account_id, $revenue->amount, 'debit');

        $revenue->date           = $request->date;
        $revenue->amount         = $request->amount;
        $revenue->account_id     = $request->account_id;
        $revenue->customer_id    = $request->customer_id;
        $revenue->user_id        = $customer->user_id;
        $revenue->category_id    = $request->category_id;
        $revenue->payment_method = 0;
        $revenue->reference      = $request->reference;
        $revenue->description    = $request->description;

        if(!empty($request->add_receipt))
        {
            if(!empty($revenue->add_receipt))
            {
                delete_file($revenue->add_receipt);
            }
            $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
            $uplaod = upload_file($request,'add_receipt',$fileName,'revenue');
            if($uplaod['flag'] == 1)
            {
                $url = $uplaod['url'];
            }
            else{
                return response()->json(['status'=>'error','message'=>$uplaod['msg']]);
            }
            $revenue->add_receipt = $url;
        }

        $revenue->save();

        if(module_is_active('ProductService'))
        {
            $category            = \Workdo\ProductService\Entities\Category::where('id', $request->category_id)->first();
        }
        else
        {
            $category = [];
        }

        $revenue->category   = !empty($category) ? $category->name : '';
        $revenue->payment_id = $revenue->id;
        $revenue->type       = 'Revenue';
        $revenue->account    = $request->account_id;
        Transaction::editTransaction($revenue);

        AccountUtility::userBalance('customer', $customer->id, $request->amount, 'credit');
        Transfer::bankAccountBalance($request->account_id, $request->amount, 'credit');

        if(module_is_active('DoubleEntry'))
        {
            $request->merge(['id'=>$revenue->id]);
        }
        event(new UpdateRevenue($request,$revenue));
        return response()->json(['status'=>'success', 'message'=> 'Revenue successfully updated!']);

    }

    public function revenueDelete(Request $request , $id){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $type = 'Revenue';
        $user = 'Customer';

        $revenue = Revenue::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$revenue){
            return response()->json(['status'=>'error','message'=>'Revenue Not Found!']);
        }

        Transaction::destroyTransaction($revenue->id, $type, $user);
        if($revenue->customer_id != 0)
        {
            AccountUtility::userBalance('customer', $revenue->customer_id, $revenue->amount, 'debit');
        }
        Transfer::bankAccountBalance($revenue->account_id, $revenue->amount, 'debit');
        if(!empty($revenue->add_receipt))
        {
            delete_file($revenue->add_receipt);
        }
        event(new DestroyRevenue($revenue));
        $revenue->delete();

        return response()->json(['status'=>'success', 'message'=>'Revenue successfully deleted!']);
    }

    public function customerCreditNoteList(Request $request)
    {
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $invoices = Invoice::where('created_by', creatorId())->where('workspace',$request->workspace_id)->get();
        $data = [];
        foreach($invoices as $invoice){
            if(!empty($invoice->customcreditNote)){
                foreach ($invoice->customcreditNote as $customcreditNote){
                    $data[] = [
                        'id'            => $customcreditNote->id,
                        'invoice'       => Invoice::invoiceNumberFormat($invoice->invoice_id),
                        'customer'      => $invoice->customer->name,
                        'date'          => company_date_formate($customcreditNote->date),
                        'amount'        => currency_format_with_sym($customcreditNote->amount),
                        'description'   => $customcreditNote->description,
                        'status'        => \Workdo\Account\Entities\CustomerCreditNotes::$statues[$customcreditNote->status]
                    ];
                }
            }
        }

        return response()->json(['status'=>'success','data'=>$data]);
    }

    public function customerCreditNoteStore(Request $request)
    {
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $invoiceIds = Invoice::where('created_by', creatorId())->where('workspace',$request->workspace_id)->pluck('id');
        $validator = \Validator::make(
            $request->all(), [
                'amount' => 'required|numeric|gte:0',
                'date' => 'required|date_format:Y-m-d',
                'invoice_id'=>['required',Rule::in($invoiceIds)]
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message'=>$messages->first()]);
        }

        $invoice = Invoice::where('created_by', creatorId())->where('workspace',$request->workspace_id)->where('id', $request->invoice_id)->first();
        if(!$invoice){
            return response()->json(['status'=>'error','message'=>'Invoice Not Found!']);
        }
        if($request->amount > $invoice->getDue())
        {
            return response()->json(['status'=>'error', 'message'=>'Maximum ' . currency_format_with_sym($invoice->getDue()) . ' credit limit of this invoice.']);
        }

        $credit              = new CustomerCreditNotes();
        $credit->invoice     = $request->invoice_id;
        $credit->customer    = $invoice->customer_id;
        $credit->date        = $request->date;
        $credit->amount      = $request->amount;
        $credit->description = $request->description;
        $credit->save();

        AccountUtility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');

        return response()->json(['status'=>'success', 'message'=>'Credit Note successfully created!']);
    }

    public function customerCreditNoteUpdate(Request $request, $id){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }
        $status = CustomerCreditNotes::$statues;
        $validator = \Validator::make(
            $request->all(), [
                'amount' => 'required|numeric|gte:0',
                'date' => 'required|date_format:Y-m-d',
                'status'=>[Rule::in($status)]
            ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $credit = CustomerCreditNotes::leftjoin('customers', 'customer_credit_notes.customer', '=', 'customers.id')->where('customers.workspace',$request->workspace_id)->where('customers.created_by', creatorId())->where('customer_credit_notes.id',$id)->select('customer_credit_notes.*')->first();

        if(!$credit){
            return response()->json(['status'=>'error','message'=>'Credit Note Not Found!']);
        }
        $invoice = Invoice::where('created_by', creatorId())->where('workspace',$request->workspace_id)->where('id', $credit->invoice)->first();
        if(!$invoice){
            return response()->json(['status'=>'error','message'=>'Invoice Not Found!']);
        }
        if($request->amount > $invoice->getDue()+$credit->amount)
        {
            return response()->json(['status'=>'error','message' => 'Maximum ' . currency_format_with_sym($invoice->getDue()) . ' credit limit of this invoice.']);
        }

        AccountUtility::updateUserBalance('customer', $invoice->customer_id, $credit->amount, 'credit');
        AccountUtility::updateCreditnoteBalance('customer', $invoice->customer_id, $credit->amount, 'credit');

        $status = array_flip($status);
        $credit->date        = $request->date;
        $credit->amount      = $request->amount;
        $credit->status      = $status[$request->status];
        $credit->description = $request->description;
        $credit->save();

        AccountUtility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');

        AccountUtility::updateCreditnoteBalance('customer', $invoice->customer_id, $request->amount, 'debit');

        return response()->json(['status'=>'success', 'message' => 'Credit Note successfully updated!']);

    }

    public function customerCreditNoteDelete(Request $request, $id){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $creditNote = CustomerCreditNotes::leftjoin('customers', 'customer_credit_notes.customer', '=', 'customers.id')->where('customers.workspace',$request->workspace_id)->where('customers.created_by', creatorId())->where('customer_credit_notes.id',$id)->first();
        if(!$creditNote){
            return response()->json(['status'=>'error','message'=>'Credit Note Not Found!']);
        }

        CustomerCreditNotes::find($id)->delete();
        AccountUtility::updateUserBalance('customer', $creditNote->customer, $creditNote->amount, 'credit');
        AccountUtility::updateCreditnoteBalance('customer', $creditNote->customer, $creditNote->amount, 'credit');

        return response()->json(['status' => 'success', 'message' => 'Credit Note successfully deleted!']);
    }

    public function billsList(Request $request)
    {
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $bills = Bill::select('bills.*', 'vendors.name as vendor_name')->where('bills.workspace', '=', $request->workspace_id)
            ->join('vendors', 'bills.vendor_id', '=', 'vendors.id')->where('bills.created_by', creatorId())->get()
            ->map(function($bill){
                return [
                    'id'            => $bill->id,
                    'bill_number'   => Bill::billNumberFormat($bill->bill_id),
                    'account_type'  => $bill->account_type,
                    'bill_date'     => company_date_formate($bill->bill_date),
                    'due_date'      => company_date_formate($bill->due_date),
                    'due_amount'    => currency_format_with_sym($bill->getDue()),
                    'order_number'  => $bill->order_number,
                    'bill_module'   => $bill->bill_module,
                    'vendor_name'   => $bill->vendor_name,
                    'status'        => Bill::$statues[$bill->status],
                    'vendor'        => [
                        'id'            => $bill->vendor->id,
                        'name'          => $bill->vendor->name,
                        'email'         => $bill->vendor->email,
                        'contact'       => $bill->vendor->contact,
                        'tax_number'    => $bill->vendor->tax_number,
                    ],
                    'items'         => $bill->items->map(function($item){
                        $taxes = Tax::whereIn('id',explode(',',$item->tax))->get()->map(function($tax){
                            return [
                                'id'        => $tax->id,
                                'name'      => $tax->name,
                                'rate'      => $tax->rate
                            ];
                        });
                        return [
                            'id'           => $item->id,
                            'product_type' => $item->product_type,
                            'quantity'     => $item->quantity,
                            'price'        => $item->price,
                            'tax'          => $taxes,
                            'discount'     => $item->discount,
                            'description'  => $item->description,
                        ];
                    }),
                    'accounts'      => $bill->accounts->map(function($account){
                        return [
                            'id'            => $account->id,
                            'price'         => currency_format_with_sym($account->price),
                            'description'   => $account->description,
                            'type'          => $account->type
                        ];
                    }),
                    'category'      => [
                        'id'        => $bill->category->id,
                        'name'      => $bill->category->name,
                        'color'     => $bill->category->color,
                    ]
                ];
            });

        return response()->json(['status'=>'success','data'=>$bills]);
    }

    public function billStore(Request $request)
    {
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $vendorIds = Vender::where('workspace', '=', $request->workspace_id)->where('created_by', creatorId())->get()->pluck('id');
        $categoriesId = \Workdo\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->where('type', 2)->get()->pluck('id');
        $chartOfAccountId = ChartOfAccount::where('workspace',$request->workspace_id)->where('created_by', creatorId())->pluck('id');
        $taxIds = \Workdo\ProductService\Entities\Tax::where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->pluck('id')->toArray();

        $requestData = $request->all();
        $requestData['tax'] = is_string($request->tax) ? json_decode($request->tax, true) : $request->tax;

        $projectIds = \Workdo\Taskly\Entities\Project::where('workspace',$request->workspace_id)->where('created_by',creatorId())->pluck('id')->toArray();
        $validator = \Validator::make(
            $requestData,
            [
                'bill_type' => 'required|in:product,project',
                'account_type' => 'required|in:Accounting,Projects',
                'vendor_id' => ['required',Rule::in($vendorIds)],
                'bill_date' => 'required|date_format:Y-m-d',
                'due_date' => 'required|date_format:Y-m-d',
                'category_id' => ['required_if:bill_type,product',Rule::in($categoriesId)],
                'project' => ['required_if:bill_type,project',Rule::in($projectIds)],
                'tax' => [
                    'nullable', // Allow null when not required
                    'required_if:bill_type,project', // Required only for projects
                    'array',
                    function ($attribute, $value, $fail) use ($request, $taxIds) {
                        if ($request->bill_type === 'project') {
                            if (!is_array($value)) {
                                $fail('The tax must be an array.');
                            } elseif (!empty(array_diff($value, $taxIds))) {
                                $fail('The selected tax is invalid.');
                            }
                        }
                    },
                ],
                'items' => 'required|array',
                'items.*.product_type' => 'required_if:bill_type,product|string',
                'items.*.item' => ['required',
                    function ($attribute, $value, $fail) use ($request) {
                        $attributeIndex = explode('.', $attribute)[1];
                        if ($request->bill_type === 'product') {
                            $product_type = $request->input('items.' . $attributeIndex . '.product_type');

                            $product_services = \Workdo\ProductService\Entities\ProductService::where('workspace_id', $request->workspace_id)
                                ->where('type', $product_type)
                                ->pluck('id');

                            if (!$product_services->contains($value)) {
                                $fail('The selected item is not valid.');
                            }
                        } elseif ($request->bill_type === 'project') {
                            $project = \Workdo\Taskly\Entities\Project::where('workspace', $request->workspace_id)
                                ->where('created_by', creatorId())
                                ->where('id', $request->project)
                                ->first();

                            $itemsIds = $project->task()->pluck('id');

                            if (!$itemsIds->contains($value)) {
                                $fail('The selected item is not valid.');
                            }
                        }
                    },
                ],
                'items.*.quantity' => 'required|numeric|gt:0',
                'items.*.price' => 'required|numeric|gte:0',
                'items.*.discount' => 'nullable|numeric|gte:0',
                'items.*.tax' => 'nullable',
                'items.*.chart_account_id' => ['nullable','numeric',Rule::in($chartOfAccountId)],
                'items.*.description' => 'nullable|string',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error','message'=> $messages->first()]);
        }

        $vendor = Vender::where('workspace',$request->workspace_id)->where('created_by', creatorId())->where('id',$request->vendor_id)->first();

        $bill = new Bill();
        $bill->bill_id = $this->billNumber();
        $bill->vendor_id = $request->vendor_id;
        $bill->user_id = !empty($vendor) ? $vendor->user_id : null;
        $bill->account_type = $request->account_type;
        $bill->bill_date = $request->bill_date;
        $bill->status = 0;
        $bill->bill_module = $request->bill_type == 'product' ? 'account' :'taskly';
        $bill->due_date = $request->due_date;
        $bill->category_id = $request->bill_type == 'product' ? $request->category_id : $request->project;
        $bill->order_number = !empty($request->order_number) ? $request->order_number : 0;
        $bill->created_by = creatorId();
        $bill->workspace = $request->workspace_id;
        $bill->save();

        Invoice::starting_number($bill->bill_id + 1, 'bill');

        $products = $request->items;
        if($request->bill_type == 'project'){

            $project_tax = implode(',', json_decode($request->tax));
        }

        for ($i = 0; $i < count($products); $i++) {
            if($request->bill_type == 'product'){
                if (!empty($products[$i]['item'])) {
                    $billProduct = new BillProduct();
                    $billProduct->bill_id = $bill->id;
                    $billProduct->product_type = $products[$i]['product_type'];
                    $billProduct->product_id = $products[$i]['item'];
                    $billProduct->quantity = $products[$i]['quantity'];
                    $itemTax = isset($products[$i]['tax']) ? implode(',', json_decode($products[$i]['tax'])) : null;
                    $billProduct->tax = $itemTax;
                    $billProduct->discount = $products[$i]['discount'];
                    $billProduct->price = $products[$i]['price'];
                    $billProduct->description = str_replace("'", "", $products[$i]['description']);
                    $billProduct->save();
                }
            }
            elseif($request->bill_type == 'project'){
                $billProduct = new BillProduct();
                $billProduct->bill_id = $bill->id;
                $billProduct->product_id = $products[$i]['item'];
                $billProduct->quantity = 1;
                $billProduct->tax = $project_tax;
                $billProduct->discount = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                $billProduct->price = $products[$i]['price'];
                $billProduct->description = str_replace("'", "", $products[$i]['description']);
                $billProduct->save();
            }

            if($request->bill_type == 'product'){
                if (!empty($products[$i]['chart_account_id'])) {
                    $billAccount = new BillAccount();
                    $billAccount->chart_account_id = $products[$i]['chart_account_id'];
                    $billAccount->price = $products[$i]['price'];
                    $billAccount->description = $products[$i]['description'];
                    $billAccount->type = 'Bill';
                    $billAccount->ref_id = $bill->id;
                    $billAccount->created_by = creatorId();
                    $billAccount->workspace = $request->workspace_id;
                    $billAccount->save();
                }
            }

            if (!empty($billProduct)) {
                Invoice::total_quantity('plus', $billProduct->quantity, $billProduct->product_id);
            }

            //Product Stock Report
            if (!empty($products[$i]['item'])) {
                $type = 'bill';
                $type_id = $bill->id;
                $description = $products[$i]['quantity'] . '  ' . __(' quantity purchase in bill') . ' ' . Bill::billNumberFormat($bill->bill_id);
                Bill::addProductStock($products[$i]['item'], $products[$i]['quantity'], $type, $description, $type_id);
            }
        }

        event(new CreateBill($request, $bill));
        return response()->json(['status'=>'success','message'=>'Bill successfully created!']);

    }

    function billNumber()
    {
        $latest = company_setting('bill_starting_number');
        if ($latest == null) {
            return 1;
        } else {
            return $latest;
        }
    }

    public function billDetail(Request $request,$id)
    {
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $bill = Bill::where('id',$id)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$bill){
            return response()->json(['status'=>'error','message'=>'Bill Not Found!']);
        }

        $data = [
            'id'            => $bill->id,
            'bill_id'       => Bill::billNumberFormat($bill->bill_id),
            'account_type'  => $bill->account_type,
            'bill_date'     => company_date_formate($bill->bill_date),
            'due_date'      => company_date_formate($bill->due_date),
            'due_amount'    => currency_format_with_sym($bill->getDue()),
            'order_number'  => $bill->order_number,
            'bill_module'   => $bill->bill_module,
            'status'        => Bill::$statues[$bill->status],
            'vendor'        => [
                'id'            => $bill->vendor->id,
                'name'          => $bill->vendor->name,
                'email'         => $bill->vendor->email,
                'contact'       => $bill->vendor->contact,
                'tax_number'    => $bill->vendor->tax_number,
            ],
            'items'         => $bill->items->map(function($item){
                $taxes = Tax::whereIn('id',explode(',',$item->tax))->get()->map(function($tax){
                    return [
                        'id'        => $tax->id,
                        'name'      => $tax->name,
                        'rate'      => $tax->rate
                    ];
                });
                return [
                    'id'           => $item->id,
                    'product_type' => $item->product_type,
                    'quantity'     => $item->quantity,
                    'price'        => $item->price,
                    'tax'          => $taxes,
                    'discount'     => $item->discount,
                    'description'  => $item->description,
                ];
            }),
            'accounts'      => $bill->accounts->map(function($account){
                return [
                    'id'            => $account->id,
                    'price'         => currency_format_with_sym($account->price),
                    'description'   => $account->description,
                    'type'          => $account->type
                ];
            }),
            'category'      => [
                'id'        => $bill->category->id,
                'name'      => $bill->category->name,
                'color'     => $bill->category->color,
            ]

        ];

        return response()->json(['status'=>'success','data'=>$data]);

    }

    public function billUpdate(Request $request,$id)
    {
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $bill = Bill::where('id',$id)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$bill){
            return response()->json(['status'=>'error','message'=>'Bill Not Found!']);
        }

        $vendorIds = Vender::where('workspace', '=', $request->workspace_id)->where('created_by', creatorId())->get()->pluck('id');
        $categoriesId = \Workdo\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->where('type', 2)->get()->pluck('id');
        $chartOfAccountId = ChartOfAccount::where('workspace',$request->workspace_id)->pluck('id');
        $taxIds = \Workdo\ProductService\Entities\Tax::where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->pluck('id')->toArray();
        $requestData = $request->all();
        $requestData['tax'] = is_string($request->tax) ? json_decode($request->tax, true) : $request->tax;

        $projectIds = \Workdo\Taskly\Entities\Project::where('workspace',$request->workspace_id)->where('created_by',creatorId())->pluck('id')->toArray();
        $validator = \Validator::make(
            $requestData,
            [
                'bill_type' => 'required|in:product,project',
                'account_type' => 'required|in:Accounting,Projects',
                'vendor_id' => ['required',Rule::in($vendorIds)],
                'bill_date' => 'required|date_format:Y-m-d',
                'due_date' => 'required|date_format:Y-m-d',
                'category_id' => ['required_if:bill_type,product',Rule::in($categoriesId)],
                'project' => ['required_if:bill_type,project',Rule::in($projectIds)],
                'tax' => [
                    'nullable', // Allow null when not required
                    'required_if:bill_type,project', // Required only for projects
                    'array',
                    function ($attribute, $value, $fail) use ($request, $taxIds) {
                        if ($request->bill_type === 'project') {
                            if (!is_array($value)) {
                                $fail('The tax must be an array.');
                            } elseif (!empty(array_diff($value, $taxIds))) {
                                $fail('The selected tax is invalid.');
                            }
                        }
                    },
                ],
                'items' => 'required|array',
                'items.*.product_type' => 'required_if:bill_type,product|string',
                'items.*.item' => ['required',
                    function ($attribute, $value, $fail) use ($request) {
                        $attributeIndex = explode('.', $attribute)[1];
                        if ($request->bill_type === 'product') {
                            $product_type = $request->input('items.' . $attributeIndex . '.product_type');

                            $product_services = \Workdo\ProductService\Entities\ProductService::where('workspace_id', $request->workspace_id)
                                ->where('type', $product_type)
                                ->pluck('id');

                            if (!$product_services->contains($value)) {
                                $fail('The selected item is not valid.');
                            }
                        } elseif ($request->bill_type === 'project') {
                            $project = \Workdo\Taskly\Entities\Project::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$request->project)->first();
                            $itemsIds = $project->task()->pluck('id');

                            if (!$itemsIds->contains($value)) {
                                $fail('The selected item is not valid.');
                            }
                        }
                    },
                ],
                'items.*.quantity' => 'required|numeric|gt:0',
                'items.*.price' => 'required|numeric|gte:0',
                'items.*.discount' => 'nullable|numeric|gte:0',
                'items.*.tax' => 'nullable',
                'items.*.chart_account_id' => ['nullable','numeric',Rule::in($chartOfAccountId)],
                'items.*.description' => 'nullable|string',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error','message'=> $messages->first()]);
        }

        $vendor = Vender::where('workspace',$request->workspace_id)->where('created_by', creatorId())->where('id',$request->vendor_id)->first();

        $bill->vendor_id = $request->vendor_id;
        $bill->user_id = !empty($vendor) ? $vendor->user_id : null;
        $bill->bill_date = $request->bill_date;
        $bill->account_type = $request->account_type;
        $bill->due_date = $request->due_date;
        $bill->bill_module = $request->bill_type == 'product' ? 'account' :'taskly';
        $bill->category_id = $request->bill_type == 'product' ? $request->category_id : $request->project;
        $bill->order_number = !empty($request->order_number) ? $request->order_number : 0;
        $bill->save();
        $products = $request->items;
        if($request->bill_type == 'project'){
            $project_tax = implode(',', json_decode($request->tax));
        }
        for ($i = 0; $i < count($products); $i++) {
            if($request->bill_type == 'product'){
                if (!empty($products[$i]['item'])) {
                    $billProduct = BillProduct::find($products[$i]['id']);
                    if ($billProduct == null) {
                        $billProduct = new BillProduct();
                        $billProduct->bill_id = $bill->id;
                        Invoice::total_quantity('plus', $products[$i]['quantity'], $products[$i]['item']);
                        $updatePrice = ($products[$i]['price'] * $products[$i]['quantity']) + ($products[$i]['itemTaxPrice']) - ($products[$i]['discount']);
                        AccountUtility::updateUserBalance('vendor', $request->vendor_id, $updatePrice, 'debit');
                    } else {
                        Invoice::total_quantity('minus', $billProduct->quantity, $billProduct->product_id);
                    }

                    if (isset($products[$i]['item'])) {
                        $billProduct->product_id = $products[$i]['item'];
                    }

                    $billProduct->product_type = $products[$i]['product_type'];
                    $billProduct->quantity = $products[$i]['quantity'];
                    $itemTax = isset($products[$i]['tax']) ? implode(',', json_decode($products[$i]['tax'])) : null;
                    $billProduct->tax = $itemTax;
                    $billProduct->discount = $products[$i]['discount'];
                    $billProduct->price = $products[$i]['price'];
                    $billProduct->description = str_replace("'", "", $products[$i]['description']);
                    $billProduct->save();
                }
            }
            elseif($request->bill_type == 'project'){
                $billProduct = BillProduct::find($products[$i]['id']);
                if ($billProduct == null) {
                    $billProduct = new BillProduct();
                    $billProduct->bill_id = $bill->id;
                }
                $billProduct->product_id = $products[$i]['item'];
                $billProduct->quantity = 1;
                $billProduct->tax = $project_tax;
                $billProduct->discount = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                $billProduct->price = $products[$i]['price'];
                $billProduct->description = str_replace("'", "", $products[$i]['description']);
                $billProduct->save();
            }

            if($request->bill_type == 'product'){
                if (!empty($products[$i]['chart_account_id'])) {
                    $billAccount = BillAccount::find($products[$i]['id']);
                    if ($billAccount == null) {
                        $billAccount = new BillAccount();
                    }
                    $billAccount->chart_account_id = $products[$i]['chart_account_id'];
                    $billAccount->price = $products[$i]['price'];
                    $billAccount->description = $products[$i]['description'];
                    $billAccount->type = 'Bill';
                    $billAccount->ref_id = $bill->id;
                    $billAccount->created_by = creatorId();
                    $billAccount->workspace = $request->workspace_id;
                    $billAccount->save();
                }
                if ($products[$i]['id'] > 0) {
                    Invoice::total_quantity('plus', $products[$i]['quantity'], $billProduct->product_id);
                }
                //Product Stock Report
                $type = 'bill';
                $type_id = $bill->id;
                StockReport::where('type', '=', 'bill')->where('type_id', '=', $bill->id)->where('workspace',$request->workspace_id)->where('created_by', creatorId())->delete();
                $description = $products[$i]['quantity'] . '  ' . __(' quantity purchase in bill') . ' ' . Bill::billNumberFormat($bill->bill_id);

                if (empty($products[$i]['id'])) {
                    Bill::addProductStock($products[$i]['item'], $products[$i]['quantity'], $type, $description, $type_id);
                }
            }
        }
        event(new UpdateBill($bill, $request));
        return response()->json(['status'=>'success', 'message' => 'Bill successfully updated!']);

    }

    public function billDelete(Request $request,$id){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }
        $bill = Bill::where('id',$id)->where('workspace',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$bill){
            return response()->json(['status'=>'error','message'=>'Bill Not Found!']);
        }

        if ($bill->vendor_id != 0 && $bill->status != 0) {
            AccountUtility::updateUserBalance('vendor', $bill->vendor_id, $bill->getTotal(), 'credit');
        }
        BillProduct::where('bill_id', '=', $bill->id)->delete();
        BillAccount::where('ref_id', '=', $bill->id)->delete();

        $bill_payments = BillPayment::where('bill_id', $bill->id)->get();

        if (!empty($bill_payments)) {
            foreach ($bill_payments as $bill_payment) {
                delete_file($bill_payment->add_receipt);
                $bill_payment->delete();
            }
        }

        event(new DestroyBill($bill));
        $bill->delete();

        return response()->json(['status'=>'success', 'message'=>'Bill successfully deleted!']);

    }

    public function paymentList(Request $request)
    {
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }
        $payments = Payment::select('payments.*', 'bank_accounts.bank_name as bank_name', 'bank_accounts.holder_name as holder_name', 'vendors.name as vendors_name','categories.name as category_name')
                ->where('payments.workspace', '=', $request->workspace_id)
                ->where('payments.created_by', creatorId())
                ->join('vendors', 'payments.vendor_id', '=', 'vendors.id')
                ->join('bank_accounts', 'payments.account_id', '=', 'bank_accounts.id')
                ->join('categories', 'payments.category_id', '=', 'categories.id')
                ->get()
                ->map(function($payment){
                    return [
                        'id'                    => $payment->id,
                        'date'                  => company_date_formate($payment->date),
                        'amount'                => currency_format_with_sym($payment->amount),
                        'description'           => $payment->description,
                        'reference'             => $payment->reference,
                        'bank_name'             => $payment->bank_name,
                        'holder_name'           => $payment->holder_name,
                        'vendors_name'          => $payment->vendors_name,
                        'category_name'         => $payment->category_name,
                    ];
        });

        return response()->json(['status'=>'success','data' => $payments]);
    }

    public function paymentStore(Request $request){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $account = BankAccount::where('workspace', $request->workspace_id)->where('created_by', creatorId())->where('id',$request->account_id)->first();
        if(!$account){
            return response()->json(['status'=>'error','message'=>'Account Not Found!']);
        }

        $vendor = Vender::where('workspace', '=',$request->workspace_id)->where('created_by', creatorId())->where('id',$request->vendor_id)->first();
        if(!$vendor){
            return response()->json(['status'=>'error','message'=>'Vendor Not Found!']);
        }
        $category = null;
        if(module_is_active('ProductService'))
        {
            $category = \Workdo\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->where('type','2')->first();
        }
        if(!$category){
            return response()->json(['status'=>'error','message'=>'Category Not Found!']);
        }

        $validator = \Validator::make(
            $request->all(), [
                'date' => 'required|date_format:Y-m-d',
                'amount' => 'required|numeric|gte:0',
                'account_id' => 'required|numeric',
                'vendor_id' => 'required|numeric',
                'category_id' => 'required|numeric',
                'reference' => 'required',
                'description' => 'required',
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $payment                 = new Payment();
        $payment->date           = $request->date;
        $payment->amount         = $request->amount;
        $payment->account_id     = $request->account_id;
        $payment->vendor_id      = $request->vendor_id;
        $payment->category_id    = $request->category_id;
        $payment->payment_method = 0;
        $payment->reference      = $request->reference;
        $payment->description    = $request->description;

        if(!empty($request->add_receipt))
        {
            $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
            $uplaod = upload_file($request,'add_receipt',$fileName,'payment');
            if($uplaod['flag'] == 1)
            {
                $url = $uplaod['url'];
            }
            else{
                return response()->json(['status' => 'error','message' => $uplaod['msg']]);
            }

            $payment->add_receipt = $url;
        }
        $payment->workspace      = $request->workspace_id;
        $payment->created_by     = creatorId();
        $payment->save();

        $payment->payment_id = $payment->id;
        $payment->type       = 'Payment';
        $payment->category   = $category->name;
        $payment->user_id    = $payment->vendor_id;
        $payment->user_type  = 'Vendor';
        $payment->account    = $request->account_id;

        Transaction::addTransaction($payment);
        $bill_payment    = new BillPayment();
        $bill_payment->name   = $vendor->name;
        $bill_payment->method = '-';
        $bill_payment->date   = company_date_formate($request->date);
        $bill_payment->amount = currency_format_with_sym($request->amount);
        $bill_payment->bill   = '';

        Transfer::bankAccountBalance($request->account_id, $request->amount, 'debit');

        event(new CreatePayment($request,$bill_payment,$payment));

        AccountUtility::userBalance('vendor', $vendor->id, $request->amount, 'debit');

        if(!empty(company_setting('Bill Payment Create')) && company_setting('Bill Payment Create')  == true)
        {
            $uArr = [
                'payment_name' => $bill_payment->name,
                'payment_bill' => $bill_payment->bill,
                'payment_amount' => $bill_payment->amount,
                'payment_date' => $bill_payment->date,
                'payment_method'=> $bill_payment->method

            ];
            try
            {
                $resp = EmailTemplate::sendEmailTemplate('Bill Payment Create', [$vendor->id => $vendor->email], $uArr);
            }
            catch (\Exception $e) {
                $resp['error'] = $e->getMessage();
            }
            return response()->json(['status'=>'success', 'message' => 'Payment successfully created!' . ((isset($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : '')]);
        }

        return response()->json(['status'=>'success', 'message' => 'Payment successfully created!']);

    }

    public function paymentUpdate(Request $request, $id){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $account = BankAccount::where('workspace', $request->workspace_id)->where('created_by', creatorId())->where('id',$request->account_id)->first();
        if(!$account){
            return response()->json(['status'=>'error','message'=>'Account Not Found!']);
        }

        $vendor = Vender::where('workspace', '=',$request->workspace_id)->where('created_by', creatorId())->where('id',$request->vendor_id)->first();
        if(!$vendor){
            return response()->json(['status'=>'error','message'=>'Vendor Not Found!']);
        }

        $category = null;
        if(module_is_active('ProductService'))
        {
            $category = \Workdo\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->where('type','2')->first();
        }
        if(!$category){
            return response()->json(['status'=>'error','message'=>'Category Not Found!']);
        }

        $validator = \Validator::make(
            $request->all(), [
                'date' => 'required|date_format:Y-m-d',
                'amount' => 'required|numeric|gte:0',
                'account_id' => 'required|numeric',
                'vendor_id' => 'required|numeric',
                'category_id' => 'required|numeric',
                'reference' => 'required',
                'description' => 'required',
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }
        $payment = Payment::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$payment){
            return response()->json(['status'=>'error','message'=>'Payment Not Found!']);
        }

        AccountUtility::userBalance('vendor', $vendor->id, $payment->amount, 'credit');

        Transfer::bankAccountBalance($payment->account_id, $payment->amount, 'credit');

        $payment->date           = $request->date;
        $payment->amount         = $request->amount;
        $payment->account_id     = $request->account_id;
        $payment->vendor_id      = $request->vendor_id;
        $payment->category_id    = $request->category_id;
        $payment->payment_method = 0;
        $payment->reference      = $request->reference;
        $payment->description    = $request->description;
        if(!empty($request->add_receipt))
        {
            if(!empty($payment->add_receipt))
            {
                delete_file($payment->add_receipt);
            }

            $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
            $uplaod = upload_file($request,'add_receipt',$fileName,'payment');
            if($uplaod['flag'] == 1)
            {
                $url = $uplaod['url'];
            }
            else{
                return response()->json(['status'=>'error','message'=>$uplaod['msg']]);
            }

            $payment->add_receipt = $url;
        }
        $payment->save();

        $payment->category   = $category->name;
        $payment->payment_id = $payment->id;
        $payment->type       = 'Payment';
        $payment->account    = $request->account_id;
        Transaction::editTransaction($payment);

        AccountUtility::userBalance('vendor', $vendor->id, $request->amount, 'debit');

        Transfer::bankAccountBalance($request->account_id, $request->amount, 'debit');

        if(module_is_active('DoubleEntry'))
        {
            $request->merge(['id'=>$payment->id]);
        }

        event(new UpdatePayment($request,$payment));

        return response()->json(['status'=>'success', 'message'=>'Payment successfully updated!']);
    }

    public function paymentDelete(Request $request, $id){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $payment = Payment::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$payment){
            return response()->json(['status'=>'error','message'=>'Payment Not Found!']);
        }

        if(!empty($payment->add_receipt)){
            delete_file($payment->add_receipt);
        }

        event(new DestroyPayment($payment));
        $payment->delete();

        $type = 'Payment';
        $user = 'Vendor';

        Transaction::destroyTransaction($payment->id, $type, $user);

        if($payment->vendor_id != 0)
        {
            AccountUtility::userBalance('vendor', $payment->vendor_id, $payment->amount, 'credit');
        }

        Transfer::bankAccountBalance($payment->account_id, $payment->amount, 'credit');

        return response()->json(['status'=>'success', 'message'=>'Payment successfully deleted!']);
    }

    public function debitNoteList(Request $request){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $bills = Bill::where('created_by', creatorId())->where('workspace',$request->workspace_id)->get();
        $data = [];
        foreach($bills as $bill){
            if (!empty($bill->customdebitNote)){
                foreach ($bill->customdebitNote as $customdebitNote){
                    $data[] = [
                        'id'            => $customdebitNote->id,
                        'bill_number'       => Bill::billNumberFormat($bill->bill_id),
                        'vendor'      => $bill->vendor->name,
                        'date'          => company_date_formate($customdebitNote->date),
                        'amount'        => currency_format_with_sym($customdebitNote->amount),
                        'description'   => $customdebitNote->description,
                        'status'        => \Workdo\Account\Entities\CustomerDebitNotes::$statues[$customdebitNote->status]
                    ];
                }
            }
        }
        return response()->json(['status'=>'success','data'=>$data]);

    }

    public function debitNoteStore(Request $request){
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $bill = Bill::where('workspace',$request->workspace_id)->where('created_by', creatorId())->where('id',$request->bill)->first();
        if(!$bill){
            return response()->json(['status'=>'error','message'=>'Invalid Bill Id!']);
        }
        $statues = CustomerDebitNotes::$statues;

        $validator = \Validator::make(
            $request->all(), [
                'bill' => 'required|numeric',
                'amount' => 'required|numeric|gt:0',
                'date' => 'required|date_format:Y-m-d',
                'status'=>[Rule::in($statues)]
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error','message' => $messages->first()]);
        }

        if($request->amount > $bill->getDue())
        {
            return response()->json(['status'=>'error', 'message' => 'Maximum ' . currency_format_with_sym($bill->getDue()) . ' credit limit of this bill.']);
        }
        $vendor             = Vender::where('user_id',$bill->user_id)->first();
        if(!$vendor){
            return response()->json(['status'=>'error', 'message'=>'Vendor Not Found!']);
        }

        $debit              = new CustomerDebitNotes();
        $debit->bill        = $bill->id;
        $debit->vendor      = $vendor->vendor_id;
        $debit->date        = $request->date;
        $debit->amount      = $request->amount;
        $debit->status      = array_flip($statues)[$request->status];
        $debit->description = $request->description;
        $debit->save();

        AccountUtility::updateUserBalance('vendor', $bill->vendor_id, $request->amount, 'credit');

        AccountUtility::updateDebitnoteBalance('vendor', $vendor->vendor_id, $request->amount, 'debit');

        return response()->json(['status'=>'success', 'message'=>'Debit Note successfully created!']);

    }

    public function debitNoteUpdate(Request $request , $id)
    {
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $bill = Bill::where('workspace',$request->workspace_id)->where('created_by', creatorId())->where('id',$request->bill)->first();
        if(!$bill){
            return response()->json(['status'=>'error','message'=>'Invalid Bill Id!']);
        }

        $statues = CustomerDebitNotes::$statues;
        $validator = \Validator::make(
            $request->all(), [
                'bill' => 'required|numeric',
                'amount' => 'required|numeric|gt:0',
                'date' => 'required|date_format:Y-m-d',
                'status'=>[Rule::in($statues)]
            ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error','message' => $messages->first()]);
        }

        if($request->amount > $bill->getDue())
        {
            return response()->json(['status'=>'error','message'=> 'Maximum ' . currency_format_with_sym($bill->getDue()) . ' credit limit of this bill!']);
        }

        $debit = CustomerDebitNotes::leftjoin('vendors', 'customer_debit_notes.vendor', '=', 'vendors.id')->where('vendors.workspace',$request->workspace_id)->where('vendors.created_by', creatorId())->where('customer_debit_notes.id',$id)->first();
        if(!$debit){
            return response()->json(['status'=>'error','message'=>'Customer Debit Note Not Found!']);
        }

        AccountUtility::userBalance('vendor', $bill->vendor_id, $debit->amount, 'credit');

        AccountUtility::updateDebitnoteBalance('vendor', $bill->vendor_id, $debit->amount, 'credit');

        $debit = CustomerDebitNotes::find($id);
        $debit->date        = $request->date;
        $debit->amount      = $request->amount;
        $debit->description = $request->description;
        $debit->save();
        AccountUtility::userBalance('vendor', $bill->vendor_id, $request->amount, 'debit');

        AccountUtility::updateDebitnoteBalance('vendor', $bill->vendor_id, $request->amount, 'debit');

        return response()->json(['status'=>'success','message'=> 'Debit Note successfully updated!']);
    }

    public function debitNoteDelete(Request $request , $id)
    {
        if (!module_is_active('Account')) {
            return response()->json(['status'=>'error','message'=>'Account Module Not Active!']);
        }

        $debitNote = CustomerDebitNotes::leftjoin('vendors', 'customer_debit_notes.vendor', '=', 'vendors.id')->where('vendors.workspace',$request->workspace_id)->where('vendors.created_by', creatorId())->where('customer_debit_notes.id',$id)->select('customer_debit_notes.*')->first();
        if(!$debitNote){
            return response()->json(['status'=>'error','message'=>'Customer Debit Note Not Found!']);
        }

        CustomerDebitNotes::find($id)->delete();
        AccountUtility::userBalance('vendor', $debitNote->vendor, $debitNote->amount, 'credit');

        AccountUtility::updateDebitnoteBalance('vendor', $debitNote->vendor, $debitNote->amount, 'credit');

        return response()->json(['status'=>'success', 'message'=> 'Debit Note successfully deleted!']);
    }

}
