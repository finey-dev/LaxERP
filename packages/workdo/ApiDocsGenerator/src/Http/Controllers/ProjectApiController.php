<?php

namespace Workdo\ApiDocsGenerator\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\Taskly\Entities\Task;
use Workdo\Taskly\Entities\Stage;
use Workdo\Taskly\Entities\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Workdo\Taskly\Entities\ActivityLog;
use Workdo\Taskly\Entities\BugComment;
use Workdo\Taskly\Entities\BugFile;
use Workdo\Taskly\Entities\BugReport;
use Workdo\Taskly\Entities\BugStage;
use Workdo\Taskly\Entities\Comment;
use Workdo\Taskly\Entities\Milestone;
use Workdo\Taskly\Entities\ProjectFile;
use Workdo\Taskly\Entities\SubTask;
use Workdo\Taskly\Entities\TaskFile;
use Workdo\Taskly\Entities\UserProject;
use Workdo\Taskly\Events\CreateBug;
use Workdo\Taskly\Events\UpdateBug;
use Workdo\Taskly\Events\CreateProject;
use Workdo\Taskly\Events\CreateTask;
use Workdo\Taskly\Events\DestroyBug;
use Workdo\Taskly\Events\DestroyProject;
use Workdo\Taskly\Events\DestroyTask;
use Workdo\Taskly\Events\UpdateProject;
use Workdo\Taskly\Events\UpdateTask;

class ProjectApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if (!module_is_active('Taskly')) {
            return response()->json(['status'=>'error','message'=>'Project Module Not Found!'],404);
        }
        $objUser          = Auth::user();
        if(Auth::user()->hasRole('client'))
        {
            $projects = Project::select('projects.*')->join('client_projects', 'projects.id', '=', 'client_projects.project_id')->where('client_projects.client_id', '=', Auth::user()->id)->where('projects.workspace', '=',$request->workspace_id)->get();
        }
        else
        {
            $projects = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $request->workspace_id)->get();
        }
        $all_projects = $projects->map(function($project){
            $users = $project->users->map(function($user){
                return [
                    'name'  =>$user->name,
                    'email' =>$user->email,
                    'avatar'=>get_file($user->avatar)
                ];
            });
            return [
                'id'                => $project->id,
                'name'              => $project->name,
                'status'            => $project->status,
                'description'       => $project->description,
                'start_date'        => $project->start_date,
                'end_date'          => $project->end_date,
                'budget'            => currency_format_with_sym($project->budget),
                'users'             => $users
            ];
        });
        return response()->json(["status"=>"success","data"=>$all_projects],200);
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
        if (!module_is_active('Taskly')) {
            return response()->json(['status'=>'error','message'=>'Project Module Not Found!'],404);
        }
        $validator = \Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'budget' => 'required|gt:0|numeric',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
            // 'users_list' => ['required', 'array', function ($attribute, $value, $fail) use($request) {
            //     $checkUser = User::where('email', $value)->where('workspace_id', $request->workspace_id)->where('created_by', creatorId())->first();

            //     if (empty($checkUser)) {
            //         $fail('Please Select Valid User!');
            //     }
            // }],
            'users_list' => ['required', 'array', function ($attribute, $value, $fail) use($request) {
                $invalidEmails = array_filter($value, function ($email) use($request) {
                    return !\DB::table('users')->where('email', $email)->where('workspace_id', $request->workspace_id)->exists();
                });

                if (!empty($invalidEmails)) {
                    $fail('The following emails are not registered: ' . implode(', ', $invalidEmails));
                }
            }],
        ]);

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=>$messages->first()],403);
        }

        $post = $request->all();

        $userList           = [];
        if(isset($post['users_list']))
        {
            $userList = $post['users_list'];
        }
        $userList[] = Auth::user()->email;
        $userList   = array_unique($userList);
        $objProject                     = new Project();
        $objProject->name               = $request->name;
        $objProject->description        = $request->description;
        $objProject->budget             = $request->budget;
        $objProject->start_date         = $request->start_date;
        $objProject->end_date           = $request->end_date;
        $objProject->copylinksetting    = '{"member":"on","client":"on","milestone":"off","progress":"off","basic_details":"on","activity":"off","attachment":"on","bug_report":"on","task":"off","invoice":"off","timesheet":"off" ,"password_protected":"off"}';
        $objProject->workspace          = $request->workspace_id;
        $objProject->created_by         = creatorId();
        $objProject->save();

        foreach($userList as $email)
        {
            $permission    = 'Member';
            $registerUsers = User::where('active_workspace',$request->workspace_id)->where('email', $email)->first();
            if($registerUsers)
            {
                if($registerUsers->id == Auth::user()->id)
                {
                    $permission = 'Owner';
                }
            }
            $this->inviteUser($registerUsers, $objProject, $permission);
        }

        if(module_is_active('CustomField'))
        {
            \Workdo\CustomField\Entities\CustomField::saveData($objProject, $request->customField);
        }
        event(new CreateProject($request, $objProject));

        return response()->json(['status'=>'success','message'=>'Project Created Successfully!'],200);


    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Request $request, $id)
    {
        if (!module_is_active('Taskly')) {
            return response()->json(['status'=>'error','message'=>'Project Module Not Found!'],404);
        }
        $project = Project::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();

        if($project)
        {
            $daysleft = round((((strtotime($project->end_date) - strtotime(date('Y-m-d'))) / 24) / 60) / 60);
            $chartData = $this->getProjectChart(
                [
                    'workspace_id' => $request->workspace_id,
                    'project_id' => $project->id,
                    'duration' => 'week',
                ]
            );
            $project_users = $project->users->map(function ($user) use ($project) {
                $complate_task = (int)count($project->user_done_tasks($user->id)) . '/' . (int)count($project->user_tasks($user->id));
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'complate_task' => $complate_task,
                ];
            });
            $project_clients = $project->clients->map(function($client) use ($project) {
                return [
                    'name'=>$client->name,
                    'email'=>$client->email
                ];
            });
            $project_milestone = $project->milestones->map(function($milestone){
                return [
                    'title'=>$milestone->title,
                    'status'=>$milestone->status,
                    'start_date'=>$milestone->start_date,
                    'end_date'=>$milestone->end_date,
                    'progress'=>$milestone->progress,
                    'cost'=> currency_format_with_sym($milestone->cost)
                ];
            });
            $project->setRelation('users', $project_users);
            $project->setRelation('clients', $project_clients);
            $project->setRelation('milestones', $project_milestone);

            $project_details = [
                'id'                => $project->id,
                'name'              => $project->name,
                'status'            => $project->status,
                'description'       => $project->description,
                'start_date'        => $project->start_date,
                'end_date'          => $project->end_date,
                'budget'            => $project->budget,
                'tags'              => $project->tags,
                'estimated_hrs'     => $project->estimated_hrs,
                'total_task'        => $project->countTask(),
                'total_comment'     => $project->countTaskComments(),
                'budget'            => currency_format_with_sym($project->budget),
                'users'             => $project_users,
                'clients'           => $project_clients,
                'milestones'        => $project_milestone,
            ];

            $data = [];
            $data['project'] = $project_details;
            $data['daysleft'] = $daysleft;
            $data['chartData'] = $chartData;
            return response()->json(['status'=>'success','data'=>$data],200);
        }
        else{
            return response()->json(['status'=>'error','message'=>__('Project Not Found!')],404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('api-docs-generator::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request,$id)
    {
        if (!module_is_active('Taskly')) {
            return response()->json(['status'=>'error','message'=>'Project Module Not Found!'],404);
        }
        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required',
                'description' => 'required',
                'budget'=>'required|gt:0|numeric',
                'start_date'=>'required|date_format:Y-m-d',
                'end_date'=>'required|date_format:Y-m-d',
                'users_list' => ['required', 'array', function ($attribute, $value, $fail) use($request) {
                    $invalidEmails = array_filter($value, function ($email) use($request) {
                        return !\DB::table('users')->where('email', $email)->where('workspace_id', $request->workspace_id)->exists();
                    });

                    if (!empty($invalidEmails)) {
                        $fail('The following emails are not registered: ' . implode(', ', $invalidEmails));
                    }
                }],
            ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=>$messages->first()],403);
        }

        $objProject = Project::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if($objProject){
            $existingUsers = UserProject::where('project_id', $objProject->id)
            ->pluck('user_id')
            ->toArray();

            $userList           = [];
            if(isset($request->users_list))
            {
                $userList = $request->users_list;
            }
            $userList[] = Auth::user()->email;
            $userList   = array_unique($userList);

            $updateUserIds = User::whereIn('email', $userList)
            ->pluck('id')
            ->toArray();
            $usersToRemove = array_diff($existingUsers, $updateUserIds);
            if (!empty($usersToRemove)) {
                UserProject::where('project_id', $objProject->id)
                    ->whereIn('user_id', $usersToRemove)
                    ->delete();
            }

            $objProject->name               = $request->name;
            $objProject->description        = $request->description;
            $objProject->budget             = $request->budget;
            $objProject->start_date         = $request->start_date;
            $objProject->end_date           = $request->end_date;
            $objProject->workspace          = $request->workspace_id;
            $objProject->created_by         = creatorId();
            $objProject->save();

            foreach($userList as $email)
            {
                $permission    = 'Member';
                $registerUsers = User::where('active_workspace',$request->workspace_id)->where('email', $email)->first();
                if($registerUsers)
                {
                    if($registerUsers->id == Auth::user()->id)
                    {
                        $permission = 'Owner';
                    }
                }
                $this->inviteUser($registerUsers, $objProject, $permission);
            }
        }
        $project = Project::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if($project){

            $project->name               = $request->name;
            $project->description        = $request->description;
            $project->budget             = $request->budget;
            $project->start_date         = $request->start_date;
            $project->end_date           = $request->end_date;
            $project->save();

            if(module_is_active('CustomField'))
            {
                \Workdo\CustomField\Entities\CustomField::saveData($project, $request->customField);
            }
            event(new UpdateProject($request, $project));

            return response()->json(['status'=>'success','message'=>'Project Updated Successfully!'],200);
        }else{
            return response()->json(['status'=>'error','message'=>'Project Not Found'],404);
        }


    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request,$id)
    {

        if (!module_is_active('Taskly')) {
            return response()->json(['status'=>'error','message'=>'Project Module Not Found!'],404);
        }
        $objUser = Auth::user();
        $project = Project::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$project){
            return response()->json(['status'=>'error','message'=>'Project Not Found'],404);
        }
        if($project->created_by == $objUser->id)
        {
            $task = Task::where('project_id', '=', $project->id)->count();
            $bug = BugReport::where('project_id', '=', $project->id)->count();

            if($task == 0 && $bug == 0)
            {
                UserProject::where('project_id', '=', $id)->delete();
                $ProjectFiles=ProjectFile::where('project_id', '=', $id)->get();
                foreach($ProjectFiles as $ProjectFile){

                    delete_file($ProjectFile->file_path);
                    $ProjectFile->delete();
                }

                Milestone::where('project_id', '=', $id)->delete();
                ActivityLog::where('project_id', '=', $id)->delete();

                if(module_is_active('CustomField'))
                {
                    $customFields = \Workdo\CustomField\Entities\CustomField::where('module','taskly')->where('sub_module','projects')->get();
                    foreach($customFields as $customField)
                    {
                        $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $id)->where('field_id',$customField->id)->first();
                        if(!empty($value)){
                            $value->delete();
                        }
                    }
                }
                event(new DestroyProject($project));
                $project->delete();

                return response()->json(['status'=>'success', 'message'=>'Project Deleted Successfully!']);
            }
            else
            {
                return response()->json(['status'=>'error', 'message'=>'There are some Task and Bug on Project, please remove it first!']);
            }
        }
        else
        {
            return response()->json(['status'=>'error', 'message'=>"You can't Delete Project!"]);
        }
    }

    public function taskBoard(Request $request,$projectID)
    {
        if (!module_is_active('Taskly')) {
            return response()->json(['status'=>'error','message'=>'Project Module Not Found!'],404);
        }
        $currentWorkspace = $request->workspace_id;
        $objUser = Auth::user();
        if(Auth::user()->hasRole('client'))
        {
            $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();
        }
        else
        {
            $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();
        }
        $stages = $statusClass = [];
        if($project)
        {
            $stages = Stage::where('workspace_id', '=', $request->workspace_id)->orderBy('order')->get();
            foreach($stages as $status)
            {
                $statusClass[] = 'task-list-' . str_replace(' ', '_', $status->id);

                $task          = Task::where('workspace', $request->workspace_id)->where('project_id', '=', $projectID);
                if(!Auth::user()->hasRole('client') && !Auth::user()->hasRole('company'))
                {
                    if(isset($objUser) && $objUser)
                    {
                        $task->whereRaw("find_in_set('" . $objUser->id . "',assign_to)");
                    }
                }
                $task->orderBy('order');
                $tasks = $task->where('status', '=', $status->id)->get();
                $status['tasks'] = $tasks->map(function($task_value){
                    return [
                        'id'            => $task_value->id,
                        'title'         => $task_value->title,
                        'priority'      => $task_value->priority,
                        'description'   => $task_value->description,
                        'start_date'    => $task_value->start_date,
                        'due_date'      => $task_value->due_date,
                        'users'         => $task_value->users()->map(function($user){
                            return [
                                'id'     => $user->id,
                                'name'   => $user->name,
                                'avatar' => get_file($user->avatar)
                            ];
                        })
                    ];
                });
            }
            $stages = $stages->map(function($value){
                return [
                    'id'=>$value->id,
                    'name'=>$value->name,
                    'color'=>$value->color,
                    'complete'=>$value->complete,
                    'order'=>$value->order,
                    'tasks'=>$value->tasks,
                ];
            });
            $data = [];
            $data['project']['id']        = $project->id;
            $data['stages']               = $stages;
            $data['statusClass']          = $statusClass;
            return response()->json(['status'=>'success','data'=>$data],200);
        }
        else
        {
            return response()->json(['status'=>'error','error'=> __('Task Note Found For This Project.')],404);
        }
    }

    public function getMilestone(Request $request,$id){
        if (!module_is_active('Taskly')) {
            return response()->json(['status'=>'error','message'=>'Project Module Not Found!'],404);
        }
        $project = Project::where('workspace',$request->workspace_id)->where('created_by',creatorId())->where('id',$id)->first();
        if(!$project){
            return response()->json(['status'=>'error','message'=>'Project Not Found'],404);
        }

        $milestone = $project->milestones
            ->map(function($value){
                return [
                    'id' => $value->id,
                    'title'=>$value->title,
                    'status'=>$value->status,
                    'cost'=>$value->cost,
                    'summary'=>$value->summary,
                ];
            });

        return response()->json(['status'=>'success', 'data'=>$milestone]);

    }

    public function taskStore(Request $request,$id)
    {

        if (!module_is_active('Taskly')) {
            return response()->json(['status'=>'error','message'=>'Project Module Not Found!'],404);
        }
        $userIds = User::select('users.id')
            ->join('user_projects', 'user_projects.user_id', '=', 'users.id')
            ->where('user_projects.project_id', '=', $id)
            ->where('users.workspace_id', '=', $request->workspace_id)
            ->pluck('id')
            ->toArray();


        $validator = \Validator::make(
            $request->all(), [
                'title' => 'required',
                'priority' => 'required',
                'assign_to' => ['required','array', function ($attribute, $value, $fail) use ($userIds) {
                    foreach ($value as $assignToId) {
                        if (!in_array($assignToId, $userIds)) {
                            $fail("The selected $attribute with ID $assignToId is invalid.");
                        }
                    }
                }],
                'start_date' => 'required|date_format:Y-m-d',
                'due_date' => 'required|date_format:Y-m-d',
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message'=>$messages->first()],403);
        }

        $objUser          = Auth::user();
        if($objUser->hasRole('client'))
        {
            $project = Project::where('workspace', '=', $request->workspace_id)->where('id', '=', $id)->first();
        }
        else
        {
            $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $request->workspace_id)->where('projects.created_by', '=', creatorId())->where('projects.id', '=', $id)->first();
        }

        if(!$project)
        {
            return response()->json(['status'=>'error','message'=>'Project Not Found'],404);
        }

        $post  = $request->all();
        $stage = Stage::where('workspace_id', '=', $request->workspace_id)->orderBy('order')->first();

        if(!$stage)
        {
            return response()->json(['status'=>'error','message'=>'Please add stages first']);
        }

        $task                      = new Task();
        $task->title               = $request->title;
        $task->priority            = $request->priority;
        $task->milestone_id        = $request->milestone_id;
        $task->start_date          = $request->start_date;
        $task->due_date            = $request->due_date;
        $task->status              = $stage->id;
        $task->project_id          = $id;
        $task->assign_to           = implode(",", $request->assign_to);
        $task->workspace           = $request->workspace_id;
        $task->save();

        ActivityLog::create(
            [
                'user_id' => Auth::user()->id,
                'user_type' => get_class(Auth::user()),
                'project_id' => $id,
                'log_type' => 'Create Task',
                'remark' => json_encode(['title' => $task->title]),
            ]
        );

        if(module_is_active('CustomField'))
        {
            \Workdo\CustomField\Entities\CustomField::saveData($task, $request->customField);
        }

        event(new CreateTask($request,$task));

        return response()->json(['status'=>'success', 'message'=>'Task Create Successfully!']);

    }

    public function taskShow(Request $request,$taskID)
    {
        if (!module_is_active('Taskly')) {
            return response()->json(['status'=>'error','message'=>'Project Module Not Found!'],404);
        }

        $task              = Task::where('workspace', '=', $request->workspace_id)->where('id',$taskID)->first();
        $objUser          = Auth::user();
        if($task){
            $task_detail = [
                'id'            => $task->id,
                'title'         => $task->title,
                'priority'      => $task->priority,
                'description'   => $task->description,
                'start_date'    => $task->start_date,
                'due_date'      => $task->due_date,
                'users'         => $task->users()->map(function($user){
                    return [
                        'id'     => $user->id,
                        'name'   => $user->name,
                        'avatar' => get_file($user->avatar)
                    ];
                }),
                'milestone'     => $task->milestone()->title  ,
                'comments'      => $task->comments->map(function($comment){
                    return [
                        'id'            => $comment->id,
                        'comment'       => $comment->comment,
                        'user_name'     => $comment->user->name,
                        'avatar'        => get_file($comment->user->avatar),
                    ];
                }),
                'files'         => $task->taskFiles->map(function($file){
                    return [
                        'file'      => get_file($file->file),
                    ];
                }),
                'sub_task'      => $task->sub_tasks->map(function($subTask){
                    return [
                        'name'          => $subTask->name,
                        'due_date'      => $subTask->due_date,
                        'status'        => $subTask->status
                    ];
                })
            ];
            $data   =[];
            $data['task']       = $task_detail;
            return response()->json(['status'=>'success','data'=>$data],200);
        }
        else{
            return response()->json(['status'=>'error','message'=>__('Task Not Found!')],404);
        }
    }

    public function taskUpdate(Request $request,$id,$taskID){

        if (!module_is_active('Taskly')) {
            return response()->json(['status'=>'error','message'=>'Project Module Not Found!'],404);
        }
        $currentWorkspace = $request->workspace_id;

        $userIds = User::select('users.*')->join('user_projects', 'user_projects.user_id', '=', 'users.id')->where('project_id', '=', $id)->where('users.workspace_id', '=', $currentWorkspace)->get()->pluck('id')->toArray();
        $validator = \Validator::make(
            $request->all(), [
                'title' => 'required',
                'priority' => 'required',
                'assign_to' => ['required','array', function ($attribute, $value, $fail) use ($userIds) {
                    foreach ($value as $assignToId) {
                        if (!in_array($assignToId, $userIds)) {
                            $fail("The selected $attribute with ID $assignToId is invalid.");
                        }
                    }
                }],
                'start_date' => 'required|date_format:Y-m-d',
                'due_date' => 'required|date_format:Y-m-d',
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message'=>$messages->first()],403);
        }

        $objUser          = Auth::user();
        if($objUser->hasRole('client'))
        {
            $project = Project::where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $id)->first();
        }
        else
        {
            $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $id)->first();
        }

        if(!$project){
            return response()->json(['status'=>'error','message'=>'Project Not Found'],404);
        }

        $post              = $request->all();
        $post['assign_to'] = implode(",", $request->assign_to);
        $task              = Task::where('workspace', '=', $request->workspace_id)->where('project_id',$id)->where('id',$taskID)->first();
        if(!$task){
            return response()->json(['status'=>'error','message'=>'Task Not found'],404);
        }
        $task->title               = $request->title;
        $task->priority            = $request->priority;
        $task->start_date          = $request->start_date;
        $task->due_date            = $request->due_date;
        $task->assign_to           = implode(",", $request->assign_to);
        $task->save();
        if(module_is_active('CustomField'))
        {
            \Workdo\CustomField\Entities\CustomField::saveData($task, $request->customField);
        }
        event(new UpdateTask($request,$task));

        return response()->json(['status'=>'success','message'=>'Task Updated Successfully!']);

    }

    public function taskDelete(Request $request,$projectID,$taskID)
    {

        $objUser = Auth::user();
        event(new DestroyTask($taskID));
        $task              = Task::where('workspace', '=', $request->workspace_id)->where('project_id',$projectID)->where('id',$taskID)->first();
        if(!$task){
            return response()->json(['status'=>'error','message'=>'Task Not Found!']);
        }
        Comment::where('task_id', '=', $task->id)->delete();
        SubTask::where('task_id', '=', $task->id)->delete();
        $TaskFiles = TaskFile::where('task_id', '=', $task->id)->get();

        foreach($TaskFiles as $TaskFile){
            delete_file($TaskFile->file);
            $TaskFile->delete();
        }
        if(module_is_active('CustomField'))
        {
            $customFields = \Workdo\CustomField\Entities\CustomField::where('module','taskly')->where('sub_module','tasks')->get();
            foreach($customFields as $customField)
            {
                $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $task->id)->where('field_id',$customField->id)->first();
                if(!empty($value)){
                    $value->delete();
                }
            }
        }

        $task->delete();
        return response()->json(['status'=>'success', 'message' => 'Task Deleted Successfully!']);

    }

    public function bugStatusList(Request $request){
        $bugStages = BugStage::where('workspace_id', '=', $request->workspace_id)->where('created_by', creatorId())->orderBy('order')->get()->map(function($bugStage){
            return [
                'id'=>$bugStage->id,
                'name'=>$bugStage->name
            ];
        });
        return $bugStages;
    }

    public function bugReport(Request $request,$project_id)
    {
        if (!module_is_active('Taskly')) {
            return response()->json(['status'=>'error','message'=>'Project Module Not Found!'],404);
        }
        $currentWorkspace = $request->workspace_id;

        $objUser = Auth::user();
        if($objUser->hasRole('client'))
        {
            $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $project_id)->first();
        }
        else
        {
            $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $project_id)->first();
        }
        if($project)
        {
            $stages = $statusClass = [];

            $stages = BugStage::where('workspace_id', '=', $currentWorkspace)->orderBy('order')->get();

            foreach($stages as &$status)
            {
                $statusClass[] = 'task-list-' . str_replace(' ', '_', $status->id);
                $bugs           = BugReport::where('project_id', '=', $project_id);
                if($objUser->type != 'client')
                {
                    if(!Auth::user()->hasRole('client') && !Auth::user()->hasRole('company'))
                    {
                        $bugs->where('assign_to', '=', $objUser->id);
                    }
                }
                $bugs->orderBy('order');

                $status['bugs'] = $bugs->where('status', '=', $status->id)->get()->map(function($bug){
                    $user = [
                        'id'=>$bug->user->id,
                        'name'=>$bug->user->name,
                        'avatar'=>get_file($bug->user->avatar),
                    ];
                    return [
                        'id'                => $bug->id,
                        'title'             => $bug->title,
                        'priority'          => $bug->priority,
                        'description'       => $bug->description,
                        'order'             => $bug->order,
                        'description'       => $bug->description,
                        'users'             => $user
                    ];
                });
            }
            $all_stages = $stages->map(function($stage){
                return [
                    'id'            => $stage->id,
                    'name'          => $stage->name,
                    'color'         => $stage->color,
                    'bugs'          => $stage->bugs
                ];
            });
            $data = [];
            $data['project']['id'] = $project->id;
            $data['stages']  = $all_stages;
            $data['statusClass']  = $statusClass;
            return response()->json(['status'=>'success','data'=>$data],200);
        }
        else{
            return response()->json(['error'=> __('Bug Not Found For This Project.')],404);
        }

    }

    public function bugStore(Request $request,$projectID){

        if (!module_is_active('Taskly')) {
            return response()->json(['status'=>'error','message'=>'Project Module Not Found!'],404);
        }
        $userIds = User::select('users.*')->join('user_projects', 'user_projects.user_id', '=', 'users.id')->where('project_id', '=', $projectID)->get()->pluck('id')->toArray();

        $bugStages = BugStage::where('workspace_id', '=', $request->workspace_id)->orderBy('order')->get()->pluck('name','id');
        $statusNames = $bugStages->only($bugStages->keys()->toArray())->values()->toArray();
        $validator = \Validator::make(
            $request->all(), [
                'title' => 'required',
                'priority' => 'required',
                'assign_to' => ['required',Rule::in($userIds)],
                'status' => ['required', 'in:' . implode(',', $statusNames)],
                'description'=>'required'
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message'=>$messages->first()],403);
        }

        $objUser          = Auth::user();
        $currentWorkspace = $request->workspace_id;

        if($objUser->hasRole('client'))
        {
            $project = Project::where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();
        }
        else
        {
            $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();
        }
        if(!$project)
        {
            return response()->json(['status'=>'error','message'=>'Project Not Found!']);
        }

        $bug                    = new BugReport();
        $bug->title             = $request->title;
        $bug->priority          = $request->priority;
        $bug->status            = array_search($request->status,$bugStages->toArray());
        $bug->project_id        = $projectID;
        $bug->assign_to         = $request->assign_to;
        $bug->description       = $request->description;
        $bug->save();

        if(module_is_active('CustomField'))
        {
            \Workdo\CustomField\Entities\CustomField::saveData($bug, $request->customField);
        }

        ActivityLog::create(
            [
                'user_id' => $objUser->id,
                'user_type' => get_class($objUser),
                'project_id' => $projectID,
                'log_type' => 'Create Bug',
                'remark' => json_encode(['title' => $bug->title]),
            ]
        );
        event(new CreateBug($request, $bug));

        return response()->json(['status'=>'success','message'=>'Bug Create Successfully!']);

    }

    public function bugReportShow(Request $request, $bug_id)
    {
        if (!module_is_active('Taskly')) {
            return response()->json(['status'=>'error','message'=>'Project Module Not Found!'],404);
        }

        $bugCheck = BugReport::leftjoin('projects', 'projects.id', '=', 'bug_reports.project_id')->where('bug_reports.id', '=', $bug_id)->where('projects.workspace', $request->workspace_id)->first();
        if(empty($bugCheck)){
            return response()->json(['status'=>'error','message'=>'Project Bug Not Found!'],404);
        }

        $bug              = BugReport::find($bug_id);
        if($bug){
            $comments = $bug->comments->map(function($comment){
                return [
                    'user_name'         => $comment->user_type != 'client' ? $comment->user->name : $comment->client->name ,
                    'avatar'            => get_file($comment->user->avatar),
                    'comment'           => $comment->comment
                ];
            });
            $user = [
                'id'=>$bug->user->id,
                'name'=>$bug->user->name,
                'avatar'=>get_file($bug->user->avatar),
            ];
            $bug_detail = [

                'id'            => $bug->id,
                'title'         => $bug->title,
                'description'   => $bug->description,
                'start_date'    => company_date_formate($bug->created_at),
                'user'          => $user,
            ];
            $files = $bug->bugFiles->map(function($bugFile){
                return [
                    'file' => get_file($bugFile->file)
                ];
            });
            $objUser          = Auth::user();
            $data = [];
            $data['bug']  = $bug_detail;
            $data['comments'] = $comments;
            $data['files']     = $files;
            return response()->json(['status'=>'success','data'=>$data],200);

        }
        else
        {
            return response()->json(['status'=>'error','message'=>__('Bug Not Found For This Project')],404);
        }

    }

    public function bugUpdate(Request $request,$projectID,$bugID){

        if (!module_is_active('Taskly')) {
            return response()->json(['status'=>'error','message'=>'Project Module Not Found!'],404);
        }

        $userIds = User::select('users.*')->join('user_projects', 'user_projects.user_id', '=', 'users.id')->where('users.workspace_id', $request->workspace_id)->where('project_id', '=', $projectID)->get()->pluck('id')->toArray();
        $bugStages = BugStage::where('workspace_id', '=', $request->workspace_id)->orderBy('order')->get()->pluck('name','id');
        $statusNames = $bugStages->only($bugStages->keys()->toArray())->values()->toArray();
        $validator = \Validator::make(
            $request->all(), [
                'title' => 'required',
                'priority' => 'required',
                'assign_to' => ['required',Rule::in($userIds)],
                'status' => ['required', 'in:' . implode(',', $statusNames)],
            ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message'=>$messages->first()],403);
        }

        $objUser          = Auth::user();
        $currentWorkspace = $request->workspace_id;

        if($objUser->hasRole('client'))
        {
            $project = Project::where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();
        }
        else
        {
            $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();
        }

        if(!$project){
            return response()->json(['status'=>'error','message'=>'Project Not Found!'],404);
        }

        $bugCheck = BugReport::leftjoin('projects', 'projects.id', '=', 'bug_reports.project_id')->where('bug_reports.id', '=', $bugID)->where('bug_reports.project_id',$projectID)->where('projects.workspace', $request->workspace_id)->first();
        if(empty($bugCheck)){
            return response()->json(['status'=>'error','message'=>'Project Bug Not Found!'],404);
        }

        $bug                    = BugReport::where('project_id',$projectID)->where('id',$bugID)->first();
        if(!$bug){
            return response()->json(['status'=>'error','message'=>'Bug Not Found!'],404);
        }
        $bug->title             = $request->title;
        $bug->priority          = $request->priority;
        $bug->status            = array_search($request->status,$bugStages->toArray());
        $bug->assign_to         = $request->assign_to;
        $bug->description       = $request->description;
        $bug->save();

        if(module_is_active('CustomField'))
        {
            \Workdo\CustomField\Entities\CustomField::saveData($bug, $request->customField);
        }

        event(new UpdateBug($request, $bug));

        return response()->json(['status'=>'success','message'=>'Bug Updated Successfully!']);

    }

    public function bugDestroy(Request $request, $projectID,$bugID){

        if (!module_is_active('Taskly')) {
            return response()->json(['status'=>'error','message'=>'Project Module Not Found!'],404);
        }

        $bugCheck = BugReport::leftjoin('projects', 'projects.id', '=', 'bug_reports.project_id')->where('bug_reports.id', '=', $bugID)->where('bug_reports.project_id',$projectID)->where('projects.workspace', $request->workspace_id)->first();
        if(empty($bugCheck)){
            return response()->json(['status'=>'error','message'=>'Bug Not Found!'],404);
        }
        $bugReport = BugReport::where('project_id',$projectID)->where('id',$bugID)->first();
        if(!$bugReport){
            return response()->json(['status'=>'error','error'=>'Bug Not Found!']);
        }

        $objUser = Auth::user();
        BugComment::where('bug_id', '=', $bugID)->delete();
        $bugfiles = BugFile::where('bug_id', '=', $bugID)->get();

        foreach($bugfiles as $bugfile){
            delete_file($bugfile->file);
            $bugfile->delete();
        }

        if(module_is_active('CustomField'))
        {
            $customFields = \Workdo\CustomField\Entities\CustomField::where('module','taskly')->where('sub_module','bugs')->get();
            foreach($customFields as $customField)
            {
                $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $bugID)->where('field_id',$customField->id)->first();
                if(!empty($value)){
                    $value->delete();
                }
            }
        }

        event(new DestroyBug($bugReport));

        $bug     = $bugReport->delete();
        return response()->json(['status'=>'success','message'=>'Bug Deleted Successfully!']);

    }

    public function inviteUser($user, $project, $permission)
    {

        $arrData               = [];
        $arrData['user_id']    = $user->id;
        $arrData['project_id'] = $project->id;
        $is_invited            = UserProject::where($arrData)->first();
        $smtp_error =[];
        $smtp_error['status'] = true;
        $smtp_error['msg'] = '';
        $company_settings = getCompanyAllSetting();
        $project->url = route('projects.show',$project->id);
        if(!$is_invited)
        {
            UserProject::create($arrData);
            if($permission != 'Owner')
            {
                if (!empty($company_settings['User Invited']) && $company_settings['User Invited']  == true) {
                    $uArr = [
                        'name' => $user->name,
                        'project' => $project->name,
                        'project_creater_name' => $project->creater->name,
                        'url' => $project->url,
                    ];
                    try {

                        if($company_settings['User Invited'])
                        {
                            $resp = EmailTemplate::sendEmailTemplate('User Invited', [$user->email], $uArr);
                        }
                        else
                        {
                                    $smtp_error['status'] = false;
                                    $smtp_error['msg'] = __('Something went wrong please try again ');
                        }
                    } catch(\Exception $e) 
                    {
                        $smtp_error['status'] = false;
                        $smtp_error['msg'] = $e->getMessage();
                    }
                }
            }
            return $smtp_error;
        }
        else
        {
            $smtp_error['status'] = false;
            $smtp_error['msg'] = 'User already invited.';
            return $smtp_error;
        }
    }

    public function getProjectChart($arrParam)
    {
        $arrDuration = [];
        if($arrParam['duration'] && $arrParam['duration'] == 'week')
        {
            $previous_week = Project::getFirstSeventhWeekDay(-1);
            foreach($previous_week['datePeriod'] as $dateObject)
            {
                $arrDuration[$dateObject->format('Y-m-d')] = $dateObject->format('D');
            }
        }

        $arrTask = [
            'label' => [],
            'color' => [],
        ];
        $stages           = Stage::where('workspace_id', '=', $arrParam['workspace_id'])->orderBy('order');

        foreach($arrDuration as $date => $label)
        {
            $objProject = Task::select('status', DB::raw('count(*) as total'))->whereDate('created_at', '=', $date)->groupBy('status');
            if(isset($arrParam['project_id']))
            {
                $objProject->where('project_id', '=', $arrParam['project_id']);
            }
            if(isset($arrParam['workspace_id']))
            {
                $objProject->whereIn('project_id', function ($query) use ($arrParam){
                    $query->select('id')->from('projects')->where('workspace', '=', $arrParam['workspace_id']);
                });
            }
            $data = $objProject->pluck('total', 'status')->all();
            foreach($stages->pluck('name', 'id')->toArray() as $id => $stage)
            {
                $arrTask[$id][] = isset($data[$id]) ? $data[$id] : 0;
            }
            $arrTask['label'][] = __($label);
        }
        $arrTask['stages'] = $stages->pluck('name', 'id')->toArray();
        $arrTask['color'] = $stages->pluck('color')->toArray();
        return $arrTask;
    }
}
