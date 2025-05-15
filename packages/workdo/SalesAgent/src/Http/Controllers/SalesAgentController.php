<?php

namespace Workdo\SalesAgent\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use App\Models\WorkSpace;
use Termwind\Components\Dd;
use Workdo\SalesAgent\DataTables\SalesAgentDatatable;
use Workdo\SalesAgent\Entities\SalesAgent;
use Workdo\SalesAgent\Entities\Program;
use Workdo\SalesAgent\Entities\Customer;
use Workdo\SalesAgent\Entities\SalesAgentPurchase;
use Workdo\SalesAgent\Events\SalesAgentCreate;
use Workdo\SalesAgent\Events\SalesAgentDelete;
use Workdo\SalesAgent\Events\SalesAgentUpdate;
use Workdo\SalesAgent\Entities\SalesAgentUtility;


class SalesAgentController extends Controller
{
    public function __construct()
    {
        if (module_is_active('GoogleAuthentication')) {
            $this->middleware('2fa');
        }
    }
    public function dashboard(Request $request)
    {
        $salesAgents = User::where('workspace_id', getActiveWorkSpace())
            ->leftjoin('customers', 'users.id', '=', 'customers.user_id')
            ->leftjoin('sales_agents', 'users.id', '=', 'sales_agents.user_id')
            ->where('users.type', 'salesagent')
            ->select('users.*', 'customers.*', 'users.name as name', 'users.email as email', 'users.id as id');

        $totalAgents    = $salesAgents->count();
        $activeAgents   = $salesAgents->where('sales_agents.is_agent_active', '1')->count();
        $inactiveAgents = $totalAgents - $activeAgents;
        $salesAgents    = $salesAgents->get();


        $totalPrograms   = Program::where('workspace', getActiveWorkSpace())->count();
        $totalSalesOrders   = SalesAgentPurchase::where('workspace', getActiveWorkSpace())->count();

        $PurchaseOrderData = [];
        foreach (SalesAgentPurchase::$purchaseOrder as $key => $order) {
            $PurchaseOrder          = SalesAgentPurchase::where('workspace', '=', getActiveWorkSpace())->where('order_status', $key)->orderBy('order', 'ASC')->count();
            $PurchaseOrderData[]    = $PurchaseOrder;
        }

        $activeworkspace = WorkSpace::where('id', getActiveWorkSpace())->pluck('name')->first();
        return view('sales-agent::dashboard.dashboard', compact('totalAgents', 'activeAgents', 'inactiveAgents', 'totalPrograms', 'totalSalesOrders', 'salesAgents', 'PurchaseOrderData', 'activeworkspace'));
    }

    public function index(SalesAgentDatatable $dataTable)
    {
        return $dataTable->render('sales-agent::salesagent.index');
    }

