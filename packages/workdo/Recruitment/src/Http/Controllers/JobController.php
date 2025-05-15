<?php

namespace Workdo\Recruitment\Http\Controllers;

use App\Models\User;
use App\Models\WorkSpace;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\ActivityLog\Entities\AllActivityLog;
use Workdo\FileSharing\Entities\FileShare;
use Workdo\Hrm\Entities\Branch;
use Workdo\Recruitment\DataTables\JobDataTable;
use Workdo\Recruitment\Entities\CustomQuestion;
use Workdo\Recruitment\Entities\InterviewSchedule;
use Workdo\Recruitment\Entities\Job;
use Workdo\Recruitment\Entities\JobApplication;
use Workdo\Recruitment\Entities\JobApplicationNote;
use Workdo\Recruitment\Entities\JobAttachment;
use Workdo\Recruitment\Entities\JobAttechment;
use Workdo\Recruitment\Entities\JobCategory;
use Workdo\Recruitment\Entities\JobNotes;
use Workdo\Recruitment\Entities\JobOnBoard;
use Workdo\Recruitment\Entities\JobStage;
use Workdo\Recruitment\Entities\JobTodos;
use Workdo\Recruitment\Events\CreateJob;
use Workdo\Recruitment\Events\CreateJobApplication;
use Workdo\Recruitment\Events\DestroyJob;
use Workdo\Recruitment\Events\UpdateJob;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(JobDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('job manage')) {

            $data['total']     = Job::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->count();
            $data['active']    = Job::where('status', 'active')->where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->count();
            $data['in_active'] = Job::where('status', 'in_active')->where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->count();

            return $dataTable->render('recruitment::job.index', compact('data'));
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
        if (Auth::user()->isAbleTo('job create')) {
            $categories = JobCategory::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('title', 'id');

            $branches = [];
            if (module_is_active('Hrm')) {
                $branches = Branch::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            }

            $status = Job::$status;

            $customQuestion = CustomQuestion::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();

            $users = User::where('created_by', '=', creatorId())->where('type', '=', 'client')->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
            if (count($users) != 0) {

                $users->prepend(__('Select Client'), '');
            }

            $job_type        = Job::$job_type;

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

            return view('recruitment::job.create', compact('categories', 'status', 'branches', 'customQuestion', 'users', 'job_type', 'recruitment_type'));
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
        if (Auth::user()->isAbleTo('job create')) {

            $rules = [
                'title' => 'required',
                'status' => 'required',
                'location' => 'required',
                'position' => 'required|min:0',
                'salary_from' => 'required',
                'salary_to' => 'required',
                'start_date' => 'required|after:yesterday',
                'end_date' => 'required|after_or_equal:start_date',
                'job_type' => 'required',
                'category' => 'required',
                'recruitment_type' => 'required',
                'skill' => 'required',
                'description' => 'required',
                'requirement' => 'required',
                'custom_question.*' => 'required',
            ];

            if (module_is_active('Hrm') && $request->has('branch') && $request->branch != null) {
                $rules['branch'] = 'required';
            }

            if ($request->has('link_type') && $request->link_type == 'Custom Link') {
                $rules['job_link'] = 'required';
            }

            if (is_array($request->visibility) && in_array('terms', $request->visibility)) {
                $rules['terms_and_conditions'] = 'required';
            }

            $validator = \Validator::make(
                $request->all(),
                $rules
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $job                       = new Job();
            $job->title                = $request->title;
            $job->recruitment_type     = $request->recruitment_type;
            $job->branch               = !empty($request->branch) ? $request->branch : 0;
            $job->location             = !empty($request->location) ? $request->location : '';
            $job->category             = $request->category;
            $job->user_id              = $request->user_id;
            $job->skill                = $request->skill;
            $job->position             = $request->position;
            $job->status               = $request->status;
            $job->job_type             = $request->job_type;
            $job->salary_from          = $request->salary_from;
            $job->salary_to            = $request->salary_to;
            $job->start_date           = $request->start_date;
            $job->end_date             = $request->end_date;
            $job->description          = $request->description;
            $job->requirement          = $request->requirement;
            $job->address              = $request->address;
            $job->link_type            = $request->link_type;
            $job->job_link             = !empty($request->job_link) ? $request->job_link : null;
            $job->terms_and_conditions = !empty($request->terms_and_conditions) ? $request->terms_and_conditions : '';
            $job->code                 = uniqid();
            $job->applicant            = !empty($request->applicant) ? implode(',', $request->applicant) : '';
            $job->visibility           = !empty($request->visibility) ? implode(',', $request->visibility) : '';
            $job->custom_question      = !empty($request->custom_question) ? implode(',', $request->custom_question) : '';
            $job->workspace            = getActiveWorkSpace();
            $job->created_by           = creatorId();
            $job->save();

            event(new CreateJob($request, $job));

            return redirect()->route('job.index')->with('success', __('The job has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Request $request, Job $job)
    {
        if (Auth::user()->isAbleTo('job show')) {
            $status          = Job::$status;
            $applications = JobApplication::where('job', $job->id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $job_attachments = [];
            if (module_is_active('FileSharing')) {
                $job_attachments = FileShare::where('related_id', $job->id)->where('type', 'Jobs')->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            }
            $notes = JobNotes::where('job_id', $job->id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $todos = JobTodos::where('related_id', $job->id)->where('sub_module', 'jobs')->where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->get();

            $activitys = [];
            $creatorId = creatorId();
            $getActiveWorkSpace = getActiveWorkSpace();
            if (module_is_active('ActivityLog')) {
                $activitys = AllActivityLog::join('users', 'all_activity_logs.user_id', '=', 'users.id')
                    ->select('all_activity_logs.*', 'users.name', 'users.type')
                    ->where('all_activity_logs.created_by', '=', $creatorId)
                    ->where('all_activity_logs.workspace', '=', $getActiveWorkSpace)
                    ->where('all_activity_logs.sub_module', '=', 'Jobs')
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

            $job->applicant  = !empty($job->applicant) ? explode(',', $job->applicant) : '';
            $job->visibility = !empty($job->visibility) ? explode(',', $job->visibility) : '';
            $job->skill      = !empty($job->skill) ? explode(',', $job->skill) : '';

            return view('recruitment::job.show', compact('status', 'job', 'applications', 'job_attachments', 'notes', 'todos', 'activitys', 'staffs', 'creatorId'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Job $job)
    {
        if (Auth::user()->isAbleTo('job edit')) {
            $categories = JobCategory::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('title', 'id');

            $branches = Branch::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            $status = Job::$status;

            $job->applicant       = explode(',', $job->applicant);
            $job->visibility      = explode(',', $job->visibility);
            $job->custom_question = explode(',', $job->custom_question);

            $customQuestion = CustomQuestion::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();

            $users = User::where('created_by', '=', creatorId())->where('type', '=', 'client')->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
            if (count($users) != 0) {

                $users->prepend(__('Select Client'), '');
            }

            $job_type        = Job::$job_type;

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

            return view('recruitment::job.edit', compact('categories', 'status', 'branches', 'job', 'customQuestion', 'users', 'job_type', 'recruitment_type'));
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
    public function update(Request $request, Job $job)
    {
        if (Auth::user()->isAbleTo('job edit')) {

            $rules = [
                'title' => 'required',
                'recruitment_type' => 'required',
                'location' => 'required',
                'category' => 'required',
                'job_type' => 'required',
                'salary_from' => 'required',
                'salary_to' => 'required',
                'skill' => 'required',
                'position' => 'required|min:0',
                'start_date' => 'required|after:yesterday',
                'end_date' => 'required|after_or_equal:start_date',
                'description' => 'required',
                'requirement' => 'required',
                'custom_question.*' => 'required',
            ];

            if (module_is_active('Hrm') && $request->has('branch') && $request->branch != null) {
                $rules['branch'] = 'required';
            }

            if ($request->has('link_type') && $request->link_type == 'Custom Link') {
                $rules['job_link'] = 'required';
            }

            if (is_array($request->visibility) && in_array('terms', $request->visibility)) {
                $rules['terms_and_conditions'] = 'required';
            }

            $validator = \Validator::make(
                $request->all(),
                $rules
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $job->title                = $request->title;
            $job->recruitment_type     = $request->recruitment_type;
            $job->branch               = !empty($request->branch) ? $request->branch : 0;
            $job->location             = !empty($request->location) ? $request->location : '';
            $job->category             = $request->category;
            $job->user_id              = $request->user_id;
            $job->skill                = $request->skill;
            $job->position             = $request->position;
            $job->status               = $request->status;
            $job->job_type             = $request->job_type;
            $job->salary_from          = $request->salary_from;
            $job->salary_to            = $request->salary_to;
            $job->start_date           = $request->start_date;
            $job->end_date             = $request->end_date;
            $job->description          = $request->description;
            $job->requirement          = $request->requirement;
            $job->address              = $request->address;
            $job->link_type            = $request->link_type;
            $job->job_link             = !empty($request->job_link) ? $request->job_link : null;
            $job->terms_and_conditions = !empty($request->terms_and_conditions) ? $request->terms_and_conditions : '';
            $job->applicant            = !empty($request->applicant) ? implode(',', $request->applicant) : '';
            $job->visibility           = !empty($request->visibility) ? implode(',', $request->visibility) : '';
            $job->custom_question      = !empty($request->custom_question) ? implode(',', $request->custom_question) : '';
            $job->save();
            event(new UpdateJob($request, $job));
            return redirect()->route('job.index')->with('success', __('The job details are updated successfully.'));
        } else {
            return redirect()->route('job.index')->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Job $job)
    {
        if (Auth::user()->isAbleTo('job delete')) {
            $application = JobApplication::where('job', $job->id)->get()->pluck('id');
            event(new DestroyJob($job));
            JobOnBoard::whereIn('application', $application)->delete();
            InterviewSchedule::whereIn('candidate', $application)->delete();
            JobApplicationNote::whereIn('application_id', $application)->delete();
            JobApplication::where('job', $job->id)->delete();

            $job->delete();

            return redirect()->route('job.index')->with('success', __('The job has been deleted.'));
        } else {
            return redirect()->route('job.index')->with('error', __('Permission denied.'));
        }
    }

    public function career($slug = null, $lang = null)
    {
        if (!empty($slug)) {
            try {
                $workspace = WorkSpace::where('slug', $slug)->first();
                $workspace_id = $workspace->id;
            } catch (\Throwable $th) {
                return redirect()->back();
            }
        } else {
            try {
                $workspace = getActiveWorkSpace();
                $workspace_id = $workspace;
                $workspace = WorkSpace::where('id', $workspace)->first();
                $slug = $workspace->slug;
            } catch (\Throwable $th) {
                return redirect()->back();
            }
        }
        $company_id = $workspace->created_by;

        try {
            $slug = $slug;
        } catch (\Throwable $th) {
            return redirect('login');
        }
        if ($lang == null) {
            $lang = 'en';
        }

        $jobs = Job::where('created_by', $company_id)->where('status', 'active')->where('is_post', 1)->where('workspace', $workspace_id)->with('branches')->get();
        \Session::put('lang', $lang);

        \App::setLocale($lang);

        $languages                          = languages();

        $currantLang = \Session::get('lang');
        if (empty($currantLang)) {
            $user        = User::find($company_id);
            $currantLang = !empty($user) && !empty($user->lang) ? $user->lang : 'en';
        }

        return view('recruitment::job.career', compact('jobs', 'languages', 'currantLang', 'company_id', 'workspace_id','workspace','slug'));
    }

    public function jobRequirement($code, $lang)
    {
        $job = Job::where('code', $code)->first();
        if ($job) {
            if ($job->status == 'in_active') {
                return redirect()->back()->with('error', __('This Job is not Active.'));
            }

            \Session::put('lang', $lang);

            \App::setLocale($lang);


            $languages = languages();

            $currantLang = \Session::get('lang');
            if (empty($currantLang)) {
                $currantLang = !empty($job->createdBy) ? $job->createdBy->lang : 'en';
            }

            $company_id = $job->created_by;
            $workspace_id = $job->workspace;
            $workspace = WorkSpace::where('id', $workspace_id)->first();
            $slug = $workspace->slug;
            return view('recruitment::job.requirement', compact('job', 'languages', 'currantLang', 'company_id', 'workspace_id', 'slug'));
        } else {
            return redirect()->back()->with('error', __('This Job is not Found.'));
        }
    }

    public function jobApply($code, $lang)
    {
        \Session::put('lang', $lang);

        \App::setLocale($lang);

        $job  = Job::where('code', $code)->first();

        $que = !empty($job->custom_question) ? explode(",", $job->custom_question) : [];

        $questions = CustomQuestion::wherein('id', $que)->get();

        $languages = languages();

        $currantLang = \Session::get('lang');
        if (empty($currantLang)) {
            $currantLang = !empty($job->createdBy) ? $job->createdBy->lang : 'en';
        }

        $company_id = $job->created_by;
        $workspace_id = $job->workspace;
        $workspace = WorkSpace::where('id', $workspace_id)->first();
        $slug = $workspace->slug;
        return view('recruitment::job.apply', compact('job', 'questions', 'languages', 'currantLang', 'company_id', 'workspace_id', 'slug'));
    }

    public function TermsAndCondition($code, $lang)
    {
        $job = Job::where('code', $code)->first();
        if ($job) {
            if ($job->status == 'in_active') {
                return redirect()->back()->with('error', __('This Job is not Active.'));
            }

            \Session::put('lang', $lang);

            \App::setLocale($lang);

            $languages = languages();

            $currantLang = \Session::get('lang');
            if (empty($currantLang)) {
                $currantLang = !empty($job->createdBy) ? $job->createdBy->lang : 'en';
            }

            $company_id = $job->created_by;
            $workspace_id = $job->workspace;
            return view('recruitment::job.terms', compact('job', 'languages', 'currantLang', 'company_id', 'workspace_id'));
        } else {
            return redirect()->back()->with('error', __('This Job is not Found.'));
        }
    }

    function generateUniqueRandomString($table, $column, $length = 8)
    {
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        do {
            $randomNumber = random_int($min, $max);
        } while (DB::table($table)->where($column, $randomNumber)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->exists());

        return $randomNumber;
    }

    public function jobApplyData(Request $request, $code)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
        ];
        if (isset($request->terms_condition_check) && empty($request->terms_condition_check)) {
            $rules['terms_condition_check'] = [
                'required',
            ];
        }

        $validator = \Validator::make(
            $request->all(),
            $rules
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $job = Job::where('code', $code)->first();

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
        $job_user = User::find($job->created_by);
        $stage = JobStage::where('created_by', $job_user->id)->where('order', \DB::raw("(select min(`order`) from job_stages)"))->first();
        $uniqueCode = $this->generateUniqueRandomString('job_applications', 'unique_id', 8);

        $jobApplication                  = new JobApplication();
        $jobApplication->job             = $job->id;
        $jobApplication->unique_id       = $uniqueCode;
        $jobApplication->name            = $request->name;
        $jobApplication->email           = $request->email;
        $jobApplication->phone           = $request->phone;
        $jobApplication->profile         = !empty($request->profile) ? $url : '';
        $jobApplication->resume          = !empty($request->resume) ? $url1 : '';
        $jobApplication->cover_letter    = $request->cover_letter;
        $jobApplication->dob             = $request->dob;
        $jobApplication->gender          = $request->gender;
        $jobApplication->country         = $request->country;
        $jobApplication->state           = $request->state;
        $jobApplication->city            = $request->city;
        $jobApplication->stage           = !empty($stage) ? $stage->id : 1;
        $jobApplication->custom_question = json_encode($request->question);
        $jobApplication->workspace      = getActiveWorkSpace($job->created_by);
        $jobApplication->created_by      = $job->created_by;
        $jobApplication->save();

        event(new CreateJobApplication($request, $jobApplication));

        return redirect()->back()->with('create_application', __('Job application successfully submitted. your tracking id is: ') . ('<span class="text-dark"><b>' . $jobApplication->unique_id . '</b></span>'));
    }

    public function grid()
    {
        if (Auth::user()->isAbleTo('job manage')) {
            $jobs = Job::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->with('branches');
            $jobs = $jobs->paginate(11);
            $data['total']     = Job::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->count();
            $data['active']    = Job::where('status', 'active')->where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->count();
            $data['in_active'] = Job::where('status', 'in_active')->where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->count();
            return view('recruitment::job.grid', compact('jobs', 'data'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function jobAttechment(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('job attachment upload')) {
            $job = Job::find($id);
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
                        'related_id'   => $job->id,
                        'file_name'    => $file_name,
                        'file_path'    => $upload['url'],
                        'file_size'    => $fileSizeFormatted,
                        'type'         => 'Jobs',
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

    public function jobAttechmentDestroy($id)
    {
        if (Auth::user()->isAbleTo('job attachment delete')) {
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

    public function noteCreate($id)
    {
        if (Auth::user()->isAbleTo('job note create')) {
            $job = Job::find($id);

            return view('recruitment::job_note.notecreate', compact('job'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function noteStore(Request $request)
    {
        if (Auth::user()->isAbleTo('job note create')) {
            $job = Job::find($request->job_id);

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

            $job_note              = new JobNotes();
            $job_note->job_id      = $job->id;
            $job_note->description = $request->description;
            $job_note->workspace   = getActiveWorkSpace();
            $job_note->created_by  = creatorId();
            $job_note->save();

            return redirect()->back()->with('success', __('The note has been created successfully.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function noteEdit(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('job note edit')) {
            $job_note = JobNotes::find($id);

            return view('recruitment::job_note.noteedit', compact('job_note'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function noteUpdate(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('job note edit')) {
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

            $job_note = JobNotes::find($id);
            $job_note->description = $request->description;
            $job_note->workspace   = getActiveWorkSpace();
            $job_note->created_by  = creatorId();
            $job_note->save();

            return redirect()->back()->with('success', __('The note details are updated successfully.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function noteDestroy($id)
    {
        if (Auth::user()->isAbleTo('job note delete')) {
            $jobnotes = JobNotes::find($id);

            $jobnotes->delete();

            return redirect()->back()->with('success', __('The note has been deleted.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function noteDescription($id)
    {
        if (Auth::user()->isAbleTo('job note show')) {
            $job_note = JobNotes::find($id);
            return view('recruitment::job_note.noteshow', compact('job_note'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function todoCreate($id)
    {
        if (Auth::user()->isAbleTo('job todo create')) {
            $job = Job::find($id);
            $users = User::where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->pluck('name', 'id');

            return view('recruitment::job_todo.create', compact('job', 'users'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function todoStore(Request $request)
    {
        if (Auth::user()->isAbleTo('job todo create')) {
            $job = Job::find($request->job_id);

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

            $job_todo               = new JobTodos();
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
            $job_todo->sub_module   = 'jobs';
            $job_todo->workspace_id = getActiveWorkSpace();
            $job_todo->created_by   = creatorId();
            $job_todo->save();

            return redirect()->back()->with('success', __('The ToDo has been created successfully.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function todoEdit(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('job todo edit')) {
            $job_todo = JobTodos::find($id);
            $users = User::where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->pluck('name', 'id');

            return view('recruitment::job_todo.edit', compact('job_todo', 'users'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function todoUpdate(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('job todo edit')) {
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

            $job_todo = JobTodos::find($id);
            $job_todo->title       = $request->title;
            $job_todo->description = $request->description;
            $job_todo->status       = 1;
            $job_todo->priority     = $request->priority;
            $job_todo->start_date   = $request->start_date;
            $job_todo->due_date    = $request->due_date;
            $job_todo->assign_by   = Auth::user()->id;
            $job_todo->assigned_to = implode(',', $request->assigned_to);
            $job_todo->module       = 'recruitment';
            $job_todo->sub_module   = 'jobs';
            $job_todo->workspace_id = getActiveWorkSpace();
            $job_todo->created_by  = creatorId();
            $job_todo->save();

            return redirect()->back()->with('success', __('The ToDo details are updated successfully.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function todoDestroy($id)
    {
        if (Auth::user()->isAbleTo('job todo delete')) {
            $job_todo = JobTodos::find($id);

            $job_todo->delete();

            return redirect()->back()->with('success', __('The ToDo has been deleted.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function todoShow($id)
    {
        if (Auth::user()->isAbleTo('job todo show')) {
            $job_todo = JobTodos::findOrFail($id);
            return view('recruitment::job_todo.show', compact('job_todo'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function jobPost(Request $request, $id)
    {
        try {
            $job          = Job::find($id);
            $job->is_post = 1;
            $job->save();

            return redirect()->back()->with('success', __('Job post successfully.'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function activitylogDestroy($id)
    {
        if (Auth::user()->isAbleTo('job activity delete')) {
            $job_todo = AllActivityLog::find($id);

            $job_todo->delete();

            return redirect()->back()->with('success', __('The ActivityLog has been deleted.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function findJob($slug = null)
    {
        if (!empty($slug)) {
            try {
                $workspace = WorkSpace::where('slug', $slug)->first();
                $workspace_id = $workspace->id;
            } catch (\Throwable $th) {
                return redirect()->back();
            }
        } else {
            try {
                $workspace = getActiveWorkSpace();
                $workspace_id = $workspace;
                $workspace = WorkSpace::where('id', $workspace)->first();
                $slug = $workspace->slug;
            } catch (\Throwable $th) {
                return redirect()->back();
            }
        }
        $company_id = $workspace->created_by;

        if ($workspace) {
            return view('recruitment::track_job.find_job', compact('company_id', 'workspace_id', 'workspace', 'slug'));
        } else {
            abort(404);
        }
    }

    public function trackJob(Request $request, $slug)
    {
        $workspace = Workspace::where('slug', $slug)->first();
        $job_appplication = JobApplication::where('unique_id', $request->unique_id)->where('email', $request->email)->where('created_by', $workspace->created_by)->where('workspace', $workspace->id)->first();

        if (isset($job_appplication) && $job_appplication != null) {
            $jobs = Job::where('id', $job_appplication->job)->where('created_by', $workspace->created_by)->where('workspace', $workspace->id)->get();
            $stage = JobStage::where('id', $job_appplication->stage)->where('created_by', $workspace->created_by)->where('workspace', $workspace->id)->first();
            $interview      = InterviewSchedule::where('candidate', $job_appplication->id)->latest()->first();
            if (!empty($slug)) {
                try {
                    $workspace = WorkSpace::where('slug', $slug)->first();
                    $workspace_id = $workspace->id;
                } catch (\Throwable $th) {
                    return redirect()->back();
                }
            } else {
                try {
                    $workspace = getActiveWorkSpace();
                    $workspace_id = $workspace;
                    $workspace = WorkSpace::where('id', $workspace)->first();
                    $slug = $workspace->slug;
                } catch (\Throwable $th) {
                    return redirect()->back();
                }
            }
            $company_id = $workspace->created_by;

            if ($job_appplication !== 'null') {
                return view('recruitment::track_job.track_job', compact('slug', 'company_id', 'workspace_id', 'workspace', 'job_appplication', 'jobs', 'stage', 'interview'));
            } else {
                return redirect()->back()->with('error', __('Something Went Wrong !!!'));
            }
        } else {
            return redirect()->back()->with('error-alert', __('These credentials do not match our records.'));
        }
    }
}
