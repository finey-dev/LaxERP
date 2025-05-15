<?php

namespace Workdo\Recruitment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\Recruitment\Entities\JobCandidate;
use Workdo\Recruitment\Entities\JobCandidateCategory;

class JobCandidateCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (Auth::user()->isAbleTo('jobcandidate-category manage')) {
            $categories = JobCandidateCategory::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();

            return view('recruitment::jobCandidateCategory.index', compact('categories'));
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
        if (Auth::user()->isAbleTo('jobcandidate-category create')) {
            return view('recruitment::jobCandidateCategory.create');
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
        if (Auth::user()->isAbleTo('jobcandidate-category create')) {

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

            $jobCategory             = new JobCandidateCategory();
            $jobCategory->name       = $request->name;
            $jobCategory->workspace  = getActiveWorkSpace();
            $jobCategory->created_by = creatorId();
            $jobCategory->save();

            // event(new CreateJobCategory($request, $jobCategory));

            return redirect()->back()->with('success', __('The job candidate category has been created successfully.'));
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
        return view('recruitment::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('jobcandidate-category edit')) {
            $jobCategory = JobCandidateCategory::find($id);
            return view('recruitment::jobCandidateCategory.edit', compact('jobCategory'));
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
        if (Auth::user()->isAbleTo('jobcandidate-category edit')) {

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
            $jobCategory = JobCandidateCategory::find($id);
            $jobCategory->name = $request->name;
            $jobCategory->save();

            // event(new UpdateJobCategory($request, $jobCategory));

            return redirect()->back()->with('success', __('The job candidate category details are updated successfully.'));
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
        if (Auth::user()->isAbleTo('jobcandidate-category delete')) {
            $jobCategory = JobCandidateCategory::find($id);
            if ($jobCategory->created_by == creatorId() && $jobCategory->workspace == getActiveWorkSpace()) {
                $jobs = JobCandidate::where('candidate_category', $jobCategory->id)->where('workspace', getActiveWorkSpace())->get();
                if (count($jobs) == 0) {
                    // event(new DestroyJobCategory($jobCategory));

                    $jobCategory->delete();
                } else {
                    return redirect()->back()->with('error', __('This Job candidate category has Job Candidate. Please remove the Job Candidate from this Job category.'));
                }
                return redirect()->back()->with('success', __('The job candidate category has been deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
