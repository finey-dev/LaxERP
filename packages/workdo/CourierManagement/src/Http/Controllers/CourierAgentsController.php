<?php

namespace Workdo\CourierManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\CourierManagement\DataTables\CourierAgentDataTable;
use Workdo\CourierManagement\Entities\CourierBranch;
use Illuminate\Support\Facades\Auth;
use Workdo\CourierManagement\Entities\CourierAgents;
use Workdo\CourierManagement\Events\CourierAgentscreate;
use Workdo\CourierManagement\Events\CourierAgentsdelete;
use Workdo\CourierManagement\Events\CourierAgentsupdate;

class CourierAgentsController extends Controller
{

    public function index(CourierAgentDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('courier agents manage')) {
            return $dataTable->render('courier-management::courier-agents.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function create()
    {
        if (Auth::user()->isAbleTo('courier agents create')) {
            $branches = CourierBranch::where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->get();
            return view('courier-management::courier-agents.create', compact('branches'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('courier agents create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $courier_agents                        = new CourierAgents();
            $courier_agents->name               = $request->name;
            $courier_agents->phone            = $request->phone;
            $courier_agents->email            = $request->email;
            $courier_agents->branch_id                = $request->branch_id;
            $courier_agents->status              = $request->status;
            $courier_agents->address              = $request->address;
            $courier_agents->workspace             = getActiveWorkSpace();
            $courier_agents->created_by            = creatorId();
            $courier_agents->save();
            event(new CourierAgentscreate($courier_agents, $request));

            return redirect()->route('courier-agents.index')->with('success', __('The Courier Agents has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function show($id)
    {
        if (Auth::user()->isAbleTo('courier agents show')) {
            $courier_agents = CourierAgents::with('branch')->find($id);
            return view('courier-management::courier-agents.show',compact('courier_agents'));
        } else {
            return redirect()->back()->with(['error' => __('Permission denied.')], 401);
        }
    }
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('courier agents edit')) {
            $courier_agents = CourierAgents::find($id);
                if ($courier_agents->created_by == creatorId() && $courier_agents->workspace == getActiveWorkSpace()) {
                    $courier_agent = CourierBranch::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
                    return view('courier-management::courier-agents.edit', compact('courier_agents', 'courier_agent'));
                } else {
                    return response()->json(['error' => __('Permission denied.')]);
                }

        } else {
            return response()->json(['error' => __('Permission denied.')]);
        }
    }
    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('courier agents edit')) {
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
            $courier_agents                        = CourierAgents::find($id);
            $courier_agents->name               = $request->name;
            $courier_agents->phone            = $request->phone;
            $courier_agents->email            = $request->email;
            $courier_agents->branch_id                = $request->branch_id;
            $courier_agents->status              = $request->status;
            $courier_agents->address              = $request->address;
            $courier_agents->workspace             = getActiveWorkSpace();
            $courier_agents->created_by            = creatorId();
            $courier_agents->update();
            event(new CourierAgentsupdate($courier_agents, $request));

            return redirect()->route('courier-agents.index')->with('success', __('The Courier Agents has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function destroy(Request $request,$id)
    {
        if (Auth::user()->isAbleTo('courier agents delete')) {
            $courier_agents = CourierAgents::find($id);
            if ($courier_agents->created_by == creatorId()  && $courier_agents->workspace == getActiveWorkSpace()) {
                event(new CourierAgentsdelete($courier_agents, $request));

                $courier_agents->delete();
                return redirect()->route('courier-agents.index')->with('success', __('The Courier Agents has been deleted successfully.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
