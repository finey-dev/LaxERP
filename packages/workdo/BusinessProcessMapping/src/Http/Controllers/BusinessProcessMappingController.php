<?php

namespace Workdo\BusinessProcessMapping\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\BusinessProcessMapping\Entities\BusinessProcessMapping;
use Workdo\BusinessProcessMapping\Entities\Related;
use Workdo\Taskly\Entities\Project;
use Workdo\Taskly\Entities\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Workdo\BusinessProcessMapping\DataTables\BusinessProcessMappingDatatable;
use Workdo\BusinessProcessMapping\Events\CreateBusinessProcessMapping;
use Workdo\BusinessProcessMapping\Events\DestroyBusinessProcessMapping;
use Workdo\BusinessProcessMapping\Events\UpdateBusinessProcessMapping;
use Workdo\BusinessProcessMapping\Mail\ComposeMail;
use Workdo\Contract\Entities\Contract;
use Workdo\Lead\Entities\Deal;
use Workdo\Lead\Entities\Lead;
use Workdo\PropertyManagement\Entities\Property;

class BusinessProcessMappingController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(BusinessProcessMappingDatatable $dataTable)
    {
        if (\Auth::user()->isAbleTo('businessprocessmapping manage')) {
            return $dataTable->render('business-process-mapping::businessprocessmapping.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function mappingIndex($related_id, $id)
    {
        $businessProcess = BusinessProcessMapping::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('related_to', $related_id)->get();

        $businessProcesses = [];
        foreach ($businessProcess as $business) {
            $idsArray = explode(',', $business->related_assign);
            if (in_array($id, $idsArray)) {
                $businessProcesses[] = $business;
            }
            $related = Related::find($business->related_to);
            $commaSeparatedString = '';
            if ($related->related == 'Other') {
                $commaSeparatedString = $business->related_assign;
            } else {
                $value = null;
                $idsArray = explode(',', $business->related_assign);

                foreach ($related as $relation) {
                    if ($related->related == 'Project') {
                        $value = Project::where('workspace', getActiveWorkSpace())->whereIn('id', $idsArray)->pluck('name')->toArray();
                        $commaSeparatedString = implode(',', $value);
                    } elseif ($related->related == 'Task') {
                        $value = Task::where('workspace', getActiveWorkSpace())->whereIn('id', $idsArray)->pluck('title')->toArray();
                        $commaSeparatedString = implode(',', $value);
                    } elseif ($related->related == 'Lead') {
                        $value = Lead::where('workspace_id', getActiveWorkSpace())->whereIn('id', $idsArray)->pluck('name')->toArray();
                        $commaSeparatedString = implode(',', $value);
                    } elseif ($related->related == 'Deal') {
                        $value = Deal::where('workspace_id', getActiveWorkSpace())->whereIn('id', $idsArray)->pluck('name')->toArray();
                        $commaSeparatedString = implode(',', $value);
                    } elseif ($related->related == 'Property') {
                        $value = Property::where('workspace', getActiveWorkSpace())->whereIn('id', $idsArray)->pluck('name')->toArray();
                        $commaSeparatedString = implode(',', $value);
                    } elseif ($related->related == 'Contract') {
                        $value = Contract::where('workspace', getActiveWorkSpace())->whereIn('id', $idsArray)->pluck('subject')->toArray();
                        $commaSeparatedString = implode(',', $value);
                    }
                }
            }
            $business->relatedTo = $commaSeparatedString;
        }
        return view('business-process-mapping::businessprocessmapping.mappingindex', compact('businessProcesses'));
    }


    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if (module_is_active('Taskly')) {
            $relateds = Related::get();
        } else {
            $relateds = Related::where('model_name', 'Other')->get();
        }
        return view('business-process-mapping::businessprocessmapping.create', compact('relateds'));
    }

    public function relatedGet(Request $request)
    {
        $related = Related::findOrFail($request->related_id);

        $value = null;

        foreach ($related as $relation) {

            if ($related->related == 'Project') {
                $value = Project::where('workspace', getActiveWorkSpace())->pluck('name', 'id');
            } elseif ($related->related == 'Task') {
                $value = Task::where('workspace', getActiveWorkSpace())->pluck('title', 'id');
            } elseif ($related->related == 'Lead') {
                $value = Lead::where('workspace_id', getActiveWorkSpace())->pluck('name', 'id');
            } elseif ($related->related == 'Deal') {
                $value = Deal::where('workspace_id', getActiveWorkSpace())->pluck('name', 'id');
            } elseif ($related->related == 'Property') {
                $value = Property::where('workspace', getActiveWorkSpace())->pluck('name', 'id');
            } elseif ($related->related == 'Contract') {
                $value = Contract::where('workspace', getActiveWorkSpace())->pluck('subject', 'id');
            } elseif ($related->related == 'Other') {
                $value = 'Other';
            }
        }

        return response()->json($value);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (\Auth::user()->isAbleTo('businessprocessmapping create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'description' => 'required',
                    'related_to' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $businessProcess              = new BusinessProcessMapping();
            $businessProcess->title       = $request->title;
            $businessProcess->description = $request->description;
            $businessProcess->related_to  = $request->related_to ?? '';
            if ($businessProcess->related_assign != 'Other') {
                if (is_array($request->value)) {
                    $businessProcess->related_assign = implode(",", $request->value);
                } else {
                    $businessProcess->related_assign = $request->value;
                }
            } else {
                $businessProcess->related_assign = $request->value;
            }
            $businessProcess->created_by  = creatorId();
            $businessProcess->workspace   = getActiveWorkSpace();
            $businessProcess->save();
            event(new CreateBusinessProcessMapping($request, $businessProcess));
            $id = $businessProcess->id;

            return redirect()->route('store.flowchart', $id)->with('success', __('The business process mapping has been created successfully'));
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
        if (\Auth::user()->isAbleTo('businessprocessmapping manage')) {
            $mapping =BusinessProcessMapping::find($id);
            return view('business-process-mapping::businessprocessmapping.description' ,compact('mapping'));
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
        $business = BusinessProcessMapping::find($id);
        $relateds = Related::get()->pluck('related', 'id');
        $related_name = null; // Initialize $related_name

        if ($business->related_to != null) {
            $related_name = Related::find($business->related_to);

            $value = null;

            if ($related_name->related == 'Project') {
                $value = Project::where('workspace', getActiveWorkSpace())->pluck('name', 'id');
            } elseif ($related_name->related == 'Task') {
                $value = Task::where('workspace', getActiveWorkSpace())->pluck('title', 'id');
            } elseif ($related_name->related == 'Lead') {
                $value = Lead::where('workspace_id', getActiveWorkSpace())->pluck('name', 'id');
            } elseif ($related_name->related == 'Deal') {
                $value = Deal::where('workspace_id', getActiveWorkSpace())->pluck('name', 'id');
            } elseif ($related_name->related == 'Property') {
                $value = Property::where('workspace', getActiveWorkSpace())->pluck('name', 'id');
            } elseif ($related_name->related == 'Contract') {
                $value = Contract::where('workspace', getActiveWorkSpace())->pluck('subject', 'id');
            } elseif ($related_name->related == 'Other') {
                $value = ['Other'];
            }

            return view('business-process-mapping::businessprocessmapping.edit', compact('relateds', 'id', 'business', 'value', 'related_name'));
        }
        return view('business-process-mapping::businessprocessmapping.edit', compact('relateds', 'id', 'business', 'related_name'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */

    public function relatedUpdate(Request $request)
    {
        $related = Related::find($request->related_id);
        $options = [];

        if ($related) {
            if ($related->related == 'Project') {
                $projects = Project::where('workspace', getActiveWorkSpace())->pluck('name', 'id');
                $options = $this->formatOptions($projects, $request->selectedValue);
            } elseif ($related->related == 'Task') {
                $tasks = Task::where('workspace', getActiveWorkSpace())->pluck('title', 'id');
                $options = $this->formatOptions($tasks, $request->selectedValue);
            } elseif ($related->related == 'Lead') {
                $lead = Lead::where('workspace_id', getActiveWorkSpace())->pluck('name', 'id');
                $options = $this->formatOptions($lead, $request->selectedValue);
            } elseif ($related->related == 'Deal') {
                $deal = Deal::where('workspace_id', getActiveWorkSpace())->pluck('name', 'id');
                $options = $this->formatOptions($deal, $request->selectedValue);
            } elseif ($related->related == 'Property') {
                $property = Property::where('workspace', getActiveWorkSpace())->pluck('name', 'id');
                $options = $this->formatOptions($property, $request->selectedValue);
            } elseif ($related->related == 'Contract') {
                $contract = Contract::where('workspace', getActiveWorkSpace())->pluck('subject', 'id');
                $options = $this->formatOptions($contract, $request->selectedValue);
            } elseif ($related->related == 'Other') {
                $options = ['Other'];
            }
        }

        return response()->json($options);
    }

    private function formatOptions($data, $selectedValue = null)
    {
        $options = [];

        foreach ($data as $key => $value) {
            $options[] = [
                'id' => $key,
                'text' => $value,
                'selected' => ($key == $selectedValue) ? true : false,
            ];
        }

        return $options;
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->isAbleTo('businessprocessmapping edit')) {
            $businessProcess = BusinessProcessMapping::find($id);

            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'description' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $businessProcess->title       = $request->title;
            $businessProcess->description = $request->description;
            $businessProcess->related_to  = $request->related_to;
            if ($businessProcess->related_assign != 'Other') {
                if (is_array($request->value)) {
                    $businessProcess->related_assign = implode(",", $request->value);
                } else {
                    $businessProcess->related_assign = $request->value;
                }
            } else {
                $businessProcess->related_assign = $request->value;
            }
            $businessProcess->created_by  = creatorId();
            $businessProcess->workspace   = getActiveWorkSpace();
            $businessProcess->save();
            event(new UpdateBusinessProcessMapping($request, $businessProcess));
            return redirect()->back()->with('success', __('The business process mapping details are updated successfully'));
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
        if (\Auth::user()->isAbleTo('businessprocessmapping delete')) {
            $businessProcess = BusinessProcessMapping::find($id);
            event(new DestroyBusinessProcessMapping($businessProcess));
            $businessProcess->delete();

            return redirect()->back()->with('success', __('The business process mapping has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function flowchart(Request $request)
    {
        $company_id = creatorId();
        $workspace_id = getActiveWorkSpace();
        return view('business-process-mapping::businessprocessmapping.flowchart', compact('company_id', 'workspace_id'));
    }

    public function storeFlowChart(Request $request, $id)
    {
        $user = Auth::user();
        $business = BusinessProcessMapping::find($id);

        $nodes = $business->nodes ?? json_encode($business->nodes);
        $connectors = $business->connectors ?? json_encode($business->connectors);
        $company_id = creatorId();
        $workspace_id = getActiveWorkSpace();


        return view('business-process-mapping::businessprocessmapping.flowchart', compact('nodes', 'connectors', 'id', 'company_id', 'workspace_id'));
    }

    public function getFlowChart(Request $request)
    {
        $data = $request->input('data');

        $decodedData = json_decode($data);

        $nodes = $decodedData->nodes;
        $connectors = $decodedData->connectors;

        $id = $request->input('id');

        $businessProcess = BusinessProcessMapping::find($id);

        $businessProcess->nodes = json_encode($nodes);
        $businessProcess->connectors = json_encode($connectors);

        $businessProcess->save();

        return response()->json(['message' => 'Data stored successfully']);
    }

    public function bussinessMapSharedLink(Request $request, $id)
    {
        $id = \Illuminate\Support\Facades\Crypt::decrypt($id);
        $user = Auth::user();
        $business = BusinessProcessMapping::find($id);
        $company_id = $business->created_by;
        $workspace_id = getActiveWorkSpace();
        if (isset($business->nodes)) {
            $nodes = $business->nodes ?? json_encode($business->nodes);
            $connectors = $business->connectors ?? json_encode($business->connectors);
            return view('business-process-mapping::businessprocessmapping.sharedlink', compact('nodes', 'connectors', 'id', 'company_id', 'workspace_id'));
        }
        return redirect()->route('dashboard');
    }

    public function flowchartPreview(Request $request, $id)
    {
        $user = Auth::user();
        $business = BusinessProcessMapping::find($id);

        $nodes = $business->nodes ?? json_encode($business->nodes);
        $connectors = $business->connectors ?? json_encode($business->connectors);
        $company_id = creatorId();
        $workspace_id = getActiveWorkSpace();
        return view('business-process-mapping::businessprocessmapping.sharedlink', compact('nodes', 'connectors', 'id', 'company_id', 'workspace_id'));
    }

    public function sendMail($id)
    {
        $businessId = $id;
        return view('business-process-mapping::businessprocessmapping.sendmail', compact('businessId'));
    }

    public function sendMailFlowchart(Request $request)
    {
        $businessid = $request->businessId;
        $business = BusinessProcessMapping::find($businessid);

        try {
            $setconfing =  SetConfigEmail();
            if ($setconfing ==  true) {
                try {
                    Mail::to($request->email_to)->send(new ComposeMail($request->subject, $request->content, $business));
                } catch (\Exception $e) {
                    $smtp_error['status'] = false;
                    $smtp_error['msg'] = $e->getMessage();
                }
            } else {
                $smtp_error['status'] = false;
                $smtp_error['msg'] = __('Something went wrong please try again ');
            }
        } catch (\Exception $e) {
            $smtp_error['status'] = false;
            $smtp_error['msg'] = $e->getMessage();
        }
        return redirect()->back()->with('success', __('Mail Send Successfully.'));
    }
}
