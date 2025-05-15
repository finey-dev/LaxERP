<?php

namespace Workdo\Planning\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use Workdo\Planning\DataTables\ChallengesDataTable;
use Workdo\Planning\DataTables\ShowChallengeDataTable;
use Workdo\Planning\Entities\PlanningCategories;
use Workdo\Planning\Entities\PlanningChallenge;
use Workdo\Planning\Entities\PlanningCharters;
use Workdo\Planning\Events\CreateChallenge;
use Workdo\Planning\Events\DestroyChallenge;
use Workdo\Planning\Events\UpdateChallenge;


class PlanningChallengeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(ChallengesDataTable $dataTable, Request $request)
    {
        if (Auth::user()->isAbleTo('planningchallenges manage')) {
            $category = PlanningCategories::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('title', 'id');

            $position = PlanningChallenge::$statues;
            return $dataTable->render('planning::planningchallenges.index',compact('category','position'));
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

        if (Auth::user()->isAbleTo('planningchallenges create')) {

            $PlanningCategories = PlanningCategories::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('title', 'id')->prepend('Select Category', '');

            return view('planning::planningchallenges.create', compact('PlanningCategories'));
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
        if (Auth::user()->isAbleTo('planningchallenges create')) {

            $validator = Validator::make(
                $request->all(),
                [
                    'challenge_name' => 'required',
                    'category' => 'required',
                    'position' => 'required',
                    'end_date' => 'required',
                    'notes' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->withInput()->with('error', $messages->first());
            }


            $Challenges                     = new PlanningChallenge();
            $Challenges->name               = $request->challenge_name;
            $Challenges->category           = $request->category;
            $Challenges->end_date           = $request->end_date;
            $Challenges->position           = $request->position;
            $Challenges->explantion         = $request->explantion;
            $Challenges->notes              = $request->notes;
            $Challenges->workspace          = getActiveWorkSpace();
            $Challenges->created_by         = creatorId();
            $Challenges->save();

            event(new CreateChallenge($request, $Challenges));
            return redirect()->route('planningchallenges.index')->with('success', __('The Challenge has been created successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id, ShowChallengeDataTable $dataTable)
    {

        return $dataTable->with('challengeId',$id)->render('planning::planningchallenges.show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {

        if (Auth::user()->isAbleTo('planningchallenges edit')) {

            $PlanningCategories = PlanningCategories::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('title', 'id')->prepend('Select Category', '');
            $Challenge = PlanningChallenge::find($id);

            return view('planning::planningchallenges.edit', compact('PlanningCategories', 'Challenge'));
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

        $Challenge = PlanningChallenge::find($id);
        if (Auth::user()->isAbleTo('planningchallenges edit')) {

            $validator = Validator::make(
                $request->all(),
                [
                    'challenge_name' => 'required',
                    'category' => 'required',
                    'position' => 'required',
                    'end_date' => 'required',
                    'notes' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->withInput()->with('error', $messages->first());
            }

            $Challenge->name               = $request->challenge_name;
            $Challenge->category           = $request->category;
            $Challenge->end_date           = $request->end_date;
            $Challenge->position           = $request->position;
            $Challenge->explantion         = $request->explantion;
            $Challenge->notes              = $request->notes;
            $Challenge->workspace          = getActiveWorkSpace();
            $Challenge->created_by         = creatorId();
            $Challenge->save();
            event(new UpdateChallenge($request, $Challenge));
            return redirect()->route('planningchallenges.index')->with('success', __('The Challenge details are updated successfully'));
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

        if (Auth::user()->isAbleTo('planningchallenges edit')) {

            $currentWorkspace = getActiveWorkSpace();
            $Challenge = PlanningChallenge::find($id);

            if ($Challenge->created_by == creatorId() && $Challenge->workspace == $currentWorkspace) {

                $Charters     = PlanningCharters::where('challenge', $Challenge->id)->where('workspace', getActiveWorkSpace())->get();

                if (count($Charters) == 0) {
                    event(new DestroyChallenge($Challenge));
                    $Challenge->delete();
                } else {
                    return redirect()->route('planningchallenges.index')->with('error', __('This challenge has charter. Please remove the charter from this challenge.'));
                }

                return redirect()->route('planningchallenges.index')->with('success', __('The Challenge has been deleted'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
