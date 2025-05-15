<?php

namespace Workdo\FormBuilder\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Arr;
use Workdo\CMMS\Entities\Location;
use Workdo\Contract\Entities\Contract;
use Workdo\Contract\Entities\ContractType;
use Workdo\FormBuilder\DataTables\FormBuilderDataTable;
use Workdo\FormBuilder\Entities\FormBuilder;
use Workdo\FormBuilder\Entities\FormBuilderModule;
use Workdo\FormBuilder\Entities\FormBuilderModuleData;
use Workdo\FormBuilder\Entities\FormField;
use Workdo\FormBuilder\Entities\FormResponse;
use Workdo\FormBuilder\Entities\UserLead;
use Workdo\FormBuilder\Events\ViewForm;
use Workdo\FormBuilder\Events\CreateForm;
use Workdo\FormBuilder\Events\CreateFormField;
use Workdo\FormBuilder\Events\DestroyForm;
use Workdo\FormBuilder\Events\DestroyFormField;
use Workdo\FormBuilder\Events\FormBuilderConvertTo;
use Workdo\FormBuilder\Events\UpdateForm;
use Workdo\FormBuilder\Events\UpdateFormField;
use Workdo\Internalknowledge\Entities\Article;
use Workdo\Internalknowledge\Entities\Book;
use Workdo\Lead\Entities\ClientDeal;
use Workdo\Lead\Entities\Deal;
use Workdo\Lead\Entities\DealStage;
use Workdo\Lead\Entities\Pipeline;
use Workdo\Lead\Entities\UserDeal;
use Workdo\MachineRepairManagement\Entities\Machine;
use Workdo\Notes\Entities\Notes;
use Workdo\Sales\Entities\Contact;
use Workdo\Sales\Entities\Opportunities;
use Workdo\Sales\Entities\OpportunitiesStage;
use Workdo\Sales\Entities\SalesAccount;
use Workdo\Sales\Entities\Stream;
use Workdo\Taskly\Entities\Project;
use Workdo\Taskly\Entities\UserProject;

class FormBuilderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(FormBuilderDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('formbuilder manage')) {

