<?php

namespace Workdo\Recruitment\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Workdo\ActivityLog\Entities\AllActivityLog;
use Workdo\FileSharing\Entities\FileShare;
use Workdo\Hrm\Entities\Branch;
use Workdo\Hrm\Entities\Department;
use Workdo\Hrm\Entities\Designation;
use Workdo\Hrm\Entities\DocumentType;
use Workdo\Hrm\Entities\Employee;
use Workdo\Hrm\Entities\EmployeeDocument;
use Workdo\Hrm\Entities\PayslipType;
use Workdo\Recruitment\DataTables\JobArchiveDataTable;
use Workdo\Recruitment\DataTables\JobOnBoardDataTable;
use Workdo\Recruitment\Entities\CustomQuestion;
use Workdo\Recruitment\Entities\InterviewSchedule;
use Workdo\Recruitment\Entities\Job;
use Workdo\Recruitment\Entities\JobApplication;
use Workdo\Recruitment\Entities\JobApplicationNote;
use Workdo\Recruitment\Entities\JobApplicationNotes;
use Workdo\Recruitment\Entities\JobApplicationTodos;
use Workdo\Recruitment\Entities\JobCandidate;
use Workdo\Recruitment\Entities\JobOnBoard;
use Workdo\Recruitment\Entities\JobStage;
use Workdo\Recruitment\Entities\OfferLetter;
use Workdo\Recruitment\Events\ConvertToEmployee;
use Workdo\Recruitment\Events\CreateJobApplication;
use Workdo\Recruitment\Events\CreateJobApplicationNote;
use Workdo\Recruitment\Events\CreateJobApplicationRating;
use Workdo\Recruitment\Events\CreateJobApplicationSkill;
use Workdo\Recruitment\Events\CreateJobApplicationStageChange;
use Workdo\Recruitment\Events\CreateJobBoard;
use Workdo\Recruitment\Events\DestroyJobApplication;
use Workdo\Recruitment\Events\DestroyJobApplicationNote;
use Workdo\Recruitment\Events\DestroyJobBoard;
use Workdo\Recruitment\Events\JobApplicationArchive;
use Workdo\Recruitment\Events\JobApplicationChangeOrder;
use Workdo\Recruitment\Events\UpdateJobBoard;

class JobApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if (Auth::user()->isAbleTo('jobapplication manage')) {
            $stages = JobStage::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->orderBy('order', 'asc')->get();

            $jobs = Job::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('title', 'id');
            $jobs->prepend('All', '');

            $stage = JobStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('title', 'id');
            $stage->prepend('All', '');

            if (isset($request->start_date) && !empty($request->start_date)) {

                $filter['start_date'] = $request->start_date;
            } else {

                $filter['start_date'] = date("Y-m-d", strtotime("-1 month"));
            }

            if (isset($request->end_date) && !empty($request->end_date)) {

                $filter['end_date'] = $request->end_date;
            } else {

                $filter['end_date'] = date("Y-m-d H:i:s", strtotime("+1 hours"));
            }

            if (isset($request->job) && !empty($request->job)) {

                $filter['job'] = $request->job;
            } else {
                $filter['job'] = '';
            }

            if (isset($request->stage) && !empty($request->stage)) {

                $filter['stage'] = $request->stage;
            } else {
                $filter['stage'] = '';
            }

