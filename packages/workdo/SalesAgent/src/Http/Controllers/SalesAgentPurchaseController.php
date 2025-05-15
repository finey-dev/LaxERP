<?php

namespace Workdo\SalesAgent\Http\Controllers;

use Workdo\SalesAgent\DataTables\ProductListDatatable;
use Workdo\SalesAgent\DataTables\InvoiceDatatable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Workdo\SalesAgent\Entities\SalesAgent;
use Workdo\SalesAgent\Entities\Program;
use Workdo\SalesAgent\Entities\SalesAgentPurchase;
use Workdo\SalesAgent\Entities\PurchaseOrderItems;
use Workdo\SalesAgent\Entities\ProgramItems;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use \Workdo\ProductService\Entities\ProductService;
use App\Models\Invoice;
use App\Models\BankTransferPayment;
use App\Models\InvoicePayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Workdo\SalesAgent\DataTables\PurchaseOrderDatatable;
use Workdo\SalesAgent\Events\SalesAgentOrderCreate;
use Workdo\SalesAgent\Events\SalesAgentOrderDelete;
use Workdo\SalesAgent\Events\SalesAgentOrderStatusUpdated;
use Workdo\SalesAgent\Entities\SalesAgentUtility;


class SalesAgentPurchaseController extends Controller
{

    public function index(PurchaseOrderDatatable $dataTable)
    {
        return $dataTable->render('sales-agent::purchase.index');
    }

