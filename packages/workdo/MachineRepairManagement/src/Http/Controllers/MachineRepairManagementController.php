<?php

namespace Workdo\MachineRepairManagement\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\MachineRepairManagement\Entities\MachineInvoice;
use Workdo\MachineRepairManagement\Entities\MachineInvoiceDiagnosis;
use Workdo\MachineRepairManagement\Entities\MachineRepairRequest;

class MachineRepairManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function __construct()
    {
        if (module_is_active('GoogleAuthentication')) {
            $this->middleware('2fa');
        }
    }
    public function index(Request $request)
    { 
        if (Auth::check()) {
            if (Auth::user()->isAbleTo('machine dashboard manage')) {
                $user = Auth::user();
                $events = [];
                $repair_request = MachineRepairRequest::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());
                if (!empty($request->date)) {
                    $date_range = explode(' to ', $request->date);
                    $repair_request->where('date_of_request', '>=', $date_range[0]);
                }
                $repair_request = $repair_request->get();
                foreach ($repair_request as $key => $request) {
                    $data = [
                        'title' => MachineRepairRequest::machineRepairNumberFormat($request->id),
                        'start' => $request->date_of_request,
                        'className' => 'event-danger'
                    ];
                    array_push($events, $data);
                }

                $totalRequest = MachineRepairRequest::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get()->count();
                $totalInvoice = Invoice::where('account_type','MachineRepairManagement')->where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get()->count();

                $diagnosisArray = MachineInvoice::getDiagnosisReportChart();

                return view('machine-repair-management::dashboard.dashboard', compact('events','totalRequest','totalInvoice','diagnosisArray'));

            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
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
        return view('machine-repair-management::create');
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
    public function show($id)
    {
        return view('machine-repair-management::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('machine-repair-management::edit');
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
}
