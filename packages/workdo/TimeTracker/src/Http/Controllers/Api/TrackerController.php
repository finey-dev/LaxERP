<?php

namespace Workdo\TimeTracker\Http\Controllers\Api;

use App\Models\WorkSpace;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\Taskly\Entities\Project;
use Workdo\Taskly\Entities\Task;
use Workdo\Taskly\Entities\UserProject;
use Workdo\Timesheet\Entities\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Workdo\TimeTracker\Entities\TimeTracker;
use Workdo\TimeTracker\Entities\TrackPhoto;
use Workdo\TimeTracker\Events\CreateTimeTracker;
use Workdo\TimeTracker\Events\UpdateTimeTracker;
use Workdo\TimeTracker\Http\Traits\ApiResponser;

class TrackerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    use ApiResponser;
    public function index()
    {
        return view('time-tracker::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function login(Request $request)
    {

        $attr = $request->validate([
            'email' => 'required|string|email|',
            'password' => 'required|string'
        ]);

        if (!\Auth::attempt($attr)) {
            return $this->error('Credentials not match', 401);
        }

        $user = Auth::user();

        if (!module_is_active('TimeTracker')){
            auth()->user()->tokens()->delete();
            return $this->error('Please subscribe with timetacker add-on.', 500);
        }

        $workspace_id = $user->currant_workspace;
        $getworkspace = WorkSpace::where("id", $workspace_id)->first();

        $settings = [
            'shot_time' => !empty(company_setting('interval_time')) ? company_setting('interval_time') : 10,
        ];

        return $this->success([
            'token' => $user->createToken("API_TOKEN")->plainTextToken,
            'user' => auth()->user(),
            'settings' => $settings,
        ], 'Login successfully.');
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return $this->success([], 'Tokens Revoked');
    }

    public function getworkspace(Request $request)
    {

        $objUser = auth()->user();

        if ($objUser && $objUser->workspace_id) {
            $rs = Workspace::where('created_by', '=', $objUser->id)->pluck('name', 'id')->toArray();
        }

        return $this->success([
            'workspaces' =>  $rs,
        ], 'Get Workspace successfully.');
    }

    public function getProjects(Request $request)
    {
        $users = User::where('email', \Auth::user()->email)->get();

        $workspaceData = WorkSpace::select(['id', 'name'])
            ->where('is_disable', 1)
            ->where(function ($query) {
                $query->whereIn('id', function ($query) {
                    $query->select('workspace_id')
                        ->from('users')
                        ->where('email', \Auth::user()->email);
                })->orWhereIn('created_by', function ($query) {
                    $query->select('id')
                        ->from('users')
                        ->where('email', \Auth::user()->email);
                });
            })
            ->with(['projects' => function ($query) use ($users) {
                $query->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
                    ->whereIn('user_projects.user_id', $users->pluck('id')->toArray())
                    ->select('projects.name', 'projects.id', 'projects.workspace');
            }])
            ->get()
            ->toArray();

        return $this->success([
            'workspaces' => $workspaceData,
        ], 'Get Project List successfully.');
    }



    public function addTracker(Request $request)
    {

        // $user = auth()->user()->email;
        $user = User::where('email',auth()->user()->email)->where('workspace_id',$request->workspaces_id)->first();
        if ($request->has('action') && $request->action == 'start') {

            $validatorArray = [
                'task_id' => 'required|integer',
            ];
            $validator      = Validator::make(
                $request->all(),
                $validatorArray
            );
            if ($validator->fails()) {
                return error_res($validator->errors()->first(), 401);
            }
            $task = Task::find($request->task_id);
            if (empty($task)) {
                return $this->error('Invalid task', 401);
            }

            $project_id = isset($task->project_id) ? $task->project_id : '';
            TimeTracker::where('created_by', '=', $user->id)->where('is_active', '=', 1)->update(['end_time' => date("Y-m-d H:i:s")]);

            $track['name']        = $task->title;
            $track['project_id']  = $project_id;
            $track['workspace_id']      = $request->workspaces_id;
            $track['start_time']  = $request->has('time') ?  date("Y-m-d H:i:s", strtotime($request->input('time'))) : date("Y-m-d H:i:s");
            $track['task_id']     = $request->has('task_id') ? $request->input('task_id') : '';
            $track                = TimeTracker::create($track);
            $track->action        = 'start';

            event(new CreateTimeTracker($request,$track));

            return $this->success($track, 'Track successfully create.');


        } else {
            $validatorArray = [
                'task_id' => 'required|integer',
                'traker_id' => 'required|integer',
            ];
            $validator      = Validator::make(
                $request->all(),
                $validatorArray
            );
            if ($validator->fails()) {
                return error_res($validator->errors()->first());
            }
            $tracker = TimeTracker::where('id', $request->traker_id)->first();

            if ($tracker) {
                $tracker->end_time   = $request->has('time') ?  date("Y-m-d H:i:s", strtotime($request->input('time'))) : date("Y-m-d H:i:s");
                $tracker->is_active  = 0;
                $tracker->total_time = TimeTracker::diffance_to_time($tracker->start_time, $tracker->end_time);
                $tracker->save();

                event(new UpdateTimeTracker($request,$tracker));

                return $this->success($tracker, 'Stop time successfully.');
            }
        }
    }

    public function uploadImage(Request $request)
    {
        $user = auth()->user();
        $file = $request->imgName;
        $image_base64 = base64_decode($request->img);
        if ($request->has('tracker_id') && !empty($request->tracker_id)) {
            $app_path = base_path('uploads/traker_images/') . $request->tracker_id . '/';
            if (!file_exists($app_path)) {
                mkdir($app_path, 0777, true);
            }
        } else {
            $app_path = base_path('uploads/traker_images/');
            if (is_dir($app_path)) {
                mkdir($app_path, 0777, true);
            }
        }
        $file_name =  $app_path . $file;
        file_put_contents($file_name, $image_base64);
        $uploadedFile = new UploadedFile($file_name, $file, 'image/jpg', null, true);
        $request['img'] = $uploadedFile;
        $upload = upload_file($request,'img',$file,'traker_images',[]);
        if($upload['flag'] == 1){
            $url = $upload['url'];
        }else{
            return $this->error('File extension not allowed', 401);
        }
        $new = new TrackPhoto();
        $new->track_id = $request->tracker_id;
        $new->user_id  = $user->id;
        $new->workspace_id = 0;
        $new->img_path  = $file;
        $new->time  = $request->time;
        $new->status  = 1;
        $new->save();
        return $this->success([], 'Uploaded successfully.');
    }
}