    public function create()
    {
        if (\Auth::user()->isAbleTo('salesagent create')) {
            $customFields = null;
            if (module_is_active('CustomField')) {
                $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id', getActiveWorkSpace())->where('module', '=', 'SalesAgent')->where('sub_module', 'Sales Agent')->get();
            } else {
                $customFields = null;
            }
            return view('sales-agent::salesagent.create', compact('customFields'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
        return view('sales-agent::salesagent.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('salesagent create')) {
            $canUse =  PlanCheck('User', Auth::user()->id);
            if ($canUse == false) {
                return redirect()->back()->with('error', 'You have maxed out the total number of Agents allowed on your current plan');
            }
            // validation
            $rules = [
                'name'              => 'required|max:120',
                'contact'           => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
                'billing_name'      => 'required',
                'billing_phone'     => 'required',
                'billing_address'   => 'required',
                'billing_city'      => 'required',
                'billing_state'     => 'required',
                'billing_country'   => 'required',
                'billing_zip'       => 'required',
                'shipping_name'     => 'required',
                'shipping_phone'    => 'required',
                'shipping_address'  => 'required',
                'shipping_city'     => 'required',
                'shipping_state'    => 'required',
                'shipping_country'  => 'required',
                'shipping_zip'      => 'required',
            ];
            if (empty($request->user_id)) {
                $rules = array_merge($rules, [
                    'email'    => 'required|email|unique:users',
                    'password' => 'required',
                    'contact'  => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
                ]);
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->route('salesagents.index')->with('error', $validator->getMessageBag()->first());
            }

            $roles = Role::where('name', 'salesagent')->where('guard_name', 'web')->where('created_by', creatorId())->first();
            if (empty($roles)) {
                return redirect()->back()->with('error', __('Agent Role Not found !'));
            }

            // User Create
            if (!empty($request->user_id)) {
                $user = User::find($request->user_id);
                if (empty($user)) {
                    return redirect()->back()->with('error', __('Something went wrong please try again.'));
                }
                if ($user->name != $request->name || $user->email != $request->email) {
                    $user->name = $request->name;
                    $user->email = $request->input('email');
                    $user->save();
                }
            } else {
                $userpassword               = $request->input('password');
                $user['name']               = $request->input('name');
                $user['email']              = $request->input('email');
                $user['mobile_no']          = $request->input('contact');
                $user['password']           = \Hash::make($userpassword);
                $user['email_verified_at']  = date('Y-m-d h:i:s');
                $user['lang']               = 'en';
                $user['type']               = $roles->name;
                $user['created_by']         = \Auth::user()->id;
                $user['workspace_id']       = getActiveWorkSpace();
                $user['active_workspace']   = getActiveWorkSpace();
                $user                       = User::create($user);
                $user->addRole($roles);
            }
            // Customer  Create
            $customer = Customer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'user_id'           => $user->id,
                    'customer_id'       => $this->customerNumber(),
                    // 'name'              => $request->name ?? null,
                    // 'contact'           => $request->contact ?? null,
                    // 'email'             => $user->email ?? null,
                    'tax_number'        => $request->tax_number ?? null,
                    'lang'              => $user->lang ?? '',
                    'workspace'         => getActiveWorkSpace(),
                    'created_by'        => Auth::user()->id,
                    'password'          => null,
                    'billing_name'      => $request->billing_name ?? null,
                    'billing_country'   => $request->billing_country ?? null,
                    'billing_state'     => $request->billing_state ?? null,
                    'billing_city'      => $request->billing_city ?? null,
                    'billing_phone'     => $request->billing_phone ?? null,
                    'billing_zip'       => $request->billing_zip ?? null,
                    'billing_address'   => $request->billing_address ?? null,
                    'shipping_name'     => $request->shipping_name ?? null,
                    'shipping_country'  => $request->shipping_country ?? null,
                    'shipping_state'    => $request->shipping_state ?? null,
                    'shipping_city'     => $request->shipping_city ?? null,
                    'shipping_phone'    => $request->shipping_phone ?? null,
                    'shipping_zip'      => $request->shipping_zip ?? null,
                    'shipping_address'  => $request->shipping_address ?? null,
                ]
            );
            //  SalesAgent Create
            $salesAgent = SalesAgent::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'user_id'      => $user->id,
                    'agent_id'     => $this->agentNumber(),
                    'customer_id'  => $customer->customer_id,
                    'workspace'    => getActiveWorkSpace(),
                    'created_by'   => Auth::user()->id,
                ]
            );

            if (module_is_active('CustomField')) {
                \Workdo\CustomField\Entities\CustomField::saveData($salesAgent, $request->customField);
            }

            event(new SalesAgentCreate($request, $salesAgent));
            return redirect()->back()->with('success', __('The SalesAgent has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id)
    {
        try {
            $id       = \Crypt::decrypt($id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Purchase Order Not Found.'));
        }

        $userId     = \Auth::user()->id;
        $salesAgent = User::where('workspace_id', getActiveWorkSpace())
            ->leftjoin('customers', 'users.id', '=', 'customers.user_id')
            ->leftjoin('sales_agents', 'users.id', '=', 'sales_agents.user_id')
            ->where('users.type', 'salesagent')
            ->where('users.id', $id)
            ->select('users.*', 'customers.*', 'users.name as name', 'users.email as email', 'users.id as id')
            ->first();

        $programs       = Program::where(function ($query) use ($id) {
            $query->whereRaw('FIND_IN_SET(?, sales_agents_applicable)', [$id])
                ->orWhereRaw('FIND_IN_SET(?, sales_agents_view)', [$id]);
        })->get();

        $purchaseOrders = SalesAgentPurchase::where('workspace', getActiveWorkSpace())
            ->where('created_by', '=', $id);

        $totalPurchaseOrders    = $purchaseOrders->get();
        $totalPrograms          = $programs->count();
        $totalSalesOrders       = $purchaseOrders->count();


        $totalInvoiceCreated = $purchaseOrders->where('invoice_id', '!=', null)->count();
        $totalDeliveredOrders = $purchaseOrders->where('order_status', '=', 3)->count();

        $totalSalesOrdersValue = [];
        foreach ($totalPurchaseOrders as $order) {
            $totalSalesOrdersValue[]  = $order->getTotal();
        }

        $totalSalesOrdersValue = array_sum($totalSalesOrdersValue);
        if (module_is_active('CustomField')) {
            $salesAgent->customField = \Workdo\CustomField\Entities\CustomField::getData($salesAgent, 'SalesAgent', 'Sales Agent');
            $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'SalesAgent')->where('sub_module', 'Sales Agent')->get();
        } else {
            $customFields = null;
        }

        return view('sales-agent::salesagent.show', compact('salesAgent', 'programs', 'totalPurchaseOrders', 'totalPrograms', 'totalSalesOrders', 'totalSalesOrdersValue', 'totalInvoiceCreated', 'totalDeliveredOrders', 'customFields'));
    }

    public function edit($id)
    {
        if (Auth::user()->isAbleTo('salesagent edit')) {

            $user         = User::where('id', $id)->where('workspace_id', getActiveWorkSpace())->first();
            $agent        = SalesAgent::where('user_id', $id)->where('workspace', getActiveWorkSpace())->first();
            $salesAgent   = Customer::where('user_id', $id)->where('workspace', getActiveWorkSpace())->first();

            $customFields = null;
            if (!empty($agent)) {
                if (module_is_active('CustomField')) {
                    $salesAgent->customField  = \Workdo\CustomField\Entities\CustomField::getData($salesAgent, 'SalesAgent', 'Sales Agent');
                    $customFields             = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'SalesAgent')->where('sub_module', 'Sales Agent')->get();
                } else {
                    $customFields = null;
                }
            }
            return view('sales-agent::salesagent.edit', compact('salesAgent', 'user', 'customFields'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('salesagent create')) {

            $validator = \Validator::make($request->all(),  [
                'name'              => 'required|max:120',
                'contact'           => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
                'billing_name'      => 'required',
                'billing_phone'     => 'required',
                'billing_address'   => 'required',
                'billing_city'      => 'required',
                'billing_state'     => 'required',
                'billing_country'   => 'required',
                'billing_zip'       => 'required',
                "shipping_name"     => "required",
                "shipping_phone"    => "required",
                "shipping_address"  => "required",
                "shipping_city"     => "required",
                "shipping_state"    => "required",
                "shipping_country"  => "required",
                "shipping_zip"      => "required",
            ]);

            if ($validator->fails()) {
                return redirect()->route('salesagents.index')->with('error', $validator->getMessageBag()->first());
            }

            $roles = Role::where('name', 'salesagent')->where('guard_name', 'web')->where('created_by', creatorId())->first();
            if (empty($roles)) {
                return redirect()->back()->with('error', __('SalesAgent Role Not found !'));
            }


            $user = User::find($id);
            if (empty($user)) {
                return redirect()->back()->with('error', __('Something went wrong please try again.'));
            }

            if ($user->name != $request->name || $user->email != $request->email) {
                $user->name = $request->name;
                $user->email = $request->email;
                $user->mobile_no = $request->contact;
                $user->save();
            }

            $customer_id_exists = !Customer::where('user_id', $id)->exists();
            $customer = Customer::updateOrCreate(
                ['user_id' => $id],
                array_merge(
                    [
                        'user_id'           => $user->id,
                        'tax_number'        => $request->tax_number ?? null,
                        'workspace'         => getActiveWorkSpace(),
                        'created_by'        => Auth::user()->id,
                        'password'          => null,
                        'billing_name'      => $request->billing_name ?? null,
                        'billing_country'   => $request->billing_country ?? null,
                        'billing_state'     => $request->billing_state ?? null,
                        'billing_city'      => $request->billing_city ?? null,
                        'billing_phone'     => $request->billing_phone ?? null,
                        'billing_zip'       => $request->billing_zip ?? null,
                        'billing_address'   => $request->billing_address ?? null,
                        'shipping_name'     => $request->shipping_name ?? null,
                        'shipping_country'  => $request->shipping_country ?? null,
                        'shipping_state'    => $request->shipping_state ?? null,
                        'shipping_city'     => $request->shipping_city ?? null,
                        'shipping_phone'    => $request->shipping_phone ?? null,
                        'shipping_zip'      => $request->shipping_zip ?? null,
                        'shipping_address'  => $request->shipping_address ?? null,
                    ],
                    $customer_id_exists ? ['customer_id' => $this->customerNumber()] : []
                )
            );

            $SalesAgent = SalesAgent::firstOrNew(['user_id' => $id]);
            if (!$SalesAgent->exists) {
                $SalesAgent->user_id        = $user->id;
                $SalesAgent->workspace      = getActiveWorkSpace();
                $SalesAgent->created_by     = Auth::user()->id;
                if ($customer_id_exists) {
                    $SalesAgent->customer_id    = $customer->customer_id;
                }
                $SalesAgent->save();
            }

            if (module_is_active('CustomField')) {
                \Workdo\CustomField\Entities\CustomField::saveData($SalesAgent, $request->customField);
            }

            event(new SalesAgentUpdate($request, $SalesAgent));
            return redirect()->back()->with('success', __('Sales Agent Updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        $Customer     = Customer::where('user_id', $id)->where('workspace', getActiveWorkSpace())->first();
        $SalesAgent   = SalesAgent::where('user_id', '=', $id)->first();

        if (Auth::user()->isAbleTo('salesagent delete')) {
            if ($SalesAgent->workspace == getActiveWorkSpace()) {
                if (module_is_active('CustomField')) {
                    $customFields = \Workdo\CustomField\Entities\CustomField::where('module', 'SalesAgent')->where('sub_module', 'Sales Agent')->get();
                    foreach ($customFields as $customField) {
                        $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $SalesAgent->id)->where('field_id', $customField->id)->first();
                        if (!empty($value)) {
                            $value->delete();
                        }
                    }
                }
                $Customer->delete();
                $SalesAgent->delete();

                event(new SalesAgentDelete($SalesAgent));

                return redirect()->route('salesagents.index')->with('success', __('Sales Agents successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function customerNumber()
    {
        return Customer::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->max('customer_id') + 1;
    }

    function agentNumber()
    {
        $latest = SalesAgent::where('workspace', getActiveWorkSpace())->latest()->first();
        if (!$latest) {
            return 1;
        }
        return $latest->agent_id + 1;
    }

    public function setting(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'salesagent_prefix' => 'required',
                // 'vendor_prefix' => 'required',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        } else {
            $post['salesagent_prefix'] = $request->salesagent_prefix;
            SalesAgentUtility::saveSettings($post);
            return redirect()->back()->with('success', 'Sales Agent setting save sucessfully.');
        }
    }

    public function changeSalesAgentStatus(Request $request)
    {
        if (isset($request->user_id) && isset($request->is_enable_login)) {
            $salesAgent = User::where('id', $request->user_id)->first();
            $salesAgent->is_enable_login = $request->is_enable_login;
            $salesAgent->save();

            $data['message'] =  __('The status has been changed successfully');
            $data['status'] = 200;
            return $data;
        }

        $data['message'] = __('Something Went Wrong!!');
        $data['status'] = 201;
        return $data;
    }
}