            return view('recruitment::jobApplication.index', compact('stages', 'jobs', 'filter', 'stage'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(Request $request)
    {
        if (Auth::user()->isAbleTo('jobapplication create')) {
            if (!empty($request->job_id)) {
                $jobId = $request->job_id;
                $jobs = Job::where('id', $request->job_id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('title', 'id');
            } else {
                $jobId = '';
                $jobs = Job::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('title', 'id');
                $jobs->prepend('--', '');
            }
            $questions = CustomQuestion::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $application_type = JobApplication::$application_type;
            $job_candidate = JobCandidate::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            return view('recruitment::jobApplication.create', compact('jobs', 'questions', 'application_type', 'jobId', 'job_candidate'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('jobapplication create')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'job' => 'required',
                    'application_type' => 'required',
                    'name' => 'required',
                    'email' => 'required',
                    'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            if (!empty($request->profile)) {

                $filenameWithExt = $request->file('profile')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('profile')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $uplaod = upload_file($request, 'profile', $fileNameToStore, 'JobApplication');
                if ($uplaod['flag'] == 1) {
                    $url = $uplaod['url'];
                } else {
                    return redirect()->back()->with('error', $uplaod['msg']);
                }
            }

            if (!empty($request->resume)) {

                $filenameWithExt1 = $request->file('resume')->getClientOriginalName();
                $filename1        = pathinfo($filenameWithExt1, PATHINFO_FILENAME);
                $extension1       = $request->file('resume')->getClientOriginalExtension();
                $fileNameToStore1 = $filename1 . '_' . time() . '.' . $extension1;

                $uplaod = upload_file($request, 'resume', $fileNameToStore1, 'JobApplication');
                if ($uplaod['flag'] == 1) {
                    $url1 = $uplaod['url'];
                } else {
                    return redirect()->back()->with('error', $uplaod['msg']);
                }
            }

            if (!empty($request->job_candidate)) {
                $job_candidate = JobCandidate::findOrFail($request->job_candidate);
            }

            $stage = JobStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->first();
            if (!empty($stage)) {
                $job                   = new JobApplication();
                $job->job              = $request->job;
                $job->job_candidate    = !empty($request->job_candidate) ? $request->job_candidate : null;
                $job->application_type = $request->application_type;
                $job->name             = $request->name;
                $job->email            = $request->email;
                $job->phone            = $request->phone;
                if (!empty($job_candidate)) {
                    $job->profile          = !empty($job_candidate->profile) ? $job_candidate->profile : '';
                    $job->resume           = !empty($job_candidate->resume) ? $job_candidate->resume : '';
                } else {
                    $job->profile          = !empty($request->profile) ? $url : '';
                    $job->resume           = !empty($request->resume) ? $url1 : '';
                }
                $job->cover_letter     = $request->cover_letter;
                $job->dob              = $request->dob;
                $job->gender           = $request->gender;
                $job->country          = $request->country;
                $job->state            = $request->state;
                $job->stage            = $stage->id;
                $job->city             = $request->city;
                $job->custom_question  = json_encode($request->question);
                $job->workspace        = getActiveWorkSpace();
                $job->created_by       = creatorId();
                $job->save();

                event(new CreateJobApplication($request, $job));

                return redirect()->back()->with('success', __('The job application has been created successfully.'));
            } else {
                return redirect()->back()->with('error', __('Please create job stage'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Request $request, $ids)
    {
        if (Auth::user()->isAbleTo('jobapplication show')) {
            $id             = Crypt::decrypt($ids);
            $jobApplication = JobApplication::find($id);
            $jobOnBoards    = JobOnBoard::where('application', $id)->where('type', 'internal')->first();
            $interview      = InterviewSchedule::where('candidate', $id)->get();
            $notes          = JobApplicationNote::where('application_id', $id)->get();
            $jobApplication_attachments = [];
            if (module_is_active('FileSharing')) {
                $jobApplication_attachments = FileShare::where('related_id', $id)->where('type', 'Job Application')->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            }
            $jobApplication_notes = JobApplicationNotes::where('jobapplication_id', $id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $jobapplication_todos = JobApplicationTodos::where('related_id', $id)->where('sub_module', 'job application')->where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->get();

            $activitys = [];
            $creatorId = creatorId();
            $getActiveWorkSpace = getActiveWorkSpace();
            if (module_is_active('ActivityLog')) {

                $activitys = AllActivityLog::join('users', 'all_activity_logs.user_id', '=', 'users.id')
                    ->select('all_activity_logs.*', 'users.name', 'users.type')
                    ->where('all_activity_logs.created_by', '=', $creatorId)
                    ->where('all_activity_logs.workspace', '=', $getActiveWorkSpace)
                    ->where('all_activity_logs.sub_module', '=', 'Job Application')
                    ->orderBy('all_activity_logs.created_at', 'desc')
                    ->get();
                if (!empty($request->filter)) {
                    $activitys = AllActivityLog::join('users', 'all_activity_logs.user_id', '=', 'users.id')
                        ->select('all_activity_logs.*', 'users.name', 'users.type')
                        ->where('all_activity_logs.created_by', '=', $creatorId)
                        ->where('all_activity_logs.workspace', '=', $getActiveWorkSpace)
                        ->where('all_activity_logs.sub_module', '=', $request->filter)
                        ->orderBy('all_activity_logs.created_at', 'desc')
                        ->get();
                }
                if (!empty($request->staff)) {
                    $activitys = AllActivityLog::join('users', 'all_activity_logs.user_id', '=', 'users.id')
                        ->select('all_activity_logs.*', 'users.name', 'users.type')
                        ->where('all_activity_logs.created_by', '=', $creatorId)
                        ->where('all_activity_logs.workspace', '=', $getActiveWorkSpace)
                        ->where('all_activity_logs.user_id', '=', $request->staff)
                        ->where('all_activity_logs.sub_module', '=', $request->filter)
                        ->orderBy('all_activity_logs.created_at', 'desc')
                        ->get();
                }
            }
            $staffs = User::where('created_by', '=', $creatorId)->where('workspace_id', '=', $getActiveWorkSpace)->orWhere('id', $creatorId)->get();

            $stages = JobStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();

            return view('recruitment::jobApplication.show', compact('jobApplication', 'notes', 'stages', 'jobOnBoards', 'interview', 'id', 'jobApplication_attachments', 'jobApplication_notes', 'jobapplication_todos', 'activitys', 'staffs', 'creatorId'));
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
        return redirect()->route('job-application.index')->with('error', __('Permission denied.'));
        return view('recruitment::edit');
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
    public function destroy(JobApplication $jobApplication)
    {
        if (Auth::user()->isAbleTo('jobapplication delete')) {
            if ($jobApplication->profile != null) {
                delete_file($jobApplication->profile);
            }
            if ($jobApplication->resume != null) {
                delete_file($jobApplication->resume);
            }
            event(new DestroyJobApplication($jobApplication));
            $jobApplication->delete();

            return redirect()->back()->with('success', __('The job application has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function order(Request $request)
    {
        if (Auth::user()->isAbleTo('jobapplication move')) {
            $post = $request->all();
            foreach ($post['order'] as $key => $item) {
                $application        = JobApplication::where('id', '=', $item)->first();
                $application->order = $key;
                $application->stage = $post['stage_id'];
                $application->save();
            }
            event(new JobApplicationChangeOrder($request, $application));
            return response()->json(['status'=>1]);

            // return redirect()->route('job-application.index')->with('success', __('Job successfully updated'));
        } else {
            return redirect()->route('job-application.index')->with('error', __('Permission denied.'));
        }
    }

    public function addSkill(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('jobapplication add skill')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'skill' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $job        = JobApplication::find($id);
            $job->skill = $request->skill;
            $job->save();

            event(new CreateJobApplicationSkill($request, $job));

            return redirect()->back()->with('success', __('Job application skill successfully added.'));
        } else {
            return redirect()->route('job-application.index')->with('error', __('Permission denied.'));
        }
    }

    public function addNote(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('jobapplication add note')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'note' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $note                 = new JobApplicationNote();
            $note->application_id = $id;
            $note->note           = $request->note;
            $note->note_created   = Auth::user()->id;
            $note->created_by     = Auth::user()->id;
            $note->save();

            event(new CreateJobApplicationNote($request, $note));

            return redirect()->back()->with('success', __('Job application notes successfully added.'));
        } else {
            return redirect()->route('job-application.index')->with('error', __('Permission denied.'));
        }
    }

    public function destroyNote($id)
    {
        if (Auth::user()->isAbleTo('jobapplication delete note')) {
            $note = JobApplicationNote::find($id);

            event(new DestroyJobApplicationNote($note));

            $note->delete();

            return redirect()->back()->with('success', __('The job application notes has been deleted successfully deleted.'));
        } else {
            return redirect()->route('job-application.index')->with('error', __('Permission denied.'));
        }
    }

    public function rating(Request $request, $id)
    {
        try {
            $jobApplication         = JobApplication::find($id);
            $jobApplication->rating = $request->rating;
            $jobApplication->save();

            event(new CreateJobApplicationRating($request, $jobApplication));
            return response()->json(['success' => __('The candidate rating successfully added')], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => __('Something went wrong please try again.')], 401);
        }
    }

    public function archive($id)
    {
        $jobApplication = JobApplication::find($id);
        if ($jobApplication->is_archive == 0) {
            $jobApplication->is_archive = 1;
            $jobApplication->save();

            event(new JobApplicationArchive($jobApplication));

            return redirect()->back()->with('success', __('Job application successfully added to archive.'));
        } else {
            $jobApplication->is_archive = 0;
            $jobApplication->save();

            event(new JobApplicationArchive($jobApplication));

            return redirect()->route('job.application.archived')->with('success', __('Job application successfully remove to archive.'));
        }
    }

    public function archived(JobArchiveDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('jobonboard manage')) {

            return $dataTable->render('recruitment::jobApplication.archived');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    //    -----------------------Job OnBoard-----------------------------_

    public function jobOnBoard(JobOnBoardDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('jobonboard manage')) {

            return $dataTable->render('recruitment::jobApplication.onboard');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function jobBoardCreate($id)
    {
        if (Auth::user()->isAbleTo('jobonboard create')) {
            $company_settings = getCompanyAllSetting();
            $status          = JobOnBoard::$status;
            $job_type        = JobOnBoard::$job_type;
            $salary_duration = JobOnBoard::$salary_duration;
            $salary_type     = PayslipType::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $applications    = InterviewSchedule::select('interview_schedules.*', 'job_applications.name')->join('job_applications', 'interview_schedules.candidate', '=', 'job_applications.id')->where('job_applications.is_employee', '!=', 1)->where('interview_schedules.workspace', getActiveWorkSpace())->get()->pluck('name', 'candidate');
            $applications->prepend('-', '');

            $branches = Branch::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            $users = User::where('created_by', '=', creatorId())->where('type', '=', 'client')->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
            if (count($users) != 0) {

                $users->prepend(__('Select Client'), '');
            }

            $job_type        = Job::$job_type;

            $recruitment_type = [];

            if (module_is_active('Hrm')) {
                $recruitment_type = [
                    '' => __('Select Type'),
                    'internal' => __('Internal'),
                    'client' => __('Client'),
                ];
            } else {
                $recruitment_type = [
                    '' => __('Select Type'),
                    'client' => __('Client'),
                ];
            }

            return view('recruitment::jobApplication.onboardCreate', compact('id', 'status', 'applications', 'salary_type', 'job_type', 'salary_duration', 'users', 'recruitment_type', 'branches', 'company_settings'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function jobBoardStore(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('jobonboard create')) {
            $rules = [
                'joining_date' => 'required',
                'status' => 'required',
                'type' => 'required',
            ];

            if (module_is_active('Hrm') && $request->has('branch_id') && $request->branch != null) {
                $rules['branch_id'] = 'required';
            }

            if ($request->has('user_id') && $request->user_id != null) {
                $rules['user_id'] = 'required';
            }

            $validator = \Validator::make(
                $request->all(),
                $rules
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }


            $id = ($id == 0) ? $request->application : $id;

            $jobBoard                  = new JobOnBoard();
            $jobBoard->application     = $id;
            $jobBoard->joining_date    = $request->joining_date;
            $jobBoard->job_type        = $request->job_type;
            $jobBoard->type            = $request->type;
            if ($request->type == 'internal') {
                $jobBoard->branch_id       = !empty($request->branch_id) ? $request->branch_id : 0;
                $jobBoard->user_id         = 0;
            } else {
                $jobBoard->user_id         = $request->user_id;
                $jobBoard->branch_id       = 0;
            }
            $jobBoard->days_of_week    = $request->days_of_week;
            $jobBoard->salary          = $request->salary;
            $jobBoard->salary_type     = $request->salary_type;
            $jobBoard->salary_duration = $request->salary_duration;
            $jobBoard->status          = $request->status;
            $jobBoard->workspace       = getActiveWorkSpace();
            $jobBoard->created_by      = creatorId();
            $jobBoard->save();

            event(new CreateJobBoard($request, $jobBoard));

            $interview = InterviewSchedule::where('candidate', $id)->first();
            if (!empty($interview)) {
                $interview->delete();
            }

            return redirect()->back()->with('success', __('Candidate successfully added in job board.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function jobBoardEdit($id)
    {
        if (Auth::user()->isAbleTo('jobonboard edit')) {
            $jobOnBoard        = JobOnBoard::find($id);
            $status            = JobOnBoard::$status;
            $job_type          = JobOnBoard::$job_type;
            $salary_duration   = JobOnBoard::$salary_duration;
            $salary_type       = PayslipType::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            $company_settings = getCompanyAllSetting();

            $branches = Branch::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            $users = User::where('created_by', '=', creatorId())->where('type', '=', 'client')->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
            if (count($users) != 0) {

                $users->prepend(__('Select Client'), '');
            }

            $recruitment_type = [];

            if (module_is_active('Hrm')) {
                $recruitment_type = [
                    '' => __('Select Recruitment Type'),
                    'internal' => __('Internal'),
                    'client' => __('Client'),
                ];
            } else {
                $recruitment_type = [
                    '' => __('Select Recruitment Type'),
                    'client' => __('Client'),
                ];
            }

            return view('recruitment::jobApplication.onboardEdit', compact('jobOnBoard', 'status', 'job_type', 'salary_duration', 'salary_type', 'users', 'recruitment_type', 'branches', 'company_settings'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function jobBoardUpdate(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('jobonboard edit')) {

            $rules = [
                'joining_date' => 'required',
                'status' => 'required',
                'type' => 'required',
            ];

            if (module_is_active('Hrm') && $request->has('branch_id') && $request->branch != null) {
                $rules['branch_id'] = 'required';
            }

            if ($request->has('user_id') && $request->user_id != null) {
                $rules['user_id'] = 'required';
            }

            $validator = \Validator::make(
                $request->all(),
                $rules
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $jobBoard                        = JobOnBoard::find($id);
            $jobBoard->joining_date          = $request->joining_date;
            $jobBoard->job_type              = $request->job_type;
            $jobBoard->type            = $request->type;
            if ($request->type == 'internal') {
                $jobBoard->branch_id       = !empty($request->branch_id) ? $request->branch_id : 0;
                $jobBoard->user_id         = 0;
            } else {
                $jobBoard->user_id         = $request->user_id;
                $jobBoard->branch_id       = 0;
            }
            $jobBoard->days_of_week          = $request->days_of_week;
            $jobBoard->salary                = $request->salary;
            $jobBoard->salary_type           = $request->salary_type;
            $jobBoard->salary_duration       = $request->salary_duration;
            $jobBoard->status                = $request->status;
            $jobBoard->save();

            event(new UpdateJobBoard($request, $jobBoard));

            return redirect()->back()->with('success', __('The job board candidate details are updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function jobBoardDelete($id)
    {
        if (Auth::user()->isAbleTo('jobonboard delete')) {
            $jobBoard = JobOnBoard::find($id);

            event(new DestroyJobBoard($jobBoard));

            $jobBoard->delete();

            return redirect()->route('job.on.board')->with('success', __('The job onBoard has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function jobBoardConvert($id)
    {
        if (Auth::user()->isAbleTo('jobonboard convert')) {
            $jobOnBoard       = JobOnBoard::find($id);
            $user             = User::where('id', $jobOnBoard->convert_to_employee)->first();
            $documents        = DocumentType::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $branches         = Branch::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $departments      = Department::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $designations     = Designation::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $employees        = Employee::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $employeesId      = Employee::employeeIdFormat($this->employeeNumber());
            $roles             = Role::where('created_by', creatorId())->whereNotIn('name', \Auth::user()->not_emp_type)->get()->pluck('name', 'id');
            $location_type    = Employee::$location_type;

            return view('recruitment::jobApplication.convert', compact('jobOnBoard', 'employees', 'employeesId', 'departments', 'designations', 'documents', 'branches', 'roles', 'user', 'location_type'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function jobBoardConvertData(Request $request, $id)
    {
        if (Auth::user()->type != 'super admin') {
            $canUse =  PlanCheck('User', \Auth::user()->id);
            if ($canUse == false) {
                return redirect()->back()->with('error', 'You have maxed out the total number of User allowed on your current plan');
            }
        }
        $roles            = Role::where('created_by', creatorId())->where('id', $request->roles)->first();
        $jobOnBoard       = JobOnBoard::where('id', $id)->first();
        if (Auth::user()->isAbleTo('jobonboard convert')) {
            $rules = [
                'name' => 'required',
                'roles' => 'required',
                'dob' => 'required',
                'gender' => 'required',
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
                'address' => 'required',
                'email' => 'required|unique:users',
                'password' => 'required',
                'branch_id' => 'required',
                'department_id' => 'required',
                'designation_id' => 'required',
            ];

            if (module_is_active('BiometricAttendance')) {
                $rules['biometric_emp_id'] = [
                    'required',
                    Rule::unique('employees')->where(function ($query) {
                        return $query->where('created_by', creatorId())->where('workspace', getActiveWorkSpace());
                    })
                ];
            }

            $validator = \Validator::make(
                $request->all(),
                $rules
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->withInput()->with('error', $messages->first());
            }

            $user = User::create(
                [
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'password' => Hash::make($request['password']),
                    'email_verified_at' => date('Y-m-d h:i:s'),
                    'type' => $roles->name,
                    'lang' => 'en',
                    'workspace_id' => getActiveWorkSpace(),
                    'active_workspace' => getActiveWorkSpace(),
                    'created_by' => creatorId(),
                ]
            );
            $user->addRole($roles);
            if (!empty($request->document) && !is_null($request->document)) {
                $document_implode = implode(',', array_keys($request->document));
            } else {
                $document_implode = null;
            }

            if (isset($request->payment_requires_work_advice)) {
                $payment_requires_work_advice = $request->payment_requires_work_advice;
            } else {
                $payment_requires_work_advice = 'off';
            }

            $employee = Employee::create(
                [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'dob' => $request['dob'],
                    'gender' => $request['gender'],
                    'phone' => $request['phone'],
                    'address' => $request['address'],
                    'email' => $user->email,
                    'employee_id' => $this->employeeNumber(),
                    'branch_id' => $request['branch_id'],
                    'department_id' => $request['department_id'],
                    'designation_id' => $request['designation_id'],
                    'company_doj' => $request['company_doj'],
                    'documents' => $document_implode,
                    'account_holder_name' => $request['account_holder_name'],
                    'account_number' => $request['account_number'],
                    'bank_name' => $request['bank_name'],
                    'bank_identifier_code' => $request['bank_identifier_code'],
                    'branch_location' => $request['branch_location'],
                    'tax_payer_id' => $request['tax_payer_id'],
                    'hours_per_day' => $request['hours_per_day'],
                    'annual_salary' => $request['annual_salary'],
                    'days_per_week' => $request['days_per_week'],
                    'fixed_salary' => $request['fixed_salary'],
                    'hours_per_month' => $request['hours_per_month'],
                    'rate_per_day' => $request['rate_per_day'],
                    'days_per_month' => $request['days_per_month'],
                    'rate_per_hour' => $request['rate_per_hour'],
                    'payment_requires_work_advice' => $payment_requires_work_advice,
                    'workspace' => $user->workspace_id,
                    'created_by' => $user->created_by,
                ]
            );

            if (!empty($employee)) {
                $JobOnBoard                      = JobOnBoard::find($id);
                $JobOnBoard->convert_to_employee = $user->id;
                $JobOnBoard->save();
            }
            if ($request->hasFile('document')) {
                foreach ($request->document as $key => $document) {

                    $filenameWithExt = $request->file('document')[$key]->getClientOriginalName();
                    $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension       = $request->file('document')[$key]->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                    $uplaod = multi_upload_file($document, 'document', $fileNameToStore, 'emp_document');
                    if ($uplaod['flag'] == 1) {
                        $url = $uplaod['url'];
                    } else {
                        return redirect()->back()->with('+error', $uplaod['msg']);
                    }
                    $employee_document = EmployeeDocument::create(
                        [
                            'employee_id' => $employee['employee_id'],
                            'document_id' => $key,
                            'document_value' => !empty($url) ? $url : '',
                            'workspace' => getActiveWorkSpace(),
                            'created_by' => creatorId(),
                        ]
                    );
                    $employee_document->save();
                }
            }

            $job_application = JobApplication::find($jobOnBoard->application);
            $job_application->is_employee = 1;
            $job_application->save();

            $company_settings = getCompanyAllSetting();
            if (!empty($company_settings['Create User']) && $company_settings['Create User']  == true) {
                $User        = User::where('id', $user->id)->where('workspace_id', '=',  getActiveWorkSpace())->first();
                $uArr = [
                    'email' => $User->email,
                    'password' => $request['password'],
                ];
                $resp = EmailTemplate::sendEmailTemplate('New User', [$User->email], $uArr);
                return redirect()->back()->with('success', __('Application successfully converted to employee.') . ((!empty($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
            }
            event(new ConvertToEmployee($request, $employee));

            return redirect()->route('job.on.board')->with('success', __('Application successfully converted to employee.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function employeeNumber()
    {
        $latest = Employee::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->employee_id + 1;
    }

    public function getByJob(Request $request)
    {
        try {
            $job                  = Job::find($request->id);
            $job->applicant       = !empty($job->applicant) ? explode(',', $job->applicant) : '';
            $job->visibility      = !empty($job->visibility) ? explode(',', $job->visibility) : '';
            $job->custom_question = !empty($job->custom_question) ? explode(',', $job->custom_question) : '';

            return json_encode($job);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getByJobCandiate(Request $request)
    {
        try {
            $job_candidate = JobCandidate::find($request->id);
            if ($job_candidate) {
                $job_candidate->name    = !empty($job_candidate->name) ? explode(',', $job_candidate->name) : '';
                $job_candidate->email   = !empty($job_candidate->email) ? explode(',', $job_candidate->email) : '';
                $job_candidate->phone   = !empty($job_candidate->phone) ? explode(',', $job_candidate->phone) : '';
                $job_candidate->dob     = !empty($job_candidate->dob) ? explode(',', $job_candidate->dob) : '';
                $job_candidate->gender  = !empty($job_candidate->gender) ? explode(',', $job_candidate->gender) : '';
                $job_candidate->country = !empty($job_candidate->country) ? explode(',', $job_candidate->country) : '';
                $job_candidate->state   = !empty($job_candidate->state) ? explode(',', $job_candidate->state) : '';
                $job_candidate->city    = !empty($job_candidate->city) ? explode(',', $job_candidate->city) : '';
                $job_candidate->profile = !empty($job_candidate->profile) ? explode(',', $job_candidate->profile) : '';
                $job_candidate->resume  = !empty($job_candidate->resume) ? explode(',', $job_candidate->resume) : '';

                return response()->json($job_candidate);
            } else {
                return response()->json(['error' => 'Candidate not found'], 404);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Permission denied.'], 403);
        }
    }


    public function stageChange(Request $request)
    {
        $application        = JobApplication::where('id', '=', $request->schedule_id)->first();
        $application->stage = $request->stage;
        $application->save();

        event(new CreateJobApplicationStageChange($request, $application));

        return response()->json(
            [
                'success' => __('The candidate stage has been changed successfully.'),
            ],
            200
        );
    }

    public function list(Request $request)
    {
        if (Auth::user()->isAbleTo('jobapplication manage')) {
            $applications = JobApplication::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $stages = JobStage::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->orderBy('order', 'asc')->get();

            $jobs = Job::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('title', 'id');
            $jobs->prepend('All', '');

            $stage = JobStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('title', 'id');
            $stage->prepend('All', '');

            if (isset($request->start_date) && !empty($request->start_date)) {

                $filter['start_date'] = $request->start_date;
            } else {

                $filter['start_date'] = date("Y-m-d", strtotime("-1 month"));
            }

            if (isset($request->end_date) && !empty($request->end_date)) {

                $filter['end_date'] = $request->end_date;
            } else {

                $filter['end_date'] = date("Y-m-d H:i:s", strtotime("+1 hours"));
            }

            if (isset($request->job) && !empty($request->job)) {

                $filter['job'] = $request->job;
            } else {
                $filter['job'] = '';
            }

            if (isset($request->stage) && !empty($request->stage)) {

                $filter['stage'] = $request->stage;
            } else {
                $filter['stage'] = '';
            }

            return view('recruitment::jobApplication.list', compact('applications', 'filter', 'jobs', 'stages', 'stage'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function grid()
    {
        if (Auth::user()->isAbleTo('jobonboard manage')) {
            $jobOnBoards = JobOnBoard::where('created_by', creatorId())->where('workspace', getActiveWorkSpace());
            $jobOnBoards = $jobOnBoards->paginate(11);
            return view('recruitment::jobApplication.grid', compact('jobOnBoards'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function offerletterupdate($lang, Request $request)
    {
        $user = OfferLetter::updateOrCreate(['lang' =>   $lang, 'created_by' =>  \Auth::user()->id], ['content' => $request->offer_content, 'workspace' => getActiveWorkSpace()]);

        return redirect()->back()->with('success', __('Offer Letter successfully saved.'));
    }

    public function offerletterPdf($id)
    {
        $users = Auth::user();
        $currantLang = getActiveLanguage();
        $Offerletter = OfferLetter::where(['lang' =>   $currantLang, 'created_by' =>  creatorId(), 'workspace' => getActiveWorkSpace()])->first();

        $job = JobApplication::find($id);
        $Onboard = JobOnBoard::find($id);
        $name = JobApplication::find($Onboard->application);
        $job_title = job::find($name->job);
        $salary = PayslipType::find($Onboard->salary_type);


        $obj = [
            'applicant_name' => $name->name,
            'app_name' => env('APP_NAME'),
            'job_title' => $job_title->title,
            'job_type' => !empty($Onboard->job_type) ? $Onboard->job_type : '',
            'start_date' => $Onboard->joining_date,
            'workplace_location' => !empty($job->jobs->branches->name) ? $job->jobs->branches->name : '',
            'days_of_week' => !empty($Onboard->days_of_week) ? $Onboard->days_of_week : '',
            'salary' => !empty($Onboard->salary) ? $Onboard->salary : '',
            'salary_type' => !empty($salary->name) ? $salary->name : '',
            'salary_duration' => !empty($Onboard->salary_duration) ? $Onboard->salary_duration : '',
            'offer_expiration_date' => !empty($Onboard->joining_date) ? $Onboard->joining_date : '',

        ];
        $Offerletter->content = OfferLetter::replaceVariable($Offerletter->content, $obj);
        return view('recruitment::jobApplication.template.offerletterpdf', compact('Offerletter', 'name'));
    }

    public function offerletterDoc($id)
    {
        $users = Auth::user();
        $currantLang = getActiveLanguage();
        $Offerletter = OfferLetter::where(['lang' =>   $currantLang, 'created_by' =>   creatorId(), 'workspace' => getActiveWorkSpace()])->first();
        $job = JobApplication::find($id);
        $Onboard = JobOnBoard::find($id);
        $name = JobApplication::find($Onboard->application);
        $job_title = job::find($name->job);
        $salary = PayslipType::find($Onboard->salary_type);
        $obj = [
            'applicant_name' => $name->name,
            'app_name' => env('APP_NAME'),
            'job_title' => $job_title->title,
            'job_type' => !empty($Onboard->job_type) ? $Onboard->job_type : '',
            'start_date' => $Onboard->joining_date,
            'workplace_location' => !empty($job->jobs->branches->name) ? $job->jobs->branches->name : '',
            'days_of_week' => !empty($Onboard->days_of_week) ? $Onboard->days_of_week : '',
            'salary' => !empty($Onboard->salary) ? $Onboard->salary : '',
            'salary_type' => !empty($salary->name) ? $salary->name : '',
            'salary_duration' => !empty($Onboard->salary_duration) ? $Onboard->salary_duration : '',
            'offer_expiration_date' => !empty($Onboard->joining_date) ? $Onboard->joining_date : '',

        ];
        $Offerletter->content = OfferLetter::replaceVariable($Offerletter->content, $obj);
        return view('recruitment::jobApplication.template.offerletterdocx', compact('Offerletter', 'name'));
    }

    public function jobApplicationAttechment(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('jobapplication attachment upload')) {
            $job_aaplication = JobApplication::find($id);
            $file_name = time() . "_" . $request->file->getClientOriginalName();

            $upload = upload_file($request, 'file', $file_name, 'job_attachment', []);

            $fileSizeInBytes = \File::size($upload['url']);
            $fileSizeInKB = round($fileSizeInBytes / 1024, 2);

            if ($fileSizeInKB < 1024) {
                $fileSizeFormatted = $fileSizeInKB . " KB";
            } else {
                $fileSizeInMB = round($fileSizeInKB / 1024, 2);
                $fileSizeFormatted = $fileSizeInMB . " MB";
            }

            if ($upload['flag'] == 1) {
                $file                 = FileShare::create(
                    [
                        'related_id'   => $job_aaplication->job,
                        'file_name'    => $file_name,
                        'file_path'    => $upload['url'],
                        'file_size'    => $fileSizeFormatted,
                        'type'         => 'Job Application',
                        'auto_destroy' => 'off',
                        'password'     => null,
                        'workspace'    => getActiveWorkSpace(),
                        'created_by'   => creatorId(),
                    ]
                );
                $return               = [];
                $return['is_success'] = true;

                return response()->json($return);
            } else {

                return response()->json(
                    [
                        'is_success' => false,
                        'error' => $upload['msg'],
                    ],
                    401
                );
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function jobApplicationAttechmentDestroy($id)
    {
        if (Auth::user()->isAbleTo('jobapplication attachment delete')) {
            $file = FileShare::find($id);

            if (!empty($file->file_path)) {
                delete_file($file->file_path);
            }
            $file->delete();
            return redirect()->back()->with('success', __('The file has been deleted.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function jobApplicationNoteCreate($id)
    {
        if (Auth::user()->isAbleTo('jobapplication note create')) {
            $job_application = JobApplication::find($id);

            return view('recruitment::jobapplication_note.notecreate', compact('job_application'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function jobApplicationNoteStore(Request $request)
    {
        if (Auth::user()->isAbleTo('jobapplication note create')) {
            $job_application = JobApplication::find($request->jobapplication_id);

            $validator = \Validator::make(
                $request->all(),
                [
                    'description' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $job_note              = new JobApplicationNotes();
            $job_note->jobapplication_id      = $job_application->id;
            $job_note->description = $request->description;
            $job_note->workspace   = getActiveWorkSpace();
            $job_note->created_by  = creatorId();
            $job_note->save();

            return redirect()->back()->with('success', __('The note has been created successfully.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function jobApplicationNoteEdit(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('jobapplication note edit')) {
            $job_application = JobApplicationNotes::find($id);

            return view('recruitment::jobapplication_note.noteedit', compact('job_application'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function jobApplicationNoteUpdate(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('jobapplication note edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'description' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $job_note = JobApplicationNotes::find($id);
            $job_note->description = $request->description;
            $job_note->workspace   = getActiveWorkSpace();
            $job_note->created_by  = creatorId();
            $job_note->save();

            return redirect()->back()->with('success', __('The note details are updated successfully.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function jobApplicationNoteDestroy($id)
    {
        if (Auth::user()->isAbleTo('jobapplication note delete')) {
            $jobnotes = JobApplicationNotes::find($id);

            $jobnotes->delete();

            return redirect()->back()->with('success', __('The note has been deleted.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function jobApplicationNoteDescription($id)
    {
        if (Auth::user()->isAbleTo('jobapplication note show')) {
            $job_application = JobApplicationNotes::find($id);
            return view('recruitment::jobapplication_note.noteshow', compact('job_application'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function jobApplicationTodoCreate($id)
    {
        if (Auth::user()->isAbleTo('jobapplication todo create')) {
            $job = JobApplication::find($id);
            $users = User::where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->pluck('name', 'id');

            return view('recruitment::jobapplication_todo.create', compact('job', 'users'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function jobApplicationTodoStore(Request $request)
    {
        if (Auth::user()->isAbleTo('jobapplication todo create')) {
            $job = JobApplication::find($request->job_id);

            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'start_date' => 'required|after:yesterday',
                    'due_date' => 'required|after_or_equal:start_date',
                    'priority' => 'required',
                    'description' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $job_todo               = new JobApplicationTodos();
            $job_todo->related_id   = $job->id;
            $job_todo->title        = $request->title;
            $job_todo->description  = $request->description;
            $job_todo->status       = 1;
            $job_todo->priority     = $request->priority;
            $job_todo->start_date   = $request->start_date;
            $job_todo->due_date     = $request->due_date;
            $job_todo->assign_by    = Auth::user()->id;
            $job_todo->assigned_to  = implode(',', $request->assigned_to);
            $job_todo->module       = 'recruitment';
            $job_todo->sub_module   = 'job application';
            $job_todo->workspace_id = getActiveWorkSpace();
            $job_todo->created_by   = creatorId();
            $job_todo->save();

            return redirect()->back()->with('success', __('The ToDo has been created successfully.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function jobApplicationTodoEdit(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('jobapplication todo edit')) {
            $job_todo = JobApplicationTodos::find($id);
            $users = User::where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->pluck('name', 'id');

            return view('recruitment::jobapplication_todo.edit', compact('job_todo', 'users'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function jobApplicationTodoUpdate(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('jobapplication todo edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'start_date' => 'required|after:yesterday',
                    'due_date' => 'required|after_or_equal:start_date',
                    'priority' => 'required',
                    'description' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $job_todo = JobApplicationTodos::find($id);
            $job_todo->title       = $request->title;
            $job_todo->description = $request->description;
            $job_todo->status       = 1;
            $job_todo->priority     = $request->priority;
            $job_todo->start_date   = $request->start_date;
            $job_todo->due_date    = $request->due_date;
            $job_todo->assign_by   = Auth::user()->id;
            $job_todo->assigned_to = implode(',', $request->assigned_to);
            $job_todo->module       = 'recruitment';
            $job_todo->sub_module   = 'job application';
            $job_todo->workspace_id = getActiveWorkSpace();
            $job_todo->created_by  = creatorId();
            $job_todo->save();

            return redirect()->back()->with('success', __('The ToDo details are updated successfully.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function jobApplicationTodoDestroy($id)
    {
        if (Auth::user()->isAbleTo('jobapplication todo delete')) {
            $job_todo = JobApplicationTodos::find($id);

            $job_todo->delete();

            return redirect()->back()->with('success', __('The ToDo has been deleted.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function jobApplicationTodoShow($id)
    {
        if (Auth::user()->isAbleTo('jobapplication todo show')) {
            $job_todo = JobApplicationTodos::findOrFail($id);
            return view('recruitment::jobapplication_todo.show', compact('job_todo'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function jobApplicationActivitylogDestroy($id)
    {
        if (Auth::user()->isAbleTo('jobapplication activity delete')) {
            $job_todo = AllActivityLog::find($id);

            $job_todo->delete();

            return redirect()->back()->with('success', __('The Activitylog has been deleted.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function offerletterindex(Request $request){

        if (Auth::user()->isAbleTo('letter offer manage')) {
            if (request()->get('offerlangs')) {
                $offerlang = request()->get('offerlangs');
            } else {
                $offerlang = "en";
            }
            //offer letter
            $Offerletter = \Workdo\Recruitment\Entities\OfferLetter::all();
            $currOfferletterLang = \Workdo\Recruitment\Entities\OfferLetter::where('created_by', Auth::user()->id)->where('lang', $offerlang)->where('workspace', getActiveWorkSpace())->first();

            return view('recruitment::offerletter.index',compact('Offerletter','currOfferletterLang','offerlang'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
}
