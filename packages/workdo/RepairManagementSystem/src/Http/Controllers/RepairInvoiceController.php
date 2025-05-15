<?php

namespace Workdo\RepairManagementSystem\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;
use Workdo\RepairManagementSystem\DataTables\RepairInvoiceDataTable;
use Workdo\RepairManagementSystem\DataTables\RepairInvoicePaymentDataTable;
use Workdo\RepairManagementSystem\Entities\RepairInvoice;
use Workdo\RepairManagementSystem\Entities\RepairInvoicePayment;
use Workdo\RepairManagementSystem\Entities\RepairOrderRequest;
use Workdo\RepairManagementSystem\Entities\RepairPart;
use Workdo\RepairManagementSystem\Events\CretaeRepairInvoice;

class RepairInvoiceController extends Controller
{
    function repairInvoiceNumber()
    {
        $latest = RepairInvoice::where('workspace', getActiveWorkSpace())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->invoice_id + 1;
    }

    public function index(RepairInvoiceDataTable $dataTable)
    {
        if (\Auth::user()->isAbleTo('repair invoice manage')) {
            return $dataTable->render('repair-management-system::repair-invoice.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create($id)
    {
        $repair_id = $id;
        return view('repair-management-system::repair-invoice.create', compact('repair_id'));
    }

    public function store(Request $request, $id)
    {
        if (\Auth::user()->isAbleTo('repair invoice create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'repair_charge' => 'required|numeric',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            RepairOrderRequest::findOrFail($id)->update(['status' => 7]);

            $repair_invoice                     = new RepairInvoice();
            $repair_invoice->invoice_id         = $this->repairInvoiceNumber();
            $repair_invoice->repair_id          = $request->id;
            $repair_invoice->repair_charge      = $request->repair_charge;
            $repair_invoice->workspace          = getActiveWorkSpace();
            $repair_invoice->created_by         = creatorId();
            $repair_invoice->save();

            event(new CretaeRepairInvoice($request, $repair_invoice));

            return redirect()->route('repair.request.index')->with('success', __('The repair order request has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(RepairInvoicePaymentDataTable $dataTable,$id)
    {
        if (\Auth::user()->isAbleTo('repair invoice show')) {
            try {
                $repair_invoice_id       = Crypt::decrypt($id);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', __('Repair Invoice Not Found.'));
            }
            $repair_invoice = RepairInvoice::with('repairOrderRequest')->find($repair_invoice_id);
            $iteams   = $repair_invoice->repairOrderRequest->repairParts ?? "";
            return $dataTable->with('id',$repair_invoice_id)->render('repair-management-system::repair-invoice.show', compact('repair_invoice', 'iteams'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function payment($invoice_id)
    {
        if (\Auth::user()->isAbleTo('repair invoice payment create')) {
            $repair_invoice = RepairInvoice::find($invoice_id);
            return view('repair-management-system::repair-invoice.payment', compact('repair_invoice'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function createPayment(Request $request, $invoice_id)
    {
        if (\Auth::user()->isAbleTo('invoice payment create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'amount' => 'required|numeric',
                ]
            );
            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->getMessageBag()->first());
            }

            $invoice = RepairInvoice::with('repairOrderRequest')->find($invoice_id);
            if (!$invoice) {
                return redirect()->back()->with('error', __('Invoice not found.'));
            }

            $due = $invoice->repairOrderRequest->getDue();
            if ($due < $request->amount) {
                return redirect()->back()->with('error', __('Amount must be smaller or equal to the due amount.'));
            }

            $repair_invoice_payment = new RepairInvoicePayment();
            $repair_invoice_payment->invoice_id = $invoice_id;
            $repair_invoice_payment->repair_id = $invoice->repair_id;
            $repair_invoice_payment->amount = $request->amount;
            $repair_invoice_payment->save();

            if (($due == $request->amount)) {
                $invoice->status = 2;
                $invoice->save();
                //Quantity Minus in ProductService
                $repair_parts = RepairPart::where('repair_id', $invoice->repair_id)->get();
                for ($i = 0; $i < count($repair_parts); $i++) {
                    if (module_is_active('ProductService')) {
                        RepairPart::total_quantity('minus', $repair_parts[$i]['quantity'], $repair_parts[$i]['product_id']);
                    }
                }
            } else {
                $invoice->status = 1;
                $invoice->save();
            }

            return redirect()->back()->with('success', __('Payment successfully added'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function setting(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'repair_invoice_prefix' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        } else {
            $post = $request->all();
            unset($post['_token']);
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
            return redirect()->back()->with('success', __('Repair Invoice Setting save successfully'));
        }
    }
}
