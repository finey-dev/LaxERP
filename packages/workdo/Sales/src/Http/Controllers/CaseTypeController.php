<?php

namespace Workdo\Sales\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Sales\Entities\CaseType;
use Workdo\Sales\Entities\CommonCase;
use Workdo\Sales\Events\CreateCaseType;
use Workdo\Sales\Events\DestroyCaseType;
use Workdo\Sales\Events\UpdateCaseType;
use PhpParser\Node\Stmt\Case_;

class CaseTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (\Auth::user()->isAbleTo('casetype manage')) {
            $types = CaseType::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->orderBy('id','desc')->get();

            return view('sales::case_type.index', compact('types'));
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
        if (\Auth::user()->isAbleTo('casetype create')) {
            return view('sales::case_type.create');
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
        if (\Auth::user()->isAbleTo('casetype create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|string|max:120',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $name                   = $request['name'];
            $casetype               = new CaseType();
            $casetype->name         = $name;
            $casetype['workspace']  = getActiveWorkSpace();
            $casetype['created_by'] = creatorId();
            $casetype->save();
            event(new CreateCaseType($request, $casetype));

            return redirect()->route('case_type.index')->with('success', __('The case type has been created successfully.'));
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
        return view('sales::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(CaseType $caseType)
    {
        if (\Auth::user()->isAbleTo('casetype edit')) {
            return view('sales::case_type.edit', compact('caseType'));
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
    public function update(Request $request, CaseType $caseType)
    {
        if (\Auth::user()->isAbleTo('casetype edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|string|max:120',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $caseType['name'] = $request->name;
            $caseType['workspace']  = getActiveWorkSpace();
            $caseType['created_by']  = creatorId();
            $caseType->update();
            event(new UpdateCaseType($request, $caseType));

            return redirect()->route('case_type.index')->with(
                'success',
                __('The case type details are updated successfully.')
            );
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(CaseType $caseType)
    {
        if (\Auth::user()->isAbleTo('casetype delete')) {
            $commoncase = CommonCase::where('type', '=', $caseType->id)->count();
            if ($commoncase == 0) {
                event(new DestroyCaseType($caseType));

                $caseType->delete();

                return redirect()->route('case_type.index')->with(
                    'success',
                    'The case type has been deleted.'
                );
            } else {
                return redirect()->back()->with('error', __('This cases type is used on cases.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
