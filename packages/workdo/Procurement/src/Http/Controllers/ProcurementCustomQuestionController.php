<?php

namespace Workdo\Procurement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\Procurement\DataTables\RfxCustomQuestionDataTable;
use Workdo\Procurement\Entities\ProcurementCustomQuestion;
use Workdo\Procurement\Entities\RfxApplication;
use Workdo\Procurement\Events\CreateRfxCustomQuestion;
use Workdo\Procurement\Events\DestroyRfxCustomQuestion;
use Workdo\Procurement\Events\UpdateRfxCustomQuestion;
use Workdo\Procurement\Entities\Rfx;

class ProcurementCustomQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(RfxCustomQuestionDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('rfx custom question manage')) {
            return $dataTable->render('procurement::customQuestion.index');
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
        if (Auth::user()->isAbleTo('rfx custom question create')) {
            $is_required = ProcurementCustomQuestion::$is_required;

            return view('procurement::customQuestion.create', compact('is_required'));
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
        if (Auth::user()->isAbleTo('rfx custom question create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'question' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $question = new ProcurementCustomQuestion();
            $question->question = $request->question;
            $question->is_required = $request->is_required;
            $question->workspace = getActiveWorkSpace();
            $question->created_by = creatorId();
            $question->save();

            event(new CreateRfxCustomQuestion($request, $question));

            return redirect()->back()->with('success', __('The question has been created successfully.'));
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
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('rfx custom question edit')) {
            $is_required = ProcurementCustomQuestion::$is_required;
            $customQuestion = ProcurementCustomQuestion::find($id);
            if ($customQuestion) {
                return view('procurement::customQuestion.edit', compact('customQuestion', 'is_required'));
            } else {
                return response()->json(['error' => __('The question is not found.')], 401);
            }

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
        if (Auth::user()->isAbleTo('rfx custom question edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'question' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $customQuestion = ProcurementCustomQuestion::find($id);
            if ($customQuestion) {
                $customQuestion->question = $request->question;
                $customQuestion->is_required = $request->is_required;
                $customQuestion->save();

                event(new UpdateRfxCustomQuestion($request, $customQuestion));
            } else {
                return redirect()->back()->with('error', __('The question is not found.'));
            }
            return redirect()->back()->with('success', __('The question are updated successfully.'));
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
        if (Auth::user()->isAbleTo('rfx custom question delete')) {
            $customQuestion = ProcurementCustomQuestion::find($id);
            if ($customQuestion) {
                $rfxs = Rfx::whereRaw("FIND_IN_SET($customQuestion->id, custom_question)")->get();
                foreach ($rfxs as $rfx) {
                    $rfxData = $rfx->custom_question;
                    $que = explode(',', $rfxData);
                    unset($que[array_search($customQuestion->id, $que)]);

                    $que = implode(',', $que);

                    $rfx->custom_question = $que;
                    $rfx->save();

                    $rfx_id = $rfx->id;

                    $questions = RfxApplication::where('rfx', $rfx_id)->get();

                    foreach ($questions as $question) {
                        $queDetail = json_decode($question->custom_question, true);

                        unset($queDetail[$customQuestion->question]);
                        $queDetail = json_encode($queDetail);
                        $question->custom_question = $queDetail;
                        $question->save();
                    }
                }
                event(new DestroyRfxCustomQuestion($customQuestion));
                $customQuestion->delete();
                return redirect()->back()->with('success', __('The question has been deleted.'));
            } else {
                return redirect()->back()->with('error', __('The question is not found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