    public function create()
    {
        $currentDate = Carbon::now()->format('Y-m-d');

        $programs = Program::where('workspace', getActiveWorkSpace())
            ->where('from_date', '<=', $currentDate)
            ->where('to_date', '>=', $currentDate)
            ->whereRaw('FIND_IN_SET(?, sales_agents_applicable)', [\Auth::user()->id])->get()->pluck('name', 'id');

        // $programs = Program::getProgramsBySalesAgentId();
        $salesAgents = User::where('workspace_id', getActiveWorkSpace())
            ->leftjoin('customers', 'users.id', '=', 'customers.user_id')
            ->leftjoin('sales_agents', 'users.id', '=', 'sales_agents.user_id')
            ->where('users.type', 'salesagent')
            ->where('users.is_disable', '1')
            ->where('sales_agents.is_agent_active', '1')
            ->select('users.name as name', 'users.email as email', 'users.id as id')
            ->get();

        $purchaseOrderNumber = SalesAgent::purchaseOrderNumberFormat($this->purchaseOrderNumber());

        return view('sales-agent::purchase.create', compact('programs', 'salesAgents', 'purchaseOrderNumber'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('salesagent purchase create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'order_date' => 'required',
                    'delivery_date' => 'required',
                    'order_details' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $order                       = new SalesAgentPurchase();
            $order->user_id              = \Auth::user()->id;
            $order->purchaseOrder_id     = $this->purchaseOrderNumber();
            $order->order_number         = $request->order_number;
            $order->order_date           = $request->order_date;
            $order->delivery_date        = $request->delivery_date;
            $order->delivery_status      = 0;
            $order->order_status         = 0;
            $order->created_by           = \Auth::user()->id;
            $order->workspace            = getActiveWorkSpace();

            $order->save();

            $products = $request->order_details;

            for ($i = 0; $i < count($products); $i++) {
                $OrderItem                       = new PurchaseOrderItems();
                $OrderItem->purchase_order_id    = $order->id;
                $OrderItem->program_id           = $products[$i]['program_id'];
                $OrderItem->item_id              = $products[$i]['item'];
                $OrderItem->quantity             = $products[$i]['quantity'];
                $OrderItem->tax                  = $products[$i]['tax'];
                $OrderItem->discount             = $products[$i]['discountHidden'];
                $OrderItem->price                = $products[$i]['price'];
                $OrderItem->description          = $products[$i]['description'];
                $OrderItem->save();
                $OrderItems[$i] = $OrderItem;
            }

            event(new SalesAgentOrderCreate($request, $order, $OrderItems));

            return redirect()->route('salesagent.purchase.order.index')->with('success', __('Purchase Order successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id)
    {
        try {
            $id       = Crypt::decrypt($id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Purchase Order Not Found.'));
        }
        $purchaseOrder    = SalesAgentPurchase::with('items')->find($id);
        if ($purchaseOrder->workspace == getActiveWorkSpace()) {
            $salesagent       = $purchaseOrder->salesagent;

            return view('sales-agent::purchase.show', compact('salesagent', 'purchaseOrder'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        $order     = SalesAgentPurchase::find($id);
        $OrderItems     =  PurchaseOrderItems::find($order->id);

        if ($order) {
            $order->delete();
        }

        if ($OrderItems) {
            $OrderItems->delete();
        }

        event(new SalesAgentOrderDelete($order, $OrderItems));

        return redirect()->back()->with('success', __('Purchase Order successfully deleted.'));
    }

    function purchaseOrderNumber()
    {
        $latest = SalesAgentPurchase::where('workspace', getActiveWorkSpace())->latest()->first();
        if (!$latest) {
            return 1;
        }
        return $latest->purchaseOrder_id + 1;
    }

    public function settingsCreate()
    {
        return view('sales-agent::purchase.purchaseSetting');
    }

    public function settings(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'sales_agent_purchase_order_prefix' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        } else {
            $post['sales_agent_purchase_order_prefix'] = $request->sales_agent_purchase_order_prefix;
            SalesAgentUtility::saveSettings($post);

            return redirect()->back()->with('success', 'Sales Agent setting save sucessfully.');
        }
    }

    public function getProgramItems(Request $request)
    {
        if ($request->program_id !== null) {
            $program    = Program::find($request->program_id);
            $program_details = ProgramItems::where('program_id', $request->program_id)->get();
            $productServices = ProductService::where('workspace_id', getActiveWorkSpace());

            foreach ($program_details as $key => $program_detail) {
                $items[$key]         = $program_detail->items;
            }
            $flattenedArray = [];

            foreach ($items as $subArray) {
                $flattenedArray = array_merge($flattenedArray, explode(',', $subArray));
            }

            $productServices->whereIn('id', $flattenedArray);

            $data['productServices'] =  $productServices->get()->pluck('name', 'id');
            $data['program_discount_type'] = $program->discount_type;
            return response()->json($data);
        }
    }

    public function product(Request $request)
    {
        $data['product']     = $product = \Workdo\ProductService\Entities\ProductService::find($request->product_id);
        $data['unit']        = !empty($product) ? ((!empty($product->unit())) ? $product->unit()->name : '') : '';
        $data['taxRate']     = $taxRate = !empty($product) ? (!empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0) : 0;
        $data['taxes']       =  !empty($product) ? (!empty($product->tax_id) ? $product->tax($product->tax_id) : 0) : 0;
        $salePrice           = !empty($product) ?  $product->purchase_price : 0;
        $quantity            = 1;
        $taxPrice            = !empty($product) ? (($taxRate / 100) * ($salePrice * $quantity)) : 0;
        $data['totalAmount'] = !empty($product) ?  ($salePrice * $quantity) : 0;
        $data['discount']    = SalesAgentPurchase::getdiscount($request->program_id, $request->product_id, $salePrice);
        $data['program_discount_type']     = Program::where('id', '=', $request->program_id)->pluck('discount_type')->first();
        $data['discount_range']            = SalesAgentPurchase::getDiscountRange($request->program_id, $request->product_id);
        return json_encode($data);
    }

    public function productList(ProductListDatatable $dataTable)
    {
        $programs = DB::table('sales_agents_programs')->whereRaw('FIND_IN_SET(?, sales_agents_applicable)', [Auth::user()->id])->pluck('name', 'id');
        return $dataTable->render('sales-agent::programs.productList', compact('programs'));
    }

    public function updateOrderStatus($order_id, $key = '')
    {
        if ($order_id != '' && $key != '') {
            $check  = SalesAgentPurchase::find($order_id)->exists();
            if ($check) {
                $purchaseOrder                  = SalesAgentPurchase::find($order_id);
                $purchaseOrder->order_status    = $key;
                $purchaseOrder->save();

                event(new SalesAgentOrderStatusUpdated($purchaseOrder));

                return redirect()->back()->with('success', __('purchaseOrder Status Updated successfully.'));
            } else {

                return redirect()->back()->with('error', __('Order Not Found!'));
            }
        } else {

            return redirect()->back()->with('error', __('Something Went Wrong!'));
        }
    }


    public function invoiceCreate($order_id)
    {
        $category       = \Workdo\ProductService\Entities\Category::where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->where('type', 1)->get()->pluck('name', 'id');
        $purchaseOrder  = SalesAgentPurchase::find($order_id);
        return view('sales-agent::purchase.invoiceCreate', compact('category', 'purchaseOrder'));
    }

    public function invoiceIndex(InvoiceDatatable $dataTable)
    {
        if (Auth::user()->type == 'salesagent') {
            $status     = Invoice::$statues;
            return $dataTable->render('sales-agent::purchase.invoiceIndex', compact('status'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function invoiceShow($e_id)
    {
        if (Auth::user()->type == 'salesagent') {
            try {
                $id       = Crypt::decrypt($e_id);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Invoice Not Found.'));
            }
            $invoice = Invoice::find($id);
            if ($invoice) {
                $bank_transfer_payments = BankTransferPayment::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('type', 'invoice')->where('request', $invoice->id)->get();
                if ($invoice->workspace == getActiveWorkSpace()) {
                    $invoicePayment = InvoicePayment::where('invoice_id', $invoice->id)->first();
                    if (module_is_active('SalesAgent')) {
                        $customer = \Workdo\SalesAgent\Entities\Customer::where('user_id', $invoice->user_id)->where('workspace', getActiveWorkSpace())->first();
                    } else {
                        $customer = $invoice->customer;
                    }
                    if (module_is_active('CustomField')) {
                        $invoice->customField = \Workdo\CustomField\Entities\CustomField::getData($invoice, 'Base', 'Invoice');
                        $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'Base')->where('sub_module', 'Invoice')->get();
                    } else {
                        $customFields = null;
                    }
                    $iteams   = $invoice->items;

                    return view('sales-agent::purchase.invoiceShow', compact('invoice', 'customer', 'iteams', 'invoicePayment', 'customFields', 'bank_transfer_payments'));
                } else {
                    return redirect()->back()->with('error', __('Permission denied.'));
                }
            } else {
                return redirect()->back()->with('error', __('This invoice is deleted.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
