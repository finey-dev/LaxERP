<?php

namespace Workdo\Recruitment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\Recruitment\Entities\JobCandidate;
use Workdo\Recruitment\Entities\JobCandidateReferral;

class JobCandidateReferralController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return redirect()->back();
        return view('recruitment::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(Request $request)
    {
        if (Auth::user()->isAbleTo('jobcandidate-referral create')) {
            $jobCandidateId = $request->query('id');
            $reference = JobCandidate::$reference;
            return view('recruitment::JobCandidateReferral.create', compact('reference', 'jobCandidateId'));
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
        if (Auth::user()->isAbleTo('jobcandidate-referral create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'organization' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required|after_or_equal:start_date',
                    'country' => 'required',
                    'state' => 'required',
                    'city' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            if (!empty($request->experience_proof)) {

                $filenameWithExt = $request->file('experience_proof')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('experience_proof')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $uplaod = upload_file($request, 'experience_proof', $fileNameToStore, 'JobApplication');
                if ($uplaod['flag'] == 1) {
                    $url = $uplaod['url'];
                } else {
                    return redirect()->back()->with('error', $uplaod['msg']);
                }
            }

            $jobcandidate_referral                   = new JobCandidateReferral();
            $jobcandidate_referral->candidate_id     = $request->candidate_id;
            $jobcandidate_referral->title            = $request->title;
            $jobcandidate_referral->organization     = $request->organization;
            $jobcandidate_referral->start_date       = $request->start_date;
            $jobcandidate_referral->end_date         = $request->end_date;
            $jobcandidate_referral->country          = $request->country;
            $jobcandidate_referral->state            = $request->state;
            $jobcandidate_referral->city             = $request->city;
            $jobcandidate_referral->experience_proof = !empty($request->experience_proof) ? $url : '';
            $jobcandidate_referral->reference        = $request->reference;
            $jobcandidate_referral->full_name        = !empty($request->full_name) ? $request->full_name : '';
            $jobcandidate_referral->reference_email  = !empty($request->reference_email) ? $request->reference_email : '';
            $jobcandidate_referral->reference_phone  = !empty($request->reference_phone) ? $request->reference_phone : '';
            $jobcandidate_referral->job_position     = !empty($request->job_position) ? $request->job_position : '';
            $jobcandidate_referral->description      = $request->description;
            $jobcandidate_referral->workspace        = getActiveWorkSpace();
            $jobcandidate_referral->created_by       = creatorId();
            $jobcandidate_referral->save();

            // event(new CreateJobProject($request, $job_project));

            return redirect()->back()->with('success', __('The job referral has been created successfully.'));
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
        if (Auth::user()->isAbleTo('jobcandidate-referral show')) {
            $jobcandidate_referral = JobCandidateReferral::find($id);

            return view('recruitment::JobCandidateReferral.show', compact('jobcandidate_referral'));
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
        if (Auth::user()->isAbleTo('jobcandidate-referral edit')) {
            $jobcandidate_referral = JobCandidateReferral::find($id);
            $reference = JobCandidate::$reference;
            return view('recruitment::JobCandidateReferral.edit', compact('jobcandidate_referral', 'reference'));
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
        if (Auth::user()->isAbleTo('jobcandidate-referral edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'organization' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required|after_or_equal:start_date',
                    'country' => 'required',
                    'state' => 'required',
                    'city' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $jobcandidate_referral = JobCandidateReferral::find($id);

            if (!empty($request->experience_proof)) {
                $filenameWithExt = $request->file('experience_proof')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('experience_proof')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $uplaod = upload_file($request, 'experience_proof', $fileNameToStore, 'JobApplication');
                if ($uplaod['flag'] == 1) {
                    if (!empty($jobcandidate_referral->experience_proof)) {
                        delete_file($jobcandidate_referral->experience_proof);
                    }
                    $url = $uplaod['url'];
                    $jobcandidate_referral->experience_proof = $url;
                } else {
                    return redirect()->back()->with('error', $uplaod['msg']);
                }
            }

            $jobcandidate_referral->title            = $request->title;
            $jobcandidate_referral->organization     = $request->organization;
            $jobcandidate_referral->start_date       = $request->start_date;
            $jobcandidate_referral->end_date         = $request->end_date;
            $jobcandidate_referral->country          = $request->country;
            $jobcandidate_referral->state            = $request->state;
            $jobcandidate_referral->city             = $request->city;
            $jobcandidate_referral->reference        = $request->reference;
            if ($request->reference == 'yes') {
                $jobcandidate_referral->full_name           = $request->full_name;
                $jobcandidate_referral->reference_email     = $request->reference_email;
                $jobcandidate_referral->reference_phone     = $request->reference_phone;
                $jobcandidate_referral->job_position        = $request->job_position;
            } else {
                $jobcandidate_referral->full_name           = '';
                $jobcandidate_referral->reference_email     = '';
                $jobcandidate_referral->reference_phone     = '';
                $jobcandidate_referral->job_position        = '';
            }
            $jobcandidate_referral->description      = $request->description;
            $jobcandidate_referral->workspace        = getActiveWorkSpace();
            $jobcandidate_referral->created_by       = creatorId();
            $jobcandidate_referral->save();

            // event(new UpdateJobExperience($request, $job_experience));

            return redirect()->back()->with('success', __('The job referral details are updated successfully.'));
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
        if (Auth::user()->isAbleTo('jobcandidate-referral delete')) {
            $currentWorkspace = getActiveWorkSpace();
            $jobcandidate_referral = JobCandidateReferral::find($id);
            if ($jobcandidate_referral->created_by == creatorId() && $jobcandidate_referral->workspace == $currentWorkspace) {

                // event(new DestroyJobExperience($jobcandidate_referral));

                if (!empty($jobcandidate_referral->experience_proof)) {
                    delete_file($jobcandidate_referral->experience_proof);
                }

                $jobcandidate_referral->delete();
                return redirect()->back()->with('success', __('The job referral has been deleted.'));
            } else {
                return redirect()->back()->with('error', 'Permission denied.');
            }
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }
}
