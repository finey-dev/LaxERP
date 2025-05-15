<?php

namespace Workdo\ApiDocsGenerator\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\WorkSpace;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Events\DestroyWorkSpace;
use App\Events\DefaultData;

class WorkSpaceApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function index()
    {
        $activeWorkspace = WorkSpace::select('id','name','slug')->find(getActiveWorkSpace());
        $workspaces = getWorkspace()->map(function($workspace){
            return [
                'id'=>$workspace->id,
                'name'=>$workspace->name,
                'slug'=>$workspace->slug
            ];
        });
        $data = [];
        $data['active_workspace'] = $activeWorkspace;
        $data['workspaces'] = $workspaces;
        if(!empty($activeWorkspace)){
            return response()->json(['status'=>'success','data'=>$data],200);
        }
        else{
            return response()->json(['status'=>'error','message'=>__('No data available')],404);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('api-docs-generator::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if(Auth::user()->type != 'super admin'){
            $canUse=  PlanCheck('Workspace',Auth::user()->id);
            if($canUse == false)
            {
                return response()->json(['status'=>'error','message'=>'You have maxed out the total number of Workspace allowed on your current plan'],403);
            }
        }
        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required|unique:work_spaces,name',
            ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=>$messages->first()],403);
        }

        try {
            $workspace = new WorkSpace();
            $workspace->name = $request->name;
            $workspace->created_by = Auth::user()->id;
            $workspace->save();

            $user = Auth::user();
            $user->active_workspace =$workspace->id;
            $user->save();
            User::CompanySetting(Auth::user()->id,$workspace->id);

            if(!empty(Auth::user()->active_module))
            {
                event(new DefaultData(Auth::user()->id,$workspace->id,Auth::user()->active_module));
            }
            $data = [];
            $data['id'] = $workspace->id;
            $data['name'] = $workspace->name;
            $data['slug'] = $workspace->slug;
            return response()->json(['status'=>'success', 'message'=>__('Workspace create successfully!'),'data'=>$data],200);
        }catch (\Exception $e) {
            return response()->json(['status'=>'error', 'message'=>$e->getMessage()],500);
        }
    }

    // /**
    //  * Show the specified resource.
    //  * @param int $id
    //  * @return Renderable
    //  */
    // public function show($id)
    // {
    //     return view('api-docs-generator::show');
    // }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $workSpace = WorkSpace::select('id','name','slug')->where('created_by', creatorId())->where('id', $id)->first();
        if(!empty($workSpace)){
            return response()->json(['status'=>'success','data'=>$workSpace],200);
        }
        else{
            return response()->json(['status'=>'error','message'=>__('No data available')],404);
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

        try {
            $workSpace = WorkSpace::where('created_by', creatorId())->where('id', $id)->first();
            if(!empty($workSpace)){
                $validator = \Validator::make(
                    $request->all(), [
                        'name' => 'required|unique:work_spaces,name,'.$workSpace->id,
                    ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();
                    return response()->json(['status'=>'error','message'=> $messages->first()],403);
                }

                $workSpace->name = $request->name;
                $workSpace->save();
                return response()->json(['status'=>'success','message'=> __('Workspace updated successfully!')],200);
            }
            else{
                return response()->json(['status'=>'error','message'=>__('No data available')],404);
            }
        } catch (\Exception $e) {
            return response()->json(['status'=>'error', 'message'=>$e->getMessage()],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($workspace_id)
    {

        $objUser   = Auth::user();
        $workspace = Workspace::where('created_by', creatorId())->where('id', $workspace_id)->first();

        if($workspace && $workspace->created_by == $objUser->id)
        {
            $other_workspac = Workspace::where('created_by',$objUser->id)->first();
            if(!empty($other_workspac))
            {
                $objUser->active_workspace = $other_workspac->id;
                $objUser->save();
            }
            // first parameter workspace
            event(new DestroyWorkSpace($workspace));

            $workspace->delete();
            return response()->json(['status'=>'success','message'=> __('Workspace Deleted Successfully!')],200);
        }
        else
        {
            return response()->json(['status'=>'errors','message'=> __("You can't delete Workspace!")],403);
        }
    }

    public function change($workspace_id)
    {
        $objUser   = Auth::user();
        $check = WorkSpace::where('created_by', creatorId())->where('id', $workspace_id)->first();
        if(!empty($check) && $check->created_by == $objUser->id)
        {
            $users = User::where('email',\Auth::user()->email)->where('workspace_id',$workspace_id)->first();
            if(empty($users))
            {
                $users = User::where('email',\Auth::user()->email)->Where('id',$check->created_by)->first();
            }
            $user = User::find($users->id);
            $user->active_workspace = $workspace_id;
            $user->save();
            if(!empty($user)){
                Auth::login($user);
                return response()->json(['status'=>'success', 'message'=>'User Workspace change successfully.'],200);
            }
            return response()->json(['status'=>'success', 'message'=>'User Workspace change successfully.'],200);
        }else{
           return response()->json(['status'=>'error','message'=> "Workspace not found."],404);
        }
    }
}
