<?php

namespace Workdo\Recruitment\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Workdo\ActivityLog\Entities\AllActivityLog;
use Workdo\FileSharing\Entities\FileShare;
use Workdo\Recruitment\DataTables\JobCandidateDataTable;
use Workdo\Recruitment\Entities\JobAward;
use Workdo\Recruitment\Entities\JobCandidate;
use Workdo\Recruitment\Entities\JobCandidateCategory;
use Workdo\Recruitment\Entities\JobCandidateNotes;
use Workdo\Recruitment\Entities\JobCandidateReferral;
use Workdo\Recruitment\Entities\JobCandidateTodos;
use Workdo\Recruitment\Entities\JobExperience;
use Workdo\Recruitment\Entities\JobExperienceCandidate;
use Workdo\Recruitment\Entities\JobProject;
use Workdo\Recruitment\Entities\JobQualification;
use Workdo\Recruitment\Entities\JobSkill;
use Workdo\Recruitment\Events\CreateJobCandidate;
use Workdo\Recruitment\Events\DestroyJobCandidate;
use Workdo\Recruitment\Events\UpdateJobCandidate;

class JobCandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function index(JobCandidateDataTable $dataTable, Request $request)
    {
        if (Auth::user()->isAbleTo('job candidate manage')) {

            
            $candidate_category = JobCandidateCategory::distinct()->pluck('name', 'id');
            $candidate_category->prepend('All', '');
            
            $job_candidate_country = JobCandidate::distinct()->pluck('country', 'country');
            $job_candidate_country->prepend('All', '');

            $job_candidate_state = JobCandidate::distinct()->pluck('state', 'state');
            $job_candidate_state->prepend('All', '');
            
            $filter = [
                'gender' => isset($request->gender) ? $request->gender : '',
                'candidate_category' => isset($request->candidate_category) ? $request->candidate_category : '',
                'country' => isset($request->country) ? $request->country : '',
                'state' => isset($request->state) ? $request->state : '',
            ];
            
            return $dataTable->render('recruitment::jobcandidate.index', compact('filter', 'candidate_category', 'job_candidate_country', 'job_candidate_state'));
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
        if (Auth::user()->isAbleTo('job candidate create')) {
            $candidate_category = JobCandidateCategory::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $creatorId = creatorId();
            $getActiveWorkSpace = getActiveWorkSpace();
            $staffs = User::where('created_by', '=', $creatorId)->where('workspace_id', '=', $getActiveWorkSpace)->orWhere('id', $creatorId)->get();
            return view('recruitment::jobcandidate.create', compact('candidate_category', 'staffs', 'creatorId'));
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
        if (Auth::user()->isAbleTo('job candidate create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required',
                    'dob' => 'before:' . date('Y-m-d'),
                    'phone' => 'required',
                    'gender' => 'required',
                    'address' => 'required',
                    'country' => 'required',
                    'state' => 'required',
                    'city' => 'required',
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

                $filenameWithExt = $request->file('resume')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('resume')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $uplaod = upload_file($request, 'resume', $fileNameToStore, 'JobApplication');
                if ($uplaod['flag'] == 1) {
                    $url1 = $uplaod['url'];
                } else {
                    return redirect()->back()->with('error', $uplaod['msg']);
                }
            }

            $job_candidate                     = new JobCandidate();
            $job_candidate->candidate_category = $request->candidate_category;
            $job_candidate->name               = $request->name;
            $job_candidate->email              = $request->email;
            $job_candidate->phone              = $request->phone;
            $job_candidate->dob                = $request->dob;
            $job_candidate->gender             = $request->gender;
            $job_candidate->address            = $request->address;
            $job_candidate->country            = $request->country;
            $job_candidate->state              = $request->state;
            $job_candidate->city               = $request->city;
            $job_candidate->description        = $request->description;
            $job_candidate->profile            = !empty($request->profile) ? $url : '';
            $job_candidate->resume             = !empty($request->resume) ? $url1 : '';
            $job_candidate->workspace          = getActiveWorkSpace();
            $job_candidate->created_by         = creatorId();
            $job_candidate->save();

            event(new CreateJobCandidate($request,  $job_candidate));

            return redirect()->route('job-candidates.edit', Crypt::encrypt($job_candidate->id))->with('success', __('The job candidate has been created successfully'))->with('experience-tab', true);
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
        return view('recruitment::jobcandidate.show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Page Not Found.'));
        }
        if (Auth::user()->isAbleTo('job candidate edit')) {
            $job_candidates = JobCandidate::find($id);
            $job_projects = JobProject::where('candidate_id', $job_candidates->id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $job_qualifications = JobQualification::where('candidate_id', $job_candidates->id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $job_awards = JobAward::where('candidate_id', $job_candidates->id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $job_experience_candidates = JobExperienceCandidate::where('candidate_id', $job_candidates->id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $job_skills = JobSkill::where('candidate_id', $job_candidates->id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $job_experiences = JobExperience::where('candidate_id', $job_candidates->id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();

            $candidate_category = JobCandidateCategory::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            $jobcandidate_referrals = JobCandidateReferral::where('candidate_id', $job_candidates->id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();

            $jobCandidate_attachments = [];
            if (module_is_active('FileSharing')) {
                $jobCandidate_attachments = FileShare::where('related_id', $job_candidates->id)->where('type', 'Job Candidate')->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            }

            $jobCandidate_notes = JobCandidateNotes::where('jobcandidate_id', $job_candidates->id)->where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();

            $jobcandidate_todos = JobCandidateTodos::where('related_id', $job_candidates->id)->where('sub_module', 'job candidate')->where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->get();

            $creatorId = creatorId();
            $getActiveWorkSpace = getActiveWorkSpace();
            $activitys = [];
            if (module_is_active('ActivityLog')) {
                $activitys = AllActivityLog::join('users', 'all_activity_logs.user_id', '=', 'users.id')
                    ->select('all_activity_logs.*', 'users.name', 'users.type')
                    ->where('all_activity_logs.created_by', '=', $creatorId)
                    ->where('all_activity_logs.workspace', '=', $getActiveWorkSpace)
                    ->where('all_activity_logs.sub_module', '=', 'Job Candidate')
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

            return view('recruitment::jobcandidate.edit', compact('job_projects', 'job_qualifications', 'job_awards', 'job_experience_candidates', 'job_skills', 'job_experiences', 'job_candidates', 'candidate_category', 'jobcandidate_referrals', 'jobCandidate_attachments', 'jobCandidate_notes', 'jobcandidate_todos', 'activitys', 'staffs', 'creatorId'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
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
        if (Auth::user()->isAbleTo('job candidate edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required',
                    'phone' => 'required',
                    'dob' => 'before:' . date('Y-m-d'),
                    'gender' => 'required',
                    'address' => 'required',
                    'country' => 'required',
                    'state' => 'required',
                    'city' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $job_candidate = JobCandidate::find($id);

            if (!empty($request->profile)) {
                $filenameWithExt = $request->file('profile')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('profile')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $uplaod = upload_file($request, 'profile', $fileNameToStore, 'JobApplication');
                if ($uplaod['flag'] == 1) {
                    if (!empty($job_candidate->profile)) {
                        delete_file($job_candidate->profile);
                    }
                    $url = $uplaod['url'];
                    $job_candidate->profile = $url;
                } else {
                    return redirect()->back()->with('error', $uplaod['msg']);
                }
            }

            if (!empty($request->resume)) {
                $filenameWithExt = $request->file('resume')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('resume')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $uplaod = upload_file($request, 'resume', $fileNameToStore, 'JobApplication');
                if ($uplaod['flag'] == 1) {
                    if (!empty($job_candidate->resume)) {
                        delete_file($job_candidate->resume);
                    }
                    $url1 = $uplaod['url'];
                    $job_candidate->resume = $url1;
                } else {
                    return redirect()->back()->with('error', $uplaod['msg']);
                }
            }

            $job_candidate->candidate_category = $request->candidate_category;
            $job_candidate->name        = $request->name;
            $job_candidate->email       = $request->email;
            $job_candidate->phone       = $request->phone;
            $job_candidate->dob         = $request->dob;
            $job_candidate->gender      = $request->gender;
            $job_candidate->address     = $request->address;
            $job_candidate->country     = $request->country;
            $job_candidate->state       = $request->state;
            $job_candidate->city        = $request->city;
            $job_candidate->description = $request->description;
            $job_candidate->workspace   = getActiveWorkSpace();
            $job_candidate->created_by  = creatorId();
            $job_candidate->save();

            event(new UpdateJobCandidate($request, $job_candidate));

            return redirect()->back()->with('success', __('The job candidate details are updated successfully.'));
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
        if (Auth::user()->isAbleTo('job candidate delete')) {
            $currentWorkspace = getActiveWorkSpace();
            $job_candidate = JobCandidate::find($id);

            if ($job_candidate->created_by == creatorId() && $job_candidate->workspace == $currentWorkspace) {

                event(new DestroyJobCandidate($job_candidate));

                if (!empty($job_candidate->profile)) {
                    delete_file($job_candidate->profile);
                }

                if (!empty($job_candidate->resume)) {
                    delete_file($job_candidate->resume);
                }

                $job_candidate->delete();
                return redirect()->back()->with('success', __('The job candidate has been deleted.'));
            } else {
                return redirect()->back()->with('error', 'Permission denied.');
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function previewJob($template, $color)
    {
        $objUser = \Auth::user();
        $job_candidates = new JobCandidate();
        $job_experiences = [];
        $job_projects = [];
        $job_qualifications = [];
        $job_awards = [];
        $job_experience_candidates = [];
        $job_skills = [];

        $job_candidate = new \stdClass();
        $job_candidate->name = '<Name>';
        $job_candidate->dob = '<Date Of Birth>';
        $job_candidate->country = '<National>';
        $job_candidate->address = '<Address>';
        $job_candidate->country = '<Country>';
        $job_candidate->state = '<State>';
        $job_candidate->city = '<City>';
        $job_candidate->phone = '<Phone>';
        $job_candidate->website = '<Website>';
        $job_candidate->summary = 'Lorem ipsum dolor sit amet. Consectetur adipiscing elit. Vivamus vulputat libero sto elit dolor vivamus adipiscing elit vivamus vulputat libero justo elit dolor ipsums dolor sit amet consectetur ipsum dolor amet. Consectetur adipiscing elit. amet. Consetetur adipiscing elit. Lorem ipsum dolor sit amet. Consectetur adipiscing elit. Vivamus vulputat libero sto elit dolor vivamus adipiscing elit vivamus vulputat libero justo elit dolor dolor amet. Lorem ipsum dolor sit amet. Consectetur adipiscing elit.';
        $job_candidate->work_experience_title = 'SENIOR GRAPHIC DESIGNER';
        $job_candidate->work_experience_date = 'June 2020- March 2024';
        $job_candidate->work_experience_description = '<p><span style="font-family: Raleway, sans-serif;">Lorem ipsum dolor sit amet. Consectetur adipiscing elit. Vivamus vulputate libero justo elit dolor Vivamus adipiscng elit vivamus vulputat libero justo. Elit dolor ipsum dolor sit amet. Consectetur ipsum dolor amet.</span></p><ul><li style="margin: 0px 0px 0px 15px; padding: 0px; list-style: disc; line-height: 1.5;">Lorem ipsum dolor sit amet. Consectetur adipiscing elit. Vivamus vulputat</li><li style="margin: 0px 0px 0px 15px; padding: 0px; list-style: disc; line-height: 1.5;">libero justo elit dolor vivamus adipiscing elit vivamus vulputat libero justo elit</li><li style="margin: 0px 0px 0px 15px; padding: 0px; list-style: disc; line-height: 1.5;">Lorem ipsum dolor sit amet. Consectetur adipiscing elit. Vivamus vulputat</li></ul>';
        $job_candidate->project_experience_title = 'SENIOR GRAPHIC DESIGNER';
        $job_candidate->project_experience_date = 'June 2020- March 2024';
        $job_candidate->project_experience_description = '<p><span style="font-family: Raleway, sans-serif;">Lorem ipsum dolor sit amet. Consectetur adipiscing elit. Vivamus vulputate libero justo elit dolor Vivamus adipiscng elit vivamus vulputat libero justo. Elit dolor ipsum dolor sit amet. Consectetur ipsum dolor amet.</span></p><ul><li style="margin: 0px 0px 0px 15px; padding: 0px; list-style: disc; line-height: 1.5;">Lorem ipsum dolor sit amet. Consectetur adipiscing elit. Vivamus vulputat</li><li style="margin: 0px 0px 0px 15px; padding: 0px; list-style: disc; line-height: 1.5;">libero justo elit dolor vivamus adipiscing elit vivamus vulputat libero justo elit</li><li style="margin: 0px 0px 0px 15px; padding: 0px; list-style: disc; line-height: 1.5;">Lorem ipsum dolor sit amet. Consectetur adipiscing elit. Vivamus vulputat</li></ul>';
        $job_candidate->qualification_experience_title = 'SENIOR GRAPHIC DESIGNER';
        $job_candidate->qualification_experience_date = 'June 2020- March 2024';
        $job_candidate->qualification_experience_description = '<p><span style="font-family: Raleway, sans-serif;">Lorem ipsum dolor sit amet. Consectetur adipiscing elit. Vivamus vulputate libero justo elit dolor Vivamus adipiscng elit vivamus vulputat libero justo. Elit dolor ipsum dolor sit amet. Consectetur ipsum dolor amet.</span></p><ul><li style="margin: 0px 0px 0px 15px; padding: 0px; list-style: disc; line-height: 1.5;">Lorem ipsum dolor sit amet. Consectetur adipiscing elit. Vivamus vulputat</li><li style="margin: 0px 0px 0px 15px; padding: 0px; list-style: disc; line-height: 1.5;">libero justo elit dolor vivamus adipiscing elit vivamus vulputat libero justo elit</li><li style="margin: 0px 0px 0px 15px; padding: 0px; list-style: disc; line-height: 1.5;">Lorem ipsum dolor sit amet. Consectetur adipiscing elit. Vivamus vulputat</li></ul>';
        $job_candidate->award_description = '<ul><li style="margin: 0px 0px 0px 15px; padding: 0px; list-style: disc; line-height: 1.5;">Lorem ipsum dolor sit amet. Consectetur adipiscing elit. Vivamus vulputat</li><li style="margin: 0px 0px 0px 15px; padding: 0px; list-style: disc; line-height: 1.5;">libero justo elit dolor vivamus adipiscing elit vivamus vulputat libero justo elit</li><li style="margin: 0px 0px 0px 15px; padding: 0px; list-style: disc; line-height: 1.5;">Lorem ipsum dolor sit amet. Consectetur adipiscing elit. Vivamus vulputat</li></ul><ul><ul style="margin-right: 0px; margin-bottom: 15px; margin-left: 0px; padding: 0px; list-style: none; line-height: 1.5; font-family: Raleway, sans-serif;"><li style="margin: 0px 0px 0px 15px; padding: 0px; list-style: disc; line-height: 1.5;">Lorem ipsum dolor sit amet. Consectetur adipiscing elit. Vivamus vulputat</li><li style="margin: 0px 0px 0px 15px; padding: 0px; list-style: disc; line-height: 1.5;">libero justo elit dolor vivamus adipiscing elit vivamus vulputat libero justo elit</li><li style="margin: 0px 0px 0px 15px; padding: 0px; list-style: disc; line-height: 1.5;">Lorem ipsum dolor sit amet. Consectetur adipiscing elit. Vivamus vulputat</li></ul></ul>';
        $job_candidate->jobs_description = '<p><span style="font-family: Raleway, sans-serif;">Lorem ipsum dolor sit amet. Consectetur adipiscing elit. Vivamus vulputate libero justo elit</span><br></p>';
        $job_candidate->skill_description = '<p><span style="font-family: Raleway, sans-serif;">Lorem ipsum dolor sit amet. Consectetur adipiscing elit. Vivamus vulputate libero justo elit dolor Vivamus adipiscng elit vivamus vulputat libero justo. Elit dolor ipsum dolor sit amet. Consectetur ipsum dolor amet.</span></p><p><span style="font-family: Raleway, sans-serif;">Lorem ipsum dolor sit amet. Consectetur adipiscing elit. Vivamus vulputate libero justo elit dolor Vivamus adipiscng elit vivamus vulputat libero justo. Elit dolor ipsum dolor sit amet. Consectetur ipsum dolor amet.</span><span style="font-family: Raleway, sans-serif;"><br></span></p>';
        $job_candidate->company_name = 'WorkDo Infotech';

        $job_candidate->JobExperience = [
            [
                'start_date' => '01-03-2023',
                'end_date' => '01-03-2024',
                'title' => '<Title>',
                'organization' => '<Organization>',
                'description' => '<Description>'
            ],
        ];
        $job_candidate->JobProject = [
            [
                'start_date' => '01-03-2023',
                'end_date' => '01-03-2024',
                'title' => '<Title>',
                'organization' => '<Organization>',
                'description' => '<Description>',
            ]
        ];
        $job_candidate->JobQualification = [
            [
                'start_date' => '01-03-2023',
                'end_date' => '01-03-2024',
                'title' => '<Title>',
                'organization' => '<Organization>',
                'description' => '<Description>',
            ]
        ];
        $job_candidate->JobAward = [
            [
                'description' => '<Description>',
            ]
        ];
        $job_candidate->JobExperienceCandidate = [
            [
                'start_date' => '01-03-2023',
                'end_date' => '01-03-2024',
                'description' => '<Description>',
            ]
        ];
        $job_candidate->JobSkill = [
            [
                'description' => '<Description>'
            ]
        ];

        $preview = 1;
        $color = '#' . $color;

        $font_color = JobCandidate::getFontColor($color);

        $default_logo = get_file('uploads/users-avatar/avatar.png');
        $company_settings = getCompanyAllSetting();

        $job_logo = isset($company_settings['job_logo']) ? $company_settings['job_logo'] : '';

        if (isset($job_logo) && !empty($job_logo)) {
            $img = get_file($job_logo);
        } else {
            $img = $default_logo;
        }

        $company_id = $job_candidates->created_by;
        $workspace = $job_candidates->workspace;
        $settings['company_name'] = isset($company_settings['company_name']) ? $company_settings['company_name'] : '';
        $settings['site_rtl'] = isset($company_settings['site_rtl']) ? $company_settings['site_rtl'] : '';
        $settings['company_email'] = isset($company_settings['company_email']) ? $company_settings['company_email'] : '';
        $settings['company_telephone'] = isset($company_settings['company_telephone']) ? $company_settings['company_telephone'] : '';
        $settings['company_address'] = isset($company_settings['company_address']) ? $company_settings['company_address'] : '';
        $settings['company_city'] = isset($company_settings['company_city']) ? $company_settings['company_city'] : '';
        $settings['company_state'] = isset($company_settings['company_state']) ? $company_settings['company_state'] : '';
        $settings['company_zipcode'] = isset($company_settings['company_zipcode']) ? $company_settings['company_zipcode'] : '';
        $settings['company_country'] = isset($company_settings['company_country']) ? $company_settings['company_country'] : '';
        $settings['registration_number'] = isset($company_settings['registration_number']) ? $company_settings['registration_number'] : '';
        $settings['tax_type'] = isset($company_settings['tax_type']) ? $company_settings['tax_type'] : '';
        $settings['vat_number'] = isset($company_settings['vat_number']) ? $company_settings['vat_number'] : '';
        return view('recruitment::templates.' . $template, compact('preview', 'color', 'settings', 'img', 'font_color', 'job_candidate', 'company_id', 'workspace', 'job_experiences', 'job_projects', 'job_qualifications', 'job_awards', 'job_experience_candidates', 'job_skills'));
    }

    public function saveJobTemplateSettings(Request $request)
    {
        $user = \Auth::user();
        $validator = \Validator::make(
            $request->all(),
            [
                'job_template' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }
        if ($request->job_logo) {
            $request->validate(
                [
                    'job_logo' => 'image',
                ]
            );

            $job_logo = $user->id . '_job_logo_' . time() . '.png';
            $uplaod = upload_file($request, 'job_logo', $job_logo, 'job_logo');
            if ($uplaod['flag'] == 1) {
                $url = $uplaod['url'];
                $old_job_logo = company_setting('job_logo');
                if (!empty($old_job_logo) && check_file($old_job_logo)) {
                    delete_file($old_job_logo);
                }
            } else {
                return redirect()->back()->with('error', $uplaod['msg']);
            }
        }
        $post = $request->all();
        unset($post['_token']);

        if (isset($post['job_template']) && (!isset($post['job_color']) || empty($post['job_color']))) {
            $post['job_color'] = "ffffff";
        }
        if (isset($post['job_logo'])) {
            $post['job_logo'] = $url;
        }
        foreach ($post as $key => $value) {
            // Define the data to be updated or inserted
            $data = [
                'key' => $key,
                'workspace' => getActiveWorkSpace(),
                'created_by' => Auth::user()->id,
            ];
            // Check if the record exists, and update or insert accordingly
            Setting::updateOrInsert($data, ['value' => $value]);
        }
        // Settings Cache forget
        comapnySettingCacheForget();
        return redirect()->back()->with('success', 'Recruitment Print setting save sucessfully.');
    }

    public function DownloadResume($resume_id)
    {
        try {
            $resumeId = Crypt::decrypt($resume_id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Resume Not Found.'));
        }

        $job_candidate = JobCandidate::with(
            [
                'JobExperience',
                'JobProject',
                'JobExperienceCandidate',
                'JobQualification',
                'JobSkill',
                'JobAward'
            ]
        )->where('id', $resumeId)->first();


        if ($job_candidate) {

            $company_settings = getCompanyAllSetting($job_candidate->created_by, $job_candidate->workspace);

            $color = '#' . (!empty($company_settings['job_color']) ? $company_settings['job_color'] : 'ffffff');

            $font_color = JobCandidate::getFontColor($color);

            $default_logo = get_file('uploads/users-avatar/avatar.png');

            $resume_profile = isset($job_candidate->profile) ? $job_candidate->profile : '';

            if (isset($resume_profile) && !empty($resume_profile)) {
                $img = get_file($resume_profile);
            } else {
                $img = $default_logo;
            }
            $company_id = $job_candidate->created_by;
            $workspace = $job_candidate->workspace;
            $settings['company_name'] = isset($company_settings['company_name']) ? $company_settings['company_name'] : '';
            $settings['site_rtl'] = isset($company_settings['site_rtl']) ? $company_settings['site_rtl'] : '';
            $settings['company_email'] = isset($company_settings['company_email']) ? $company_settings['company_email'] : '';
            $settings['company_telephone'] = isset($company_settings['company_telephone']) ? $company_settings['company_telephone'] : '';
            $settings['company_address'] = isset($company_settings['company_address']) ? $company_settings['company_address'] : '';
            $settings['company_city'] = isset($company_settings['company_city']) ? $company_settings['company_city'] : '';
            $settings['company_state'] = isset($company_settings['company_state']) ? $company_settings['company_state'] : '';
            $settings['company_zipcode'] = isset($company_settings['company_zipcode']) ? $company_settings['company_zipcode'] : '';
            $settings['company_country'] = isset($company_settings['company_country']) ? $company_settings['company_country'] : '';
            $settings['registration_number'] = isset($company_settings['registration_number']) ? $company_settings['registration_number'] : '';
            $settings['tax_type'] = isset($company_settings['tax_type']) ? $company_settings['tax_type'] : '';
            $settings['vat_number'] = isset($company_settings['vat_number']) ? $company_settings['vat_number'] : '';
            $settings['job_template'] = isset($company_settings['job_template']) ? $company_settings['job_template'] : '';
            return view('recruitment::templates.' . $settings['job_template'], compact('job_candidate', 'color', 'settings', 'img', 'font_color', 'company_id', 'workspace'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function jobCandidateAttechment(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('jobcandidate-attachment upload')) {
            $jobcandidate = JobCandidate::find($id);
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
                        'related_id'   => $jobcandidate->id,
                        'file_name'    => $file_name,
                        'file_path'    => $upload['url'],
                        'file_size'    => $fileSizeFormatted,
                        'type'         => 'Job Candidate',
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

    public function jobCandidateAttechmentDestroy($id)
    {
        if (Auth::user()->isAbleTo('jobcandidate-attachment delete')) {
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

    public function jobCandidateNoteCreate($id)
    {
        if (Auth::user()->isAbleTo('jobcandidate-note create')) {
            $job_candidate = JobCandidate::find($id);

            return view('recruitment::jobcandidate_note.notecreate', compact('job_candidate'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function jobCandidateNoteStore(Request $request)
    {
        if (Auth::user()->isAbleTo('jobcandidate-note create')) {
            $job_candidate = JobCandidate::find($request->jobcandidate_id);

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

            $job_note                  = new JobCandidateNotes();
            $job_note->jobcandidate_id = $job_candidate->id;
            $job_note->description     = $request->description;
            $job_note->workspace       = getActiveWorkSpace();
            $job_note->created_by      = creatorId();
            $job_note->save();

            return redirect()->back()->with('success', __('The note has been created successfully.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function jobCandidateNoteEdit(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('jobcandidate-note edit')) {
            $job_candidate = JobCandidateNotes::find($id);

            return view('recruitment::jobcandidate_note.noteedit', compact('job_candidate'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function jobCandidateNoteUpdate(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('jobcandidate-note edit')) {
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

            $job_note = JobCandidateNotes::find($id);
            $job_note->description = $request->description;
            $job_note->workspace   = getActiveWorkSpace();
            $job_note->created_by  = creatorId();
            $job_note->save();

            return redirect()->back()->with('success', __('The note details are updated successfully.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function jobCandidateNoteDestroy($id)
    {
        if (Auth::user()->isAbleTo('jobcandidate-note delete')) {
            $jobnotes = JobCandidateNotes::find($id);

            $jobnotes->delete();

            return redirect()->back()->with('success', __('The note has been deleted.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function jobCandidateNoteDescription($id)
    {
        if (Auth::user()->isAbleTo('jobcandidate-note show')) {
            $job_candidate = JobCandidateNotes::find($id);
            return view('recruitment::jobcandidate_note.noteshow', compact('job_candidate'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function jobCandidateTodoCreate($id)
    {
        if (Auth::user()->isAbleTo('jobcandidate-todo create')) {
            $job = JobCandidate::find($id);
            $users = User::where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->pluck('name', 'id');

            return view('recruitment::jobcandidate_todo.create', compact('job', 'users'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function jobCandidateTodoStore(Request $request)
    {
        if (Auth::user()->isAbleTo('jobcandidate-todo create')) {
            $job = JobCandidate::find($request->jobcandidate_id);

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

            $job_todo               = new JobCandidateTodos();
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
            $job_todo->sub_module   = 'job candidate';
            $job_todo->workspace_id = getActiveWorkSpace();
            $job_todo->created_by   = creatorId();
            $job_todo->save();

            return redirect()->back()->with('success', __('The ToDo has been created successfully.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function jobCandidateTodoEdit(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('jobcandidate-todo edit')) {
            $job_todo = JobCandidateTodos::find($id);
            $users = User::where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->pluck('name', 'id');

            return view('recruitment::jobcandidate_todo.edit', compact('job_todo', 'users'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function jobCandidateTodoUpdate(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('jobcandidate-todo edit')) {
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

            $job_todo = JobCandidateTodos::find($id);
            $job_todo->title       = $request->title;
            $job_todo->description = $request->description;
            $job_todo->status       = 1;
            $job_todo->priority     = $request->priority;
            $job_todo->start_date   = $request->start_date;
            $job_todo->due_date    = $request->due_date;
            $job_todo->assign_by   = Auth::user()->id;
            $job_todo->assigned_to = implode(',', $request->assigned_to);
            $job_todo->module       = 'recruitment';
            $job_todo->sub_module   = 'job candidate';
            $job_todo->workspace_id = getActiveWorkSpace();
            $job_todo->created_by  = creatorId();
            $job_todo->save();

            return redirect()->back()->with('success', __('The ToDo details are updated successfully.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function jobCandidateTodoDestroy($id)
    {
        if (Auth::user()->isAbleTo('jobcandidate-todo delete')) {
            $job_todo = JobCandidateTodos::find($id);

            $job_todo->delete();

            return redirect()->back()->with('success', __('The ToDo has been deleted.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function jobCandidateTodoShow($id)
    {
        if (Auth::user()->isAbleTo('jobcandidate-todo show')) {
            $job_todo = JobCandidateTodos::findOrFail($id);
            return view('recruitment::jobcandidate_todo.show', compact('job_todo'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function jobCandidateActivitylogDestroy($id)
    {
        if (Auth::user()->isAbleTo('jobcandidate-activity delete')) {
            $job_todo = AllActivityLog::find($id);

            $job_todo->delete();

            return redirect()->back()->with('success', __('The Activitylog has been deleted.'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
}
