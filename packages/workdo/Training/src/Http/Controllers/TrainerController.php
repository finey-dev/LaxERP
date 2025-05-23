<?php

namespace Workdo\Training\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\Hrm\Entities\Branch;
use Workdo\Training\DataTables\TrainerDataTable;
use Workdo\Training\Entities\Trainer;
use Workdo\Training\Entities\Training;
use Workdo\Training\Events\CreateTrainer;
use Workdo\Training\Events\DestroyTrainer;
use Workdo\Training\Events\UpdateTrainer;

class TrainerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(TrainerDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('trainer manage')) {

            return $dataTable->render('training::trainer.index');
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
        if (Auth::user()->isAbleTo('trainer create')) {
            $branches = Branch::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            return view('training::trainer.create', compact('branches'));
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
        if (Auth::user()->isAbleTo('trainer create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'branch' => 'required',
                    'firstname' => 'required|max:120',
                    'lastname' => 'required|max:120',
                    'contact' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
                    'email' => 'required|email|max:100',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $trainer             = new Trainer();
            $trainer->branch     = $request->branch;
            $trainer->firstname  = $request->firstname;
            $trainer->lastname   = $request->lastname;
            $trainer->contact    = $request->contact;
            $trainer->email      = $request->email;
            $trainer->address    = $request->address;
            $trainer->expertise  = $request->expertise;
            $trainer->workspace  = getActiveWorkSpace();
            $trainer->created_by = creatorId();
            $trainer->save();

            event(new CreateTrainer($request, $trainer));

            return redirect()->route('trainer.index')->with('success', __('The trainer has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Trainer $trainer)
    {
        if (Auth::user()->isAbleTo('trainer show')) {
            return view('training::trainer.show', compact('trainer'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Trainer $trainer)
    {
        if (Auth::user()->isAbleTo('trainer edit')) {
            $branches = Branch::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            return view('training::trainer.edit', compact('branches', 'trainer'));
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
    public function update(Request $request, Trainer $trainer)
    {
        if (Auth::user()->isAbleTo('trainer edit')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'branch' => 'required',
                    'firstname' => 'required|max:120',
                    'lastname' => 'required|max:120',
                    'contact' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
                    'email' => 'required|email|max:100',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $trainer->branch    = $request->branch;
            $trainer->firstname = $request->firstname;
            $trainer->lastname  = $request->lastname;
            $trainer->contact   = $request->contact;
            $trainer->email     = $request->email;
            $trainer->address   = $request->address;
            $trainer->expertise = $request->expertise;
            $trainer->save();
            event(new UpdateTrainer($request, $trainer));
            return redirect()->route('trainer.index')->with('success', __('The trainer details are updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Trainer $trainer)
    {
        if (Auth::user()->isAbleTo('trainer delete')) {
            if ($trainer->created_by == creatorId() &&  $trainer->workspace  == getActiveWorkSpace()) {
                $trainings = Training::where('trainer', $trainer->id)->where('workspace', getActiveWorkSpace())->get();
                if (count($trainings) == 0) {
                    event(new DestroyTrainer($trainings));
                    $trainer->delete();
                } else {
                    return redirect()->route('trainer.index')->with('error', __('This Trainer has Training List. Please remove the Training List from this Trainer.'));
                }
                return redirect()->route('trainer.index')->with('success', __('The trainer has been deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
