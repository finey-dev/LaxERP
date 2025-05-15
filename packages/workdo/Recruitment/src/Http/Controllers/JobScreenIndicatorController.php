<?php

namespace Workdo\Recruitment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\Recruitment\Entities\CustomQuestion;
use Workdo\Recruitment\Entities\JobScreenIndicator;
use Workdo\Recruitment\Entities\JobScreeningType;

class JobScreenIndicatorController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (Auth::user()->isAbleTo('screen indicator manage')) {
            $categories = JobScreenIndicator::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();

            return view('recruitment::ScreenIndicator.index', compact('categories'));
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
        if (Auth::user()->isAbleTo('screen indicator create')) {

            $screening_type = JobScreeningType::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            return view('recruitment::ScreenIndicator.create', compact('screening_type'));
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
        if (Auth::user()->isAbleTo('screen indicator create')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'screening_type' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $jobCategory                 = new JobScreenIndicator();
            $jobCategory->name           = $request->name;
            $jobCategory->screening_type = $request->screening_type;
            $jobCategory->workspace      = getActiveWorkSpace();
            $jobCategory->created_by     = creatorId();
            $jobCategory->save();

            // event(new CreateJobCategory($request, $jobCategory));

            return redirect()->back()->with('success', __('The screen indicator has been created successfully.'));
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
        if (Auth::user()->isAbleTo('screen indicator edit')) {
            $jobCategory = JobScreenIndicator::find($id);
            $screening_type = JobScreeningType::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            return view('recruitment::ScreenIndicator.edit', compact('jobCategory', 'screening_type'));
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
        if (Auth::user()->isAbleTo('screen indicator edit')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'screening_type' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $jobCategory = JobScreenIndicator::find($id);
            $jobCategory->name           = $request->name;
            $jobCategory->screening_type = $request->screening_type;
            $jobCategory->save();

            // event(new UpdateJobCategory($request, $jobCategory));

            return redirect()->back()->with('success', __('The screen indicator details are updated successfully.'));
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
        if (Auth::user()->isAbleTo('screen indicator delete')) {
            $jobCategory = JobScreenIndicator::find($id);
            if ($jobCategory->created_by == creatorId() && $jobCategory->workspace == getActiveWorkSpace()) {
                    // event(new DestroyJobCategory($jobCategory));

                    $jobCategory->delete();
                return redirect()->back()->with('success', __('The screen indicator has been deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
