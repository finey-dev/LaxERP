<?php

namespace Workdo\MachineRepairManagement\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;
use Workdo\MachineRepairManagement\Entities\Machine;
use Workdo\MachineRepairManagement\Entities\MachineInvoice;
use Workdo\MachineRepairManagement\Entities\MachineInvoiceDiagnosis;
use Workdo\MachineRepairManagement\Entities\MachineInvoicePayment;
use Workdo\MachineRepairManagement\Entities\MachineRepairRequest;
use Workdo\MachineRepairManagement\Events\CreateDiagnosis;
use Workdo\MachineRepairManagement\Events\CreatePaymentDiagnosis;
use Workdo\MachineRepairManagement\Events\DestroyDiagnosis;
use Workdo\MachineRepairManagement\Events\DestroyPaymentDiagnosis;
use Workdo\MachineRepairManagement\Events\ProductDestroyDiagnosis;
use Workdo\MachineRepairManagement\Events\UpdateDiagnosis;

class MachinesInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if(Auth::user()->isAbleTo('machine diagnosis manage'))
        {
            $status = MachineInvoice::$statues;

            if(Auth::user()->type != 'company')
            {
                $query = MachineInvoice::where('created_by',creatorId())->where('workspace',getActiveWorkSpace());
            }
            else
            {
                $query = MachineInvoice::where('workspace',getActiveWorkSpace());
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
            if(isset($request->status))
            {
                $query->where('status', $request->status);
            }
            $invoices = $query->get();
            return view('machine-repair-management::invoice.index', compact('invoices', 'status'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function Grid(Request $request)
    {
        if(Auth::user()->isAbleTo('machine diagnosis manage'))
        {

            $status = MachineInvoice::$statues;

            if(Auth::user()->type != 'company')
            {
                $query = MachineInvoice::where('created_by',creatorId())->where('workspace',getActiveWorkSpace());
            }
            else
            {
                $query = MachineInvoice::where('workspace',getActiveWorkSpace());
            }

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
            if(isset($request->status))
            {
                $query->where('status', $request->status);
            }
            $invoices = $query->get();
            return view('machine-repair-management::invoice.grid', compact('invoices', 'status'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($rid)
    {
        if(module_is_active('ProductService'))
        {
            if(Auth::user()->isAbleTo('machine diagnosis create'))
            {
                try {
                    $id       = Crypt::decrypt($rid);
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', __('Invoice Not Found.'));
                }

                $invoice_number = MachineInvoice::machineInvoiceNumberFormat($this->invoiceNumber());
                $category = [];
                // $projects = [];
                $taxs = [];
                if(module_is_active('ProductService'))
                    {
                        $taxs = \Workdo\ProductService\Entities\Tax::where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
                    }
                if(isset($id) && $id == 0 || isset($id) && $id == null){
                    // $requests = MachineRepairRequest::where('staff_id','!=',null)->where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('id', 'id');
                    $requests = MachineRepairRequest::where('staff_id', '!=', null)
                        ->where('created_by', creatorId())
                        ->where('workspace', getActiveWorkSpace())
                        ->whereNotIn('id', function($query) {
                            $query->select('request_id')->from('machine_invoices');
                        })
                        ->get()
                        ->pluck('id', 'id');
                    return view('machine-repair-management::invoice.create', compact('invoice_number','taxs','requests'));
                }else{
                    $repair_request = MachineRepairRequest::find($id);
                    $machine_details = Machine::find($repair_request->machine_id);
                    return view('machine-repair-management::invoice.create', compact('invoice_number','taxs','repair_request','machine_details'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->route('machine-repair-management::invoice.index')->with('error', __('Please Enable Product & Service Module'));
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if(Auth::user()->isAbleTo('machine diagnosis create'))
        {
            if($request->invoice_type == "product")
            {
                $validator = \Validator::make(
                    $request->all(), [
                                    'request_id' => 'required',
                                    'issue_date' => 'required',
                                    'due_date' => 'required',
                                    'service_charge' => 'required',
                                    'estimated_time' => 'required',
                                    'items' => 'required',
                                ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                $status = MachineInvoice::$statues;
                $invoice                 = new MachineInvoice();
                
                if(isset($request->request_id)){
                    $repair_request = MachineRepairRequest::find($request->request_id);
                }

                $invoice->invoice_id     = $this->invoiceNumber();
                $invoice->request_id     = $request->request_id;
                $invoice->customer_name  = isset($repair_request->customer_name) ? $repair_request->customer_name : '';
                $invoice->customer_email = isset($repair_request->customer_email) ? $repair_request->customer_email : '';
                $invoice->status         = 0;
                $invoice->issue_date     = $request->issue_date;
                $invoice->due_date       = $request->due_date;
                $invoice->estimated_time = $request->estimated_time;
                $invoice->service_charge = $request->service_charge;
                $invoice->workspace      = getActiveWorkSpace();
                $invoice->created_by     = creatorId();

                $invoice->save();
                $products = $request->items;

                for($i = 0; $i < count($products); $i++)
                {
                    $invoiceProduct                 = new MachineInvoiceDiagnosis();
                    $invoiceProduct->invoice_id     = $invoice->id;
                    $invoiceProduct->request_id     = $request->request_id;
                    $invoiceProduct->product_type   = $products[$i]['product_type'];
                    $invoiceProduct->product_id     = $products[$i]['item'];
                    $invoiceProduct->quantity       = $products[$i]['quantity'];
                    $invoiceProduct->tax            = $products[$i]['tax'];
                    $invoiceProduct->discount       = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                    $invoiceProduct->price          = $products[$i]['price'];
                    $invoiceProduct->description    = str_replace( array( '\'', '"', '`','{',"\n"), ' ', $products[$i]['description']);
                    $invoiceProduct->save();

                    if(module_is_active('ProductService'))
                    {
                        MachineInvoice::total_quantity('minus',$invoiceProduct->quantity,$invoiceProduct->product_id);
                    }

                    if(isset($request->request_id)){
                        $machine = Machine::find($repair_request->machine_id);
                        $machine->last_maintenance_date = $request->issue_date;
                        $machine->save();


                        $repair_request = MachineRepairRequest::find($request->request_id);
                        $repair_request->status = 'Completed';
                        $repair_request->save();
                    }

                }
                event(new CreateDiagnosis($request,$invoice));


                return redirect()->route('machine-repair-invoice.index', $invoice->id)->with('success', __('The machine repair invoice has been created successfully.'));

            }
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
    public function show($m_id)
    {
        if(Auth::user()->isAbleTo('machine diagnosis show'))
        {
            try {
                $id       = Crypt::decrypt($m_id);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Invoice Not Found.'));
            }
            $invoice = MachineInvoice::find($id);
            if($invoice)
            {
                // $bank_transfer_payments = BankTransferPayment::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->where('type','invoice')->where('request',$invoice->id)->get();
                if($invoice->workspace == getActiveWorkSpace())
                {
                    $invoicePayment = MachineInvoicePayment::where('invoice_id', $invoice->id)->first();
                    // $invoice_attachment = InvoiceAttechment::where('invoice_id', $invoice->id)->get();
                    $iteams   = $invoice->items;

                    return view('machine-repair-management::invoice.view', compact('invoice', 'iteams', 'invoicePayment'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Permission denied.'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('This machine invoice is deleted.'));
            }
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
    public function edit($e_id)
    {
        if(module_is_active('ProductService'))
        {
            if(Auth::user()->isAbleTo('machine diagnosis edit'))
            {
                try {
                    $id       = Crypt::decrypt($e_id);
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', __('Invoice Not Found.'));
                }
                $invoice = MachineInvoice::find($id);

                $invoice_number = MachineInvoice::machineInvoiceNumberFormat($invoice->invoice_id);

                // $requests = MachineRepairRequest::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('id', 'id');
                $requests = MachineRepairRequest::where('staff_id', '!=', null)
                           ->where('created_by', creatorId())
                           ->where('workspace', getActiveWorkSpace())
                           ->whereNotIn('id', function($query) use ($id,$invoice) {
                               $query->select('request_id')->from('machine_invoices')->where('request_id', '!=', $id)->where('request_id', '!=', $invoice->request_id);
                           })
                           ->get()
                           ->pluck('id', 'id');

                $taxs = [];
                if(module_is_active('ProductService'))
                {
                    $taxs = \Workdo\ProductService\Entities\Tax::where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
                }

                return view('machine-repair-management::invoice.edit', compact('invoice', 'invoice_number', 'requests', 'taxs'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return redirect()->route('machine-repair-invoice.index')->with('error', __('Please Enable Product & Service Module'));
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
        if(Auth::user()->isAbleTo('machine diagnosis edit'))
        {
            $invoice = MachineInvoice::find($id);
            if($invoice->workspace == getActiveWorkSpace())
            {
                if($request->invoice_type == "product")
                {
                    $validator = \Validator::make(
                        $request->all(), [
                                        'request_id' => 'required',
                                        'issue_date' => 'required',
                                        'due_date' => 'required',
                                        'service_charge' => 'required',
                                        'estimated_time' => 'required',
                                        'items' => 'required',
                                    ]
                    );
                    if($validator->fails())
                    {
                        $messages = $validator->getMessageBag();

                        return redirect()->route('invoice.index')->with('error', $messages->first());
                    }
                    
                    if(isset($request->request_id)){
                        $repair_request = MachineRepairRequest::find($request->request_id);
                    }

                    $invoice->request_id     = $request->request_id;
                    $invoice->customer_name  = isset($repair_request->customer_name) ? $repair_request->customer_name : '';
                    $invoice->customer_email = isset($repair_request->customer_email) ? $repair_request->customer_email : '';
                    $invoice->issue_date     = $request->issue_date;
                    $invoice->due_date       = $request->due_date;
                    $invoice->estimated_time = $request->estimated_time;
                    $invoice->service_charge = $request->service_charge;
    
                    $invoice->save();
                    
                    $products = $request->items;
                    for($i = 0; $i < count($products); $i++)
                    {
                        $invoiceProduct = MachineInvoiceDiagnosis::find($products[$i]['id']);

                        if($invoiceProduct == null)
                        {
                            $invoiceProduct             = new MachineInvoiceDiagnosis();
                            $invoiceProduct->invoice_id = $invoice->id;

                            MachineInvoice::total_quantity('minus',$products[$i]['quantity'],$products[$i]['item']);
                        }
                        else
                        {
                            MachineInvoice::total_quantity('plus',$invoiceProduct->quantity,$invoiceProduct->product_id);
                        }

                        if(isset($products[$i]['item']))
                        {
                            $invoiceProduct->product_id = $products[$i]['item'];
                        }
                        $invoiceProduct->request_id = $request->request_id;
                        $invoiceProduct->product_type   = $products[$i]['product_type'];
                        $invoiceProduct->quantity       = $products[$i]['quantity'];
                        $invoiceProduct->tax            = $products[$i]['tax'];
                        $invoiceProduct->discount       = isset($products[$i]['discount']) ? $products[$i]['discount'] : 0;
                        $invoiceProduct->price          = $products[$i]['price'];
                        $invoiceProduct->description    = str_replace( array( '\'', '"', '`','{',"\n"), ' ', $products[$i]['description']);
                        $invoiceProduct->save();

                        //inventory management (Quantity)
                        if($products[$i]['id'] > 0)
                        {
                            MachineInvoice::total_quantity('minus',$products[$i]['quantity'],$invoiceProduct->product_id);
                        }
                    }
                }
                // first parameter request second parameter invoice
                event(new UpdateDiagnosis($request,$invoice));
                return redirect()->route('machine-repair-invoice.index')->with('success', __('The machine repair invoice has been updated successfully.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
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
        if(Auth::user()->isAbleTo('machine diagnosis delete'))
        {
            $invoice = MachineInvoice::find($id);
            if($invoice->workspace == getActiveWorkSpace())
            {
                foreach($invoice->payments as $invoices)
                {
                    if(!empty($invoices->add_receipt))
                    {
                        try
                        {
                            delete_file($invoices->add_receipt);
                        }
                        catch (\Exception $e)
                        {
                        }
                    }
                    $invoices->delete();
                }
                MachineInvoiceDiagnosis::where('invoice_id', '=', $invoice->id)->delete();

                // first parameter invoice
                event(new DestroyDiagnosis($invoice));
                $invoice->delete();

                return redirect()->route('machine-repair-invoice.index')->with('success', __('The machine repair invoice has been deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function payment($invoice_id)
    {
        if(Auth::user()->isAbleTo('machine invoice payment create'))
        {
            $invoice = MachineInvoice::where('id', $invoice_id)->first();

            return view('machine-repair-management::invoice.payment', compact('invoice'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function createPayment(Request $request, $invoice_id)
    {
        if(Auth::user()->isAbleTo('machine invoice payment create'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'date' => 'required',
                                   'amount' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $invoicePayment                 = new MachineInvoicePayment();

            $invoicePayment->invoice_id     = $invoice_id;
            $invoicePayment->date           = $request->date;
            $invoicePayment->amount         = $request->amount;
            $invoicePayment->payment_method = 0;
            $invoicePayment->reference      = $request->reference;
            $invoicePayment->description    = $request->description;
            if(!empty($request->add_receipt))
            {
                $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
                $uplaod = upload_file($request,'add_receipt',$fileName,'payment');
                if($uplaod['flag'] == 1)
                {
                    $url = $uplaod['url'];
                }
                else{
                    return redirect()->back()->with('error',$uplaod['msg']);
                }
                $invoicePayment->add_receipt = $url;
            }
            $invoicePayment->save();

            $invoice = MachineInvoice::where('id', $invoice_id)->first();
            $due     = $invoice->getDue();
            $total   = $invoice->getTotal();
            if($invoice->status == 0)
            {
                $invoice->send_date = date('Y-m-d');
                $invoice->save();
            }
            if($due <= 0)
            {
                $invoice->status = 3;
                $invoice->save();
            }
            else
            {
                $invoice->status = 2;
                $invoice->save();
            }

            $payment            = new MachineInvoicePayment();
            $payment->name      = $invoice->customer_name;
            $payment->email     = $invoice->customer_email;
            $payment->date      = company_date_formate($request->date);
            $payment->amount    = currency_format_with_sym($request->amount);
            $payment->invoice   = 'machineInvoice ' . MachineInvoice::machineInvoiceNumberFormat($invoice->invoice_id);
            $payment->dueAmount = currency_format_with_sym($invoice->getDue());

            event(new CreatePaymentDiagnosis($invoice,$invoicePayment));

            return redirect()->back()->with('success', __('The payment has been added successfully.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
        }

    }

    public function paymentDestroy($invoice_id, $payment_id)
    {
        if(Auth::user()->isAbleTo('machine invoice payment delete'))
        {
            $payment = MachineInvoicePayment::find($payment_id);
            if(!empty($payment->add_receipt))
            {
                try
                {
                    delete_file($payment->add_receipt);
                }
                catch (\Exception $e)
                {
                }
            }
            $invoice = MachineInvoice::where('id', $invoice_id)->first();
            $due     = $invoice->getDue();
            $total   = $invoice->getTotal();

            if($due > 0 && $total != $due)
            {
                $invoice->status = 2;

            }
            else
            {
                $invoice->status = 1;
            }

            $invoice->save();

            // first parameter invoice second parameter payment
            event(new DestroyPaymentDiagnosis($invoice, $payment));

            $payment->delete();
            return redirect()->back()->with('success', __('The payment has been deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function invoiceNumber()
    {
        $machine_invoice = MachineInvoice::latest()->first();
        if(isset($machine_invoice) && $machine_invoice != null){
            return $machine_invoice->invoice_id + 1;
        }else{
            return 1;
        }
    }

    public function request(Request $request)
    {
        if(isset($request->id) && !empty($request->id)){
            $repair_request = MachineRepairRequest::where('id',$request->id)->where('workspace',getActiveWorkSpace())->where('created_by',creatorId())->first();
            $customer['name'] = !empty($repair_request->customer_name) ? $repair_request->customer_name : '';
            $customer['email'] = !empty($repair_request->customer_email) ? $repair_request->customer_email : '';
            $machine_details = Machine::find($repair_request->machine_id);

            return view('machine-repair-management::invoice.customer_detail', compact('customer','repair_request','machine_details'));
        }
    }

    public function InvoiceSectionGet(Request $request)
    {
        $type = $request->type;
        $acction = $request->acction;
        $invoice = [];
        if($acction == 'edit')
        {
            $invoice = MachineInvoice::find($request->invoice_id);
        }

        if($request->type == "product")
        {
            $product_services = \Workdo\ProductService\Entities\ProductService::where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
            $product_services_count =$product_services->count();
            if($acction != 'edit')
            {
                $product_services->prepend('--', '');
            }
            $product_type = \Workdo\ProductService\Entities\ProductService::$product_type;
            $returnHTML = view('machine-repair-management::invoice.section',compact('product_services','type' ,'acction','invoice','product_services_count','product_type'))->render();
                $response = [
                    'is_success' => true,
                    'message' => '',
                    'html' => $returnHTML,
                ];
            return response()->json($response);
        }
        else
        {
            return [];
        }
    }
    
    public function product(Request $request)
    {
        $data['product']     = $product = \Workdo\ProductService\Entities\ProductService::find($request->product_id);
        $data['unit']        = !empty($product) ? ((!empty($product->unit())) ? $product->unit()->name : '') : '';
        $data['taxRate']     = $taxRate = !empty($product) ? (!empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0 ): 0;
        $data['taxes']       =  !empty($product) ? ( !empty($product->tax_id) ? $product->tax($product->tax_id) : 0) : 0;
        $salePrice           = !empty($product) ?  $product->sale_price : 0;
        $quantity            = 1;
        $taxPrice            = !empty($product) ? (($taxRate / 100) * ($salePrice * $quantity)) : 0;
        $data['totalAmount'] = !empty($product) ?  ($salePrice * $quantity) : 0;

        return json_encode($data);
    }

    public function productDestroy(Request $request)
    {

        // if(Auth::user()->isAbleTo('invoice product delete'))//machine 
        // {
            $invoiceProduct = MachineInvoiceDiagnosis::where('id', '=', $request->id)->first();

            // first parameter request second parameter invoice
            event(new ProductDestroyDiagnosis($request,$invoiceProduct));

            $invoiceProduct->delete();

            return response()->json(['success' => __('Invoice product successfully deleted.')]);
        // }
        // else
        // {
        //     return response()->json(['error' => __('Permission denied.')]);
        // }
    }

    public function items(Request $request)
    {
        $items = MachineInvoiceDiagnosis::where('invoice_id', $request->invoice_id)->where('product_id', $request->product_id)->first();
        return json_encode($items);
    }

    public function invoicePdf($id)
    {
        try {
            $id = \Illuminate\Support\Facades\Crypt::decrypt($id);
        } catch (\Throwable $th) {
            return redirect('login');
        }
        $invoice = MachineInvoice::find($id);
        if($invoice)
        {


            $iteams   = $invoice->items;
            $repair_request = MachineRepairRequest::find($invoice->request_id);
            $machine = Machine::find($repair_request->machine_id);


            $settings = getCompanyAllSetting($invoice->created_by,$invoice->workspace);
            //Set your logo
            $company_logo = get_file(sidebar_logo());
            $img  = $company_logo;


            $color      = '#ffffff';
            $font_color = '#000000';
            return view('machine-repair-management::invoice.pdf', compact('invoice', 'iteams', 'color', 'repair_request', 'machine', 'img', 'font_color', 'settings'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }


    }

}
