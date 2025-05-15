<?php

namespace Workdo\Workflow\Http\Controllers;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Workdo\Workflow\Entities\Workflowdothis;
use Workdo\Workflow\Entities\WorkflowModule;
use Illuminate\Support\Facades\Validator;
use Workdo\Hrm\Entities\Award;
use Workdo\ProductService\Entities\Tax;
use Workdo\Workflow\Entities\Workflow;
use Workdo\Workflow\Entities\WorkflowModuleField;
use Workdo\Workflow\Events\CreateWorkflow;
use Workdo\Workflow\Events\UpdateWorkflow;
use Workdo\Workflow\Events\DestroyWorkflow;
use Workdo\Workflow\DataTables\WorkflowDataTable;

class WorkflowController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(WorkflowDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('workflow manage')) {
            return $dataTable->render('workflow::workflow.index');
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
        if (\Auth::user()->isAbleTo('workflow create')) {

            $modules_n = WorkflowModule::select('module', \DB::raw('MAX(id) as id'))->groupBy('module')->get();
            $modules[''] = __('Please select');

            foreach($modules_n as $module)
            {
                if(module_is_active($module->module) || $module->module == 'general')
                {
                    $modules[$module->id] = Module_Alias_Name($module->module);
                }
            }
            $workflowdothis = Workflowdothis::get();
            $workflow = [];
            foreach($workflowdothis as $workflowdo)
            {
                if(module_is_active($workflowdo->module) || $workflowdo->module == 'Email' ){
                    $workflow[$workflowdo->id] = $workflowdo->submodule;
                }
            }

            return view('workflow::workflow.create', compact('modules','workflow'));
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('workflow create')) {
            $validatorArray = [
                'name' => 'required|max:120',
                'event' => 'required',
                'do_this' => 'required',
                'module_name' => 'required',

            ];
            $validator = Validator::make(
                $request->all(),
                $validatorArray
            );
            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $Workflow                   = new Workflow();
            $Workflow['name']           = $request->name;
            $Workflow['event']          = $request->event;
            $Workflow['module_name']    = $request->module_name;
            $Workflow['do_this']        = implode(",",$request->do_this);
            $Workflow['message']        = $request->message;
            $Workflow['workspace']      = getActiveWorkSpace();

            $json_dothis = [];

            if(!empty($request->email_type))
            {
                $email = [
                    'email_type' => $request->email_type,
                    'email_address' => ($request->email_type == 'staff') ? $request->email_staff:  $request->email_address,
                ];
                $json_dothis['email'] = $email;
            }

            if(!empty($request->webhook_url) && !empty($request->method))
            {
                $webhook = [
                    'webhook_url' => $request->webhook_url,
                    'method' => $request->method,
                ];
                $json_dothis['webhook'] = $webhook;
            }

            if(!empty($request->telegram_access) && !empty($request->telegram_chat))
            {
                $telegram = [
                    'telegram_access' => $request->telegram_access,
                    'telegram_chat' => $request->telegram_chat,
                ];

                $json_dothis['telegram'] = $telegram;
            }

            if(!empty($request->slack_url))
            {
                $slack = [
                    'slack_url' => $request->slack_url,
                ];
                $json_dothis['slack'] = $slack;
            }

            if(!empty($request->twilio_type))
            {
                $twilio = [
                    'twilio_type' => $request->twilio_type,
                    'twilio_number' => ($request->twilio_type == 'staff') ? $request->twilio_staff:  $request->twilio_number,
                ];
                $json_dothis['twilio'] = $twilio;
            }
            $fieldsArray = [];

            if(isset($request->fields) && count($request->fields) > 0)
            {
                foreach ($request->fields as $key => $value) {
                    $fieldsArray[] = [
                        'preview_type'  => array_key_exists("preview_type",$value) ?  $value['preview_type'] : null,
                        'condition'     => array_key_exists("condition",$value) ?  $value['condition'] : null,
                        'value'         => array_key_exists("value",$value) ?  $value['value'] : null,
                    ];
                }
            }

            $Workflow['do_this_data']   = json_encode($json_dothis);
            $Workflow['json_data']      = json_encode($fieldsArray);
            $Workflow['created_by']      = creatorId();
            $Workflow->save();

            event(new CreateWorkflow($request,$Workflow));

            return redirect()->route('workflow.index')->with('success', __('The workflow has been created successfully.'));
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
        return redirect()->back();
        return view('workflow::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (\Auth::user()->isAbleTo('workflow edit'))
        {
            $methods = ['GET' => 'GET', 'POST' => 'POST'];

            $workflow = Workflow::where('id',$id)->with('workflow_dothis')->first();
            if($workflow)
            {
                $modules_n = WorkflowModule::select('module', \DB::raw('MAX(id) as id'))->groupBy('module')->get();

                foreach($modules_n as $module)
                {
                    if(module_is_active($module->module) || $module->module == 'general'){
                       $modules[$module->id] = Module_Alias_Name($module->module);
                    }
                }

                $workflowdothis = Workflowdothis::get();
                $flow = [];
                foreach($workflowdothis as $workflowdo)
                {
                    if(module_is_active($workflowdo->module) || $workflowdo->module == 'Email' ){
                        $flow[$workflowdo->id] = $workflowdo->submodule;
                    }
                }

                return view('workflow::workflow.edit', compact('modules', 'workflow', 'methods','flow'));
            }else{
                return redirect()->route('workflow.index')->with('error', __('Workflow not found.'));
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
        if (\Auth::user()->isAbleTo('workflow edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $workflow               = Workflow::find($id);
            $workflow->name         = $request->name;
            $workflow->event        = $request->event;
            $workflow->module_name  = $request->module_name;
            $workflow->do_this      = implode(",",$request->do_this);
            $workflow->message      = $request->message;
            $workflow->workspace    = getActiveWorkSpace();

            $json_dothis = [];

            if(!empty($request->email_type))
            {
                $email = [
                    'email_type' => $request->email_type,
                    'email_address' => ($request->email_type == 'staff') ? $request->email_staff:  $request->email_address,
                ];
                $json_dothis['email'] = $email;
            }

            if(!empty($request->webhook_url) && !empty($request->method))
            {
                $webhook = [
                    'webhook_url' => $request->webhook_url,
                    'method' => $request->method,
                ];
                $json_dothis['webhook'] = $webhook;
            }

            if(!empty($request->telegram_access) && !empty($request->telegram_chat))
            {
                $telegram = [
                    'telegram_access' => $request->telegram_access,
                    'telegram_chat' => $request->telegram_chat,
                ];

                $json_dothis['telegram'] = $telegram;
            }

            if(!empty($request->slack_url))
            {
                $slack = [
                    'slack_url' => $request->slack_url,
                ];
                $json_dothis['slack'] = $slack;
            }

            if(!empty($request->twilio_type))
            {
                $twilio = [
                    'twilio_type' => $request->twilio_type,
                    'twilio_number' => ($request->twilio_type == 'staff') ? $request->twilio_staff:  $request->twilio_number,
                ];
                $json_dothis['twilio'] = $twilio;
            }
            $fieldsArray = [];

            if(isset($request->fields) && count($request->fields) > 0)
            {
                foreach ($request->fields as $key => $value) {
                    $fieldsArray[] = [
                        'preview_type'  => array_key_exists("preview_type",$value) ?  $value['preview_type'] : null,
                        'condition'     => array_key_exists("condition",$value) ?  $value['condition'] : null,
                        'value'         => array_key_exists("value",$value) ?  $value['value'] : null,
                    ];
                }
            }

            $workflow->do_this_data = json_encode($json_dothis);
            $workflow->json_data = json_encode($fieldsArray);
            $workflow->created_by = creatorId();
            $workflow->update();

            event(new UpdateWorkflow($request,$workflow));

            return redirect()->route('workflow.index')->with('success', __('The workflow details are updated successfully.'));
        } else{
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Workflow $workflow)
    {
        if (\Auth::user()->isAbleTo('workflow delete')) {
            $workflow->delete();

            event(new DestroyWorkflow($workflow));

            return redirect()->back()->with('success', __('The workflow has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function getfielddata(Request $request)
    {
        $events = WorkflowModuleField::where('workmodule_id', $request->event_id)->get()->pluck('field_name','id');

        $response = [
            'is_success' => true,
            'message' => '',
            'data' => $events,
        ];
        return response()->json($response);
    }

    public function getcondition(Request $request)
    {
        $workflow = WorkflowModuleField::find($request->workmodule_id);
        $data = null;
        if($workflow->input_type == 'select')
        {

            if($workflow->model_name == 'Tax')
            {
                $data = \Workdo\ProductService\Entities\Tax::where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Category' && $workflow->model_name == 'Category')
            {
                $data = \Workdo\ProductService\Entities\Category::where('workspace_id', getActiveWorkSpace())->where('type', '=', 1)->get()->pluck('name', 'id');
            }
            elseif ($workflow->model_name == 'User' && $workflow->field_name == 'Project User')
            {
                $data = User::where('created_by',creatorId())->emp()->where('workspace_id',getActiveWorkSpace())->orWhere('id',Auth::user()->id)->get()->pluck('name', 'id');
            }
            elseif ($workflow->model_name == 'User' && $workflow->field_name == 'Contract User')
            {
                $data = User::where('workspace_id',getActiveWorkSpace())->where('created_by', '=', creatorId())->get()->pluck('name', 'id');
            }
            elseif ($workflow->model_name == 'User' && $workflow->field_name == 'Lead User' )
            {
                $data = User::where('created_by', '=', creatorId())->where('type', '!=', 'client')->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Type' && $workflow->model_name == 'ContractType')
            {
                $data = \Workdo\Contract\Entities\ContractType::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'status' && $workflow->model_name == 'Ticket')
            {
                $data = \Workdo\SupportTicket\Entities\Ticket::where('created_by', '=', creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('status', 'id');
            }
            elseif($workflow->field_name == 'Category' && $workflow->model_name == 'TicketCategory')
            {
                $data = \Workdo\SupportTicket\Entities\TicketCategory::where('created_by', '=', creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'client' && $workflow->model_name == 'DealUser')
            {
                $data = User::where('created_by', '=', creatorId())->where('type', '=', 'client')->where('workspace_id',getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Type' && $workflow->model_name == 'Appointment')
            {
                $data = \Workdo\Appointment\Entities\Appointment::$appointment_type;
            }
            elseif($workflow->field_name == 'Category' && $workflow->model_name == 'pos_Category')
            {
                $data = \Workdo\ProductService\Entities\Category::where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->where('type', 2)->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Warehouse' && $workflow->model_name == 'Warehouse')
            {
                $data = Warehouse::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Award Type' && $workflow->model_name == 'AwardType')
            {
                $data = \Workdo\Hrm\Entities\AwardType::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Employee' && $workflow->model_name == 'User')
            {
                $data = User::where('created_by', '=', creatorId())->where('type', '=', 'staff')->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Branch' && $workflow->model_name == 'Branch')
            {
                $data = \Workdo\Hrm\Entities\Branch::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Trainer Option' && $workflow->model_name == 'Training')
            {
                $data = \Workdo\Training\Entities\Training::$options;
            }
            elseif($workflow->field_name == 'Training Type' && $workflow->model_name == 'TrainingType')
            {
                $data = \Workdo\Training\Entities\TrainingType::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Trainer' && $workflow->model_name == 'Trainer')
            {
                $data = \Workdo\Training\Entities\Trainer::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('firstname', 'id');
            }
            elseif($workflow->field_name == 'Employee' && $workflow->model_name == 'Employee')
            {
                $data = \Workdo\Hrm\Entities\Employee::where('created_by', '=', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Location' && $workflow->model_name == 'Location')
            {
                $data = \Workdo\CMMS\Entities\Location::where('workspace',getActiveWorkSpace())->where('created_by',creatorId())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Account' && $workflow->model_name == 'SalesAccount')
            {
                $data = \Workdo\Sales\Entities\SalesAccount::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Opportunities Stage' && $workflow->model_name == 'OpportunitiesStage')
            {
                $data = \Workdo\Sales\Entities\OpportunitiesStage::where('workspace',getActiveWorkSpace())->where('created_by',creatorId())->get()->pluck('name', 'id');
            }
            elseif($workflow->model_name == 'Quote' && $workflow->field_name != 'Sales Quote')
            {
                $data = \Workdo\Sales\Entities\Quote::$status;
            }
            elseif($workflow->field_name == 'Tax' && $workflow->model_name == 'Tax')
            {
                $data = \Workdo\ProductService\Entities\Tax::where('created_by', creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Opportunity' && $workflow->model_name == 'Opportunities')
            {
                $data = \Workdo\Sales\Entities\Opportunities::where('workspace',getActiveWorkSpace())->where('created_by',creatorId())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Sales Quote' && $workflow->model_name == 'Quote')
            {
                $data = \Workdo\Sales\Entities\Quote::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Customer Name' && $workflow->model_name == 'User')
            {
                $data = \App\Models\User::where('type','client')->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Vehicle name' && $workflow->model_name == 'Vehicle')
            {
                $data = \Workdo\Fleet\Entities\Vehicle::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name','id');
            }
            elseif($workflow->field_name == 'Name' && $workflow->model_name == 'User')
            {
                $data = \App\Models\User::where('type','staff')->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Maintenance Type' && $workflow->model_name == 'MaintenanceType')
            {
                $data = \Workdo\Fleet\Entities\MaintenanceType::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Driver Name' && $workflow->model_name == 'Driver')
            {
                $data = \Workdo\Fleet\Entities\Driver::where('workspace', getActiveWorkSpace())->where('created_by', '=', creatorId())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Fuel Type' && $workflow->model_name == 'FuelType')
            {
                $data = \Workdo\Fleet\Entities\FuelType::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Gender')
            {
                $data = \Workdo\Workflow\Entities\Workflow::$gender;
            }
            elseif($workflow->field_name == 'Room' && $workflow->model_name == 'Rooms')
            {
                $data = \Workdo\Holidayz\Entities\Rooms::where(['created_by' => creatorId(), 'workspace' => getActiveWorkSpace()])->get()->pluck('room_type','id');
            }
            elseif($workflow->field_name == 'Skill' && $workflow->model_name == 'Skill')
            {
                $data = \Workdo\GymManagement\Entities\Skill::where('workspace',getActiveWorkSpace())->where('created_by',creatorId())->get()->pluck('name','id');
            }
            elseif($workflow->field_name == 'Member' && $workflow->model_name == 'GymMember')
            {
                $data = \Workdo\GymManagement\Entities\GymMember::where('workspace',getActiveWorkSpace())->where('created_by',creatorId())->get()->pluck('name','id');
            }
            elseif($workflow->field_name == 'Service' && $workflow->model_name == 'BeautyService')
            {
                $data = \Workdo\BeautySpaManagement\Entities\BeautyService::where('created_by',creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name','id');
            }
            elseif($workflow->field_name == 'Status' && $workflow->model_name == 'AgricultureFleet' && $workflow->model_name != 'HospitalBed')
            {
                $data = \Workdo\AgricultureManagement\Entities\AgricultureFleet::$status;
            }
            elseif($workflow->field_name == 'Activity' && $workflow->model_name == 'AgricultureActivities')
            {
                $data = \Workdo\AgricultureManagement\Entities\AgricultureActivities::where('workspace',getActiveWorkSpace())->where('created_by', creatorId())->get()->pluck('name','id');
            }
            elseif($workflow->field_name == 'Farmer' && $workflow->model_name == 'AgricultureUser')
            {
                $data = \Workdo\AgricultureManagement\Entities\AgricultureUser::where('workspace',getActiveWorkSpace())->where('created_by', creatorId())->get()->pluck('name','id');
            }
            elseif($workflow->field_name == 'Agriculture Cycle' && $workflow->model_name == 'AgricultureCycles')
            {
                $data = \Workdo\AgricultureManagement\Entities\AgricultureCycles::where('workspace',getActiveWorkSpace())->where('created_by', creatorId())->get()->pluck('name','id');
            }
            elseif($workflow->field_name == 'Department' && $workflow->model_name == 'AgricultureDepartment')
            {
                $data = \Workdo\AgricultureManagement\Entities\AgricultureDepartment::where('workspace',getActiveWorkSpace())->where('created_by', creatorId())->get()->pluck('name','id');
            }
            elseif($workflow->field_name == 'Season'&& $workflow->model_name == 'AgricultureSeason')
            {
                $data = \Workdo\AgricultureManagement\Entities\AgricultureSeason::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get()->pluck('name','id');
            }
            elseif($workflow->field_name == 'Cycles' && $workflow->model_name == 'AgricultureCycles')
            {
                $data = \Workdo\AgricultureManagement\Entities\AgricultureCycles::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get()->pluck('name','id');
            }
            elseif($workflow->field_name == 'Fleets' && $workflow->model_name == 'AgricultureFleet')
            {
                $data = \Workdo\AgricultureManagement\Entities\AgricultureFleet::where('workspace', getActiveWorkSpace())->where('status',0)->where('created_by', creatorId())->get()->pluck('name','id');
            }
            elseif($workflow->field_name == 'Equipments' && $workflow->model_name == 'AgricultureEquipment')
            {
                $data = \Workdo\AgricultureManagement\Entities\AgricultureEquipment::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get()->pluck('name','id');
            }
            elseif($workflow->field_name == 'Processes' && $workflow->model_name == 'AgricultureProcess')
            {
                $data = \Workdo\AgricultureManagement\Entities\AgricultureProcess::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get()->pluck('name','id');
            }
            elseif($workflow->field_name == 'Vehicle Type' && $workflow->model_name == 'VehicleType')
            {
                $data = \Workdo\GarageManagement\Entities\VehicleType::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Vehicle Brand' && $workflow->model_name == 'VehicleBrand')
            {
                $data = \Workdo\GarageManagement\Entities\VehicleBrand::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Vehicle Color' && $workflow->model_name == 'VehicleColor')
            {
                $data = \Workdo\GarageManagement\Entities\VehicleColor::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Vehicle FuelType' && $workflow->model_name == 'FuelType')
            {
                $data = \Workdo\GarageManagement\Entities\FuelType::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Assign To' && $workflow->model_name == 'User')
            {
                $data = \App\Models\User::where('type','staff')->where('is_disable','=','1')->get()->pluck('name','id');
            }
            elseif($workflow->field_name == 'Vehicle Name' && $workflow->model_name == 'VehicleType')
            {
                $data = \Workdo\GarageManagement\Entities\VehicleType::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Service Category' && $workflow->model_name == 'RepairCategory')
            {
                $data = \Workdo\GarageManagement\Entities\RepairCategory::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Service Type' && $workflow->model_name == 'Workflow')
            {
                $data = \Workdo\Workflow\Entities\Workflow::$serviceType;
            }
            elseif($workflow->field_name == 'Wash' && $workflow->model_name == 'Workflow')
            {
                $data = \Workdo\Workflow\Entities\Workflow::$wash;
            }
            elseif($workflow->field_name == 'Transport Type' && $workflow->model_name == 'TransportType')
            {
                $data = \Workdo\TourTravelManagement\Entities\TransportType::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('transport_type_name', 'id');
            }
            elseif($workflow->field_name == 'Season Name' && $workflow->model_name == 'Season')
            {
                $data = \Workdo\TourTravelManagement\Entities\Season::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('season_name', 'id');
            }
            elseif($workflow->field_name == 'Varient' && $workflow->model_name == 'NewspaperVarient')
            {
                $data = \Workdo\Newspaper\Entities\NewspaperVarient::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name','id');
            }
            elseif($workflow->field_name == 'Tax' && $workflow->model_name == 'Tax')
            {
                $data = Tax::where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->get()->pluck('name','id');
            }
            elseif($workflow->field_name == 'Property' && $workflow->model_name == 'Property')
            {
                $data = \Workdo\PropertyManagement\Entities\Property::where('workspace',getActiveWorkSpace())->where('created_by', creatorId())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Rent Type' && $workflow->model_name == 'Workflow')
            {
                $data = \Workdo\Workflow\Entities\Workflow::$rentType;
            }
            elseif($workflow->field_name == 'Specialization' && $workflow->model_name == 'Specialization')
            {
                $data = \Workdo\HospitalManagement\Entities\Specialization::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Manufacturer' && $workflow->model_name == 'User')
            {
                $data = \App\Models\User::where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->where('type','vendor')->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Medicine Category' && $workflow->model_name == 'MedicineCategory')
            {
                $data = \Workdo\HospitalManagement\Entities\MedicineCategory::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Bed Type' && $workflow->model_name == 'BedType')
            {
                $data = \Workdo\HospitalManagement\Entities\BedType::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Ward' && $workflow->model_name == 'Ward')
            {
                $data = \Workdo\HospitalManagement\Entities\Ward::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Status' && $workflow->model_name == 'HospitalBed')
            {
                $data = \Workdo\HospitalManagement\Entities\HospitalBed::$status;
            }
            elseif($workflow->field_name == 'Services' && $workflow->model_name == 'LaundryService')
            {
                $data = \Workdo\LaundryManagement\Entities\LaundryService::where('workspace_id',getActiveWorkSpace())->where('created_by',creatorId())->pluck('name','id')->toArray();
            }
            elseif($workflow->field_name == 'Location' && $workflow->model_name == 'LaundryLocation')
            {
                $data = \Workdo\LaundryManagement\Entities\LaundryLocation::where('workspace_id',getActiveWorkSpace())->where('created_by',creatorId())->pluck('name','id')->toArray();
            }
            elseif($workflow->field_name == 'Skill' && $workflow->model_name == 'Skill')
            {
                $data = \Workdo\GymManagement\Entities\Skill::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Member' && $workflow->model_name == 'GymMember')
            {
                $data = \Workdo\GymManagement\Entities\GymMember::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get()->pluck('name', 'id');
            }
            elseif($workflow->field_name == 'Gender' && $workflow->model_name == 'Workflow')
            {
                $data = \Workdo\Workflow\Entities\Workflow::$gender;
            }
            elseif($workflow->field_name == 'Recruitment Type' && $workflow->model_name == 'Job')
            {
                $data = \Workdo\Recruitment\Entities\Job::$recruitment_type;
            }
            elseif($workflow->field_name == 'Job Type' && $workflow->model_name == 'Job')
            {
                $data = \Workdo\Recruitment\Entities\Job::$job_type;
            }
            elseif($workflow->field_name == 'Interviewer' && $workflow->model_name == 'JobApplication')
            {
                $data = \Workdo\Recruitment\Entities\JobApplication::where('is_employee', '!=', 1)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        $returnHTML =  view('workflow::workflow.input', compact('workflow','data','request'))->render();

            $response = [
                'is_success' => true,
                'message' => '',
                'html' => $returnHTML,
            ];
        return response()->json($response);
    }

    public function attribute(Request $request)
    {
        $Workflowdothis = [];

        if(isset($request->attribute_id) && count($request->attribute_id) > 0)
        {
            $Workflowdothis = Workflowdothis::whereIn('id' , $request->attribute_id)->get()->pluck('submodule')->toArray();
        }
        $workflow = Workflow::find($request->workflow_id);

        $staff = User::where('created_by',creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('name','email');
        $staff_mobile = User::where('created_by',creatorId())->where('workspace_id',getActiveWorkSpace())->get()->pluck('name','mobile_no');
        $methods = ['GET' => 'GET', 'POST' => 'POST'];
        $returnHTML =  view('workflow::workflow.append',compact('Workflowdothis','workflow','staff','staff_mobile','methods'))->render();

        $response = [
            'is_success' => true,
            'message' => '',
            'html' => $returnHTML,
        ];
        return response()->json($response);
    }

    public function module(Request $request)
    {
        $workflowmodule = WorkflowModule::find($request->module);

        $response = [
            'is_success' => false,
            'message' => "",
            'event_name' => [],
        ];
        if($workflowmodule)
        {
            $event_name = WorkflowModule::where('module', $workflowmodule->module)->pluck('submodule','id');
            $response = [
                'is_success' => true,
                'message' => '',
                'event_name' => $event_name,
            ];

        }
        return response()->json($response);

    }

}
