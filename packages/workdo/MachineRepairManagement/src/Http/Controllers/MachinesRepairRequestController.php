<?php

namespace Workdo\MachineRepairManagement\Http\Controllers;

use App\Events\DestroyInvoice;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\MachineRepairManagement\DataTables\MachineRepairRequestDataTable;
use Workdo\MachineRepairManagement\Entities\Machine;
use Workdo\MachineRepairManagement\Entities\MachineRepairRequest;
use Workdo\MachineRepairManagement\Events\CreateRepairRequest;
use Workdo\MachineRepairManagement\Events\DestroyRepairRequest;
use Workdo\MachineRepairManagement\Events\UpdateRepairRequest;

class MachinesRepairRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(MachineRepairRequestDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('repair request manage')) {
            return $dataTable->render('machine-repair-management::repair-request.index');
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
        if (Auth::user()->isAbleTo('repair request create')) {
            $machines = Machine::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->where('status', 'active')->get()->pluck('name', 'id');
            $machines->prepend(__('Select Machine'), '');
            $priority_level = ['Low' => __('Low'), 'Medium' => __('Medium'), 'High' => __('High')];
            $status = ['Pending' => __('Pending'), 'In Progress' => __('In Progress'), 'Completed' => __('Completed')];

            return view('machine-repair-management::repair-request.create', compact('machines', 'priority_level', 'status'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('repair request create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'machine_id'            => 'required',
                    'customer_name'         => 'required|string|max:150',
                    'customer_email'        => 'required|email|regex:/(.+)@(.+)\.(.+)/i',
                    'priority_level'        => 'required',
                    'description_of_issue'  => 'required|string|max:255',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $repair_request                         = new MachineRepairRequest();
            $repair_request->machine_id             = $request->machine_id;
            $repair_request->customer_name          = $request->customer_name;
            $repair_request->customer_email         = $request->customer_email;
            $repair_request->priority_level         = $request->priority_level;
            $repair_request->description_of_issue   = $request->description_of_issue;
            $repair_request->date_of_request        = Carbon::today();
            $repair_request->workspace              = getActiveWorkSpace();
            $repair_request->created_by             = creatorId();
            $repair_request->save();

            event(new CreateRepairRequest($request, $repair_request));

            return redirect()->route('machine-repair-request.index')->with('success', __('The machine repair request has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        if (Auth::user()->isAbleTo('repair request show')) {
            $id       = \Crypt::decrypt($id);
            $repair_request = MachineRepairRequest::where('id', $id)->where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->first();

            return view('machine-repair-management::repair-request.show', compact('repair_request'));
        } else {
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
        if (Auth::user()->isAbleTo('repair request edit')) {
            $repair_request = MachineRepairRequest::find($id);
            if (isset($repair_request) && !empty($repair_request)) {
                $machines = Machine::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->where('status', 'active')->get()->pluck('name', 'id');
                $machines->prepend(__('Select Machine'), '');
                $staffs = User::where('type', 'staff')->where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
                $staffs->prepend(__('Select Staff'), '');
                $priority_level = ['Low' => __('Low'), 'Medium' => __('Medium'), 'High' => __('High')];
                $status = ['Pending' => __('Pending'), 'In Progress' => __('In Progress'), 'Completed' => __('Completed')];

                return view('machine-repair-management::repair-request.edit', compact('repair_request', 'machines', 'staffs', 'priority_level', 'status'));
            } else {
                redirect()->back()->with('error', __('Machine repair request not found.'));
            }
        } else {
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
        if (Auth::user()->isAbleTo('repair request edit')) {
            $repair_request = MachineRepairRequest::find($id);
            if (isset($repair_request) && !empty($repair_request)) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'machine_id'            => 'required',
                        'customer_name'         => 'required|string|max:150',
                        'customer_email'        => 'required|email|regex:/(.+)@(.+)\.(.+)/i',
                        'priority_level'        => 'required',
                        'status'                => 'required',
                        'description_of_issue'  => 'required|string|max:255',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $repair_request->machine_id             = $request->machine_id;
                $repair_request->customer_name          = $request->customer_name;
                $repair_request->customer_email         = $request->customer_email;
                if (isset($request->staff_id) && !empty($request->staff_id)) {
                    $repair_request->staff_id           = $request->staff_id;
                }
                $repair_request->priority_level         = $request->priority_level;
                $repair_request->description_of_issue   = $request->description_of_issue;
                $repair_request->status                 = $request->status;
                $repair_request->workspace              = getActiveWorkSpace();
                $repair_request->created_by             = creatorId();
                $repair_request->save();

                event(new UpdateRepairRequest($request, $repair_request));

                return redirect()->route('machine-repair-request.index')->with('success', __('The machine repair request has been updated successfully.'));
            } else {
                redirect()->back()->with('error', __('Machine repair request not found.'));
            }
        } else {
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
        if (Auth::user()->isAbleTo('repair request delete')) {
            $repair_request = MachineRepairRequest::find($id);
            if (isset($repair_request) && !empty($repair_request)) {
                event(new DestroyRepairRequest($repair_request));

                $invoice = Invoice::where('customer_id', $id)->where('account_type', 'MachineRepairManagement')->first();
                if (isset($invoice) && !empty($invoice)) {
                    foreach ($invoice->payments as $invoices) {
                        if (!empty($invoices->add_receipt)) {
                            try {
                                delete_file($invoices->add_receipt);
                            } catch (\Exception $e) {
                            }
                        }
                        $invoices->delete();
                    }
                    InvoiceProduct::where('invoice_id', '=', $invoice->id)->delete();

                    // first parameter invoice
                    event(new DestroyInvoice($invoice));
                    $invoice->delete();
                }

                $repair_request->delete();

                return redirect()->route('machine-repair-request.index')->with('success', __('The machine repair request has been deleted.'));
            } else {
                return redirect()->back()->with('error', __('Machine repair request not found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
