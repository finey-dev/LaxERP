<?php

namespace Workdo\MachineRepairManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\MachineRepairManagement\DataTables\MachineServiceAgreementDataTable;
use Workdo\MachineRepairManagement\Entities\MachineServiceAgreement;
use Workdo\MachineRepairManagement\Events\CreateMachineServiceAgreement;
use Workdo\MachineRepairManagement\Events\DestroyMachineServiceAgreement;
use Workdo\MachineRepairManagement\Events\UpdateMachineServiceAgreement;

class MachineServiceAgreementController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(MachineServiceAgreementDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('machine service agreement manage')) {

            return $dataTable->render('machine-repair-management::agreement.index');
        }else{
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if (Auth::user()->isAbleTo('machine service agreement create')) {

            return view('machine-repair-management::agreement.create');

        }else{
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if(Auth::user()->isAbleTo('machine service agreement create'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'customer_id' => 'required',
                                   'start_date' => 'required',
                                   'end_date' => 'required',
                                   'coverage_details' => 'required',
                                   'details' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('machine-service-agreement.index')->with('error', $messages->first());
            }

            $machineserviceagreement                    = new MachineServiceAgreement();
            $machineserviceagreement->customer_id       = $request->customer_id;
            $machineserviceagreement->start_date        = $request->start_date;
            $machineserviceagreement->end_date          = $request->end_date;
            $machineserviceagreement->coverage_details  = $request->coverage_details;
            $machineserviceagreement->details           = $request->details;
            $machineserviceagreement->workspace         = getActiveWorkSpace();
            $machineserviceagreement->created_by        = creatorId();
            $machineserviceagreement->save();

            event(new CreateMachineServiceAgreement($request,$machineserviceagreement));
            return redirect()->route('machine-service-agreement.index')->with('success', __('The service agreement has been created successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return redirect()->back();
        return view('machine-repair-management::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('machine service agreement edit'))
        {
            $machineserviceagreement  = MachineServiceAgreement::where('id', $id)->where('workspace', getActiveWorkSpace())->first();
            return view('machine-repair-management::agreement.edit',compact('machineserviceagreement'));

        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
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
        if(Auth::user()->isAbleTo('machine service agreement edit'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                    'customer_id' => 'required',
                                    'start_date' => 'required',
                                    'end_date' => 'required',
                                    'coverage_details' => 'required',
                                    'details' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $machineserviceagreement                   = MachineServiceAgreement::find($id);
            $machineserviceagreement['customer_id']      = $request->customer_id;
            $machineserviceagreement['start_date']      = $request->start_date;
            $machineserviceagreement['end_date'] = $request->end_date;
            $machineserviceagreement['coverage_details']      = $request->coverage_details;
            $machineserviceagreement['details']      = $request->details;
            $machineserviceagreement->workspace        = getActiveWorkSpace();
            $machineserviceagreement->created_by       = creatorId();
            $machineserviceagreement->update();

            event(new UpdateMachineServiceAgreement($request,$machineserviceagreement));


            return redirect()->route('machine-service-agreement.index')->with('success', __('The service agreement details are updated successfully.'));
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
        if(Auth::user()->isAbleTo('machine service agreement delete'))
        {
            $machineserviceagreement = MachineServiceAgreement::find($id);

            if(!empty($machineserviceagreement))
            {
                event(new DestroyMachineServiceAgreement($machineserviceagreement));

                $machineserviceagreement->delete();

                return redirect()->route('machine-service-agreement.index')->with('success', 'The service agreement has been deleted.' );
            }
        }
        else
        {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
