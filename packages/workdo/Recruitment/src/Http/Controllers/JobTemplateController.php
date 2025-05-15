<?php

namespace Workdo\Recruitment\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\Hrm\Entities\Branch;
use Workdo\Recruitment\DataTables\JobTemplateDataTable;
use Workdo\Recruitment\Entities\CustomQuestion;
use Workdo\Recruitment\Entities\Job;
use Workdo\Recruitment\Entities\JobCategory;
use Workdo\Recruitment\Entities\JobTemplate;

class JobTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(JobTemplateDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('job template manage')) {

            return $dataTable->render('recruitment::job-template.index');
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
        return redirect()->back();
        return view('recruitment::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */

    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('job template manage')) {
            $job = Job::where('id', $request->job_id)->first();

            if ($job) {
                $jobData = $job->only([
                    'id',
                    'title',
                    'recruitment_type',
                    'branch',
                    'location',
                    'category',
                    'user_id',
                    'skill',
                    'position',
                    'status',
                    'job_type',
                    'salary_from',
                    'salary_to',
                    'start_date',
                    'end_date',
                    'description',
                    'requirement',
                    'address',
                    'link_type',
                    'job_link',
                    'is_post',
                    'code',
                    'terms_and_conditions',
                    'applicant',
                    'visibility',
                    'custom_question',
                    'workspace',
                    'created_by'
                ]);
                $jobData['job_id'] = !empty($request->job_id) ? $request->job_id : '';

                JobTemplate::create($jobData);

                return redirect()->route('job-template.index')->with('success', __('The job template has been created successfully.'));
            } else {
                return redirect()->back()->with('error', __('Something went wrong, Please try again.'));
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
    public function show($id)
    {
        if (Auth::user()->isAbleTo('job template show')) {
            $job_template = JobTemplate::where('id', $id)->where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->first();
            $job_template->applicant  = !empty($job_template->applicant) ? explode(',', $job_template->applicant) : '';
            $job_template->visibility = !empty($job_template->visibility) ? explode(',', $job_template->visibility) : '';
            $job_template->skill      = !empty($job_template->skill) ? explode(',', $job_template->skill) : '';

            return view('recruitment::job-template.show', compact('job_template'));
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
        if (Auth::user()->isAbleTo('job template edit')) {
            $job_template = JobTemplate::where('id', $id)->where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->first();
            $categories = JobCategory::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('title', 'id');

            $branches = Branch::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            $status = Job::$status;

            $job_template->applicant       = explode(',', $job_template->applicant);
            $job_template->visibility      = explode(',', $job_template->visibility);
            $job_template->custom_question = explode(',', $job_template->custom_question);

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

            return view('recruitment::job-template.edit', compact('categories', 'status', 'branches', 'job_template', 'customQuestion', 'users', 'job_type', 'recruitment_type'));
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
        if (Auth::user()->isAbleTo('job template edit')) {
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

            $job_template                       = JobTemplate::find($id);
            $job_template->title                = $request->title;
            $job_template->recruitment_type     = $request->recruitment_type;
            $job_template->branch               = !empty($request->branch) ? $request->branch : 0;
            $job_template->location             = !empty($request->location) ? $request->location : '';
            $job_template->category             = $request->category;
            $job_template->user_id              = $request->user_id;
            $job_template->skill                = $request->skill;
            $job_template->position             = $request->position;
            $job_template->status               = $request->status;
            $job_template->job_type             = $request->job_type;
            $job_template->salary_from          = $request->salary_from;
            $job_template->salary_to            = $request->salary_to;
            $job_template->start_date           = $request->start_date;
            $job_template->end_date             = $request->end_date;
            $job_template->description          = $request->description;
            $job_template->requirement          = $request->requirement;
            $job_template->address              = $request->address;
            $job_template->link_type            = $request->link_type;
            $job_template->job_link             = !empty($request->job_link) ? $request->job_link : null;
            $job_template->terms_and_conditions = !empty($request->terms_and_conditions) ? $request->terms_and_conditions : '';
            $job_template->applicant            = !empty($request->applicant) ? implode(',', $request->applicant) : '';
            $job_template->visibility           = !empty($request->visibility) ? implode(',', $request->visibility) : '';
            $job_template->custom_question      = !empty($request->custom_question) ? implode(',', $request->custom_question) : '';
            $job_template->save();

            return redirect()->route('job-template.index')->with('success', __('The job template details are updated successfully.'));
        } else {
            return redirect()->route('job-template.index')->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('job template delete')) {
            $job_template = JobTemplate::find($id);

            $job_template->delete();

            return redirect()->route('job-template.index')->with('success', __('The job template has been deleted.'));
        } else {
            return redirect()->route('job-template.index')->with('error', __('Permission denied.'));
        }
    }

    public function grid()
    {
        if (Auth::user()->isAbleTo('job template manage')) {
            $job_templates = JobTemplate::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());
            $job_templates = $job_templates->paginate(11);
            return view('recruitment::job-template.grid', compact('job_templates'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function convertToJob(Request $request)
    {
        if (Auth::user()->isAbleTo('job manage')) {
            $job_template = JobTemplate::where('id', $request->job_template_id)->where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->first();

            if ($job_template) {
                $job                       = new Job();
                $job->title                = $job_template->title;
                $job->recruitment_type     = $job_template->recruitment_type;
                $job->branch               = !empty($job_template->branch) ? $job_template->branch : 0;
                $job->location             = !empty($job_template->location) ? $job_template->location : '';
                $job->category             = $job_template->category;
                $job->user_id              = $job_template->user_id;
                $job->skill                = $job_template->skill;
                $job->position             = $job_template->position;
                $job->status               = $job_template->status;
                $job->job_type             = $job_template->job_type;
                $job->salary_from          = $job_template->salary_from;
                $job->salary_to            = $job_template->salary_to;
                $job->start_date           = $job_template->start_date;
                $job->end_date             = $job_template->end_date;
                $job->description          = $job_template->description;
                $job->requirement          = $job_template->requirement;
                $job->address              = $job_template->address;
                $job->link_type            = $job_template->link_type;
                $job->job_link             = !empty($job_template->job_link) ? $job_template->job_link : null;
                $job->terms_and_conditions = !empty($job_template->terms_and_conditions) ? $job_template->terms_and_conditions : '';
                $job->code                 = uniqid();
                $job->applicant            = !empty($job_template->applicant) ? $job_template->applicant : '';
                $job->visibility           = !empty($job_template->visibility) ? $job_template->visibility : '';
                $job->custom_question      = !empty($job_template->custom_question) ? $job_template->custom_question : '';
                $job->workspace            = getActiveWorkSpace();
                $job->created_by           = creatorId();
                $job->save();

                return redirect()->route('job.index')->with('success', __('The job has been created successfully.'));
            } else {
                return redirect()->back()->with('error', 'Something went wrong, Please try again.');
            }
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
}