            return $dataTable->render('form-builder::form_builder.index');
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
        if (Auth::user()->isAbleTo('formbuilder create')) {
            return view('form-builder::form_builder.create');
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
        if (Auth::user()->isAbleTo('formbuilder create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name'      => 'required|string|max:255',
                    'is_active' => 'required|in:0,1',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->route('form_builder.index')->with('error', $messages->first());
            }

            $form_builder             = new FormBuilder();
            $form_builder->name       = $request->name;
            $form_builder->code       = uniqid() . time();
            $form_builder->is_active  = $request->is_active;
            $form_builder->created_by = creatorId();
            $form_builder->workspace  = getActiveWorkSpace();
            $form_builder->save();

            event(new CreateForm($request, $form_builder));

            return redirect()->route('form_builder.index')->with('success', __('The form has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(FormBuilder $formBuilder)
    {
        if (Auth::user()->isAbleTo('formbuilder form field manage')) {
            if ($formBuilder->created_by == creatorId() && $formBuilder->workspace == getActiveWorkSpace()) {
                return view('form-builder::form_builder.show', compact('formBuilder'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(FormBuilder $formBuilder)
    {
        if (Auth::user()->isAbleTo('formbuilder edit')) {
            if ($formBuilder->created_by == creatorId() && $formBuilder->workspace == getActiveWorkSpace()) {
                return view('form-builder::form_builder.edit', compact('formBuilder'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
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
    public function update(Request $request, FormBuilder $formBuilder)
    {
        if (Auth::user()->isAbleTo('formbuilder edit')) {
            if ($formBuilder->created_by == creatorId() && $formBuilder->workspace == getActiveWorkSpace()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name'      => 'required|string|max:255',
                        'is_active' => 'required|in:0,1',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('form_builder.index')->with('error', $messages->first());
                }

                $formBuilder->name           = $request->name;
                $formBuilder->is_active      = $request->is_active;
                $formBuilder->is_lead_active = 0;
                $formBuilder->save();

                event(new UpdateForm($request, $formBuilder));

                return redirect()->route('form_builder.index')->with('success', __('The form has been updated successfully.'));
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
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
    public function destroy(FormBuilder $formBuilder)
    {
        if (Auth::user()->isAbleTo('formbuilder delete')) {
            if ($formBuilder->created_by == creatorId() && $formBuilder->workspace == getActiveWorkSpace()) {
                FormField::where('form_id', '=', $formBuilder->id)->delete();
                FormBuilderModuleData::where('form_id', '=', $formBuilder->id)->delete();
                FormResponse::where('form_id', '=', $formBuilder->id)->delete();

                $formBuilder->delete();

                event(new DestroyForm($formBuilder));

                return redirect()->route('form_builder.index')->with('success', __('The form has been deleted'));
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function formView($code)
    {
        if (!empty($code)) {
            try {
                $code = $code;
            } catch (\Throwable $th) {
                return redirect('login');
            }
            $form = FormBuilder::where('code', 'LIKE', $code)->first();
            if (!empty($form)) {
            $company_id = $form->created_by;
            $workspace_id = $form->workspace;
                if ($form->is_active == 1) {
                    $objFields = $form->form_field;

                    return view('form-builder::form_builder.form_view', compact('objFields', 'code', 'form', 'company_id', 'workspace_id'));
                } else {
                    return view('form-builder::form_builder.form_view', compact('code', 'form', 'company_id', 'workspace_id'));
                }
            } else {
                return redirect()->route('login')->with('error', __('Form not found please contact to admin.'));
            }
        } else {
            return redirect()->route('login')->with('error', __('Permission Denied.'));
        }
    }

    public function viewResponse($form_id)
    {
        if (Auth::user()->isAbleTo('formbuilder show')) {
            $form = FormBuilder::find($form_id);
            if ($form->created_by == creatorId() && $form->workspace == getActiveWorkSpace()) {
                return view('form-builder::form_builder.response', compact('form'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function responseDetail($response_id)
    {
        if (Auth::user()->isAbleTo('formbuilder response view')) {
            $formResponse = FormResponse::find($response_id);
            $form         = FormBuilder::find($formResponse->form_id);
            if ($form->created_by == creatorId()) {
                $response = json_decode($formResponse->response, true);

                return view('form-builder::form_builder.response_detail', compact('response'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function fieldCreate($id)
    {
        if (Auth::user()->isAbleTo('formbuilder form field create')) {
            $formbuilder = FormBuilder::find($id);
            if ($formbuilder->created_by == creatorId() && $formbuilder->workspace == getActiveWorkSpace()) {
                $types = FormBuilder::$fieldTypes;

                return view('form-builder::form_builder.field_create', compact('types', 'formbuilder'));
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function fieldStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('formbuilder form field create')) {
            $formbuilder = FormBuilder::find($id);
            if ($formbuilder->created_by == creatorId() && $formbuilder->workspace == getActiveWorkSpace()) {

                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name'      => 'required|string|max:255',
                        'type'      => 'required|in:text,email,number,date,textarea',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                FormField::create(
                    [
                        'form_id'       => $formbuilder->id,
                        'name'          => $request->name,
                        'type'          => $request->type,
                        'created_by'    => creatorId(),
                        'workspace'     => getActiveWorkSpace(),
                    ]
                );

                event(new CreateFormField($request, $formbuilder));

                return redirect()->back()->with('success', __('The field has been created successfully.'));
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function fieldEdit($id, $field_id)
    {
        if (Auth::user()->isAbleTo('formbuilder form field edit')) {
            $form = FormBuilder::find($id);
            if ($form->created_by == creatorId() && $form->workspace == getActiveWorkSpace()) {
                $form_field = FormField::find($field_id);

                if (!empty($form_field)) {
                    $types = FormBuilder::$fieldTypes;

                    return view('form-builder::form_builder.field_edit', compact('form_field', 'types', 'form'));
                } else {
                    return redirect()->back()->with('error', __('Field not found.'));
                }
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function fieldUpdate($id, $field_id, Request $request)
    {
        if (Auth::user()->isAbleTo('formbuilder form field edit')) {
            $form = FormBuilder::find($id);
            if ($form->created_by == creatorId() && $form->workspace == getActiveWorkSpace()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name'      => 'required|string|max:255',
                        'type'      => 'required|in:text,email,number,date,textarea',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $field = FormField::find($field_id);
                $field->update(
                    [
                        'name' => $request->name,
                        'type' => $request->type,
                    ]
                );

                event(new UpdateFormField($request, $form));

                return redirect()->back()->with('success', __('The field has been updated successfully.'));
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function fieldDestroy($id, $field_id)
    {
        if (Auth::user()->isAbleTo('formbuilder form field delete')) {
            $form = FormBuilder::find($id);
            if ($form->created_by == creatorId() && $form->workspace == getActiveWorkSpace()) {
                if (!empty($form->module) && $form->module != 0) {
                    $formBuilderModuleData = FormBuilderModuleData::where('form_id', $form->id)->first();
                    $removedJsonData = json_decode($formBuilderModuleData->response_data);
                    $module = FormBuilderModule::find($form->module);

                    if ($module->module == 'Lead' && $module->submodule == 'Lead') {
                        unset($removedJsonData->user_id, $removedJsonData->pipeline_id);
                    } elseif ($module->module == 'Lead' && $module->submodule == 'Deal') {
                        unset($removedJsonData->clients_id, $removedJsonData->pipeline_id);
                    } elseif ($module->module == 'Taskly' && $module->submodule == 'Project') {
                        unset($removedJsonData->users_id);
                    } elseif ($module->module == 'MachineRepairManagement' && $module->submodule == 'Machine') {
                        unset($removedJsonData->status);
                    } elseif ($module->module == 'Sales' && $module->submodule == 'Contact') {
                        unset($removedJsonData->account_id, $removedJsonData->user_id);
                    } elseif ($module->module == 'Contract' && $module->submodule == 'Contract') {
                        unset($removedJsonData->user_id, $removedJsonData->type_id);
                    } elseif ($module->module == 'Internalknowledge' && $module->submodule == 'Book') {
                        unset($removedJsonData->users_id);
                    } elseif ($module->module == 'Internalknowledge' && $module->submodule == 'Article') {
                        unset($removedJsonData->book_id, $removedJsonData->type_id);
                    } elseif ($module->module == 'Notes' && $module->submodule == 'Note') {
                        unset($removedJsonData->color);
                    }
                    $checkField = in_array($field_id, (array)$removedJsonData);
                }

                if (!empty($checkField)) {
                    return redirect()->back()->with('error', __('Please remove this field from Convert To.'));
                } else {
                    $form_field = FormField::find($field_id);
                    if (!empty($form_field)) {
                        $form_field->delete();
                    } else {
                        return redirect()->back()->with('error', __('Field not found.'));
                    }

                    event(new DestroyFormField($form));

                    return redirect()->back()->with('success', __('The field has been deleted.'));
                }
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function formFieldBind($form_id)
    {
        if (Auth::user()->isAbleTo('formbuilder convert to')) {
            $form = FormBuilder::find($form_id);

            $modules = FormBuilderModule::select('module', 'submodule')
                ->where('type', 'company')
                ->get();
            $sub_modules = FormBuilderModule::select('id', 'module', 'submodule')
                ->whereIn('module', $modules->pluck('module'))
                ->where('type', 'company')
                ->get()
                ->groupBy('module');

            $formBuilderModule = [];
            $active_modules = ActivatedModule();
            foreach ($active_modules as $active_module) {
                foreach ($modules as $module) {
                    if ($active_module == $module->module) {
                        $temp = [];

                        if ($sub_modules->has($module->module)) {
                            $temp = $sub_modules[$module->module]
                                ->pluck('submodule', 'id')
                                ->toArray();
                        }

                        $formBuilderModule[Module_Alias_Name($module->module)] = $temp;
                    }
                }
            }
            $formBuilderModule = Arr::prepend($formBuilderModule, 'Select Module');
            if ($form->created_by == creatorId() && $form->workspace == getActiveWorkSpace()) {
                $types     = $form->form_field->pluck('name', 'id');
                $formField = FormBuilderModuleData::where('form_id', '=', $form_id)->first();

                // Get Users
                $users = User::where('workspace_id', getActiveWorkSpace())->emp()->get()->pluck('name', 'id');

                // Pipelines
                $pipelines = \Workdo\Lead\Entities\Pipeline::where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');

                return view('form-builder::form_builder.form_field', compact('form', 'types', 'formField', 'users', 'pipelines', 'formBuilderModule'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    // Store convert into lead modal
    public function bindStore(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('formbuilder convert to')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'is_lead_active' => 'required|in:0,1',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $form = FormBuilder::find($id);
            if (!$form) {
                return redirect()->back()->with('error', __('Form not found.'));
            }
            $form->is_lead_active   = $request->is_lead_active;
            $form->module           = $request->module;
            $form->save();
            $module             = FormBuilderModule::find($form->module);
            if ($form->created_by == creatorId() && $form->workspace == getActiveWorkSpace()) {
                if ($form->is_lead_active == 1) {
                    if (!$module) {
                        $form->is_lead_active = 0;
                        $form->module = 0;
                        $form->save();
                        return redirect()->back()->with('error', __('Module not found.'));
                    }
                    $post = $request->except('_token', 'is_lead_active', 'module', 'form_id', 'form_response_id');
                    if (!empty($request->form_response_id)) {
                        // if record already exists then update it.
                        $field_bind = FormBuilderModuleData::find($request->form_response_id);
                        $field_bind->update(
                            [
                                'module'        => $module->id,
                                'response_data' => json_encode($post),
                                'workspace'     => getActiveWorkSpace(),
                            ]
                        );
                    } else {
                        // Create Field Binding record on form_field_responses tbl
                        FormBuilderModuleData::create(
                            [
                                'form_id'       => $request->form_id,
                                'module'        => $module->id,
                                'response_data' => json_encode($post),
                                'workspace'     => getActiveWorkSpace(),
                            ]
                        );
                    }

                    event(new FormBuilderConvertTo($request, $form, $module));
                }
                return redirect()->back()->with('success', __('Convert to setting has been saved successfully.'));
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function formViewStore(Request $request)
    {
        // Get form
        $form       = FormBuilder::where('code', 'LIKE', $request->code)->first();
        $module     = FormBuilderModule::find($form->module);
        if (!empty($form)) {
            $arrFieldResp = [];
            foreach ($request->field as $key => $value) {
                $arrFieldResp[FormField::find($key)->name] = (!empty($value)) ? $value : '-';
            }

            // response store
            FormResponse::create(
                [
                    'form_id' => $form->id,
                    'response' => json_encode($arrFieldResp),
                ]
            );
            try {
                $post = null;
                if (!empty($module) && module_is_active($module->module)) {
                    $formBuilderModuleData  = $form->fieldResponse;
                    $objField               = json_decode($formBuilderModuleData->response_data);
                    $usr   = User::find($form->created_by);
                    if ($form->is_lead_active == 1) {
                        if ($module->module == 'Lead') {
                            if ($module->submodule == 'Lead') {

                                $stage = \Workdo\Lead\Entities\LeadStage::where('pipeline_id', '=', $objField->pipeline_id)->first();
                                if (!empty($stage)) {
                                    $post              = new \Workdo\Lead\Entities\Lead();
                                    $post->name        = $request->field[$objField->name_id];
                                    $post->email       = $request->field[$objField->email_id];
                                    $post->subject     = $request->field[$objField->subject_id];
                                    $post->user_id     = $objField->user_id;
                                    $post->pipeline_id = $objField->pipeline_id;
                                    $post->stage_id    = $stage->id;
                                    $post->created_by  = $usr->id;
                                    $post->date        = date('Y-m-d');
                                    $post->workspace_id = $usr->active_workspace;
                                    $post->save();

                                    $usrLeads = [
                                        $usr->id,
                                        $objField->user_id,
                                    ];

                                    foreach ($usrLeads as $usrLead) {
                                        UserLead::create(
                                            [
                                                'user_id' => $usrLead,
                                                'lead_id' => $post->id,
                                            ]
                                        );
                                    }
                                }
                            } elseif ($module->submodule == 'Deal') {
                                $stage = DealStage::where('pipeline_id', '=', $objField->pipeline_id)->first();
                                if (empty($stage)) {
                                    return redirect()->back()->with('error', __('Please Create Stage for This Pipeline.'));
                                } else {
                                    $post       = new Deal();
                                    $post->name = $request->field[$objField->deal_name_id];
                                    if (empty($request->field[$objField->price_id])) {
                                        $post->price = 0;
                                    } else {
                                        $post->price = $request->field[$objField->price_id];
                                    }
                                    $post->pipeline_id      = $objField->pipeline_id;
                                    $post->stage_id         = $stage->id;
                                    $post->status           = 'Active';
                                    $post->phone            = $request->field[$objField->phone_no_id];
                                    $post->created_by       = $usr->id;
                                    $post->workspace_id     = $usr->active_workspace;
                                    $post->save();

                                    $clients = User::whereIN('id', array_filter(explode(',', $objField->clients_id)))->get()->pluck('email', 'id')->toArray();

                                    foreach (array_keys($clients) as $client) {
                                        ClientDeal::create(
                                            [
                                                'deal_id' => $post->id,
                                                'client_id' => $client,
                                            ]
                                        );
                                    }

                                    UserDeal::create(
                                        [
                                            'user_id' => $usr->id,
                                            'deal_id' => $post->id,
                                        ]
                                    );
                                }
                            }
                        } elseif ($module->module == 'Taskly' && $module->submodule == 'Project') {
                            $post                     = new Project();
                            $post->name               = $request->field[$objField->project_name_id];
                            $post->description        = $request->field[$objField->description_id];
                            $post->start_date         = $post->end_date = date('Y-m-d');
                            $post->copylinksetting    = '{"member":"on","client":"on","milestone":"off","progress":"off","basic_details":"on","activity":"off","attachment":"on","bug_report":"on","task":"off","invoice":"off","timesheet":"off" ,"password_protected":"off"}';
                            $post->created_by         = $usr->id;
                            $post->workspace          = $usr->active_workspace;
                            $post->save();

                            $userList   = $objField->users_id;
                            $userList[] = $usr->id;
                            $userList   = array_unique($userList);
                            foreach ($userList as $userId) {
                                $registerUsers = User::where('active_workspace', $usr->active_workspace)->where('id', $userId)->first();
                                // assign project
                                $arrData               = [];
                                $arrData['user_id']    = $registerUsers->id;
                                $arrData['project_id'] = $post->id;
                                $is_invited            = UserProject::where($arrData)->first();
                                if (!$is_invited) {
                                    UserProject::create($arrData);
                                }
                            }
                        } elseif ($module->module == 'MachineRepairManagement' && $module->submodule == 'Machine') {
                            $post                         = new Machine();
                            $post->name                   = $request->field[$objField->machine_name_id];
                            $post->manufacturer           = $request->field[$objField->manufacturer_name_id];
                            $post->model                  = $request->field[$objField->model_id];
                            $post->installation_date      = $request->field[$objField->installation_date_id];
                            $post->description            = $request->field[$objField->description_id];
                            $post->last_maintenance_date  = $request->field[$objField->installation_date_id];
                            $post->status                 = $objField->status;
                            $post->created_by             = $usr->id;
                            $post->workspace              = $usr->active_workspace;
                            $post->save();
                        } elseif ($module->module == 'CMMS' && $module->submodule == 'Location') {
                            $count = Location::where('company_id', $usr->id)->where('workspace', $usr->active_workspace)->count();

                            if ($count <= 0) {
                                $current_location = 1;
                            } else {
                                $current_location = 0;
                            }

                            $post = Location::create(
                                [
                                    'created_by'        => $usr->id,
                                    'name'              => $request->field[$objField->name_id],
                                    'address'           => $request->field[$objField->address_id],
                                    'company_id'        => $usr->id,
                                    'workspace'         => $usr->active_workspace,
                                    'current_location'  => $current_location,
                                ]
                            );
                        } elseif ($module->module == 'Sales') {
                            if ($module->submodule == 'Contact') {
                                $post                       = new Contact();
                                $post['user_id']            = $objField->user_id;
                                $post['name']               = $request->field[$objField->name_id];
                                $post['account']            = $objField->account_id;
                                $post['email']              = $request->field[$objField->email_id];
                                $post['phone']              = $request->field[$objField->phone_no_id];
                                $post['contact_postalcode'] = $request->field[$objField->postal_code_id];
                                $post['created_by']         = $usr->id;
                                $post['workspace']          = $usr->active_workspace;
                                $post->save();

                                Stream::create(
                                    [
                                        'user_id'       => $usr->id,
                                        'created_by'    => $usr->id,
                                        'workspace'     => $usr->active_workspace,
                                        'log_type'      => 'created',
                                        'remark'        => json_encode(
                                            [
                                                'owner_name'        => $usr->name,
                                                'title'             => 'contact',
                                                'stream_comment'    => '',
                                                'user_name'         => $post->name,
                                            ]
                                        ),
                                    ]
                                );
                            } elseif ($module->submodule == 'Opportunities') {
                                $post                = new Opportunities();
                                $post['user_id']     = $objField->user_id;
                                $post['name']        = $request->field[$objField->name_id];
                                $post['account']     = $objField->account_id;
                                $post['stage']       = $objField->opportunities_stage_id;
                                $post['amount']      = $request->field[$objField->amount_id];
                                $post['probability'] = $request->field[$objField->probability_id];
                                $post['close_date']  = $request->field[$objField->close_date_id];
                                $post['created_by']  = $usr->id;
                                $post['workspace']   = $usr->active_workspace;
                                $post->save();

                                Stream::create(
                                    [
                                        'user_id'       => $usr->id,
                                        'created_by'    => $usr->id,
                                        'workspace'     => $usr->active_workspace,
                                        'log_type'      => 'created',
                                        'remark'        => json_encode(
                                            [
                                                'owner_name'        => $usr->name,
                                                'title'             => 'opportunities',
                                                'stream_comment'    => '',
                                                'user_name'         => $post->name,
                                            ]
                                        ),
                                    ]
                                );
                            }
                        } elseif ($module->module == 'Contract' && $module->submodule == 'Contract') {
                            $post              = new Contract();
                            $post->subject     = $request->field[$objField->subject_id];
                            $post->user_id     = $objField->user_id;
                            $post->value       = $request->field[$objField->value_id];
                            $post->type        = $objField->type_id;
                            $post->start_date  = $request->field[$objField->start_date_id];
                            $post->end_date    = $request->field[$objField->end_date_id];
                            $post->created_by  = $usr->id;
                            $post->workspace   = $usr->active_workspace;
                            $post->save();
                        } elseif ($module->module == 'Internalknowledge') {
                            if ($module->submodule == 'Book') {
                                $post              = new Book();
                                $post->title       = $request->field[$objField->title_id];
                                $post->description = $request->field[$objField->description_id];
                                $post->user_id     = implode(",", $objField->users_id);
                                $post->created_by  = $usr->id;
                                $post->workspace   = $usr->active_workspace;
                                $post->save();
                            } elseif ($module->submodule == 'Article') {
                                $post              = new Article();
                                $post->book        = $objField->book_id;
                                $post->title       = $request->field[$objField->title_id];
                                $post->description = $request->field[$objField->description_id];
                                $post->type        = $objField->type_id;
                                $post->post_id     = $usr->id;
                                $post->created_by  = $usr->id;
                                $post->workspace   = $usr->active_workspace;
                                $post->save();
                            }
                        } elseif ($module->module == 'Notes' && $module->submodule == 'Note') {
                            $post = new Notes();
                            $post->title            = $request->field[$objField->title_id];
                            $post->text             = $request->field[$objField->description_id];
                            $post->color            = $objField->color;
                            $post->created_by       = $usr->id;
                            $post->workspace_id     = $usr->active_workspace;
                            $post->save();
                        }
                    }
                }
                event(new ViewForm($request, $post, $form, $module));

                return redirect()->back()->with('success', __('Data has been submited successfully.'));
            } catch (\Throwable $th) {
                if (isset($module) && !empty($module)) {
                    return redirect()->back()->with('success', __('Data has been submited successfully, ' . '<br><span class="text-danger">' . 'Response is not convert to ' . Module_Alias_Name($module->module) . '>>' . $module->submodule . '</span>'));
                } else {
                    return redirect()->back()->with('success', __('Data has been submited successfully.'));
                }
            }
        } else {
            return redirect()->route('login')->with('error', __('Something went wrong.'));
        }
    }

    public function module(Request $request)
    {
        if (!empty($request->module) && !empty($request->form_id)) {
            $creatorId          = creatorId();
            $getActiveWorkSpace = getActiveWorkSpace();
            $form               = FormBuilder::find($request->form_id);
            $module             = FormBuilderModule::find($request->module);

            if ($form->created_by == $creatorId && $form->workspace == $getActiveWorkSpace) {
                $fields     = $form->form_field->pluck('name', 'id');
                $formField = FormBuilderModuleData::where('form_id', '=', $request->form_id)->first();
                $jsonRemovedField = !empty($formField->response_data) ? json_decode($formField->response_data) : null;
                if ($module->module == 'Lead') {
                    // Pipelines
                    $pipelines = Pipeline::where('created_by', '=', $creatorId)->where('workspace_id', $getActiveWorkSpace)->get()->pluck('name', 'id');

                    if ($module->submodule == 'Lead') {
                        // Get Users
                        $users = User::where('workspace_id', $getActiveWorkSpace)->emp()->get()->pluck('name', 'id');

                        $returnHTML = view('form-builder::form_builder.module_fields', compact('module', 'form', 'fields', 'formField', 'users', 'pipelines', 'jsonRemovedField'))->render();
                    } elseif ($module->submodule == 'Deal') {
                        $clients      = User::where('created_by', '=', $creatorId)->where('type', '=', 'client')->get()->pluck('name', 'id');
                        $returnHTML = view('form-builder::form_builder.module_fields', compact('module', 'form', 'fields', 'formField', 'clients', 'pipelines', 'jsonRemovedField'))->render();
                    }
                } elseif ($module->module == 'Taskly' && $module->submodule == 'Project') {
                    $users = User::where('created_by', $creatorId)
                        ->emp()
                        ->where('workspace_id', $getActiveWorkSpace)
                        ->orWhere('id', Auth::user()->id)
                        ->get()
                        ->pluck('name', 'id')
                        ->map(function ($name, $id) {
                            return $name . ' - ' . User::find($id)->email;
                        });
                    $returnHTML = view('form-builder::form_builder.module_fields', compact('module', 'form', 'fields', 'formField', 'users', 'jsonRemovedField'))->render();
                } elseif ($module->module == 'MachineRepairManagement' && $module->submodule == 'Machine') {
                    $status = ['Active' => __('Active'), 'Inactive' => __('Inactive')];
                    $returnHTML = view('form-builder::form_builder.module_fields', compact('module', 'form', 'fields', 'formField', 'status', 'jsonRemovedField'))->render();
                } elseif ($module->module == 'CMMS' && $module->submodule == 'Location') {
                    $returnHTML = view('form-builder::form_builder.module_fields', compact('module', 'form', 'fields', 'formField', 'jsonRemovedField'))->render();
                } elseif ($module->module == 'Sales') {
                    $account = SalesAccount::where('created_by', $creatorId)->where('workspace', $getActiveWorkSpace)->get()->pluck('name', 'id');
                    $users = User::where('workspace_id', $getActiveWorkSpace)->emp()->get()->pluck('name', 'id');
                    if ($module->submodule == 'Contact') {
                        $returnHTML = view('form-builder::form_builder.module_fields', compact('module', 'form', 'fields', 'formField', 'jsonRemovedField', 'account', 'users'))->render();
                    } elseif ($module->submodule == 'Opportunities') {
                        $opportunities_stage = OpportunitiesStage::where('created_by', $creatorId)->where('workspace', $getActiveWorkSpace)->get()->pluck('name', 'id');
                        $returnHTML = view('form-builder::form_builder.module_fields', compact('module', 'form', 'fields', 'formField', 'jsonRemovedField', 'account', 'users', 'opportunities_stage'))->render();
                    }
                } elseif ($module->module == 'Contract' && $module->submodule == 'Contract') {
                    $users       = User::where('workspace_id', $getActiveWorkSpace)->where('created_by', '=', $creatorId)->get()->pluck('name', 'id');
                    $contractType = ContractType::where('created_by', '=', $creatorId)->where('workspace', $getActiveWorkSpace)->get()->pluck('name', 'id');
                    $returnHTML = view('form-builder::form_builder.module_fields', compact('module', 'form', 'fields', 'formField', 'jsonRemovedField', 'users', 'contractType'))->render();
                } elseif ($module->module == 'Internalknowledge') {
                    if ($module->submodule == 'Book') {
                        $users = User::where('created_by', $creatorId)->where('workspace_id', $getActiveWorkSpace)->get()->pluck('name', 'id')->map(function ($name, $id) {
                            return $name . ' - ' . User::find($id)->email;
                        });
                        $returnHTML = view('form-builder::form_builder.module_fields', compact('module', 'form', 'fields', 'formField', 'jsonRemovedField', 'users'))->render();
                    } elseif ($module->submodule == 'Article') {
                        $books = Book::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('title', 'id');
                        $type = ['document' => __('Document'), 'mindmap' => __('Mindmap')];
                        $returnHTML = view('form-builder::form_builder.module_fields', compact('module', 'form', 'fields', 'formField', 'jsonRemovedField', 'books', 'type'))->render();
                    }
                } elseif ($module->module == 'Notes' && $module->submodule == 'Note') {
                    $color = ['bg-primary' => __('Primary'), 'bg-secondary' => __('Secondary'), 'bg-info' => __('Info'), 'bg-warning' => __('Warning'), 'bg-danger' => __('Danger')];
                    $returnHTML = view('form-builder::form_builder.module_fields', compact('module', 'form', 'fields', 'formField', 'jsonRemovedField', 'color'))->render();
                }
            }

            $response = [
                'is_success'    => true,
                'message'       => '',
                'html'          => $returnHTML,
            ];
            return response()->json($response);
        }
    }
}
